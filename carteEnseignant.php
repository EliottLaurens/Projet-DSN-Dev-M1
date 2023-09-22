<?php
session_start();
if(!isset($_SESSION['role'])) {
    header('Location: login.html');
    exit;
}
if($_SESSION['role'] != "enseignant") {
    die("Accès non autorisé");
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <title>Administration de la Carte</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
	<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.4.1/MarkerCluster.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.4.1/MarkerCluster.Default.css" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.4.1/leaflet.markercluster.js"></script>

</head>
<body>

<div class="control-buttons">
	<button id="importCsvButton">Importer CSV</button>
	<input type="file" id="csvFileInput" accept=".csv" style="display: none;" />
    <button id="addPointButton">Ajouter un point</button>
    <button id="filterStateButton">Filtrer par état</button>
	<button id="filterTypeButton">Filtrer par type</button>
	<select id="stateFilter" style="display:none;">
        <option value="all">Tous</option>
        <option value="1">Gelé</option>
        <option value="2">Disponible</option>
        <option value="3">Semi-Disponible</option>
        <option value="4">Non Disponible</option>
    </select>
    <select id="typeFilter" style="display:none;">
        <option value="all">Tous</option>
        <option value="1">Maison de Retraite</option>
        <option value="2">Hôpital</option>
        <option value="3">Clinique</option>
        <option value="4">CCAS</option>
    </select>
</div>

<div id="addPointModal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000; background-color: white; padding: 20px; border: 1px solid black;">
    <form id="addPointForm">
        <label>Structure: <input type="text" name="structure" required></label><br>
		<label>Type:
            <select name="type">
                <option value="1">Maison de Retraite</option>
                <option value="2">Hôpital</option>
                <option value="3">Clinique</option>
                <option value="4">CCAS</option>
            </select>
        </label><br>
		<label>Professionnel(le): <input type="text" name="professionnel" required></label><br>
		<label>Mail professionnel(le): <input type="text" name="mailPro"></label><br>
		<label>Telephone professionnel(le): <input type="text" name="telPro"></label><br>
        <label>Date du dernier stage: <input type="date" name="lastDate" required></label><br>
        <label>Etat:
            <select name="etat">
                <option value="1">Gelé</option>
                <option value="2">Disponible</option>
                <option value="3">Semi-Disponible</option>
                <option value="4">Non Disponible</option>
            </select>
        </label><br>
        <label>Adresse: <input type="text" name="address" required></label><br>
		<label>Code Postal: <input type="text" name="postalCode" required></label><br>
		<label>Ville: <input type="text" name="city" required></label><br>
        <label>Latitude (optionnel): <input type="text" name="latitude"></label><br>
        <label>Longitude (optionnel): <input type="text" name="longitude"></label><br>
        <button type="button" onclick="closeModal()">Annuler</button>
        <input type="submit" value="Ajouter">
    </form>
</div>


<div id="map" style="width: 100%;"></div>

<script>
var map = L.map('map').setView([43.6, 1.44], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

var markers = L.markerClusterGroup();

fetch('getPoints.php')
.then(response => response.json())
.then(data => {
    data.forEach(point => {
	
		let markerColor = getMarkerColor(point.Etat);
        let customIcon = L.icon({
            iconUrl: `https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${markerColor}.png`,
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
			
        let marker = L.marker([point.Latitude, point.Longitude], { icon: customIcon, draggable: true, Etat: point.Etat, Type: point.Type})
            .bindPopup(`
				<form id="updateForm${point.ID}">
					<b>Adresse :</b> ${point.Adresse}, ${point.Codepostal} ${point.Ville}<br>
					<label><b>Structure:</b></label>
					<input type="text" id="structure${point.ID}" value="${point.Structure}"><br>
					<label><b>Type:</b></label>
					<select id="type${point.ID}">
						<option value="1" ${point.Type === 1 ? 'selected' : ''}>Maison de Retraite</option>
						<option value="2" ${point.Type === 2 ? 'selected' : ''}>Hôpital</option>
						<option value="3" ${point.Type === 3 ? 'selected' : ''}>Clinique</option>
						<option value="4" ${point.Type === 4 ? 'selected' : ''}>CCAS</option>
					</select><br>
					<label><b>Professionnel(le):</b></label>
					<input type="text" id="professionnel${point.ID}" value="${point.Professionnel}"><br>
					<label><b>Mail Professionnel(le):</b></label>
					<input type="text" id="mailPro${point.ID}" value="${point.MailPro}"><br>
					<label><b>Telephone Professionnel(le):</b></label>
					<input type="text" id="telPro${point.ID}" value="${point.TelPro}"><br>
					<label><b>Date du dernier stage:</b></label>
					<input type="date" id="date${point.ID}" value="${point.Datedernierstage}"><br>
					<label><b>Etat:</b></label>
					<select id="etat${point.ID}">
						<option value="1" ${point.Etat === 1 ? 'selected' : ''}>Gelé</option>
						<option value="2" ${point.Etat === 2 ? 'selected' : ''}>Disponible</option>
						<option value="3" ${point.Etat === 3 ? 'selected' : ''}>Semi-Disponible</option>
						<option value="4" ${point.Etat === 4 ? 'selected' : ''}>Non Disponible</option>
					</select><br>
					<button type="button" onclick="updatePoint(${point.ID})">Mettre à jour</button>
				</form>
			`);

        markers.addLayer(marker);
        
        marker.on('dragend', function(e) {
            updateMarker(point.ID, e.target.getLatLng().lat, e.target.getLatLng().lng);
        });
    });
    
    map.addLayer(markers);
});

document.getElementById("addPointButton").addEventListener("click", function() {
    document.getElementById("addPointModal").style.display = "block";
});

function closeModal() {
    document.getElementById("addPointModal").style.display = "none";
}

document.getElementById("addPointForm").addEventListener("submit", function(event) {
    event.preventDefault();
    
    let formData = new FormData(event.target);

    fetch("addPoint.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === "success") {
            location.reload(); // Recharge la page pour voir le nouveau point
        }
    })
    .catch(error => console.error("Error:", error));
});

function updatePoint(id) {
    let structure = document.getElementById(`structure${id}`).value;
    let date = document.getElementById(`date${id}`).value;
    let etat = document.getElementById(`etat${id}`).value;
	let type = document.getElementById(`type${id}`).value;
	let professionnel = document.getElementById(`professionnel${id}`).value;
	let mailPro = document.getElementById(`mailPro${id}`).value;
	let telPro = document.getElementById(`telPro${id}`).value;
    
    let formData = new FormData();
    formData.append('id', id);
    formData.append('structure', structure);
    formData.append('date', date);
    formData.append('etat', etat);
	formData.append('type', type);
	formData.append('professionnel', professionnel);
	formData.append('mailPro', mailPro);
	formData.append('telPro', telPro);

    fetch(`updatePoint.php`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        location.reload();
    });
}

function getMarkerColor(etat) {
    switch (etat) {
        case 1:
            return 'blue';
        case 2:
            return 'green';
        case 3:
            return 'yellow';
        case 4:
            return 'red';
        default:
            return 'grey';
    }
}

function filterMarkersByState(state) {
    markers.eachLayer(function(layer) {
        if (state === "all" || layer.options.Etat.toString() === state) {
            layer.setOpacity(1);
            layer.setZIndexOffset(0);
        } else {
            layer.setOpacity(0);
            layer.setZIndexOffset(-1000);
        }
    });
}

document.getElementById("stateFilter").addEventListener("change", function(e) {
    let selectedState = e.target.value;
    filterMarkersByState(selectedState);
});


function filterMarkersByType(type) {
    markers.eachLayer(function(layer) {
        if (type === "all" || layer.options.Type.toString() === type) {
            layer.setOpacity(1);
            layer.setZIndexOffset(0);
        } else {
            layer.setOpacity(0);
            layer.setZIndexOffset(-1000);
        }
    });
}

document.getElementById("typeFilter").addEventListener("change", function(e) {
    let selectedType = e.target.value;
    filterMarkersByType(selectedType);
});

document.getElementById('filterStateButton').addEventListener('click', function() {
	let typeFilter = document.getElementById('typeFilter');
	typeFilter.style.display = "none";
	typeFilter.value = "all";
	filterMarkersByType("all");
    let stateFilter = document.getElementById('stateFilter');
    if (stateFilter.style.display === "none" || stateFilter.style.display === "") {
        stateFilter.style.display = "block";
    } else {
        stateFilter.style.display = "none";
    }
});

document.getElementById('filterTypeButton').addEventListener('click', function() {
	let stateFilter = document.getElementById('stateFilter');
	stateFilter.style.display = "none";
	stateFilter.value = "all";
	filterMarkersByState("all");
    let typeFilter = document.getElementById('typeFilter');
    if (typeFilter.style.display === "none" || typeFilter.style.display === "") {
        typeFilter.style.display = "block";
    } else {
        typeFilter.style.display = "none";
    }
});

document.getElementById('importCsvButton').addEventListener('click', function() {
    document.getElementById('csvFileInput').click();
});

document.getElementById('csvFileInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const text = e.target.result;
        const rows = text.split(/\r?\n/).slice(1);
        const points = rows.map(row => {
            const cols = row.split(',');
            return {
                structure: cols[0] ? cols[0].trim() : '',
                type: cols[1] ? cols[1].trim() : '',
                professionnel: cols[2] ? cols[2].trim() : '',
                mailPro: cols[3] ? cols[3].trim() : '',
                telPro: cols[4] ? cols[4].trim() : '',
                lastDate: cols[5] ? cols[5].trim() : '',
                etat: cols[6] ? cols[6].trim() : '',
                address: cols[7] ? cols[7].trim() : '',
                postalCode: cols[8] ? cols[8].trim() : '',
                city: cols[9] ? cols[9].trim() : '',
                latitude: cols[10] ? parseFloat(cols[10].trim()) : 0,
                longitude: cols[11] ? parseFloat(cols[11].trim()) : 0,
            };
        });

        fetch('importPoints.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(points)
        }).then(response => {
            if (response.ok) return response.text();
            else return response.text().then(text => Promise.reject(text));
        }).then(text => {
            alert('Points importés avec succès !');
			location.reload();
        }).catch(error => {
            alert('Erreur lors de l’importation: ' + error);
        });
    };
    reader.readAsText(file);
});


</script>
</body>
</html>
