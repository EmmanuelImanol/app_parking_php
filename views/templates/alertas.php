<?php 
  // Avisa que $alertas viene de afuera
  /** @var array $alertas */ 
?>
<?php if(!empty($alertas)): ?>
  <div class="alertas-contenedor">
    <?php foreach($alertas as $tipo => $mensajes): ?>
      <?php foreach($mensajes as $mensaje): ?>
        <div class="alerta <?= $tipo ?>">
          <?= $mensaje ?>
        </div>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </div>
<?php endif; ?>