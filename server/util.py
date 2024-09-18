import pickle
import json
import numpy as np

__locations = None
__data_columns = None
__model = None

# def get_estimated_price(location,sqft,bhk,bath,balcony):
#     try:
#         loc_index = __data_columns.index(location.lower())
#     except:
#         loc_index = -1

#     x = np.zeros(len(__data_columns))
#     x[0] = sqft
#     x[1] = bath
#     x[2] = bhk
#     x[3] = balcony
#     if loc_index>=0:
#         x[loc_index] = 1

#     return round(__model.predict([x])[0],2)

# Prepare the feature vector for prediction
def prepare_feature_vector(new_data):
    feature_vector = []
    
    # Iterate through all columns and create the feature vector
    for col in __data_columns:
        if col != 'price' and col != 'address':
            feature_vector.append(new_data.get(col, 0))
        elif col.startswith('address_'):
            feature_vector.append(new_data.get(col, 0))
    
    # Add intercept term at the beginning
    feature_vector = np.insert(feature_vector, 0, 1)
    return np.array(feature_vector)

# Function to predict the house price based on input features
def predict(features):
    return features.dot(__theta)

# Function to estimate the house price
def get_estimated_price(location, aana, bedroom, bathroom, floors, parking, road):
    # Prepare new_data dictionary with all features
    new_data = {
        'aana': aana,
        'bedroom': bedroom,
        'bathroom': bathroom,
        'floors': floors,
        'parking': parking,
        'road': road
    }
    
    # Add one-hot encoding for the address dynamically
    for col in __data_columns:
        if col.startswith('address_'):
            if col.replace('address_', '') == location.lower():
                new_data[col] = 1
            else:
                new_data[col] = 0

    # Prepare feature vector for the model
    feature_vector = prepare_feature_vector(new_data)

    # Make a prediction using the loaded model (theta)
    return round(predict(feature_vector), 2)

def load_saved_artifacts():
    global __theta
    global __data_columns
    global __model
    global __locations
    print("Loading saved artifacts...start")
    
    # Load the model from the pickle file
    with open('./artifacts/house_price_model.pkl', 'rb') as f:
        model_data = pickle.load(f)
        __theta = model_data['theta']
        __data_columns = model_data['columns']
        # __data_columns = json.load(f)['data_columns']
        __locations = [col.replace('address_', '') for col in __data_columns if col.startswith('address_')]
        __model = model_data  # This will hold all model-related data
    
    print("Loading saved artifacts...done")

def get_location_names():
    return __locations

def get_data_columns():
    return __data_columns

if __name__ == '__main__':
    load_saved_artifacts()
    print(get_location_names())