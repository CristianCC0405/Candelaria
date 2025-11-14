<?php
session_start();
require_once __DIR__.'/db.php';
require_once __DIR__.'/helpers.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombres = trim($_POST['nombres'] ?? '');
  $apellidos = trim($_POST['apellidos'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';
  $pass2 = $_POST['password2'] ?? '';

  if (!$nombres || !$apellidos || !$email || !$pass) $errors[] = "Completa todos los campos.";
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido.";
  if ($pass !== $pass2) $errors[] = "Las contraseñas no coinciden.";
  if (strlen($pass) < 6) $errors[] = "La contraseña debe tener al menos 6 caracteres.";

  if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) $errors[] = "El email ya está registrado.";
    else {
      $hash = password_hash($pass, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("INSERT INTO users (nombres, apellidos, email, password_hash) VALUES (?, ?, ?, ?)");
      $stmt->execute([capitalizar($nombres), capitalizar($apellidos), $email, $hash]);
      $_SESSION['user_id'] = $pdo->lastInsertId();
      header("Location: /php/dashboard.php");
      exit;
    }
  }
}
?>
<!-- simple HTML del formulario -->
<!doctype html>
<html><head><meta charset="utf-8"><title>Crear cuenta</title></head><body>
<h2>Crear cuenta</h2>
<?php if (!empty($errors)): foreach ($errors as $e): ?><div style="color:red"><?=htmlspecialchars($e)?></div><?php endforeach; endif; ?>
<form method="post" action="">
  <input name="nombres" placeholder="Nombres" value="<?=htmlspecialchars($_POST['nombres'] ?? '')?>" required><br>
  <input name="apellidos" placeholder="Apellidos" value="<?=htmlspecialchars($_POST['apellidos'] ?? '')?>" required><br>
  <input name="email" placeholder="Correo electrónico" type="email" value="<?=htmlspecialchars($_POST['email'] ?? '')?>" required><br>
  <input name="password" placeholder="Contraseña" type="password" required><br>
  <input name="password2" placeholder="Repetir contraseña" type="password" required><br>
  <button type="submit">CREAR CUENTA</button>
</form>
</body></html>
