<?php
require '../conexion.php';header('Content-Type: application/json; charset=utf-8');
$a=$_GET['action']??$_POST['action']??'';function r($ok,$d=[]){echo json_encode(array_merge(['success'=>$ok],$d));exit();}
if($a==='list'){$r=$conn->query("SELECT * FROM estado ORDER BY id_estado DESC");$rows=[];while($x=$r->fetch_assoc())$rows[]=$x;r(true,['estados'=>$rows]);}
if($a==='create'){$n=$_POST['nombre_estado'];$d=$_POST['descripcion_estado'];$st=$conn->prepare("INSERT INTO estado(nombre_estado,descripcion_estado)VALUES(?,?)");$st->bind_param("ss",$n,$d);r($st->execute());}
if($a==='update'){$id=(int)$_POST['id_estado'];$n=$_POST['nombre_estado'];$d=$_POST['descripcion_estado'];$st=$conn->prepare("UPDATE estado SET nombre_estado=?,descripcion_estado=? WHERE id_estado=?");$st->bind_param("ssi",$n,$d,$id);r($st->execute());}
if($a==='delete'){$id=(int)$_POST['id_estado'];$conn->query("DELETE FROM estado WHERE id_estado=$id");r(true);}
r(false);
