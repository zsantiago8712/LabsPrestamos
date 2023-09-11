(function (){
   
    $(".btnQuitarAdeudo").on("click", function (){
        let id = $(this).closest("tr").attr("data-id");
        let adeudo = $(this).closest("tr").attr("data-adeudo");
        var fila = $(this).closest("tr")
        console.log(id);
        console.log(adeudo);
        $.ajax({
            url: "solicitudes/quitarAdeudosCNT.php",
            type: "POST",
            dataType: "JSON",
            data: {"id_prestamo": id, "id_adeudo" : adeudo}
        }).done(function (response){
            if(response["success"])
            {
                modalAlert("Se ha quitado el adeudo. ", "Aviso");
                fila.remove();
            }else{
                modalAlert(response["errorMessage"]);
            }
            
        }).fail(function (){
            modalAlert("Error en el servidor");
        })

    })

   
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