<?php

ini_set('memory_limit', '8192M');
set_time_limit(0);

// File
$location = "dataset/";
$ext = ".geojson";
$file =  $location . "pais" . $ext;

// Settings
$id = "cartodb_id";
// $colores = ['#14162b','#1b253b','#22374c','#28485e','#2f5a70','#356c83','#3a7f96','#3f93a9','#45a7bd','#4abcd2','#4ed0e7','#53e6fc']; // Azul
$colores = ['#efefea','#fdd2bf','#ffb698','#ff9973','#ff794a','#ff501b','#e93603','#ca2d05','#aa2407','#8d1b07','#721305','#560a00']; // Bordo - Rojo

$min = 0;
$max = 0;
$total = 0;

// Read File
$read = file_get_contents($file);
$data = json_decode($read);

echo "<pre>";
echo "Calculando Cantidad de Datos = ", count($data->features), "<br /><br />";

echo "1) Verificando Datos Densidad <br /><br />";
foreach ($data->features as $key => $value) {
  // Validando datos densidad
  if ($value->properties->densidad == null || $value->properties->densidad == "") {
    // echo "ERROR (" . $key . ") - Formato invalido";
    // echo "<br />";
    // echo "El dato original es: ";
    // var_dump($value->properties->densidad);
    // echo "El dato modificado es: ";
    $value->properties->densidad = 0;
    // var_dump($value->properties->densidad);
    // echo "<br />";
  } else { // Redondeando valores
    $value->properties->densidad = round($value->properties->densidad, 2);
  }

  // Validando datos densidad logaritmica
  if ($value->properties->densidad == null || $value->properties->densidad == ""|| $value->properties->densidad < 0) {
    // echo "ERROR (" . $key . ") - Formato invalido";
    // echo "<br />";
    // echo "El dato original es: ";
    // var_dump($value->properties->densidad);
    // echo "El dato modificado es: ";
    $value->properties->densidad = 0;
    // var_dump($value->properties->densidad);
    // echo "<br />";
  } else { // Redondeando valores
    $value->properties->densidad = round($value->properties->densidad, 2);
  }

  // Se ajusta escala para generar picos
  // $value->properties->densidad = $value->properties->densidad * 1000;

  // Escala Log
  $value->properties->densidad = (sqrt($value->properties->densidad)) * 25;

  // Calculando valor maximo
  if ($max < $value->properties->densidad) {
    $max = $value->properties->densidad;
  }

  // Sumar Total
  $total = $total + $value->properties->densidad;
}
echo "Calculando Cantidad de Datos = ", count($data->features), "<br /><br />";

echo "2) Generando escala de color <br /><br />";
$arraySort = arrayAsociativoOrdenado($data->features, $id);
$arrayBackUp = $data->features;
$arraySortData = [];
$contadorDatos = 0;

foreach ($arraySort as $keySort => $valueSort) {
  if ($valueSort > 99.99) {
    $arraySortData[] =  $arrayBackUp[$keySort];
    $contadorDatos++;
  }
}

$data->features = $arraySortData;

$iteracion = $total / count($colores);
$contadorColor = 0;

foreach ($data->features as $key => $value) {
  $contadorColor = $contadorColor + $value->properties->densidad;

  if ($contadorColor < $iteracion) {
    $value->properties->color = $colores[0];
  } else if ($contadorColor < ($iteracion * 2)) {
    $value->properties->color = $colores[1];
  } else if ($contadorColor < ($iteracion * 3)) {
    $value->properties->color = $colores[2];
  } else if ($contadorColor < ($iteracion * 4)) {
    $value->properties->color = $colores[3];
  } else if ($contadorColor < ($iteracion * 5)) {
    $value->properties->color = $colores[4];
  } else if ($contadorColor < ($iteracion * 6)) {
    $value->properties->color = $colores[5];
  } else if ($contadorColor < ($iteracion * 7)) {
    $value->properties->color = $colores[6];
  } else if ($contadorColor < ($iteracion * 8)) {
    $value->properties->color = $colores[7];
  } else if ($contadorColor < ($iteracion * 9)) {
    $value->properties->color = $colores[8];
  } else if ($contadorColor < ($iteracion * 10)) {
    $value->properties->color = $colores[9];
  } else if ($contadorColor < ($iteracion * 11)) {
    $value->properties->color = $colores[10];
  } else if ($contadorColor < ($iteracion * 12)) {
    $value->properties->color = $colores[11];
  }
}

foreach ($data->features as $key => $value) {
  if ($value->properties->color == null) {
    $value->properties->color = 'white';
  }
}
echo "Calculando Cantidad de Datos = ", count($data->features), "<br /><br />";

// Funciones
function arrayAsociativoOrdenado($data, $id){
  $array = [];
  foreach ($data as $key => $value) {
    $array[$key] = $value->properties->densidad;
  }
  asort($array);
  return $array;
}

// echo "<br />Reduciendo tamaño del archivo ... <br />";
foreach ($data->features as $key => $value) {
  unset($value->properties->viviendas);
  unset($value->properties->radio_id);
  // unset($value->properties->prov_id);
  unset($value->properties->hogares);
  // unset($value->properties->dpto_id);
  unset($value->properties->log_densi);
}


echo "<br />Guardando datos ... <br />";
$newFile =  $location . "datos" . $ext;

$data = json_encode($data);
$fp = fopen($newFile, 'w+');
fwrite($fp, $data);
fclose($fp);
?>
