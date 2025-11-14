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
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Iniciar Sesión - #CANDELARIA2026</title>

  <link rel="icon" href="../img/favicon.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/disfraces.css"> <!-- para mantener coherencia -->
  
  <style>
    /* Ajustes visuales del formulario (igual estilo del sitio) */
    .login-box {
      max-width: 400px;
      margin: 40px auto;
      padding: 20px;
      border: 2px solid #7f6000;
      border-radius: 16px;
      background: #fffaf0;
      text-align: center;
    }
    .login-box h2 {
      color: #7f6000;
      margin-bottom: 15px;
      font-size: 28px;
    }
    .login-box input {
      width: 90%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 10px;
      border: 1px solid #bbb;
      font-size: 16px;
    }
    .login-box button {
      padding: 12px 25px;
      background: #7f6000;
      color: white;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-size: 17px;
      margin-top: 10px;
    }
    .login-box button:hover {
      background: #a18400;
    }
    .login-links a {
      color: #7f6000;
      text-decoration: none;
      font-size: 15px;
    }
  </style>
</head>

<body>

  <!-- === HEADER IGUAL A LAS DEMÁS PÁGINAS === -->
  <header>
    <a href="../index.html" class="logo">
      <img src="../img/logo.png" alt="logo">
      <span>#CANDELARIA2026</span>
    </a>

    <div class="menu-icon" onclick="toggleMenu()">☰</div>

    <nav>
      <a href="../index.html">Inicio</a>
      <a href="../danzas.html">Danzas</a>
      <a href="../orden.html">Orden</a>
      <a class="separador">|</a>
      <a href="../yo-bailo.html">Yo bailo</a>
      <a href="../yo-veo.html">Yo veo</a>
      <a class="separador">|</a>
      <a href="https://www.tiktok.com/@bailaencandelaria" target="_blank">
        <img src="../img/tiktok.png" alt="TikTok" class="tiktok-icon">
      </a>
    </nav>
  </header>

  <!-- === CONTENIDO CENTRAL === -->
  <main>
    <section id="disfraces">
      <h1>INICIAR SESIÓN</h1>
      <p>Accede para publicar o editar tus negocios.</p>
    </section>

    <div class="login-box">
      
      <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $e): ?>
          <div style="color:red; margin-bottom:10px;"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
      <?php endif; ?>

      <form method="post">
        <input name="email" placeholder="Correo electrónico" type="email" required>
        <input name="password" placeholder="Contraseña" type="password" required>
        <button type="submit">INICIAR SESIÓN</button>
      </form>

      <div class="login-links" style="margin-top: 15px;">
        <p><a href="/php/forgot_password.php">¿Olvidaste tu contraseña?</a></p>
        <p><a href="/php/register.php">Crear una cuenta nueva</a></p>
      </div>
    </div>

  </main>

  <!-- === FOOTER IGUAL A TODO EL SITIO === -->
  <footer>
    <p>#CANDELARIA2026</p>
    <p>bailaencandelaria@gmail.com</p>
  </footer>

  <script src="../js/script.js"></script>

</body>
</html>
