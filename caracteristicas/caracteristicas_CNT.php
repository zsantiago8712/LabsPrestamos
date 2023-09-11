<?
include ("../includes/conectaBD.php");
include ("../clases/caracteristicas.class.php");

$caracteristicas = new Caracteristica($link);

if(!isset($_POST["id_caracteristica"]))
{
    exitWithErrorMsg("No se mandaron los datos necesarios");
}

$id_car_t = $_POST["id_caracteristica"];
$id_car = (int) $id_car_t;

if(isset($_POST["tipo"])){

    $car = $_POST["caracteristica"];
    $uni = $_POST["unidad"];
    $abr = $_POST["abreviatura"];
    $tipo = $_POST["tipo"]; 

    if($id_car == "0"){
        if(!$caracteristicas->agregar_caracteristica($car, $uni, $abr, $tipo))
        {
            exitWithErrorMsg($caracteristicas->lasterror);
        }
        if($tipo == 2){
            $id_nuevaTL= $caracteristicas->link->lastid;
            $val = $_POST["valores"];
            
            foreach($val as $key => $value)
            {
                if(!$caracteristicas->agregar_opciones($value["valor"]))
            {
                exitWithErrorMsg($caracteristicas->lasterror);
            }
            }
        }
    }
    elseif($id_car > 0 && isset($_POST["valores"]) && $tipo == 2){

        $opciones = $_POST["valores"];

        if(!$caracteristicas->actualizar_caracteristica($id_car, $car, $uni, $abr, $tipo, $opciones))
        {
            exitWithErrorMsg($caracteristicas->lasterror);
        }

    }elseif($id_car > 0){
        $opciones = 0;

        if(!$caracteristicas->actualizar_caracteristica($id_car, $car, $uni, $abr, $tipo, $opciones))
        {
            exitWithErrorMsg($caracteristicas->lasterror);
        }

    }
    
}elseif($id_car > 0 && !isset($_POST["tipo"])){

    $id = $_POST["id_caracteristica"];
    $response["carac_mod"] = $caracteristicas->get_all_caracteristica($id);

}

$response["success"] = true;
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