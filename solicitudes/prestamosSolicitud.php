<?
header('Content-Type:text/html;charset=utf-8');
include "../includes/conectaBD.php";
include "../clases/InventarioPrestamoSrv.php";
include "../clases/InventarioSerieSrv.php";
include "../clases/validaciones.php";

InventarioPrestamoSrv::inicializaInventarioPrestamoSrv($link);
$alumno = htmlspecialchars($_GET["alumno"]);
$lab = htmlspecialchars($_GET["lab"]);
if(!validar::isNumber($alumno) || !validar::isNumber($lab))
{
    errorWindow("Parámetros inválidos");
}
$AdeudoActivos = InventarioPrestamoSrv::checkAdeudos($link,$alumno,$lab);
$solicitud = InventarioPrestamoSrv::getSolicitudAlumno($lab,$alumno);
InventarioSerieSrv::inicializaInventarioSerie($link);
function errorWindow($error)
{
    ?>
    <script>
        $("#headerModalWaring").html("<i class='bi bi-x-circle-fill text-danger fs-3'></i><h3 class='modal-title'>Error</h3>");
        $("#bodyModalWaring").html("<?=$error ?>");
        $("#modalWaring").modal("show");
        $("#btnCerrarModalWaring").on("click", function (){
            $("#modalWaring").modal("hide");
            $("#contenidoPrincipal").load("solicitudes/verSolicitudes.php", function(){
                $.getScript("js/versolicitudes.js");
                $("#btnCerrarModalWaring").off("click");
            });
        })
    </script>
    <?
    exit();
}
?>
  <div id="ContenidoSolicitud" class="card-body">
    <?if(true){ ?> 
        <h5 class="card-title">Solicitudes</h5>
        
        <table class="table">
            <tr data-id="0">
                <th>Descripción</th>
                <th>Disponibles</th>
                <th>Solicitados</th>
                <th>Entregados</th>
                <th>Fecha entrega</th>
                <th>Acción</th>
            </tr>
        <?php foreach($solicitud as $key => $value){  
      ?>
            <tr class="valortabla" data-id = "<?= $value["id_inventario_prestamo"] ?>">
                <td class="Descripcion"><?= $value["descripcion"] ?></td>
                <td class="cantidadDisponible"><?=$value["disponible"]; ?></td>
                <td><?=$value["cantidad_solicitada"]; ?></td>
                <? if($value["id_cuantificacion"] == "2"){ $series = InventarioSerieSrv::getSeries($value["id_inventario"],$lab);  ?> 
                    <td><select class="form-select serieSelect" name="" id="">
                        <option value="0">Selecciona una opción</option>
                    <? foreach($series as $llave => $valor) { ?> 
                        <option value="<?=$valor["id_serie_lote"]; ?>"><?=$valor["serie_clave"]; ?></option>
                        <?}  ?>
                    </select></td>
                    <? }else { ?> 
                    <td><div class="d-flex justify-content-center"><input type="number" class=" w-50 form-control cantidadSolicitud" value = "<?=$value["cantidad_solicitada"] ?>" max="<? if($value["disponible"] >= $value["cant_max"]){ echo $value["cant_max"]; }else{echo $value["disponibles"]; }  ?>"></div></td>
                    <?}  ?>
                
                <td><?=$value["fecha_entrega_programada"]; ?></td>
                <td><div class="d-flex justify-content-center"><input class="form-check-input checkDenegar" type="checkbox" value="1" id="flexCheckDefault" <?if($value["disponible"] == "0" || $value["disponible"] == "0.00"){ echo "checked";} ?>>
                        <label class="form-check-label" for="flexCheckDefault">Rechazar</label></div></td>
                    
            </tr>
            <script>
                window.document.solicitudes["prestamo_<?=$value["id_inventario_prestamo"]?>"] = {"id_prestamo" : <?=$value["id_inventario_prestamo"]?>,"id" : "<?= $value["id_existencia_lab"] ?>", "rechazar": 0 , "cantidad" : "<?=$value["cantidad_solicitada"]; ?>"<? if($value["id_cuantificacion"] == "2"){?>, serie: "0" <?}?>}
            </script>
            <?php } ?>
            
        </table>
        <div class="float-end">
            <button id="btnAceptarSolicitud" class="btn btn-lg btn-success">Aceptar Préstamos</button>

        </div>
        <?}else{ ?> 
        <h4>El alumno cuenta con adeudos, por lo que no es posible aceptar nuevas solicitudes.</h4>
        <script>
                window.document.solicitudes = null
            </script>
        <?php } ?>
    </div>