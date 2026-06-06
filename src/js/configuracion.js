import { mostrarAlertas } from "./utils.js";

const formEstacionamiento = document.querySelector('#formEstacionamiento');
const formTarifas = document.querySelector('#formTarifas');
const btnAgregarTarifa = document.querySelector('#btnAgregarTarifa');
const btnImprimir = document.querySelector('#ticketBtnImprimir');
const nuevaTarifaForm = document.querySelector('#nuevaTarifaForm');
let formularioVisible = false;

let datosConfiguracion = {};
let datosTarifas = {};

if(btnImprimir) {
  btnImprimir.addEventListener('click', () => {
    setTimeout(() => {
      window.print();
    }, 300);
  });
}

if(formEstacionamiento) {
  formEstacionamiento.addEventListener('submit', actualizarDatosConfiguracion);
}

if(formTarifas) {
  formTarifas.addEventListener('submit', async (e) => {
    e.preventDefault();

    if(formularioVisible) {
      await crearTarifa();
      return;
    }

    await actualizarTarifa(e);
  });
  formTarifas.addEventListener('click', desactivarTarifa);
}

obtenerDatosConfiguracion();

async function obtenerDatosConfiguracion() {
  try {
    const url = `${API_URL}/configuracion/estacionamiento`;
    const configuracion = await fetch(url);
    const datosEstacionamiento = await configuracion.json();

    datosConfiguracion = datosEstacionamiento || {};

    mostrarDatosConfiguracion(datosEstacionamiento);
  } catch (error) {
    console.error('Error al obtener los datos de configuración: ', error);
  }
}

function mostrarDatosConfiguracion(datos) {
  if(!datos) return;
  const { 
    nombreSucursal, 
    direccionEstacionamiento,
    regimenFiscal,
    representacionLegal,
    rfc,
    telefonoContacto,
  } = datos;

  document.querySelector('#nombre-sucursal').value = nombreSucursal ?? '';
  document.querySelector('#direccion-estacionamiento').value = direccionEstacionamiento ?? '';
  document.querySelector('#regimen-fiscal').value = regimenFiscal ?? '';
  document.querySelector('#representacion-legal').value = representacionLegal ?? '';
  document.querySelector('#rfc').value = rfc ?? '';
  document.querySelector('#telefono-contacto').value = telefonoContacto ?? '';
}


async function actualizarDatosConfiguracion(e) {
  e.preventDefault();

  // Extraemos los valores actuales que están escritos en lon inputs del formulario
  const nombreSucursal = document.querySelector('#nombre-sucursal').value.trim();
  const direccionEstacionamiento = document.querySelector('#direccion-estacionamiento').value.trim();
  const regimenFiscal = document.querySelector('#regimen-fiscal').value.trim();
  const representacionLegal = document.querySelector('#representacion-legal').value.trim();
  const rfc = document.querySelector('#rfc').value.trim();
  const telefonoContacto = document.querySelector('#telefono-contacto').value.trim();

  // Validar el estado actual contra los datos que se tenían antes de editar
  if(
    nombreSucursal === (datosConfiguracion.nombreSucursal ?? '') &&
    direccionEstacionamiento === (datosConfiguracion.direccionEstacionamiento ?? '') &&
    regimenFiscal === (datosConfiguracion.regimenFiscal ?? '') &&
    representacionLegal === (datosConfiguracion.representacionLegal ?? '') &&
    rfc === (datosConfiguracion.rfc ?? '') &&
    telefonoContacto === (datosConfiguracion.telefonoContacto ?? '')
  ) {
    mostrarAlertas({ error: ['No se han realizado cambios en la configuración']});
    return;
  }

  const datosFormulario = new FormData(formEstacionamiento);

  try {
    const url = `${API_URL}/configuracion/actualizar`;
    const respuesta = await fetch(url, {
      method: 'POST',
      body: datosFormulario
    });

    const resultado = await respuesta.json();
    
    if(resultado.resultado) {
      mostrarAlertas({ exito: [resultado.mensaje]});
      obtenerDatosConfiguracion();
    } else {
      mostrarAlertas(resultado.alertas);
    }
  } catch (error) {
    console.error('Hubo un error al actualizar: ', error);
    mostrarAlertas({ error: ['Error de conexión con el servidor'] });
  }
}

