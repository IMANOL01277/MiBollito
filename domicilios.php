<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Domicilios - Mi Bollito</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light" style="background-color: #f8f9fa;">

  <div class="container mt-5">
    <div class="row">
      
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h1 class="text-center mb-4">🚚 Módulo de Domicilios</h1>
            <a href="index.php" class="btn btn-secondary mb-3">⬅ Volver</a>

            <form>
              <div class="mb-3">
                <label for="nombreConductor" class="form-label">Nombre del conductor</label>
                <input type="text" class="form-control" id="nombreConductor" placeholder="Ingrese el nombre del conductor">
              </div>
              <div class="mb-3">
                <label for="fechaPedido" class="form-label">Fecha de pedido</label>
                <input type="date" class="form-control" id="fechaPedido">
              </div>
              <div class="mb-3">
                <label for="numeroMatricula" class="form-label">Número de matrícula</label>
                <input type="text" class="form-control" id="numeroMatricula" placeholder="Ingrese el número de matrícula">
              </div>

              <div class="mb-3">
                <label for="cantidad" class="form-label">Cantidad</label>
                <input type="number" class="form-control" id="cantidad" placeholder="Cantidad">
              </div>
              <div class="mb-3">
                <label for="tipo" class="form-label">Tipo</label>
                <input type="text" class="form-control" id="tipo" placeholder="Tipo">
              </div>
              <div class="mb-3">
                <label for="idProducto" class="form-label">ID Producto</label>
                <div class="input-group">
                  <input type="text" class="form-control" id="idProducto" placeholder="ID Producto">
                  <button class="btn btn-outline-secondary" type="button">+</button>
                </div>
              </div>

              <div class="text-center">
                <button type="submit" class="btn btn-primary">Guardar</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      
      <div class="col-md-6">
        <div class="text-center">
          <img src="img/logo_mibollito.png" alt="" class="img-fluid" style="max-width: 300px;">
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>