<?php 
  /** @var string $titulo */ 
  /** @var string $contenido */ 
  /** @var string $script */ 
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>App Parking - <?= $titulo ?></title>

  <link rel="icon" href="data:,">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link rel="stylesheet" href="<?= base_url('/build/css/app.css') ?>">
</head>
<body class="dashboard">

  <div class="dashboard__grid">
    <?php include_once __DIR__ . '/templates/dashboard-sidebar.php'; ?>

    <main class="dashboard__contenido">
      <?php echo $contenido; ?>
    </main>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <?php echo $script ?? ''; ?>
  
</body>
</html>