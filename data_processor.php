<?php

ini_set('memory_limit', '8192M');
set_time_limit(0);

// File
$location = "dataset/";
$ext = ".geojson";
$file =  $location . "pais" . $ext;

// Settings
$id = "cartodb_id";
$colores = ['#fbe9e7','#ffcfc0','#ffb499','#ff9772','#ff764a','#fd4f1b','#df4317','#c13714','#a62b10','#8a200d','#6f1508','#560a00']; // oscuro a claro

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
  if ($value->properties->densidad == null || $value->properties->densidad == "" || $value->properties->densidad < 0) {
    $value->properties->densidad = 0;
  }

  // Escala Log
  $value->properties->densidad = (sqrt($value->properties->densidad)) * 25;

  // Validando datos densidad
  if ($value->properties->densidad == null || $value->properties->densidad == "" || $value->properties->densidad < 0) {
    $value->properties->densidad = 0;
  }

  // Redondeando valores
  // $value->properties->densidad = round($value->properties->densidad, 0);
  // $value->properties->area = round(intval($value->properties->area), 0);
  // $value->properties->poblacion = round(intval($value->properties->poblacion), 0);
  $value->properties->densidad = intval($value->properties->densidad);
  $value->properties->area = intval($value->properties->area);
  $value->properties->poblacion = intval($value->properties->poblacion);

  // Se ajusta escala para generar picos
  // $value->properties->densidad = $value->properties->densidad * 1000;

  // Calculando valor maximo
  if ($max < $value->properties->poblacion) {
    $max = $value->properties->poblacion;
  }

  // Sumar Total
  $total = $total + $value->properties->densidad;
}

echo "VALOR MAXIMO<br /><br />";
echo $max;
echo "<br /><br />";

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
    $value->properties->color = '#fbe9e7';
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
  unset($value->properties->hogares);
  unset($value->properties->log_densid);
  unset($value->properties->dpto_id);
  unset($value->properties->frac_id);
}


echo "<br />Guardando datos ... <br />";
$newFile =  $location . "datos" . $ext;

$data = json_encode($data);
$fp = fopen($newFile, 'w+');
fwrite($fp, $data);
fclose($fp);
?>
