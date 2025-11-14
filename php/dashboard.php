<?php
session_start();
require_once __DIR__.'/db.php';
if (empty($_SESSION['user_id'])) {
  header("Location: /php/login.php");
  exit;
}
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Mi cuenta</title></head><body>
<h2>Bienvenido, <?=htmlspecialchars($user['nombres'])?></h2>
<p><a href="/php/agregar_negocio.php">Agregar Negocio</a> | <a href="/php/mis_negocios.php">Mis anuncios</a> | <a href="/php/logout.php">Cerrar sesión</a></p>
</body></html>
