<?php
include("conexion.php");

$nombre = $_POST['nombre'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirmar = $_POST['confirmar'];

if ($password !== $confirmar) {
    die("Las contraseñas no coinciden.");
}

// Validación de seguridad
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/', $password)) {
    die("La contraseña no cumple los requisitos de seguridad.");
}

// Encriptar contraseña
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Insertar usuario
$stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nombre, $email, $passwordHash);

if ($stmt->execute()) {
    echo "<script>alert('Usuario registrado correctamente.'); window.location='login.php';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>
