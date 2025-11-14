<?php
session_start();
require_once __DIR__.'/db.php';
if (empty($_SESSION['user_id'])) { header("Location: /php/login.php"); exit; }
$user_id = $_SESSION['user_id'];

// en la parte de handling arriba de archivo:
if (isset($_GET['toggle_pin']) && isset($_GET['id'])) {
  $id = intval($_GET['id']);
  // obtener current
  $stmt = $pdo->prepare("SELECT pinned FROM negocios WHERE id = ? AND user_id = ?");
  $stmt->execute([$id, $user_id]);
  $row = $stmt->fetch();
  if ($row) {
    $new = $row['pinned'] ? 0 : 1;
    $pdo->prepare("UPDATE negocios SET pinned = ? WHERE id = ? AND user_id = ?")->execute([$new, $id, $user_id]);
  }
  header("Location: /php/mis_negocios.php");
  exit;
}

if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $stmt = $pdo->prepare("DELETE FROM negocios WHERE id = ? AND user_id = ?");
  $stmt->execute([$id, $user_id]);
  header("Location: /php/mis_negocios.php");
  exit;
}
$stmt = $pdo->prepare("SELECT * FROM negocios WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$rows = $stmt->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Mis anuncios</title></head><body>
<h2>Mis anuncios</h2>
<?php foreach($rows as $r): ?>
  <div style="border:1px solid #ccc;padding:8px;margin:8px;">
    <strong><?=htmlspecialchars($r['titulo'])?></strong> (<?=htmlspecialchars($r['categoria'])?>) - <?=htmlspecialchars($r['created_at'])?><br>
    <a href="/php/edit_negocio.php?id=<?=$r['id']?>">Editar</a> |
    <a href="/php/mis_negocios.php?delete=<?=$r['id']?>" onclick="return confirm('Eliminar?')">Eliminar</a>
    <?php if($r['pinned']): ?><span style="background:gold;color:black;padding:2px 6px;border-radius:3px">FIJADO</span><?php endif;?>
  </div>
<?php endforeach;?>
</body></html>
