import pandas as pd
import numpy as np
import re
from sklearn.model_selection import ShuffleSplit, GridSearchCV
from sklearn.linear_model import Lasso, LinearRegression

# Your existing preprocessing code

df = pd.read_csv('Nepali_house_dataset.csv')

df2 = df.drop(columns=['TITLE', 'BUILDUP AREA', 'FACING', 'BUILT YEAR', 'PARKING', 'AMENITIES',])
df3 = df2.dropna()

df4 = df3.copy()

def extract_aana(value):
    if isinstance(value, str) and value.strip() != "":
        try:
            return float(value.split(' ')[0])
        except ValueError:
            return None
    return None

df4['Aana'] = df4['LAND AREA'].apply(extract_aana)

def extract_road_width(value):
    if isinstance(value, str) and value.strip() != "":
        if '-' in value:
            return None
        try:
            return float(value.split(' ')[0])
        except ValueError:
            return None
    return None

df4['Road'] = df4['ROAD ACCESS'].apply(extract_road_width)
df4.dropna(subset=['Road'], inplace=True)

df5 = df4.drop(columns=['LAND AREA', 'ROAD ACCESS'])

def convert_price(price_str):
    price_str = price_str.replace('Rs.', '').strip()
    
    if 'Lac/aana' in price_str or 'aana' in price_str:
        return None
    
    if 'Lac' in price_str:
        lac_value = re.search(r'(\d+(\.\d+)?)', price_str)
        if lac_value:
            return float(lac_value.group(0)) * 100000
        else:
            return None
            
    elif 'Cr' in price_str:
        cr_value = re.search(r'(\d+(\.\d+)?)', price_str)
        if cr_value:
            return float(cr_value.group(0)) * 10000000
        else:
            return None
            
    else:
        price_str = price_str.replace(',', '')
        try:
            return float(price_str)
        except ValueError:
            return None

df5['PRICE'] = df5['PRICE'].apply(convert_price)
df5 = df5.dropna(subset=['PRICE'])
df5['price_per_aana'] = df5['PRICE'] / df5['Aana']

df5 = df5.replace([np.inf, -np.inf], np.nan)
df5 = df5.dropna(subset=['price_per_aana'])

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

    for location, location_df in df.groupby('LOCATION'):
        bedroom_stats = {}
        
        for bedroom, bedroom_df in location_df.groupby('BEDROOM'):
            bedroom_stats[bedroom] = {
                'mean': np.mean(bedroom_df.price_per_aana),
                'std': np.std(bedroom_df.price_per_aana),
                'count': bedroom_df.shape[0]
            }
        
        for bedroom, bedroom_df in location_df.groupby('BEDROOM'):
            stats = bedroom_stats.get(bedroom - 1)
            if stats and stats['count'] > 5:
                exclude_indices = np.append(exclude_indices, bedroom_df[bedroom_df.price_per_aana < stats['mean']].index.values)
    
    return df.drop(exclude_indices, axis='index')

df8 = remove_bedroom_outliers(df7)
df8 = df8.drop(columns=['price_per_aana'])

df9 = pd.get_dummies(df8, columns=['LOCATION'], drop_first=True)
df9['PRICE'] = pd.to_numeric(df9['PRICE'], errors='coerce')
df9.dropna(subset=['PRICE'], inplace=True)

df9 = df9[df9['PRICE'] < df9['PRICE'].quantile(0.95)]

X = df9.drop('PRICE', axis=1)
y = df9['PRICE']

# Residual-based outlier removal function
def remove_residual_outliers(model, X, y):
    y_pred = model.predict(X)
    residuals = y - y_pred
    std_residuals = np.std(residuals)
    mean_residuals = np.mean(residuals)

    # Define outliers based on residuals greater than 2 standard deviations from mean
    outlier_indices = X[(residuals < (mean_residuals - 2 * std_residuals)) | (residuals > (mean_residuals + 2 * std_residuals))].index

    # Remove the outliers
    X_cleaned = X.drop(outlier_indices)
    y_cleaned = y.drop(outlier_indices)
    return X_cleaned, y_cleaned

def find_best_model_using_gridsearchcv(X, y):
    algos = {
        'linear_regression': {
            'model': LinearRegression(),
            'params': {
                'copy_X': [True, False],
                'fit_intercept': [True, False],
                'n_jobs': [1, 2, 3],
                'positive': [True, False]
            }
        },
        'lasso': {
            'model': Lasso(),
            'params': {
                'alpha': [1, 2],
                'selection': ['random', 'cyclic']
            }
        }
    }

    scores = []
    cv = ShuffleSplit(n_splits=5, test_size=0.2, random_state=0)
    for algo_name, config in algos.items():
        gs = GridSearchCV(config['model'], config['params'], cv=cv, return_train_score=False)
        gs.fit(X, y)
        
        # Remove residual outliers for the best model found
        best_model = gs.best_estimator_
        X_cleaned, y_cleaned = remove_residual_outliers(best_model, X, y)
        
        # Refit the grid search on cleaned data
        gs_cleaned = GridSearchCV(config['model'], config['params'], cv=cv, return_train_score=False)
        gs_cleaned.fit(X_cleaned, y_cleaned)
        
        scores.append({
            'model': algo_name,
            'best_score': gs_cleaned.best_score_,
            'best_params': gs_cleaned.best_params_
        })

    return pd.DataFrame(scores, columns=['model', 'best_score', 'best_params'])

# Run the updated grid search with outlier removal
print(find_best_model_using_gridsearchcv(X, y))
