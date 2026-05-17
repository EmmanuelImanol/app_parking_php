let toastContenedor = document.querySelector('.toast-contenedor');

if(!toastContenedor) {
  toastContenedor = document.createElement('DIV');
  toastContenedor.classList.add('toast-contenedor');
  document.body.appendChild(toastContenedor);
}

function cerrarModalTicket() {
  document.querySelector('#modalTicket').style.display = 'none';
}

function generarQR(codigoQR) {
  return new Promise((resolve, reject) => {
    const contenedor = document.querySelector('#ticketQR');
    contenedor.innerHTML = '';
  
    if(!codigoQR) {
      resolve();
      return;
    };

    if(typeof QRCode === 'undefined') {
      contenedor.textContent = codigoQR;
      resolve();
      return;
    }

    try {
      new QRCode(contenedor, {
        text: codigoQR,
        width: 120,
        height: 120,
        colorDark: '#0f172a',
        colorLight: '#fff',
        correctLevel: QRCode.CorrectLevel.H
      });
      resolve();
    } catch (error) {
      reject(error);
    }

  });
}

// ── Listeners (se registran una sola vez al cargar el módulo) ──
document.querySelector('#ticketBtnImprimir').addEventListener('click', () => {
  window.print();
});

document.querySelector('#modalTicket').addEventListener('click', (e) => {
  if(e.target === document.querySelector('#ticketBtnCerrar')) cerrarModalTicket();
});

export async function mostrarTicket(datos) {
  document.getElementById('ticketSucursal').textContent  = datos.nombreSucursal    ?? '—';
  document.getElementById('ticketDireccion').textContent = datos.direccion          ?? '—';
  document.getElementById('ticketTipoBadge').textContent = datos.tipoTicket ?? '—';

  // Datos fiscales
  document.getElementById('ticketRFC').textContent       = datos.rfc               ?? '—';
  document.getElementById('ticketRegimen').textContent   = datos.regimenFiscal     ?? '—';
  document.getElementById('ticketRepLegal').textContent  = datos.representacionLegal ?? '—';
  document.getElementById('ticketTelefono').textContent  = datos.telefonoContacto  ?? '—';

  // Folio
  document.getElementById('ticketFolio').textContent     = datos.folio             ?? '—';

  // Vehículo
  document.getElementById('ticketPlaca').textContent     = datos.placa             ?? '—';
  document.getElementById('ticketTipoAuto').textContent  = datos.tipoVehiculo      ?? '—';
  document.getElementById('ticketObservaciones').textContent = datos.observaciones ?? 'Sin observaciones';
  document.getElementById('ticketEntrada').textContent   = formatearFecha(datos.horaEntrada);
  document.getElementById('ticketSalida').textContent    = formatearFecha(datos.horaSalida);
  document.getElementById('ticketTiempo').textContent    = datos.tiempo            ?? '—';
  document.getElementById('ticketTarifa').textContent    = datos.tarifa
    ? `$${parseFloat(datos.tarifa).toFixed(2)} / hr`
    : '—';
  // Total
  document.getElementById('ticketTotal').textContent     = datos.total
    ? `$${parseFloat(datos.total).toFixed(2)}`
    : '—';
  document.getElementById('ticketCodigoQR').textContent = datos.codigoQR ?? '—';
    
  document.getElementById('modalTicket').style.display = 'flex';

  try {
    await generarQR(datos.codigoQR);
  } catch (error) {
    console.log('Error al generar QR: ', error);
  }
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

export function formatearFecha(fechaEntrada) {
  if(!fechaEntrada) return '—';

  const fechaNormalizada = fechaEntrada.replace(' ', 'T');

  const fecha = new Date(fechaNormalizada);

  if(isNaN(fecha.getTime())) return '—';  // ← protección extra

  return new Intl.DateTimeFormat('es-MX', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  }).format(fecha);
}