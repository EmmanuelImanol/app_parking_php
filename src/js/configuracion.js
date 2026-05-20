import { mostrarAlertas } from "./utils.js";

const formEstacionamiento = document.querySelector('#formEstacionamiento');
const formTarifas = document.querySelector('#formTarifas');

let datosConfiguracion = {};
let datosTarifas = {};

if(formEstacionamiento) {
  formEstacionamiento.addEventListener('submit', actualizarDatosConfiguracion);
}

if(formTarifas) {
  formTarifas.addEventListener('submit', actualizarTarifa);
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