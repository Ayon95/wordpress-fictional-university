class LeafletMap {
  constructor() {
    const mapContainers = document.querySelectorAll(".acf-map");
    mapContainers.forEach((container) => this.createMap(container));
  }

  createMap(container) {
    const markerElements = container.querySelectorAll(".marker");
    const bounds = L.latLngBounds();

    const map = L.map(container).setView([0, 0], 18);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      maxZoom: 19,
      tileSize: 512,
      zoomOffset: -1,
      id: "mapbox/streets-v11",
      attribution:
        'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    markerElements.forEach((markerElement) => {
      const { latitude, longitude } = markerElement.dataset;
      const marker = L.marker([latitude, longitude]).addTo(map);
      const markerLatLng = L.latLng(latitude, longitude);

      if (markerElement.innerHTML) {
        marker.bindPopup(markerElement.innerHTML);
      }

      // We can center the markers by including marker positions in the map area
      bounds.extend(markerLatLng);
    });

    map.fitBounds(bounds);
  }
}

export default LeafletMap;
