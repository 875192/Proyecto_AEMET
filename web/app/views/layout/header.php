<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= Vista::e($tituloPagina) ?></title>
  <link rel="stylesheet" href="assets/vendor/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body class="d-flex flex-column min-vh-100">

<!-- CABECERA -->
<header>
  <nav class="navbar navbar-expand navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="index.php"><?= Vista::e(Config::get('app.nombre')) ?></a>
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
        <?php if (Sesion::estaIdentificado()): ?>
          <li class="nav-item"><span class="navbar-text me-3"><?= Vista::e(Sesion::usuario()['nick']) ?></span></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar sesión</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>
</header>

<!-- CUERPO -->
<main class="container flex-grow-1 py-4">
  <?php if (!empty($aviso)): ?>
    <div class="alert alert-warning" role="alert"><?= Vista::e($aviso) ?></div>
  <?php endif; ?>
