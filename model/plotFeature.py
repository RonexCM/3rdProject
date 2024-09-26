import pandas as pd
import numpy as np
import re
import matplotlib.pyplot as plt
from sklearn.linear_model import LinearRegression

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

def plotFeature(feature):
    # Select the feature column and the target variable
    X = df9[[feature]]  # Use double brackets to keep it as a DataFrame
    y = df9['PRICE']

    # Fit a linear regression model
    model = LinearRegression()
    model.fit(X, y)  # No need to reshape, already in the right shape

    # Predict y values based on the model
    y_pred = model.predict(X)

    # Plotting the results
    plt.figure(figsize=(8, 5))
    plt.scatter(X, y, color='blue', alpha=0.5, label='Data Points')
    plt.plot(X, y_pred, color='red', linewidth=2, label='Regression Line')
    plt.title(f'Single Variable Linear Regression: {feature.lower()} vs PRICE')
    plt.xlabel(feature)
    plt.ylabel('PRICE')
    plt.legend()
    plt.grid()
    plt.savefig(f'client/Image/{feature.lower()}_vs_PRICE.png')  # Save the plot
    plt.close()

# Call the function with the desired feature
plotFeature("BEDROOM")
plotFeature("BATHROOM")
plotFeature("Aana")
plotFeature("FLOOR")
plotFeature("Road")
