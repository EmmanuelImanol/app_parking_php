document.addEventListener('DOMContentLoaded', () => {
  const btnMenu = document.querySelector('.sidebar__menu');
  const sidebarNavbar = document.querySelector('.sidebar__navbar');
  
  if(btnMenu && sidebarNavbar) {
    btnMenu.addEventListener('click', function() {
      sidebarNavbar.classList.toggle('activo');
    });

    // Cerrar al hacer click fuera
    document.addEventListener('click', (e) => {
      if(!sidebarNavbar.contains(e.target) && !btnMenu.contains(e.target)) {
        sidebarNavbar.classList.remove('activo');
      }
    });
  }
})