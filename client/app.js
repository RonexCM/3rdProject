function onClickedEstimatePrice() {
    console.log("Estimate price button clicked");
    var sqft = document.getElementById("uiSqft");
    var bhk = document.getElementById("uiBHK");
    var bath = document.getElementById("uiBathrooms");
    var balcony = document.getElementById("uiBalcony");
    var location = document.getElementById("uiLocations");
    var estPrice = document.getElementById("uiEstimatedPrice");
  
    var url = "http://127.0.0.1:5000/predict_home_price"; //Use this if you are NOT using nginx which is first 7 tutorials
  
    $.post(url, {
        total_sqft: parseFloat(sqft.value),
        bhk: parseFloat(bhk.value),
        bath: parseFloat(bath.value),
        location: location.value,
        balcony: parseFloat(balcony.value)
    },function(data, status) {
        console.log(data.estimated_price);
        estPrice.innerHTML = "<h2> The Predicted Price is " + data.estimated_price.toString() + " Lakh</h2>";
        console.log(status);
    });
  }
  
  function onPageLoad() {
    var url = "http://127.0.0.1:5000/get_location_names"; // Use this if you are NOT using nginx which is first 7 tutorials
    $.get(url,function(data, status) {
        console.log("got response for get_location_names request");
        if(data) {
            var locations = data.locations;
            var uiLocations = document.getElementById("uiLocations");
            $('#uiLocations').empty();
            for(var i in locations) {
                var opt = new Option(locations[i]);
                $('#uiLocations').append(opt);
            }
        }
    });
  }
  
  window.onload = onPageLoad;