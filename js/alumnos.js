(function (){
    let urlPrincipal = "solicitudes/prestamosSolicitud.php?alumno=" + window.document.alumnoSolicitud + "&lab=" + window.document.labSolicitud;
        $("#ContenidoAlumnoSolicitudes").load(urlPrincipal,function (){
            $.getScript("js/prestamosSolicitud.js", function (){});
            });
    $("#btnPrestamos").on("click", function (){
        $("#btnSolicitud").removeClass("active");
        $("#btnAdeudosAlumno").removeClass("active");
        $(this).addClass("active");
        let url = "solicitudes/prestamosActivos.php?alumno=" + window.document.alumnoSolicitud + "&lab=" + window.document.labSolicitud;
        $("#ContenidoAlumnoSolicitudes").load(url,function (){
            $.getScript("js/prestamosActivos.js", function (){});
            });
    });
    $("#btnSolicitud").on("click", function (){
        $("#btnPrestamos").removeClass("active");
        $("#btnAdeudosAlumno").removeClass("active");
        $(this).addClass("active");
        let url = "solicitudes/prestamosSolicitud.php?alumno=" + window.document.alumnoSolicitud + "&lab=" + window.document.labSolicitud;
        $("#ContenidoAlumnoSolicitudes").load(url,function (){
            $.getScript("js/prestamosSolicitud.js", function (){});
            });
    });
    $("#btnAdeudosAlumno").on("click", function (){
        $("#btnPrestamos").removeClass("active");
        $("#btnSolicitud").removeClass("active");
        $(this).addClass("active");
        let url = "solicitudes/prestamosAdeudos.php?alumno=" + window.document.alumnoSolicitud + "&lab=" + window.document.labSolicitud;
        $("#ContenidoAlumnoSolicitudes").load(url,function (){
            $.getScript("js/prestamosAdeudos.js", function (){});
            });
    });
    $("#btnVolver").on("click", function (){
        $("#contenidoPrincipal").load("solicitudes/verSolicitudes.php?lab="+ window.document.labSolicitud, function(){
            $.getScript("js/versolicitudes.js");
        });
    });
   
   
    
})()