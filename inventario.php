<?php include("includes/header.php"); ?>
<?php require 'conexion.php'; ?>

<div class="card-style">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-box-seam"></i> Inventario</h4>
    <div>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCreateProduct">
        <i class="bi bi-plus-circle"></i> Nuevo
      </button>
    </div>
  </div>

  <div id="alerts"></div>

  <div class="table-responsive">
    <table class="table table-hover" id="productosTable">
      <thead class="table-light">
        <tr><th>#</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr>
      </thead>
      <tbody id="productosBody"></tbody>
    </table>
  </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="modalCreateProduct" tabindex="-1">
  <div class="modal-dialog">
    <form id="formCreateProduct" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nuevo producto</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nombre</label>
          <input name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" class="form-control"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Categoría</label>
          <select name="id_categoria" id="selectCategoria" class="form-select" required></select>
        </div>
        <div class="row g-2">
          <div class="col">
            <label class="form-label">Precio</label>
            <input name="precio" type="number" step="0.01" min="0" class="form-control" required>
          </div>
          <div class="col">
            <label class="form-label">Stock</label>
            <input name="stock" type="number" min="0" class="form-control" value="0" required>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-success" type="submit">Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditProduct" tabindex="-1">
  <div class="modal-dialog">
    <form id="formEditProduct" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar producto</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_producto" id="edit_id">
        <div class="mb-3">
          <label class="form-label">Nombre</label>
          <input id="edit_nombre" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea id="edit_descripcion" name="descripcion" class="form-control"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Categoría</label>
          <select name="id_categoria" id="edit_categoria" class="form-select" required></select>
        </div>
        <div class="row g-2">
          <div class="col">
            <label class="form-label">Precio</label>
            <input id="edit_precio" name="precio" type="number" step="0.01" min="0" class="form-control" required>
          </div>
          <div class="col">
            <label class="form-label">Stock</label>
            <input id="edit_stock" name="stock" type="number" min="0" class="form-control" required>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" type="submit">Actualizar</button>
      </div>
    </form>
  </div>
</div>

<script>
const alertsContainer = document.getElementById('alerts');

// Cargar productos
async function loadProductos() {
  const res = await fetch('ajax/productos.php?action=list');
  const data = await res.json();
  const tbody = document.getElementById('productosBody');
  tbody.innerHTML = '';
  if (!data.success) return showAlert(alertsContainer, 'danger', data.message);
  data.products.forEach((p, i) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${i + 1}</td>
      <td>${p.nombre}</td>
      <td>${p.categoria ?? '-'}</td>
      <td>$${Number(p.precio).toFixed(2)}</td>
      <td>${p.stock}</td>
      <td>
        <button class="btn btn-sm btn-primary btn-edit" data-id="${p.id_producto}"><i class="bi bi-pencil"></i></button>
        <button class="btn btn-sm btn-danger btn-delete" data-id="${p.id_producto}"><i class="bi bi-trash"></i></button>
      </td>`;
    tbody.appendChild(tr);
  });
  document.querySelectorAll('.btn-edit').forEach(b => b.onclick = onEditClick);
  document.querySelectorAll('.btn-delete').forEach(b => b.onclick = onDeleteClick);
}

// Cargar categorías
async function loadCategorias() {
  const res = await fetch('ajax/productos.php?action=categories');
  const data = await res.json();
  const selects = [document.getElementById('selectCategoria'), document.getElementById('edit_categoria')];
  selects.forEach(s => s.innerHTML = '');
  if (data.success) {
    data.categorias.forEach(c => {
      selects.forEach(s => {
        const opt = document.createElement('option');
        opt.value = c.id_categoria;
        opt.textContent = c.nombre;
        s.appendChild(opt);
      });
    });
  }
}

// Crear producto
document.getElementById('formCreateProduct').addEventListener('submit', async e => {
  e.preventDefault();
  const form = new FormData(e.target);
  form.append('action', 'create');
  const res = await fetch('ajax/productos.php', { method: 'POST', body: form });
  const json = await res.json();
  if (json.success) {
    showAlert(alertsContainer, 'success', json.message);
    e.target.reset();
    bootstrap.Modal.getInstance(document.getElementById('modalCreateProduct')).hide();
    loadProductos();
  } else showAlert(alertsContainer, 'danger', json.message);
});

// Editar producto
async function onEditClick() {
  const id = this.dataset.id;
  const res = await fetch('ajax/productos.php?action=get&id=' + id);
  const r = await res.json();
  if (!r.success) return showAlert(alertsContainer, 'danger', r.message);
  document.getElementById('edit_id').value = r.product.id_producto;
  document.getElementById('edit_nombre').value = r.product.nombre;
  document.getElementById('edit_descripcion').value = r.product.descripcion;
  document.getElementById('edit_precio').value = r.product.precio;
  document.getElementById('edit_stock').value = r.product.stock;
  document.getElementById('edit_categoria').value = r.product.id_categoria;
  new bootstrap.Modal(document.getElementById('modalEditProduct')).show();
}

// Actualizar producto
document.getElementById('formEditProduct').addEventListener('submit', async e => {
  e.preventDefault();
  const form = new FormData(e.target);
  form.append('action', 'update');
  const res = await fetch('ajax/productos.php', { method: 'POST', body: form });
  const json = await res.json();
  if (json.success) {
    showAlert(alertsContainer, 'success', json.message);
    bootstrap.Modal.getInstance(document.getElementById('modalEditProduct')).hide();
    loadProductos();
  } else showAlert(alertsContainer, 'danger', json.message);
});

// Eliminar producto
async function onDeleteClick() {
  if (!confirm('¿Eliminar este producto?')) return;
  const form = new FormData();
  form.append('action', 'delete');
  form.append('id_producto', this.dataset.id);
  const res = await fetch('ajax/productos.php', { method: 'POST', body: form });
  const json = await res.json();
  if (json.success) {
    showAlert(alertsContainer, 'success', json.message);
    loadProductos();
  } else showAlert(alertsContainer, 'danger', json.message);
}

window.addEventListener('load', () => {
  loadCategorias();
  loadProductos();
});
</script>

<?php include("includes/footer.php"); ?>
