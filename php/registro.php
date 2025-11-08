<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Usuario</title>
  <style>
    body { font-family: Arial; margin: 30px; background: #f9f9f9; }
    form { background: white; padding: 20px; border-radius: 10px; width: 300px; }
    input { width: 100%; padding: 8px; margin: 6px 0; }
    button { background: #008CBA; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; }
  </style>
</head>
<body>

<h2>Crear cuenta</h2>
<form action="procesar_registro.php" method="POST" onsubmit="return validarFormulario()">
  <input type="text" name="nombre" placeholder="Nombre completo" required>
  <input type="email" name="email" placeholder="Correo electrónico" required>
  <input type="password" id="password" name="password" placeholder="Contraseña" required>
  <input type="password" id="confirmar" name="confirmar" placeholder="Confirme contraseña" required>
  <button type="submit">Registrarse</button>
</form>

<script>
function validarFormulario() {
  const pass = document.getElementById('password').value;
  const conf = document.getElementById('confirmar').value;
  const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/;

  if (!regex.test(pass)) {
    alert("La contraseña debe tener al menos una mayúscula, una minúscula, un número y 6 caracteres.");
    return false;
  }
  if (pass !== conf) {
    alert("Las contraseñas no coinciden.");
    return false;
  }
  return true;
}
</script>

</body>
</html>
