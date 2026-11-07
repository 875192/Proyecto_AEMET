<h1 class="h3 mb-3">Inicio</h1>

<?php if ($error !== null): ?>
  <div class="alert alert-danger" role="alert"><?= Vista::e($error) ?></div>
<?php else: ?>
  <div class="alert alert-success" role="alert">
    Conexión con la base de datos correcta: <?= count($ciudades) ?> ciudades y
    <?= number_format($lecturas, 0, ',', '.') ?> lecturas diarias cargadas.
  </div>

  <table class="table table-striped table-sm">
    <thead>
      <tr><th>Ciudad</th><th>Estación AEMET</th><th>Altitud (m)</th></tr>
    </thead>
    <tbody>
      <?php foreach ($ciudades as $c): ?>
        <tr>
          <td><?= Vista::e($c['nombre']) ?></td>
          <td><?= Vista::e($c['indicativo']) ?></td>
          <td><?= Vista::e($c['altitud'] ?? '—') ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<p class="text-muted small">Página de comprobación temporal: se sustituirá por el inicio de sesión.</p>
