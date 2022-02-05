var map = L.map("map").setView([51.505, -0.09], 13);

var tiles = L.tileLayer(
  "https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw",
  {
    maxZoom: 18,
    attribution:
      'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, ' +
      'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    id: "mapbox/streets-v11",
    tileSize: 512,
    zoomOffset: -1,
  }
).addTo(map);

var users = $(".user");
var tabUser = [];

users.each(function () {
  var lat = $(this).attr("data-latitude");
  var lng = $(this).attr("data-longitude");
  var path = $(this).attr("data-avatar");
  var username = $(this).attr("data-username");
  var avatar = L.icon({
    iconUrl: "../uploads/avatars/" + path,
    iconSize: [48, 48], // size of the icon
  });
  tabUser.push([lng, lat]);
  L.marker([lng, lat], { icon: avatar }).addTo(map).bindPopup(username);
});
map.fitBounds([tabUser], { maxZoom: 10 });
