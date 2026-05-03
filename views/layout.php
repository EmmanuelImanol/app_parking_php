<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Parking App</title>

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