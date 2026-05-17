<?php 
  /** @var string $titulo */ 
  /** @var array $tarifas */ 
  /** @var string $script */ 
?>

<div class="contenedor">
  <?php include_once __DIR__ . '/../../templates/alertas.php'; ?>
  <h1><?= $titulo ?></h1>

  <form class="formulario-entrada" id="formEntrada" novalidate>
    <div class="campo">
      <label for="placa">Placa del vehiculo: </label>
      <input 
        type="text"
        id="placa"
        name="placa"
        placeholder="Ej. ABC-123"
        class="uppercase"
        maxlength="11"
      >
    </div>
    <div class="campo">
      <label for="observaciones">Observaciones:</label>
      <input 
        type="text"
        id="observaciones"
        name="observaciones"
        placeholder="Ej. Versa Negro"
      >
    </div>
    <div class="campo">
      <label for="tipoVehiculo">Tipo vehículo:</label>
      <div class="radios">
        <?php foreach($tarifas as $tarifa): ?>
          <label class="radio-option">
            <input 
              type="radio"
              name="tipoVehiculo"
              id="tipoVehiculo"
              value="<?= $tarifa->tipo ?>"
              <?= $tarifa->tipo === 'auto' ? 'checked' : '' ?>
            >
            <span><?= ucfirst($tarifa->tipo) ?></span>
            <small>$<?= $tarifa->horaTarifa ?></small>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
    <input type="submit" class="submit-registrar-entrada" value="Registrar Entrada">
  </form>

  <div class="contenedor-registros" id="registros-entrada"></div>
</div>

<div id="modal-registros" class="modal-registros-contenedor">
  <div class="modal-contenido">

    <div class="modal-header">
      <div class="icono-ring">
        <i class="fa-solid fa-car"></i>
      </div>
      <h2 id="modal-placa" class="modal-placa"></h2>
      <p id="modal-tipo" class="modal-subtitulo capitalize"></p>
    </div>

    <div class="modal-chips">
      <div class="chip chip--ingreso">
        <div class="chip-label"><i class="fa-solid fa-arrow-right-to-bracket"></i>Ingreso</div>
        <p id="modal-entrada" class="chip-valor"></p>
      </div>

      <div class="chip chip--observaciones">
        <div class="chip-label"><i class="ti ti-note"></i>Observaciones</div>
        <p id="modal-observaciones" class="chip-valor"></p>
      </div>

      <div class="chip chip--monto">
        <div class="chip-label">Total</div>
        <p id="modal-total" class="chip-valor"></p>
      </div>
    </div>

    <div class="contenedor-botones">
      <button type="button" id="modal-cerrar" class="btn-modal-cerrar">Cerrar</button>
      <button type="button" id="modal-cobrar" class="btn-modal-cobrar">Cobrar</button>
    </div>

  </div>
</div>

<div id="modalTicket" class="modal-ticket-contenedor hidden">
  <div class="modal-ticket">

    <div class="ticket-header">
      <div class="ticket-header-texto">
        <p id="ticketSucursal">—</p>
        <p id="ticketDireccion">—</p>
        <span id="ticketTipoBadge" class="ticket-tipo-badge">—</span>
      </div>
    </div>

    <div class="ticket-seccion">
      <p class="ticket-seccion-titulo">Datos fiscales</p>
      <div class="ticket-row">
        <span class="lbl">RFC</span>
        <span class="val" id="ticketRFC">—</span>
      </div>
      <div class="ticket-row">
        <span class="lbl">Régimen fiscal</span>
        <span class="val" id="ticketRegimen">—</span>
      </div>
      <div class="ticket-row">
        <span class="lbl">Rep. legal</span>
        <span class="val" id="ticketRepLegal">—</span>
      </div>
      <div class="ticket-row">
        <span class="lbl">Teléfono</span>
        <span class="val" id="ticketTelefono">—</span>
      </div>
    </div>

    <hr class="ticket-divisor">

    <div class="ticket-seccion">
      <p class="ticket-seccion-titulo">Folio</p>
      <div class="ticket-row">
        <span class="lbl">No. folio</span>
        <span class="val mono" id="ticketFolio">—</span>
      </div>
    </div>

    <hr class="ticket-divisor">

    <div class="ticket-seccion">
      <p class="ticket-seccion-titulo">Vehículo</p>
      <div class="ticket-row">
        <span class="lbl">Placa</span>
        <span class="val" id="ticketPlaca">—</span>
      </div>
      <div class="ticket-row">
        <span class="lbl">Tipo de auto</span>
        <span class="val" id="ticketTipoAuto">—</span>
      </div>
      <div class="ticket-row">
        <span class="lbl">Observaciones</span>
        <span class="val" id="ticketObservaciones">—</span>
      </div>
      <div class="ticket-row">
        <span class="lbl">Entrada</span>
        <span class="val" id="ticketEntrada">—</span>
      </div>
      <div class="ticket-row">
        <span class="lbl">Salida</span>
        <span class="val" id="ticketSalida">—</span>
      </div>
      <div class="ticket-row">
        <span class="lbl">Tiempo</span>
        <span class="val" id="ticketTiempo">—</span>
      </div>
      <div class="ticket-row">
        <span class="lbl">Tarifa</span>
        <span class="val" id="ticketTarifa">—</span>
      </div>
    </div>

    <hr class="ticket-divisor">

    <div class="ticket-qr">
      <div id="ticketQR"></div>
      <p class="ticket-qr-label">Código QR de verificación</p>
      <p class="ticket-qr-codigo" id="ticketCodigoQR">—</p>
    </div>

    <div class="ticket-total">
      <span class="total-label">Total pagado</span>
      <span class="total-valor" id="ticketTotal">—</span>
    </div>

    <div class="ticket-pie">
      <p>— SIN VALIDEZ FISCAL —</p>
    </div>

    <div class="ticket-acciones">
      <button id="ticketBtnImprimir">🖨 Imprimir</button>
      <button id="ticketBtnCerrar">✕ Cerrar</button>
    </div>

  </div>
</div>


<?php 
  $base_url_app = base_url('/build/js/app.js');
  $base_url_cobroporhora = base_url('/build/js/cobroporhora.js');
  $base_url_utils = base_url('/build/js/utils.js');
  $script .= "
    <script> 
      const API_URL = '" . $_ENV['API_URL'] . "';
    </script>
    <script src='{$base_url_app}'  type='module'></script>
    <script src='{$base_url_cobroporhora}' type='module'></script>
    <script src='{$base_url_utils}' type='module'></script>
  "; 
?>