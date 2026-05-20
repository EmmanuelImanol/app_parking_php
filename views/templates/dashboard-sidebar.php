<aside class="dashboard__sidebar">
  <nav class="dashboard__navbar">

    <div class="dashboard__logo">
      <div class="dashboard__logo-icono">
        <i class="fa-solid fa-square-parking"></i>
      </div>
      <div class="dashboard__logo-texto">
        <span class="dashboard__logo-nombre">Parking App</span>
        <span class="dashboard__logo-subtitulo">Panel de control</span>
      </div>
    </div>

    <?php if($_SESSION['rol'] !== 'cajero'): ?>
      <a href="<?= base_url('/dashboard') ?>" class="dashboard__enlace <?php echo pagina_actual('/dashboard') ? 'dashboard__enlace--actual' : ''; ?>">
        <i class="fa-solid fa-house dashboard__icono"></i>
        <span>Dashboard</span>
      </a>
      <a href="<?= base_url('/dashboard/usuarios') ?>" class="dashboard__enlace <?php echo pagina_actual('/dashboard/usuarios') ? 'dashboard__enlace--actual' : ''; ?>">
        <i class="fa-solid fa-users dashboard__icono"></i>
        <span>Usuarios</span>
      </a>
      <a href="<?= base_url('/dashboard/configuracion') ?>" class="dashboard__enlace <?php echo pagina_actual('/dashboard/configuracion') ? 'dashboard__enlace--actual' : ''; ?>">
        <i class="fa-solid fa-cogs dashboard__icono"></i>
        <span>Configuración</span>
      </a>
    <?php endif; ?>
    <a href="<?= base_url('/dashboard/cobroporhora') ?>" class="dashboard__enlace <?php echo pagina_actual('/dashboard/cobroporhora') ? 'dashboard__enlace--actual' : ''; ?>">
      <i class="fa-solid fa-clock dashboard__icono"></i>
      <span>Cobro por hora</span>
    </a>

  </nav>

  <a href="<?= base_url('/logout') ?>" class="dashboard__enlace cerrar-sesion">
    <i class="fa-solid fa-right-from-bracket dashboard__icono"></i>
    <span>Cerrar Sesión</span>
  </a>
</aside>

<!-- Navbar móvil -->
<div class="sidebar">
  <a class="sidebar__logo" href="<?= base_url('/dashboard') ?>">
    <div class="sidebar__logo-icono">
      <i class="fa-solid fa-square-parking"></i>
    </div>
    <span class="sidebar__logo-nombre">Parking</span>
  </a>

  <nav class="sidebar__navbar">
    <?php if($_SESSION['rol'] !== 'cajero'): ?>
      <a href="<?= base_url('/dashboard') ?>" class="sidebar__enlace">
        <i class="fa-solid fa-house"></i> Dashboard
      </a>
      <a href="<?= base_url('/dashboard/usuarios') ?>" class="sidebar__enlace">
        <i class="fa-solid fa-users"></i> Usuarios
      </a>
      <a href="<?= base_url('/dashboard/configuracion') ?>" class="sidebar__enlace">
        <i class="fa-solid fa-cogs"></i> Configuración
      </a>
    <?php endif; ?>
    <a href="<?= base_url('/dashboard/cobroporhora') ?>" class="sidebar__enlace">
      <i class="fa-solid fa-clock"></i> Cobro por hora
    </a>
    <a href="<?= base_url('/logout') ?>" class="sidebar__enlace cerrar-sesion">
      <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
    </a>
  </nav>

  <div class="sidebar__menu">
    <i class="fa-solid fa-bars"></i>
  </div>
</div>

<?php 
  $base_url = base_url('/build/js/app.js');
  $script = $script ?? "<script src='{$base_url}'></script>"; 
?>