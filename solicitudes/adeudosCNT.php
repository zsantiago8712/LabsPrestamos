<?php 

if(!isset($_POST["tipo"]) || !isset($_POST["id_prestamo"])|| !isset($_POST["comentarios"]) || !isset($_POST["id_usuario"]))
{
    exitWithErrorMsg("No se recibio la solicitud");
}
$tipo = htmlspecialchars($_POST["tipo"]);
$id_prestamo = htmlspecialchars($_POST["id_prestamo"]);
$comentarios = htmlspecialchars($_POST["comentarios"]);
$id_usuario = htmlspecialchars($_POST["id_usuario"]);
include "../clases/InventarioAdeudos.class.php";
include "../includes/conectaBD.php";
if(!isNumber($tipo) || !isNumber($id_prestamo) || !isNumber($id_usuario))
{
     exitWithErrorMsg("Los datos han sido enviados de forma incorrecta.");
}
$Adeudo = new adeudos($link);
$Adeudo->id_usuario = $id_usuario;
$Adeudo->id_inventario_prestamo = $id_prestamo;
$Adeudo->comentarios = $comentarios;
$Adeudo->id_tipo_adeudo = $tipo;
$link->start_transaction();
if(!$Adeudo->save())
{
    $link->rollback();
    
    exitWithErrorMsg("No se pudo generar el adeudo. Error: " . $Adeudo->lasterror);
}
$link->commit();
$response["success"] = true;
echo(json_encode($response));
exit();
function exitWithErrorMsg($errorMessage)
{
    $response["success"] = false;
    $response["errorMessage"] = $errorMessage;
    echo(json_encode($response));
    exit();
}
function isNumber($dato)
{
    return preg_match("/^[.0-9]{1,6}$/i",$dato);
}
?>