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
    .progress {
      height: 8px;
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

  <form action="guardar_registro.php" method="POST" id="registroForm" novalidate>
    <div class="mb-3">
      <label for="nombre" class="form-label">Nombre completo</label>
      <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Juan Diaz" required>
    </div>

    <div class="mb-3">
      <label for="correo" class="form-label">Correo electrónico</label>
      <input type="email" class="form-control" id="correo" name="correo" placeholder="example@gmail.com" required>
    </div>

    <div class="mb-3">
      <label for="contraseña" class="form-label">Contraseña</label>
      <input type="password" class="form-control" id="contraseña" name="contraseña" required>
      <div class="progress mt-2">
        <div id="passwordStrength" class="progress-bar" role="progressbar" style="width: 0%"></div>
      </div>
      <ul class="small mt-2" id="passwordCriteria">
        <li id="length" class="text-danger">• Mínimo 8 caracteres</li>
        <li id="uppercase" class="text-danger">• Al menos una letra mayúscula</li>
        <li id="special" class="text-danger">• Al menos un carácter especial (!@#$%^&*)</li>
      </ul>
    </div>

    <div class="mb-3">
      <label for="confirmar" class="form-label">Confirmar contraseña</label>
      <input type="password" class="form-control" id="confirmar" name="confirmar" required>
      <div id="matchMessage" class="small mt-1 text-danger"></div>
    </div>

    <button type="submit" class="btn btn-success w-100 mt-2">Registrarse</button>
  </form>

  <p class="text-center mt-3">
    ¿Ya tienes cuenta?  
    <a href="login.php" class="text-decoration-none fw-bold text-primary">Inicia sesión</a>
  </p>
</div>

<script>
  const passwordInput = document.getElementById("contraseña");
  const confirmInput = document.getElementById("confirmar");
  const form = document.getElementById("registroForm");
  const matchMessage = document.getElementById("matchMessage");
  const strengthBar = document.getElementById("passwordStrength");
  const criteria = {
    length: document.getElementById("length"),
    uppercase: document.getElementById("uppercase"),
    special: document.getElementById("special")
  };

  // 🔹 Validación de seguridad en tiempo real
  passwordInput.addEventListener("input", () => {
    const password = passwordInput.value;
    let strength = 0;

    if (password.length >= 8) {
      criteria.length.classList.replace("text-danger", "text-success");
      strength++;
    } else {
      criteria.length.classList.replace("text-success", "text-danger");
    }

    if (/[A-Z]/.test(password)) {
      criteria.uppercase.classList.replace("text-danger", "text-success");
      strength++;
    } else {
      criteria.uppercase.classList.replace("text-success", "text-danger");
    }

    if (/[\W_]/.test(password)) {
      criteria.special.classList.replace("text-danger", "text-success");
      strength++;
    } else {
      criteria.special.classList.replace("text-success", "text-danger");
    }

    const colors = ["bg-danger", "bg-warning", "bg-success"];
    strengthBar.className = "progress-bar " + (colors[strength - 1] || "");
    strengthBar.style.width = `${(strength / 3) * 100}%`;
  });

  // 🔹 Validar coincidencia de contraseñas
  confirmInput.addEventListener("input", () => {
    if (confirmInput.value === passwordInput.value) {
      matchMessage.textContent = "✅ Las contraseñas coinciden";
      matchMessage.classList.replace("text-danger", "text-success");
    } else {
      matchMessage.textContent = "❌ Las contraseñas no coinciden";
      matchMessage.classList.replace("text-success", "text-danger");
    }
  });

  // 🔹 Evitar envío si las contraseñas no coinciden
  form.addEventListener("submit", (e) => {
    if (passwordInput.value !== confirmInput.value) {
      e.preventDefault();
      alert("Las contraseñas no coinciden. Por favor, verifica.");
    }
  });
</script>

</body>
</html>
