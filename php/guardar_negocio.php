<?php
session_start();
include "../php/conexion.php";  // TU ARCHIVO DE CONEXIÓN

if (!isset($_SESSION["user_id"])) {
    die("Debes iniciar sesión.");
}

// Validaciones básicas
$nombre = $_POST["nombre"] ?? "";
$categoria = $_POST["categoriaGeneral"] ?? "";
$sub = $_POST["subcategoria"] ?? "";

if ($nombre == "" || $categoria == "" || $sub == "") {
    die("Faltan datos obligatorios.");
}

// =====================
//  GUARDAR IMAGEN
// =====================

if (!isset($_FILES["Imagen"])) {
    die("No subiste imagen.");
}

$img = $_FILES["Imagen"];
$maxSize = 10 * 1024 * 1024; // 10 MB

if ($img["size"] > $maxSize) {
    die("La imagen supera los 10MB.");
}

$ext = pathinfo($img["name"], PATHINFO_EXTENSION);
$ext = strtolower($ext);

if (!in_array($ext, ["jpg","jpeg","png","webp"])) {
    die("Formato no permitido.");
}

$nombreImg = "neg_" . uniqid() . "." . $ext;
$ruta = "../negocios/" . $nombreImg;

move_uploaded_file($img["tmp_name"], $ruta);

// =====================
//  GUARDAR EN LA BASE
// =====================

$stmt = $conexion->prepare("INSERT INTO negocios
(nombre, categoria, subcategoria, imagen, usuario_id)
VALUES (?, ?, ?, ?, ?)");

$stmt->bind_param("ssssi", $nombre, $categoria, $sub, $nombreImg, $_SESSION["user_id"]);

if ($stmt->execute()) {
    echo "OK";
} else {
    echo "Error: " . $conexion->error;
}

?>
