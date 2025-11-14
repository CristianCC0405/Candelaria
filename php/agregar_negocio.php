<?php
session_start();
require_once __DIR__.'/db.php';
require_once __DIR__.'/helpers.php';

if (empty($_SESSION['user_id'])) {
  header("Location: /php/login.php");
  exit;
}
$user_id = $_SESSION['user_id'];

$grupos = ['Yo Baila'=>'Yo Baila','Yo Veo'=>'Yo Veo'];
$categorias = [
  'Yo Baila' => ['Disfraces','Otros','Bordados','Accesorios','Fotografia','Belleza','Joyeria','Costura'],
  'Yo Veo'   => ['Asientos','Eventos','Turismo','Merchandising','Bebidas','Alimentacion','Transporte','Alojamiento']
];

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $titulo = trim($_POST['titulo'] ?? '');
  $grupo = $_POST['grupo'] ?? '';
  $categoria = $_POST['categoria'] ?? '';

  if (!$titulo || !$grupo || !$categoria) $errors[] = "Completa título, grupo y categoría.";
  if (mb_strlen($titulo) > 100) $errors[] = "Título demasiado largo.";

  // Recoger campos según categoría
  $campos = [];
  // todos los campos que quieras recibir vienen en POST con nombres predecibles
  // ej: ciudad,direccion,whatsapp,tiktok,instagram,ubicacion,desde,hasta,de,a,vehiculo...
  foreach ($_POST as $k=>$v) {
    if (in_array($k, ['titulo','grupo','categoria'])) continue;
    $campos[$k] = trim($v);
  }

  // Validaciones específicas
  if (isset($campos['whatsapp']) && !validar_whatsapp($campos['whatsapp'])) $errors[] = "WhatsApp debe tener 9 dígitos sin espacios.";
  if (isset($campos['desde']) && !validar_precio($campos['desde'])) $errors[] = "Precio Desde inválido.";
  if (isset($campos['hasta']) && !validar_precio($campos['hasta'])) $errors[] = "Precio Hasta inválido.";

  // Limitar longitud y capitalizar campos de texto
  foreach ($campos as $k=>$v) {
    $v = mb_substr($v,0,25);
    // capitalizar palabras salvo los enlaces y números y campos tipo whatsapp
    if (!in_array($k, ['whatsapp','tiktok','instagram','desde','hasta','de','a'])) {
      $v = capitalizar($v);
    }
    $campos[$k] = $v;
  }

  // Manejo de imagen subida
  $image_path = null;
  if (!empty($_FILES['imagen']['name'])) {
    $categoria_folder = strtolower(str_replace(' ', '', $categoria)); // ej: Disfraces -> disfraces
    $carpeta = __DIR__ . "/../img/{$categoria_folder}/";
    $nombre_archivo = siguiente_nombre_img($carpeta, 'img');
    $dest = $carpeta . $nombre_archivo;

    // Validar tipo y tamaño (por ejemplo hasta 3MB)
    $allowed = ['image/jpeg','image/jpg','image/png'];
    if (!in_array($_FILES['imagen']['type'], $allowed)) $errors[] = "Formato de imagen no permitido.";
    if ($_FILES['imagen']['size'] > 3 * 1024 * 1024) $errors[] = "Imagen demasiado grande (máx 3MB).";

    if (empty($errors)) {
      // convertir a jpg si viene png? aquí movemos tal cual (mejor guardar extension original)
      move_uploaded_file($_FILES['imagen']['tmp_name'], $dest);
      // ruta relativa para DB
      $image_path = "img/{$categoria_folder}/{$nombre_archivo}";
    }
  }

  if (empty($errors)) {
    $stmt = $pdo->prepare("INSERT INTO negocios (user_id, titulo, grupo, categoria, campos_json, imagen) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, capitalizar($titulo), $grupo, $categoria, json_encode($campos, JSON_UNESCAPED_UNICODE), $image_path]);
    $success = true;
  }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Agregar negocio</title></head><body>
<h2>Agregar negocio</h2>
<?php if ($success): ?>
  <div style="background:#def;border:1px solid #9ab;padding:10px;position:relative;">
    TU ANUNCIO HA SIDO PUBLICADO EN UN ORDEN ALEATORIO.
    SI QUIERES QUE SIEMPRE APAREZCA EN PRIMER ORDEN ESCRIBE A: <strong>bailaencandelaria@gmail.com</strong>
    <button onclick="this.parentElement.style.display='none'" style="float:right">X</button>
  </div>
<?php endif; ?>
<?php if (!empty($errors)): foreach ($errors as $e): ?><div style="color:red"><?=htmlspecialchars($e)?></div><?php endforeach; endif; ?>

<form method="post" enctype="multipart/form-data">
  <input name="titulo" placeholder="Título" value="<?=htmlspecialchars($_POST['titulo'] ?? '')?>" required><br>

  <label>Grupo</label>
  <select name="grupo" id="grupo" required>
    <option value="">--</option>
    <?php foreach ($grupos as $k=>$v): $sel = (($_POST['grupo'] ?? '')==$k)?'selected':''; ?>
      <option value="<?=htmlspecialchars($k)?>" <?=$sel?>><?=htmlspecialchars($v)?></option>
    <?php endforeach;?>
  </select><br>

  <label>Categoría</label>
  <select name="categoria" id="categoria" required>
    <option value="">--</option>
    <?php
      $gsel = $_POST['grupo'] ?? '';
      if ($gsel && isset($categorias[$gsel])):
        foreach ($categorias[$gsel] as $cat):
          $sel = (($_POST['categoria'] ?? '')==$cat)?'selected':'';
          echo "<option value=\"".htmlspecialchars($cat)."\" $sel>".htmlspecialchars($cat)."</option>";
        endforeach;
      endif;
    ?>
  </select><br>

  <!-- Aquí generamos campos simples; ideal: usar JS para cambiar según categoría del desplegable -->
  <!-- Ejemplo básico de campos comunes -->
  Ciudad: <input name="ciudad" value="<?=htmlspecialchars($_POST['ciudad'] ?? '')?>"><br>
  Dirección: <input name="direccion" value="<?=htmlspecialchars($_POST['direccion'] ?? '')?>"><br>
  WhatsApp: <input name="whatsapp" value="<?=htmlspecialchars($_POST['whatsapp'] ?? '')?>" maxlength="9"><br>
  TikTok: <input name="tiktok" value="<?=htmlspecialchars($_POST['tiktok'] ?? '')?>"><br>
  Instagram: <input name="instagram" value="<?=htmlspecialchars($_POST['instagram'] ?? '')?>"><br>
  Desde S/.: <input name="desde" value="<?=htmlspecialchars($_POST['desde'] ?? '')?>" maxlength="3"><br>
  Hasta S/.: <input name="hasta" value="<?=htmlspecialchars($_POST['hasta'] ?? '')?>" maxlength="3"><br>

  Imagen (jpg/png) : <input type="file" name="imagen" accept="image/jpeg,image/png"><br>

  <button type="submit">PUBLICAR</button>
</form>

<script>
// Para mejorar UX: cambiar las opciones de 'categoria' según 'grupo' sin recargar
const categoriasMap = <?=json_encode($categorias, JSON_UNESCAPED_UNICODE)?>;
document.getElementById('grupo').addEventListener('change', function(){
  const g = this.value;
  const select = document.getElementById('categoria');
  select.innerHTML = '<option value="">--</option>';
  if (categoriasMap[g]) {
    categoriasMap[g].forEach(c=> {
      const o = document.createElement('option');
      o.value = c; o.textContent = c;
      select.appendChild(o);
    });
  }
});
</script>

</body></html>
