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
    if ($stmt->fetch()) {
      $errors[] = "El email ya está registrado.";
    } else {
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
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>#CANDELARIA2026</title>

  <link rel="icon" href="../img/favicon.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/trajes.css">

  <style>
    .register-box {
      max-width: 400px;
      margin: 40px auto;
      padding: 20px;
      border: 2px solid #7f6000;
      border-radius: 16px;
      background: #fffaf0;
      text-align: center;
    }
    @media (max-width: 768px) {
      .register-box { margin: 20px; }
    }

    .register-box input {
      width: 90%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 10px;
      border: 1px solid #bbb;
      font-size: 16px;
    }

    .register-box button {
      padding: 12px 25px;
      background: #7f6000;
      color: white;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-size: 18px;
      font-weight: bold;
      margin-top: 10px;
    }
    .register-box button:hover {
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

  <!-- HEADER -->
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

  <main>
    <section id="trajes">
      <h1>CREAR<br>CUENTA</h1>
      <p style="font-weight: lighter;">Regístrate para publicar o editar tus negocios.</p>
    </section>

    <div class="register-box">

      <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $e): ?>
          <div style="color:red; margin-bottom:10px;"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
      <?php endif; ?>

      <form method="post">
        <input name="nombres" placeholder="Nombres" value="<?=htmlspecialchars($_POST['nombres'] ?? '')?>" required>
        <input name="apellidos" placeholder="Apellidos" value="<?=htmlspecialchars($_POST['apellidos'] ?? '')?>" required>
        <input name="email" placeholder="Correo electrónico" type="email" value="<?=htmlspecialchars($_POST['email'] ?? '')?>" required>
        <input name="password" placeholder="Contraseña" type="password" required>
        <input name="password2" placeholder="Repetir contraseña" type="password" required>
        <button type="submit">CREAR CUENTA</button>
      </form>

      <div class="login-links" style="margin-top: 15px;">
        <p><a href="/php/login.php">¿Ya tienes una cuenta? Iniciar sesión</a></p>
      </div>
    </div>
  </main>

  <footer>
    <p>#CANDELARIA2026</p>
    <p>bailaencandelaria@gmail.com</p>
  </footer>

  <script src="../js/script.js"></script>

</body>
</html>
