<?
// []   <>
include "../clases/validaciones.php";
if(!isset($_POST["solicitud"]))
{
    exitWithErrorMsg("Parámetros inválidos");
}
$solicitud = $_POST["solicitud"];
$response["warning"] = false;
include ("../includes/conectaBD.php");
include("../clases/InventarioExistencia.php");
include("../clases/InventarioPrestamo.php");
include("../clases/UsuarioInventarioSrv.php");
include("../clases/InventarioSerie.php");
include("../clases/AlumnosSrv.php");
session_start();
$alumno = new alumno($link,$_POST["id_usuario"]);
$response["warningMessage"] = Array();
$response["warningid"] = Array();
$usrInv = new UsuarioInventarioSrv($link, $alumno->cuenta, $alumno->tipo_usuario);
foreach($solicitud as $key => $value)
{
    $link->start_transaction();
    $inv = new InventarioExistencia($link, $value["id"],1);
    $prestamo = new InventarioPrestamo($link,$value["id_prestamo"]);
    $prestados = $usrInv->getCantidadPrestados($value["id"]);
    if(!isNumber($value["id"]) || !isNumber($value["cantidad"]) || !isNumber($value["id_prestamo"]) || !isNumber($value["rechazar"]))
    {
        $response["warning"] = true;
        $link->commit();
        array_push($response["warningMessage"], "Parámetros incorrectos para: " . $value["descripcion"]);
        array_push($response["warningid"],$value["id_prestamo"]);
        continue;
    }
    if($value["rechazar"] == "1" || (isset($value["serie"]) && $value["serie"] == "0"))
    {
        $prestamo->id_status_prestamo = 3;
        if(!$prestamo->save())
        {
            $response["warning"] = true;
            $link->rollback();
            array_push($response["warningMessage"], "Error al rechazar: " . $value["descripcion"]);
            array_push($response["warningid"],$value["id_prestamo"]);
            continue;
        }
	$link->commit();
        continue; 
    }
    if(isset($value["serie"]))
    {
        if(!validar::isNumber($value["serie"]))
        {
            $response["warning"] = true;
            $link->rollback();
            array_push($response["warningMessage"], "Serie inválida para " . $value["descripcion"]);
            array_push($response["warningid"],$value["id_prestamo"]);
            continue;
        }
        $inv->disponible = $inv->disponible - $value["cantidad"];
        $prestamo->id_status_prestamo = 2;
        $prestamo->cantidad_entregada =  $value["cantidad"];
        $prestamo->fecha_recepcion = fechaEntrega();
        $invSerie = new InventarioSerie($link,$value["serie"],1);
        $invSerie->disponible = $value["cantidad"] - 1;
        $prestamo->id_serie_lote = $value["serie"];
        if(!$inv->save() || !$prestamo->save() || !$invSerie->save() )
        {
            $response["warning"] = true;
            $link->rollback();
            array_push($response["warningMessage"], "Error al procesar el préstamo: " . $value["descripcion"]);
            array_push($response["warningid"],$value["id_prestamo"]);
            continue;
        }
       

    }else{ 
    if($value["cantidad"] > $inv->disponible)
    {
        $response["warning"] = true;
        $link->rollback();
        array_push($response["warningMessage"], "Inventario insuficiente para " . $value["descripcion"]);
        array_push($response["warningid"],$value["id_prestamo"]);
        continue;
    }
    if($prestados + $value["cantidad"] >  $inv->prestamos)
    {
        $response["warning"] = true;
        $link->rollback();
        array_push($response["warningMessage"], "Préstamos máximos excedidos : " . $value["descripcion"]);
        array_push($response["warningid"],$value["id_prestamo"]);
        continue;
    }
    if(intval($value["cantidad"]) < 0)
    {
        $response["warning"] = true;
        $link->rollback();
        array_push($response["warningMessage"], "Cantidad inválida: " . $value["descripcion"]);
        array_push($response["warningid"],$value["id_prestamo"]);
        continue;
    }
    $inv->disponible = $inv->disponible - $value["cantidad"];
    $prestamo->id_status_prestamo = 2;
    $prestamo->cantidad_entregada =  $value["cantidad"];
    //TODO: modificar la fecha de entrega a los dias que tiene el material considerando 
    //que se recorra al siguiente dia habil en caso de ser fin de semana.
    //$prestamo->fecha_entrega_programada = ?;
    $prestamo->fecha_recepcion = fechaEntrega();
    
    
    if(!$inv->save() || !$prestamo->save())
    {
        $response["warning"] = true;
        $link->rollback();
        array_push($response["warningMessage"], "Error al procesar el préstamo: " . $value["descripcion"]);
        array_push($response["warningid"],$value["id_prestamo"]);
        continue;
    }}
    $link->commit();
}
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
    return is_numeric($dato);
}
function fechaEntrega(){
   	$hoy = new DateTime('now',new DateTimeZone("America/Mexico_City"));
    return $hoy->format("Y-m-d");
}
?>