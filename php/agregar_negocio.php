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

  $campos = [];
  foreach ($_POST as $k=>$v) {
    if (in_array($k, ['titulo','grupo','categoria'])) continue;
    $campos[$k] = trim($v);
  }

  if (isset($campos['whatsapp']) && !validar_whatsapp($campos['whatsapp'])) $errors[] = "WhatsApp debe tener 9 dígitos sin espacios.";
  if (isset($campos['desde']) && !validar_precio($campos['desde'])) $errors[] = "Precio Desde inválido.";
  if (isset($campos['hasta']) && !validar_precio($campos['hasta'])) $errors[] = "Precio Hasta inválido.";

  foreach ($campos as $k=>$v) {
    $v = mb_substr($v,0,25);
    if (!in_array($k, ['whatsapp','tiktok','instagram','desde','hasta','de','a'])) {
      $v = capitalizar($v);
    }
    $campos[$k] = $v;
  }

  $image_path = null;
  if (!empty($_FILES['imagen']['name'])) {
    $categoria_folder = strtolower(str_replace(' ', '', $categoria));
    $carpeta = __DIR__ . "/../img/{$categoria_folder}/";
    $nombre_archivo = siguiente_nombre_img($carpeta, 'img');
    $dest = $carpeta . $nombre_archivo;

    $allowed = ['image/jpeg','image/jpg','image/png'];
    if (!in_array($_FILES['imagen']['type'], $allowed)) $errors[] = "Formato de imagen no permitido.";
    if ($_FILES['imagen']['size'] > 3 * 1024 * 1024) $errors[] = "Imagen demasiado grande (máx 3MB).";

    if (empty($errors)) {
      move_uploaded_file($_FILES['imagen']['tmp_name'], $dest);
      $image_path = "img/{$categoria_folder}/{$nombre_archivo}";
    }
  }

  if (empty($errors)) {
    $stmt = $pdo->prepare("INSERT INTO negocios (user_id, titulo, grupo, categoria, campos_json, imagen) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
      $user_id,
      capitalizar($titulo),
      $grupo,
      $categoria,
      json_encode($campos, JSON_UNESCAPED_UNICODE),
      $image_path
    ]);
    $success = true;
  }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Agregar negocio</title>

<!-- Tus estilos globales -->
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/disfraces.css">
<link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">

<style>
  body { font-family: 'Lato', sans-serif; }

  .form-container {
    max-width: 500px;
    margin: 40px auto;
    padding: 25px;
    border: 2px solid #7f6000;
    border-radius: 16px;
    background: #fffaf0;
    text-align: center;
  }

  h2 {
    color: #7f6000;
    margin-bottom: 20px;
    font-size: 28px;
    text-align: center;
  }

  input, select {
    width: 90%;
    padding: 12px;
    margin: 8px 0;
    border-radius: 10px;
    border: 1px solid #bbb;
    font-size: 16px;
  }

  button {
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

  button:hover {
    background: #a18400;
  }

  .msg {
    background: #def;
    border: 1px solid #9ab;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 8px;
  }

  .msg-error {
    color: red;
    margin-bottom: 10px;
  }

</style>
</head>

<body>

<section id="disfraces">
  <h1>AGREGAR NEGOCIO</h1>
  <p style="font-weight:300;">Publica tu negocio en #CANDELARIA2026</p>
</section>

<div class="form-container">

<?php if ($success): ?>
  <div class="msg">
    TU ANUNCIO HA SIDO PUBLICADO EN UN ORDEN ALEATORIO.<br>
    SI QUIERES QUE APAREZCA PRIMERO ESCRIBE A: <strong>bailaencandelaria@gmail.com</strong>
    <button onclick="this.parentElement.style.display='none'" style="float:right;">X</button>
  </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
  <?php foreach ($errors as $e): ?>
    <div class="msg-error"><?= htmlspecialchars($e) ?></div>
  <?php endforeach; ?>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">

  <input name="titulo" placeholder="Título" 
         value="<?=htmlspecialchars($_POST['titulo'] ?? '')?>" required>

  <label>Grupo</label>
  <select name="grupo" id="grupo" required>
    <option value="">--</option>
    <?php foreach ($grupos as $k=>$v):
      $sel = (($_POST['grupo'] ?? '')==$k)?'selected':''; ?>
      <option value="<?=htmlspecialchars($k)?>" <?=$sel?>><?=htmlspecialchars($v)?></option>
    <?php endforeach;?>
  </select>

  <label>Categoría</label>
  <select name="categoria" id="categoria" required>
    <option value="">--</option>
    <?php
      $gsel = $_POST['grupo'] ?? '';
      if ($gsel && isset($categorias[$gsel])):
        foreach ($categorias[$gsel] as $cat):
          $sel = (($_POST['categoria'] ?? '')==$cat)?'selected':''; ?>
          <option value="<?=htmlspecialchars($cat)?>" <?=$sel?>><?=htmlspecialchars($cat)?></option>
        <?php endforeach;
      endif;
    ?>
  </select>

  Ciudad: <input name="ciudad" value="<?=htmlspecialchars($_POST['ciudad'] ?? '')?>">
  Dirección: <input name="direccion" value="<?=htmlspecialchars($_POST['direccion'] ?? '')?>">
  WhatsApp: <input name="whatsapp" maxlength="9"
                   value="<?=htmlspecialchars($_POST['whatsapp'] ?? '')?>">
  TikTok: <input name="tiktok" value="<?=htmlspecialchars($_POST['tiktok'] ?? '')?>">
  Instagram: <input name="instagram" value="<?=htmlspecialchars($_POST['instagram'] ?? '')?>">
  Desde S/.: <input name="desde" maxlength="3"
                    value="<?=htmlspecialchars($_POST['desde'] ?? '')?>">
  Hasta S/.: <input name="hasta" maxlength="3"
                    value="<?=htmlspecialchars($_POST['hasta'] ?? '')?>">

  Imagen (jpg/png):  
  <input type="file" name="imagen" accept="image/jpeg, image/png">

  <button type="submit">PUBLICAR</button>

</form>
</div>

<script>
const categoriasMap = <?=json_encode($categorias, JSON_UNESCAPED_UNICODE)?>;
document.getElementById('grupo').addEventListener('change', function(){
  const g = this.value;
  const select = document.getElementById('categoria');
  select.innerHTML = '<option value="">--</option>';
  if (categoriasMap[g]) {
    categoriasMap[g].forEach(c=>{
      const o=document.createElement('option');
      o.value=c; o.textContent=c;
      select.appendChild(o);
    });
  }
});
</script>

</body>
</html>
