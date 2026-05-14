import { mostrarAlertas } from "./utils.js";

const btnRegistrarEntrada = document.querySelector('.submit-registrar-entrada');
const formEntrada = document.querySelector('#formEntrada');
const registrosEntrada = document.querySelector('#registros-entrada');
const modalRegistros = document.querySelector('#modal-registros');
const modalRegistrosContenedor = document.querySelector('.modal-registros-contenedor');
const btnModalCerrar = document.querySelector('#modal-cerrar');
const btnModalCobrar = document.querySelector('#modal-cobrar');

let tarifas = {};
let vehiculos = [];
let vehiculoId = null;

if(modalRegistros) {
  registrosEntrada.addEventListener('click', (e) => {
    const tarjeta = e.target.closest('.vehiculo-entrada');
    if(!tarjeta) return;
    const id = Number(tarjeta.dataset.id);
    const vehiculo = vehiculos.find(v => v.id === id);
    abrirModal(vehiculo);
  })
}

function abrirModal(vehiculo) {
  const { id, placa, observaciones, tipoVehiculo, horaEntrada } = vehiculo;

  vehiculoId = id;

  // calcular total
  const ahora = new Date();
  const entrada = new Date(horaEntrada);
  const horas = Math.ceil((ahora - entrada) / (1000 * 60 * 60));
  const tarifa = tarifas[tipoVehiculo.toLowerCase()] ?? 0;
  const total = horas * tarifa;

  // Llenar el modal
  modalRegistros.querySelector('#modal-placa').textContent = placa;
  modalRegistros.querySelector('#modal-tipo').textContent = tipoVehiculo;
  modalRegistros.querySelector('#modal-observaciones').textContent = observaciones;
  modalRegistros.querySelector('#modal-entrada').textContent = formatearFecha(horaEntrada);
  modalRegistros.querySelector('#modal-total').textContent = `$${total}`;

  modalRegistrosContenedor.classList.add('visible');
}

if(btnModalCerrar) {
  btnModalCerrar.addEventListener('click', cerrarModal);
}
if(btnModalCobrar) {
  btnModalCobrar.addEventListener('click', cobrarSalida);
}

function cerrarModal() {
  modalRegistrosContenedor.classList.remove('visible');
  vehiculoId = null;
}

async function cobrarSalida() {
  if(!vehiculoId) return;
  const datos = new FormData();
  datos.append('id', vehiculoId);

  try {
    const url = `${API_URL}/cobroporhora/salida`;
    const respuesta = await fetch(url, {
      method: 'POST',
      body: datos
    });

    const resultado = await respuesta.json();
    
    if(resultado.resultado) {
      mostrarAlertas({ exito: [resultado.mensaje] });
      cerrarModal();
      consultarAPI();
    } else {
      mostrarAlertas({ error: [resultado.mensaje] });
    }
  } catch (error) {
    console.log(error)
  }
}

if(btnRegistrarEntrada) {
  btnRegistrarEntrada.addEventListener('click', (e) => {
    e.preventDefault();
    submitRegistrarEntrada();
  })
}


function submitRegistrarEntrada() {
  const placa = document.querySelector('#placa').value.trim();
  const observaciones = document.querySelector('#observaciones').value.trim();
  const tipoVehiculoInput = document.querySelector('input[name="tipoVehiculo"]:checked');

  if(placa === '' || observaciones === '' || !tipoVehiculoInput) {
    mostrarAlertas({ error: ['Todos los campos son obligatorios'] })
    return;
  }
  
  const vehiculo = {
    placa,
    observaciones,
    tipoVehiculo: tipoVehiculoInput.value
  };

  entradaVehiculo(vehiculo);
}

async function obtenerTarifas() {
  try {
    const url = `${API_URL}/tarifas`;
    const respuesta = await fetch(url);
    const datos = await respuesta.json();
    
    datos.forEach(tarifa => {
      tarifas[tarifa.tipo] = parseFloat(tarifa.horaTarifa);
    });
    
  } catch (error) {
    console.log(error);
  }
}

