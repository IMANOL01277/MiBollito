<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Usuario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #2575fc, #6a11cb);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .registro-card {
      background: #fff;
      border-radius: 15px;
      padding: 2rem;
      width: 100%;
      max-width: 450px;
      box-shadow: 0 0 20px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>

<div class="registro-card">
  <h3 class="text-center mb-4">Crear cuenta</h3>

  <?php if (isset($_GET['mensaje'])): ?>
    <div class="alert alert-success text-center"><?= htmlspecialchars($_GET['mensaje']) ?></div>
  <?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger text-center"><?= htmlspecialchars($_GET['error']) ?></div>
  <?php endif; ?>

  <form action="guardar_registro.php" method="POST">
    <div class="mb-3">
      <label for="nombre" class="form-label">Nombre completo</label>
      <input type="text" class="form-control" id="nombre" name="nombre" required>
    </div>
    <div class="mb-3">
      <label for="correo" class="form-label">Correo electrónico</label>
      <input type="email" class="form-control" id="correo" name="correo" required>
    </div>
    <div class="mb-3">
      <label for="contraseña" class="form-label">Contraseña</label>
      <input type="password" class="form-control" id="contraseña" name="contraseña" required>
    </div>
    <button type="submit" class="btn btn-success w-100">Registrarse</button>
  </form>

  <p class="text-center mt-3">
    ¿Ya tienes cuenta?  
    <a href="login.php" class="text-decoration-none fw-bold text-primary">Inicia sesión</a>
  </p>
</div>

</body>
</html>
