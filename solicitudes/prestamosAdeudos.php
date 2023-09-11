<?php
header('Content-Type:text/html;charset=utf-8');
include "../includes/conectaBD.php";
include "../clases/InventarioPrestamoSrv.php";
include "../clases/validaciones.php";
InventarioPrestamoSrv::inicializaInventarioPrestamoSrv($link);
$alumno = htmlspecialchars($_GET["alumno"]);
$lab = htmlspecialchars($_GET["lab"]);
if(!validar::isNumber($alumno) || !validar::isNumber($lab))
{
    validar::errorWindow("No se recibieron bien los datos para la solicitud.");
}

$adeudos = InventarioPrestamoSrv::getAdeudosAlumno($lab,$alumno);
if($adeudos === false)
{
  validar::errorWindow("No se pudo completar la solicitud. Error: " . InventarioPrestamoSrv::$lasterror);
}
?>
<div id="ContenidoAdeudos" class="card-body">
        <h5 class="card-title"> Adeudos Activos</h5>
        <table class="table">
            <tr>
                <th>Descripción</th>
                <th>Prestados</th>
                <th>Comentarios</th>
                <th>Fecha_entrega</th>
                <th>Acción</th>
            </tr>
        <?php foreach($adeudos as $key => $value){  
      ?>
            <tr data-id = "<?= $value["id_inventario_prestamo"] ?>" data-adeudo = "<?= $value["id_adeudo"] ?>">
                <td><?= $value["descripcion"] ?></td>
                <td><?=$value["cantidad_entregada"]; ?></td>
                <td><?=$value["comentarios"]; ?></td>
                <td><?=$value["fecha_entrega_programada"]; ?></td>
                <td><div class="d-flex justify-content-center"><button class="btn btn-primary mx-4 btnQuitarAdeudo">Quitar adeudo</button></div></td>  
            </tr>
            <script>
                window.document.adeudos["prestamo_<?=$value["id_inventario_prestamo"]?>"] = {"id_prestamo" : <?=$value["id_inventario_prestamo"]?>,"id" : "<?= $value["id_existencia_lab"] ?>"};
            </script>
            <?php }if(count($adeudos) <= 0){ ?> 
                <tr>
                    <td colspan="5"><h2>No hay adeudos activos.</h2></td>
                </tr>
                <?} ?>
        </table>
    </div>