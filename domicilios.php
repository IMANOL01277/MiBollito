<?php include("includes/header.php"); ?>
<div class="card-style">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="bi bi-truck"></i> Domicilios</h4>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalDomicilio">+ Nuevo</button>
  </div>

  <div id="alertDom"></div>
  <div class="table-responsive">
    <table class="table table-hover" id="tablaDomicilios">
      <thead><tr><th>#</th><th>Cliente</th><th>Dirección</th><th>Producto</th><th>Cantidad</th><th>Estado</th><th>Acciones</th></tr></thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalDomicilio" tabindex="-1">
  <div class="modal-dialog">
    <form id="formDomicilio" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Registrar Domicilio</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="id_domicilio" id="id_domicilio">
        <div class="mb-3"><label>Cliente</label><input class="form-control" name="cliente" required></div>
        <div class="mb-3"><label>Dirección</label><input class="form-control" name="direccion" required></div>
        <div class="mb-3"><label>Producto</label><input class="form-control" name="producto" required></div>
        <div class="mb-3"><label>Cantidad</label><input type="number" class="form-control" name="cantidad" min="1" required></div>
        <div class="mb-3"><label>Estado</label>
          <select class="form-select" name="estado" required>
            <option value="pendiente">Pendiente</option>
            <option value="enviado">Enviado</option>
            <option value="entregado">Entregado</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
        <button class="btn btn-success" type="submit">Guardar</button>
      </div>
    </form>
  </div>
</div>

<script>
async function loadDomicilios(){
  const r=await fetch('ajax/domicilios.php?action=list');const j=await r.json();
  const t=document.querySelector('#tablaDomicilios tbody');t.innerHTML='';
  j.domicilios.forEach((d,i)=>t.innerHTML+=`<tr><td>${i+1}</td><td>${d.cliente}</td><td>${d.direccion}</td><td>${d.producto}</td>
  <td>${d.cantidad}</td><td><span class="badge bg-${d.estado==='entregado'?'success':d.estado==='enviado'?'primary':'warning'}">${d.estado}</span></td>
  <td><button class='btn btn-sm btn-primary' onclick='editDom(${JSON.stringify(d)})'><i class='bi bi-pencil'></i></button>
  <button class='btn btn-sm btn-danger' onclick='delDom(${d.id_domicilio})'><i class='bi bi-trash'></i></button></td></tr>`);
}
function editDom(d){for(const[k,v]of Object.entries(d)){const e=document.getElementById(k);if(e)e.value=v;}new bootstrap.Modal('#modalDomicilio').show();}
async function delDom(id){if(!confirm('¿Eliminar domicilio?'))return;const fd=new FormData();fd.append('action','delete');fd.append('id_domicilio',id);await fetch('ajax/domicilios.php',{method:'POST',body:fd});loadDomicilios();}
document.getElementById('formDomicilio').addEventListener('submit',async e=>{
 e.preventDefault();const fd=new FormData(e.target);fd.append('action',fd.get('id_domicilio')?'update':'create');
 await fetch('ajax/domicilios.php',{method:'POST',body:fd});bootstrap.Modal.getInstance('#modalDomicilio').hide();e.target.reset();loadDomicilios();
});
loadDomicilios();
</script>
<?php include("includes/footer.php"); ?>
