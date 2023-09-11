<?php header('Content-Type:text/html;charset=utf-8'); 
include ("../includes/conectaBD.php");
include("../clases/Item.class.php");
$item = new item($link);
$Laborarios = new laboratoros($link);
session_start();
$laboratorios = $Laborarios->get_all_lab();
if(isset($_GET["id"]))
{
    $lab = $_GET["id"];
    
    if(!isset($_GET["descripcion"]))
    {
        $items = $item->laboratorioBusqueda($lab);
    }
    $lab = explode(",",$lab);
    
}
if(isset($_GET["descripcion"]))
{
    $desc = htmlspecialchars( $_GET["descripcion"]);
    $desc = str_replace("_", " ",$desc);
    if(isset($lab))
    {
        $items = $item->busqueda($desc,$lab);
    }else{
        $items = $item->busqueda($desc,array());
    }
}else{
    if(!isset($lab))
    {
        $items = $item->load();
    }
    
}


$count = 0;
?>
<div class="busqueda">
            <div class="row text-center">
                <div class="col-3"></div><div class="col-6"><input id="Busqueda" type="text" class="form-control align-top" style="display: inline;width: 75%" placeholder="Buscar...."> <button id="btnBusqueda" class="btn btn-danger align-top" style="width: 10%"><i class="bi bi-search"></i></button></div>
            </div>
            <br>
        <div class="row">
            <div class="col-12  text-center">
                <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                <?foreach($laboratorios as $key => $value){?> 
                    <input type="checkbox" class="btn-check btnLab" id="btn_<?= $value["id_laboratorio"] ?>" data-value="<?= $value["id_laboratorio"]?>" value = "<?= $value["id_laboratorio"] ?>" <? if(isset($lab)){foreach($lab as $llave => $valor){if($valor == $value["id_laboratorio"]){echo "checked";}} } ?>>
                    <label class="btn btn-outline-danger " for="btn_<?= $value["id_laboratorio"]?>"><?= $value["laboratorio"] ?></label>
                    <?} ?> 
                </div>
            </div>
        </div>
            <div class="col-12 my-3 text-center"><button class="btn btn-danger my-1" id="btnAgregar">Agregar Nuevo Material</button></div>
            <div class="container">
                <table class="table table-bordered border-danger">
                    <tr class="table-dark">
                        <th>Descripción</th>
                        <th>Clasificación</th>
                        <th>Características</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Consumo</th>
                        <th>Cuantificación</th>
                        <th>Editar</th>
                    </tr>
                    <? $count = 0; foreach($items as $key =>$value) {
                        if($count % 2)
                        {
                            $class = "table-dark";
                            $btn = "btn-outline-danger";
                        }else {
                            
                            $class = "table-danger";
                            $btn = "btn-outline-dark";
                        } 
                        $count = $count + 1;?>
                    <tr class="<?=$class?>">
                        <td><?=$value["desc"]?></td>
                        <td><? echo $value["clasificacion"]["parent"] . "." . $value["clasificacion"]["clasificacion"] ;?></td>
                        <td><div class="row justify-content-center">
                            <? foreach($value["caracteristicas"] as $llave => $valor) { 
                                foreach($valor["caracteristicas"] as $y => $dato) {  
                                    if(!$y){?>
                                <div class="col-11 border border-dark"><? echo $dato ." " . $valor["valor"] . " " . $valor["prefijo"]?></div>
                        <? }} } ?></div></td>
                        <td><?= $value["marca"][0]["marca"] ?></td>
                        <td><?= $value["modelo"][0]["modelo"] ?></td>
                        <td><?= $value["consumo"][0]["descrip"] ?></td>
                        <td><?= $value["cuantificacion"][0]["descrip"] ?></td>
                        <td><button class="btnEditar btn <?=$btn?>" id="<?= $value["id"] ?>"><i class="bi bi-pencil"></i></button></td>
                    </tr> <? } ?>
                    
                </table>

            </div>
        </div>
        
        <br>
            <script>
                var descripcion = "";
                var lab = "";
                <?if(isset($desc)){
            ?>   descripcion = "<?= $desc ?>";<?} ?>
            <? if(isset($lab)){?> lab = "<?=$lab ?>" <? } ?>
            </script>
            