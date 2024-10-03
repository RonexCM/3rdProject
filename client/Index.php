<?php
session_start();
require 'connection.php'; // Include database connection

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['save'])) {
    // Ensure that the user session is valid
    if (isset($_SESSION['userid'])) {
        $user_id = $_SESSION['userid']; // Retrieve the user ID from the session
    } else {
        echo "User not logged in.";
        exit();
    }

    // Retrieve form data and sanitize it
    $aana = mysqli_real_escape_string($conn, $_POST['Aana']);
    $bedroom = (int)$_POST['Bedroom']; // Cast to integer
    $bathroom = (int)$_POST['Bathrooms']; // Cast to integer
    $floor = (int)$_POST['Floors']; // Cast to integer
    $road = mysqli_real_escape_string($conn, $_POST['Road']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $price = (float)$_POST['Price']; // Cast to float for numeric price

    // Prepare SQL query
    $model_type = mysqli_real_escape_string($conn, $_POST['model_type']); // Ensure it's sanitized
    $date = date('Y-m-d H:i:s'); // Current date and time

    // Insert user data into the predictions table
    $sql = "INSERT INTO predictions (user_id, aana, bedroom, bathroom, floor, road, location, price, model_type, date) 
            VALUES ('$user_id', '$aana', $bedroom, $bathroom, $floor, '$road', '$location', $price, '$model_type', '$date')";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        // echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error; // Show detailed error message
    }
}
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
<style>
    .tab {
            display: inline-block;
            padding: 10px 20px;
            color: white;
            text-align: center;
            text-decoration: none;
            margin-right: 5px;
            cursor: pointer;
        }

        .tab.active {
 /* Active tab color */
        }

        .content {
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 15px;
        }

        .hidden {
            display: none;
        }
        .actions a {
        padding: 5px 10px;
        margin:none;
        color: black;
        text-decoration: none;
        font-size: 16px;

}

.actions a:hover {
    color: black;

}

.actions a.active {
    color: #28a745; /* Highlight active link in green */
    text-decoration: none; /* Remove underline from active link */
    font-weight: bold;
    border-bottom:3px solid; 
}
</style>

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
                    <li><a href="my_prediction.php?model_type=">My Prediction</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <div class="container">
        <h2>Predict the Price</h2>
       
    <form class="form" method="POST">
        <h2>Area (Aana)</h2>
        <input class="area" type="text" id="uiAana" class="floatLabel" name="Aana" placeholder="Enter the Area in Aana">
        
        <h2>Bedroom</h2>
        <input class="area" type="text" id="uiBedroom" class="floatLabel" name="Bedroom" placeholder="Enter the Bedroom">
    
        <h2>Bathrooms</h2>
        <input class="area" type="text" id="uiBathrooms" class="floatLabel" name="Bathrooms" placeholder="Enter the number of Bathrooms">
    
        <h2>Floors</h2>
        <input class="area" type="text" id="uiFloors" class="floatLabel" name="Floors" placeholder="Enter the number of Floors">
    
        <h2>Road Width (Feet)</h2>
        <input class="area" type="text" id="uiRoad" class="floatLabel" name="Road" placeholder="Enter the Road Width">
    
        <h2>Location</h2>
        <select class="location" id="uiLocations" name="location">
            <option value="" disabled="disabled" selected="selected">Choose a Location</option>
            <option>Electronic City</option>
            <option>Rajaji Nagar</option>
            <option>Jhamshikhel</option>
            <option>Baneshwor</option>
            <option>Others</option>
        </select>
        <label>Select Model:</label><br>
        <input type="radio" id="linear" name="model_type" value="linear" checked>
        <label for="linear">Linear Regression</label><br>
        <input type="radio" id="lasso" name="model_type" value="lasso">
        <label for="lasso">Lasso Regression</label><br><br>
        <input type="text" style="display:none;" name="Price" id="price"/>
        <button class="submit" onclick="onClickedEstimatePrice()" type="button">Estimate Price</button>
        
    
        <div id="uiEstimatedPrice" class="result">
            <h2></h2>  
        </div>
        
    </form>
    </div> 
    <div class="flex" style="display:flex; justify-content:center; align-items:center;">
        <div class="actions">

            <a  class="tab active" id="relationTab" onclick="showRelation()">Relation</a>
        </div>
        <div class="actions">

            <a  class="tab" id="compareTab" onclick="showCompare()">Compare</a>
        </div>
    </div>
    <div  id="compareDiv" class="content hidden home">
        <h1 style="font-size: 25px;">Comparison of Models based on Best Score: </h1><br/>
        <div class="imgContainer">
                <img src="Image/compare.png" alt="Regression Plot">
            </div>
        </div>
    <div  id="relationDiv" class="content home">
        <h1 style="font-size: 25px;">Relation of Each Independent Variable with Dependent Variable: </h1>
        <div class="btnContainer">
            <button onclick="showImage('Aana')">Aana</button>
            <button onclick="showImage('Bathroom')">Bathroom</button>
            <button onclick="showImage('Bedroom')">Bedroom</button>
            <button onclick="showImage('Floor')">Floor</button>
            <button onclick="showImage('Road')">Road</button>
        </div>

        <div class="imgContainer">
            <img id="regressionImage" src="Image/default.png" alt="Regression Plot">
        </div>

    </div>
        <div class="home" id="home">
            <h2>Home</h2>
            <div class="content">
                <p>Welcome to the House Price Prediction System.<br>
                This is your go-to solution for predicting house prices. Lorem ipsum dolor sit amet consectetur adipisicing elit. Fugiat consectetur aperiam possimus adipisci alias quisquam, molestias doloribus inventore magnam dolorem consequuntur ipsam saepe delectus magni, voluptates ratione facilis expedita temporibus.</p>
                <img src="Image/House.jpg" alt="House Image">
            </div>
        </div>
    </di>

 
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
    <script>
        function showRelation() {
        // event.preventDefault();
        document.getElementById('relationDiv').classList.remove('hidden');
        document.getElementById('compareDiv').classList.add('hidden');
        setActiveTab('relationTab');
    }

    function showCompare() {
        // event.preventDefault();
        document.getElementById('relationDiv').classList.add('hidden');
        document.getElementById('compareDiv').classList.remove('hidden');
        setActiveTab('compareTab');
    }

    function setActiveTab(activeTabId) {
        const tabs = document.querySelectorAll('.tab');
        tabs.forEach(tab => {
            tab.classList.remove('active');
        });
        document.getElementById(activeTabId).classList.add('active');
    }
        function resetForm() {
        // Reset all the input fields in the form
        document.querySelector('.form').reset();

        // Clear the estimated price output (if any)
        document.getElementById("uiEstimatedPrice").querySelector("h2").innerHTML = "";
    }
        function showImage(feature) {
            // Define the image source based on the feature
            console.log(feature.toLowerCase())
            const imageSource = `Image/${feature.toLowerCase()}_vs_PRICE.png`; // Adjust the image path as needed
            
            // Get the image element
            const imgElement = document.getElementById('regressionImage');
            imgElement.src = imageSource;  // Set the image source
            imgElement.style.display = 'block';  // Show the image
        }
    </script>
</body>
</html>
