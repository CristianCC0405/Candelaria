<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión</title>
  <style>
    body { font-family: Arial; margin: 30px; background: #f9f9f9; }
    form { background: white; padding: 20px; border-radius: 10px; width: 300px; }
    input { width: 100%; padding: 8px; margin: 6px 0; }
    button { background: #4CAF50; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; }
  </style>
</head>
<body>

<h2>Iniciar Sesión</h2>
<form action="procesar_login.php" method="POST">
  <input type="email" name="email" placeholder="Correo electrónico" required>
  <input type="password" name="password" placeholder="Contraseña" required>
  <button type="submit">Entrar</button>
</form>

</body>
</html>
