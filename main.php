<?php
header('Content-Type:text/html;charset=utf-8');
session_start();
if(!isset($_SESSION["usuario"]))
{
    header("location: index.php");
    exit();
}
if($_SESSION["usuario"] == "" )
{
  header("location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <title>Ibero_Préstamos</title>
</head>
<body>
    <div id="headerIberoPrestamos" class="bg-danger text-light d-flex justify-content-center">
        <h2 class="text-center">IBERO Préstamos Administrativo</h2>
    </div>
    <div class="wrapper mx-1">
        <!-- Sidebar - Menu lateral  -->
        <nav id="sidebar" class='active'>
            <ul class="list-unstyled components">
                <li><a id="agregarMaterial" class="module_menu" data-title="Agegar Material" data-ruta="inventario/mainAdmin.php" data-script="js/mainAdmin.js">Materiales</a></li>
                <li><a id="verArbol" class="module_menu" data-title="Arbol Materiales" data-ruta="clasificacion/clasificacion.php" data-script="js/clasificacion.js">Árbol de Materiales</a></li>
                <li><a id="agregarCaracteristicas" class="module_menu" data-title="Agregar Caracteristicas" data-ruta="caracteristicas/gestion_caracteristicas.php" data-script="js/caracteristicas.js">Agregar Características</a></li>
                <li><a id="verSolicitudes" class="module_menu" data-title="Solicitudes" data-ruta="solicitudes/verSolicitudes.php" data-script="js/versolicitudes.js">Solicitudes</a></li>
            </ul>

        </nav>
    </div>
    <br>
    <div class="row justify-content-end"><div class="col-2"><button id="btnCerrarSession" class="btn btn-outline-dark">Cerrar Sesión</button></div></div>
    <hr>
    <div class="row">
        <div class="col-1">

        </div>
        <div id="contenidoPrincipal" class="col-11">
        
        </div>

    </div>
    
    <!-- Modal -->
  <div class="container">
    <!-- Button to Open the Modal -->
    <button id="btnModalWaring" type="button" class="btn btn-primary" data-bs-toggle="modalWaring" data-bs-target="#modalWaring"
      hidden="true"></button>

    <!-- The Modal -->
    <div class="modal" id="modalWaring">
      <div class="modal-dialog">
        <div class="modal-content">

          <!-- Modal Header -->
          <div class="modal-header" id="headerModalWaring">
            <h4 class="modal-title">Aviso</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modalWaring"></button>
          </div>

          <!-- Modal body -->
          <div class="modal-body" id="bodyModalWaring"></div>

          <!-- Modal footer -->
          <div class="modal-footer">
            <button id="btnCerrarModalWaring" type="button" class="btn btn-secondary" data-bs-dismiss="#modalWaring">Cerrar</button>
          </div>

        </div>
      </div>
    </div>

  </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="js/main.js"></script>
</body>
</html>