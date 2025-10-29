<?php
require '../conexion.php';header('Content-Type: application/json; charset=utf-8');
$a=$_GET['action']??$_POST['action']??'';function r($ok,$d=[]){echo json_encode(array_merge(['success'=>$ok],$d));exit();}
if($a==='resumen'){
 $q=$conn->query("SELECT tipo,SUM(total) total FROM movimientos_inventario WHERE fecha_movimiento>=DATE_SUB(CURDATE(),INTERVAL 30 DAY) GROUP BY tipo");
 $data=['entrada'=>0,'salida'=>0,'ganancia'=>0];while($x=$q->fetch_assoc()){$data[$x['tipo']]=$x['total'];}
 $data['ganancia']=$data['salida']-$data['entrada'];r(true,['resumen'=>$data]);
}
if($a==='list'){
 $r=$conn->query("SELECT m.*,p.nombre AS producto FROM movimientos_inventario m LEFT JOIN productos p ON p.id_producto=m.id_producto WHERE fecha_movimiento>=DATE_SUB(CURDATE(),INTERVAL 30 DAY) ORDER BY fecha_movimiento DESC");
 $rows=[];while($x=$r->fetch_assoc())$rows[]=$x;r(true,['movs'=>$rows]);
}
r(false);
