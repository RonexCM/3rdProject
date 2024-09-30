import matplotlib.pyplot as plt
import pandas as pd

# Create a DataFrame with your best scores
data = {
    'model': ['linear_regression', 'lasso'],
    'best_score': [0.769466, 0.701519 ],
}

df = pd.DataFrame(data)

# Plotting the scores
plt.bar(df['model'], df['best_score'], color=['blue', 'orange'])
plt.ylabel('Best Score (R²)')
plt.title('Model Comparison: Best Scores for Linear Regression and Lasso')

# Zooming in on the y-axis # Set the range to focus on the area of interest
plt.grid(axis='y')  # Add grid lines for better readability
plt.show()
