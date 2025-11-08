<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['usuario_id'])) {
  die("No tienes permiso para publicar.");
}

$usuario_id = $_SESSION['usuario_id'];
$categoria_general = $_POST['categoria_general'];
$categoria_especifica = $_POST['categoria_especifica'];
$nombre_negocio = $_POST['nombre_negocio'];
$lugar = $_POST['lugar'];
$descripcion = $_POST['descripcion'];
$tiktok = $_POST['tiktok'];
$whatsapp = $_POST['whatsapp'];

// Carpeta donde se guardarán las imágenes
$carpeta = "../../uploads/negocios/";
if (!file_exists($carpeta)) mkdir($carpeta, 0777, true);

// Función para subir imágenes
function subirImagen($campo) {
  global $carpeta;
  if (!empty($_FILES[$campo]['name'])) {
    $nombreArchivo = time() . "_" . basename($_FILES[$campo]["name"]);
    $ruta = $carpeta . $nombreArchivo;
    if (move_uploaded_file($_FILES[$campo]["tmp_name"], $ruta)) {
      return "uploads/negocios/" . $nombreArchivo;
    }
  }
  return NULL;
}

$imagen_portada = subirImagen('imagen_portada');
$imagen_01 = subirImagen('imagen_01');
$imagen_02 = subirImagen('imagen_02');
$imagen_03 = subirImagen('imagen_03');
$imagen_04 = subirImagen('imagen_04');

// Insertar en la BD
$stmt = $conexion->prepare("
  INSERT INTO negocios (usuario_id, categoria_general, categoria_especifica, nombre_negocio, lugar, descripcion, tiktok, whatsapp, imagen_portada, imagen_01, imagen_02, imagen_03, imagen_04)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("issssssssssss", $usuario_id, $categoria_general, $categoria_especifica, $nombre_negocio, $lugar, $descripcion, $tiktok, $whatsapp, $imagen_portada, $imagen_01, $imagen_02, $imagen_03, $imagen_04);

if ($stmt->execute()) {
  echo "<script>alert('Negocio publicado correctamente'); window.location='publicar_negocio.php';</script>";
} else {
  echo "Error al guardar: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>
