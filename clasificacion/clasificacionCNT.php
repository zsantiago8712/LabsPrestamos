
<?php
/*
* Creado por: Derek Escamilla.
* fecha: 3/27/2023
* Ubicación: Universidad Iberoamericana
*
*
*/
$nuevos = Array();
$bd = Array();
if(isset($_POST["nuevos"]))
{
    $nuevos = $_POST["nuevos"];
    foreach($nuevos as $key => $value)
    {
        if(!preg_match("/[j\_0-9]{1,5}/i",$value[0]["id"]))
        {
            exitWithErrorMsg("Error: valor: " . $value[0]["id"] . " no aceptado");
        }
        if(!preg_match("/[a-zá-ÿ]{1,20}/i",$value[0]["text"]))
        {
            exitWithErrorMsg("Error: valor: " . $value[0]["text"] . " no aceptado");
        }
        if(!preg_match("/[j\_0-9]{1,5}/i",$value[0]["parent"]))
        {
            exitWithErrorMsg("Error: valor: " . $value[0]["parent"] . " no aceptado");
        }
        if(!preg_match("/[j\_0-9]{1,5}/i",$value[0]["old_parent"]))
        {
            exitWithErrorMsg("Error: valor: " . $value[0]["old_parent"] . " no aceptado");
        }
        if(!preg_match("/[0-1]{1}/i",$value[0]["borrar"]))
        {
            exitWithErrorMsg("Error: valor: " . $value[0]["borrar"] . " no aceptado");
        }
    }
}
if(isset($_POST["bd"]))
{
    $bd = $_POST["bd"];
    foreach($bd as $key => $value)
    {
        if(!preg_match("/[0-9]{1,3}/",$value[0]["id"]))
        {
            exitWithErrorMsg("Error: valor: " . $value[0]["id"] . " no aceptado");
        }
        if(!preg_match("/[a-zá-ÿ]{1,20}/i",$value[0]["text"]))
        {
            exitWithErrorMsg("Error: valor: " . $value[0]["text"] . " no aceptado");
        }
        if(!preg_match("/[0-9]{1,3}/i",$value[0]["parent"]))
        {
            exitWithErrorMsg("Error: valor: " . $value[0]["parent"] . " no aceptado");
        }
        if(!preg_match("/[0-9]{1,3}/i",$value[0]["old_parent"]))
        {
            exitWithErrorMsg("Error: valor: " . $value[0]["old_parent"] . " no aceptado");
        }
        if(!preg_match("/[0-1]{1}/i",$value[0]["borrar"]))
        {
            exitWithErrorMsg("Error: valor: " . $value[0]["borrar"] . " no aceptado");
        }
    }
}
if(!isset($_POST["nuevos"]) && !isset($_POST["bd"]))
{
    exitWithErrorMsg("Error: No se mandaron ningun dato.");
}
include ("../includes/conectaBD.php");
include ("../clases/jsTree.class.php");
$jsTree = new jsTree($link);
if(!$jsTree->save($nuevos,$bd))
{
     exitWithErrorMsg("Error: " . $jsTree->lasterror);
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

?>