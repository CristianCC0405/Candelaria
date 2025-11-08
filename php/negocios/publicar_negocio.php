<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  die("⚠️ Debes iniciar sesión para publicar tu negocio. <a href='../login.php'>Inicia sesión aquí</a>.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Publicar negocio</title>
  <style>
    body { font-family: Arial; background: #f9f9f9; padding: 20px; }
    form { background: white; padding: 25px; border-radius: 10px; width: 400px; }
    label { font-weight: bold; display: block; margin-top: 10px; }
    input, select, textarea { width: 100%; padding: 8px; margin-top: 4px; }
    button { background: #0078D7; color: white; padding: 10px; border: none; border-radius: 5px; margin-top: 10px; cursor: pointer; }
  </style>
</head>
<body>

<h2>Publicar mi negocio</h2>

<form action="procesar_negocio.php" method="POST" enctype="multipart/form-data" onsubmit="return validarFormulario()">
  
  <label>Categoría principal:</label>
  <select id="categoria_general" name="categoria_general" required>
    <option value="">Selecciona...</option>
    <option value="YO BAILO">YO BAILO</option>
    <option value="YO VEO">YO VEO</option>
  </select>

  <label>Subcategoría:</label>
  <select id="categoria_especifica" name="categoria_especifica" required>
    <option value="">Selecciona una categoría</option>
  </select>

  <label>Nombre del negocio:</label>
  <input type="text" name="nombre_negocio" required>

  <label>Lugar:</label>
  <input type="text" name="lugar" required>

  <label>Descripción:</label>
  <textarea name="descripcion" rows="4"></textarea>

  <label>Link de TikTok:</label>
  <input type="url" name="tiktok">

  <label>Número de WhatsApp:</label>
  <input type="text" name="whatsapp" pattern="[0-9]{9}" title="Debe tener 9 dígitos sin espacios" required>

  <label>Imagen de portada:</label>
  <input type="file" name="imagen_portada" accept="image/*" required>

  <label>Imágenes adicionales (opcionales):</label>
  <input type="file" name="imagen_01" accept="image/*">
  <input type="file" name="imagen_02" accept="image/*">
  <input type="file" name="imagen_03" accept="image/*">
  <input type="file" name="imagen_04" accept="image/*">

  <button type="submit">Publicar negocio</button>
</form>

<script>
const categorias = {
  "YO BAILO": ["Disfraces", "Caretas", "Accesorios", "Estampados", "Fotografía", "Belleza", "Joyería", "Costura"],
  "YO VEO": ["Asientos", "Eventos", "Turismo", "Merchandising", "Bebidas", "Alimentación", "Transporte", "Alojamiento"]
};

const general = document.getElementById("categoria_general");
const especifica = document.getElementById("categoria_especifica");

general.addEventListener("change", () => {
  const seleccion = general.value;
  especifica.innerHTML = "<option value=''>Selecciona una categoría</option>";
  if (categorias[seleccion]) {
    categorias[seleccion].forEach(cat => {
      const opt = document.createElement("option");
      opt.value = cat;
      opt.textContent = cat;
      especifica.appendChild(opt);
    });
  }
});

function validarFormulario() {
  const telefono = document.querySelector("input[name='whatsapp']").value;
  if (!/^[0-9]{9}$/.test(telefono)) {
    alert("El número de WhatsApp debe tener exactamente 9 dígitos.");
    return false;
  }
  return true;
}
</script>

</body>
</html>
