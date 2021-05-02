//
//
// public/assets/js/OpenStreetMap.js
// Appelé depuis templates/page/contact.html.twig 
//
window.onload = () => {
// Initialise la carte
    //let zone = document.querySelector('#');

    let carte = L.map('carteContact').setView([47.91, 1.9], 13); 
    carte.on('click', onMapClick);

    // Charge les tuiles
    L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
        attribution: 'Données <a href="//osm.org/copyright">OpenStreetMap</a>',
        minZoom: 1,
        maxZoom: 20
    }).addTo(carte);
    // zone circulaire
    var circle = L.circle([47.918, 1.895], {
    color: 'red',
    fillColor: '#f03',
    fillOpacity: 0.5,
    radius: 500
    }).addTo(carte);

    // marqueurs
    var sites = {
        "cathedrale": {"lat": 47.901857, "lon": 1.910372, "nom": "Cathédrale"},
        "esat": {"lat": 47.886524, "lon": 1.875373, "nom": "ESAT"}
    };

    for(site in sites){
        var marqueur = L.marker([ sites[site].lat, sites[site].lon ]).addTo(carte);
        marqueur.bindPopup("<p>"+ sites[site].nom +"</p>")
    }

    // fonctions
    function onMapClick(e) { alert("Vous avez sélectionné le point " + e.latlng); }

}