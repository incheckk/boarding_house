import sys
import json
import pandas as pd
import numpy as np
from sklearn.linear_model import LinearRegression

# Optional: suppress only the specific harmless warning
import warnings
warnings.filterwarnings("ignore", message="X does not have valid feature names")

try:
    # Read JSON from PHP (via stdin)
    input_json = sys.stdin.read().strip()
    
    if not input_json:
        print(json.dumps({"error": "No data received"}))
        sys.exit(0)

    data = json.loads(input_json)
    df = pd.DataFrame(data)

    if df.empty or len(df) < 2:
        print(json.dumps({
            "correlation": 0.00,
            "predicted_reservations": 0.0,
            "top_rooms": [],
            "group_stats": {}
        }))
        sys.exit(0)

    # Convert to numeric
    df['views'] = pd.to_numeric(df['views'], errors='coerce').fillna(0)
    df['reservations'] = pd.to_numeric(df['reservations'], errors='coerce').fillna(0)

    # 1. Correlation
    correlation = df['views'].corr(df['reservations'])
    correlation = round(float(correlation if pd.notna(correlation) else 0.0), 3)

    # 2. Linear Regression Prediction (+10 views above average)
    model = LinearRegression()
    X = df[['views']]  # Keep as DataFrame to avoid warning
    y = df['reservations']
    model.fit(X, y)

    avg_views = df['views'].mean()
    future_X = pd.DataFrame([[avg_views + 10]], columns=['views'])  # Same format
    predicted = float(model.predict(future_X)[0])
    predicted = round(predicted, 2)

    # 3. Top 3 most viewed rooms
    top_rooms = (df.nlargest(3, 'views')
                 [['room_number', 'views', 'reservations']]
                 .to_dict(orient='records'))

    # 4. Average by room type
    group_stats = (df.groupby('room_size')
                   .agg({'views': 'mean', 'reservations': 'mean'})
                   .round(2)
                   .to_dict(orient='index'))

    # Final clean result
    result = {
        "correlation": correlation,
        "predicted_reservations": predicted,
        "top_rooms": top_rooms,
        "group_stats": {str(k): v for k, v in group_stats.items()}
    }

    print(json.dumps(result))

except Exception as e:
    error_msg = str(e).replace('"', "'")
    print(json.dumps({"error": f"Python crash: {error_msg}"}))
