<?php
include("includes/header.php");
if ($_SESSION['rol'] !== 'administrador') {
  echo "<div class='alert alert-danger mt-4'>🚫 No tienes permisos para acceder aquí.</div>";
  include("includes/footer.php");
  exit();
}
?>
<div class="card-style">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="bi bi-people"></i> Usuarios</h4>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalUser">+ Nuevo Usuario</button>
  </div>

  <div id="alertUsers"></div>
  <div class="table-responsive">
    <table class="table table-hover" id="tablaUsuarios">
      <thead><tr><th>#</th><th>Nombre</th><th>Correo</th><th>Rol</th><th>Acciones</th></tr></thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalUser" tabindex="-1">
  <div class="modal-dialog">
    <form id="formUser" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nuevo Usuario</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_usuario" id="id_usuario">
        <div class="mb-3"><label class="form-label">Nombre</label>
          <input name="nombre" id="nombre" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Correo</label>
          <input name="correo" id="correo" type="email" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Contraseña</label>
          <input name="contraseña" id="contraseña" type="password" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Rol</label>
          <select name="rol" id="rol" class="form-select">
            <option value="empleado">Empleado</option>
            <option value="administrador">Administrador</option>
          </select></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
        <button class="btn btn-success" type="submit">Guardar</button>
      </div>
    </form>
  </div>
</div>

<script>
async function loadUsers() {
  const res = await fetch('ajax/usuarios.php?action=list');
  const data = await res.json();
  const tbody = document.querySelector('#tablaUsuarios tbody');
  tbody.innerHTML = '';
  data.users?.forEach((u,i)=>{
    tbody.innerHTML += `<tr>
      <td>${i+1}</td><td>${u.nombre}</td><td>${u.correo}</td>
      <td>${u.rol}</td>
      <td>
        <button class='btn btn-sm btn-primary' onclick='editUser(${JSON.stringify(u)})'><i class='bi bi-pencil'></i></button>
        <button class='btn btn-sm btn-danger' onclick='deleteUser(${u.id_usuario})'><i class='bi bi-trash'></i></button>
      </td></tr>`;
  });
}
function editUser(u){
  document.getElementById('id_usuario').value = u.id_usuario;
  document.getElementById('nombre').value = u.nombre;
  document.getElementById('correo').value = u.correo;
  document.getElementById('rol').value = u.rol;
  new bootstrap.Modal('#modalUser').show();
}
async function deleteUser(id){
  if(!confirm('¿Eliminar usuario?'))return;
  const fd=new FormData(); fd.append('action','delete'); fd.append('id_usuario',id);
  const res=await fetch('ajax/usuarios.php',{method:'POST',body:fd});
  const j=await res.json(); alert(j.message); loadUsers();
}
document.getElementById('formUser').addEventListener('submit',async e=>{
  e.preventDefault();
  const fd=new FormData(e.target);
  fd.append('action', fd.get('id_usuario') ? 'update' : 'create');
  const res=await fetch('ajax/usuarios.php',{method:'POST',body:fd});
  const j=await res.json(); alert(j.message);
  bootstrap.Modal.getInstance('#modalUser').hide(); e.target.reset(); loadUsers();
});
loadUsers();
</script>
<?php include("includes/footer.php"); ?>
