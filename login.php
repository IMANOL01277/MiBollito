<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-card {
      background: #fff;
      border-radius: 15px;
      padding: 2rem;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 0 20px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>

<div class="login-card">
  <h3 class="text-center mb-4">Iniciar Sesión</h3>

  <!-- ✅ Mensaje de éxito si viene desde el registro -->
  <?php if (isset($_GET['mensaje'])): ?>
    <div class="alert alert-success text-center"><?= htmlspecialchars($_GET['mensaje']) ?></div>
  <?php endif; ?>

  <!-- ❌ Mensaje de error si hay problema al iniciar -->
  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger text-center"><?= htmlspecialchars($_GET['error']) ?></div>
  <?php endif; ?>

  <!-- 🧾 Formulario de inicio de sesión -->
  <form action="validar_login.php" method="POST">
    <div class="mb-3">
      <label for="correo" class="form-label">Correo electrónico</label>
      <input type="email" class="form-control" id="correo" name="correo" placeholder="Ingrese su correo" required>
    </div>
    <div class="mb-3">
      <label for="contraseña" class="form-label">Contraseña</label>
      <input type="password" class="form-control" id="contraseña" name="contraseña" placeholder="Ingrese su contraseña" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Entrar</button>
  </form>

  <p class="text-center mt-3">
    ¿No tienes una cuenta?  
    <a href="registro.php" class="text-decoration-none fw-bold text-primary">Registrarse</a>
  </p>
</div>

</body>
</html>
