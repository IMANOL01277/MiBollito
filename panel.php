<?php include("includes/header.php"); ?>

<h3 class="mb-4"><i class="bi bi-speedometer"></i> Bienvenido al Panel</h3>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card p-3 shadow-sm text-center">
      <i class="bi bi-box-seam fs-1 text-warning"></i>
      <h5 class="mt-2">Inventario</h5>
      <a href="inventario.php" class="btn btn-outline-warning btn-sm mt-2">Entrar</a>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3 shadow-sm text-center">
      <i class="bi bi-graph-up fs-1 text-success"></i>
      <h5 class="mt-2">Estadísticas</h5>
      <a href="estadisticas.php" class="btn btn-outline-success btn-sm mt-2">Ver</a>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3 shadow-sm text-center">
      <i class="bi-bicycle fs-1 text-success"></i>
      <h5 class="mt-2">Domicilios</h5>
      <a href="domicilios.php" class="btn btn-outline-success btn-sm mt-2">Entrar</a>
    </div>
  </div>
  <?php if ($_SESSION['rol'] === 'administrador'): ?>
  <div class="col-md-4">
    <div class="card p-3 shadow-sm text-center">
      <i class="bi bi-person fs-1 text-primary"></i>
      <h5 class="mt-2">Usuarios</h5>
      <a href="usuarios.php" class="btn btn-outline-primary btn-sm mt-2">Administrar</a>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3 shadow-sm text-center">
      <i class="bi bi-bookmark fs-1 text-primary"></i>
      <h5 class="mt-2">Categorias</h5>
      <a href="categorias.php" class="btn btn-outline-primary btn-sm mt-2">Administrar</a>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3 shadow-sm text-center">
      <i class="bi bi-people fs-1 text-primary"></i>
      <h5 class="mt-2">Proveedores</h5>
      <a href="proveedores.php" class="btn btn-outline-primary btn-sm mt-2">Administrar</a>
    </div>
  </div>
    <div class="col-md-4">
    <div class="card p-3 shadow-sm text-center">
      <i class="bi bi-truck fs-1 text-primary"></i>
      <h5 class="mt-2">Vendedores</h5>
      <a href="vendedores.php" class="btn btn-outline-primary btn-sm mt-2">Administrar</a>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php include("includes/footer.php"); ?>
