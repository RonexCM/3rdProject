<?php
session_start()
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>House Price Prediction System</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="app.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>House Price Prediction System</h1>
        <nav>
            <ul>
                <!-- <li><a href="#">Home</a></li> -->
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
                <?php if(isset($_SESSION['userid'])): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2>Predict the Price</h2>
       

        <!-- aana = float(request.form['aana'])
    location = request.form['location']
    bedroom = int(request.form['bedroom'])
    bathroom = int(request.form['bathroom'])
    floors = int(request.form['floors'])
    parking = int(request.form['parking'])
    road = float(request.form['road']) -->
    <form class="form">
        <h2>Area (Aana)</h2>
        <input class="area" type="text" id="uiAana" class="floatLabel" name="Aana" placeholder="Enter the Area in Aana">
        
        <h2>Bedroom</h2>
        <input class="area" type="text" id="uiBedroom" class="floatLabel" name="BHK" placeholder="Enter the BHK">
    
        <h2>Bathrooms</h2>
        <input class="area" type="text" id="uiBathrooms" class="floatLabel" name="Bathrooms" placeholder="Enter the number of Bathrooms">
    
        <h2>Floors</h2>
        <input class="area" type="text" id="uiFloors" class="floatLabel" name="Floors" placeholder="Enter the number of Floors">
    
        <h2>Parking</h2>
        <input class="area" type="text" id="uiParking" class="floatLabel" name="Parking" placeholder="Enter the number of Parking spaces">
    
        <h2>Road Width (Feet)</h2>
        <input class="area" type="text" id="uiRoad" class="floatLabel" name="Road" placeholder="Enter the Road Width">
    
        <h2>Location</h2>
        <select class="location" id="uiLocations">
            <option value="" disabled="disabled" selected="selected">Choose a Location</option>
            <option>Electronic City</option>
            <option>Rajaji Nagar</option>
            <option>Jhamshikhel</option>
            <option>Baneshwor</option>
            <option>Others</option>
        </select>
    
        <button class="submit" onclick="onClickedEstimatePrice()" type="button">Estimate Price</button>
    
        <div id="uiEstimatedPrice" class="result">
            <h2></h2>  
        </div>
    </form>
    </div> 

        <div class="home" id="home">
            <h2>Home</h2>
            <div class="content">
                <p>Welcome to the House Price Prediction System.<br>
                This is your go-to solution for predicting house prices. Lorem ipsum dolor sit amet consectetur adipisicing elit. Fugiat consectetur aperiam possimus adipisci alias quisquam, molestias doloribus inventore magnam dolorem consequuntur ipsam saepe delectus magni, voluptates ratione facilis expedita temporibus.</p>
                <img src="Image/House.jpg" alt="House Image">
            </div>
        </div>
    </div>

 
<div class="about" id="about">
    <h2>About</h2>
    <div class="content">
        <p>Welcome to the House Price Prediction System.<br>
        This is your go-to solution for predicting house prices. Lorem ipsum dolor sit amet consectetur adipisicing elit. Fugiat consectetur aperiam possimus adipisci alias quisquam, molestias doloribus inventore magnam dolorem consequuntur ipsam saepe delectus magni, voluptates ratione facilis expedita temporibus.</p>
        <img src="Image/House.jpg" alt="House Image">
    </div>
</div>

<div class="contact" id="contact">
    <h2>Contact</h2>
    <div class="content">
        <p>Welcome to the House Price Prediction System.<br>
        This is your go-to solution for predicting house prices. Lorem ipsum dolor sit amet consectetur adipisicing elit. Fugiat consectetur aperiam possimus adipisci alias quisquam, molestias doloribus inventore magnam dolorem consequuntur ipsam saepe delectus magni, voluptates ratione facilis expedita temporibus.</p>
        <img src="Image/House.jpg" alt="House Image">
    </div>
</div>
    
    <footer>
        <p>&copy; 2024 House Price Prediction System. All rights reserved.</p>
    </footer>

</body>
</html>
