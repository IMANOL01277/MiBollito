<?php include("includes/header.php"); ?>
<div class="card-style">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="bi bi-tags"></i> Estados</h4>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalEstado">+ Nuevo Estado</button>
  </div>
  <div class="table-responsive">
    <table class="table table-hover" id="tablaEstados">
      <thead><tr><th>#</th><th>Nombre</th><th>Descripción</th><th>Acciones</th></tr></thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<div class="modal fade" id="modalEstado" tabindex="-1">
  <div class="modal-dialog">
    <form id="formEstado" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Nuevo estado</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="id_estado" id="id_estado">
        <div class="mb-3"><label>Nombre</label><input class="form-control" name="nombre_estado" id="nombre_estado" required></div>
        <div class="mb-3"><label>Descripción</label><textarea class="form-control" name="descripcion_estado" id="descripcion_estado"></textarea></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
        <button class="btn btn-success" type="submit">Guardar</button>
      </div>
    </form>
  </div>
</div>

<script>
async function loadEstados(){
  const r=await fetch('ajax/estados.php?action=list');const j=await r.json();
  const t=document.querySelector('#tablaEstados tbody');t.innerHTML='';
  j.estados.forEach((e,i)=>t.innerHTML+=`<tr><td>${i+1}</td><td>${e.nombre_estado}</td><td>${e.descripcion_estado??'-'}</td>
    <td><button class='btn btn-sm btn-primary' onclick='editEstado(${JSON.stringify(e)})'><i class='bi bi-pencil'></i></button>
    <button class='btn btn-sm btn-danger' onclick='deleteEstado(${e.id_estado})'><i class='bi bi-trash'></i></button></td></tr>`);
}
function editEstado(e){id_estado.value=e.id_estado;nombre_estado.value=e.nombre_estado;descripcion_estado.value=e.descripcion_estado;new bootstrap.Modal('#modalEstado').show();}
async function deleteEstado(id){if(!confirm('¿Eliminar?'))return;const fd=new FormData();fd.append('action','delete');fd.append('id_estado',id);const r=await fetch('ajax/estados.php',{method:'POST',body:fd});loadEstados();}
document.getElementById('formEstado').addEventListener('submit',async e=>{
 e.preventDefault();const fd=new FormData(e.target);fd.append('action',fd.get('id_estado')?'update':'create');
 await fetch('ajax/estados.php',{method:'POST',body:fd});bootstrap.Modal.getInstance('#modalEstado').hide();e.target.reset();loadEstados();
});
loadEstados();
</script>
<?php include("includes/footer.php"); ?>
