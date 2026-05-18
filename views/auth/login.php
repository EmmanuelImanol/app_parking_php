<div class="contenedor">
  <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
  <form action="<?= base_url('/') ?>" class="formulario-login" method="POST" novalidate>
    <div class="campo">
      <label for="email">Email: </label>
      <div class="campo__input">
        <i class="fa-solid fa-envelope campo__icono"></i>
        <input 
          type="email"
          id="email"
          name="email"
          placeholder="ejemplo@parking.com"
        >
      </div>
    </div>
    <div class="campo">
      <label for="password">Contraseña:</label>
      <div class="campo__input">
        <i class="fa-solid fa-lock campo__icono"></i>
        <input 
          type="password" 
          name="password" 
          placeholder="Contraseña"
          id="password"
        >
        <i class="fa-solid fa-eye campo__ojo" id="togglePassword"></i>
      </div>
    </div>
    <input type="submit" class="btn-login" value="Iniciar Sesión">
  </form>
</div>

<?php 
  $base_url = base_url('/build/js/app.js');
  $script = "<script src='{$base_url}'></script>"; 
?>