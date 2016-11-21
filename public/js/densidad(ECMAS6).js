window.onload = () => {

  const app = () => {
    let data = {
      coordenadas: [{{$coordenadas[0]}}, {{$coordenadas[1]}}],
      generarReferenciaId: (string) => {
        let texto = string.toLowerCase();
        let reemplazar = [
          ["á", "a"],
          ["é", "e"],
          ["í", "i"],
          ["ó", "o"],
          ["ú", "u"],
          ["ñ", "n"],
          [" ", ""]
        ];

        for (var i = 0; i < reemplazar.length; i++) {
          while (texto.indexOf(reemplazar[i][0]) != -1) {
            texto = texto.replace(reemplazar[i][0], reemplazar[i][1]);
          }
        }

        return texto;
      }
    };
    let varGlobal = {
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

    data = datos(data);
    data = dataset(data);
    console.log(data); // Entorno de desarrollo.
    console.log(varGlobal); // Entorno de desarrollo.
    funciones(data, varGlobal);
    render(data, varGlobal);

    const datos = (datos) => {
      mapboxgl.accessToken = 'pk.eyJ1IjoiZnJhbWxvcGV6IiwiYSI6ImNpdWhrYWdvNjAwdjYzcHFmaDl1YTQyOTYifQ.g62pBFWJnDt8vIiHQ5HM8A'; // Token MapBox
      datos.mapbox = new mapboxgl.Map({
        container   : 'map',
        style       : 'mapbox://styles/framlopez/civjo1vi600582iqoly4paool', // Style MapBox
        center      : datos.coordenadas,
        zoom        : 9.5, // Definimos el ZOOM inicial
        pitch       : 60,
        bearing     : 0,
        maxBounds   : [[-80, -60], [-50, -20]] // Sets bounds as max
      });
      datos.mapbox.addControl(new mapboxgl.NavigationControl({position: 'bottom-right'})); // Control de ZOOM
      datos.mapbox.addControl(new mapboxgl.ScaleControl({ position: 'bottom-left', maxWidth: 80, unit: 'metric' })); // Control de escala
      datos.mapbox.addControl(new mapboxgl.Geocoder()); // Control Search

      return datos;
    }
    const dataset = (datos) => {
      $.get('dataset/datos.geojson', function(file) { // Preparar datos
        datos.dataset = JSON.parse(file);
      });

      return datos;
    }
    const funciones = (datos, extras) => {
      panelIzquierdo();
      miniMapa(datos, extras);

      const panelIzquierdo = () => {
        let panelState = 0;

        $("#panel_show").click(() => { // Panel Izquierdo Mostrar/Ocultar
          if (panelState === 0) {
            $("#container_panel").fadeOut("fast");
            $("#panel_show").css({ 'border-radius': '0px 5px 5px 0px' }).empty().append(">");
            $("#map > div.mapboxgl-control-container > div.mapboxgl-ctrl-top-left > div > input[type='text']").css({ 'border-radius': '5px 0px 0px 5px' });
            panelState = 1;
          } else {
            $("#container_panel").fadeIn("fast");
            $("#panel_show").css({ 'border-radius': '0px 5px 0px 0px' }).empty().append("<");
            $("#map > div.mapboxgl-control-container > div.mapboxgl-ctrl-top-left > div > input[type='text']").css({ 'border-radius': '5px 0px 0px 0px' });
            panelState = 0;
          }
        });
        $('div.mapboxgl-ctrl-top-left input[type="text"]').attr('placeholder', 'Buscá una provincia'); // Se modifica placeholder de mapbox
      }
      const miniMapa = (datos, extras) => {
        for (let provincia in extras.provincias) {
          let elemento = $('#' + datos.generarReferenciaId(extras.provincias[provincia].nombre));
          let coordenada = extras.provincias[provincia].coordenadas;

          elemento.mouseover((e) => {
            $(this).css({ 'stroke-width': '2' });
            $('#provs')[0].append($(this)[0]);
          });
          elemento.mouseout((e) => {
            $(this).css({ 'stroke-width': '0.5' });
          });
          elemento.click((e) => {
            datos.mapbox.flyTo({ center: coordenada });
          });
        }
      }
    }
    const render = (datos, extras) => {
      datos.mapbox.on('load', function (d) { // Cargamos datos
        datos.mapbox.addSource("Argentina", { // Tomamos datos de dataset local
          'type': 'geojson',
          'data': datos.dataset
        });
        datos.mapbox.addSource("Argentina-Hover", {
          'type': 'geojson',
          'data': extras.datosHover
        });
        datos.mapbox.addLayer({ // Agregamos capa de extrusion
          'id': 'extrusion',
          'source': 'Argentina',
          'type': 'fill',
          'paint': {
            'fill-color': {
              'property': 'color',
              'type': 'identity'
            },
            'fill-extrude-height': {
              'property': 'densidad',
              'type': 'identity'
            },
            'fill-opacity': 1
          }
        }, 'country-label-sm');
        datos.mapbox.addLayer({
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
      datos.mapbox.on('mousemove', function (e) {
        let features = datos.mapbox.queryRenderedFeatures(e.point, { layers: ['extrusion'] });

        if (features.length) { // Si hay datos
          extras.datosHover.features[0] = features[0];
          datos.mapbox.getSource('Argentina-Hover').setData(extras.datosHover);
          datos.mapbox.getCanvas().style.cursor = ''; // Defino tipo de cursor
          $('#tooltip').css({ "transform": "translate(" + (e.point.x + 10) + "px, " + (e.point.y + 140) + "px)"; "display": "flex" });
          $('#contentTooltip').empty().append(
            `<section class="tooltip_container"><article>Provincia: ${ extras.provincias['prov' + features[0].properties.prov_id].nombre }</article><article class="fontWhite"><svg x="0px" y="0px" class="peopleIcon" viewBox="0 0 13.8 12.2"><path fill="white" d="M13.8,12.2c-4.6,0-9.1,0-13.7,0c0-0.4-0.1-0.8-0.1-1.1c0-1.2,0.5-1.9,1.7-2.3c0.9-0.3,1.8-0.5,2.7-0.8c0.3-0.1,0.6-0.3,0.9-0.5c0.2-0.2,0.3-0.4,0-0.7C4.4,5.7,3.9,4.4,3.9,3C4,1.3,5.5-0.1,7,0C8.6,0.1,10,1.7,9.9,3.4C9.8,4.7,9.3,5.8,8.3,6.8C8.1,7,8.1,7.3,8.4,7.5c0.3,0.2,0.6,0.4,0.9,0.5c0.9,0.3,1.8,0.5,2.7,0.8c1.4,0.4,2,1.3,1.9,2.8C13.8,11.8,13.8,12,13.8,12.2z"/></svg> ${parseFloat(features[0].properties.poblacion).toFixed(0)}<span class="fontSilver">&nbsp;(${ parseFloat(features[0].properties.densidad).toFixed(2) }) / km<sup>2</sup></span></article><article class="fontWhite"><svg x="0px" y="0px" class="areaIcon" viewBox="0 0 9.6 10"><g><polyline fill="none" stroke="#FFFFFF" stroke-miterlimit="10" points="9.1,8 9.1,9.5 7.6,9.5" /><line fill="none" stroke="#FFFFFF" stroke-miterlimit="10" stroke-dasharray="1.8796,1.8796" x1="5.8" y1="9.5" x2="2.9" y2="9.5"/><polyline fill="none" stroke="#FFFFFF" stroke-miterlimit="10" points="2,9.5 0.5,9.5 0.5,8" /><line fill="none" stroke="#FFFFFF" stroke-miterlimit="10" stroke-dasharray="1.9848,1.9848" x1="0.5" y1="6" x2="0.5" y2="3"/><polyline fill="none" stroke="#FFFFFF" stroke-miterlimit="10" points="0.5,2 0.5,0.5 2,0.5" /><line fill="none" stroke="#FFFFFF" stroke-miterlimit="10" stroke-dasharray="1.8796,1.8796" x1="3.9" y1="0.5" x2="6.7" y2="0.5"/><polyline fill="none" stroke="#FFFFFF" stroke-miterlimit="10" points="7.6,0.5 9.1,0.5 9.1,2" /><line fill="none" stroke="#FFFFFF" stroke-miterlimit="10" stroke-dasharray="1.9848,1.9848" x1="9.1" y1="4" x2="9.1" y2="7"/></g></svg>${ parseFloat(features[0].properties.area).toFixed(2) }km<sup>2</sup></article></section>`
          );

          // Cuando se pasa el mouse por una provincia en el mapa, se pinta la provincia en el miniMapa.
          let selector = '#' + datos.generarReferenciaId(extras.provincias['prov' + features[0].properties.prov_id].nombre);
          let elemento = $(selector);

          (extras.elementHover !== null) ? $(extras.elementHover).css({ 'stroke-width': '0.5' }) : ''; // Si es la primera vez, no hace nada, sino, borra el grosor de linea del anterior selector.
          extras.elementHover = selector; // Guarda una referencia del selector que se modifico para poder borrarlo en la siguiente iteración.
          elemento.css({ 'stroke-width': '2' }); // Cambia el grosor de linea de la provincia seleccionada en el miniMapa.
          $('#provs')[0].append(elemento[0]); // Cambia el orden de los svg para que se vean todos los bordes.
        } else {
          if (extras.elementHover !== null) {
            $(extras.elementHover).css({ 'stroke-width': '0.5' });
            extras.elementoHover = null;
          }
          $('#tooltip').hide();
          datos.mapbox.getCanvas().style.cursor = ''; // Defino tipo de cursor
          extras.datosHover.features[0] = '';
          datos.mapbox.getSource('Argentina-Hover').setData(extras.datosHover);
        }
      });
      // map.setLayerZoomRange('my-layer', 2, 5);
    }
  }

  app();
}
