<?php
include("../conexion.php");

$id = $_GET['id'] ?? 0;
$result = $conexion->query("SELECT * FROM negocios WHERE id=$id");
if ($result->num_rows == 0) die("Negocio no encontrado.");
$negocio = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?php echo $negocio['nombre_negocio']; ?></title>
  <style>
    body { font-family: Arial; background: #f9f9f9; padding: 20px; }
    .contenedor { max-width: 800px; margin: auto; background: white; border-radius: 10px; padding: 20px; }
    img { width: 100%; border-radius: 10px; margin-bottom: 10px; }
    h2 { margin-top: 0; }
    .contacto a { text-decoration: none; color: #0078D7; }
  </style>
</head>
<body>

<div class="contenedor">
  <h2><?php echo $negocio['nombre_negocio']; ?></h2>
  <p><strong>Categoría:</strong> <?php echo $negocio['categoria_general'] . " → " . $negocio['categoria_especifica']; ?></p>
  <p><strong>Lugar:</strong> <?php echo $negocio['lugar']; ?></p>
  <p><strong>Descripción:</strong><br><?php echo nl2br($negocio['descripcion']); ?></p>

  <div>
    <img src='../../<?php echo $negocio['imagen_portada']; ?>'>
    <?php
      for ($i=1; $i<=4; $i++) {
        $campo = "imagen_0$i";
        if ($negocio[$campo]) echo "<img src='../../{$negocio[$campo]}'>";
      }
    ?>
  </div>

  <div class="contacto">
    <p><strong>WhatsApp:</strong> <a href="https://wa.me/51<?php echo $negocio['whatsapp']; ?>" target="_blank"><?php echo $negocio['whatsapp']; ?></a></p>
    <?php if ($negocio['tiktok']) { ?>
      <p><strong>TikTok:</strong> <a href="<?php echo $negocio['tiktok']; ?>" target="_blank"><?php echo $negocio['tiktok']; ?></a></p>
    <?php } ?>
  </div>
</div>

</body>
</html>
