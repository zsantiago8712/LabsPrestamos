<?php header('Content-Type:text/html;charset=utf-8');
session_start();
$Laboratorios = $_SESSION["laboratorios"];
$id_lab = $_SESSION["id_laboratorios"];
include ("../includes/conectaBD.php");
include ("../clases/Item.class.php");
$Item = new item($link);
$estatus = $Item->get_estatus();
$caracteristicas = new Caracteristica($link);
$caracteristica = $caracteristicas->get_caracteristicas();
$prefijos = $caracteristicas->get_prefijos();
$Cuanti = new cuantificacion($link);
$Consumo = new consumo($link);
$Clasificacion = new jsTree($link);
$count = 3;
$Consumo = $Consumo->load();
$Marca = new marca($link);
$Marca = $Marca->load();
$Modelo = new Modelo($link);
$Modelo = $Modelo->load();
$cuantificacion = $Cuanti->load();
$Arbol = $Clasificacion->load();
$titulo = "Agregar Material";
$clasi = "Selecciona una clasificacion";
$Unidades = $Item->get_unidad();
if(isset($_GET["id"]))
{
  $id = $_GET["id"];
  $num = 1;
  $item = $Item->load($id);
  $item = $item[0];
  $titulo = "Editar Material";
  $clasi = $item["clasificacion"]["parent"] . "." . $item["clasificacion"]["clasificacion"];
  $cantidadLaboratorios = $Item->get_Existencias($id);
  $generaleslab = $Item->getGenerales_Lab($id);
}
function isString($dato)
{
    return preg_match("/^[ a-zá-ÿÁ-Ý]{1,50}$/i",$dato);
}
?>
<style>
  .col-6 {
    margin-top: 1.5rem;
  }
  input {
    width: fit-content;
    text-transform:uppercase
  }
  .mayus{
    text-transform:uppercase;
  }
</style>
<br>
<div class="container"><h3><?= $titulo?></h3></div>

