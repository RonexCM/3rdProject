function onClickedEstimatePrice() {
  console.log("Estimate price button");
  var aana = document.getElementById("uiAana");
  var bedroom = document.getElementById("uiBedroom");
  var bath = document.getElementById("uiBathrooms");
  var floor = document.getElementById("uiFloors");
  // var parking = document.getElementById("uiParking");
  var road = document.getElementById("uiRoad");
  const selectedModel = document.querySelector(
    'input[name="model_type"]:checked'
  );

  console.log(selectedModel.value);
  var estPrice = document.getElementById("uiEstimatedPrice");
  var location = document.getElementById("uiLocations");
  var url = "http://127.0.0.1:5000/predict_home_price"; //Use this if you are NOT using nginx which is first 7 tutorials
  $.post(
    url,
    {
      aana: parseFloat(aana.value),
      bedroom: parseFloat(bedroom.value),
      bath: parseFloat(bath.value),
      location: location.value,
      floor: parseFloat(floor.value),
      // parking: parseFloat(parking.value),
      road: parseFloat(road.value),
      model_type: selectedModel.value,
    },
    function (data, status) {
      const priceInRupees = data.estimated_price;
      const priceInCrores = priceInRupees / 10000000;
      estPrice.innerHTML =
        "<h2> The Predicted Price is " +
        priceInCrores.toFixed(2).toString() +
        " Crores</h2>";
      console.log(priceInCrores);
      console.log(status);
    }
  );
}

function onPageLoad() {
  var url = "http://127.0.0.1:5000/get_location_names"; // Use this if you are NOT using nginx which is first 7 tutorials
  $.get(url, function (data, status) {
    console.log("got response for get_location_names request");
    if (data) {
      var locations = data.locations;
      var uiLocations = document.getElementById("uiLocations");
      $("#uiLocations").empty();
      for (var i in locations) {
        var opt = new Option(locations[i]);
        $("#uiLocations").append(opt);
      }
    }
  });
}

window.onload = onPageLoad;
