<?php header('Content-Type:text/html;charset=utf-8'); 
include ("../includes/conectaBD.php");
include ("../clases/jsTree.class.php");
$jsTree = new jsTree($link);
$Arbol = $jsTree->load();
?>
<div id="btnRow" class="row justify-content-center">
    <div class="col-3">
        <button id="Agregar" class="btn btn-success">Agregar</button>
    </div>
    <div class="col-3">
        <button id="Editar" class="btn btn-primary">Editar</button>
    </div>
    <div class="col-3">
        <button id="Borrar" class="btn btn-danger">Borrar</button>
    </div>
</div>
<div class="row my-5">
    <div id="jTree" class="bg-light col-10">

    </div>
    <div class="col-3 mx-5"> <button class="btn btn-danger my-5" id="Guardar">Guardar</button></div>
   
   <script>
    var dtsTree = JSON.parse('<?echo json_encode($Arbol)?>');
   </script>
</div>
