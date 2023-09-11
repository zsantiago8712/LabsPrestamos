<?php
/*
* Creado por: Derek Escamilla.
* fecha: 28/03/2023
* Ubicación: Universidad Iberoamericana
*
*
*/
header('Content-Type:text/html;charset=utf-8');
session_start();
if(!isset($_POST["general"])  || !isset($_POST["id"]) )
{
    exitWithErrorMsg("No se mandaron los datos necesarios");
}
$General = $_POST["general"];
$id = $_POST["id"];
$caraBorrar = 0;
$BanderaLaboratorio = 0;
if(isset($_POST["caraBorrar"]))
{
    $caraBorrar = $_POST["caraBorrar"];
    foreach($caraBorrar as $key => $value)
    {
        if(!isNumber($value))
        {
            exitWithErrorMsg("Se espera un numero en Caracteristicas borradas");
        }
    }
}
if(!isNumber($id))
{
    exitWithErrorMsg("Se espera un numero de id");
}
$Caracteristicas = Array();
if(isset($_POST["caracteristicas"]))
{
    $Caracteristicas = $_POST["caracteristicas"];
}


include ("../includes/conectaBD.php");
include("../clases/Item.class.php");
foreach($General as $key => $value)
{
    if($key == 'consumo' || $key == 'clasificacion' || $key == 'cuantificacion')
    {
        if(!isNumber($value))
        {
            exitWithErrorMsg("Se espera un numero en: " . $key);
        }
    }elseif($key == "descripcion") {
        if(!isString($value))
        {
            exitWithErrorMsg("Se espera un string en: " . $key);
        }
    }elseif($key == "numParte")
    { 
        if($value != ""){ 
        if(!isviable($value))
        {
            if($value != null)
            {
                exitWithErrorMsg("No se acepto el num_parte proporcionado: " . $value);
            }
        }}
    }elseif($key == "unidad")
    {
        if(!isNumber($value))
        {
            exitWithErrorMsg("No se acepto la unidad dada.");
        }
    }elseif($key == "prefijo")
    {
        if(!isNumber($value))
        {
            exitWithErrorMsg("No se acepto el prefijo");
        }
    }
    if($key == 'marca' && $value != "0")
    {
        if(!isviable($value))
        {
            exitWithErrorMsg("No es viable: " . $key);
        }
        if(!isNumber($value) && $value != "0")
        {
            $Marca = new marca($link);
            if($Marca->checkString($value)){}else{
                if(!$Marca->save($value))
            {
                exitWithErrorMsg("No se puede guardar: " . $key);
            }
            }
            
            
        }
    }  
    if($key == "descripMarca") 
    {
        if(!isviable($value))
        {
            exitWithErrorMsg("No es viable: " . $key);
        }
            $Marca = new marca($link);
            if($Marca->checkString($value)){}else{
                if(!$Marca->save($value))
            {
                exitWithErrorMsg("No se puede guardar: " . $key);
            }
            }
    }
}
if(!isNumber($General["modelo"]) && $General["modelo"] != "0")
{
    if(!isviable($General["modelo"]))
    {
        exitWithErrorMsg("No es viable el modelo");
    }
    $Modelo = new modelo($link);
    if(isset($Marca))
    {
        $marcaModelo = $Marca->id;
    }else{
        $marcaModelo = $General["marca"];
    }
    if($Modelo->checkString($General["modelo"],$marcaModelo)){}else{

    
    if(!$Modelo->save($General["modelo"],$marcaModelo))
    {
        exitWithErrorMsg("No se puede guardar: " . $key);
    }}
}else{
    if(isset($General["descripModelo"])){ 
    if(!isviable($General["descripModelo"]))
    {
        exitWithErrorMsg("No es viable el modelo");
    }
    $Modelo = new modelo($link);
    if(isset($Marca))
    {
        $marcaModelo = $Marca->id;
    }else{
        $marcaModelo = $General["marca"];
    }
    if($Modelo->checkString($General["descripModelo"],$marcaModelo)){}else{

    
    if(!$Modelo->save($General["descripModelo"],$marcaModelo))
    {
        exitWithErrorMsg("No se puede guardar: " . $General["descripModelo"]);
    }}
    }}




if(isset($Marca))
{
    $General["marca"] = $Marca->id;
}
if(isset($Modelo))
{
    $General["modelo"] = $Modelo->id;
}

foreach($Caracteristicas as $key => $value)
{
    if(!isNumber($value["tipo"]))
    {
        exitWithErrorMsg("Se espera un string en: " . $value["tipo"]);
    }
    if(!isNumber($value["caracteristica"]))
    {
        exitWithErrorMsg("Se espera un numero en: " . $value["caracteristica"]);
    }
    if(!isNumber($value["prefijo"]))
    {
        exitWithErrorMsg("Se espera un numero en: " . $value["prefijo"]);
    }
    if(!preg_match("/^[0-9.]{1,9}$/",$value["valor"]))
    {
        exitWithErrorMsg("Se espera un numero en: " . $value["valor"]);
    }
}
$generalLab = Array();
$Laboratorios = Array();
if(isset($_POST["laboratorios"]))
{
    if(!isset($_POST["generalLab"]))
    {
        exitWithErrorMsg("Se espera recibir la informacion de prestamo del laboratorio");
    }

    $Laboratorios = $_POST["laboratorios"];
    $Labs = $_SESSION["laboratorios"];
    $generalLab = $_POST["generalLab"];
    foreach($Laboratorios as $key => $value)
    {
        foreach($value as $llave => $valor)
        {
            if($General["cuantificacion"] == "1" || $General["cuantificacion"] == "4")
            {
                if(!isNumber($valor["cantidad"]))
                {
                    if($valor["cantidad"] != null)
                    {
                        exitWithErrorMsg("Se espera un numero en: " . $valor["cantidad"] . " 'Cantidad'"); 
                    }
                
                } 
            
            }
        }
    }
}


$item = new item($link);
if(!$item->save($General, $Caracteristicas, $Laboratorios, $id, $caraBorrar,$generalLab,$_SESSION['usuario']))
{
    exitWithErrorMsg($item->lasterror);
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
function isString($dato)
{
    return preg_match("/^[ ()A-ZÁ-Ý0-9-\/\"\.\*#]{1,100}$/",$dato);
}
function isNumber($dato)
{
    return preg_match("/^[.0-9]{1,6}$/i",$dato);
}
function isviable($dato)
{
    return preg_match("/^[ _a-zá-ÿ0-9\-]{1,30}$/i",$dato);
}
?>