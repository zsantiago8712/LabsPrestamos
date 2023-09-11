<?php
session_start();
header('Content-Type:text/html;charset=utf-8');
if(isset($_GET["sessionClose"]))
{
  $_SESSION["usuario"] = "";
  $_SESSION["area"] = "";
  $_SESSION["nombre"] = "";
  $_SESSION["tipo"] = "";
  session_destroy();
}

?>
<!DOCTYPE html>
<html lang="es">
    
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <title>IBERO_Prestamos_Laboratorios</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
            
    </head>
    <body>
      <div class="w-100 h-100">
        <header>
        <div id="headerIberoPrestamos" class="bg-danger text-light mb-4">
          <h2 class="text-center">PrestamosIBERO Prestamos Laboratorios <span class = "h3">V 1.0.0</span></h2>
          <h4 class = "text-light text-center bg-danger text-light"><a class="text-light" href="http://www.ibero.mx/">Universidad Iberoamericana</a></h4>
        </div>
        </header>

        <br>
        <br>
        <section class="d-flex d-md-inline-flex flex-row justify-content-center w-100 pt-3">   
          <form method="post" action="login.php" class="w-50 w-lg-">
                <label for="inputUser" class="form-label h4">
                  Usuario:
                </label>
                <input id="inputUser" class="form-control bg-danger text-white"  type="user" 
                        name="user" 
                        required/>
                <br/>
                
                <label for="inputPwd" class="form-label h4" >
                    Contraseña:
                </label>
                <input id="inputPwd" class="form-control bg-danger text-white"  
                        type="password" 
                        name="pwd" 
                        required/>
                <br>

                <input class="btn btn-outline-dark" id="btn" type="submit" value="Iniciar Sessión" />
          </form>
        </section>
        
        <br>
        <footer id="footer" class="bg-black text-white position-absolute bottom-0 w-100">
          <address class="text-center">
          Coordinación de Cómputo Académico, Universidad Iberoamericana Ciudad de México.<br>
          Prolongación Paseo de la Reforma 880, Lomas de Santa Fe, México, C.P. 01219, Distrito Federal.<br>
          Tel +52(55)5950-4000 y 9177-4400. Lada nacional sin costo: 01 800 627-7615.
          </address>
        </footer>
      
      </div>
      
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    
    </body>
</html>