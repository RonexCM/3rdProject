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
model2 = Lasso()
model2.fit(X,y)
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
    'Bedroom': 12,
    'Bathroom': 5,
    'Floors': 5,
    'Parking': 4,
    'Aana': 9,  # Ensure this matches your cleaned data
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
print(len(feature_vector))
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
lib = model2.predict([feature_vector])

print(df11.shape)
print("Library Predictions:", library_predictions)  # First 5 predictions
print("Library Lasso:", lib)  # First 5 predictions
# print("Hardcoded Predictions:", hardcoded_predictions)  # First 5 predictions