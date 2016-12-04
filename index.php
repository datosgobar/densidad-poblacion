<!DOCTYPE html>
<html>

  <head>
      <title>Densidad de Problación</title>
      <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
      <meta charset='utf-8' />
      <!-- jQuery -->
      <script src="javascript/jQuery/jquery-3.1.1.min.js"></script>
      <!-- MapBox -->
      <script src='https://api.mapbox.com/mapbox-gl-js/v0.26.0/mapbox-gl.js'></script>
      <link href='https://api.mapbox.com/mapbox-gl-js/v0.26.0/mapbox-gl.css' rel='stylesheet' />
      <!-- MapBox - GeoCoding -->
      <script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v1.3.1/mapbox-gl-geocoder.js'></script>
      <link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v1.3.1/mapbox-gl-geocoder.css' type='text/css' />
      <!-- JS -->
      <script language="JavaScript" src="http://www.geoplugin.net/javascript.gp" type="text/javascript"></script>
      <script type="text/javascript">
        window.onload = function () {
          var data = {
            generarReferenciaId: function generarReferenciaId(string) {
              var texto = string.toLowerCase();
              var reemplazar = [["á", "a"], ["é", "e"], ["í", "i"], ["ó", "o"], ["ú", "u"], ["ñ", "n"], [" ", ""]];

              for (var i = 0; i < reemplazar.length; i++) {
                while (texto.indexOf(reemplazar[i][0]) != -1) {
                  texto = texto.replace(reemplazar[i][0], reemplazar[i][1]);
                }
              }

              return texto;
            },
          };
          var varGlobal = {
            datosHover: { features: [], type: "FeatureCollection" },
            provincias: {
              prov02: { nombre: 'Ciudad Autónoma de Buenos Aires', coordenadas: [-58.5737501, -34.6156537] },
              prov06: { nombre: 'Buenos Aires', coordenadas: [-57.9882757, -34.9205233] },
              prov10: { nombre: 'Catamarca', coordenadas: [-65.8102653, -28.4645295] },
              prov14: { nombre: 'Córdoba', coordenadas: [-64.3347743, -31.3993435] },
              prov18: { nombre: 'Corrientes', coordenadas: [-58.8625991, -27.486234] },
              prov22: { nombre: 'Chaco', coordenadas: [-59.0655577, -27.4606439] },
              prov26: { nombre: 'Chubut', coordenadas: [-65.1382395, -43.2988118] },
              prov30: { nombre: 'Entre Ríos', coordenadas: [-60.585175, -31.74729934] },
              prov34: { nombre: 'Formosa', coordenadas: [-58.2651823, -26.1721389] },
              prov38: { nombre: 'Jujuy', coordenadas: [-65.3757678, -24.2053236] },
              prov42: { nombre: 'La Pampa', coordenadas: [-64.3714478, -36.6193668] },
              prov46: { nombre: 'La Rioja', coordenadas: [-66.9259895, -29.4142785] },
              prov50: { nombre: 'Mendoza', coordenadas: [-68.8936245, -32.8833303] },
              prov54: { nombre: 'Misiones', coordenadas: [-55.9596213, -27.396305] },
              prov58: { nombre: 'Neuquén', coordenadas: [-68.1856129, -38.9412136] },
              prov62: { nombre: 'Río Negro', coordenadas: [-63.035245, -40.8250187] },
              prov66: { nombre: 'Salta', coordenadas: [-65.5008411, -24.7960684] },
              prov70: { nombre: 'San Juan', coordenadas: [-68.5677821, -31.5317707] },
              prov74: { nombre: 'San Luis', coordenadas: [-66.379878, -33.2976372] },
              prov78: { nombre: 'Santa Cruz', coordenadas: [-69.3419608, -51.6284629] },
              prov82: { nombre: 'Santa Fe', coordenadas: [-60.7764694, -31.6181235] },
              prov86: { nombre: 'Santiago del Estero', coordenadas: [-64.3372607, -27.8016971] },
              prov90: { nombre: 'Tucumán', coordenadas: [-65.2928061, -26.8328416] },
              prov94: { nombre: 'Tierra del Fuego', coordenadas: [-68.3730148, -54.8053847] }
            },
            elementHover: null
          };

          iniciarApp(data, varGlobal);
          function iniciarApp(data, varGlobal) {
            if (window.location.search !== "") {
              data.coordenadas = [parseFloat(window.location.search.slice(1, window.location.search.length - 1).split(",")[0]), parseFloat(window.location.search.slice(1, window.location.search.length - 1).split(",")[1])];
              console.log('coordenadas URL'); // Entorno de desarrollo
              renderMap(data, varGlobal);
            } else {
              $.get("http://freegeoip.net/json/<?php echo $_SERVER['REMOTE_ADDR']; ?>", function(geoLocation) {
                if (geoLocation.latitude == 0 && geoLocation.longitude == 0) {
                  data.coordenadas = [-58.5737501, -34.6156537];
                  console.log('coordenadas defecto'); // Entorno de desarrollo
                } else {
                  data.coordenadas = [geoLocation.longitude, geoLocation.latitude];
                  console.log('coordenadas geolocalizadas'); // Entorno de desarrollo
                }
                renderMap(data, varGlobal);
              });
            }
          }
          function renderMap(data, varGlobal) {
            // MapBox
            mapboxgl.accessToken = 'pk.eyJ1IjoiZnJhbWxvcGV6IiwiYSI6ImNpdWhrYWdvNjAwdjYzcHFmaDl1YTQyOTYifQ.g62pBFWJnDt8vIiHQ5HM8A';
            if (!mapboxgl.supported()) {
              alert('Este sitio no es soportado por este navegador.');
            } else {
              data.mapbox = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/framlopez/civjo1vi600582iqoly4paool',
                center: data.coordenadas,
                zoom: 8,
                minZoom: 8,
                maxZoom: 11,
                pitch: 60,
                bearing: 0,
                maxBounds: [-72.9, -54.9, -53.6, -21.9]
              });

              data.mapbox.addControl(new mapboxgl.NavigationControl({ position: 'bottom-right' }));
              data.mapbox.addControl(new mapboxgl.ScaleControl({ position: 'bottom-left', maxWidth: 80, unit: 'metric' }));
              data.mapbox.addControl(new mapboxgl.Geocoder({ country: 'ar', accessToken: mapboxgl.accessToken }));

              data.mapbox.on('load', function (d) {
                data.mapbox.setPaintProperty('dataset', 'fill-color', { 'property': 'color', 'type': 'identity' });
                data.mapbox.setPaintProperty('dataset', 'fill-extrude-height', { 'property': 'densidad', 'type': 'identity' });
                // data.mapbox._render();
                data.mapbox.addSource("Argentina-Hover", {
                  'type': 'geojson',
                  'data': varGlobal.datosHover
                });
                data.mapbox.addLayer({
                  'id': 'color-hover',
                  'source': 'Argentina-Hover',
                  'type': 'fill',
                  "paint": {
                    'fill-color': 'black',
                    'fill-extrude-height': {
                      'property': 'densidad',
                      'type': 'identity'
                    },
                    'fill-opacity': 0.5
                  }
                }, 'country-label-sm');
              });
              data.mapbox.on('mousemove', function (e) {
                var features = data.mapbox.queryRenderedFeatures(e.point, { layers: ['dataset'] });

                if (features.length) { // Si hay datos
                  varGlobal.datosHover.features[0] = features[0];
                  data.mapbox.getSource('Argentina-Hover').setData(varGlobal.datosHover);
                  data.mapbox.getCanvas().style.cursor = 'pointer'; // Defino tipo de cursor
                  $('#tooltip').css({ "transform": "translate(" + (e.point.x + 10) + "px, " + (e.point.y + 10) + "px)" }).show();
                  $('#contentTooltip').empty().append(`<section>
                    <article>${ varGlobal.provincias['prov' + features[0].properties.prov_id].nombre }</article>
                    <article>
                      <svg x="0px" y="0px" viewBox="0 0 13.8 12.2">
                        <path fill="white" d="M13.8,12.2c-4.6,0-9.1,0-13.7,0c0-0.4-0.1-0.8-0.1-1.1c0-1.2,0.5-1.9,1.7-2.3c0.9-0.3,1.8-0.5,2.7-0.8c0.3-0.1,0.6-0.3,0.9-0.5c0.2-0.2,0.3-0.4,0-0.7C4.4,5.7,3.9,4.4,3.9,3C4,1.3,5.5-0.1,7,0C8.6,0.1,10,1.7,9.9,3.4C9.8,4.7,9.3,5.8,8.3,6.8C8.1,7,8.1,7.3,8.4,7.5c0.3,0.2,0.6,0.4,0.9,0.5c0.9,0.3,1.8,0.5,2.7,0.8c1.4,0.4,2,1.3,1.9,2.8C13.8,11.8,13.8,12,13.8,12.2z" />
                      </svg>
                      <span class="textWhite">${ new Intl.NumberFormat("de-DE").format(parseFloat(features[0].properties.poblacion).toFixed(0)) } &nbsp; </span>( ${ new Intl.NumberFormat("de-DE").format(parseFloat(features[0].properties.densidad).toFixed(0)) } ) / km<sup>2</sup>
                    </article>
                    <article>
                      <svg x="0px" y="0px" viewBox="0 0 9.6 10">
                        <polyline class="areaIconSvg" points="9.1,8 9.1,9.5 7.6,9.5" />
                        <line class="areaIconSvg" stroke-dasharray="1.8796,1.8796" x1="5.8" y1="9.5" x2="2.9" y2="9.5"/>
                        <polyline class="areaIconSvg" points="2,9.5 0.5,9.5 0.5,8" />
                        <line class="areaIconSvg" stroke-dasharray="1.9848,1.9848" x1="0.5" y1="6" x2="0.5" y2="3"/>
                        <polyline class="areaIconSvg" points="0.5,2 0.5,0.5 2,0.5" />
                        <line class="areaIconSvg" stroke-dasharray="1.8796,1.8796" x1="3.9" y1="0.5" x2="6.7" y2="0.5"/>
                        <polyline class="areaIconSvg" points="7.6,0.5 9.1,0.5 9.1,2" />
                        <line class="areaIconSvg" stroke-dasharray="1.9848,1.9848" x1="9.1" y1="4" x2="9.1" y2="7"/>
                      </svg>
                      <span class="textWhite">${ parseFloat(features[0].properties.area).toFixed(3) } km<sup>2</sup></span>
                    </article>
                  </section>`);

                  // Cuando se pasa el mouse por una provincia en el mapa, se pinta la provincia en el miniMapa.
                  var selector = '#' + data.generarReferenciaId(varGlobal.provincias['prov' + features[0].properties.prov_id].nombre);
                  var elemento = $(selector);

                  varGlobal.elementHover !== null ? $(varGlobal.elementHover).css({ 'stroke-width': '0.5' }) : ''; // Si es la primera vez, no hace nada, sino, borra el grosor de linea del anterior selector.
                  varGlobal.elementHover = selector; // Guarda una referencia del selector que se modifico para poder borrarlo en la siguiente iteración.
                  elemento.css({ 'stroke-width': '2' }); // Cambia el grosor de linea de la provincia seleccionada en el miniMapa.
                  $('#provs')[0].append(elemento[0]); // Cambia el orden de los svg para que se vean todos los bordes.
                } else {
                  varGlobal.datosHover.features[0] = '';
                  data.mapbox.getSource('Argentina-Hover').setData(varGlobal.datosHover);
                  data.mapbox.getCanvas().style.cursor = ''; // Defino tipo de cursor
                  $('#tooltip').hide();
                  varGlobal.elementHover !== null ? $(varGlobal.elementHover).css({ 'stroke-width': '0.5' }) : varGlobal.elementoHover = null;
                }
              });
              data.mapbox.on('mouseout', function (e){
                varGlobal.datosHover.features[0] = '';
                data.mapbox.getSource('Argentina-Hover').setData(varGlobal.datosHover);
                data.mapbox.getCanvas().style.cursor = ''; // Defino tipo de cursor
                $('#tooltip').hide();
                varGlobal.elementHover !== null ? $(varGlobal.elementHover).css({ 'stroke-width': '0.5' }) : varGlobal.elementoHover = null;
              });

              funciones(data, varGlobal);
            }

            function funciones(datos, extras) {
              showTutorial(extras);
              panelIzquierdo();
              miniMapa(datos, extras);
              posicionamiento();

              function showTutorial(extras) {
                if (checkCookie("tutorial") == false) {
                  window.hideTutorial = hideTutorial;
                  $('body').prepend('<div id="tutorial"><div class="containerTutorial"><h3>Titulo</h3><p>¡BIENVENIDOS! En esta visualización te invitamos a explorar como nos distribuimos dentro del territorio de Argentina. Podés recorrerlo con ayuda del mapa lateral o con el buscador seleccionando la provincia que quieras ver. Cuanto más oscuro y alto es el polígono más habitantes viven allí.</p><button class="visualizacion" onClick="window.hideTutorial();">IR AL MAPA 3D</button><button class="close" onClick="window.hideTutorial();">X</button></div></div>');
                }
              }
              function hideTutorial() {
                setCookie('tutorial', 'true');
                $('#tutorial').remove();
              }
              function setCookie(cname, cvalue) {
                var d = new Date();
                d.setTime(d.getTime() + (1000*365*24*60*60*1000));
                var expires = "expires="+d.toUTCString();
                window.document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
              }
              function getCookie(cname) {
                  var name = cname + "=";
                  var ca = document.cookie.split(';');

                  for(var i = 0; i < ca.length; i++) {
                      var c = ca[i];
                      while (c.charAt(0) == ' ') {
                          c = c.substring(1);
                      }
                      if (c.indexOf(name) == 0) {
                          return c.substring(name.length, c.length);
                      }
                  }

                  return "";
              }
              function checkCookie(cname) {
                var tutorial = getCookie(cname);

                if (tutorial == "") {
                  setCookie(cname, "false");
                  return false;
                } else if (tutorial == "false") {
                  return false;
                } else if (tutorial == "true") {
                  return true;
                }
              }
              function panelIzquierdo() {
                var panelState = 0;

                $("#panel_show").click(function () {
                  // Panel Izquierdo Mostrar/Ocultar
                  if (panelState === 0) {
                    $("#container_panel").fadeOut("fast");
                    $("#panel_show").css({ 'border-radius': '0px 5px 5px 0px' }).children().css({ '-webkit-transform': 'rotate(180deg)', '-moz-transform': 'rotate(180deg)', '-o-transform': 'rotate(180deg)', '-ms-transform': 'rotate(180deg)', 'transform': 'rotate(180deg)' });
                    $("#map > div.mapboxgl-control-container > div.mapboxgl-ctrl-top-left > div > input[type='text']").css({ 'border-radius': '5px 0px 0px 5px' });
                    panelState = 1;
                  } else {
                    $("#container_panel").fadeIn("fast");
                    $("#panel_show").css({ 'border-radius': '0px 5px 0px 0px' }).children().css({ '-webkit-transform': 'rotate(0deg)', '-moz-transform': 'rotate(0deg)', '-o-transform': 'rotate(0deg)', '-ms-transform': 'rotate(0deg)', 'transform': 'rotate(0deg)' });
                    $("#map > div.mapboxgl-control-container > div.mapboxgl-ctrl-top-left > div > input[type='text']").css({ 'border-radius': '5px 0px 0px 0px' });
                    panelState = 0;
                  }
                });

                $('div.mapboxgl-ctrl-top-left input[type="text"]').attr('placeholder', 'Buscá una provincia'); // Se modifica placeholder de mapbox

                $('.mapboxgl-ctrl-attrib.mapboxgl-ctrl').hide();
              };
              function miniMapa(datos, extras) {
                function _loop() {
                  var elemento = $('#' + datos.generarReferenciaId(extras.provincias[provincia].nombre));
                  var coordenada = extras.provincias[provincia].coordenadas;

                  elemento.mouseover(function (e) {
                    $(this).css({ 'stroke-width': '2' });
                    $('#provs').append($(this)[0]);
                  });
                  elemento.mouseout(function (e) {
                    $(this).css({ 'stroke-width': '0.5' });
                  });
                  elemento.click(function (e) {
                    datos.mapbox.flyTo({ center: coordenada });
                  });
                };

                for (var provincia in extras.provincias) {
                  _loop();
                }
              };
              function posicionamiento() {

                var medidas = { 'alto': 0, 'ancho': 0 }
                  , estadoButton = false;

                $(window).resize(function(){
                  medidas = calcularMedidas(medidas);

                  if (medidas.ancho > 600) {
                    $('.nav_map').removeAttr('style');
                    estadoButton = false;
                  }

                  posicionBuscador(medidas);

                });

                $('#buttonNav').click(function(e){
                  if (estadoButton == false) {
                    $('.nav_map').css({ 'display': 'flex' });
                    $(this).css({ '-webkit-transform': 'rotate(180deg)', '-moz-transform': 'rotate(180deg)', '-o-transform': 'rotate(180deg)', '-ms-transform': 'rotate(180deg)', 'transform': 'rotate(180deg)' });
                    estadoButton = true;
                  } else {
                    $('.nav_map').hide();
                    $(this).css({ '-webkit-transform': 'rotate(0deg)', '-moz-transform': 'rotate(0deg)', '-o-transform': 'rotate(0deg)', '-ms-transform': 'rotate(0deg)', 'transform': 'rotate(0deg)' });
                    estadoButton = false;
                  }

                  calcularMedidas(medidas);
                  posicionBuscador(medidas);
                });

                function calcularMedidas(objMedidas) {
                  objMedidas.alto = $('.header_container').outerHeight();
                  objMedidas.ancho = $(window).outerWidth();

                  return objMedidas;
                }
                function posicionBuscador(objMedidas) {
                  $('.mapboxgl-ctrl-top-left').css({ 'top': objMedidas.alto + 'px' });
                  $('#panel_show').css({ 'top': objMedidas.alto + 20 + 'px' });
                  $('#container_panel').css({ 'top': objMedidas.alto + 59 + 'px' });
                }
              }
            };
          };
        };
      </script>
      <!-- CSS -->
      <link rel="stylesheet" href="css/style.css">
  </head>
  <body>
    <section class="contenido_app">
      <div class="header_container">
        <nav class="nav_gob">
          <svg x="0px" y="0px" viewBox="0 0 384 70">
            <g>
              <g>
                <path fill="#595959" d="M60,22.3L60,22.3c0-5.3,3.9-9.6,9.6-9.6c3.5,0,5.5,1.2,7.2,2.8l-2.6,3c-1.4-1.3-2.9-2.1-4.7-2.1c-3.1,0-5.3,2.6-5.3,5.7v0.1c0,3.1,2.2,5.8,5.3,5.8c2.1,0,3.4-0.8,4.8-2.2l2.6,2.6c-1.9,2-4,3.3-7.5,3.3C64,31.7,60,27.5,60,22.3z" />
                <path fill="#595959" d="M78.9,27.3L78.9,27.3c0-3.1,2.3-4.5,5.7-4.5c1.4,0,2.4,0.2,3.4,0.6v-0.2c0-1.7-1-2.6-3-2.6c-1.5,0-2.6,0.3-3.9,0.8l-1-3c1.5-0.7,3.1-1.1,5.5-1.1c2.2,0,3.8,0.6,4.8,1.6c1,1,1.5,2.6,1.5,4.5v8.2H88v-1.5c-1,1.1-2.3,1.8-4.3,1.8C81.1,31.7,78.9,30.1,78.9,27.3z M88.1,26.4v-0.7c-0.7-0.3-1.6-0.5-2.5-0.5c-1.7,0-2.8,0.7-2.8,1.9v0.1c0,1.1,0.9,1.7,2.2,1.7C86.8,28.9,88.1,27.9,88.1,26.4z"/>
                <path fill="#595959" d="M94.2,29.5l1.7-2.6c1.5,1.1,3.1,1.7,4.4,1.7c1.2,0,1.7-0.4,1.7-1v-0.1c0-0.9-1.4-1.2-2.9-1.6c-2-0.6-4.2-1.5-4.2-4.2v-0.1c0-2.9,2.3-4.5,5.1-4.5c1.8,0,3.7,0.6,5.2,1.6l-1.5,2.8c-1.4-0.8-2.8-1.3-3.8-1.3c-1,0-1.5,0.4-1.5,1v0.1c0,0.8,1.3,1.2,2.9,1.7c2,0.7,4.3,1.6,4.3,4.2v0.1c0,3.1-2.3,4.5-5.4,4.5C98.3,31.7,96.1,31,94.2,29.5z"/>
                <path fill="#595959" d="M107.6,27.3L107.6,27.3c0-3.1,2.3-4.5,5.7-4.5c1.4,0,2.4,0.2,3.4,0.6v-0.2c0-1.7-1-2.6-3-2.6c-1.5,0-2.6,0.3-3.9,0.8l-1-3c1.5-0.7,3.1-1.1,5.5-1.1c2.2,0,3.8,0.6,4.8,1.6c1,1,1.5,2.6,1.5,4.5v8.2h-3.9v-1.5c-1,1.1-2.3,1.8-4.3,1.8C109.8,31.7,107.6,30.1,107.6,27.3z M116.8,26.4v-0.7c-0.7-0.3-1.6-0.5-2.5-0.5c-1.7,0-2.8,0.7-2.8,1.9v0.1c0,1.1,0.9,1.7,2.2,1.7C115.5,28.9,116.8,27.9,116.8,26.4z"/>
                <path fill="#595959" d="M132.3,13h8.4c2.3,0,4.1,0.7,5.4,1.9c1,1,1.6,2.5,1.6,4.2v0.1c0,3-1.6,4.8-3.9,5.7l4.5,6.6h-4.7l-3.9-5.9h-0.1h-3.1v5.9h-4V13z M140.4,21.9c2,0,3.1-1,3.1-2.6v-0.1c0-1.7-1.2-2.6-3.2-2.6h-4v5.3H140.4z"/>
                <path fill="#595959" d="M149.9,24.4L149.9,24.4c0-4.1,3.3-7.4,7.6-7.4c4.4,0,7.6,3.2,7.6,7.3v0.1c0,4-3.3,7.3-7.6,7.3C153.2,31.7,149.9,28.5,149.9,24.4z M161.2,24.4L161.2,24.4c0-2.1-1.5-3.9-3.7-3.9c-2.3,0-3.6,1.8-3.6,3.8v0.1c0,2.1,1.5,3.9,3.7,3.9C159.8,28.3,161.2,26.5,161.2,24.4z"/>
                <path fill="#595959" d="M166.9,29.5l1.7-2.6c1.5,1.1,3.1,1.7,4.4,1.7c1.2,0,1.7-0.4,1.7-1v-0.1c0-0.9-1.4-1.2-2.9-1.6c-2-0.6-4.2-1.5-4.2-4.2v-0.1c0-2.9,2.3-4.5,5.1-4.5c1.8,0,3.7,0.6,5.2,1.6l-1.5,2.8c-1.4-0.8-2.8-1.3-3.8-1.3c-1,0-1.5,0.4-1.5,1v0.1c0,0.8,1.3,1.2,2.9,1.7c2,0.7,4.3,1.6,4.3,4.2v0.1c0,3.1-2.3,4.5-5.4,4.5C171,31.7,168.8,31,166.9,29.5z"/>
                <path fill="#595959" d="M180.4,27.3L180.4,27.3c0-3.1,2.3-4.5,5.7-4.5c1.4,0,2.4,0.2,3.4,0.6v-0.2c0-1.7-1-2.6-3-2.6c-1.5,0-2.6,0.3-3.9,0.8l-1-3c1.5-0.7,3.1-1.1,5.5-1.1c2.2,0,3.8,0.6,4.8,1.6c1,1,1.5,2.6,1.5,4.5v8.2h-3.9v-1.5c-1,1.1-2.3,1.8-4.3,1.8C182.6,31.7,180.4,30.1,180.4,27.3z M189.5,26.4v-0.7c-0.7-0.3-1.6-0.5-2.5-0.5c-1.7,0-2.8,0.7-2.8,1.9v0.1c0,1.1,0.9,1.7,2.2,1.7C188.3,28.9,189.5,27.9,189.5,26.4z"/>
                <path fill="#595959" d="M196.1,24.4L196.1,24.4c0-4.8,3.1-7.3,6.4-7.3c2.1,0,3.4,1,4.4,2.1v-6.9h4v19.2h-4v-2c-1,1.3-2.3,2.3-4.4,2.3C199.2,31.7,196.1,29.1,196.1,24.4z M207,24.4L207,24.4c0-2.4-1.5-3.9-3.4-3.9s-3.4,1.5-3.4,3.9v0.1c0,2.3,1.6,3.9,3.4,3.9S207,26.7,207,24.4z"/>
                <path fill="#595959" d="M213.6,27.3L213.6,27.3c0-3.1,2.3-4.5,5.7-4.5c1.4,0,2.4,0.2,3.4,0.6v-0.2c0-1.7-1-2.6-3-2.6c-1.5,0-2.6,0.3-3.9,0.8l-1-3c1.5-0.7,3.1-1.1,5.5-1.1c2.2,0,3.8,0.6,4.8,1.6c1,1,1.5,2.6,1.5,4.5v8.2h-3.9v-1.5c-1,1.1-2.3,1.8-4.3,1.8C215.8,31.7,213.6,30.1,213.6,27.3z M222.8,26.4v-0.7c-0.7-0.3-1.6-0.5-2.5-0.5c-1.7,0-2.8,0.7-2.8,1.9v0.1c0,1.1,0.9,1.7,2.2,1.7C221.5,28.9,222.8,27.9,222.8,26.4z"/>
              </g>
              <g>
                <path fill="#0072BC" d="M61,44.6h7.2c4.3,0,7,2.4,7,6.1v0.1c0,4.1-3.3,6.3-7.3,6.3h-3.6v5.9H61V44.6z M68,54.1c2.4,0,4-1.4,4-3.3v-0.1c0-2.2-1.5-3.3-4-3.3h-3.8v6.6H68z"/>
                <path fill="#0072BC" d="M78.1,49.1h3.2v3.1c0.9-2.1,2.5-3.5,4.9-3.4v3.4H86c-2.8,0-4.7,1.8-4.7,5.5v5.3h-3.2V49.1z"/>
                <path fill="#0072BC" d="M88.2,56.1L88.2,56.1c0-4,2.8-7.3,6.8-7.3c4.4,0,6.6,3.5,6.6,7.5c0,0.3,0,0.6-0.1,0.9H91.4c0.3,2.3,1.9,3.5,4,3.5c1.5,0,2.7-0.6,3.8-1.7l1.9,1.7c-1.3,1.6-3.1,2.6-5.7,2.6C91.3,63.2,88.2,60.3,88.2,56.1z M98.5,55c-0.2-2-1.4-3.6-3.5-3.6c-1.9,0-3.3,1.5-3.6,3.6H98.5z"/>
                <path fill="#0072BC" d="M103.7,61.1l1.4-2.2c1.5,1.1,3.1,1.7,4.5,1.7c1.4,0,2.2-0.6,2.2-1.5v-0.1c0-1.1-1.5-1.4-3.1-1.9c-2-0.6-4.3-1.4-4.3-4.1v-0.1c0-2.6,2.2-4.2,4.9-4.2c1.7,0,3.6,0.6,5.1,1.6l-1.3,2.3c-1.3-0.8-2.8-1.3-3.9-1.3c-1.2,0-1.9,0.6-1.9,1.4v0.1c0,1,1.5,1.4,3.1,1.9c2,0.6,4.3,1.5,4.3,4v0.1c0,2.9-2.3,4.4-5.1,4.4C107.6,63.2,105.4,62.5,103.7,61.1z"/>
                <path fill="#0072BC" d="M118,43.9h3.4v3H118V43.9z M118.2,49.1h3.2v13.9h-3.2V49.1z"/>
                <path fill="#0072BC" d="M124.8,56L124.8,56c0-4.6,3.1-7.2,6.4-7.2c2.3,0,3.7,1.2,4.7,2.4v-7.5h3.2v19.2h-3.2v-2.3c-1,1.4-2.5,2.6-4.7,2.6C127.9,63.2,124.8,60.6,124.8,56z M136,56L136,56c0-2.7-1.9-4.5-4-4.5c-2.2,0-4,1.7-4,4.4V56c0,2.7,1.8,4.4,4,4.4C134.1,60.5,136,58.7,136,56z"/>
                <path fill="#0072BC" d="M142.3,56.1L142.3,56.1c0-4,2.8-7.3,6.8-7.3c4.4,0,6.6,3.5,6.6,7.5c0,0.3,0,0.6-0.1,0.9h-10.2c0.3,2.3,1.9,3.5,4,3.5c1.5,0,2.7-0.6,3.8-1.7l1.9,1.7c-1.3,1.6-3.1,2.6-5.7,2.6C145.4,63.2,142.3,60.3,142.3,56.1z M152.6,55c-0.2-2-1.4-3.6-3.5-3.6c-1.9,0-3.3,1.5-3.6,3.6H152.6z"/>
                <path fill="#0072BC" d="M158.9,49.1h3.2v2.2c0.9-1.3,2.2-2.4,4.3-2.4c3.1,0,4.9,2.1,4.9,5.3v8.8h-3.2v-7.9c0-2.2-1.1-3.4-3-3.4c-1.8,0-3.1,1.3-3.1,3.4v7.8h-3.2V49.1z"/>
                <path fill="#0072BC" d="M174.4,56.1L174.4,56.1c0-4,3-7.3,7.2-7.3c2.6,0,4.3,1,5.5,2.4l-2,2.1c-1-1-2-1.7-3.6-1.7c-2.3,0-4,2-4,4.4V56c0,2.5,1.7,4.5,4.1,4.5c1.5,0,2.6-0.7,3.6-1.7l1.9,1.9c-1.4,1.5-3,2.6-5.7,2.6C177.5,63.2,174.4,60,174.4,56.1z"/>
                <path fill="#0072BC" d="M190.2,43.9h3.4v3h-3.4V43.9z M190.4,49.1h3.2v13.9h-3.2V49.1z"/>
                <path fill="#0072BC" d="M206.2,62.9v-1.7c-0.9,1.1-2.4,2-4.5,2c-2.6,0-4.9-1.5-4.9-4.3v-0.1c0-3.1,2.4-4.5,5.6-4.5c1.7,0,2.8,0.2,3.8,0.6v-0.3c0-1.9-1.2-3-3.4-3c-1.5,0-2.7,0.3-4,0.9L198,50c1.5-0.7,3-1.2,5.3-1.2c4.1,0,6.1,2.2,6.1,5.9v8.2H206.2z M206.2,57.1c-0.8-0.3-1.9-0.6-3.2-0.6c-2,0-3.2,0.8-3.2,2.2v0.1c0,1.3,1.2,2,2.7,2c2.1,0,3.7-1.2,3.7-2.9V57.1z"/>
                <path fill="#0072BC" d="M220.3,56L220.3,56c0-4.6,3.1-7.2,6.4-7.2c2.3,0,3.7,1.2,4.7,2.4v-7.5h3.2v19.2h-3.2v-2.3c-1,1.4-2.5,2.6-4.7,2.6C223.4,63.2,220.3,60.6,220.3,56z M231.5,56L231.5,56c0-2.7-1.9-4.5-4-4.5c-2.2,0-4,1.7-4,4.4V56c0,2.7,1.8,4.4,4,4.4C229.6,60.5,231.5,58.7,231.5,56z"/>
                <path fill="#0072BC" d="M237.8,56.1L237.8,56.1c0-4,2.8-7.3,6.8-7.3c4.4,0,6.6,3.5,6.6,7.5c0,0.3,0,0.6-0.1,0.9H241c0.3,2.3,1.9,3.5,4,3.5c1.5,0,2.7-0.6,3.8-1.7l1.9,1.7c-1.3,1.6-3.1,2.6-5.7,2.6C240.9,63.2,237.8,60.3,237.8,56.1z M248.1,55c-0.2-2-1.4-3.6-3.5-3.6c-1.9,0-3.3,1.5-3.6,3.6H248.1z"/>
                <path fill="#0072BC" d="M262.5,43.8h3.2v19.2h-3.2V43.8z"/>
                <path fill="#0072BC" d="M278.3,62.9v-1.7c-0.9,1.1-2.4,2-4.5,2c-2.6,0-4.9-1.5-4.9-4.3v-0.1c0-3.1,2.4-4.5,5.6-4.5c1.7,0,2.8,0.2,3.8,0.6v-0.3c0-1.9-1.2-3-3.4-3c-1.5,0-2.7,0.3-4,0.9l-0.9-2.5c1.5-0.7,3-1.2,5.3-1.2c4.1,0,6.1,2.2,6.1,5.9v8.2H278.3z M278.4,57.1c-0.8-0.3-1.9-0.6-3.2-0.6c-2,0-3.2,0.8-3.2,2.2v0.1c0,1.3,1.2,2,2.7,2c2.1,0,3.7-1.2,3.7-2.9V57.1z"/>
                <path fill="#0072BC" d="M293.4,44.6h3l9.8,12.7V44.6h3.2v18.4h-2.7l-10.1-13.1v13.1h-3.2V44.6z"/>
                <path fill="#0072BC" d="M322.3,62.9v-1.7c-0.9,1.1-2.4,2-4.5,2c-2.6,0-4.9-1.5-4.9-4.3v-0.1c0-3.1,2.4-4.5,5.6-4.5c1.7,0,2.8,0.2,3.8,0.6v-0.3c0-1.9-1.2-3-3.4-3c-1.5,0-2.7,0.3-4,0.9l-0.9-2.5c1.5-0.7,3-1.2,5.3-1.2c4.1,0,6.1,2.2,6.1,5.9v8.2H322.3z M322.4,57.1c-0.8-0.3-1.9-0.6-3.2-0.6c-2,0-3.2,0.8-3.2,2.2v0.1c0,1.3,1.2,2,2.7,2c2.1,0,3.7-1.2,3.7-2.9V57.1z"/>
                <path fill="#0072BC" d="M328.5,56.1L328.5,56.1c0-4,3-7.3,7.2-7.3c2.6,0,4.3,1,5.5,2.4l-2,2.1c-1-1-2-1.7-3.6-1.7c-2.3,0-4,2-4,4.4V56c0,2.5,1.7,4.5,4.1,4.5c1.5,0,2.6-0.7,3.6-1.7l1.9,1.9c-1.4,1.5-3,2.6-5.7,2.6C331.5,63.2,328.5,60,328.5,56.1z"/>
                <path fill="#0072BC" d="M344.3,43.9h3.4v3h-3.4V43.9z M344.4,49.1h3.2v13.9h-3.2V49.1z"/>
                <path fill="#0072BC" d="M351,56.1L351,56.1c0-4,3.1-7.3,7.4-7.3s7.4,3.2,7.4,7.2V56c0,3.9-3.1,7.2-7.4,7.2C354.1,63.2,351,60,351,56.1z M362.6,56.1L362.6,56.1c0-2.5-1.8-4.5-4.3-4.5c-2.5,0-4.2,2-4.2,4.4V56c0,2.4,1.8,4.5,4.2,4.5C360.9,60.5,362.6,58.5,362.6,56.1z M359.9,43l2.9,1.3l-3.2,3h-2.5L359.9,43z"/>
                <path fill="#0072BC" d="M369,49.1h3.2v2.2c0.9-1.3,2.2-2.4,4.3-2.4c3.1,0,4.9,2.1,4.9,5.3v8.8h-3.2v-7.9c0-2.2-1.1-3.4-3-3.4c-1.8,0-3.1,1.3-3.1,3.4v7.8H369V49.1z"/>
              </g>
              <g>
                <g>
                  <path fill="#FFFFFF" d="M22,61.1c-9.6,0-17-10.5-17-22.9H39C39,50.7,31.7,61.1,22,61.1"/>
                </g>
                <g>
                  <path fill="#231F20" d="M25.8,9.7l0.3,0c0,0-0.2-1.2-2.4-1.1h-0.5c0,0-0.2,0.7-0.2,0.7l1.5-0.1C24.4,9.2,25.5,9.1,25.8,9.7"/>
                </g>
                <g>
                  <path fill="#231F20" d="M23.3,10.5c0-0.1,0.3-0.4,0.3-0.4s0.1,0,0.1,0c0,0.1-0.1,0.1-0.1,0.2c0,0.2,0.1,0.4,0.4,0.5c0.1,0,0.4,0,0.4,0c0.2-0.1,0.4-0.3,0.4-0.5c0-0.1,0-0.1,0-0.2c0.2,0,0.4,0.2,0.5,0.4l0.5,0c0,0-0.3-1.2-2.4-0.9c0,0-0.5,0.7-0.5,0.8C22.8,10.6,23.3,10.6,23.3,10.5"/>
                </g>
                <g>
                  <path fill="#231F20" d="M18.5,10.5l0.5-0.1c0.1-0.2,0.3-0.3,0.5-0.4c0,0.1,0,0.1,0,0.1c0,0.3,0.1,0.5,0.4,0.5c0,0,0.3,0,0.4,0c0.2-0.1,0.4-0.3,0.4-0.5c0,0,0,0,0-0.1c0.1,0,0.1,0,0.1,0s0.3,0.3,0.3,0.3c0,0.1,0.5,0.1,0.5,0c0-0.1-0.5-0.8-0.5-0.8C18.8,9.3,18.5,10.5,18.5,10.5"/>
                </g>
                <g>
                  <path fill="#231F20" d="M19.8,9.2l1.6,0.1c0,0-0.2-0.6-0.2-0.7h-0.4c-2.4-0.1-2.6,1.1-2.6,1.1l0.3,0C18.8,9.1,19.8,9.2,19.8,9.2"/>
                </g>
                <g>
                  <path fill="#231F20" d="M24.3,25.6c0,0-0.1,0-0.1,0C24.3,25.6,24.3,25.7,24.3,25.6"/>
                </g>
                <g>
                  <path fill="#231F20" d="M23.9,64.2c1.6-0.2,6.5-2.3,8.2-3.7c1.9,0.2,2.6-1.3,3.9-2c-0.1-0.1-0.2-0.2-0.4-0.2c0.8-0.5,1.2-1.1,2-1.8c-0.1-0.1-0.4-0.2-0.7-0.2c1.3-0.4,0.4-0.9,1.6-1.8c0-0.1-0.1-0.1-0.2-0.1c0.8-0.5,0.8-1.3,1.5-2.1c-0.2-0.1-0.4-0.2-0.7-0.2c1.4-0.4,1.3-1.4,2.1-2.3c-0.1,0-0.3,0-0.6-0.1c0.8-0.6,0.5-1.4,1.1-2.3c-0.2-0.1-0.5-0.2-0.8-0.1c1.2-0.5,1.1-1.7,1.7-2.8c-0.1-0.1-0.3-0.1-0.5-0.1c1-1-0.3-0.6,0.5-2.6c-0.2-0.1-0.5-0.1-0.8,0.1c1-0.8,0.8-1.8,1.4-3.1c-0.7-0.1-2.2,0.9-2.6,1.8v-0.1c0.1-0.3,0.3-0.6,0.9-1c1.5-1.1,0.9-2.1,1.3-3.5c-0.7-0.1-2,1.1-2.3,2c0-0.1,0-0.2-0.1-0.3c0.2-0.4,0.3-0.9,1.1-1.3c1.3-0.7,0.9-2,1.3-3.4c-0.2,0-0.5,0-0.7,0.2c0.3-0.9-0.3-1.6-0.2-2.7c-0.7,0-1.6,1.3-1.7,2.2c0-0.1-0.1-0.2-0.2-0.4c0.1-0.3,0.2-0.8,0.8-1.2c1.3-1.5,0.2-2.3,0.4-3.8c-0.2,0-0.3,0-0.4,0.1c-0.1-0.7-0.6-1.2-0.7-2.1c-0.7,0.1-1.4,1.4-1.3,2.3c-0.1-0.1-0.2-0.3-0.3-0.4c0.1-0.3,0.1-0.8,0.7-1.3c0.7-1.4,0-2.1-0.1-3.5c-0.2,0-0.4,0.1-0.7,0.3c0-0.9-0.5-1.4-0.8-2.4c-0.5,0.1-0.9,0.7-1.1,1.4c0-0.3,0.1-0.6,0.2-1c0-1.9-0.7-2-1.3-3.5c-0.2,0.2-0.4,0.6-0.6,1.1c-0.2-1.2-0.7-1.6-1.4-2.7c-0.4,0.4-0.6,1.6-0.5,2.4c-0.1-0.3-0.2-0.6-0.1-1.1c-0.2-1.5-0.9-1.9-1.6-2.8c0.3,0,0.7-0.1,1.1-0.4c0,0-0.6,0.1-1-0.1c-0.4-0.2-0.6-0.6-1-0.7c-0.4-0.2-0.8,0.1-1.4-0.4l3.7-0.8c0,0-3.6-0.7-3.6-0.7s0.2-0.2,0.6-0.3c0.3,0,0.8,0,1.1-0.4c0.1-0.2,0.6-0.7,1.7-0.6c-0.8-0.5-1.3-0.3-2-0.1c-0.5,0.3-0.8,0.1-1.6,0.1c0,0,3-1.9,3-1.9l-3.6,0.8c0.6-0.9,1.1-0.5,1.5-1.5c0.1-0.2,0.5-0.7,1.1-1c-0.7-0.2-1.4,0.2-2.1,1C28.6,7,28,7.2,27.6,7.3c0,0,2-3,2-3s-2.1,1.5-3.1,2v0c0.1-0.8,0.7-0.7,0.8-1.6c-0.1-0.3,0-1,0.7-1.7c-0.8,0.2-1.2,0.8-1.5,1.7c-0.2,0.3-0.6,0.5-1,0.9c0,0,0.7-3.6,0.7-3.6l-2.1,3c0,0-0.2-0.1-0.1-0.5c0.3-0.6,0.3-0.9,0.2-1.2c-0.2-0.5-0.5-1.1,0-1.9c-1.2,0.7-0.6,1.7-0.9,2.4c-0.1,0.3-0.4,0.7-0.5,1l-0.6-3.6l-0.7,3.6c-0.4-0.6-0.2-0.8-0.4-1.4c-0.3-0.7-0.9-0.5-0.9-2c-0.2,0.5-0.5,0.9,0.1,2.3c0.1,0.2-0.1,0.8,0,1.3l-2-3L19,5.6c-0.5,0-0.6-0.8-0.9-1.1c-0.5-0.4-1.2-0.3-1.6-1.5c-0.2,0.8,0.1,1.3,0.9,2c0.3,0.4,0.4,1,0.6,1.4l-3.1-2l2.1,3c-0.7,0.1-0.7-0.8-2.1-0.8c-0.9,0-1.4-0.8-1.3-0.6c0.4,1.6,1.5,1.1,1.9,1.7c0.1,0.1,0.6,0.6,0.7,0.8l-3.6-0.8l3.1,2c-0.5,0.3-1.2-0.4-1.8-0.1c-1.2,0.6-1.7,0-1.8,0c0.7,0.9,1.2,0.7,1.9,0.7c0.5,0,0.9,0.3,1.5,0.5l-3.6,0.7l3.6,0.7c-0.4,0.4-1.1,0.1-1.6,0.5c-1.1,1.2-2.2,0.6-1.8,0.8c0.9,0.6,1.4,0.5,2,0.3c-0.4,0.3-0.7,0.5-0.9,0.9c-0.1-0.3-0.1-0.5-0.3-0.6c-0.7,1-1.5,1.1-1.8,2.4c-0.1-0.3-0.2-0.6-0.4-0.8c-0.6,1.1-1.3,1.3-1.5,2.5c-0.1-0.3-0.2-0.6-0.4-0.7C8,18.9,7,19.2,7.3,21c0.2,0.5,0.2,0.8,0.1,1c-0.1-0.7-0.4-1.5-0.9-1.6c-0.5,0.8-0.9,1.4-1,2.3c-0.2-0.1-0.3-0.2-0.5-0.2c-0.1,1.4-1.2,1.9-0.2,3.5c0.6,0.5,0.5,0.9,0.5,1.3c-0.1,0-0.1,0.1-0.2,0.2c0.1-0.9-0.7-2.2-1.4-2.3c-0.1,1-0.6,1.5-0.5,2.4c-0.2-0.1-0.3-0.2-0.5-0.2c0.2,1.4-0.9,2,0.4,3.4c0.6,0.4,0.7,0.8,0.8,1.2c-0.1,0.1-0.2,0.2-0.2,0.4c-0.1-0.9-1-2.1-1.7-2.1C2.2,32,1.4,31.9,1.9,33c-0.3-0.2-0.5-0.2-0.7-0.2c0.4,1.4-0.1,2.6,1.3,3.4c0.8,0.4,0.8,0.9,1.1,1.2c-0.1,0.1-0.1,0.2-0.1,0.3c-0.3-0.9-1.6-2-2.3-2C1.5,36.9,1.3,38,2,38.8c-0.4-0.2-0.8-0.4-1.2-0.3c0.7,1.4,0.3,2.2,1.6,3.3c-0.4-0.2-0.8-0.3-1.1-0.1c0.4,1,0.2,1.7,0.6,2.4c-0.2,0-0.4,0.1-0.5,0.1c0.7,1.1,0.5,2,1.6,2.7c-0.3,0-0.5,0-0.7,0.1c0.5,0.8,0.5,1.5,0.9,2.2c-0.2,0-0.3,0.1-0.4,0.2c0.9,0.8,0.9,1.7,1.9,2.3c-0.3,0-0.5,0.1-0.6,0.2c0.7,0.7,0.9,1.4,1.6,1.9c-0.3,0-0.5,0.1-0.7,0.3c1,0.7,1.1,1.5,2.2,1.9c-0.3,0-0.5,0.1-0.7,0.2c1.3,1,0.4,1.4,1.6,1.8C7.9,58,7.8,58,7.8,58.1c1.3,0.7,1.5,2.4,4.4,2.4h0c1.4,1.5,5.2,3.3,8,3.7L23.9,64.2z M42,42.4c-0.3,0.9,0,1.5-0.8,2.2c-0.6,0.1-0.5,0.3-0.7,0.5c0.1-0.3,0.2-0.7,0.4-0.9c-0.2,0.1-0.4,0.3-0.6,0.6c-0.1-0.1-0.4-0.1-0.7-0.7c-0.4-0.8-0.1-1.1-0.2-1.9c0.4,0.7,1,1.3,1.1,1.8C40.8,43.3,41.7,42.3,42,42.4 M40.5,42.9c0.1-0.2,0.4-0.5,0.8-0.7c-0.4,0.3-0.6,0.7-0.8,1C40.5,43.1,40.5,43,40.5,42.9 M42.4,39.3c-0.4,1-0.1,1.4-1,2.3c-0.6,0.3-0.6,0.4-0.7,0.6c0.1-0.3,0.3-0.7,0.5-1c-0.3,0.2-0.5,0.4-0.7,0.7c-0.1-0.1-0.4,0-0.6-0.5c-0.4-0.7,0-1-0.1-1.7c0.4,0.5,1,1,1,1.5C41,40.4,42.1,39.3,42.4,39.3 M40.8,38.5c0.1-0.7,1-1.9,1.4-2c-0.3,1,0.1,1.4-0.7,2.3c-0.6,0.3-0.5,0.5-0.6,0.7c0-0.4,0.2-0.7,0.3-1c-0.2,0.2-0.4,0.5-0.5,0.8c-0.1-0.1-0.4,0.1-0.7-0.4c-0.5-0.6-0.2-1-0.4-1.7C39.9,37.6,40.6,38,40.8,38.5 M39.7,36.3c0.4,0.4,0.6,0.5,0.7,0.9c-0.2-0.2-0.5-0.4-0.7-0.6C39.7,36.5,39.7,36.4,39.7,36.3M42.1,33.5c-0.2,1,0.2,1.4-0.7,2.3c-0.6,0.3-0.5,0.4-0.7,0.6c0.1-0.4,0.2-0.7,0.3-0.9c-0.2,0.2-0.4,0.4-0.5,0.7c-0.1-0.1-0.4,0-0.7-0.5c-0.5-0.6-0.2-1-0.4-1.7c0.5,0.5,1.2,0.9,1.3,1.3C40.9,34.6,41.8,33.5,42.1,33.5 M40.6,33.2c0-0.7,0.7-1.9,1-2c-0.1,1,0.4,1.3-0.2,2.3c-0.5,0.4-0.4,0.5-0.6,0.7c0-0.3,0.1-0.7,0.2-1c-0.2,0.2-0.4,0.5-0.4,0.8c-0.2-0.1-0.4,0.1-0.8-0.3c-0.6-0.5-0.4-0.9-0.7-1.5C39.6,32.5,40.4,32.8,40.6,33.2 M38.5,46.5c0-0.2,0.1-0.3,0.1-0.5c0,0.1,0,0.1,0,0.1c0.4,1.2,0.7,1.3,0.6,2.1c0,0-0.1,0.1-0.1,0.1C39.3,47.7,38.9,47,38.5,46.5 M37.1,52.4c0-0.4-0.2-0.8-0.4-1.2c0.1-0.1,0.1-0.3,0.2-0.4C37,51.7,37.2,51.9,37.1,52.4 M38.3,52.2c-0.5,0.1-1,0.4-1.2,0.7c0-0.1,0-0.1,0-0.1C37.4,52.6,37.6,52.2,38.3,52.2 M36,50.5c0,0-3,0.5-6.8-2c0.3-0.4,0.4-1.4,0.3-2c-0.1-0.5-0.1-0.1-0.2,0.2c-0.4,0.9-0.6,1-1,1.3c-1.1-1.1-1.3-1.1-2.7-2c-1-0.5-1.1-0.9-1.7-1.3c-1,0.5-2.5,1.4-3.3,0.8c-0.7-0.3,0.7-0.7,1.3-1.6c0.5-0.7,1.1-0.8,1.9-0.7c1.5,0.3,2.3,0.9,4.5,1.9c2.8,0.2,4.8,1,7.6,1.6c0.5,0.1,1.2,0.2,1.5,0.2C37.1,48.2,36.7,49,36,50.5 M23.9,47.5c0.8,0.9,1.6,1.1,2.1,2.2c-0.2,0.3-0.5,0.4-0.8,0.2c-1.2-1.4-2.4-1.6-1.9-2.3c-0.2,0-0.5,0.3-0.4,0.5c0.2,1,1.3,1.4,1.6,2.2c-0.1,0.7-1.5,0.1-2.1-1.7l0-0.1c-0.2,0.5-0.5,0.7,0.4,1.7c0.2,0.4,0.3,0.8-0.3,0.7c0.1,0.1-0.3,0-0.1,0h0.1c0,0,0,0-0.1-0.1c0.3-1.7-1.3-1.4-1.2-1.4c0.2-0.6-0.7-1.2-1.6-0.8c0.1-0.5-0.8-1.2-2.1-0.7c-0.2-0.5-1.4-0.9-2.5,0.5c-2,0.2-5.2,1.9-7.2,1.1c-0.1,0.1-1.2-3.2-1.3-3.3c2.3-0.2,7.5-0.6,9.4-1.4c1.4-0.9,4.2-1.4,4.8-1.1c0,0.5-1.2,1.4-0.6,1.9c1,1,2.6,0.5,3.8-0.1c1.1,1.2,3,1.3,3.7,2.9l-0.2,0.5c-0.4,0.3-0.4,0.3-0.9-0.4c-1.1-1.5-2.1-1.1-2.3-2.1C23.6,46.8,23.9,47.2,23.9,47.5 M18.5,50.5c-0.4,0.2,0.2-0.2,0.3-0.3l0.2-0.2c0.4-0.3,0.8-0.7,1.1-0.1c0.2,0.4-1.2,1.2-1.4,1.3c-0.2,0.1-0.3,0.1-0.4-0.1C18.2,50.7,18.3,50.5,18.5,50.5 M17.3,50.3c-0.2,0.1-0.4,0.1-0.6-0.1c-0.1-0.3,0.3-0.7,0.5-0.9c0.1-0.2,0.3-0.2,0.3-0.2l0.2-0.2c0.5-0.1,0.5,0,0.6,0.2C18.5,49.3,17.6,50.3,17.3,50.3 M20.6,50.6c0,0,0.5-0.6,0.8,0.2c0.1,0.3-0.5,0.7-1,0.9C20.1,51.5,19.4,51.5,20.6,50.6 M16,49.4c-0.2,0.1-0.6-0.3-0.4-0.4l0.2-0.4c0.4-0.1,0.6-0.3,0.7-0.1C16.7,48.7,16.1,49.3,16,49.4M7.2,50.8C7.1,51,7.1,51.1,7,51.2c0-0.1,0.1-0.3,0.1-0.5C7.2,50.8,7.2,50.8,7.2,50.8 M6.8,52.4C6.6,52.2,6.4,52,6,51.9c0.3,0.1,0.5,0.2,0.7,0.3C6.8,52.3,6.8,52.3,6.8,52.4 M4.8,47.9C4.8,47.8,4.8,47.8,4.8,47.9c-0.2-0.9,0.1-1,0.5-2.1c0,0.2,0.1,0.3,0.1,0.5C5.1,46.8,4.8,47.4,4.8,47.9 M5,31.5c-0.2,0.1-0.4,0.2-0.6,0.3c0.1-0.1,0.3-0.3,0.6-0.5C5.1,31.4,5,31.4,5,31.5 M5.2,38.5h16v4.7c-0.7-0.1-2-0.2-3,0c-2.9,0.5-2,2-12.1,2.1C5.1,42.2,5.2,38.5,5.2,38.5 M15.1,15.2c0.1-0.2,0.3-0.2,0.4-0.8c0.4-1.3,1.1-1.1,2-1.7c0.3,0.4-0.8,1.8-1.4,2.2c0.4-0.1,1,0.5,1.6,0.7c-0.7,0.1-1,0.5-1.7,0.3c-0.5-0.2-0.5-0.4-0.6-0.6c0.2-0.2,0.4-0.5,0.6-0.7C15.7,14.8,15.4,15,15.1,15.2 M27.3,14.8c-0.3,0-0.5-0.1-0.8-0.2c0.4-0.1,0.8-0.3,1.1-0.5c0.7,0.1,1.3,0.5,1.3,1.1c-0.6-0.2-0.7,0.2-1.6,0.4c-0.8,0.2-1.2-0.3-1.9-0.4c-0.2-0.1-0.3-0.3-0.3-0.4c0.3,0.1,0.7,0.1,1.1,0l0,0.1C26.5,14.9,26.9,14.9,27.3,14.8 M24.7,13.8c0.4,0,0.7-0.1,1.1-0.2c-0.2,0-0.5,0-0.8,0c0.5-0.3,0.8-0.7,1.5-0.7c0.9,0,0.9,0.5,1.4,0.3C28,13.6,25.4,14.8,24.7,13.8 M23.5,13.6c0.4,0.1,0.8,0.9,1.3,1.4c-0.7-0.2-1,0-1.6-0.5c-0.4-0.4-0.4-0.6-0.4-0.8c0.3-0.1,0.5-0.3,0.7-0.5c-0.3,0.2-0.6,0.3-0.9,0.3c0.2-0.1,0.3-0.1,0.6-0.6c0.8-0.8,1.2-0.3,2.2-0.6C25.2,12.7,24.1,13.5,23.5,13.6 M21.4,13.8c0.4,0,0.9,0.7,1.5,1c-0.7,0-1,0.2-1.6-0.1c-0.5-0.3-0.5-0.5-0.6-0.7c0.3-0.2,0.4-0.4,0.6-0.6c-0.2,0.2-0.6,0.4-0.9,0.5c0.2-0.2,0.3-0.1,0.5-0.7c0.7-0.9,1-0.6,2-1C22.9,12.5,22,13.6,21.4,13.8 M19.9,15.1c-0.7,0-1,0.3-1.7,0c-0.5-0.2-0.5-0.5-0.6-0.6c0.2-0.2,0.4-0.4,0.6-0.7c-0.2,0.2-0.5,0.4-0.9,0.6c0.2-0.2,0.3-0.2,0.5-0.8c0.7-1,1.2-0.7,2.1-1.2c0,0.3-0.9,1.4-1.6,1.8C18.8,14.2,19.4,14.7,19.9,15.1M31.5,18.4c0.1,0,0.2,0,0.3,0C31.7,18.4,31.6,18.5,31.5,18.4C31.5,18.5,31.5,18.4,31.5,18.4 M37.4,26.5c0.2,0.1,0.3,0.2,0.4,0.3c-0.1,0-0.3-0.1-0.4-0.1C37.4,26.6,37.4,26.6,37.4,26.5 M29,44.4c-1.6-0.3-3.6-2.1-6-2v-4h15.9c0,0.5-0.3,4.8-1.1,7.7C34.5,46.1,31.6,44.3,29,44.4 M39,31.3c0.3,0.2,0.5,0.4,0.7,0.5c-0.2-0.1-0.4-0.2-0.6-0.3C39,31.4,39,31.4,39,31.3 M40.8,28.3c-0.1,1,0.4,1.3-0.2,2.3c-0.5,0.3-0.4,0.5-0.5,0.7c0-0.3,0-0.7,0.2-1c-0.2,0.2-0.3,0.5-0.4,0.7c-0.1,0-0.4,0.1-0.8-0.3c-0.6-0.5-0.4-0.9-0.7-1.6c0.6,0.5,1.4,0.8,1.6,1.2C39.8,29.5,40.5,28.3,40.8,28.3 M39.3,28.2c-0.1-0.7,0.3-1.9,0.6-2.1c0.1,1,0.7,1.3,0.2,2.3c-0.4,0.4-0.3,0.5-0.4,0.7c-0.1-0.3-0.1-0.7,0-1c-0.2,0.3-0.3,0.5-0.3,0.8c-0.2,0-0.4,0.1-0.9-0.2c-0.7-0.4-0.6-0.9-1-1.5C38.1,27.6,39,27.8,39.3,28.2 M38.5,23.3c0.1,1,0.7,1.3,0.2,2.3c-0.5,0.4-0.3,0.5-0.4,0.7c-0.1-0.3-0.1-0.7,0-1c-0.1,0.2-0.3,0.5-0.3,0.8c-0.2,0-0.4,0.1-0.9-0.3c-0.7-0.5-0.6-0.9-1-1.5c0.6,0.4,1.5,0.6,1.8,1C37.8,24.6,38.2,23.4,38.5,23.3 M37.2,21.2c0.3,1,0.9,1.2,0.6,2.2c-0.4,0.4-0.2,0.6-0.3,0.8c-0.1-0.3-0.2-0.7-0.2-1c-0.1,0.3-0.2,0.5-0.1,0.8c-0.2,0-0.4,0.2-0.9-0.1c-0.8-0.4-0.8-0.8-1.3-1.4c0.7,0.3,1.6,0.5,2,0.8C36.7,22.6,36.9,21.4,37.2,21.2 M36.5,22.7c-0.1-0.2-0.3-0.3-0.5-0.4C36.1,22.4,36.3,22.5,36.5,22.7C36.5,22.7,36.5,22.7,36.5,22.7 M35.3,18.5c0.4,1,0.8,0.9,0.7,2.1c-0.2,0.6-0.1,0.7-0.1,1c-0.2-0.3-0.3-0.7-0.4-1c0,0.3,0,0.6,0.1,1c-0.2,0-0.3,0.3-0.8,0.1c-0.7-0.2-0.8-0.7-1.3-1.1c0.7,0.1,1.5,0,1.8,0.3C35,20.1,35.1,18.7,35.3,18.5M33.7,19c-0.3-0.6-0.4-1.9-0.2-2.2c0.5,0.9,0.9,0.7,1,1.9c-0.2,0.6,0,0.7,0,1c-0.2-0.3-0.4-0.6-0.5-1c0,0.3,0,0.6,0.2,0.9c-0.2,0.1-0.2,0.3-0.8,0.2c-0.8-0.1-0.9-0.5-1.5-0.9C32.6,19,33.3,18.7,33.7,19 M31.1,14.7c0.6,0.9,1.2,0.8,1.3,2c-0.1,0.6,0,0.7,0.1,0.9c-0.2-0.3-0.4-0.6-0.5-0.9c0,0.3,0.1,0.6,0.2,0.9c-0.2,0-0.2,0.3-0.8,0.3c-0.8,0-0.9-0.5-1.5-0.8c0.7,0,1.5-0.3,1.9-0.1C31.2,16.4,30.9,15,31.1,14.7 M30.7,14c0,0.1-0.1,0.2-0.1,0.4c-0.1-0.2-0.2-0.3-0.4-0.4C30.4,14,30.6,14,30.7,14 M30.5,14.9c-0.1,0.7,0.1,0.7,0.2,1c-0.1,0-0.1,0-0.2,0c-0.2-0.3-0.5-0.6-0.6-0.7c0.1,0.5,0.1,0.7,0.3,1c-0.1,0.1-0.2,0.2-0.5,0.2c-0.9,0.1-1.1-0.1-1.5-0.3c0.6-0.3,0.9-0.6,1.5-0.4c0,0,0-0.1-0.1-0.2c0,0,0,0,0,0l0,0c-0.1-0.2-0.3-0.4-0.5-0.7c-0.3-0.5-0.5-1-0.4-1.2C29.5,14.1,30.2,13.8,30.5,14.9 M29.9,13.2h-0.8C29,13.1,28.9,13,28.8,13C29.2,13,29.7,13.2,29.9,13.2 M16.1,12.6C16.8,12.3,17,12,17,12l-2.2-0.6l2.3-0.5c0,0-0.1-0.1-1.4-0.5c-0.3-0.1,0-0.3-1.1-0.6c0.4-0.1,0.3,0.1,1,0.2c0.5,0,0.9,0.1,1.7-0.1c0,0-1.3-1-2.2-1.5c0,0,1.5,0.4,2.6,0.7c0-0.1-0.9-0.8-1.5-1.6c-0.1-0.2-0.2-0.3-0.3-0.4c0.6,0.2,0.6,0.4,1,0.6c0.4,0.1,1.1,0.6,1.4,0.5c-0.7-0.9-1.6-2.3-1.6-2.3l2.3,1.6c-0.1-0.6-0.5-1.1-0.6-1.3c-0.3-1-0.2-0.9-0.4-1.3c0.6,0.9,0.9,1.5,1.8,2l-0.4-2.9l1.4,2.5c0.1-0.4,0-1.1-0.1-1.3c0.1-1.3,0.1-0.5,0-1.5c0.2,0.5,0.2,0.4,0.2,1.1c0.3,0.6,0.4,1,0.8,1.5l0.6-2.7c0,0,0.4,2.7,0.4,2.7c0.3-0.6,0.3-0.9,0.5-1.3C23.5,4.6,23.7,4,23.7,4c-0.2,1.1-0.2,0.7-0.3,1.3c0,0.6,0,1.4,0.1,1.5l1.7-2.4l-0.7,2.8c0.2-0.1,0.9-0.9,1.5-1.4c0.2-0.2,0.5-0.6,0.5-0.6C26.1,5.7,26,6,25.8,6.4c-0.3,0.7-0.2,0.4-0.5,1.4l2.4-1.6l-1.6,2.3c0,0,0.1,0.1,1.2-0.5c0.6-0.2,1.1-0.4,1.5-0.6c-0.8,0.7-1.1,0.6-2.2,2l2.7-0.5c0,0-2.2,1.3-2.3,1.4c0.1,0.1,1.1-0.1,1.7,0c0.6,0,0.6,0,1.2-0.1c-0.7,0.2-0.9,0.1-1.7,0.4c-0.3,0.2-1,0.4-1.1,0.6l2.7,0.5c0,0-2.6,0.6-3.1,0.8c-0.2,0-0.3,0-0.5,0c-0.1,0-0.2,0-0.3,0c0.1-0.2,0.2-0.4,0.1-0.6c-0.9,0.2-1.7,0-2.4,0.2c0.1-0.2,0.1-0.4,0-0.5c-1.1,0.5-2,0.3-3,1c0-0.2,0-0.5-0.1-0.7c-1,0.5-1.8,0.2-2.5,0.8c0-0.2,0-0.4,0-0.5c-1,0.4-1.8,0.5-2.5,1.3c-0.2,0-0.4-0.1-0.6-0.1c0,0,0-0.1-0.1-0.1c0,0-0.1,0.1-0.1,0.1c-0.1,0-0.3,0-0.5,0C15.2,12.9,15.7,12.8,16.1,12.6 M13,15.8c0.3-1.1,0.9-0.9,1.6-1.6c0.2,0.3-0.4,1.6-0.9,2c0.4-0.1,1.1,0.3,1.8,0.4c-0.7,0.2-0.8,0.6-1.6,0.5c-0.6-0.1-0.6-0.3-0.8-0.4c0.2-0.2,0.3-0.5,0.3-0.8c-0.1,0.3-0.4,0.5-0.6,0.7C12.9,16.4,13,16.4,13,15.8 M14.4,15.9C14.4,15.9,14.4,15.9,14.4,15.9C14.3,15.9,14.3,15.9,14.4,15.9L14.4,15.9z M11.3,17.2c0.1-1.2,0.7-1.1,1.3-1.9c0.2,0.3-0.1,1.7-0.5,2.2c0.4-0.2,1.1,0.1,1.8,0.1c-0.6,0.3-0.7,0.7-1.5,0.7c-0.6,0-0.7-0.2-0.8-0.3c0.1-0.3,0.2-0.6,0.2-0.9c-0.1,0.3-0.3,0.6-0.5,0.9C11.3,17.8,11.5,17.8,11.3,17.2 M10.8,18.1c0,0.2-0.1,0.3-0.1,0.4c0,0,0,0,0,0C10.7,18.5,10.8,18.3,10.8,18.1 M9.3,18.7c0.1-1.2,0.4-1.1,1-2c0.2,0.3,0.1,1.6-0.2,2.2c0.4-0.2,1.1,0,1.8,0c-0.6,0.4-0.7,0.8-1.4,0.9c-0.6,0.1-0.7-0.2-0.8-0.2c0.1-0.3,0.1-0.6,0.1-0.9c-0.1,0.3-0.2,0.6-0.5,0.9C9.4,19.4,9.5,19.4,9.3,18.7 M8.5,18.6c0.2,0.3,0.2,1.7-0.1,2.3c0.3-0.3,1.1-0.2,1.8-0.3c-0.6,0.4-0.6,0.9-1.3,1.1c-0.5,0.1-0.6-0.1-0.8-0.1c0.1-0.3,0.1-0.6,0.1-0.9c-0.1,0.3-0.2,0.7-0.4,1c0-0.3,0.2-0.3-0.1-0.9C7.7,19.6,8.1,19.6,8.5,18.6M7.4,22.8c0.1-0.1,0.2-0.2,0.3-0.2C7.6,22.6,7.4,22.8,7.4,22.8C7.4,22.9,7.4,22.8,7.4,22.8 M6.6,21.4c0.3,0.1,0.5,1.4,0.2,2.1C7.2,23.1,8,23,8.8,22.7c-0.5,0.6-0.5,1-1.3,1.3c-0.6,0.3-0.8,0.1-0.9,0.2c0-0.3,0-0.5-0.1-0.8c0,0.3-0.1,0.6-0.2,0.9c0-0.2,0.1-0.3-0.3-0.7C5.7,22.6,6.4,22.3,6.6,21.4 M5.1,25.7c-0.5-1,0.1-1.3,0.2-2.2c0.3,0.1,0.7,1.3,0.6,2c0.3-0.3,1.1-0.6,1.7-1c-0.4,0.6-0.3,1-1,1.5c-0.5,0.4-0.7,0.2-0.9,0.3c0-0.3-0.1-0.5-0.3-0.8c0.1,0.3,0,0.6,0,0.9C5.5,26.1,5.6,26,5.1,25.7 M6.6,26.5c0,0.1-0.1,0.2-0.1,0.2C6.2,26.9,6,27,5.7,27.1C5.9,26.9,6.2,26.8,6.6,26.5 M4.2,26.2c0.3,0.1,0.7,1.4,0.6,2c0.3-0.4,1.1-0.6,1.7-0.9c-0.4,0.6-0.3,1-1,1.4C5,29.1,4.8,28.9,4.6,29c0-0.3-0.1-0.6-0.2-0.8c0.1,0.3,0,0.6,0,1c0-0.2,0-0.3-0.4-0.7C3.5,27.4,4.1,27.1,4.2,26.2 M3.4,30.5c-0.6-0.9-0.1-1.3-0.2-2.2c0.4,0.1,1,1.3,1,1.9c0.2-0.4,1-0.7,1.5-1.2c-0.3,0.7-0.1,1-0.7,1.6C4.6,31,4.4,30.9,4.2,31c-0.1-0.3-0.2-0.5-0.4-0.7c0.1,0.3,0.2,0.6,0.2,1C3.9,31,4,30.8,3.4,30.5 M2.4,31.2c0.4,0.1,1,1.3,1,2c0.2-0.4,1-0.7,1.5-1.1c-0.3,0.7-0.1,1-0.7,1.5c-0.4,0.4-0.6,0.3-0.8,0.3c-0.1-0.3-0.2-0.5-0.4-0.7c0.1,0.3,0.2,0.6,0.2,1c-0.1-0.2,0-0.3-0.5-0.7C2,32.5,2.5,32.1,2.4,31.2 M3.5,36c-0.1-0.3-0.3-0.5-0.5-0.7c0.2,0.3,0.3,0.6,0.3,1c-0.2-0.2-0.1-0.3-0.6-0.6c-0.8-0.9-0.5-1.1-0.7-2c0.3,0,1.2,1.1,1.4,1.7c0.1-0.4,0.8-0.8,1.3-1.3c-0.2,0.7,0.1,1-0.4,1.6C3.9,36,3.6,35.9,3.5,36 M4.4,36.2v0.3c-0.3,0.2-0.5,0.4-0.7,0.6C3.8,36.7,4,36.6,4.4,36.2 M1.9,36.5c0.3,0.1,1.3,1.1,1.4,1.8c0.1-0.4,0.8-0.8,1.2-1.3c-0.2,0.7,0.1,1.1-0.4,1.6c-0.3,0.4-0.6,0.3-0.7,0.4c-0.1-0.3-0.3-0.5-0.5-0.7c0.2,0.3,0.3,0.6,0.3,1c-0.1-0.2-0.1-0.3-0.6-0.7C1.8,37.8,2.2,37.5,1.9,36.5M3.3,40.1v0.1c-0.2-0.3-0.5-0.7-0.9-1.1C3,39.4,3.2,39.8,3.3,40.1 M1.8,39.4c0.3,0,1.3,0.9,1.6,1.5c0-0.4,0.6-0.9,1-1.5c0,0.7,0.3,1.1-0.1,1.7c-0.2,0.5-0.5,0.4-0.6,0.5C3.5,41.4,3.2,41.2,3,41c0.2,0.3,0.4,0.6,0.5,0.9c-0.2-0.2-0.1-0.3-0.7-0.6C1.4,40.1,2.8,41.7,1.8,39.4 M3.6,42.7c0,0.1,0,0.1,0,0.2c-0.2-0.4-0.5-0.7-0.9-1C3.1,42.2,3.4,42.5,3.6,42.7 M3.5,43.7c0.1-0.5,0.7-1.1,1.1-1.8c-0.1,0.7,0.2,1.1-0.2,1.8c-0.3,0.6-0.5,0.6-0.6,0.7c-0.1-0.3-0.3-0.4-0.6-0.6c0.2,0.2,0.3,0.5,0.4,0.9c-0.2-0.2-0.1-0.3-0.7-0.5c-0.8-0.6-0.5-0.9-0.8-1.9C2.4,42.3,3.3,43.1,3.5,43.7 M2.3,44.6c0.3-0.1,1.4,0.6,1.7,1.2c0-0.4,0.5-1.1,0.8-1.9c0,0.7,0.4,1,0.1,1.8c-0.2,0.6-0.4,0.6-0.5,0.8c-0.2-0.2-0.4-0.4-0.7-0.5c0.2,0.2,0.4,0.5,0.6,0.8C4,46.7,4,46.5,3.4,46.5C2.5,46,2.8,45.4,2.3,44.6 M5,48.6c0-0.4,0.5-1.1,0.7-1.8c0,0.7,0.4,1,0.1,1.8c-0.2,0.6-0.4,0.6-0.5,0.7c-0.2-0.2-0.4-0.3-0.7-0.4C4.8,49,5,49.3,5.2,49.6C5,49.5,5,49.3,4.4,49.3c-1-0.5-0.7-1.1-1.2-1.9C3.5,47.3,4.6,48,5,48.6M3.8,49.6c0.3-0.2,1.6,0.4,2,0.9c-0.1-0.4,0.3-1.2,0.5-1.9c0.2,0.7,0.5,0.9,0.4,1.7c-0.1,0.6-0.3,0.7-0.4,0.8c-0.2-0.2-0.5-0.3-0.8-0.3c0.3,0.1,0.5,0.4,0.7,0.6C6,51.3,6,51.1,5.4,51.2C4.3,50.9,4.5,50.3,3.8,49.6 M5,52.1c0.3-0.1,1.6,0.4,2,1c-0.1-0.4,0.3-1.1,0.5-1.8c0.1,0.7,0.5,0.9,0.4,1.7c-0.1,0.6-0.3,0.6-0.4,0.8c-0.2-0.2-0.5-0.3-0.8-0.4C7,53.4,7.2,53.7,7.4,54c-0.2-0.1-0.2-0.3-0.8-0.2C5.5,53.4,5.7,52.8,5,52.1 M6,54.1c0.3-0.2,1.7,0.2,2.2,0.6C8,54.4,8.3,53.7,8.4,53c0.3,0.7,0.7,0.8,0.7,1.5c0,0.6-0.3,0.6-0.3,0.8c-0.3-0.1-0.5-0.2-0.8-0.2c0.3,0.1,0.6,0.3,0.8,0.5c-0.2-0.1-0.3-0.2-0.9-0.1C6.7,55.3,6.8,54.8,6,54.1 M9.1,56.2c0,0.1,0.1,0.2,0.1,0.3c-0.3-0.2-0.7-0.4-1.1-0.5C8.5,56,8.8,56.1,9.1,56.2 M7.3,56.3c0.3-0.2,1.7,0.3,2.2,0.8c-0.2-0.4,0.2-1.1,0.3-1.8c0.3,0.7,0.7,0.8,0.6,1.6c0,0.6-0.3,0.6-0.3,0.8c-0.3-0.2-0.6-0.2-0.9-0.3c0.3,0.1,0.6,0.4,0.8,0.6c-0.2-0.1-0.3-0.2-0.9-0.2C8,57.5,8.1,57,7.3,56.3M12.4,59.9c-0.2-0.2-0.5-0.4-0.9-0.3c-1.2-0.1-1.5-0.9-2.5-1.4c0.3-0.2,1.8,0.1,2.4,0.5c-0.2-0.3,0-1-0.1-1.7c0.4,0.6,0.9,0.7,0.9,1.4c0.1,0.6,0.1,0.9,0.2,1.3c-0.2-0.2-0.4-0.3-0.7-0.4C12,59.4,12.2,59.6,12.4,59.9 M10.9,58.1L10.9,58.1L10.9,58.1L10.9,58.1z M18.8,63.1c-2.7-0.8-5-2.1-5.6-3c-0.2-0.5-0.3-1.1-0.4-1.8c0,0,0,0,0-0.1c1.9,1.6,4,2.8,6.2,3.3h-0.2C18,62.4,18.4,62.9,18.8,63.1 M21.9,60.9c-7.3-0.2-11.7-6.1-13.3-9c-0.3-0.3,4.2-1.7,5.2-2l1.2-0.3c0,0.5,1,0.7,0.9,0.5c0.2,0.1-0.1,0.5,0.5,0.9c0.6,0.4,1,0.2,1,0.1c-0.4,0.4,0.9,1.1,1.7,0.6c-0.1,0.4,0.5,0.5,0.7,0.5c0.3,0.2,0.9,0.3,1.4,0v4c0.1,0.8,0.1,0.9,0.5,1.1c1.6,0.1,1.3-0.4,1.5-0.9C23,54.6,23,52.1,23,52.1c1.1-0.1,0.8-0.4,1.3-0.5c0.5,0.1,0.7,0.1,1.1-0.5c0.6,0.1,1.1-0.1,1.3-0.6c0.5,0.2,1.2-0.3,1.6-1.2c1.1,0.3,5,2.4,6.9,3C34.3,53.7,29.7,60.9,21.9,60.9 M34.7,56.7c0.2-0.1,0.4-0.2,0.6-0.2c-0.3,0.1-0.5,0.2-0.7,0.4C34.6,56.8,34.7,56.7,34.7,56.7 M32.9,58.5C32.9,58.5,32.9,58.5,32.9,58.5C32.9,58.5,32.9,58.5,32.9,58.5 M31,58.6c-0.1,0.7-0.2,1.2-0.3,1.7c-0.7,1.3-4.1,2.4-5.5,2.8c0.4-0.4,0.7-1.2,0.1-1.6h-0.1C27.2,61,29.2,59.9,31,58.6C31,58.5,31,58.5,31,58.6 M32.7,59.8c-0.5-0.1-0.9,0.2-1.2,0.4c0.2-0.3,0.4-0.6,0.7-0.8c-0.3,0.1-0.5,0.2-0.7,0.3c0-0.3,0-0.6,0.1-1c0.1-0.7,0.6-0.8,1-1.4c-0.1,0.7,0.2,1.4-0.1,1.8c0.6-0.4,2.2-0.7,2.5-0.5C33.9,59.1,33.9,59.7,32.7,59.8 M34.6,58.2c-0.6-0.1-0.7,0.1-0.9,0.2c0.3-0.2,0.6-0.4,0.9-0.6c-0.3,0.1-0.6,0.1-0.9,0.3c-0.1-0.2-0.3-0.2-0.3-0.8c0-0.8,0.4-0.9,0.7-1.6c0.1,0.7,0.4,1.4,0.2,1.8c0.5-0.5,2-1,2.3-0.8C35.7,57.4,35.8,57.9,34.6,58.2M36,56c-0.6-0.1-0.7,0-0.9,0.1c0.3-0.2,0.5-0.5,0.9-0.6c-0.3,0-0.6,0.1-0.9,0.2c-0.1-0.2-0.3-0.2-0.3-0.8c0-0.8,0.4-0.9,0.7-1.6c0.1,0.7,0.4,1.4,0.2,1.9c0.5-0.5,1.9-0.9,2.2-0.7C37.1,55.2,37.2,55.8,36,56 M37.3,54.2c-0.6,0-0.6,0.1-0.9,0.2c0.2-0.3,0.4-0.5,0.7-0.7c-0.3,0.1-0.6,0.2-0.8,0.4c-0.1-0.2-0.4-0.2-0.5-0.8c-0.2-0.8,0.2-1,0.4-1.7c0.2,0.7,0.6,1.4,0.5,1.9c0.4-0.6,1.7-1.2,2-1C38.2,53.3,38.4,53.9,37.3,54.2 M38.7,51.6c-0.6-0.1-0.6,0.1-0.8,0.2c0.2-0.3,0.4-0.5,0.7-0.7c-0.3,0.1-0.6,0.2-0.8,0.3c-0.1-0.2-0.3-0.2-0.4-0.8c-0.1-0.8,0.3-1,0.4-1.7c0.2,0.7,0.6,1.5,0.5,1.9c0.4-0.5,1.7-1.1,2-0.9C39.6,50.7,39.7,51.3,38.7,51.6 M39.6,49.6c-0.6,0-0.6,0.2-0.8,0.3c0.2-0.3,0.3-0.6,0.6-0.8c-0.3,0.1-0.5,0.2-0.7,0.5c-0.1-0.1-0.4-0.2-0.5-0.8c-0.3-0.8,0.1-1.1,0.1-1.8c0.3,0.7,0.8,1.4,0.8,1.9c0.3-0.6,1.5-1.3,1.8-1.2C40.4,48.6,40.6,49.2,39.6,49.6 M39.9,47.7c0.1-0.1,0.3-0.2,0.5-0.2C40.2,47.5,40,47.5,39.9,47.7 M40.6,46.8c-0.6,0-0.6,0.2-0.8,0.3c0.2-0.3,0.3-0.6,0.6-0.8c-0.3,0.1-0.5,0.2-0.7,0.4c-0.1-0.1-0.3-0.1-0.6-0.7c-0.3-0.8,0.1-1.1,0.1-1.8c0.3,0.7,0.8,1.4,0.8,1.9c0.3-0.6,1.4-1.4,1.7-1.3C41.3,45.7,41.6,46.3,40.6,46.8"/>
                </g>
                <g>
                  <path fill="#FACC63" d="M16.1,12.6C16.8,12.3,17,12,17,12l-2.2-0.6l2.3-0.5c0,0-0.1-0.1-1.4-0.5c-0.3-0.1,0-0.3-1.1-0.6c0.4-0.1,0.3,0.1,1,0.2c0.5,0,0.9,0.1,1.7-0.1c0,0-1.3-1-2.2-1.5c0,0,1.5,0.4,2.6,0.7c0-0.1-0.9-0.8-1.5-1.6c-0.1-0.2-0.2-0.3-0.3-0.4c0.6,0.2,0.6,0.4,1,0.6c0.4,0.1,1.1,0.6,1.4,0.5c-0.7-0.9-1.6-2.3-1.6-2.3l2.3,1.6c-0.1-0.6-0.5-1.1-0.6-1.3c-0.3-1-0.2-0.9-0.4-1.3c0.6,0.9,0.9,1.5,1.8,2l-0.4-2.9l1.4,2.5c0.1-0.4,0-1.1-0.1-1.3c0.1-1.3,0.1-0.5,0-1.5c0.2,0.5,0.2,0.5,0.2,1.1c0.3,0.6,0.4,1,0.8,1.5l0.6-2.7c0,0,0.4,2.7,0.4,2.7c0.3-0.6,0.3-0.9,0.5-1.3C23.5,4.6,23.7,4,23.7,4c-0.2,1.1-0.2,0.7-0.3,1.3c0,0.6,0,1.4,0.1,1.5l1.7-2.4l-0.7,2.8c0.2-0.1,0.9-0.9,1.5-1.4c0.2-0.2,0.5-0.6,0.5-0.6C26.1,5.7,26,6,25.8,6.4c-0.3,0.7-0.2,0.4-0.5,1.4l2.4-1.6l-1.6,2.3c0,0,0.1,0.1,1.2-0.5c0.6-0.2,1.1-0.4,1.5-0.6c-0.8,0.7-1.1,0.6-2.2,2l2.7-0.5c0,0-2.2,1.3-2.3,1.4c0.1,0.1,1.1-0.1,1.7,0c0.6,0,0.6,0,1.2-0.1c-0.7,0.2-0.9,0.1-1.6,0.4c-0.3,0.2-1,0.4-1.1,0.6l2.7,0.5c0,0-2.6,0.6-3.1,0.8c-0.1,0-0.3,0-0.5,0c-0.1,0-0.2,0-0.3,0c0.1-0.2,0.2-0.4,0.1-0.6c-0.9,0.2-1.7,0-2.4,0.2c0.1-0.2,0.1-0.4,0-0.5c-1.1,0.5-2,0.3-3,1c0-0.2,0-0.5-0.1-0.7c-1,0.5-1.8,0.2-2.5,0.8c0-0.2,0-0.4,0-0.5c-1,0.4-1.8,0.5-2.5,1.3c-0.2,0-0.4-0.1-0.6-0.1c0,0-0.1-0.1-0.1-0.1c0,0-0.1,0.1-0.1,0.1c-0.1,0-0.3,0-0.5,0C15.2,12.9,15.7,12.8,16.1,12.6"/>
                </g>
                <g>
                  <path fill="#231F20" d="M25.8,9.7l0.3,0c0,0-0.2-1.2-2.4-1.1h-0.5c0,0-0.2,0.7-0.2,0.7l1.5-0.1C24.4,9.2,25.5,9.1,25.8,9.7"/>
                </g>
                <g>
                  <path fill="#231F20" d="M23.3,10.5c0-0.1,0.3-0.4,0.3-0.4s0.1,0,0.1,0c0,0.1,0,0.1,0,0.2c0,0.2,0.1,0.4,0.4,0.5c0.1,0,0.4,0,0.4,0c0.2-0.1,0.4-0.3,0.4-0.5c0-0.1,0-0.1,0-0.2c0.2,0,0.4,0.2,0.6,0.4l0.5,0c0,0-0.3-1.2-2.4-0.9c0,0-0.5,0.7-0.5,0.8C22.8,10.6,23.3,10.6,23.3,10.5"/>
                </g>
                <g>
                  <path fill="#231F20" d="M18.5,10.5l0.5-0.1c0.1-0.2,0.4-0.3,0.5-0.4c0,0.1,0,0.1,0,0.1c0,0.3,0.1,0.5,0.4,0.5c0,0,0.3,0,0.4,0c0.2-0.1,0.4-0.3,0.4-0.5c0,0,0,0,0-0.1c0.1,0,0.1,0,0.1,0s0.3,0.3,0.3,0.3c0,0.1,0.5,0.1,0.5,0c0-0.1-0.5-0.8-0.5-0.8C18.8,9.3,18.5,10.5,18.5,10.5"/>
                </g>
                <g>
                  <path fill="#231F20" d="M19.8,9.2l1.6,0.1c0,0-0.2-0.6-0.2-0.7h-0.4c-2.4-0.1-2.6,1-2.6,1l0.3,0C18.8,9,19.8,9.2,19.8,9.2"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M42,42.4c-0.3,0.9,0,1.5-0.8,2.2c-0.5,0.1-0.5,0.3-0.7,0.5c0.1-0.3,0.2-0.7,0.4-0.9c-0.2,0.2-0.4,0.3-0.6,0.6c-0.1-0.1-0.3-0.1-0.7-0.7c-0.4-0.8-0.1-1.1-0.3-1.9c0.4,0.7,1,1.3,1.1,1.8C40.8,43.3,41.7,42.3,42,42.4"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M42.4,39.3c-0.4,1-0.1,1.4-1,2.3c-0.6,0.3-0.6,0.4-0.7,0.6c0.1-0.4,0.3-0.7,0.5-1c-0.3,0.2-0.5,0.4-0.7,0.7c-0.1-0.1-0.4,0-0.6-0.5c-0.4-0.7,0-1-0.1-1.8c0.4,0.6,1,1.1,1,1.5C41,40.4,42.1,39.3,42.4,39.3"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M40.8,38.5c0.2-0.7,1-1.9,1.4-2c-0.3,1,0.1,1.4-0.7,2.3c-0.6,0.3-0.5,0.5-0.7,0.7c0.1-0.4,0.2-0.7,0.4-1c-0.2,0.2-0.4,0.5-0.6,0.7c-0.1-0.1-0.4,0.1-0.7-0.4c-0.5-0.6-0.2-1-0.4-1.7C40,37.6,40.6,38,40.8,38.5"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M42.1,33.5c-0.3,1,0.1,1.4-0.7,2.3c-0.6,0.3-0.5,0.4-0.7,0.6c0.1-0.3,0.2-0.7,0.3-0.9c-0.2,0.2-0.4,0.4-0.6,0.7c-0.1-0.1-0.4,0-0.8-0.5c-0.5-0.6-0.2-0.9-0.4-1.7c0.5,0.5,1.2,0.9,1.3,1.3C40.9,34.7,41.8,33.5,42.1,33.5"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M40.6,33.2c0-0.7,0.7-1.9,1-2c-0.1,1,0.4,1.3-0.2,2.3c-0.5,0.4-0.4,0.5-0.5,0.7c0-0.3,0-0.7,0.2-1c-0.2,0.2-0.4,0.5-0.4,0.8c-0.2,0-0.4,0.1-0.8-0.3c-0.6-0.5-0.4-0.9-0.7-1.5C39.6,32.5,40.4,32.8,40.6,33.2"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M15.1,15.2c0.1-0.2,0.3-0.2,0.4-0.8c0.4-1.3,1.1-1.1,2-1.7c0.3,0.4-0.8,1.8-1.4,2.2c0.4-0.1,1,0.5,1.6,0.7c-0.7,0.1-1,0.5-1.7,0.2c-0.5-0.2-0.5-0.4-0.6-0.5c0.2-0.2,0.4-0.5,0.6-0.8C15.7,14.9,15.4,15.1,15.1,15.2"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M27.3,14.8c-0.3,0-0.5-0.1-0.8-0.2c0.4-0.1,0.8-0.3,1.1-0.5c0.7,0.1,1.3,0.5,1.3,1.1c-0.6-0.2-0.7,0.2-1.6,0.3c-0.8,0.2-1.2-0.3-1.9-0.4c-0.2-0.2-0.3-0.3-0.3-0.5c0.4,0.1,0.7,0.1,1.1,0l0,0.1C26.6,14.9,26.9,14.9,27.3,14.8"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M24.7,13.8c0.4,0,0.7-0.1,1-0.2c-0.2,0.1-0.5,0-0.8,0c0.5-0.3,0.8-0.7,1.5-0.7c0.9,0,0.9,0.5,1.5,0.3C28,13.6,25.4,14.8,24.7,13.8"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M23.5,13.6c0.4,0.1,0.8,0.9,1.3,1.4c-0.7-0.2-1,0-1.6-0.5c-0.5-0.4-0.4-0.6-0.4-0.8c0.3-0.1,0.5-0.3,0.7-0.5c-0.3,0.2-0.6,0.3-0.9,0.3c0.2-0.1,0.3-0.1,0.6-0.6c0.8-0.8,1.2-0.3,2.2-0.6C25.2,12.7,24.1,13.5,23.5,13.6"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M21.4,13.8c0.4,0.1,0.9,0.7,1.5,1.1c-0.7-0.1-1,0.2-1.7-0.2c-0.5-0.3-0.4-0.5-0.5-0.7c0.3-0.2,0.4-0.4,0.6-0.7c-0.2,0.2-0.6,0.4-0.9,0.5c0.2-0.2,0.3-0.2,0.5-0.7c0.7-0.9,1-0.6,2-1C22.9,12.6,22,13.6,21.4,13.8"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M20,15.1c-0.7,0-1,0.3-1.7,0c-0.5-0.2-0.5-0.4-0.6-0.6c0.3-0.2,0.4-0.4,0.6-0.7c-0.2,0.2-0.6,0.4-0.9,0.6c0.2-0.2,0.3-0.1,0.5-0.8c0.7-1,1.1-0.7,2.1-1.2c0,0.3-0.9,1.5-1.6,1.8C18.8,14.2,19.4,14.8,20,15.1"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M40.8,28.3c-0.1,1,0.4,1.3-0.2,2.3c-0.5,0.3-0.4,0.5-0.5,0.7c0-0.3,0-0.7,0.2-1c-0.2,0.2-0.3,0.5-0.4,0.8c-0.2-0.1-0.4,0-0.8-0.4c-0.6-0.5-0.4-0.9-0.8-1.6c0.6,0.5,1.4,0.8,1.6,1.2C39.8,29.6,40.5,28.3,40.8,28.3"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M39.3,28.2c-0.1-0.7,0.3-1.9,0.6-2.1c0.1,1,0.7,1.3,0.2,2.3c-0.4,0.4-0.3,0.5-0.4,0.7c-0.1-0.3-0.1-0.7,0-1c-0.1,0.2-0.2,0.5-0.3,0.8c-0.2,0-0.4,0.1-0.9-0.2c-0.7-0.4-0.6-0.9-1-1.5C38.2,27.6,39,27.8,39.3,28.2"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M38.5,23.3c0.1,1,0.7,1.3,0.2,2.3c-0.4,0.4-0.3,0.5-0.4,0.7c-0.1-0.3-0.1-0.7,0-1c-0.1,0.2-0.3,0.5-0.3,0.8c-0.2,0-0.4,0.1-0.9-0.3c-0.7-0.5-0.6-0.9-1-1.5c0.6,0.4,1.5,0.6,1.8,1C37.8,24.6,38.2,23.4,38.5,23.3"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M37.2,21.2c0.3,0.9,0.9,1.2,0.6,2.2c-0.4,0.4-0.2,0.6-0.3,0.8c-0.1-0.3-0.2-0.6-0.2-1c-0.1,0.3-0.2,0.5-0.1,0.8c-0.2,0-0.3,0.1-0.9-0.2c-0.8-0.4-0.8-0.8-1.3-1.4c0.7,0.3,1.6,0.5,1.9,0.8C36.7,22.7,36.9,21.4,37.2,21.2"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M35.3,18.5c0.4,1,0.8,0.9,0.7,2.1c-0.2,0.6-0.1,0.7-0.1,1c-0.2-0.3-0.3-0.7-0.4-1c0,0.3,0,0.6,0.1,1c-0.2,0-0.3,0.3-0.8,0.1c-0.7-0.2-0.8-0.7-1.3-1.1c0.7,0.1,1.5,0,1.8,0.3C35,20.1,35.1,18.7,35.3,18.5"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M33.7,19c-0.4-0.6-0.5-1.9-0.2-2.2c0.5,0.9,0.9,0.7,0.9,1.9c-0.1,0.6,0,0.7,0,0.9c-0.2-0.3-0.4-0.6-0.5-0.9c0,0.3,0,0.6,0.2,0.9c-0.2,0.1-0.2,0.3-0.8,0.2c-0.8-0.1-0.9-0.5-1.5-0.9C32.6,19,33.3,18.8,33.7,19"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M31.1,14.7c0.6,0.8,1.2,0.8,1.3,2c-0.2,0.6,0,0.7,0,0.9c-0.2-0.3-0.4-0.6-0.5-0.9c0,0.3,0.1,0.6,0.2,0.9c-0.2,0.1-0.2,0.3-0.8,0.3c-0.8,0-0.9-0.4-1.5-0.8c0.7,0,1.5-0.3,1.9-0.1C31.2,16.4,30.9,15,31.1,14.7"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M30.5,14.9c-0.1,0.7,0.1,0.6,0.2,0.9c-0.1,0-0.1,0-0.2,0c-0.2-0.3-0.5-0.6-0.6-0.7c0.1,0.5,0.1,0.7,0.3,1c-0.1,0.1-0.2,0.2-0.5,0.2c-1,0.1-1.1-0.2-1.5-0.4c0.6-0.3,0.9-0.6,1.5-0.4c0-0.1,0-0.1-0.1-0.2h0c0,0,0,0,0,0c-0.1-0.2-0.3-0.4-0.5-0.7c-0.3-0.5-0.5-1-0.4-1.2C29.5,14.1,30.2,13.8,30.5,14.9"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M13,15.8c0.3-1.1,0.9-0.9,1.5-1.6c0.2,0.3-0.3,1.6-0.8,2.1c0.4-0.1,1.1,0.3,1.9,0.4c-0.7,0.2-0.9,0.6-1.6,0.5c-0.6-0.1-0.6-0.3-0.8-0.4c0.2-0.2,0.3-0.5,0.3-0.8c-0.1,0.3-0.4,0.5-0.6,0.7C12.9,16.4,13,16.4,13,15.8"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M11.3,17.2c0.1-1.1,0.7-1.1,1.3-1.9c0.2,0.3-0.1,1.7-0.5,2.3c0.4-0.2,1.1,0,1.8,0.1c-0.6,0.3-0.7,0.7-1.5,0.7c-0.6,0-0.7-0.2-0.8-0.3c0.1-0.3,0.2-0.6,0.2-0.9c-0.1,0.3-0.3,0.6-0.5,0.9C11.3,17.8,11.5,17.8,11.3,17.2"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M9.4,18.8c0.1-1.2,0.4-1.1,0.9-2c0.2,0.3,0.1,1.7-0.2,2.2c0.4-0.2,1.1,0,1.8,0c-0.6,0.4-0.7,0.8-1.4,0.9c-0.6,0-0.7-0.2-0.8-0.2c0.1-0.3,0.1-0.6,0.1-0.9c-0.1,0.3-0.2,0.7-0.5,0.9C9.4,19.4,9.5,19.4,9.4,18.8"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M8.5,18.6c0.2,0.3,0.3,1.7-0.1,2.3c0.4-0.3,1.1-0.2,1.8-0.3c-0.6,0.4-0.6,0.9-1.3,1.1c-0.5,0.1-0.7-0.1-0.8-0.1c0.1-0.3,0.1-0.6,0.1-0.9c-0.1,0.3-0.2,0.7-0.4,1c0-0.2,0.1-0.3-0.1-0.9C7.7,19.6,8.1,19.6,8.5,18.6"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M6.7,21.4c0.3,0.1,0.5,1.4,0.2,2.1c0.3-0.3,1.2-0.5,1.9-0.8c-0.5,0.6-0.5,1-1.3,1.4c-0.6,0.3-0.7,0.1-0.9,0.2c0-0.3,0-0.6-0.1-0.8c0,0.3-0.1,0.7-0.2,1c0-0.2,0.1-0.3-0.3-0.8C5.7,22.6,6.4,22.3,6.7,21.4"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M5.1,25.7c-0.5-1,0.1-1.3,0.2-2.2c0.3,0.1,0.8,1.3,0.6,2c0.3-0.4,1.1-0.6,1.8-1c-0.4,0.6-0.3,1-1,1.5c-0.5,0.4-0.7,0.2-0.9,0.3c0-0.3-0.1-0.5-0.3-0.8c0.1,0.3,0.1,0.6,0,0.9C5.5,26.2,5.6,26,5.1,25.7"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M4.2,26.2c0.3,0.1,0.7,1.3,0.6,2c0.3-0.4,1.1-0.6,1.8-0.9c-0.5,0.6-0.3,1-1,1.4C5,29,4.8,28.9,4.6,29c0-0.3-0.1-0.5-0.3-0.8c0.1,0.3,0,0.6,0,1c-0.1-0.2,0.1-0.4-0.4-0.7C3.5,27.4,4.1,27.1,4.2,26.2"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M3.5,30.5c-0.6-0.9-0.1-1.3-0.2-2.2c0.3,0,1,1.2,1,1.9c0.2-0.4,1-0.7,1.5-1.2c-0.3,0.7-0.1,1-0.7,1.6C4.6,31,4.4,30.9,4.2,31c-0.1-0.3-0.2-0.5-0.4-0.7C4,30.5,4,30.9,4,31.2C3.9,31,4,30.9,3.5,30.5"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M2.5,31.2c0.3,0.1,1,1.3,1,2c0.2-0.4,1-0.7,1.5-1.1c-0.3,0.7-0.1,1-0.7,1.5c-0.5,0.4-0.7,0.3-0.8,0.3c-0.1-0.3-0.2-0.5-0.4-0.8c0.1,0.3,0.2,0.6,0.2,1c-0.1-0.2,0-0.3-0.5-0.7C2.1,32.5,2.5,32.2,2.5,31.2"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M3.5,36c-0.1-0.3-0.3-0.5-0.5-0.7c0.2,0.3,0.3,0.6,0.3,0.9c-0.1-0.2-0.1-0.3-0.6-0.6c-0.8-0.9-0.5-1.1-0.7-2.1c0.3,0,1.3,1.1,1.4,1.7c0.1-0.4,0.8-0.8,1.3-1.3c-0.2,0.7,0.1,1-0.4,1.6C3.9,36,3.6,35.9,3.5,36"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M1.9,36.5c0.3,0.1,1.3,1.1,1.4,1.8c0.1-0.4,0.8-0.8,1.2-1.3c-0.2,0.7,0.1,1-0.4,1.6c-0.3,0.5-0.6,0.3-0.7,0.4c-0.1-0.3-0.3-0.5-0.5-0.7c0.2,0.3,0.3,0.6,0.4,0.9c-0.2-0.2-0.1-0.3-0.6-0.6C1.9,37.8,2.2,37.5,1.9,36.5"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M1.8,39.4c0.3,0,1.3,0.9,1.6,1.5c0-0.4,0.6-0.9,1-1.5c0,0.7,0.3,1.1-0.1,1.7c-0.3,0.5-0.5,0.4-0.6,0.5C3.5,41.4,3.2,41.2,3,41c0.2,0.2,0.4,0.6,0.5,0.9c-0.2-0.2-0.1-0.3-0.7-0.6C1.4,40.1,2.8,41.8,1.8,39.4"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M3.5,43.7c0-0.4,0.7-1.1,1.1-1.8c-0.1,0.7,0.2,1.1-0.2,1.8c-0.3,0.6-0.5,0.6-0.6,0.7c-0.2-0.2-0.3-0.4-0.6-0.6c0.2,0.2,0.3,0.6,0.4,0.9c-0.2-0.2-0.1-0.3-0.7-0.5c-0.8-0.6-0.4-0.9-0.8-1.8C2.5,42.3,3.3,43.1,3.5,43.7"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M2.3,44.6c0.3-0.1,1.4,0.6,1.7,1.3c0-0.4,0.5-1.2,0.8-1.9c0,0.7,0.4,1,0.1,1.8c-0.2,0.6-0.4,0.6-0.5,0.8c-0.2-0.2-0.4-0.4-0.7-0.5c0.3,0.2,0.4,0.5,0.6,0.8C4,46.7,4,46.5,3.5,46.5C2.5,46,2.8,45.4,2.3,44.6"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M5,48.6c0-0.4,0.5-1.2,0.8-1.9c0,0.7,0.4,1,0.1,1.8c-0.2,0.6-0.4,0.6-0.5,0.8c-0.2-0.2-0.4-0.3-0.7-0.5C4.9,49,5,49.3,5.2,49.6C5,49.5,5,49.3,4.4,49.3c-1-0.5-0.7-1.1-1.2-1.9C3.5,47.3,4.7,48,5,48.6"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M3.8,49.6c0.3-0.2,1.6,0.4,2,0.9c-0.1-0.4,0.3-1.2,0.4-1.9c0.2,0.7,0.6,0.9,0.4,1.7c-0.1,0.6-0.3,0.6-0.4,0.8c-0.2-0.2-0.5-0.3-0.8-0.3c0.3,0.2,0.5,0.4,0.7,0.6C6,51.3,6,51.1,5.4,51.2C4.3,50.9,4.5,50.3,3.8,49.6"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M5.1,52.1c0.3-0.2,1.6,0.4,2,1c-0.1-0.4,0.3-1.1,0.5-1.8c0.1,0.7,0.5,0.9,0.4,1.7c-0.1,0.6-0.3,0.6-0.4,0.7c-0.2-0.2-0.5-0.3-0.8-0.4C7,53.4,7.2,53.7,7.4,54c-0.2-0.1-0.2-0.3-0.8-0.2C5.5,53.4,5.7,52.8,5.1,52.1"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M6,54.1c0.3-0.2,1.7,0.2,2.2,0.6C8,54.4,8.3,53.7,8.4,53c0.3,0.7,0.7,0.8,0.7,1.6c0,0.6-0.3,0.6-0.3,0.8c-0.2-0.1-0.6-0.2-0.8-0.2c0.3,0.1,0.6,0.3,0.8,0.5c-0.2-0.1-0.3-0.2-0.9-0.1C6.7,55.3,6.8,54.8,6,54.1"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M7.3,56.3c0.3-0.2,1.7,0.3,2.2,0.8c-0.2-0.4,0.2-1.1,0.3-1.8c0.2,0.7,0.7,0.8,0.6,1.6c0,0.6-0.3,0.6-0.3,0.8c-0.3-0.2-0.6-0.2-0.9-0.3c0.3,0.1,0.6,0.4,0.8,0.6c-0.2-0.1-0.3-0.3-0.9-0.2C8,57.5,8.1,57,7.3,56.3"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M12.4,59.9c-0.2-0.2-0.5-0.4-0.9-0.3c-1.2-0.1-1.5-0.9-2.4-1.4c0.3-0.2,1.8,0.1,2.4,0.5c-0.2-0.4,0-1.1-0.1-1.8c0.4,0.6,0.8,0.7,0.9,1.4c0.1,0.6,0.1,1,0.2,1.3c-0.2-0.2-0.4-0.3-0.7-0.5C12,59.4,12.2,59.7,12.4,59.9"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M32.7,59.8c-0.5-0.1-0.9,0.2-1.1,0.4c0.2-0.3,0.4-0.6,0.7-0.8c-0.2,0-0.5,0.2-0.7,0.3c0-0.3,0-0.6,0.1-1.1c0.1-0.7,0.6-0.8,1-1.4c-0.1,0.7,0.1,1.4-0.1,1.8c0.6-0.4,2.1-0.7,2.4-0.5C33.9,59.1,33.9,59.7,32.7,59.8"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M34.6,58.2c-0.6-0.1-0.7,0.1-0.9,0.2c0.3-0.2,0.5-0.4,0.9-0.6c-0.3,0.1-0.6,0.2-0.9,0.3c-0.1-0.2-0.3-0.2-0.3-0.8c0-0.7,0.4-0.9,0.7-1.6c0.1,0.7,0.4,1.4,0.2,1.8c0.5-0.5,2-1,2.3-0.8C35.7,57.4,35.8,58,34.6,58.2"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M36,56c-0.6-0.1-0.7,0-0.9,0.1c0.3-0.2,0.5-0.5,0.9-0.6c-0.3,0-0.6,0.1-0.9,0.2c-0.1-0.2-0.3-0.2-0.3-0.8c0-0.8,0.4-0.9,0.7-1.6c0.1,0.7,0.4,1.5,0.2,1.9c0.5-0.5,1.9-0.9,2.2-0.7C37.1,55.2,37.2,55.8,36,56"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M37.3,54.2c-0.6,0-0.6,0.1-0.8,0.2c0.2-0.3,0.4-0.5,0.7-0.7c-0.3,0.1-0.6,0.2-0.8,0.3C36.3,54,36,54,35.9,53.4c-0.2-0.8,0.2-1,0.4-1.7c0.2,0.7,0.6,1.4,0.5,1.9c0.4-0.6,1.7-1.2,2-1C38.2,53.3,38.4,53.9,37.3,54.2"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M38.7,51.6c-0.6-0.1-0.6,0.1-0.8,0.2c0.2-0.3,0.4-0.5,0.7-0.7c-0.3,0.1-0.6,0.2-0.8,0.3c-0.1-0.2-0.3-0.2-0.4-0.8c-0.1-0.8,0.2-1,0.4-1.7c0.2,0.7,0.6,1.5,0.5,1.9c0.4-0.6,1.7-1.1,2-0.9C39.6,50.7,39.8,51.3,38.7,51.6"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M39.7,49.6c-0.6,0-0.6,0.2-0.8,0.3c0.1-0.3,0.3-0.6,0.6-0.8c-0.3,0.1-0.5,0.2-0.7,0.5c-0.1-0.1-0.3-0.2-0.5-0.8c-0.3-0.8,0.1-1,0.1-1.8c0.3,0.7,0.8,1.4,0.8,1.8c0.3-0.6,1.5-1.3,1.8-1.2C40.4,48.6,40.6,49.2,39.7,49.6"/>
                </g>
                <g>
                  <path fill="#0B9E5C" d="M40.7,46.8c-0.6,0.1-0.6,0.2-0.8,0.3c0.1-0.3,0.3-0.6,0.6-0.8c-0.3,0.1-0.5,0.2-0.7,0.5c-0.1-0.1-0.3-0.1-0.6-0.8c-0.3-0.8,0.1-1.1,0.1-1.8c0.3,0.7,0.8,1.4,0.8,1.9c0.3-0.6,1.4-1.4,1.7-1.2C41.3,45.7,41.6,46.3,40.7,46.8"/>
                </g>
                <g>
                  <path fill="#EFB780" d="M36,50.5c0,0-3,0.5-6.8-2c0.3-0.4,0.4-1.4,0.3-2c-0.1-0.5-0.1-0.1-0.2,0.2c-0.4,0.9-0.6,1-1,1.3c-1.1-1.1-1.3-1.1-2.7-2c-1-0.6-1.1-0.9-1.7-1.3c-1,0.5-2.5,1.4-3.3,0.8c-0.6-0.3,0.8-0.7,1.3-1.5c0.5-0.7,1.1-0.9,1.9-0.7c1.5,0.3,2.3,0.9,4.6,1.8c2.8,0.2,4.8,1,7.6,1.6c0.5,0.1,1.2,0.2,1.5,0.2C37.1,48.2,36.7,49,36,50.5"/>
                </g>
                <g>
                  <path fill="#EFB780" d="M23.9,47.5c0.8,0.9,1.6,1.1,2.1,2.2c-0.2,0.3-0.5,0.4-0.9,0.2c-1.2-1.4-2.4-1.6-1.9-2.3c-0.3,0-0.5,0.3-0.4,0.5c0.2,1,1.3,1.4,1.7,2.2c-0.2,0.7-1.5,0.1-2.1-1.7l0-0.1c-0.3,0.5-0.5,0.7,0.4,1.8c0.2,0.4,0.3,0.8-0.3,0.7c0.1,0.1-0.3,0-0.1,0h0.1c0,0-0.1,0-0.1-0.1c0.3-1.7-1.3-1.4-1.2-1.4c0.2-0.6-0.7-1.2-1.5-0.8c0.1-0.5-0.8-1.2-2.1-0.7c-0.2-0.5-1.4-0.9-2.5,0.5c-2,0.2-5.3,1.9-7.2,1c-0.1,0.1-1.2-3.2-1.3-3.3c2.3-0.2,7.5-0.6,9.3-1.4c1.5-0.9,4.2-1.4,4.8-1.1c-0.1,0.5-1.2,1.4-0.6,1.9c1,1,2.7,0.5,3.8-0.1c1.1,1.2,3,1.4,3.7,2.9l-0.2,0.5c-0.4,0.3-0.4,0.3-0.9-0.4c-1.1-1.5-2.1-1.1-2.3-2.2C23.6,46.8,23.9,47.2,23.9,47.5"/>
                </g>
                <g>
                  <path fill="#EFB780" d="M18.5,50.5c-0.4,0.2,0.2-0.3,0.3-0.4l0.2-0.2c0.3-0.3,0.8-0.7,1.1-0.1c0.2,0.4-1.2,1.2-1.4,1.2c-0.2,0.1-0.3,0.1-0.4-0.1C18.2,50.7,18.3,50.6,18.5,50.5"/>
                </g>
                <g>
                  <path fill="#EFB780" d="M17.3,50.3c-0.2,0.1-0.4,0.1-0.6-0.1c-0.1-0.2,0.3-0.7,0.5-0.9c0.2-0.2,0.3-0.2,0.3-0.2l0.3-0.2c0.5-0.1,0.5,0,0.6,0.2C18.5,49.3,17.6,50.3,17.3,50.3"/>
                </g>
                <g>
                  <path fill="#EFB780" d="M20.6,50.6c0,0,0.6-0.6,0.8,0.2c0.1,0.3-0.5,0.7-1.1,0.9C20.1,51.5,19.4,51.5,20.6,50.6"/>
                </g>
                <g>
                  <path fill="#EFB780" d="M16,49.4c-0.2,0.1-0.6-0.3-0.4-0.4l0.2-0.4c0.4-0.1,0.6-0.3,0.7-0.1C16.6,48.7,16.1,49.3,16,49.4"/>
                </g>
                <g>
                  <path fill="#168FBA" d="M22,15.8c-9.1,0-16.5,9.6-16.8,21.7h33.7C38.5,25.5,31.1,15.8,22,15.8"/>
                </g>
                <g>
                  <path fill="#231F20" d="M23,42.5l0-9.6c0,0,3.3,0.1,4-0.1c0,0,1.2-0.5,1.4-1.5c0.1-0.7-0.4-2.8-0.5-3.2c0,0-0.6-0.4-0.6-0.9c0,0-0.3-3.6-1.1-4.7c-0.7-1-1.8-2.2-4.3-2.2c-2.4,0-3.6,1.6-3.8,2.7c-0.2,1.4,1.3,1.4,1.3,1.4s-0.7,0.6-0.9,0.9c-0.2,0.3-0.8,0.9-1.5,2.1c-0.7,1.2-1.1,3.2-0.8,3.8c0.3,0.6,1.4,1.4,2.7,1.6c1.3,0.1,2.2-0.1,2.2-0.1v9.5L23,42.5z"/>
                </g>
                <g>
                  <path fill="#D93E26" d="M19.4,29.4c-1,0-1.4,0.5-2.2,0.6c0,0,0.2-1.8,1.9-3.9c0.7-0.8,2.4-1.7,3.3-2.1l0-0.2c-0.8,0.1-2.8,0.5-3.3-0.4c-0.1-0.9,1.1-1.9,2.8-2c1,0,1.8,0.3,2.6,0.8c0.9,0.6,1.3,1.7,1.5,2.7c-1.2,0.5-1.4,0.7-1.6,0.8c0.2,0,0.4,0.1,0.8,0.1c0.4,0,0.7-0.1,0.9-0.1v0.6c0.1,0.1,0.2,0.2,0.2,0.3c-2,0.9-3,1.3-3.2,1.2c0,0,0.4,0.1,0.8,0.1c0.7,0,1.7-0.3,2.4-0.5c0,0.4,0,0.7,0.6,0.9l0,0l0,0c-2.1,1.1-2.8,1-3,0.9l-1.5-0.3c0.4,0.1,1.2,0.8,1.9,1c0.7,0.1,2.1-0.5,2.8-0.7l0.3,0.9c-1.2,0-2.6,1.8-4.4,1.1C21.9,30.5,20.7,29.4,19.4,29.4"/>
                </g>
                <g>
                  <path fill="#D93E26" d="M21.1,31.9c-0.9,0-2.4,0-2.4-0.1c-0.5-0.1-1.7-0.8-1.5-0.9c0.1,0,0.4,0.1,1.5-0.4C20,30,21.1,31.6,21.1,31.9"/>
                </g>
                <g>
                  <path fill="#D93E26" d="M27.5,30.9c-0.1,0.3-0.4,0.9-0.7,1c-0.3,0-1.4,0-2.1,0c0.5,0,0.8-0.2,1.1-0.3C27.7,30.6,27.5,30.9,27.5,30.9"/>
                </g>
                <g>
                  <path fill="#FFFFFF" d="M21,66.2c0.1-0.3,0-0.7,0-0.7s-0.7,1-1.6,1.5c-0.8,0.5-2.2,0.8-2.4,0.8c0.2,0.2,0.4,0.4,0.6,0.5c0.2,0,1.3-0.1,2.3-0.8C20.5,67.2,21,66.2,21,66.2"/>
                </g>
                <g>
                  <path fill="#168FBA" d="M23.1,66.2c-0.1-0.3,0-0.7,0-0.7s0.7,1,1.6,1.5c0.8,0.5,2.2,0.8,2.4,0.8c0.4-0.4,0.7-0.7,0.7-0.7s-1.3-0.2-2.5-0.5c-1.2-0.4-1.8-1.1-1.9-1.4c-0.1-0.2,0.1-0.8,0.3-0.9c0.2-0.2,2.2-0.3,2.1-2c-0.1-2-3.4-1.5-3.9-1.4c-0.4-0.1-3.8-0.7-3.8,1.4c-0.1,1.7,2,1.9,2.1,2c0.2,0.2,0.4,0.7,0.3,0.9c-0.1,0.2-0.6,1-1.8,1.4c-1.2,0.4-2.5,0.5-2.5,0.5s0.3,0.4,0.6,0.7c0.2-0.1,1.6-0.4,2.4-0.8c0.9-0.6,1.6-1.5,1.6-1.5s0.2,0.4,0,0.7c0,0-0.5,1-1.2,1.4c-1,0.7-2.1,0.7-2.3,0.8c0.3,0.2,0.7,0.4,1,0.4c2.1-0.1,2.9-2.4,2.9-2.4s0.3,0.2,0.6,0.2h0h0c0.3,0,0.6-0.2,0.6-0.2s0.8,2.4,2.9,2.4c0.3,0,0.7-0.2,1-0.4c-0.3,0-1.3-0.2-2.3-0.8C23.6,67.2,23.1,66.2,23.1,66.2"/>
                </g>
                <g>
                  <path fill="#FFFFFF" d="M27.2,67.8c-0.3-0.1-1.6-0.4-2.4-0.8c-0.9-0.6-1.6-1.5-1.6-1.5s-0.1,0.4,0,0.7c0,0,0.5,1,1.2,1.4c1,0.6,2,0.7,2.3,0.8h0C26.8,68.2,27,68,27.2,67.8"/>
                </g>
                <g>
                  <path fill="#FFFFFF" d="M20.5,61.5c0,0-0.5,0.4-0.6,0.9c-0.1,0.5,0.1,1,0.1,1S18.9,63,19,62.3C19,61.2,20.5,61.5,20.5,61.5"/>
                </g>
                <g>
                  <path fill="#FFFFFF" d="M23.7,61.5c0,0,0.5,0.4,0.6,0.9c0.1,0.5-0.1,1-0.1,1s1.1-0.4,1.1-1.1C25.1,61.2,23.7,61.5,23.7,61.5"/>
                </g>
                <g>
                  <path fill="#FFFFFF" d="M22.1,61.9H22c-1.9,0-0.8,1.7-0.8,1.7s0.3-0.2,0.8-0.2h0.2c0.5,0,0.8,0.2,0.8,0.2S24,61.9,22.1,61.9"/>
                </g>
                <g>
                  <path fill="#FFFFFF" d="M22.2,64.2h-0.1h0H22c-0.5,0-0.5,0.3-0.5,0.5c0,0.2,0.1,1.3,0.5,1.3h0h0.1c0.4,0,0.5-1,0.5-1.3C22.6,64.4,22.7,64.2,22.2,64.2"/>
                </g>
              </g>
            </g>
          </svg>
          <a href="#" class="acerca">Acerca de esta visualización</a>
          <button id="buttonNav"><svg x="0px" y="0px" viewBox="0 0 18.4 9.8"><polyline fill="none" stroke="#818285" stroke-width="0.75" stroke-miterlimit="10" points="18.2,9.5 9.2,0.5 0.3,9.5 " /></svg></button>
        </nav>
        <header class="nav_map">
            <div>
              <h1>Explorá dónde vivimos los argentinos</h1>
              <h2>Densidad poblacional por radio censal</h2>
            </div>
            <div>
              <div class="scale_graphic">
                <svg x="0px" y="0px" viewBox="0 0 261.7 41.1">
                	<g>
                		<text transform="matrix(1 0 0 1 208.553 35.7717)">5.895</text>
                	</g>
                	<g>
                		<text transform="matrix(1 0 0 1 19.7978 35.7717)">0</text>
                	</g>
                	<linearGradient id="SVGID_1_" gradientUnits="userSpaceOnUse" x1="77.4009" y1="-201.7549" x2="245.1427" y2="-201.7549" gradientTransform="matrix(1 0 0 1 -43 233.3822)">
                		<stop  offset="1.082191e-02" style="stop-color:#FFFFFF"/>
                		<stop  offset="5.005246e-02" style="stop-color:#FFF2EA"/>
                		<stop  offset="0.1242" style="stop-color:#FDD7C3"/>
                		<stop  offset="0.2255" style="stop-color:#F9AD8B"/>
                		<stop  offset="0.3486" style="stop-color:#F4774C"/>
                		<stop  offset="0.438" style="stop-color:#EF4523"/>
                		<stop  offset="0.515" style="stop-color:#DA4023"/>
                		<stop  offset="0.6606" style="stop-color:#B33621"/>
                		<stop  offset="0.8575" style="stop-color:#792418"/>
                		<stop  offset="1" style="stop-color:#53140C"/>
                	</linearGradient>
                	<path fill="url(#SVGID_1_)" d="M34.7,27.6h167.5v8H34.7c-0.2,0-0.3-0.1-0.3-0.3v-7.4C34.4,27.7,34.5,27.6,34.7,27.6z"/>
                	<g>
                		<path style="transform: translate(-10px, 4px)" d="M39.5,9.9h-9.8c-0.1,0-0.3-0.1-0.3-0.3c0-0.1,0-0.1,0-0.2c-0.1-1,0.5-1.9,1.5-2.2c0.6-0.2,1.2-0.4,1.8-0.6 c0.2-0.1,0.5-0.2,0.7-0.4c0.2-0.1,0.2-0.3,0-0.5C32.7,5,32.3,4,32.3,3c0-1.3,1.2-2.4,2.3-2.3c1.2,0.1,2.2,1.3,2.1,2.5 c-0.1,1-0.5,1.9-1.1,2.6c-0.2,0.2-0.2,0.4,0,0.5c0.2,0.1,0.4,0.3,0.7,0.4c0.6,0.2,1.2,0.4,1.8,0.5c1.1,0.3,1.7,1.3,1.7,2.4 c0,0,0,0,0,0C39.8,9.8,39.6,9.9,39.5,9.9z"/>
                		<text transform="matrix(1 0 0 1 35 14.2615)">Cantidad de habitantes por km</text>
                		<text transform="matrix(0.8 0 0 0.8 205 11)">2</text>
                	</g>
                </svg>
              </div>
            </div>
            <a href="#" class="acercaMobile">Acerca de esta visualización</a>
        </header>
      </div>

      <div id="tooltip" class="tooltip">
        <svg id="palito_tool" x="0px" y="0px" viewBox="0 0 15 15">
          <g>
            <line fill="none" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" x1="12.5" y1="12.5" x2="2.5" y2="2.5"/>
            <circle fill="#FFFFFF" cx="2.5" cy="2.5" r="1.6"/>
          </g>
        </svg>
        <div id="contentTooltip" class="contentTooltip"></div>
      </div>

      <div id='map'></div>
      <button id="panel_show"><svg x="0px" y="0px" viewBox="0 0 18.4 9.8"><polyline fill="none" stroke="#818285" stroke-width="0.75" stroke-miterlimit="10" points="18.2,9.5 9.2,0.5 0.3,9.5 " /></svg></button>
      <section id="container_panel" class="container_panel"><!-- Left Command Panel -->
        <header><h3>Clickeá sobre las capitales  de las provincias para ver  otros resultados</h3></header>
        <article>
          <svg id="svgMap" x="0px" y="0px" viewBox="0 0 189.7 336.8">
          	<g id="provs">
          		<polygon id="misiones" class="prov provhover" points="137.3,55 139.5,58.8 139.7,60.4 141.4,62.2 148.9,57.8 151.3,57.5 155.7,54.3 157.5,45.8 156.4,43.6 155.1,38.9 149.2,39.9 148.2,47 143.2,51.4" />
          		<polygon id="corrientes" class="prov provhover" points="136.3,65.5 130.9,72.2 126.1,78 120.5,83.1 117.7,89.3 113,84.9 101.1,86.1 101.3,82.2 105,71.3 106.1,62.9 108.5,60.2 109.7,55.4 118.6,56.3 133.6,56.8 137.3,55 139.5,58.8 139.7,60.4 141.4,62.2" />
          		<polygon id="formosa" class="prov provhover" points="77.7,7 78.9,8.5 81,9.6 89.5,17.2 95.6,22.7 100.9,23.4 111.1,30.2 113,32.2 116.1,32.4 120.7,35.1 121.1,37.1 116.1,44 114.2,50.7 103,43.6 101.1,40 92.7,32.4 84.9,27.7 83.9,26.2 77.7,23.7" />
          		<polygon id="larioja" class="prov provhover" points="49.6,85.4 45.9,76.7 40,72 39.3,69.1 35.5,66.4 33,67.7 28.7,67.3 20.7,62.6 14.6,61.5 11.6,66.8 15.9,69.3 18.9,72.4 19.4,79.1 25.7,82.3 29.4,85.6 33.9,91.4 35.3,96.8 37.3,102.6 41.8,103 45.3,102.4 45.3,90.7" />
          		<polygon id="tucuman" class="prov provhover" points="53.2,56.6 53,60.8 50.9,61.5 47.3,62.6 44.1,59.2 44.1,56.1 42.8,54.6 43.4,50.5 41.2,48.3 41.2,46 45.3,45.8 46.8,43.6 56.8,45.4 "/>
          		<polygon id="sanjuan" class="prov provhover" points="33.4,102.8 33.2,107.7 29.8,106.2 25.5,105.5 23.2,107.9 20,108.6 20,105.9 14.6,106.6 13.7,109.3 8.2,110.4 6.9,107.5 7.3,105.5 5,103.7 4.6,97.7 6.6,92.5 8.7,86.7 6.9,77.1 9.1,75.3 11.6,66.8 15.9,69.3 18.9,72.4 19.4,79.1 25.7,82.3 29.4,85.6 33.9,91.4 35.3,96.8 37.3,102.6" />
          		<polygon id="santafe" class="prov provhover" points="77.7,86.1 83,65.3 82.9,62.9 106.1,62.9 105,71.3 101.3,82.2 101.1,86.1 99.8,90.3 96.4,95 93.6,99.2 91.8,102.1 91.6,108.4 95.5,116.7 93.2,119.3 89.8,119.1 82.7,127.2 68,127.2 81.1,111.3 79.1,106.4 78,102.4 80.7,91.2" />
          		<polygon id="sanluis" class="prov provhover" points="33.4,102.8 33.2,107.7 33.7,112.4 35.3,117.3 40.3,126.5 41.8,131.4 40.9,141.4 52.8,141.4 52.8,115.5 54.3,110.6 54.1,106.6 51.8,106.2 50.5,104.1 45.3,102.4 41.8,103 37.3,102.6" />
          		<polygon id="cordoba" class="prov provhover" points="52.8,133.6 52.8,115.5 54.3,110.6 54.1,106.6 51.8,106.2 50.5,104.1 45.3,102.4 45.3,90.7 49.6,85.4 52.3,84.5 55,82 55.5,78.7 59.1,77.3 64.8,78.5 72.3,79.3 75.2,85.4 77.7,86.1 80.7,91.2 78,102.4 79.1,106.4 81.1,111.3 68,127.2 68,133.6" />
          		<polygon id="mendoza" class="prov provhover" points="8.2,110.4 13.7,109.3 14.6,106.6 20,105.9 20,108.6 23.2,107.9 25.5,105.5 29.8,106.2 33.2,107.7 33.7,112.4 35.3,117.3 40.3,126.5 41.8,131.4 40.9,141.4 29.4,141.4 29.4,161.1 21.4,157.8 16.9,156.6 14.6,153.5 10.7,151.9 8.2,147.9 8.2,139.9 6.9,138.1 8.5,136.1 10.7,128 11.9,126.3 11.9,118 9.6,116.2" />
          		<polygon id="neuquen" class="prov provhover" points="26.4,177.9 30.3,175.8 29.4,173.2 29.4,161.1 21.4,157.8 16.9,156.6 14.6,153.5 10.7,151.9 8.2,147.9 5.5,149.5 3.5,156.4 4.1,160.4 3.5,164.7 7.5,172.3 3.2,176.7 2.8,183.4 0.5,184.8 1.9,191.5 1,194.2 1.9,198.6 7.8,198.2 8.9,193.9 13.9,191.9 16.9,187 17.8,184.6" />
          		<polygon id="lapampa" class="prov provhover" points="29.4,161.1 29.4,141.4 40.9,141.4 52.8,141.4 52.8,133.6 68,133.6 68,178.3 59.6,173.6 50.5,173.4 44.3,172.5 38.7,169.4 34.6,168.5 33.2,163.8 31.4,161.8" />
          		<polygon id="buenosaires" class="prov provhover" points="111.4,124.5 106.1,122.9 101.8,119.8 95.5,116.7 93.2,119.3 89.8,119.1 82.7,127.2 68,127.2 68,178.3 68,191.3 71.2,192.8 73.2,195.3 77.3,191.3 76.4,186.8 77.3,181 79.1,180.1 77.3,175.6 76.4,171.4 77.3,170.3 80.7,172 88.2,172.9 95.2,172.5 102.7,170.7 111.8,167.1 118.2,161.3 123.9,152.8 123.9,147 120.5,147 118.9,142.3 119.8,138.3 119.3,135.6 114.3,131.6 111.1,128.3" />
          		<polygon id="rionegro" class="prov provhover" points="73.2,195.3 71.2,192.8 68,191.3 68,178.3 59.6,173.6 50.5,173.4 44.3,172.5 38.7,169.4 34.6,168.5 33.2,163.8 31.4,161.8 29.4,161.1 29.4,173.2 30.3,175.8 26.4,177.9 17.8,184.6 16.9,187 13.9,191.9 8.9,193.9 7.8,198.2 1.9,198.6 3.2,205.6 56.6,205.6 57.3,200.2 55.3,195.7 55.3,193 57.5,192.8 63.2,195.3 67.3,196.2 71.6,196.6" />
          		<polygon id="chubut" class="prov provhover" points="57.1,207.3 59.8,207.3 60.7,209.1 63,208 61.6,206.2 66.2,206.2 67.7,211.6 63.9,213.4 63.9,211.4 60.7,210.5 58.4,211.6 58.4,213.4 61.8,214.5 62.1,215.6 57.5,218.7 55.7,222.8 55.9,228.3 54.1,230.1 53.7,232.4 55,234.1 53.4,235.7 48.7,236.2 47.1,237.7 44.1,238.6 40.9,242.6 39.6,246.4 9.4,246.4 11,242.9 10.5,240.2 7.1,239.5 5.3,237.5 5.5,235.9 11.6,235.7 11.2,232.8 7.3,232.1 6,227.9 6.6,225.7 6.4,223.4 4.8,222.5 3.2,216.3 1.4,214.9 1.4,210.5 3.7,207.8 3.2,205.6 56.6,205.6" />
          		<polygon id="santacruz" class="prov provhover" points="8.9,248.4 10.5,255.8 8.7,259.6 7.1,267.4 8,272.3 8.2,281.3 5.3,287.1 5.5,295.6 6.9,297.8 8.7,301.8 11.6,300 15,300.9 14.8,305.2 17.5,310.3 30.5,310.3 34.1,311 37.3,311.7 38.4,310.8 34.6,304.7 32.5,298 33.7,291.3 40.5,284.9 41.2,280.8 42.8,275.2 49.1,269 51.4,268.8 51.6,266.3 53.7,260.7 53.2,257.8 51.4,256 46.4,255.1 42.8,252.5 40,249.6 39.6,246.4 9.4,246.4"/>
          		<polygon id="tierradelfuego" class="prov provhover" points="37.5,314.3 37.5,330.4 43.4,330.6 47.1,331.5 46.2,332.9 37.3,332 37.3,335.3 44.3,334.9 49.3,334.7 53.4,335.1 58,334.4 59.1,332.7 53.2,331.5 49.6,328.9 45.9,325.7 43.2,323.3 41.4,320.8 38.7,320.1 39.8,317.7" />
          		<polygon class="prov" points="110.8,289 110,290.3 108.7,290.4 108.9,291.4 106.8,294 105.1,296 103.6,295.5 102.6,296.3 101.7,297.8 100.4,298.7 99.7,297.6 98.4,297.3 97.2,295.8 99.2,295.3 100.5,294.9 101.1,293.3 100.5,291.7 102.6,291.7 104,292.3 101.9,290.3 100.9,289.2 99.5,287.9 100.7,287.2 102.2,289.1 104.6,288.5 106.6,288.7 108.5,289.2 109.3,287.6 110.8,287.4" />
          		<polygon class="prov" points="111.3,293.8 110.3,292.4 108.7,293.4 107.2,295.2 106.3,296.8 106.8,298.3 107.9,299.8 108.3,300.7 109.6,300.3 108.8,298.7 110.8,298.4 111.6,297.3 112.6,298.4 113.3,297 111.8,295.2 112.4,294.5 114.3,295.1 116.1,295.3 118.4,293.5 119.6,291.7 121.3,291.5 121.3,290.3 120.2,288.5 118.3,288.4 118.4,290.4 116.4,289.5 115,289.3 115,288.3 116.7,288.3 116.8,287.1 115.1,286.8 113,287.1 113.4,288.8 111.6,290.1 111.1,291.4 111.9,293" />
          		<polygon id="salta" class="prov provhover" points="66,39.6 77.7,25.2 77.7,23.7 77.7,7 72.4,1.5 60.2,1.5 57.3,7.8 54.6,4.7 51.8,3.9 49,3.8 48.7,11 50.8,12.3 52.2,16.5 55.9,16.1 59.4,17.3 59.4,25.7 56.9,27.6 52.8,28.6 46.8,27.3 42.5,22.6 42.5,18.4 38.7,17.3 38.2,22.8 35.5,24.4 30.3,21.3 28.9,24.2 19.8,29.1 18.4,31.8 18.9,34.2 27.1,35.6 38.2,36.2 38.4,40.3 36.2,41.2 39.6,46.1 45.3,45.8 46.8,43.6 56.8,45.4 59.6,40.7 65,40.5 65.9,39.6" />
          		<polygon id="catamarca" class="prov provhover" points="41.2,48.3 43.4,50.5 42.8,54.6 44.1,56.1 44.1,59.2 47.3,62.6 50.9,61.5 51.4,66.8 51.8,72.2 55,75.1 55.5,78.7 55,82 52.3,84.5 49.6,85.4 45.9,76.7 40,72 39.3,69.1 35.5,66.4 33,67.7 28.7,67.3 20.7,62.6 14.6,61.5 16.2,54.6 20,53.9 20.5,51.9 18,47.6 19.4,44.3 18.9,34.2 27.1,35.6 38.2,36.2 38.4,40.3 36.2,41.2 39.6,46.1 41.2,46" />
          		<polygon id="chaco" class="prov provhover" points="108.5,60.2 109.7,55.4 114.2,50.7 103,43.6 101.1,40 92.7,32.4 84.9,27.7 83.9,26.2 77.7,23.7 77.7,25.2 66,39.6 82.9,39.6 82.9,62.9 106.1,62.9" />
          		<polygon id="santiagodelestero" class="prov provhover" points="66,39.6 82.9,39.6 82.9,62.9 83,65.3 77.7,86.1 75.2,85.4 72.3,79.3 64.8,78.5 59.1,77.3 55.5,78.7 55,75.1 51.8,72.2 51.4,66.8 50.9,61.5 53,60.8 53.2,56.6 57.1,49.4 56.8,45.4 59.6,40.7 65,40.5" />
          		<polygon id="jujuy" class="prov provhover" points="31.6,12.3 30.5,10.1 33.4,7 37.3,4.7 38,1.6 39.6,0.5 45.5,3.6 49,3.8 48.7,11 50.8,12.3 52.2,16.5 55.9,16.1 59.4,17.3 59.4,25.7 56.9,27.6 52.8,28.6 46.8,27.3 42.5,22.6 42.5,18.4 38.7,17.3 38.2,22.8 35.5,24.4 30.3,21.3" />
          		<polygon id="entrerios" class="prov provhover" points="117.7,89.3 113,84.9 101.1,86.1 99.8,90.3 96.4,95 93.6,99.2 91.8,102.1 91.6,108.4 95.5,116.7 101.8,119.8 106.1,122.9 111.4,124.5 111.1,119.3 111.1,115.5 113.4,113.1 113.4,101.7 116.4,94.5" />
          	</g>
            <circle fill="#EE4423" cx="49" cy="49.7" r="2.9"/>
          	<circle fill="#EE4423" cx="61.4" cy="59.8" r="2.9"/>
          	<circle fill="#EE4423" cx="44.8" cy="66.8" r="2.9"/>
          	<circle fill="#EE4423" cx="44.8" cy="31.5" r="2.9"/>
          	<circle fill="#EE4423" cx="47.7" cy="20.9" r="2.9"/>
          	<circle fill="#EE4423" cx="112.8" cy="40.8" r="2.9"/>
          	<circle fill="#EE4423" cx="142.4" cy="56" r="2.9"/>
          	<circle fill="#EE4423" cx="111.8" cy="61.5" r="2.9"/>
          	<circle fill="#EE4423" cx="95.5" cy="105.7" r="2.9"/>
          	<circle fill="#EE4423" cx="88.1" cy="102" r="2.9"/>
          	<circle fill="#EE4423" cx="61.4" cy="95.9" r="2.9"/>
          	<circle fill="#EE4423" cx="33.2" cy="77.3" r="2.9"/>
          	<circle fill="#EE4423" cx="20.9" cy="97.9" r="2.9"/>
          	<circle fill="#EE4423" cx="42.8" cy="118.7" r="2.9"/>
          	<circle fill="#EE4423" cx="20.9" cy="116.7" r="2.9"/>
          	<circle fill="#EE4423" cx="58.5" cy="147.9" r="2.9"/>
          	<circle fill="#EE4423" cx="114.2" cy="136.2" r="2.9"/>
          	<circle fill="#EE4423" cx="68" cy="193.4" r="2.9"/>
          	<circle fill="#EE4423" cx="25.5" cy="174.3" r="2.9"/>
          	<circle fill="#EE4423" cx="54.7" cy="217.1" r="2.9"/>
          	<circle fill="#EE4423" cx="29.6" cy="302.7" r="2.9"/>
          	<circle fill="#EE4423" cx="104.7" cy="56.8" r="2.9"/>
          	<circle fill="#EE4423" cx="41.8" cy="332.9" r="2.9"/>
          	<g>
              <line fill="none" stroke="#808184" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="2.1481,2.1481" x1="131.3" y1="130.4" x2="115.2" y2="130.4"/>
          		<path fill="none" stroke="#808184" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="1.9813,1.9813" d=" M187.3,128.3c1.3,16.3-12.2,29.9-28.6,28.6c-12.8-1-23.3-11.4-24.3-24.3c-1.3-16.3,12.2-29.9,28.6-28.6 C175.9,105,186.3,115.4,187.3,128.3z"/>
              <polygon id="ciudadautonomadebuenosaires" class="prov provhover" points=" 178.6,132.8 178.6,132.6 179,132.7 179.1,132.5 178,131.8 177.8,132.4 177.7,132.4 177.6,132.6 177.5,132.5 177.4,132.3 177.4,132.1 177.6,132 177.8,131.9 177.7,131.8 177.4,131.8 177,132 176.6,131.9 176.6,131.9 177.7,131.5 178,131.6 178.4,131.5 178.6,131.3 178.6,130.9 178.5,130.4 178.5,130.4 178.4,130.1 178.3,129.9 178.3,129.9 178.1,129.7 177.5,128.3 177.3,128.2 177.1,128 176.6,127.3 176.6,127.3 176.4,127.2 176.4,127.1 176.2,127 176.1,126.9 176,126.9 175,127.2 174.9,127.2 174.8,126.9 174.7,127 174.8,127.3 174.1,127.3 174.5,126.4 174.7,126.7 174.8,126.7 174.5,126 174.3,126 174.2,125.9 174.2,125.8 174.3,125.8 175.5,125.6 175.7,125.5 175.7,125.5 175,125.4 175,125.1 175,125.1 175.7,125.1 175.6,124.7 174.8,124.8 174.6,124.5 175.1,124.5 175.2,124.5 175.4,124.5 175.4,124.2 175.3,124.1 174.2,124.2 174.8,123.8 174.3,123.4 173.8,123.5 173.4,123.5 173,123.3 173.9,123.2 173.1,122.5 172.1,122.6 171.8,122.3 171.8,122.3 172.7,122.3 172.7,122.2 172.3,121.9 172.3,121.7 170.6,121.6 170.6,121.8 171.9,122.8 171.8,122.9 171.8,122.9 171.5,122.9 170.7,122.3 170.4,122.1 170.4,122 170.4,122 170.2,121.7 170,121.4 169.7,121 169.7,120.9 169.5,120.8 168.8,120.2 168.5,120.1 167.7,119.1 167.5,119 167.6,118.7 167.5,118.8 167.5,118.8 167.4,118.9 167.2,118.7 167,118.6 167,118.5 166.4,118.3 166,118.1 165.5,117.9 165.2,117.7 164.3,116.8 164.2,116.6 163.7,115.8 163.6,115.7 163.6,115.5 163.4,115.3 163.3,115.2 163.3,115.2 163.1,115.2 162.8,115.2 162.8,115.2 162.6,115.1 162.6,115 162.6,114.8 161.3,114.4 161.3,114.4 161.3,114.3 161.3,114.2 161.4,114.2 161.3,114.2 161.3,114.2 161.2,114.4 161.2,114.4 161.1,114.6 161,114.6 160.8,114.8 160.7,115 160.6,114.9 160.4,114.8 160.3,114.6 160.6,114.2 160.7,114.1 160.9,114.2 161,114.1 161.1,114.1 161.1,113.9 161,113.6 160.9,113.4 160.9,113.3 160.6,113.8 160.5,114 160.1,114.3 159.8,114.4 159.7,114.4 159.7,114.4 160,114.2 160.3,114.1 160.3,113.9 160.2,113.8 160.2,113.6 159.6,112.5 159.5,112.7 159.3,113 158.9,113.6 158.9,113.6 156.8,114.6 152.6,116.5 149.1,124.4 148.4,126 147.5,128.1 146.8,129.7 146.8,133.5 146.8,137.6 157.5,148.3 159.5,145.9 161.1,143.8 164.2,139.9 164.2,139.9 164.2,139.9 166.2,139.3 167.6,139.6 167.7,139.7 167.9,139.7 168,139.8 168.2,139.9 168.5,139.9 168.6,140.1 168.7,140.1 168.9,140.2 172.5,139.3 173.4,138.3 173.5,138.2 175.5,136.9 175.5,135.9 176,135.5 177.9,134.2 177.4,133.8 177.7,133.7 177.9,134 178.1,134 178.3,133.8 177.9,133.4 178.1,133.4 178.4,133.6 178.6,133.8 178.7,133.9 179,133.8 179.2,133.6 179.3,133.3 	"/>
          	</g>
            <circle fill="#EE4423" cx="161" cy="130" r="2.9"/>
          </svg>
        </article>
        <footer>
          COMPARTÍ LA VISUALIZACIÓN DE ESTA LOCALIDAD
          <aside>
            <svg x="0px" y="0px" viewBox="0 0 18 18"><path d="M14.2,10.5c1,0,1.9,0.4,2.7,1.1s1.1,1.6,1.1,2.7s-0.4,1.9-1.1,2.7S15.3,18,14.2,18s-1.9-0.4-2.7-1.1 s-1.1-1.6-1.1-2.7c0-0.1,0-0.2,0-0.4l-4.2-2.1c-0.7,0.7-1.6,1-2.6,1c-1,0-1.9-0.4-2.7-1.1S0,10,0,9s0.4-1.9,1.1-2.7s1.6-1.1,2.7-1.1 c1,0,1.8,0.3,2.6,1l4.2-2.1c0-0.2,0-0.3,0-0.4c0-1,0.4-1.9,1.1-2.7S13.2,0,14.2,0s1.9,0.4,2.7,1.1S18,2.7,18,3.8s-0.4,1.9-1.1,2.7 s-1.6,1.1-2.7,1.1c-1,0-1.8-0.3-2.6-1L7.5,8.6c0,0.2,0,0.3,0,0.4s0,0.2,0,0.4l4.2,2.1C12.4,10.8,13.3,10.5,14.2,10.5z" /></svg>
            <svg x="0px" y="0px" viewBox="0 0 18 18"><path d="M18,3.4v11.2c0,0.9-0.3,1.7-1,2.4s-1.5,1-2.4,1H3.4c-0.9,0-1.7-0.3-2.4-1s-1-1.5-1-2.4V3.4 C0,2.4,0.3,1.7,1,1s1.5-1,2.4-1h11.2c0.9,0,1.7,0.3,2.4,1S18,2.4,18,3.4z M15,5.6C14.6,5.8,14.1,6,13.6,6c0.5-0.3,0.9-0.8,1.1-1.4 c-0.5,0.3-1,0.5-1.6,0.6c-0.5-0.5-1.1-0.8-1.8-0.8c-0.7,0-1.3,0.2-1.7,0.7S8.8,6.3,8.8,7c0,0.2,0,0.4,0.1,0.6c-1-0.1-2-0.3-2.8-0.8 S4.4,5.7,3.8,4.9C3.6,5.3,3.5,5.8,3.5,6.2c0,0.9,0.4,1.6,1.1,2.1c-0.4,0-0.8-0.1-1.2-0.3v0c0,0.6,0.2,1.1,0.6,1.6s0.9,0.7,1.4,0.8 c-0.2,0.1-0.4,0.1-0.6,0.1c-0.1,0-0.3,0-0.5,0c0.2,0.5,0.5,0.9,0.9,1.2s0.9,0.5,1.4,0.5c-0.9,0.7-1.9,1.1-3.1,1.1 c-0.2,0-0.4,0-0.6,0c1.2,0.7,2.4,1.1,3.8,1.1c0.9,0,1.7-0.1,2.5-0.4s1.4-0.6,2-1.1s1-1,1.4-1.6s0.7-1.2,0.9-1.9s0.3-1.3,0.3-2 c0-0.1,0-0.2,0-0.3C14.3,6.6,14.7,6.1,15,5.6z" /></svg>
            <svg x="0px" y="0px" viewBox="0 0 18 18"><path d="M14.6,0c0.9,0,1.7,0.3,2.4,1s1,1.5,1,2.4v11.2c0,0.9-0.3,1.7-1,2.4s-1.5,1-2.4,1h-2.2v-7h2.3l0.4-2.7h-2.7 V6.6c0-0.4,0.1-0.8,0.3-1s0.5-0.3,1.1-0.3l1.4,0V2.8c-0.5-0.1-1.2-0.1-2.1-0.1c-1.1,0-1.9,0.3-2.5,0.9s-1,1.5-1,2.6v2H7.3V11h2.3v7 H3.4c-0.9,0-1.7-0.3-2.4-1s-1-1.5-1-2.4V3.4C0,2.4,0.3,1.7,1,1s1.5-1,2.4-1H14.6z" /></svg>
          </aside>
        </footer>
      </section>
    </section>
  </body>

</html>
