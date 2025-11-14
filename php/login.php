<?php
session_start();
require_once __DIR__.'/db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';

  if (!$email || !$pass) $errors[] = "Completa todos los campos.";

  if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($pass, $user['password_hash'])) {
      $errors[] = "Email o contraseña incorrectos.";
    } else {
      $_SESSION['user_id'] = $user['id'];
      header("Location: /php/dashboard.php");
      exit;
    }
  }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Iniciar sesión</title></head><body>
<h2>Iniciar sesión</h2>
<?php if (!empty($errors)): foreach ($errors as $e): ?><div style="color:red"><?=htmlspecialchars($e)?></div><?php endforeach; endif; ?>
<form method="post">
  <input name="email" placeholder="Correo electrónico" type="email" required><br>
  <input name="password" placeholder="Contraseña" type="password" required><br>
  <!-- Aquí puedes integrar reCAPTCHA si quieres -->
  <button type="submit">INICIAR SESIÓN</button>
</form>
<p><a href="/php/forgot_password.php">Olvidé mi contraseña</a></p>
<p><a href="/php/register.php">Crear cuenta</a></p>
</body></html>
