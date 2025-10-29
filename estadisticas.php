<?php include("includes/header.php"); ?>
<div class="card-style">
  <h4><i class="bi bi-bar-chart"></i> Estadísticas (últimos 30 días)</h4>
  <div class="row text-center mt-4" id="resumenCards"></div>
  <canvas id="grafico" height="120" class="mt-4"></canvas>
  <hr>
  <h5 class="mt-4">📋 Movimientos recientes</h5>
  <div class="table-responsive">
    <table class="table table-bordered" id="tablaMovs">
      <thead><tr><th>#</th><th>Producto</th><th>Tipo</th><th>Cantidad</th><th>Total</th><th>Fecha</th></tr></thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
async function loadEstadisticas(){
  const r=await fetch('ajax/movimientos.php?action=resumen');const j=await r.json();
  const cont=document.getElementById('resumenCards');cont.innerHTML='';
  const colors={entrada:'success',salida:'danger',ganancia:'primary'};
  for(const[k,v]of Object.entries(j.resumen))cont.innerHTML+=`<div class='col-md-4'><div class='resumen-card'><h6 class='text-muted'>${k}</h6><h3 class='text-${colors[k]||'dark'}'>$${Number(v).toLocaleString()}</h3></div></div>`;
  new Chart(document.getElementById('grafico'),{type:'bar',data:{labels:Object.keys(j.resumen),datasets:[{label:'Valores ($)',data:Object.values(j.resumen),backgroundColor:['#28a745','#dc3545','#007bff']}]},options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
  const r2=await fetch('ajax/movimientos.php?action=list');const m=await r2.json();
  const tb=document.querySelector('#tablaMovs tbody');tb.innerHTML='';
  m.movs.forEach((x,i)=>tb.innerHTML+=`<tr><td>${i+1}</td><td>${x.producto}</td><td><span class='badge bg-${x.tipo==='entrada'?'success':'danger'}'>${x.tipo}</span></td><td>${x.cantidad}</td><td>$${x.total}</td><td>${x.fecha_movimiento}</td></tr>`);
}
loadEstadisticas();
</script>
<?php include("includes/footer.php"); ?>
