<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión - Mi Bollito</title>
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
      max-width: 450px;
      box-shadow: 0 0 20px rgba(0,0,0,0.2);
      position: relative;
    }
  </style>
</head>
<body>

<div class="login-card">
  <h3 class="text-center mb-4">Iniciar Sesión</h3>

  <!-- Mensajes de éxito o error -->
  <?php if (isset($_GET['mensaje'])): ?>
    <div id="alerta" class="alert alert-success text-center">
      <?= htmlspecialchars($_GET['mensaje']) ?>
    </div>
  <?php elseif (isset($_GET['error'])): ?>
    <div id="alerta" class="alert alert-danger text-center">
      <?= htmlspecialchars($_GET['error']) ?>
    </div>
  <?php endif; ?>

  <form action="validar_login.php" method="POST">
    <div class="mb-3">
      <label for="correo" class="form-label">Correo electrónico</label>
      <input type="email" class="form-control" id="correo" name="correo" required>
    </div>
    <div class="mb-3">
      <label for="contraseña" class="form-label">Contraseña</label>
      <input type="password" class="form-control" id="contraseña" name="contraseña" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
  </form>

  <p class="text-center mt-3">
    ¿No tienes cuenta?
    <a href="registro.php" class="text-decoration-none fw-bold text-primary">Regístrate aquí</a>
  </p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Desvanecer el mensaje automáticamente
  const alerta = document.getElementById('alerta');
  if (alerta) {
    setTimeout(() => {
      alerta.style.transition = "opacity 0.5s ease";
      alerta.style.opacity = "0";
      setTimeout(() => alerta.remove(), 500); // Se elimina del DOM
    }, 3000); // ⏳ 3 segundos antes de desaparecer
  }
</script>

</body>
</html>
