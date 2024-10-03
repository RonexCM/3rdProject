var isEstimate = false;
var flexContainer = document.getElementById("formActions");
function onClickedEstimatePrice() {
  console.log("Estimate price button");
  var aana = document.getElementById("uiAana");
  var bedroom = document.getElementById("uiBedroom");
  var bath = document.getElementById("uiBathrooms");
  var floor = document.getElementById("uiFloors");
  // var parking = document.getElementById("uiParking");
  var road = document.getElementById("uiRoad");
  var price = document.getElementById("price");
  const selectedModel = document.querySelector(
    'input[name="model_type"]:checked'
  );

  var estPrice = document.getElementById("uiEstimatedPrice");
  var location = document.getElementById("uiLocations");
  if (
    !aana.value ||
    isNaN(parseFloat(aana.value)) ||
    parseFloat(aana.value) < 2.5
  ) {
    return alert("Please enter a valid number for Aana (must be >= 2.5)");
  }
  if (
    !bedroom.value ||
    isNaN(parseInt(bedroom.value)) ||
    parseInt(bedroom.value) < 1
  ) {
    return alert("Please enter a valid number of bedrooms (must be >= 1)");
  }
  if (!bath.value || isNaN(parseInt(bath.value)) || parseInt(bath.value) < 1) {
    return alert("Please enter a valid number of bathrooms (must be >= 1)");
  }
  if (
    !floor.value ||
    isNaN(parseInt(floor.value)) ||
    parseInt(floor.value) < 1
  ) {
    return alert("Please enter a valid number of floors (must be >= 1)");
  }
  if (
    !road.value ||
    isNaN(parseFloat(road.value)) ||
    parseFloat(road.value) < 5
  ) {
    return alert("Please enter a valid road size in meters (must be >= 5)");
  }
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
      price.value = priceInCrores.toFixed(2).toString() + " Crores</h2>";
      estPrice.innerHTML =
        "<h2> The Predicted Price is " +
        priceInCrores.toFixed(2).toString() +
        " Crores</h2> <div class='flex' style='display: flex;gap:20px;'><button class='submit' style='width: 125px;' type='submit' name='save'>Save</button> <button class='submit' style='width: 125px;' onclick='resetForm();' name='reset'>Reset</button> </div>";

      // <button class="submit" style="width: 125px;" type="submit" name="save">Save</button>

      isEstimate = true;
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
