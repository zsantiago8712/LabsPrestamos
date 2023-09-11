<?
if(!isset($_POST["name"]))
{
    exitWithErrorMsg("No se recibieron datos");
}
include ("../includes/conectaBD.php");
include("../clases/marca.class.php");
include("../clases/modelo.class.php");
$id = $_POST["name"];
$Marca = new marca($link);
$Modelo = new modelo($link);
if(!isNumber($id))
{
    $id = 0;
    $modelo = $Modelo->get_Modelo_Marca($id);
}else {
    $modelo = $Modelo->get_Modelo_Marca($id);
}


$response["success"] = true;
$response["resultados"] = array("id" => $id, "modelos" => $modelo);
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
    return preg_match("/^[0-9]{1,4}$/i",$dato);
}
?>