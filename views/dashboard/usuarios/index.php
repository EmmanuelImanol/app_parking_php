<?php 
  /** @var string $titulo */ 
  /** @var object $usuario */ 
  /** @var array $usuarios */ 
  /** @var string $script */ 
?>

<div class="contenedor">
  <h1 class="titulo-pagina"><?= $titulo ?></h1>
  <button 
    type="button"
    class="btn-crear"
  >
    <i class="fa-solid fa-user-plus"></i>
    Crear usuario
  </button>
  <?php include_once __DIR__ . '/../../templates/alertas.php'; ?>
  <div class="contenedor-usuarios" id="usuarios"></div>
</div>

<!-- Modal -->
<div class="modal">
  <div class="modal-contenido">
    <button 
      type="button"
      class="cerrar-modal"
    >
      <i class="fa-regular fa-circle-xmark"></i>
    </button>

    <h2 class="modal-titulo">Crear Usuario</h2>

    <form class="form-usuario" novalidate>
      <input type="hidden" class="modal-id">
      <div class="campo">
        <label for="nombre">Nombre:</label>
        <input 
          type="text" 
          name="nombre" 
          id="nombre"
          placeholder="Nombre del usuario"
        >
      </div>
      <div class="campo">
        <label for="email">Email:</label>
        <input 
          type="email" 
          name="email" 
          id="email"
          placeholder="ej. correo@parking.com"
        >
      </div>

      <!-- Solo visible al crear -->
      <div class="campo campo-password">
        <label for="password">Contraseña:</label>
        <input 
          type="password" 
          name="password" 
          id="password"
        >
      </div>

      <div class="campo campo-password">
        <label for="password_confirm">Repetir contraseña:</label>
        <input 
          type="password" 
          name="password_confirm" 
          id="password_confirm"
        >
      </div>

      <input 
        type="submit" 
        value="Crear Usuario"
        class="modal-btn"
      >
    </form>
  </div>
</div>

<div id="modal-confirmar" class="modal-confirmar">
  <div class="modal-confirmar__contenido">
    <h3>¿Eliminar usuario?</h3>
    <p>Esta acción no se puede deshacer.</p>
    <div class="modal-confirmar__botones">
      <button class="btn-cancelar">Cancelar</button>
      <button class="btn-confirmar">Sí, eliminar</button>
    </div>
  </div>
</div>

<?php 
  $base_url_app = base_url('/build/js/app.js');
  $base_url_usuarios = base_url('/build/js/usuarios.js');
  $script .= "
    <script src='{$base_url_app}'></script>
    <script src='{$base_url_usuarios}'></script>
  "; 
?>