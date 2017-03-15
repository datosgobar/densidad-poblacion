$(window).ready(function() {

    var data = {
        generarReferenciaId: function(string) {
            var texto = string.toLowerCase();
            var reemplazar = [
                ['á', 'a'],
                ['é', 'e'],
                ['í', 'i'],
                ['ó', 'o'],
                ['ú', 'u'],
                ['ñ', 'n'],
                [' ', '']
            ];

            for (var i = 0; i < reemplazar.length; i++) {
                while (texto.indexOf(reemplazar[i][0]) !== -1) {
                    texto = texto.replace(reemplazar[i][0], reemplazar[i][1]);
                }
            }

            return texto;
        }
    };
    var varGlobal = {
        datosHover: {
            features: [],
            type: 'FeatureCollection'
        },
        provincias: {
            prov02: {
                nombre: 'Ciudad Autónoma de Buenos Aires',
                coordenadas: [-58.5737501, -34.6156537]
            },
            prov06: {
                nombre: 'Buenos Aires',
                coordenadas: [-57.9882757, -34.9205233]
            },
            prov10: {
                nombre: 'Catamarca',
                coordenadas: [-65.8102653, -28.4645295]
            },
            prov14: {
                nombre: 'Córdoba',
                coordenadas: [-64.3347743, -31.3993435]
            },
            prov18: {
                nombre: 'Corrientes',
                coordenadas: [-58.8625991, -27.486234]
            },
            prov22: {
                nombre: 'Chaco',
                coordenadas: [-59.0655577, -27.4606439]
            },
            prov26: {
                nombre: 'Chubut',
                coordenadas: [-65.1382395, -43.2988118]
            },
            prov30: {
                nombre: 'Entre Ríos',
                coordenadas: [-60.585175, -31.74729934]
            },
            prov34: {
                nombre: 'Formosa',
                coordenadas: [-58.2651823, -26.1721389]
            },
            prov38: {
                nombre: 'Jujuy',
                coordenadas: [-65.3757678, -24.2053236]
            },
            prov42: {
                nombre: 'La Pampa',
                coordenadas: [-64.3714478, -36.6193668]
            },
            prov46: {
                nombre: 'La Rioja',
                coordenadas: [-66.9259895, -29.4142785]
            },
            prov50: {
                nombre: 'Mendoza',
                coordenadas: [-68.8936245, -32.8833303]
            },
            prov54: {
                nombre: 'Misiones',
                coordenadas: [-55.9596213, -27.396305]
            },
            prov58: {
                nombre: 'Neuquén',
                coordenadas: [-68.1856129, -38.9412136]
            },
            prov62: {
                nombre: 'Río Negro',
                coordenadas: [-63.035245, -40.8250187]
            },
            prov66: {
                nombre: 'Salta',
                coordenadas: [-65.5008411, -24.7960684]
            },
            prov70: {
                nombre: 'San Juan',
                coordenadas: [-68.5677821, -31.5317707]
            },
            prov74: {
                nombre: 'San Luis',
                coordenadas: [-66.379878, -33.2976372]
            },
            prov78: {
                nombre: 'Santa Cruz',
                coordenadas: [-69.3419608, -51.6284629]
            },
            prov82: {
                nombre: 'Santa Fe',
                coordenadas: [-60.7764694, -31.6181235]
            },
            prov86: {
                nombre: 'Santiago del Estero',
                coordenadas: [-64.3372607, -27.8016971]
            },
            prov90: {
                nombre: 'Tucumán',
                coordenadas: [-65.2928061, -26.8328416]
            },
            prov94: {
                nombre: 'Tierra del Fuego',
                coordenadas: [-68.3730148, -54.8053847]
            }
        },
        elementHover: null
    };

    iniciarApp(data, varGlobal);

    function iniciarApp(data, varGlobal) {

      if (window.location.search !== '') {
        data.coordenadas = [
          parseFloat(window.location.search.slice(1, window.location.search.length - 1).split(',')[0]),
          parseFloat(window.location.search.slice(1, window.location.search.length - 1).split(',')[1])
        ];
      } else {
        data.coordenadas = [-58.5737501, -34.6156537];
      }

      renderMap(data, varGlobal);
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
              zoom: 10,
              minZoom: 8,
              maxZoom: 11,
              pitch: 60,
              bearing: 0,
              maxBounds: [-72.9, -54.9, -53.6, -21.9]
          });

          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {

              if (position.coords.latitude != 0 && position.coords.longitude != 0) {
                data.mapbox.flyTo({ center: [position.coords.longitude, position.coords.latitude] });
              }
            }, function(error) {}, { enableHighAccuracy: true, timeout: 5000 });
          }

          data.mapbox.on('load', function(d) {
            $('.cargando').empty();
              data.mapbox.addControl(new mapboxgl.NavigationControl({
                  position: 'bottom-right'
              }));
              data.mapbox.addControl(new mapboxgl.ScaleControl({
                  position: 'bottom-left',
                  maxWidth: 80,
                  unit: 'metric'
              }));
              data.mapbox.addControl(new mapboxgl.Geocoder({
                  country: 'ar',
                  accessToken: mapboxgl.accessToken
              }));

              data.mapbox.setPaintProperty('dataset', 'fill-color', {
                  'property': 'color',
                  'type': 'identity'
              });
              data.mapbox.setPaintProperty('dataset', 'fill-extrude-height', {
                  'property': 'densidad',
                  'type': 'identity'
              });
              data.mapbox.addSource('Argentina-Hover', {
                  'type': 'geojson',
                  'data': varGlobal.datosHover
              });
              data.mapbox.addLayer({
                  'id': 'color-hover',
                  'source': 'Argentina-Hover',
                  'type': 'fill',
                  'paint': {
                      'fill-color': 'black',
                      'fill-extrude-height': {
                          'property': 'densidad',
                          'type': 'identity'
                      },
                      'fill-opacity': 0.5
                  }
              }, 'country-label-sm');

              funciones(data, varGlobal);
          });

          data.mapbox.on('mousemove', function(e) {
              var features = data.mapbox.queryRenderedFeatures(e.point, {
                  layers: ['dataset']
              });

              if (features.length) { // Si hay datos
                  varGlobal.datosHover.features[0] = features[0];
                  data.mapbox.getSource('Argentina-Hover').setData(varGlobal.datosHover);
                  data.mapbox.getCanvas().style.cursor = 'pointer'; // Defino tipo de cursor
                  $('#tooltip').css({'transform': `translate(${ e.point.x + 10 }px, ${ e.point.y + 154 }px)`}).show();
                  $('#contentTooltip').empty().append(`<section><article>${ varGlobal.provincias['prov' + features[0].properties.prov_id].nombre }</article><article><svg x="0px" y="0px" viewBox="0 0 13.8 12.2"><path fill="white" d="M13.8,12.2c-4.6,0-9.1,0-13.7,0c0-0.4-0.1-0.8-0.1-1.1c0-1.2,0.5-1.9,1.7-2.3c0.9-0.3,1.8-0.5,2.7-0.8c0.3-0.1,0.6-0.3,0.9-0.5c0.2-0.2,0.3-0.4,0-0.7C4.4,5.7,3.9,4.4,3.9,3C4,1.3,5.5-0.1,7,0C8.6,0.1,10,1.7,9.9,3.4C9.8,4.7,9.3,5.8,8.3,6.8C8.1,7,8.1,7.3,8.4,7.5c0.3,0.2,0.6,0.4,0.9,0.5c0.9,0.3,1.8,0.5,2.7,0.8c1.4,0.4,2,1.3,1.9,2.8C13.8,11.8,13.8,12,13.8,12.2z" /></svg><span class="textWhite">${ new Intl.NumberFormat("de-DE").format(parseFloat(features[0].properties.poblacion).toFixed(0)) } &nbsp; </span>( ${ new Intl.NumberFormat("de-DE").format(parseFloat(features[0].properties.poblacion / features[0].properties.area).toFixed(0)) } ) / km<span class="sup">2</span></article><article><svg x="0px" y="0px" viewBox="0 0 9.6 10"><polyline class="areaIconSvg" points="9.1,8 9.1,9.5 7.6,9.5" /><line class="areaIconSvg" stroke-dasharray="1.8796,1.8796" x1="5.8" y1="9.5" x2="2.9" y2="9.5"/><polyline class="areaIconSvg" points="2,9.5 0.5,9.5 0.5,8" /><line class="areaIconSvg" stroke-dasharray="1.9848,1.9848" x1="0.5" y1="6" x2="0.5" y2="3"/><polyline class="areaIconSvg" points="0.5,2 0.5,0.5 2,0.5" /><line class="areaIconSvg" stroke-dasharray="1.8796,1.8796" x1="3.9" y1="0.5" x2="6.7" y2="0.5"/><polyline class="areaIconSvg" points="7.6,0.5 9.1,0.5 9.1,2" /><line class="areaIconSvg" stroke-dasharray="1.9848,1.9848" x1="9.1" y1="4" x2="9.1" y2="7"/></svg><span class="textWhite">${ new Intl.NumberFormat("de-DE").format(parseFloat(features[0].properties.area).toFixed(3)) } km<span class="sup">2</span></span></article></section>`);

                  // Cuando se pasa el mouse por una provincia en el mapa, se pinta la provincia en el miniMapa.
                  var selector = '#' + data.generarReferenciaId(varGlobal.provincias['prov' + features[0].properties.prov_id].nombre);
                  var elemento = $(selector);

                  varGlobal.elementHover !== null ? $(varGlobal.elementHover).css({
                      'stroke-width': '0.5'
                  }) : ''; // Si es la primera vez, no hace nada, sino, borra el grosor de linea del anterior selector.
                  varGlobal.elementHover = selector; // Guarda una referencia del selector que se modifico para poder borrarlo en la siguiente iteración.
                  elemento.css({
                      'stroke-width': '2'
                  }); // Cambia el grosor de linea de la provincia seleccionada en el miniMapa.
                  $('#provs')[0].append(elemento[0]); // Cambia el orden de los svg para que se vean todos los bordes.
              } else {
                  varGlobal.datosHover.features[0] = '';
                  data.mapbox.getSource('Argentina-Hover').setData(varGlobal.datosHover);
                  data.mapbox.getCanvas().style.cursor = ''; // Defino tipo de cursor
                  $('#tooltip').hide();
                  varGlobal.elementHover !== null ? $(varGlobal.elementHover).css({
                      'stroke-width': '0.5'
                  }) : varGlobal.elementoHover = null;
              }
          });
          data.mapbox.on('mouseout', function(e) {
              varGlobal.datosHover.features[0] = '';
              data.mapbox.getSource('Argentina-Hover').setData(varGlobal.datosHover);
              data.mapbox.getCanvas().style.cursor = ''; // Defino tipo de cursor
              // $('#tooltip').hide();
              varGlobal.elementHover !== null ? $(varGlobal.elementHover).css({
                  'stroke-width': '0.5'
              }) : varGlobal.elementoHover = null;
          });
      }

      function funciones(datos, extras) {
          panelIzquierdo();
          miniMapa(datos, extras);
          changes(datos);

          function panelIzquierdo() {
              var panelState = 0;

              $("#button_pannel").click(function() {
                  // Panel Izquierdo Mostrar/Ocultar
                  if (panelState == 0) {
                      $("#container_panel").fadeOut("fast");
                      $("#button_pannel").css({
                          'border-radius': '0px 5px 5px 0px'
                      }).children().css({
                          '-webkit-transform': 'rotate(180deg)',
                          '-moz-transform': 'rotate(180deg)',
                          '-o-transform': 'rotate(180deg)',
                          '-ms-transform': 'rotate(180deg)',
                          'transform': 'rotate(180deg)'
                      });
                      $("#map > div.mapboxgl-control-container > div.mapboxgl-ctrl-top-left > div > input[type='text']").css({
                          'border-radius': '5px 0px 0px 5px'
                      });
                      panelState = 1;
                  } else {
                      $("#container_panel").fadeIn("fast");
                      $("#button_pannel").css({
                          'border-radius': '0px 5px 0px 0px'
                      }).children().css({
                          '-webkit-transform': 'rotate(0deg)',
                          '-moz-transform': 'rotate(0deg)',
                          '-o-transform': 'rotate(0deg)',
                          '-ms-transform': 'rotate(0deg)',
                          'transform': 'rotate(0deg)'
                      });
                      $("#map > div.mapboxgl-control-container > div.mapboxgl-ctrl-top-left > div > input[type='text']").css({
                          'border-radius': '5px 0px 0px 0px'
                      });
                      panelState = 0;
                  }
              });

              $('div.mapboxgl-ctrl-top-left input[type="text"]').attr('placeholder', 'Buscá una provincia'); // Se modifica placeholder de mapbox

              $('.mapboxgl-ctrl-attrib.mapboxgl-ctrl').hide();
          }

          function miniMapa(datos, extras) {
              function _loop() {
                  var elemento = $('#' + datos.generarReferenciaId(extras.provincias[provincia].nombre));
                  var coordenada = extras.provincias[provincia].coordenadas;

                  elemento.mouseover(function(e) {
                      $(this).css({
                          'stroke-width': '2'
                      });
                      $('#provs').append($(this)[0]);
                  });
                  elemento.mouseout(function(e) {
                      $(this).css({
                          'stroke-width': '0.5'
                      });
                  });
                  elemento.click(function(e) {
                      datos.mapbox.flyTo({
                          center: coordenada
                      });
                  });
              }

              for (var provincia in extras.provincias) {
                  _loop();
              }
          }

          function changes(datos) {

              function setCookie(cname, cvalue, exyears) {
                  var d = new Date();
                  d.setTime(d.getTime() + (exyears * 365 * 24 * 60 * 60 * 1000));
                  var expires = "expires=" + d.toUTCString();
                  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
              }

              function getCookie(cname) {
                  var name = cname + "=";
                  var ca = document.cookie.split(';');
                  for (var i = 0; i < ca.length; i++) {
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

              // Manejo de contenido de pestañas de Header_Mobile
              hashLocation(window.location.hash);

              $(window).on('hashchange', function() {
                  hashLocation(window.location.hash);
              });

              function hashLocation(hash) {

                  switch (hash) {
                      case "#inicio":
                          $("#explorar").hide();
                          $("#acercaDe").hide();
                          $(".accent_button").removeAttr("class");
                          $('.header_mobile').children().eq(0).removeAttr().attr("class", "accent_button");
                          if (getCookie("visited") != "true" || $("body").outerWidth() < 768) {
                              $(hash).attr("style", "display: flex;");
                          }
                          break;
                      case "#explorar":
                          $("#inicio").hide();
                          $("#acercaDe").hide();
                          $(".accent_button").removeAttr("class");
                          $('.header_mobile').children().eq(1).removeAttr().attr("class", "accent_button");
                          if ($("body").outerWidth() < 768) {
                              $(hash).attr("style", "display: flex;");
                          }
                          break;
                      case "#acercaDe":
                          $("#inicio").hide();
                          $("#explorar").hide();
                          $(".accent_button").removeAttr("class");
                          $('.header_mobile').children().eq(2).removeAttr().attr("class", "accent_button");
                          if ($("body").outerWidth() < 768) {
                              $(hash).attr("style", "display: flex;");
                          }
                          break;
                      default:
                          $("#explorar").hide();
                          $("#acercaDe").hide();
                          $(".accent_button").removeAttr("class");
                          $('.header_mobile').children().eq(0).removeAttr().attr("class", "accent_button");
                          if (getCookie("visited") != "true" || $("body").outerWidth() < 768) {
                              $("#inicio").attr("style", "display: flex;");
                          }
                  }
              }

              if ($("body").outerWidth() > 768) {
                  calcularPosicionPanelIzquierdo();
              } else {

              }

              var tempSize = true;

              $(window).resize(function() { // Manejo de cambios de tamaño de pantalla

                  if ($("body").outerWidth() >= 768) {

                      $('.header_map').show();

                      $(".header_mobile").children().first().click();

                      $("#inicio").hide();
                      $("#explorar").hide();
                      $("#acercaDe").hide();

                      calcularPosicionPanelIzquierdo();

                      tempSize = false;
                  } else {

                      if (tempSize == false) {
                          window.location.hash = "#explorar";
                      }

                      $('.header_map').hide();

                      // $("#map > div.mapboxgl-control-container > div.mapboxgl-ctrl-top-left").removeAttr('style');
                      $("#button_pannel").removeAttr('style');
                      $("#container_panel").removeAttr('style');

                      tempSize = true;
                  }
              });

              function calcularPosicionPanelIzquierdo() {
                  // Buscador, Boton, Panel
                  // $("#map > div.mapboxgl-control-container > div.mapboxgl-ctrl-top-left").css({
                  //     "padding-top": $("#header").outerHeight() + $("#productName").outerHeight() + 20
                  // });
                  $("#button_pannel").css({
                      "display": "flex",
                      "top": $("#header").outerHeight() + $("#productName").outerHeight() + 20
                  });
                  $("#container_panel").css({
                      "display": "block",
                      "top": $("#header").outerHeight() + $("#productName").outerHeight() + 19
                  });
              }

              setCookie("visited", "true", 5);
          }
      }
    }
});
