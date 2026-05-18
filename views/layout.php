<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Parking App</title>

  <link rel="icon" href="data:,">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="<?= base_url('/build/css/app.css') ?>">
</head>
<body>

  <?php 
    // Le avisa que $contenido viene de afuera
    /** @var string $contenido */ 
    echo $contenido; 
  ?>

  <?php echo $script ?? ''; ?>
  
</body>
</html>