<?php
session_start();
include("conexion.php");

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conexion->prepare("SELECT id, nombre, password FROM usuarios WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $nombre, $hash);
    $stmt->fetch();

    if (password_verify($password, $hash)) {
        $_SESSION['usuario_id'] = $id;
        $_SESSION['usuario_nombre'] = $nombre;
        echo "<script>alert('Bienvenido, $nombre'); window.location='../index.html';</script>";
    } else {
        echo "Contraseña incorrecta.";
    }
} else {
    echo "No existe una cuenta con ese correo.";
}

$stmt->close();
$conexion->close();
?>
