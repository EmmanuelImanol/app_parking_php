<aside class="dashboard__sidebar">
  <nav class="dashboard__navbar">
    <div class="dashboard__logo">
      <span>App</span>
    </div>
    <a href="/dashboard" class="dashboard__enlace <?php echo pagina_actual('/dashboard') ? 'dashboard__enlace--actual' : ''; ?>">
      <i class="fa-solid fa-house dashboard__icono"></i>
      <span class="dashboard__menu-texto">
        Dashboard
      </span>
    </a>
    <a href="/dashboard/usuarios" class="dashboard__enlace <?php echo pagina_actual('/dashboard/usuarios') ? 'dashboard__enlace--actual' : ''; ?>">
      <i class="fa-solid fa-users dashboard__icono"></i>
      <span class="dashboard__menu-texto">
        Usuarios
      </span>
    </a>
    <a href="/dashboard/cobroporhora" class="dashboard__enlace <?php echo pagina_actual('/dashboard/cobroporhora') ? 'dashboard__enlace--actual' : ''; ?>">
      <i class="fa-solid fa-square-parking dashboard__icono"></i>
      <span class="dashboard__menu-texto">
        Cobro por hora
      </span>
    </a>
  </nav>

  <a href="/logout" class="dashboard__enlace cerrar-sesion">
    <i class="fa-solid fa-right-from-bracket dashboard__icono"></i>
    <span>
      Cerrar Sesión
    </span>
  </a>
</aside>

<div class="sidebar">
  <a href="">App</a>
  <nav class="sidebar__navbar">
    <a href="/dashboard" class="sidebar__enlace">Dasboard</a>
    <a href="/dashboard/usuarios" class="sidebar__enlace">Usuarios</a>
    <a href="/dashboard/cobroporhora" class="sidebar__enlace">Cobro por hora</a>
  </nav>
  <i class="fa-solid fa-bars sidebar__menu"></i>
</div>