<?
// []   <>
if(!isset($_POST["activos"]))
{
    exitWithErrorMsg("Parámetros inválidos");
}
$activo = $_POST["activos"];
include ("../includes/conectaBD.php");
include("../clases/InventarioExistencia.php");
include("../clases/InventarioPrestamo.php");
include("../clases/InventarioSerie.php");
include "../clases/validaciones.php";
if(!validar::isNumber($activo["id"]) || !validar::isNumber($activo["cantidad"]) || !validar::isNumber($activo["id_prestamo"]) || !validar::isNumber($activo["danados"]))
{
     exitWithErrorMsg("Parámetros inválidos.");
}
$link->start_transaction();
$inv = new InventarioExistencia($link, $activo["id"],1);
$prestamo = new InventarioPrestamo($link,$activo["id_prestamo"]);

if($activo["cantidad"] != $prestamo->cantidad_entregada)
{
    $link->rollback();
    exitWithErrorMsg("Cantidad entregada inválida.");
}
if(intval($activo["danados"]) < 0 || $activo["danados"] > $prestamo->cantidad_entregada  )
{
    $link->rollback();
    exitWithErrorMsg("Cantidad inválida: dañados");
}

$inv->disponible = $inv->disponible + $activo["cantidad"] - $activo["danados"];
$prestamo->id_status_prestamo = 5;
$prestamo->fecha_entrega_real = fechaEntrega();

if(isset($activo["serie"]))
{
    if(!validar::isNumber($activo["serie"]))
    {
        exitWithErrorMsg("Serie inválida.");
    }
    $invSerie = new InventarioSerie($link,$activo["serie"],1);
    $invSerie->disponible = 1;
    if(!$invSerie->save())
    {
        $link->rollback();
        exitWithErrorMsg("Error al actualizar disponibilidad en serie " . $invSerie->clave_interna);
    }
}

if(!$inv->save() || !$prestamo->save())
{
    $link->rollback();
    exitWithErrorMsg("Error al generar la entrega del material.");
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
function fechaEntrega(){
    $hoy = new DateTime('now',new DateTimeZone("America/Mexico_City"));
    return $hoy->format("Y-m-d H:m:s");
}
?>