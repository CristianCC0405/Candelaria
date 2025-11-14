<?php
require_once __DIR__.'/../php/db.php';

// obtenemos los anuncios de la categoría 'Disfraces'
$stmt = $pdo->prepare("SELECT * FROM negocios WHERE categoria = ? ORDER BY pinned DESC, created_at DESC");
$stmt->execute(['Disfraces']);
$rows = $stmt->fetchAll();

// separamos fijados y no fijados
$pinned = [];
$resto = [];
foreach ($rows as $r) {
  if ($r['pinned']) $pinned[] = $r;
  else $resto[] = $r;
}

// nos quedamos con máximo 2 fijos al inicio (el admin/tu puede poner pinned=1 manualmente o desde panel)
$first = array_slice($pinned, 0, 2);

// mezclar $resto
shuffle($resto);

// montar lista final
$final = array_merge($first, $resto);

// ahora renderiza las tarjetas (usa las clases que ya tienes en tu HTML)
?>
<div class="lista-negocios">
<?php foreach ($final as $n): 
   $campos = json_decode($n['campos_json'], true);
?>
  <div class="tarjeta-negocio">
    <h3><?=htmlspecialchars($n['titulo'])?></h3>
    <?php if($n['imagen']): ?><img src="/<?=htmlspecialchars($n['imagen'])?>" alt=""> <?php endif; ?>
    <!-- Ejemplo mostrando ciudad y whatsapp si existen -->
    <?php if(!empty($campos['ciudad'])): ?><div class="Ciudad"><?=htmlspecialchars($campos['ciudad'])?></div><?php endif; ?>
    <?php if(!empty($campos['whatsapp'])): ?><div class="WhatsApp"><?=htmlspecialchars($campos['whatsapp'])?></div><?php endif; ?>
  </div>
<?php endforeach;?>
</div>
