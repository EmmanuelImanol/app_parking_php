import { mostrarAlertas } from "./utils.js";

const formulario = document.querySelector('.formulario-configuracion');
let datosConfiguracion = {};

if(formulario) {
  formulario.addEventListener('submit', actualizarDatosConfiguracion);
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

  const datosFormulario = new FormData(formulario);

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