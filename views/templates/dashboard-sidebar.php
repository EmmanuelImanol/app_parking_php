<aside class="dashboard__sidebar">
  <nav class="dashboard__navbar">
    <div class="dashboard__logo">
      <span>App</span>
    </div>
    <a href="<?= base_url('/dashboard') ?>" class="dashboard__enlace <?php echo pagina_actual('/dashboard') ? 'dashboard__enlace--actual' : ''; ?>">
      <i class="fa-solid fa-house dashboard__icono"></i>
      <span class="dashboard__menu-texto">
        Dashboard
      </span>
    </a>
    <a href="<?= base_url('/dashboard/usuarios') ?>" class="dashboard__enlace <?php echo pagina_actual('/dashboard/usuarios') ? 'dashboard__enlace--actual' : ''; ?>">
      <i class="fa-solid fa-users dashboard__icono"></i>
      <span class="dashboard__menu-texto">
        Usuarios
      </span>
    </a>
    <a href="<?= base_url('/dashboard/cobroporhora') ?>" class="dashboard__enlace <?php echo pagina_actual('/dashboard/cobroporhora') ? 'dashboard__enlace--actual' : ''; ?>">
      <i class="fa-solid fa-square-parking dashboard__icono"></i>
      <span class="dashboard__menu-texto">
        Cobro por hora
      </span>
    </a>
  </nav>

  <a href="<?= base_url('/logout') ?>" class="dashboard__enlace cerrar-sesion">
    <i class="fa-solid fa-right-from-bracket dashboard__icono"></i>
    <span>
      Cerrar Sesión
    </span>
  </a>
</aside>

<div class="sidebar">
  <a href="">App</a>
  <nav class="sidebar__navbar">
    <a href="<?= base_url('/dashboard') ?>" class="sidebar__enlace">Dasboard</a>
    <a href="<?= base_url('/dashboard/usuarios') ?>" class="sidebar__enlace">Usuarios</a>
    <a href="<?= base_url('/dashboard/cobroporhora') ?>" class="sidebar__enlace">Cobro por hora</a>
    <a href="<?= base_url('/logout') ?>" class="sidebar__enlace">Cerrar Sesión</a>
  </nav>
  <i class="fa-solid fa-bars sidebar__menu"></i>
</div>

<?php 
  $base_url = base_url('/build/js/app.js');
  $script = $script ?? "<script src='{$base_url}'></script>"; 
?>