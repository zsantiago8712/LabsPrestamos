(function (){
    $("#btnAceptarSolicitud").on("click", function (){
        $(this).prop("disabled",true);
        var bandera = 0;
        var error;
        $("#ContenidoSolicitud table tr[class='valortabla']").each(function (){
            if($(this).attr("data-id") != "0")
            {
                let disponible = $(this).find(".cantidaSolicitud").attr("max");
                let cantidad = $(this).find(".cantidadSolicitud").val();
                let id = $(this).attr("data-id");
                let descripcion = $(this).find(".Descripcion").text();
                if(cantidad == undefined)
                {
                    let serie = $(this).find(".serieSelect").val();
                    if(serie == "0")
                    {
                        if(!$(this).find(".checkDenegar").is(':checked'))
                        {
                            bandera = 1;
                            error="Seleccione un número de serie para " + descripcion;
                            $("#btnAceptarSolicitud").prop("disabled",false);
                            return;
                        }
                        
                    }
                    window.document.solicitudes["prestamo_" + id].cantidad = 1;
                    window.document.solicitudes["prestamo_" + id].serie = serie;
                }else { 
                    window.document.solicitudes["prestamo_" + id].cantidad = cantidad;
                if(+cantidad <= 0 && !$(this).find(".checkDenegar").is(':checked'))
                {
                    bandera = 1;
                    error = "Cantidad inválida en: " + descripcion;
                    $("#btnAceptarSolicitud").prop("disabled",false);
                    return;
                }}

                window.document.solicitudes["prestamo_" + id].descripcion = descripcion;
                if($(this).find(".checkDenegar").is(':checked'))
                {
                    window.document.solicitudes["prestamo_" + id].rechazar = 1;
                }
                if(+disponible < +cantidad)
                {
                    bandera = 1;
                    error = "Inventario insuficiente para: " + descripcion;
                    $("#btnAceptarSolicitud").prop("disabled",false);
                    return;
                }
                
                console.log(window.document.solicitudes["prestamo_" + id].cantidad);
            }

            
           
        });
        if(bandera)
            {
                modalAlert(error);
                return;
            }
        var id_usuario = $("#ContenidoAlumno").attr("data-id");
        $.ajax({
            url: "solicitudes/solicitudCNT.php",
            type: "POST",
            dataType: "JSON",
            data: {"solicitud" : window.document.solicitudes, "id_usuario": id_usuario}
        }).done(function (response){
            $("#btnAceptarSolicitud").prop("disabled",false);
            if(response["success"])
            {
                if(response["warning"])
                {
                    
                    $("#ContenidoSolicitud table tr[class='valortabla']").each(function (){
                        var eliminarFilas  = 0;
                        for (var i = 0; i < response["warningid"].length; i++) {
                            if($(this).attr("data-id") == response["warningid"][i])
                            {
                                 eliminarFilas = 1;
                                 
                                $("<tr><td colspan='6'>"+ response["warningMessage"][i] +"</td></tr>").insertAfter("#ContenidoSolicitud table tr[data-id='"+ response["warningid"][i] +"']"); 
                            }
                        }
                        if(eliminarFilas == 0)
                        {
                            $(this).remove();
                            delete window.document.solicitudes["prestamo_" + $(this).attr("data-id")];
                        }
                    });
                    

                }else {
                    modalAlert("Solicitud registrada","Aviso");
                    $("#ContenidoSolicitud table tr").each(function (){
                        if($(this).attr("data-id") != "0")
                        {
                            $(this).remove();
                        }
                    });

                }
                
            }else{
                modalAlert("Error al procesar la solicitud.")
            }
        }).fail(function (){
            $("#btnAceptarSolicitud").prop("disabled",false);
            modalAlert("Error en la comunicación con el servidor.");
        });
    });
    function modalAlert(message, mode="Error", title = ""){

        if(title == ""){
            title = mode;
        }
        //Si es un modo no existente, que sea aviso
        if(mode!="Aviso" && mode!="Exito" && mode!="Error")
        {
            mode="Aviso"
        }
        let header=""
        switch(mode)
        {
            case 'Aviso':
            header="<i class='bi bi-exclamation-circle-fill text-primary fs-2'></i><h3 class='modal-title'>" +title+"</h3>"
            break;
            case 'Exito':
            header="<i class='bi bi-cart-check-fill text-success fs-2'></i><h3 class='modal-title'>" +title+"</h3>"
            break;
            case 'Error':
            header="<i class='bi bi-x-circle-fill text-danger fs-3'></i><h3 class='modal-title'>" +title+"</h3>"
            break;
        }
        $("#headerModalWaring").html(header);
        $("#bodyModalWaring").html(message);
        $("#modalWaring").modal("show");
        $("#btnCerrarModalWaring").on("click", function (){
            $("#modalWaring").modal("hide");});
        return false;
    }
})()