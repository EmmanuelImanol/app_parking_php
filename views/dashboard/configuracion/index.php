<?php
  /** @var string $titulo */
  /** @var array $tarifas */
  /** @var string $script */
?>

<div class="contenedor">
  <h1><?= $titulo ?></h1>
  <form class="formulario-configuracion">
    <div class="formulario-configuracion__header">
      <h2>Mi Estacionamiento</h2>
      <p>Aqui puedes modificar la información de tu estacionamiento</p>
    </div>
    <div class="formulario-configuracion__body">
      <div>
        <label for="nombre-sucursal">Nombre Sucursal:</label>
        <input type="text" id="nombre-sucursal" name="nombreSucursal">
      </div>
      <div>
        <label for="direccion-estacionamiento">Dirección:</label>
        <textarea 
          name="direccionEstacionamiento" 
          id="direccion-estacionamiento"
        ></textarea>
      </div>
      <div>
        <label for="regimen-fiscal">Regimen Fiscal:</label>
        <input type="text" id="regimen-fiscal" name="regimenFiscal">
      </div>
      <div>
        <label for="representacion-legal">Representación Legal:</label>
        <input type="text" id="representacion-legal" name="representacionLegal">
      </div>
      <div>
        <label for="rfc">RFC:</label>
        <input type="text" id="rfc" name="rfc">
      </div>
      <div>
        <label for="telefono-contacto">Telefono:</label>
        <input type="text" id="telefono-contacto" name="telefonoContacto">
      </div>
    </div> <!-- .formulario-configuracion__body -->

    <div class="formulario-configuracion__footer">
      <button type="submit" class="btn-configuracion">Guardar Cambios</button>
    </div>
  </form> <!-- .formulario-configuracion -->

  <form class="formulario-configuracion">
    <div class="formulario-configuracion__header">
      <h2>Tarifas</h2>
      <p>Aqui puedes modificar las tarifas de tu estacionamiento</p>
    </div>
    <div class="formulario-configuracion__body">
      <?php foreach($tarifas as $tarifa): ?>
        <div class="tarifa-row">
          <div>
            <input 
              type="text"
              id="tipoTarifa-<?= $tarifa->id ?>"
              name="tipo"
              value="<?= htmlspecialchars($tarifa->tipo) ?>"
            >
          </div>
          <div>
            <input 
              type="text"
              id="horaTarifa-<?= $tarifa->id ?>"
              name="horaTarifa"
              value="<?= htmlspecialchars($tarifa->horaTarifa) ?>"
            >
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="formulario-configuracion__footer">
      <button type="submit" class="btn-configuracion">Guardar Cambios</button>
    </div>
  </form>
</div> <!-- .contenedor -->

<?php 
  $base_url_app = base_url('/build/js/app.js');
  $base_url_cobroporhora = base_url('/build/js/configuracion.js');
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