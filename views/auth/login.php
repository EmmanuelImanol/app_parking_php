<div class="contenedor">
  <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
  <form action="<?= base_url('/') ?>" class="formulario-login" method="POST" novalidate>
    <div class="campo">
      <label for="email">Email: </label>
      <input 
        type="email"
        id="email"
        name="email"
        placeholder="ejemplo@parking.com"
      >
    </div>
    <div class="campo">
      <label for="password">Contraseña:</label>
      <input 
        type="password" 
        name="password" 
        id="password"
      >
    </div>
    <input type="submit" value="Iniciar Sesión">
  </form>
</div>

<?php $script = "<script src='/build/js/app.js'></script>"; ?>