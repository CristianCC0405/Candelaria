<?php
include("../conexion.php");

// Filtro opcional
$categoria_general = $_GET['categoria_general'] ?? '';
$categoria_especifica = $_GET['categoria_especifica'] ?? '';

$query = "SELECT * FROM negocios WHERE 1";
if ($categoria_general != '') {
    $query .= " AND categoria_general = '" . $conexion->real_escape_string($categoria_general) . "'";
}
if ($categoria_especifica != '') {
    $query .= " AND categoria_especifica = '" . $conexion->real_escape_string($categoria_especifica) . "'";
}
$query .= " ORDER BY destacado DESC, fecha_publicacion DESC";

$resultado = $conexion->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Negocios publicados</title>
  <style>
    body { font-family: Arial; background: #f9f9f9; margin: 20px; }
    .contenedor { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px; }
    .tarjeta {
      background: white; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.1);
      overflow: hidden; transition: 0.3s; cursor: pointer;
    }
    .tarjeta:hover { transform: scale(1.02); }
    .tarjeta img { width: 100%; height: 180px; object-fit: cover; }
    .info { padding: 10px; }
    .nombre { font-weight: bold; font-size: 18px; }
    .categoria { font-size: 14px; color: #555; }
    .destacado { background: gold; padding: 3px 6px; border-radius: 5px; font-size: 12px; color: #000; }
  </style>
</head>
<body>

<h2>Negocios publicados</h2>

<div class="contenedor">
<?php
if ($resultado->num_rows > 0) {
    while ($negocio = $resultado->fetch_assoc()) {
        echo "<div class='tarjeta' onclick=\"window.location='ver_negocio.php?id={$negocio['id']}'\">";
        if ($negocio['destacado']) echo "<div class='destacado'>🌟 Popular</div>";
        echo "<img src='../../{$negocio['imagen_portada']}' alt='imagen'>";
        echo "<div class='info'>";
        echo "<div class='nombre'>{$negocio['nombre_negocio']}</div>";
        echo "<div class='categoria'>{$negocio['categoria_general']} → {$negocio['categoria_especifica']}</div>";
        echo "<div><small>{$negocio['lugar']}</small></div>";
        echo "</div></div>";
    }
} else {
    echo "<p>No hay negocios publicados aún.</p>";
}
?>
</div>

</body>
</html>
