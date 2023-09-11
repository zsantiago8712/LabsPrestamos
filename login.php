<?
header('Content-Type:text/html;charset=utf-8');
if( !isset($_REQUEST['user']) || 
	!isset($_REQUEST['pwd']) ) {
	header('Location: index.php');
	exit();
}
	
if(!preg_match("/^[0-9a-zñ\.-_]{4,20}$/i", $_REQUEST['user'])){
	header('Location: index.php?bu=1');
	exit();
}
	
if(preg_match("/[\s]/", $_REQUEST['pwd'])){
	header('Location: index.php?bu=2');
	exit();
}

$usuario = $_REQUEST['user'];
$pwd	 = $_REQUEST['pwd'];
include("includes/conectaBD.php");
include("clases/Usuario.class.php");
include("clases/ccaAppSecurity.class.php");



$User = new Usuario($link,$usuario);

if($User->id_usuario == 0){
	include("includes/cierraBD.php");
	header("Location: index.php?bu=cru");
	exit();	

}

$appSecurity = new ccaAppSecurity($link, 2301, "aplicaciones_cca",false);

if( !$appSecurity->isdomainuser($usuario, $pwd, $User->dominio, $User->ldap_server) ){
	include("includes/cierraBD.php");
	header('Location: index.php?bu=ndu');
	exit();	
}

if( !$appSecurity->isValidAppUser($usuario) ){
	include("includes/cierraBD.php");
	header('Location: index.php?bu=nau');
	exit();	
}
include "clases/laboratorio.class.php";
$laboratorios = new laboratoros($link, $User->id_usuario);
session_start();
$_SESSION['id_usuario']		 =	$User->id_usuario;
$_SESSION['usuario']		 =	$usuario;
$_SESSION['nombre_usuario']	 =	$User->nombre_usuario;
$_SESSION['correo']			 =	$User->correo;
$_SESSION["laboratorios"] = $laboratorios->lab;
$_SESSION["id_laboratorios"] = $laboratorios->id_lab;
header('Location: main.php');
exit();	
