<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\geoPlugin;

class MapController extends Controller
{
  public function showMap($lon = null, $lat = null){

    $provs = collect(['prov02' => 'Ciudad Autónoma de Buenos Aires',
              'prov06' => 'Buenos Aires',
              'prov10' => 'Catamarca',
              'prov14' => 'Córdoba',
              'prov18' => 'Corrientes',
              'prov22' => 'Chaco',
              'prov26' => 'Chubut',
              'prov30' => 'Entre Ríos',
              'prov34' => 'Formosa',
              'prov38' => 'Jujuy',
              'prov42' => 'La Pampa',
              'prov46' => 'La Rioja',
              'prov50' => 'Mendoza',
              'prov54' => 'Misiones',
              'prov58' => 'Neuquén',
              'prov62' => 'Río Negro',
              'prov66' => 'Salta',
              'prov70' => 'San Juan',
              'prov74' => 'San Luis',
              'prov78' => 'Santa Cruz',
              'prov82' => 'Santa Fe',
              'prov86' => 'Santiago del Estero',
              'prov90' => 'Tucumán',
              'prov94' => 'Tierra del Fuego, Antártida e Islas del Atlántico Sur']);

    // Coordenadas
    if ($lon === null || $lat === null) {
      $geo = new geoPlugin();
      $geo->locate();
      $lat = $geo->latitude;
      $lon = $geo->longitude;

      if ($lat == 0 && $lon == 0) {
        $lat = -34.606031099999996;
        $lon = -58.47763559999999;
      }
    }

    $data = [
      'coordenadas' => [$lon, $lat],
      'provincias' => $provs
    ];

    return view('welcome', $data);
  }
}
