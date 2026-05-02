<?php 
  /** @var string $titulo */ 
  /** @var object $usuario */ 
?>

<div class="contenedor">
  <h1><?= $titulo ?></h1>

  <?php include_once __DIR__ . '/../../templates/alertas.php'; ?>
  <form class="formulario" action="/dashboard/usuarios" method="POST" novalidate>
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
</div>

<?php $script = "<script src='/build/js/app.js'></script>"; ?>