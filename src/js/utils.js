let toastContenedor = document.querySelector('.toast-contenedor');

if(!toastContenedor) {
  toastContenedor = document.createElement('DIV');
  toastContenedor.classList.add('toast-contenedor');
  document.body.appendChild(toastContenedor);
}

export function mostrarAlertas(alertas) {
  const iconos = {
    error: '✕',
    exito: '✓'
  };

  // ✅ Elimina toasts anteriores antes de mostrar nuevos
  toastContenedor.innerHTML = '';
  
  Object.entries(alertas).forEach(([tipo, mensajes]) => {
    mensajes.forEach(mensaje => {
      const toast = document.createElement('DIV');
      toast.classList.add('toast', tipo);
      toast.innerHTML = `
        <span class="toast-icono">${iconos[tipo] ?? 'ℹ'}</span>
        <span class="toast-mensaje">${mensaje}</span>
      `;

      // Cerrar al hacer click en el toast
      toast.addEventListener('click', () => {
        toast.classList.add('toast-salir')
        toast.addEventListener('animationend', () => toast.remove())
      })

      toastContenedor.appendChild(toast);

      // Eliminar después de 3 segundos con animación de salida
      setTimeout(() => {
        toast.classList.add('toast-salir');
        toast.addEventListener('animationend', () => toast.remove())
      }, 3000)
    })
  })
}