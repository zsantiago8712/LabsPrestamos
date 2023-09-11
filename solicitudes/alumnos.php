<?
header('Content-Type:text/html;charset=utf-8');
include "../clases/InventarioPrestamoSrv.php";
include "../clases/AlumnosSrv.php";
include "../clases/InventarioSerieSrv.php";
include "../clases/validaciones.php";
include "../includes/conectaBD.php";
if(!isset($_GET["id"]) || !isset($_GET["id_lab"]))
{
    errorWindow("Parámetros incorrectos");
}
$id_usuario = htmlspecialchars($_GET["id"]);
$id_lab= htmlspecialchars($_GET["id_lab"]);
if(!validar::isNumber($id_usuario) || !validar::isNumber($id_lab))
{
    errorWindow("Parámetros incorrectos");
}
$alumno = new alumno($link, $id_usuario);
InventarioPrestamoSrv::carritoPorEntregar($link,$alumno->cuenta,$alumno->tipo_usuario,$id_lab);
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
<script>
    window.document.solicitudes = new Object();
    window.document.activos = new Object();
    window.document.adeudos = new Object();
    window.document.alumnoSolicitud = <?=$id_usuario ?>;
    window.document.labSolicitud = <?=$id_lab ?>;
</script>
<div id="ContenidoAlumno" data-id= "<?= $id_usuario ?>" data-lab = "<?= $id_lab ?>" class="container">
<table class="table">
    <tr>
        <th>Cuenta</th>
        <th>Nombre</th>
        <th>Volver</th>
    </tr>
    <tr>
    <td><?= $alumno->cuenta?></td>
        <td><? echo $alumno->nombre . " " . $alumno->ap_paterno . " " . $alumno->ap_materno; ?></td>
        <td><button id="btnVolver" class="btn btn-outline-primary">Volver</button></td>
    </tr>
</table>
<div class="card text-center">
    <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs">
        <li class="nav-item">
            <button id="btnSolicitud" class="nav-link active" aria-current="true">Solicitudes</button>
        </li>
        <li class="nav-item">
            <button id="btnPrestamos" class="nav-link">Activos</button>
        </li>
        <li class="nav-item">
            <button id="btnAdeudosAlumno" class="nav-link">Adeudos</button>
        </li>
    </ul>
    </div>
    <div id="ContenidoAlumnoSolicitudes">

    </div>
  

    

    

    
    

</div>
</div>