async function consultarAPI() {
  try {
    const url = `${API_URL}/cobroporhora`;
    const respuesta = await fetch(url);
    const vehiculos = await respuesta.json();
    mostrarVehiculos(vehiculos);
  } catch (error) {
    console.log(error);
  }
}

function mostrarVehiculos(data) {
  vehiculos = data;
  registrosEntrada.innerHTML = '';
  data.forEach(vehiculo => {
    const { id, placa, observaciones, tipoVehiculo, horaEntrada } = vehiculo;

    const cronometroDisplay = document.createElement('P');
    cronometroDisplay.classList.add('cronometro');
    cronometroDisplay.textContent = 'Calculando Tiempo...'

    const placaVehiculo = document.createElement('P');
    placaVehiculo.classList.add('placa');
    placaVehiculo.textContent = placa;

    const observacionesVehiculo = document.createElement('P');
    observacionesVehiculo.textContent = observaciones;

    const vehiculoTipo = document.createElement('P');
    vehiculoTipo.textContent = tipoVehiculo;

    const horaEntradaVehiculo = document.createElement('P');
    horaEntradaVehiculo.textContent = formatearFecha(horaEntrada);
  
    const vehiculoDIV = document.createElement('DIV');
    vehiculoDIV.classList.add('vehiculo-entrada');
    vehiculoDIV.dataset.id = id;
  
    vehiculoDIV.appendChild(placaVehiculo);
    vehiculoDIV.appendChild(cronometroDisplay);
    vehiculoDIV.appendChild(observacionesVehiculo);
    vehiculoDIV.appendChild(vehiculoTipo);
    vehiculoDIV.appendChild(horaEntradaVehiculo);
    
    registrosEntrada.appendChild(vehiculoDIV);

    iniciarCronometro(horaEntrada, cronometroDisplay);
  });
}

async function entradaVehiculo(vehiculo) {
  const { placa, observaciones, tipoVehiculo } = vehiculo;
  // construir la petición
  const datos = new FormData();
  datos.append('placa', placa);
  datos.append('observaciones', observaciones);
  datos.append('tipoVehiculo', tipoVehiculo);

  try {
    const url = `${API_URL}/cobroporhora/entrada`;
    const respuesta = await fetch(url, {
      method: 'POST',
      body: datos
    });
    
    const resultado = await respuesta.json();
    if(resultado.resultado) {
      mostrarAlertas({ exito: [resultado.mensaje]});
      registrosEntrada.innerHTML = '';
      consultarAPI();
      formEntrada.reset();
    } else {
      mostrarAlertas({ error: [resultado.mensaje]});
      formEntrada.reset();
    }
  } catch (error) {
    console.log(error);
  }
}

function formatearFecha(fechaEntrada) {
  const fecha = new Date(fechaEntrada);
  return new Intl.DateTimeFormat('es-MX', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  }).format(fecha);
}

function iniciarCronometro(horaEntrada, elementoID) {
  const entrada = new Date(horaEntrada).getTime();

  // Actualizar cada 1 segundo
  const intervalo = setInterval(() => {
    const ahora = new Date().getTime();
    const diferencia = ahora - entrada;

    const horas = Math.floor(diferencia / (1000 * 60 * 60));
    const minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));
    const segundos = Math.floor((diferencia % (1000 * 60)) / 1000);

    const tiempoFormateado = `${String(horas).padStart(2, '0')}:${String(minutos).padStart(2, '0')}:${String(segundos).padStart(2, '0')}`;

    if(document.body.contains(elementoID)) {
      elementoID.textContent = `${tiempoFormateado}`;
    } else {
      clearInterval(intervalo);
    }
  }, 1000);
}

if(registrosEntrada.lenght) {
  consultarAPI();
  obtenerTarifas();
}
