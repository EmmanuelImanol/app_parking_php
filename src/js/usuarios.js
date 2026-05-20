import { mostrarAlertas } from "./utils.js";

let modoModal = 'crear';
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

if(contenedorUsuarios) {
  consultarAPI();
}

async function consultarAPI() {
  try {
    const url = `${API_URL}/usuarios`;
    const resultado = await fetch(url);
    const usuarios = await resultado.json();
    mostrarUsuarios(usuarios);
  } catch (error) {
    console.log(error);
  }
}

function obtenerIniciales(nombre) {
  const partes = nombre.trim().split(' ');
  if (partes.length >= 2) {
    // Tiene nombre y apellido → primera letra de cada uno
    return (partes[0][0] + partes[1][0]).toUpperCase();
  }
  // Solo un nombre → primeras dos letras
  return nombre.substring(0, 2).toUpperCase();
}

// Abrir modal en modo EDITAR al hacer click en tarjeta
function mostrarUsuarios(usuarios) {
  usuarios.forEach( usuario => {
    const { id, nombre, email, rol } = usuario;
    const esCajero = rol === 'cajero';

    // Avatar
    const avatar = document.createElement('DIV');
    avatar.classList.add('usuario__avatar');
    if (esCajero) avatar.classList.add('usuario__avatar--cajero');
    avatar.textContent = obtenerIniciales(nombre);

    // Nombre
    const nombreEl = document.createElement('P');
    nombreEl.classList.add('usuario__nombre');
    nombreEl.textContent = nombre;

    //Badge de rol
    const rolBadgeEl = document.createElement('SPAN');
    rolBadgeEl.classList.add('usuario__rol');
    if (esCajero) rolBadgeEl.classList.add('usuario__rol--cajero');
    rolBadgeEl.textContent = rol.charAt(0).toUpperCase() + rol.slice(1);

    // Info (nombre + rol)
    const info = document.createElement('DIV');
    info.classList.add('usuario__info');
    info.appendChild(nombreEl);
    info.appendChild(rolBadgeEl);

    // Cabecera
    const cabecera = document.createElement('DIV');
    cabecera.classList.add('usuario__cabecera');
    cabecera.appendChild(avatar);
    cabecera.appendChild(info);

    // Divisor
    const divisor1 = document.createElement('HR');
    divisor1.classList.add('usuario__divisor');

    // Email en cuerpo
    const dato = document.createElement('SPAN');
    dato.classList.add('usuario__dato');
    dato.textContent = email;

    const cuerpo = document.createElement('DIV');
    cuerpo.classList.add('usuario__cuerpo');
    cuerpo.appendChild(dato);

    // Divisor footer
    const divisor2 = document.createElement('HR');
    divisor2.classList.add('usuario__divisor');

    // Botones
    const btnEditar = document.createElement('BUTTON');
    btnEditar.textContent = 'Editar';
    btnEditar.classList.add('btn-editar');
    btnEditar.addEventListener('click', (e) => {
      e.stopPropagation();
      abrirModal('editar', usuario);
    });

    const btnEliminar = document.createElement('BUTTON');
    btnEliminar.textContent = 'Eliminar';
    btnEliminar.classList.add('btn-eliminar');
    btnEliminar.addEventListener('click', (e) => {
      e.stopPropagation();
      confirmarEliminar(id, usuarioDiv);
    });

    const footer = document.createElement('DIV');
    footer.classList.add('usuario__footer');
    footer.appendChild(btnEditar);
    footer.appendChild(btnEliminar);

    // Tarjeta completa
    const usuarioDiv = document.createElement('DIV');
    usuarioDiv.classList.add('usuario');
    usuarioDiv.dataset.idUsuario = id;
    usuarioDiv.appendChild(cabecera);
    usuarioDiv.appendChild(divisor1);
    usuarioDiv.appendChild(cuerpo);
    usuarioDiv.appendChild(divisor2);
    usuarioDiv.appendChild(footer);

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

    modal.dataset.nombreOriginal = usuario.nombre;
    modal.dataset.emailOriginal = usuario.email;
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

  if(modoModal === 'editar') {
    const sinCambios = 
      usuario.nombre === modal.dataset.nombreOriginal &&
      usuario.email  === modal.dataset.emailOriginal;

    if(sinCambios) {
      mostrarAlertas({ error: ['No has realizado ningún cambio'] });
      return;
    }
  }

  modalBtn.disabled = true;
  modalBtn.value = 'Procesando...';

  if(modoModal === 'crear') {
    await crearUsuario(usuario);
  } else {
    await actualizarUsuario(usuario);
  }

  modalBtn.disabled = false;
  modalBtn.value = modoModal === 'crear' ? 'Crear Usuario' : 'Guardar Cambios';

})

async function crearUsuario(usuario) {
  const { nombre, email, password, password_confirm } = usuario;
  const datos = new FormData();
  datos.append('nombre', nombre);
  datos.append('email', email);
  datos.append('password', password);
  datos.append('password_confirm', password_confirm);

  try {
    const url = `${API_URL}/usuario`;
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

async function actualizarUsuario(usuario) {
  const { nombre, email } = usuario;
  const id = document.querySelector('.modal-id').value;

  const datos = new FormData();
  datos.append('id', id);
  datos.append('nombre', nombre);
  datos.append('email', email);

  try {
    const url = `${API_URL}/usuario/actualizar`;
    const respuesta = await fetch(url, {
      method: 'POST',
      body: datos
    });

    const resultado = await respuesta.json();
    if(resultado.resultado) {
      mostrarAlertas({ exito: ['Usuario Actualizado Correctamente'] });
      cerrarModal();
      contenedorUsuarios.innerHTML = '';
      consultarAPI();
    } else {
      if(resultado.alertas) {
        mostrarAlertas(resultado.alertas);
      } else {
        mostrarAlertas({ error: [resultado.mensaje ?? 'Hubo un error'] });
      }
    }
  } catch (error) {
    console.log(error);
  }
}

async function eliminarUsuario(id, elemento) {
  const datos = new FormData();
  datos.append('id', id);

  try {
    const url = `${API_URL}/usuario/eliminar`;
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