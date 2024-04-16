<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Calor de Equipos</title>
    <!-- Agregar enlaces a la biblioteca de OpenLayers -->
    <link rel="stylesheet" href="https://openlayers.org/en/v6.13.0/css/ol.css" type="text/css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        h2 {
            text-align: center;
            margin-top: 20px;
        }
        #map {
            width: 90%;
            height: 500px;
            margin: 20px auto;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container input[type="date"],
        .form-container input[type="time"],
        .form-container input[type="number"], /* Nuevo: campo de entrada para el número del equipo */
        .form-container button {
            display: block;
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-container button {
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
    </style>
    <script src="https://openlayers.org/en/v6.13.0/build/ol.js" type="text/javascript"></script>
</head>
<body>
    <h2>Mapa de Calor de Equipos</h2>
    <div id="map"></div> <!-- Contenedor para el mapa -->
    <div class="form-container">
        <label for="fecha-seleccionada">Fecha:</label>
        <input type="date" id="fecha-seleccionada">
        <label for="hora-inicio">Hora de Inicio:</label>
        <input type="time" id="hora-inicio">
        <label for="hora-fin">Hora de Fin:</label>
        <input type="time" id="hora-fin">
        <label for="equipo-id">Número del equipo:</label>
        <input type="number" id="equipo-id" placeholder="Número del equipo">
        <button onclick="actualizarMapa()">Consultar</button>
    </div>

    <script type="text/javascript">
        var map = new ol.Map({
            target: 'map',
            layers: [
                new ol.layer.Tile({
                    source: new ol.source.OSM()
                })
            ],
            view: new ol.View({ 
                center: ol.proj.fromLonLat([-58.369309, -34.579721]),
                zoom: 15
            })
        });

        // Función para actualizar el mapa con las ubicaciones del día y hora seleccionadas
        function actualizarMapa() {
            var fechaSeleccionada = document.getElementById('fecha-seleccionada').value;
            var horaInicio = document.getElementById('hora-inicio').value;
            var horaFin = document.getElementById('hora-fin').value;
            var equipoId = document.getElementById('equipo-id').value; // Nuevo: obtener el número del equipo

            // Concatenar fecha y hora de inicio
            var fechaHoraInicio = fechaSeleccionada + ' ' + horaInicio;
            // Concatenar fecha y hora de fin
            var fechaHoraFin = fechaSeleccionada + ' ' + horaFin;

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var ubicaciones = JSON.parse(this.responseText);
                    var heatmapLayer = new ol.layer.Heatmap({
                        source: new ol.source.Vector({
                            features: new ol.format.GeoJSON().readFeatures(ubicaciones, {
                                featureProjection: 'EPSG:3857'
                            })
                        }),
                        blur: 15,
                        radius: 10,
                        opacity: 0.7
                    });

                    // Borrar capa anterior de heatmap
                    map.getLayers().forEach(function(layer) {
                        if (layer instanceof ol.layer.Heatmap) {
                            map.removeLayer(layer);
                        }
                    });

                    // Agregar nueva capa de heatmap al mapa
                    map.addLayer(heatmapLayer);
                }
            };
            // Envía el parámetro equipoId al archivo PHP
            xhr.open("GET", "ubicaciones.php?fechaHoraInicio=" + fechaHoraInicio + "&fechaHoraFin=" + fechaHoraFin + "&equipoId=" + equipoId, true); // Nuevo: enviar el número del equipo
            xhr.send();
        }

        window.onload = function() {
            var now = new Date();
            var hour = ('0' + now.getHours()).slice(-2);
            var minutes = ('0' + now.getMinutes()).slice(-2);
            var currentTime = hour + ':' + minutes;
            var hourOneBefore = ('0' + (now.getHours() - 1)).slice(-2) + ':' + minutes;
            document.getElementById('fecha-seleccionada').valueAsDate = now;
            document.getElementById('hora-inicio').value = hourOneBefore;
            document.getElementById('hora-fin').value = currentTime;
        };
    </script>
</body>
</html