<hr>
<div id="tabs" class="container">
  <div id="tab-contenido">
    <script>
      window.document.laboratorios = new Array();
    </script>
  
  <div id="tabs-1">
    <form id="General" data-tipo="<?if(isset($item)){ echo $id;}else { echo "0";} ?>">

    
    <div class="row">
        <div class="col-6">
            <label for="descripcion"><b>Descripción</b></label>
            <input type="text" style="text-transform:uppercase" name="descripcion" id="Descripcion" class="form-control" <?if(isset($item)){echo "value='" . $item["desc"] . "'";} ?> required>
        </div>
        <div class="col-6">
            <label for="consumo"><b>Tipo Consumo</b></label>
            <select name="consumo" id="Consumo" class="form-select">
            <option <?if(!isset($item)){echo "selected";} ?> value="0">Selecciona un Tipo de Consumo</option>
            <? foreach($Consumo as $key =>$value) {  ?>
                <option <?if(isset($item)&& $value["descrip"] == $item["consumo"][0]["descrip"] ){echo "selected";} ?> value="<?= $value["id_consumo"] ?>"><?= $value["descrip"] ?></option>
                <? } ?>
            </select>
          </div>
          <div class="col-6">
            <label for="marca"><b>Marca</b></label>
            <select id="marca" name ="marca" class="form-select" style="text-transform:uppercase">
              <?if(isset($item)){?> <?}else{ ?><option selected="selected" value ="0">Selecciona una marca</option> <?} ?>
              <? foreach($Marca as $key =>$value) {  ?>
                <option <?if(isset($item) && $item["marca"][0]["marca"] == $value["marca"]){ echo "selected";}?> value="<?= $value["id_marca"] ?>"><?= $value["marca"] ?></option>              
              <? } ?> </select>
        </div>
        <div class="col-6">
            <label for="modelo"><b>Modelo</b></label>
            <select name="modelo" id="Modelo" class="form-select" style="text-transform:uppercase">
            <?if(isset($item)){?>
              <option selected="selected" value="<?=$item["modelo"][0]["id_modelo"] ?>"><?= $item["modelo"][0]["modelo"]?>  </option>
              <?}else{ ?><option selected="selected" value ="0">Selecciona un modelo</option><?} ?>
              
            </select>
            
        </div>
        <div id="cuanti" class="col-6">
            <label for="input2"><b>Cuantificación</b></label> <br>
            <?foreach($cuantificacion as $key => $value){  ?>
            <input class="form-check-input" type="radio" name="cuantificacion" data-value="<?=$value["id_cuantificacion"]?>" id="<?= $value["descrip"] ?>" required <?if(isset($item)&& $value["descrip"] == $item["cuantificacion"][0]["descrip"] ){echo "checked";} ?> <? if(isset($item)){echo "disabled";} ?>>
            <label class="form-check-label" for="<?= $value["descrip"] ?>">
            <?= $value["descrip"] ?>
            </label>
            <br>
            <? }?>
        </div>
        <div class="col-6">
            <label for="input5"><b>Clasificación</b></label>
            <a id="btnJstree" class="btn btn-primary" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample" data-id="<?if(isset($item)){echo $item["id_clasificacion"]; }else {echo "0";} ?>">
            <?= $clasi ?>
            </a>
            <div class="collapse" id="collapseExample">
              <div id="Clasificacion" class="card card-body">
              <div class="row my-5">
                <div id="jTree" class="bg-light col-10">

                </div>
              <script>
                var dtsTree = JSON.parse('<?echo json_encode($Arbol)?>');
              </script>
                </div>
              </div>
            </div>
           
        </div>
        <div class="col-6">
          <label for="numParte"><b>Num_Parte</b></label>
          <input id="numParte" type="text" class="form-control w-25" name="numParte" placeholder="opcional" <?if(isset($item)){if($item["num_parte"] == null){}else{echo "value='". $item["num_parte"] ."'";}} ?>>

        </div>
        <div class="col-6">
        <label for="unidad"><b>Unidad</b></label>
          <div class="row">
            <div class="col-6">
              <select name="unidad" id="Gunidad" class="form-select"> 
                <?foreach($Unidades as $key => $value){  ?>
                <option value="<?= $value["id_unidad"] ?>"  <?if(isset($item)){if($item["id_unidad"] == $value["id_unidad"]){echo "selected"; $etiquetaUnidad = $value["unidad"]; }} ?>><?= $value["unidad"] ?></option>
              <? } ?></select>
            </div>
            <div class="col-6">
            <select name="unidadPrefijo" id="unidadPrefijo" class="form-select w-50"><?php foreach($prefijos as $key => $value){ ?> 
              <option value="<?= $value["id_prefijo"]?>" <?if(isset($item)){if($item["id_prefijo"] == $value["id_prefijo"]){echo "selected"; }} ?>><?= $value["nombre"]?>(<?= $value["simbolo"]?>)</option>
      <? } ?></select>
            </div>

            </div>
        </div>

    </div>
    </form>
  </div>
  <select class='form-select align-middle' name='estado[]' id="EstatusPrincipal">
    <?foreach($estatus as $llave => $valor)
    {?>
    <option value="<?=$valor["id_estatus"] ?>"><?=$valor["estatus"] ?></option>

    <?} ?></select>
  <?php foreach($Laboratorios as $key => $lab){ ?>
  <div id="tabs-<?=$count?>">
  <div class="row">
        <div class="col-3">
        <label for="serieCantidad" class="form-label">Cantidad máxima a prestar</label>
        <input data-id ="<? if(isset($item)){ $miid = 0;foreach($generaleslab as $llave => $valor){if($valor["id_laboratorio"] == $id_lab[$key]){$miid = $valor["id_existencia_lab"];}} echo $miid;}else{echo "0";} ?>" type="number" id="serieCantidad" name="prestamo[]" class="form-control inputprestamo" <?if(isset($item)){foreach($generaleslab as $llave => $valor){ if($valor["id_laboratorio"] == $id_lab[$key]){echo "value='". $valor["prestamos"] ."'";}}}else{} ?>>
      </div>
      <div class="col-3">
        <label for="serieDias" class="form-label">Días máximos a prestar</label><input type="number" id="serieCantidad" name="dias[]" class="form-control inputdias" max="30" <? if(isset($item)){foreach($generaleslab as $llave => $valor){if($valor["id_laboratorio"]==$id_lab[$key]){echo "value = '". $valor["dias"] ."'";}}} ?>>
      </div>
      <div class="col-3">
        <label for="serieRenovar" class="form-label">Renovable/No renovable</label>
        <select name="renovar[]" id="serieRenovar" class="form-select selectrenovar">
          <option value="1">Renovable</option>
          <option <?if(isset($item)){foreach($generaleslab as $llave => $valor){if($valor["id_laboratorio"]==$id_lab[$key]&&$valor["renovar"] == "2"){echo "selected";}}} ?> value="2">No renovable</option>
        </select>
      </div>
      <div class="col-3 position-relative">
        <div class="position-absolute bottom-0">
        <input class="form-check-input serieVisibilidad" type="checkbox" name="visibilidad" value="1" <?if(isset($item)){foreach($generaleslab as $llave => $valor){if($valor["id_laboratorio"]==$id_lab[$key] && $valor["visibilidad_web"] == "1"){ echo "checked";}}} ?>>
            <label class="form-check-label" for="visibilidad">
            Visible en prestamos alumnos
            </label>
        </div>
      
      </div>
    </div>
    <br>
    <div class="none"  style='display: none'>
    <label class="label" for="serieControl"></label>
    <input class="contadorSerie" type="hidden" value="0">
    <button class="btn btn-danger btnMas"><i class="bi bi-plus-square-fill"></i></button>
    <span class="laboratoriosFormularioSerie" data-type = "<?= $lab ?>">
    
      <table class="table tableSerie">
        <?
        if(isset($item))
        {
          if($item["cuantificacion"][0]["descrip"] == "LOTE/CADUCIDAD")  
          {
            foreach($cantidadLaboratorios as $llave => $valor)
            {
              if($id_lab[$key] == $valor["id_laboratorio"]) { 
              $loteCaducidad = explode(" ", $valor["num_o_lote_y_caducidad"]);
              ?>
              <tr class='filaSerie'>
                <td><button style='height: 10px; width: 10px' class='btn btn-danger'>X</button></td>
                <td><span><?echo $llave + 1 ?></span></td>
                <td><input name='serie[]' data-id="<?= $valor["id_serie_lote"] ?>" type = 'text' class='form-control' style='width: 10rem' value='<?= $loteCaducidad[0] ?>'></td>
                <td><input name='qr[]' type = 'text' class='form-control' style='width: 10rem' value='<?= $valor["qr"] ?>'></td>
                <td><input size='16' name='fecha[]' type='date' class='form-control' id='datetime' value='<?= $loteCaducidad[1] ?>' required></td>
                <td><input name='cantidadLote[]' type = 'text' class='form-control' style='width: 10rem' value='<?= $valor["existencia"] ?>'></td>
                
              </tr>
              <?}
            }

          }elseif($item["cuantificacion"][0]["descrip"] == "NÚMERO SERIE")
          {
            
            foreach($cantidadLaboratorios as $llave => $valor)
            {
              if($id_lab[$key] == $valor["id_laboratorio"]) { 
                $numSerie = $valor["num_o_lote_y_caducidad"];
                ?>
                 <tr class='filaSerie'>
                  <td><button style='height: 10px; width: 10px' class='btn btn-danger'>X</button></td>
                  <td><span><?echo $llave + 1 ?></span></td>
                  <td><input name='serie[]' data-id="<?= $valor["id_serie_lote"]?>" type = 'text' class='form-control' style='width: 10rem' value='<?= $numSerie ?>'></td>
                  <td><input name='uia[]' type = 'text' class='form-control' style='width: 10rem' value='<?= $valor["clave_interna"] ?>'></td>
                  <td><input name='qr[]' type = 'text' class='form-control' style='width: 10rem' value='<?= $valor["qr"] ?>'></td>
                  <td><select class='form-select align-middle' name='estado[]'>
                  <? foreach($estatus as $piz => $dato){  ?>
                <option <?if($valor["id_estatus"] == $dato["id_estatus"]){echo "selected";} ?> value='<?= $dato["id_estatus"] ?>'><?= $dato["estatus"] ?></option>
                
                <? } ?></select></td>
                </tr>
                <?
              }
            }

          }
          else{
            $bandera = 1;
          }
        } 
        ?>
      
      </table>
    </span>
    </div>
  
  <span class="laboratoriosFormularioCantidad">
    <table id="tableCantidad" class="table">
      <tr class="table-dark">
        <th>Cantidad</th>
      </tr>
      <tr>
        <td><input name="cantidad[]" type="text" class="form-control w-50" <?if(isset($bandera)){foreach($cantidadLaboratorios as $llave => $valor){ if($valor["id_laboratorio"] == $id_lab[$key]){echo "value='". $valor["existencia"] ."'";}}} ?>></td>
        <td class="EtiquetaUnidad"><? if(isset($etiquetaUnidad)){echo $etiquetaUnidad;} ?></td>
      </tr>
    </table>
  
  </span>
  
  </div>
  <? $count = $count +1; } $count = 3;?>


  <div id="tabs-2" class="row">
    <h3>Características:</h3>
    <select id="CaracteristicaPrincipal" name="caracteristica[]" class='form-select' data-id="0">
        <option value="0">Selecciona una Característica</option> <? foreach($caracteristica as $key =>$value){ ?>
          <option value="<?= $value["id_caracteristica"]?>" data-tipo="<?=$value["tipo"] ?>"><?= $value["caracteristica"] ?>(<?=$value["simbolo"] ?>)</option>
      <? } ?></select>
      <select id="Prefijos" name="prefijo[]" class='form-select'><?php foreach($prefijos as $key => $value){ ?> 
        <option value="<?= $value["id_prefijo"]?>"><?= $value["nombre"]?>(<?= $value["simbolo"]?>)</option>
      <? } ?></select>
  <div class="col-2"><button id="btnMasCar" class="btn btn-danger mx-5"><i class="bi bi-plus-square-fill"></i></button></div> <br>
  <div class="table-responsive">
  <table class="table">
    <tr id="tablaCaracteristicaTitulos"><th>Borrar</th><th>#</th><th>Característica</th><th>Prefijo</th><th>Valor</th></tr>
    <?if(isset($item)&& $item["caracteristicas"][0]["caracteristicas"][0] != "Sin caracteristicas"){ foreach($item["caracteristicas"] as $llave => $valor) {?> 
      <tr class='my-1 caracteristicaGeneral'>
      <td><button style='' class='btn btn-danger btnBorrarCaracteristicaActual'>X</button></td>
      <td><? echo $num; $num=$num+1; ?></td>
      <td><select  name="caracteristica[]" class='form-select' <?echo "data-id='". $valor["id"] ."'"; ?>>
        <option value="0">Selecciona una Característica</option> <? foreach($caracteristica as $key =>$value){ ?>
          <option <?if($valor['caracteristicas'][0] == $value["caracteristica"] ){echo "selected "; $tipo = $caracteristicas->get_all_caracteristica($value["id_caracteristica"]); }?> value="<?= $value["id_caracteristica"]?>" data-tipo="<?=$value["tipo"] ?>"><?= $value["caracteristica"] ?>(<?=$value["simbolo"] ?>)</option>
      <? } ?></select></td>
      <?if($valor["valor"] == null || isString($valor["valor"]))
      { ?>
      <td>Sin prefijo</td>
      <td><select name='valor[]' class='form-select'>
      <? foreach($tipo[0] as $key => $value) { ?>
        <option <?if($valor["caracteristicas"][1] == $value["valor"]){echo "selected";} ?> value='<?= $value["id_lista"] ?>'><?= $value["valor"] ?></option>
      <? }?>
      </select></td>
      <? $tipo = 0;}else {?>
        <td><select id="Prefijos" name="prefijo[]" class='form-select'> <option value="0">Selecciona un Prefijo</option> <?php foreach($prefijos as $key => $value){ ?> 
        <option <?if($valor["prefijo"]== $value["nombre"]){echo "selected";} ?> value="<?= $value["id_prefijo"]?>"><?= $value["nombre"]?>(<?= $value["simbolo"]?>)</option>
      <? } ?></select></td>
      <td><input name='valor[]' type='text' class ='form-control' value="<?=$valor["valor"] ?>"></td>

      <?} ?>
      </tr>
      
    <?}} ?>
  </table>
  </div>
  </div>


  </div>
  
  
  <ul id="tabs-menu">
    <li><a href="#tabs-1">Generales</a></li>
    <li><a href="#tabs-2">Características</a></li>
    <?php foreach($Laboratorios as $key => $lab){  
      ?>
      <script>
        window.document.laboratorios.push("<?=$lab?>");
    </script>
      <li><a href="#tabs-<?=$count?>"><?= $lab ?></a></li>
      <? $count = $count +1; } ?>
    <br>
  </ul>
</div>
<br>
<button id="btnRegresar" class="btn btn-danger mx-4 display:inline">Regresar</button>
<button id="btnGuardar" class="btn btn-danger mx-4 display:inline" >Guardar</button>
