<?php /** @var string $titulo */ ?>

<div class="contenedor">
  <h1 class="titulo-pagina"><?= $titulo ?></h1>

  <div class="contenedor-botones">
    <a class="btn-accion-rapida" href="<?= base_url('/dashboard/cobroporhora') ?>">Cobro Por Hora</a>
    <a class="btn-accion-rapida" href="#">Cobro Por Turno</a>
    <a class="btn-accion-rapida" href="#">Convenios</a>
    <a class="btn-accion-rapida" href="#">Pensiones</a>
  </div>

  <div>
    <h2>Cobro Por Hora</h2>
    <p>Reportes de cobros por hora</p>
    <div>
      <p>Cantidad</p>
      <span>$0.00</span>
    </div>
  </div>
</div>