(function() {
  let modoModal = 'crear';
  let toastContenedor = document.querySelector('.toast-contenedor');
  let usuarioAEliminar = null;

  const contenedorUsuarios = document.querySelector('#usuarios');
  const modal = document.querySelector('.modal');
  const modalTitulo = document.querySelector('.modal-titulo');
  const modalBtn = document.querySelector('.modal-btn');
  const campoPassword = document.querySelectorAll('.campo-password');
  const btnCrear = document.querySelector('.btn-crear');
  const modalConfirmar = document.querySelector('#modal-confirmar');

  function confirmarEliminar(id, elemento) {
    usuarioAEliminar = { id, elemento };
    modalConfirmar.classList.add('activo');
  }

  // Confirmar Eliminación
  document.querySelector('.btn-confirmar').addEventListener('click', async () => {
    if(!usuarioAEliminar) return;
    await eliminarUsuario(usuarioAEliminar.id, usuarioAEliminar.elemento);
    cerrarModalConfirmar();
  });

  document.querySelector('.btn-cancelar').addEventListener('click', () => {
    cerrarModalConfirmar();
  });

  // Cerrar al hacer click en el overlay
  modalConfirmar.addEventListener('click', function(e) {
    if(e.target === this) cerrarModalConfirmar();
  });

  function cerrarModalConfirmar() {
    modalConfirmar.classList.remove('activo');
    usuarioAEliminar = null;
  }

  if(!toastContenedor) {
    toastContenedor = document.createElement('DIV');
    toastContenedor.classList.add('toast-contenedor');
    document.body.appendChild(toastContenedor);
  }

  if(contenedorUsuarios) {
    consultarAPI();
  }

  async function consultarAPI() {
    try {
      const url = `${BASE_URL}/api/usuarios`;
      const resultado = await fetch(url);
      const usuarios = await resultado.json();
      mostrarUsuarios(usuarios);
    } catch (error) {
      console.log(error);
    }
  }

  // Abrir modal en modo EDITAR al hacer click en tarjeta
  function mostrarUsuarios(usuarios) {
    usuarios.forEach( usuario => {
      const { id, nombre, email, rol } = usuario;
      const nombreUsuario = document.createElement('P');
      nombreUsuario.textContent = nombre;

      const emailUsuario = document.createElement('P');
      emailUsuario.textContent = email;
      
      const rolUsuario = document.createElement('P');
      rolUsuario.textContent = rol;

      const btnEliminar = document.createElement('BUTTON');
      btnEliminar.textContent = 'Eliminar';
      btnEliminar.classList.add('btn-eliminar');

      btnEliminar.addEventListener('click', (e) => {
        e.stopPropagation();
        confirmarEliminar(id, usuarioDiv);
      })

      const usuarioDiv = document.createElement('DIV');
      usuarioDiv.classList.add('usuario');
      usuarioDiv.dataset.idUsuario = id;

      usuarioDiv.appendChild(nombreUsuario);
      usuarioDiv.appendChild(emailUsuario);
      usuarioDiv.appendChild(rolUsuario);
      usuarioDiv.appendChild(btnEliminar);

      usuarioDiv.addEventListener('click', () => abrirModal('editar', usuario));
      contenedorUsuarios.appendChild(usuarioDiv);
    });
  }

  // Abrir modal en modo CREAR
  btnCrear.addEventListener('click', () => {
    abrirModal('crear');
  })

  // Cerrar el modal, click en el overlay (fondo oscuro)
  modal.addEventListener('click', function(e) {
    if(e.target === this) {
      cerrarModal();
    }
  })

  // Cerrar modal, click en el botón X
  document.querySelector('.cerrar-modal').addEventListener('click', () => {
    cerrarModal();
  })

  // Cerrar modal, con la tecla Escape
  document.addEventListener('keydown', (e) => {
    if(e.key === 'Escape' && modal.classList.contains('activo')) {
      cerrarModal();
    }
  })

  function abrirModal(modo, usuario = {}) {
    modoModal = modo;

    // Limpiar el formd
    document.querySelector('.form-usuario').reset();
    document.querySelector('.modal-id').value = '';

    if(modo === 'crear') {
      modalTitulo.textContent = 'Crear Usuario';
      modalBtn.value = 'Crear Usuario';
      campoPassword.forEach(password => password.style.display = 'block')
    } else {
      modalTitulo.textContent = 'Editar Usuario';
      modalBtn.value = 'Guardar Cambios';
      campoPassword.forEach(password => password.style.display = 'none')

      // Rellenar con los datos del usuario
      document.querySelector('.modal-id').value = usuario.id;
      document.querySelector('#nombre').value = usuario.nombre;
      document.querySelector('#email').value = usuario.email;
    }

    modal.classList.add('activo');
  }

  function cerrarModal() {
    modal.classList.remove('activo');
    document.querySelector('.form-usuario').reset();
    document.querySelector('.modal-id').value = '';
  }

  // Submit del form - decide si crear o editar según el modo

  document.querySelector('.form-usuario').addEventListener('submit', async function(e) {
    e.preventDefault();

    const usuario = {
      nombre: document.querySelector('#nombre').value.trim(),
      email: document.querySelector('#email').value.trim(),
      password: document.querySelector('#password').value.trim(),
      password_confirm: document.querySelector('#password_confirm').value.trim(),
    }

    if(modoModal === 'crear') {
      await crearUsuario(usuario);
    } else {
      await actualizarUsuario(usuario);
    }
  })

  async function crearUsuario(usuario) {
    const { nombre, email, password, password_confirm } = usuario;
    const datos = new FormData();
    datos.append('nombre', nombre);
    datos.append('email', email);
    datos.append('password', password);
    datos.append('password_confirm', password_confirm);

    try {
      const url = `${BASE_URL}/api/usuario`;
      const respuesta = await fetch(url, {
        method: 'POST',
        body: datos
      });
      
      const resultado = await respuesta.json();
      if(resultado.resultado) {
        mostrarAlertas({ exito: ['Usuario creado correctamente'] });
        cerrarModal();
        // Refrescar lista
        contenedorUsuarios.innerHTML = '';
        consultarAPI();
      } else {
        mostrarAlertas(resultado.alertas);
      }
    } catch (error) {
      console.log(error);
    }
  }

  async function eliminarUsuario(id, elemento) {
    const datos = new FormData();
    datos.append('id', id);

    try {
      const url = `${BASE_URL}/api/usuario/eliminar`;
      const respuesta = await fetch(url, {
        method: 'POST',
        body: datos
      });

      const resultado = await respuesta.json();

      if(resultado.resultado) {
        mostrarAlertas({ exito: ['Usuario eliminado correctamente']});
        // Elimina la tarjeta del DOM sin recargar toda la lista
        elemento.remove()
      } else {
        mostrarAlertas({ error: [resultado.mensaje] });
      }
    } catch (error) {
      console.log(error)
    }
  }

  function mostrarAlertas(alertas) {
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
})()