<?php
require '../conexion.php';
header('Content-Type: application/json; charset=utf-8');
$action = $_GET['action'] ?? $_POST['action'] ?? '';

function res($ok,$data=[]){echo json_encode(array_merge(['success'=>$ok],$data));exit();}

if($action==='list'){
  $r=$conn->query("SELECT id_usuario,nombre,correo,rol FROM usuarios ORDER BY id_usuario DESC");
  $users=[];while($u=$r->fetch_assoc())$users[]=$u;
  res(true,['users'=>$users]);
}
if($action==='create'){
  $n=$_POST['nombre'];$c=$_POST['correo'];$p=password_hash($_POST['contraseña'],PASSWORD_DEFAULT);
  $rol=$_POST['rol']??'empleado';
  $st=$conn->prepare("INSERT INTO usuarios(nombre,correo,contraseña,rol)VALUES(?,?,?,?)");
  $st->bind_param("ssss",$n,$c,$p,$rol);
  res($st->execute(),['message'=>'Usuario creado']);
}
if($action==='update'){
  $id=(int)$_POST['id_usuario'];
  $n=$_POST['nombre'];$c=$_POST['correo'];$rol=$_POST['rol'];
  if(!empty($_POST['contraseña'])){
    $p=password_hash($_POST['contraseña'],PASSWORD_DEFAULT);
    $st=$conn->prepare("UPDATE usuarios SET nombre=?,correo=?,contraseña=?,rol=? WHERE id_usuario=?");
    $st->bind_param("ssssi",$n,$c,$p,$rol,$id);
  }else{
    $st=$conn->prepare("UPDATE usuarios SET nombre=?,correo=?,rol=? WHERE id_usuario=?");
    $st->bind_param("sssi",$n,$c,$rol,$id);
  }
  res($st->execute(),['message'=>'Usuario actualizado']);
}
if($action==='delete'){
  $id=(int)$_POST['id_usuario'];
  $conn->query("DELETE FROM usuarios WHERE id_usuario=$id");
  res(true,['message'=>'Usuario eliminado']);
}
res(false,['message'=>'Acción inválida']);
