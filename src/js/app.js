
const btnMenu = document.querySelector('.sidebar__menu');
const sidebarNavbar = document.querySelector('.sidebar__navbar');

btnMenu.addEventListener('click', function() {
  sidebarNavbar.classList.toggle('activo');
})