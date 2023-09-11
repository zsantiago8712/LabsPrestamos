<?php header('Content-Type:text/html;charset=utf-8'); 
include ("../includes/conectaBD.php");
include ("../clases/caracteristicas.class.php");

$caracteristicas = new Caracteristica($link);
$datos = $caracteristicas->load();
$flagLista = array();

$cont = 0;
?>

<div class = "row">
    <div id="liz" class = "col-5 mx-5">
        <input name = "inId_caracteristica" class="form-control" type = "hidden">
            <div class="form-text">
                Ingrese la unidad con la cual se mide la característica
            </div>
        <div class = "row row pb-3">
            <label class="form-label">Característica o  Magnitud</label>
            <input name="inCar" class="form-control" >
                <div class="form-text">
                    Ingrese la Característica única o magnitud de medición del material
                </div>
        </div>
        <div class = "row row pb-3">
            <label class="form-label">Unidad de medición</label>
            <input name = "inUni" class="form-control" >
                <div class="form-text">
                    Ingrese la unidad con la cual se mide la característica
                </div>
        </div>
        <div class = "row pb-3">
        <div class = "col mx-2">
            <label class="form-label">Abreviatura</label>
                <input name ="inAbr" class="form-control w-50" >
                    <div class="form-text">
                    Unidad de medición del material
                    </div>
        </div>
        </div>
        <div class = "row pb-3">
        <label class="form-label">Tipo</label>
        <div class="form-text">
                Ingrese el tipo de material
        </div>
        <select id ="selectTipo" name = "selTipo" class="form-select">
            <option selected>Opciones de tipo</option>
            <option value="2">Lista</option>
            <option value="1">Numérico</option>
        </select>
        </div>
        <div id="nuOpcDiv" class="col-12 text-center w-2" style = "display: none">
            <button class="btn btn-danger my-2" id="nuOpc">Nueva Opción</button>
        </div>
    </div>

    <div class ="accordion col-5 mx-5">
        <div class = "accordion-item">
            <div>
                <div class="accordion-header btn-alert">
                    <button class="accordion-button bg-danger text-dark" type = "button" data-bs-toggle="collapse" data-bs-target="#collapseTabla">
                        Tabla de características
                    </button>
                </div>

        <div id="collapseTabla" class="accordion-collapse collapse show">
            <div class="accordion-body">

            <table id="inven_car" class="table table-bordered border-danger">
                <tr class="table-dark w-auto">
                    <th>Característica o Unidad</th>
                    <th>Abreviatura</th>
                    <th>Tipo</th>
                    <th>Opciones</th>
                </tr>
                <?
            foreach($datos as $key => $value){?>
                <tr <?
                if(!$value["id_lista"]){
                    if($cont % 2){
                        echo('class = "table-dark"');
                    }else{
                        echo('class = "table-danger"');
                    }?>>
                    <td><?=$value["caracteristica"]?></td>
                    <td><?=$value["simbolo"]?></td>
                    <td><?if($value["tipo"]== 1){
                        echo("Numérico");
                    }else{
                        echo("Lista");
                    }?></td>
                    <?if($cont % 2){?>
                        <td><button id = "<?=$value["id_caracteristica"]?>" class="btn btn-outline-danger edit-btn">
                            <i class="bi bi-pencil"></i></button>
                        </td>
                    <?}else{?>
                        <td><button id = "<?=$value["id_caracteristica"]?>" class="btn btn-outline-dark edit-btn">
                            <i class="bi bi-pencil"></i></button>
                        </td>
                    <?}
                $cont++;   
                }elseif(!in_array($value["id_caracteristica"], $flagLista)){
                    if($cont % 2){
                        echo('class = "table-dark"');
                    }else{
                        echo('class = "table-danger"');
                    }?>>
                    <td><?=$value["caracteristica"]?></td>
                    <td><?=$value["simbolo"]?></td>
                    <td><?if($value["tipo"]== 1){
                        echo("Numérico");
                    }else{
                        echo("Lista");
                    }?></td>
                    <?if($cont % 2){?>
                        <td><button id = "<?=$value["id_caracteristica"]?>" class="btn btn-outline-danger edit-btn">
                            <i class="bi bi-pencil"></i></button>
                        </td>
                    <?}else{?>
                        <td><button id = "<?=$value["id_caracteristica"]?>" class="btn btn-outline-dark edit-btn">
                            <i class="bi bi-pencil"></i></button>
                        </td>
                    <?}
                array_push($flagLista, $value["id_caracteristica"]);
                $cont++;
                }
            }
                ?>
            </table>
            </div>
        </div>
      </div>
    </div>
</div>
<div id="btnCancel" class="col-12 text-center w-2" style = "display: none">
            <button class="btn btn-danger my-2" id="nuOpc">Cancelar</button>
</div>
<div class="col-12 text-center"><button class="btn btn-danger my-2" id="btnAgregar">Agregar Nueva Característica</button></div>