function capturarTarifasOriginales() {
  const rows = formTarifas?.querySelectorAll('.tarifa-row');
  if(!rows) return;

  rows.forEach(row => {
    const inputTipo = row.querySelector('input[name="tipo"]');
    const inputTarifa = row.querySelector('input[name="horaTarifa"]');
    if(!inputTipo || !inputTarifa) return;

    const id = inputTarifa.dataset.id;
    datosTarifas[id] = {
      tipo: inputTipo.value.trim(),
      horaTarifa: parseFloat(inputTarifa.value)
    };
  });

  // console.log('Tarifas originales capturadas: ', datosTarifas);
}

capturarTarifasOriginales();

async function actualizarTarifa(e) {
  e.preventDefault();

  const rows = formTarifas.querySelectorAll('.tarifa-row');

  // Verificar si hay cambios en las tarifas
  const huboCambios = Array.from(rows).some(row => {
    const inputTipo = row.querySelector('input[name="tipo"]');
    const inputTarifa = row.querySelector('input[name="horaTarifa"]');

    // ← Ignora filas sin data-id
    if(!inputTarifa?.dataset.id) return false;
    if(!inputTipo || !inputTarifa) return false;

    const id = inputTarifa.dataset.id;
    const original = datosTarifas[id];

    // console.log('ID:', id);
    // console.log('Original:', original);
    // console.log('Tipo actual:', inputTipo.value.trim(), '| Original tipo:', original?.tipo);
    // console.log('Tarifa actual:', parseFloat(inputTarifa.value), '| Original tarifa:', original?.horaTarifa);

    return parseFloat(inputTarifa.value) !== original?.horaTarifa || inputTipo.value.trim() !== original?.tipo;
  });

  // console.log(huboCambios);
  // console.log('Rows encontrados:', rows.length);

  if(!huboCambios) {
    mostrarAlertas({ error: ['No se han realizado cambios en las tarifas']});
    return;
  }

  // Armar payload con las tarifas modificadas
  const tarifasModificadas = Array.from(rows).filter(row => {
    const inputTarifa = row.querySelector('input[name="horaTarifa"]');
    const inputTipo = row.querySelector('input[name="tipo"]');

    // ← Ignora filas sin data-id (nueva tarifa oculta)
    if(!inputTarifa?.dataset.id) return false;
    if(!inputTarifa || !inputTipo) return false;

    const id = inputTarifa.dataset.id;
    const original = datosTarifas[id];

    return parseFloat(inputTarifa.value) !== original?.horaTarifa || inputTipo.value.trim() !== original?.tipo;
  }).map(row => ({
    id: row.querySelector('input[name="horaTarifa"]').dataset.id,
    tipo: row.querySelector('input[name="tipo"]').value.trim().toLowerCase(),
    horaTarifa: parseFloat(row.querySelector('input[name="horaTarifa"]').value)
  }));

  // Enviar al servidor
  try {
    const url = `${API_URL}/tarifas/actualizar`;
    const respuesta = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(tarifasModificadas)
    });

    const resultado = await respuesta.json();

    if(resultado.resultado) {
      mostrarAlertas({ exito: [resultado.mensaje]});
      capturarTarifasOriginales();
    } else {
      resultado.alertas ? mostrarAlertas(resultado.alertas) : mostrarAlertas({ error: [resultado.mensaje]})
    }
  } catch (error) {
    console.error('Error al actualizar tarifas:', error);
    mostrarAlertas({ error: ['Error de conexión con el servidor'] });
  }
}

if(btnAgregarTarifa) {
  btnAgregarTarifa.addEventListener('click', () => {
    formularioVisible = !formularioVisible;
    nuevaTarifaForm.style.display = formularioVisible ? 'block' : 'none';
    btnAgregarTarifa.textContent = formularioVisible ? '✕ Cancelar' : '+ Agregar tarifa';

    if(formularioVisible) {
      document.querySelector('#nuevoTipo').focus();
    } else {
      document.querySelector('#nuevoTipo').value = '';
      document.querySelector('#nuevaTarifaInput').value = '';
    }
  });
}

