from flask import Flask,request, jsonify
import util
app = Flask(__name__)

@app.route('/get_location_names',methods=['GET'])
def get_location_names():
    response = jsonify({
        'locations': util.get_location_names()
    })
    response.headers.add('Access-Control-Allow-Origin','*')
    
    return response
    
@app.route('/predict_home_price',methods=['POST'])
def predict_home_price():
    # total_sqft = float(request.form['total_sqft'])
    # location = request.form['location']
    # bhk = int(request.form['bhk'])
    # bath = int(request.form['bath'])
    # balcony = int(request.form['balcony'])
    aana = float(request.form['aana'])
    location = request.form['location']
    bedroom = int(request.form['bedroom'])
    bathroom = int(request.form['bath'])
    floors = int(request.form['floor'])
    # parking = int(request.form['parking'])
    road = float(request.form['road'])
    model_type = request.form['model_type']

    response = jsonify({
        # 'estimated_price': util.get_estimated_price(location,total_sqft, bhk, bath,balcony)
        'estimated_price': util.get_estimated_price(location, aana, bedroom, bathroom, floors, road,model_type)
    })
    response.headers.add('Access-Control-Allow-Origin','*')
    
    return response

if __name__ == "__main__":
    print("Start server for House Price predicton")
    util.load_saved_artifacts()
    app.run()