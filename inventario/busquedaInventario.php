<? 
if(!isset($_GET)){
    exitWithErrorMsg("No se recibieron datos");
}
include ("../includes/conectaBD.php");
include("../clases/Item.class.php");
$term = $_GET["term"];
$item = new item($link);
$resultados = $item->busqueda($term);
if(!$resultados)
{
    exitWithErrorMsg("No se pudo completar el query");
}
$name = "";
foreach($resultados as $key => $value)
{
    $name = $value["descripcion"];
}
$response["name"] = $name;
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