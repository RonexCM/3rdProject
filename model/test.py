import pandas as pd
import numpy as np
import pickle
import json
from sklearn.linear_model import LinearRegression
# Load the dataset
df = pd.read_csv('model/nepali_dataset.csv')

# Drop unnecessary columns
df2 = df.drop(columns=['Title', 'City', 'Face', 'Year', 'Views', 'Road', 'Road Type', 'Build Area', 'Posted', 'Amenities'])

# Remove rows with null data
df3 = df2.dropna()

df4 = df3.copy()

# Define a function to safely extract 'Aana' from 'Area'
def extract_aana(value):
    if isinstance(value, str) and value.strip() != "":
        try:
            return float(value.split(' ')[0])
        except ValueError:
            return None
    return None

# Apply the function to create 'Aana' column
df4['Aana'] = df4['Area'].apply(extract_aana)

# Define a function to safely extract 'Road Width'
def extract_road_width(value):
    if isinstance(value, str) and value.strip() != "":
        try:
            return float(value.split(' ')[0])
        except ValueError:
            return None
    return None

# Apply the function to create 'Road' column
df4['Road'] = df4['Road Width'].apply(extract_road_width)

# Drop columns no longer needed
df5 = df4.drop(columns=['Area', 'Road Width'])

# Remove rows where 'Aana' is empty, zero, or invalid
df5 = df5.dropna(subset=['Aana'])
df5 = df5[df5['Aana'] > 0]

# Calculate price per Aana
df5['price_per_aana'] = df5['Price'] / df5['Aana']

# Remove rows with infinity or NaN in 'price_per_aana'
df5 = df5.replace([np.inf, -np.inf], np.nan)  # Replace infinities with NaN
df5 = df5.dropna(subset=['price_per_aana'])  # Drop rows with NaN values in 'price_per_aana'

# Clean address column
df5['Address'] = df5['Address'].apply(lambda x: x.strip() if isinstance(x, str) else '')

# Group by address and handle less frequent addresses
location_stats = df5.groupby('Address')['Address'].agg('count').sort_values(ascending=False)
print(len(location_stats[location_stats <= 5]))

location_stats_less_than_5 = location_stats[location_stats <= 5]
df5['Address'] = df5['Address'].apply(lambda x: 'others' if x in location_stats_less_than_5 else x)

def remove_pps_outliers(df):
    df_out = pd.DataFrame()
    for key, subdf in df.groupby('Address'):
        m = np.mean(subdf.price_per_aana)
        st = np.std(subdf.price_per_aana)
        reduced_df = subdf[(subdf.price_per_aana > (m - st)) & (subdf.price_per_aana <= (m + st))]
        df_out = pd.concat([df_out, reduced_df], ignore_index=True)
    return df_out

df6 = remove_pps_outliers(df5)
print(df6.shape)

def remove_bhk_outliers(df):
    exclude_indices = np.array([])

    for location, location_df in df.groupby('Address'):
        aana_stats = {}
        
        # First loop to calculate statistics for each aana
        for aana, aana_df in location_df.groupby('Aana'):
            aana_stats[aana] = {
                'mean': np.mean(aana_df.price_per_aana),
                'std': np.std(aana_df.price_per_aana),
                'count': aana_df.shape[0]
            }
        
        # Second loop to compare each aana with the previous aana
        for aana, aana_df in location_df.groupby('Aana'):
            stats = aana_stats.get(aana - 1)
            if stats and stats['count'] > 5:
                # Filter out outliers based on the mean of the previous aana
                exclude_indices = np.append(exclude_indices, aana_df[aana_df.price_per_aana < stats['mean']].index.values)
    
    # Drop the outlier rows from the DataFrame
    return df.drop(exclude_indices, axis='index')

# Apply the function to the DataFrame
df8 = remove_bhk_outliers(df6)
df8 = df8.drop(columns=['price_per_aana'])

# Create dummy variables for the Address column
df11 = pd.get_dummies(df8, columns=['Address'], drop_first=True)
df11 = df11[df11['Price'] < df11['Price'].quantile(0.95)]

# Split into features and target
X = df11.drop('Price', axis=1)

y = df11['Price']



columns = {
    'data-columns' : [col.lower() for col in X.columns]
}
with open("columns.json","w") as f:
    f.write(json.dumps(columns))
    
X = X.astype(float)
y = y.astype(float)

# Convert to numpy arrays
X = np.array(X)
y = np.array(y)

# Add intercept term to the feature matrix
X = np.c_[np.ones(X.shape[0]), X]  # Add a column of ones to X for the intercept

# Compute theta using the closed-form solution
try:
    theta = np.linalg.inv(X.T.dot(X)).dot(X.T).dot(y)
    print(len(theta))
except np.linalg.LinAlgError as e:
    print("Error in computing theta:", e)

# Create a mapping for address encoding

model = LinearRegression()
model.fit(X, y)
library_theta = np.append(model.intercept_, model.coef_)
print("Library Theta (with intercept):", library_theta)

# Compare with your theta
print("Hardcoded Theta:", theta)
address_columns = [col for col in df11.columns if col.startswith('Address_')]
address_mapping = {col.replace('Address_', ''): col for col in address_columns}

# Define a function to prepare the new data
def prepare_feature_vector(new_data):
    feature_vector = []
    # Add numeric features
    for col in df11.columns:
        if col != 'Price' and col != 'Address':
            feature_vector.append(new_data.get(col, 0))
        elif col.startswith('Address_'):
            feature_vector.append(new_data.get(col, 0))
    
    # Add intercept term
    feature_vector = np.insert(feature_vector, 0, 1)
    return np.array(feature_vector)

# Example of new data
new_data = {
    'Bedroom': 6,
    'Bathroom': 6,
    'Floors': 3,
    'Parking': 2,
    'Aana': 7,  # Ensure this matches your cleaned data
    'Road': 16,
}

# Example address
new_address = 'others'

# Add one-hot encoding for the address dynamically
address_cols = [col for col in df11.columns if col.startswith('Address_')]
for col in address_cols:
    if col.replace('Address_', '') == new_address:
        new_data[col] = 1
    else:
        new_data[col] = 0

# Prepare feature vector
feature_vector = prepare_feature_vector(new_data)
print((feature_vector))
# Define a predict function
def predict(features):
    return features.dot(theta)

# Make the prediction
predicted_price = predict(feature_vector)
print("Predicted price:", predicted_price)

model = LinearRegression(fit_intercept=True)
model.fit(X, y)

# Get coefficients and intercept
library_theta = np.append(model.intercept_, model.coef_)
print("Library Theta (with intercept):", library_theta)


print("Deature Theta (with intercept):", feature_vector)
# Predict with library model
library_predictions = model.predict([feature_vector])

print(df11.shape)
print("Library Predictions:", library_predictions)  # First 5 predictions
# print("Hardcoded Predictions:", hardcoded_predictions)  # First 5 predictions
