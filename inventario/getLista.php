<?php
if(!isset($_POST["id"]))
{
    exitWithErrorMsg("No se mando la caracteristica");
}
$id = $_POST["id"];
if(!preg_match("/^[0-9]{1,3}$/i",$id))
{
    exitWithErrorMsg("No se mando la caracteristica de forma correcta");
}
include ("../includes/conectaBD.php");
include("../clases/caracteristicas.class.php");
$Caracteristica = new Caracteristica($link,$id);
$response["success"] = true;
$response["tipo"] = $Caracteristica->tipo;
if($Caracteristica->tipo == "2")
{
    $response["lista"] = $Caracteristica->lista;
}
echo json_encode($response);
exit();

function exitWithErrorMsg($errorMessage)
{
    $response["success"] = false;
    $response["errorMessage"] = $errorMessage;
    echo(json_encode($response));
    exit();
}
?>