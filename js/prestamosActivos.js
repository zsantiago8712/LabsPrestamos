(function (){
    $(".btnEntregar").on("click", entregar);
	//Abre  modal:
    $(".btnAdeudo").on("click", function(){
        let id_prestamo = $(this).closest("tr").attr("data-id");
        let entregados = $(this).closest("tr").find(".cantidadPrestada");
        $("#Adeudo").attr("data-id", id_prestamo);
        $("#Adeudo").attr("data-cant", entregados);
    });
	//Botón del modal para generar adeudo:
    $("#Adeudo").on("click",generarAdeudo);
    function generarAdeudo()
    {
        let id_prestamo = $(this).attr("data-id");
        if(id_prestamo == "0" )
        {
            modalAlert("Adeudo inválido.");
            return;
        }
        let tipoAdeudo = $("#tipoAdeudo").val();
        let comentarios = $("#comentarioAdeudo").val();
        let id_usuario = $(this).attr("data-usuario");
        let fila = $("#ContenidoPrestamos tr[data-id='" + id_prestamo +"'");
        $.ajax({
            url: "solicitudes/adeudosCNT.php",
            type: "POST",
            dataType: "JSON",
            data: {"tipo": tipoAdeudo, "comentarios" : comentarios, "id_prestamo": id_prestamo, "id_usuario" : id_usuario}
        }).done(function (response){
            if(response["success"])
            {
                modalAlert("Adeudo generado para: " + fila.children().first().text(), "Aviso");
                fila.remove();
                
            }else{
                modalAlert(response["errorMessage"]);
            }
            $("#modalAdeudo").modal("hide");
        }).fail(function (){
            modalAlert("Error en el servidor");
            $("#modalAdeudo").modal("hide");
        })

    }
   
    function entregar()
    {
        var parent = $(this).closest("tr");
        let prestada =  parent.find(".cantidadPrestada").text();
        let entregada =   parent.find(".cantidadSolicitud").val();
        let danos =  parent.find(".danados").val();
        var descripcion =   parent.children().first().text();
        let serie = $(this).attr("data-serie");
        if((+entregada) < prestada)
        {
            modalAlert("No se permiten entregas parciales de material.");
            return;
        }
        if((+danos > prestada))
        {
            modalAlert("Cantidad inválida en campo Dañados: " + descripcion);
            return;
        }
        if((+danos < 0))
        {
            modalAlert("Cantidad inválida en campo Dañados: " + descripcion);
            return;
        }
        let id =  parent.attr("data-id");
        window.document.activos["prestamo_" + id].cantidad = (+entregada);
        window.document.activos["prestamo_" + id].danados = (+danos);
        if(serie != "0")
        {
            window.document.activos["prestamo_" + id].serie = serie;
        }
        console.log(window.document.activos);
        $.ajax({
            url: "solicitudes/activosCNT.php",
            type: "POST",
            dataType: "JSON",
            data: {"activos" : window.document.activos["prestamo_" + id]}
        }).done(function (response){
            if(response["success"])
            {
                modalAlert("Entrega de material exitosa:  " + descripcion, "Aviso");
             parent.remove();
            }else{
                modalAlert(response["errorMessage"]);
            }
        })
    }
    
    function modalAlert(message, mode="Error", title = ""){

        if(title == ""){
            title = mode;
        }
        //Si es un modo no existente, que sea aviso
        if(mode!="Aviso" && mode!="Exito" && mode!="Error")
        {
            mode="Aviso";
        }
        let header="";
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