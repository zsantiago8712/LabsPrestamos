<?php
// []   <>
if(!isset($_POST["id_prestamo"]) || !isset($_POST["id_adeudo"]))
{
    exitWithErrorMsg("No se recibio la solicitud");
}
$id_prestamo = $_POST["id_prestamo"];
$id_adeudo = $_POST["id_adeudo"];
include "../clases/InventarioAdeudos.class.php";
include("../clases/InventarioPrestamo.php");
include "../includes/conectaBD.php";
include "../clases/validaciones.php";
if(!validar::isNumber($id_adeudo) || !validar::isNumber($id_prestamo))
{
     exitWithErrorMsg("Los datos han sido enviados de forma incorrecta.");
}
$Adeudo = new adeudos($link, $id_adeudo);
$prestamo = new InventarioPrestamo($link,$id_prestamo);
$Adeudo->activo = 0;
$prestamo->id_status_prestamo = 7;
$link->start_transaction();
if(!$Adeudo->save() || !$prestamo->save())
{
    $link->rollback();
    exitWithErrorMsg("no se pudo quitar el adeudo ERROR: " + $Adeudo->lasterror . " " . $prestamo->lasterror);
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
?>