async function crearTarifa() {
  const tipo = document.querySelector('#nuevoTipo').value.trim();
  const horaTarifa = document.querySelector('#nuevaTarifaInput').value;

 // Validación en cliente
 const errores = [];
 if(!tipo) errores.push('El tipo de tarifa es obligatorio');
 if(!horaTarifa) errores.push('La tarifa es obligatoria');
 if(parseFloat(horaTarifa) <= 0) errores.push('La tarifa debe ser mayor a 0');

 if(errores.length > 0) {
    mostrarAlertas({ error: errores });
    return false;
  }

  const datos = new FormData();
  datos.append('tipo', tipo.toLowerCase());
  datos.append('horaTarifa', horaTarifa);

  try {
    const url = `${API_URL}/tarifas/crear`;
    const respuesta = await fetch(url, {
      method: 'POST',
      body: datos
    });
    
    const resultado = await respuesta.json();

    if(resultado.resultado) {
      mostrarAlertas({ exito: [resultado.mensaje] });
      agregarTarifaAlDOM(resultado.tarifa);

      // Limpia y oculta el formulario
      document.querySelector('#nuevoTipo').value = '';
      document.querySelector('#nuevaTarifaInput').value = '';
      nuevaTarifaForm.style.display = 'none';
      btnAgregarTarifa.textContent = '+ Agregar tarifa';
      formularioVisible = false;
    } else {
      resultado.alertas ? mostrarAlertas(resultado.alertas) : mostrarAlertas({ error: [resultado.mensaje] });
    }
  } catch (error) {
    console.error('Error al crear tarifa:', error);
    mostrarAlertas({ error: ['Error de conexión con el servidor'] });
  }
}

function agregarTarifaAlDOM(tarifa) {
  const div = document.createElement('DIV');
  div.classList.add('tarifa-row');
  div.innerHTML = `
    <div class="input-tipo">
      <input
        type="text"
        name="tipo"
        data-id="${tarifa.id}"
        value="${tarifa.tipo}"
      >
    </div>
    <div class="input-tarifa">
      <span>$</span>
      <input
        type="number"
        name="horaTarifa"
        data-id="${tarifa.id}"
        min="0"
        value="${tarifa.horaTarifa}"
      >
    </div>
    <button
      type="button"
      class="btn-desactivar-tarifa"
      data-id="${tarifa.id}"
      data-activo="1"
    >
      Desactivar
    </button>
  `;

  // Inserta antes del formulario de nueva tarifa
  nuevaTarifaForm.parentNode.insertBefore(div, nuevaTarifaForm);

  // Registra en originales para que la comparación funcione
  datosTarifas[tarifa.id] = {
    tipo: tarifa.tipo,
    horaTarifa: parseFloat(tarifa.horaTarifa)
  }
}

async function desactivarTarifa(e) {
  const btn = e.target.closest('.btn-desactivar-tarifa');
  if(!btn) return;

  const id = btn.dataset.id;
  console.log(id);
  const datos = new FormData();
  datos.append('id', id);

  try {
    const url = `${API_URL}/tarifas/toggle`;
    const respuesta = await fetch(url, {
      method: 'POST',
      body: datos
    });

    const resultado = await respuesta.json();
    console.log(resultado)
    if(resultado.resultado) {
      mostrarAlertas({ exito: [resultado.mensaje]});

      btn.dataset.activo = resultado.activo;
      btn.textContent = resultado.activo ? 'Desactivar' : 'Activar';

      const row = btn.closest('.tarifa-row');
      row.classList.toggle('tarifa-inactiva', !resultado.activo);
    } else {
      mostrarAlertas({ error: [resultado.mensaje] });
    }
  } catch (error) {
    console.error('Error al cambiar estado de tarifa:', error);
    mostrarAlertas({ error: ['Error de conexión con el servidor'] });
  }
}