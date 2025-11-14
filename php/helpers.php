<?php
function capitalizar($s) {
  $s = trim(mb_strtolower($s));
  // capitaliza primera letra de cada palabra
  return mb_convert_case($s, MB_CASE_TITLE, "UTF-8");
}

function validar_whatsapp($s) {
  return preg_match('/^[0-9]{9}$/', $s);
}

function validar_precio($s) {
  return preg_match('/^[0-9]{2,3}$/', $s);
}

// retorna siguiente nombre de imagen para una carpeta y prefijo
function siguiente_nombre_img($carpeta, $prefijo = 'img') {
  if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);
  $files = scandir($carpeta);
  $max = 0;
  foreach ($files as $f) {
    if (preg_match('/' . preg_quote($prefijo) . '(\d+)\./', $f, $m)) {
      $n = intval($m[1]);
      if ($n > $max) $max = $n;
    }
  }
  return $prefijo . ($max + 1) . '.jpg'; // guardamos jpg por defecto
}
