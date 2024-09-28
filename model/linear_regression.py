import pandas as pd
import numpy as np
import re
import pickle
import json
from sklearn.linear_model import Lasso
from sklearn.preprocessing import StandardScaler
from sklearn.model_selection import train_test_split

df = pd.read_csv('model/Nepali_house_dataset.csv')


df2 = df.drop(columns=['TITLE', 'BUILDUP AREA', 'FACING', 'BUILT YEAR', 'PARKING', 'AMENITIES'])
df3 = df2.dropna()

df4 = df3.copy()

def extract_aana(value):
    if isinstance(value, str) and value.strip() != "":
        try:
            return float(value.split(' ')[0])
        except ValueError:
            return None
    return None

# Apply the function to create 'Aana' column
df4['Aana'] = df4['LAND AREA'].apply(extract_aana)

def extract_road_width(value):
    if isinstance(value, str) and value.strip() != "":
        # Check for range pattern (like '13-20 Feet')
        if '-' in value:
            return None  # Drop rows containing ranges
        try:
            return float(value.split(' ')[0])
        except ValueError:
            return None
    return None
# Apply the function to create 'Road' column
df4['Road'] = df4['ROAD ACCESS'].apply(extract_road_width)
df4.dropna(subset=['Road'], inplace=True)

df5 = df4.drop(columns=['LAND AREA', 'ROAD ACCESS'])

# Function to convert price strings to numerical values
def convert_price(price_str):
    # Remove 'Rs.' and unnecessary whitespace
    price_str = price_str.replace('Rs.', '').strip()
    
    # Handle 'Lac/aana' or similar formats
    if 'Lac/aana' in price_str or 'aana' in price_str:
        return None  # Remove rows with 'Lac/aana' or 'aana'
    
    if 'Lac' in price_str:
        # Extract the numeric value
        lac_value = re.search(r'(\d+(\.\d+)?)', price_str)
        if lac_value:
            return float(lac_value.group(0)) * 100000  # 1 Lac = 100,000
        else:
            return None
            
    elif 'Cr' in price_str:
        # Extract the numeric value
        cr_value = re.search(r'(\d+(\.\d+)?)', price_str)
        if cr_value:
            return float(cr_value.group(0)) * 10000000  # 1 Cr = 10,000,000
        else:
            return None
            
    else:
        # Handle standard numbers
        price_str = price_str.replace(',', '')  # Remove commas
        try:
            return float(price_str)  # Convert to float
        except ValueError:
            return None  # Return None if conversion fails

# Apply the function to the 'Price' column
df5['PRICE'] = df5['PRICE'].apply(convert_price)

# Remove rows where 'Price' is None (i.e., invalid formats)
df5 = df5.dropna(subset=['PRICE'])

df5['price_per_aana'] = df5['PRICE'] / df5['Aana']


# Remove rows with infinity or NaN in 'price_per_aana'
df5 = df5.replace([np.inf, -np.inf], np.nan)  # Replace infinities with NaN
df5 = df5.dropna(subset=['price_per_aana'])  # Drop rows with NaN values in 'price_per_aana'

df5.LOCATION = df5.LOCATION.apply(lambda x: x.strip())

location_stats = df5.groupby('LOCATION')['LOCATION'].agg('count').sort_values(ascending=False)
location_stats_less_than_10 = location_stats[location_stats<=10]

df5.LOCATION = df5.LOCATION.apply(lambda x: 'others' if x in location_stats_less_than_10 else x)

df6 = df5[~(df5.Aana / df5.BEDROOM < 0.5)]


def remove_pps_outliers(df):
     df_out = pd.DataFrame()
     for key, subdf in df.groupby('LOCATION'):
         m = np.mean(subdf.price_per_aana)
         st = np.std(subdf.price_per_aana)
         reduced_df = subdf[(subdf.price_per_aana > (m-st)) & (subdf.price_per_aana <= (m+st))]
         df_out = pd.concat([df_out,reduced_df],ignore_index=True)
     return df_out

df7 = remove_pps_outliers(df6)


def remove_bedroom_outliers(df):
    exclude_indices = np.array([])

    # Group the DataFrame by 'Address' (location)
    for location, location_df in df.groupby('LOCATION'):
        bedroom_stats = {}
        
        # First loop to calculate statistics for each number of bedrooms
        for bedroom, bedroom_df in location_df.groupby('BEDROOM'):
            bedroom_stats[bedroom] = {
                'mean': np.mean(bedroom_df.price_per_aana),
                'std': np.std(bedroom_df.price_per_aana),
                'count': bedroom_df.shape[0]
            }
        
        # Second loop to compare prices for each number of bedrooms
        for bedroom, bedroom_df in location_df.groupby('BEDROOM'):
            stats = bedroom_stats.get(bedroom - 1)  # Get the stats for one bedroom less
            if stats and stats['count'] > 5:  # Ensure we have enough data points for comparison
                # Filter out outliers where the price per aana is less than the mean price per aana of the lower-bedroom properties
                exclude_indices = np.append(exclude_indices, bedroom_df[bedroom_df.price_per_aana < stats['mean']].index.values)
    
    # Drop the outlier rows from the DataFrame
    return df.drop(exclude_indices, axis='index')

# Apply the function to the DataFrame
df8 = remove_bedroom_outliers(df7)
df8 = df8.drop(columns=['price_per_aana'])

df9 = pd.get_dummies(df8, columns=['LOCATION'], drop_first=True)
df9 = df9[df9['PRICE'] < df9['PRICE'].quantile(0.95)]

X = df9.drop('PRICE', axis=1)

y = df9['PRICE']

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
# X_scaled = np.c_[np.ones(X_scaled.shape[0]), X_scaled]
# Compute theta using the scaled features
try:
    theta = np.linalg.inv(X.T.dot(X)).dot(X.T).dot(y)
except np.linalg.LinAlgError as e:
    print("Error in computing theta:", e)



# Create a mapping for address encoding
address_columns = [col for col in df9.columns if col.startswith('LOCATION_')]
address_mapping = {col.replace('LOCATION_', ''): col for col in address_columns}

X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Implement Lasso Regression
lasso_model = Lasso()  # You can tune the 'alpha' parameter as needed
lasso_model.fit(X_train, y_train)

import pickle

# Data to be saved in the pickle file
model_data = {
    'theta': theta,  # Model parameters
    'lasso': lasso_model,
    'columns': [col.lower() for col in df9.columns],  # Feature columns used in the model
    'address_mapping': address_mapping  # Mapping of addresses for one-hot encoding
}

# Save the data into a pickle file
with open('server/artifacts/house_price_model.pkl', 'wb') as f:
    pickle.dump(model_data, f)

print("Model saved to pickle file.")
# Example of new data
# new_data = {
#     'FLOOR': 3.5,
#     'BEDROOM': 5,
#     'BATHROOM': 7,
#     'Aana': 3.2,  # Ensure this matches your cleaned data
#     'Road': 12,
# }

# # Example address
# new_address = 'Bhaisepati, Lalitpur'


