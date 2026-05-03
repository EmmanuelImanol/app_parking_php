document.addEventListener('DOMContentLoaded', () => {
  const btnMenu = document.querySelector('.sidebar__menu');
  const sidebarNavbar = document.querySelector('.sidebar__navbar');
  const alertasContenedor = document.querySelector('.alertas-contenedor');
  
  if(btnMenu && sidebarNavbar) {
    btnMenu.addEventListener('click', function() {
      sidebarNavbar.classList.toggle('activo');
    })
  }
  
  // Si existe el elemento alertasContenedor eliminalas en 3 segundos
  if(alertasContenedor) {
    setTimeout(() => {
      alertasContenedor.style.display = 'none';
    }, 3000);
  }
})