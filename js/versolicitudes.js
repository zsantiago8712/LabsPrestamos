(function(){
    $("#btnBuscar").on("click", function(){
        let search = $("#inputBusqueda").val();
        let laboratorio = $(".btn_lab:checked").val();
        var url = "solicitudes/verSolicitudes.php?lab=" + laboratorio
        if(search != "")
        {
            url = "solicitudes/verSolicitudes.php?cuenta=" + search + "&lab=" + laboratorio;
        }
        $("#contenidoPrincipal").load(url,function (){
            $.getScript("js/versolicitudes.js", function (){});
            });
    });
    $(".btnDetalles").on("click",function (){
        let laboratorio = $(".btn_lab:checked").val();
        let id_usuario = $(this).attr("data-id");
        let url = "solicitudes/alumnos.php?id_lab=" + laboratorio + "&id=" + id_usuario;
        $("#contenidoPrincipal").load(url, function (){
            $.getScript("js/alumnos.js");
        });
    });
    $(".btn_lab").on("click", function (){
        let cuenta = window.document.busqueda;
        let lab = $(this).val();
        var  url = "solicitudes/verSolicitudes.php?lab=" + lab
        if(cuenta != 0)
        {
            url = url + "&cuenta=" + cuenta
        }
        $("#contenidoPrincipal").load(url,function (){
            $.getScript("js/versolicitudes.js", function (){});
            });
    });
})()
