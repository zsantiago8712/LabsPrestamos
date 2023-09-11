<?
header('Content-Type:text/html;charset=utf-8');
//Generar session.
session_start();
$Laboratorios = $_SESSION["laboratorios"];
$id_lab = $_SESSION["id_laboratorios"];
include "../includes/conectaBD.php";
//Clase de validación de datos.
include "../clases/validaciones.php";
if(isset($_GET["lab"])) // Validar que exista laboratorio, en caso de que no asiganra el primero en tu perfil.
{
    $btn_lab = htmlspecialchars($_GET["lab"]);
    if(!validar::isNumber($btn_lab)) // si el laboratorio no es un numero, mandara ua vista de error.
    {
        errorLab();
    }
}else{
    $btn_lab = $id_lab[0];
}
include "../clases/InventarioPrestamoSrv.php";
InventarioPrestamoSrv::inicializaInventarioPrestamoSrv($link);
if(isset($_GET["cuenta"]))
{
    $cuenta = htmlspecialchars($_GET["cuenta"]);
    if(!validar::isString($cuenta))
    {
        $alumnos = Array();
    }else{
        $alumnos = InventarioPrestamoSrv::getPrestamosSolicitudPorLaboratorio($btn_lab, $cuenta);
    }
    
   
}else{
    $alumnos = InventarioPrestamoSrv::getPrestamosSolicitudPorLaboratorio($btn_lab);
}
function errorLab ($error = "Error en la solicitud de laboratorio.")
{
    ?>
    <div class="container">
        <h3><?= $error?></h3>
    </div>
    <?php
    exit();
}
?>
<nav class="navbar bg-body-tertiary">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">Buscar Alumno:</span>
        <div class="d-flex" role="search">
            <input id="inputBusqueda" class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            <button id="btnBuscar" class="btn btn-outline-success" type="submit"><i class="bi bi-search"></i></button>
         </div>
    </div>
</nav>
<div class="btn-group" role="group" aria-label="Basic radio toggle button group">
<?php foreach($Laboratorios as $key => $lab){ ?>
    <input type="radio" class="btn-check btn_lab" value="<?=$id_lab[$key] ?>" name="btnLab" id="btnLab_<?=$id_lab[$key] ?>" autocomplete="off" <? if(isset($btn_lab) && $id_lab[$key] == $btn_lab){echo "checked";}else{if($key == 0){ echo "checked";}} ?>>
    <label class="btn btn-outline-primary" for="btnLab_<?=$id_lab[$key] ?>"><?= $lab ?></label>
<?}?>
  
</div>
<br>
        <div id="laboratorio_Prestamos" class="laboratorio overflow-auto" style="max-height: 80%">
        <table class="table">
            <tr class="sticky-top bg-white">
                <th>Cuenta</th>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Detalles</th>
            </tr>
           <? if(isset($alumnos)){if(count($alumnos) > 0){   
            if(count($alumnos) == 1){ ?>
                <script>
                    window.document.busqueda = "<?= $alumnos[0]["cuenta"] ?>";
                </script>
    
                <?}else{?> 
                    <script>
                    window.document.busqueda = 0;
                </script>
    
                <?}
            foreach($alumnos as $key => $value){?>
            
                <tr>

                    <td><?= $value["cuenta"] ?></td>
                    <td><? echo  $value["ap_materno"] . " " . $value["ap_paterno"] . " " .  $value["nombres"]; ?></td>
                    <td><div class="d-flex flex-row">

                    
                        <?if(isset($value["adeudos"]) && isset($value["vencidos"])){ 
                        if($value["adeudos"] == "1") {?>
                            <div class="border-danger border-2 ">
                                <i class="bi bi-exclamation-octagon text-danger iconoPrestamo"></i>
                            </div>
                            <?}?>
                            <?if($value["vencidos"] == "1") {?>
                            <div class="border-danger border-2 ">
                                <i class="bi bi-exclamation text-warning iconoPrestamo" style="border: 2px solid yellow;"></i>
                            </div><? }?>
                            <? if($value["adeudos"] == "0" && $value["vencidos"] == 0){  ?>
                                <div class="border-danger border-2 ">
                                <i class="bi bi-check-all text-success iconoPrestamo" style="border: 2px solid green;"></i>
                            </div>
                                <? }?>
                            <?}?>
                    </div></td>
                    <td><button class="btn btn-outline-dark btnDetalles" data-id = "<?=  $value["id_usuario"] ?>" ><i class="bi bi-file-earmark-break-fill"></i></button></td>
                </tr>
            
            <?}}else{  ?>

                <tr>
                    <td colspan="3"><h3>No se encontró ningún alumno.</h3> </td>
                </tr>

           <? }}?>
        </table>
        </div>
        <div id="information" class="bg-info-subtle">
            <div class="row">
                
                    <div class="col-md-2 col-sm-1">
                        <h4><b>Leyenda:</b> </h4>
                    </div>
                    <div class="col-md-3 col-sm-2 ">
                        <div class="d-flex flex-row mb-3">
                            <div class="px-1"><h4>Existen solicitudes/activos:</h4></div>
                            <div class="border-danger border-2 ">
                                <i class="bi bi-check-all text-success iconoPrestamo" style="border: 2px solid green;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-2">
                        <div class="d-flex flex-row mb-3">
                            <div><h4>Existen adeudos:</h4></div>
                            <div class="border-danger border-2 ">
                                <i class="bi bi-exclamation-octagon text-danger iconoPrestamo"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-2">
                        <div class="d-flex flex-row mb-3">
                            <div><h4>Existen vencidos:</h4></div>
                            <div class="border-danger border-2 ">
                                <i class="bi bi-exclamation text-warning iconoPrestamo" style="border: 2px solid yellow;"></i>
                            </div>
                        </div>
                    </div>
                    
                
            </div>

        </div>
