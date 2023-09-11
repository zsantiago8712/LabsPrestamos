<?php


class validar{ 
    public static function isString($dato,$cracteres = 100)
    {
        $cadena = "/^[ _()A-ZÁ-Ýa-zá-ÿ0-9-\/\"\.]{1,". $cracteres ."}$/";
        return preg_match($cadena,$dato);
    }
    public static function isNumber($dato)
    {
        return is_numeric($dato);
    }
    public static function errorWindow($error = "ERROR")
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
}
?>