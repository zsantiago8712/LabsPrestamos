<?php 
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
    validar::errorWindow("Parámetros inválidos");
}
$activos = InventarioPrestamoSrv::getActivosAlumno($lab,$alumno);
InventarioSerieSrv::inicializaInventarioSerie($link);
if($activos === false)
{
  validar::errorWindow("Error al consultar la solicitud.");
}
?>
<div id="ContenidoPrestamos" class="card-body">
        <h5 class="card-title"> Préstamos Activos</h5>
        <table class="table">
            <tr>
                <th>Descripción</th>
                <th>Prestados</th>
                <!-- <th>Entregados</th> -->
                <th>Dañados</th>
                <th>Fecha entrega</th>
                <th>Acción</th>
            </tr>
        <?php foreach($activos as $key => $value){  
      ?>
            <tr data-id = "<?= $value["id_inventario_prestamo"] ?>">
            <? if($value["id_cuantificacion"] == "2" || $value["id_cuantificacion"] == "3"){ $serie = InventarioSerieSrv::getSerieLote($value["id_serie_lote"]); } else{$serie = "";};  ?>
                
                <td><?= $value["descripcion"] ?> <? if($serie != ""){ echo "Clave_uia: " .$serie; }?></td>
                <td class="cantidadPrestada"><?=$value["cantidad_entregada"]; ?></td>
                <!-- <td><div class="d-flex justify-content-center"> -->
		    <input type="hidden" class=" w-50 form-control cantidadSolicitud" value = "<?=$value["cantidad_entregada"] ?>">
		<!-- </div></td> -->
                <td><div class="d-flex justify-content-center"><input type="number" class=" w-50 form-control danados" value = "0"></div></td>
                <td><?=$value["fecha_entrega_programada"]; ?></td>
                <td><div class="d-flex justify-content-center"><button class="btn btn-primary mx-4 btnEntregar" data-serie="<?if($serie != ""){echo $value["id_serie_lote"];}else{echo 0;} ?>" >Entregar</button><button type="button" class="btn btn-danger btnAdeudo" data-bs-toggle="modal" data-bs-target="#modalAdeudo">Adeudo</button></div></td>  
            </tr>
            <script>
                window.document.activos["prestamo_<?=$value["id_inventario_prestamo"]?>"] = {"id_prestamo" : <?=$value["id_inventario_prestamo"]?>,"id" : "<?= $value["id_existencia_lab"] ?>" , "cantidad" : "<?=$value["cantidad_solicitada"]; ?>"};
            </script>
            <?php } if(count($activos) == 0){
              ?>
              <h4>El usuario no tiene actualmente préstamos activos.</h4>
            <?} ?>
        </table>
    </div>

    
<!-- Modal -->
<div class="modal fade" id="modalAdeudo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Reporte de Adeudo</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container">
            <label for="tipoAdeudo" class="form-label">Tipo de Adeudo</label>
            <select name="" id="tipoAdeudo" class="form-select">
                <option value="1">Adeudo</option>
            </select>
            <div class="mb-3">
                <label for="comentarioAdeudo" class="form-label">Comentarios</label>
                <textarea class="form-control" id="comentarioAdeudo" rows="3"></textarea>
            </div>
            <br>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button id="Adeudo" data-id="0" data-usuario="<?=$alumno ?>" type="button" class="btn btn-danger">Generar Adeudo</button>
      </div>
    </div>
  </div>
</div>