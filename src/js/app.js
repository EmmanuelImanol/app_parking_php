document.addEventListener('DOMContentLoaded', () => {
  const btnMenu = document.querySelector('.sidebar__menu');
  const sidebarNavbar = document.querySelector('.sidebar__navbar');
  const inputPassword = document.querySelector('#password');
  const iconoOjo = document.querySelector('#togglePassword');
  
  if(inputPassword && iconoOjo) {

    inputPassword.addEventListener('input', () => {
      iconoOjo.style.display = inputPassword.value.length > 0 ? 'block' : 'none';
    });

    iconoOjo.addEventListener('click', () => {
      const esPassword = inputPassword.type === 'password';
      inputPassword.type = esPassword ? 'text' : 'password';
      iconoOjo.classList.toggle('fa-eye', !esPassword);
      iconoOjo.classList.toggle('fa-eye-slash', esPassword);
    });
  }
  
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