<?php
require '../conexion.php';header('Content-Type: application/json; charset=utf-8');
$a=$_GET['action']??$_POST['action']??'';function r($ok,$d=[]){echo json_encode(array_merge(['success'=>$ok],$d));exit();}
if($a==='list'){$r=$conn->query("SELECT * FROM domicilios ORDER BY id_domicilio DESC");$rows=[];while($x=$r->fetch_assoc())$rows[]=$x;r(true,['domicilios'=>$rows]);}
if($a==='create'){$c=$_POST['cliente'];$d=$_POST['direccion'];$p=$_POST['producto'];$cant=(int)$_POST['cantidad'];$e=$_POST['estado'];
$st=$conn->prepare("INSERT INTO domicilios(cliente,direccion,producto,cantidad,estado)VALUES(?,?,?,?,?)");$st->bind_param("sssds",$c,$d,$p,$cant,$e);r($st->execute());}
if($a==='update'){$id=(int)$_POST['id_domicilio'];$c=$_POST['cliente'];$d=$_POST['direccion'];$p=$_POST['producto'];$cant=(int)$_POST['cantidad'];$e=$_POST['estado'];
$st=$conn->prepare("UPDATE domicilios SET cliente=?,direccion=?,producto=?,cantidad=?,estado=? WHERE id_domicilio=?");$st->bind_param("sssisi",$c,$d,$p,$cant,$e,$id);r($st->execute());}
if($a==='delete'){$id=(int)$_POST['id_domicilio'];$conn->query("DELETE FROM domicilios WHERE id_domicilio=$id");r(true);}
r(false);
