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
      <p id="modal-tipo" class="modal-subtitulo"></p>
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

      <div class="chip">
        <div class="chip-label">Tipo</div>
        <p id="modal-tipo" class="modal-subtitulo"></p>
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


<?php 
  $base_url_app = base_url('/build/js/app.js');
  $base_url_cobroporhora = base_url('/build/js/cobroporhora.js');
  $script .= "
    <script> 
      const API_URL = '" . $_ENV['API_URL'] . "';
    </script>
    <script src='{$base_url_app}'></script>
    <script src='{$base_url_cobroporhora}' type='module'></script>
  "; 
?>