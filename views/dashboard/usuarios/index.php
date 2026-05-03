<?php 
  /** @var string $titulo */ 
  /** @var object $usuario */ 
  /** @var array $usuarios */ 
?>

<div class="contenedor">
  <h1><?= $titulo ?></h1>

  <?php include_once __DIR__ . '/../../templates/alertas.php'; ?>
  <div class="usuarios">
    <form class="formulario" action="<?= base_url('/dashboard/usuarios') ?>" method="POST" novalidate>
      <div class="campo">
        <label for="nombre">Nombre:</label>
        <input 
          type="text" 
          id="nombre" 
          name="nombre" 
          placeholder="Nombre del usuario"
          value="<?php echo $usuario->nombre; ?>"
        >
      </div>
      <div class="campo">
        <label for="email">Email:</label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          placeholder="Email"
          value="<?php echo $usuario->email; ?>"
        >
      </div>
      <div class="campo">
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password">
      </div>
      <div class="campo">
        <label for="password_confirm">Repetir Contraseña</label>
        <input type="password" name="password_confirm" id="password_confirm">
      </div>
      <input type="submit" value="Crear Usuario">
    </form>
    <div class="usuarios__contenedor">
      <?php if(count($usuarios) === 0): ?>
        <p>No hay usuarios aún</p>
      <?php else: ?>
        <?php foreach($usuarios as $usuario): ?>
          <div class="usuarios__card">
            <p><?= $usuario->nombre ?></p>
            <p><?= $usuario->email ?></p>
            <p><?= $usuario->rol ?></p>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php 
  $base_url = base_url('/build/js/app.js');
  $script = "<script src='{$base_url}'></script>"; 
?>