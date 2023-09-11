(function (){

    $("#selectTipo").change(function (){
        $("#nuOpcDiv").css("display", "none");
       opcionesLista(0);

    });
    function opcionesLista(lista){
        $("#tablaOp").remove();
        let i = 0;
        console.log(lista);
        if(lista.length > 0){
            $("#nuOpcDiv").css("display", "contents");
            $("#liz").append(   
                '<table id = "tablaOp" class="table table-bordered">'+
                    '<tr class="table w-5 table-dark">'+
                        '<th>Opción</th>'+
                    '</tr>')
        
            while(i < lista.length){
                $("#tablaOp").append(
                    '<tr class="table-dark">'+
                        '<td>'+
                            '<div class="form-group">'+
                                '<input class="form-control opciones" value = "'+ lista[i].valor +'" data-id = "'+lista[i].id_lista+'" data-original = "'+ lista[i].valor +'">'+
                            '</div>'+
                        '</td>'+
                    '</tr>' 
                )
                i++;
            }
            return;
        }

        if($("#selectTipo").val() == 2){
            $("#nuOpcDiv").css("display", "contents");
    
            $("#liz").append(   
                '<table id = "tablaOp" class="table table-bordered">'+
                    '<tr class="table w-5 table-dark">'+
                        '<th>Opción</th>'+
                    '</tr>'+
                    '<tr class="table-dark">'+
                        '<td>'+
                            '<div class="form-group">'+
                                '<input class="form-control opciones">'+
                            '</div>'+
                        '</td>'+
                    '</tr>'      
            );
           }else if($("#selectTipo").val() != 1){
            $("#tablaOp").remove();
            $("#nuOpcDiv").css("display", "none");
            $("#nuOpc").remove();
           }
    };
    $("#nuOpc").click(function(){

        $("#tablaOp").append(
            '<tr class="table-dark">'+
                '<td>'+
                '<div class="form-group">'+
                '<input class="form-control opciones" id = "0" data-id = "0">'+
                '</div>'+
              '</td>'+
            '</tr>'
            );
    })

    $("#btnAgregar").click(function(){

        var id_car_ne = $("input[name = 'inId_caracteristica']").val();
        var car = $("input[name = 'inCar']").val();
        var uni = $("input[name = 'inUni']").val();
        var abr = $("input[name = 'inAbr']").val();
        var tipo = $("select[name ='selTipo']").val();
        var opcionesArr = [];

        if(id_car_ne == ""){
            id_car_ne = 0;
        }
 
        $(".opciones").each(function() {

        var opc = {
            "id": $(this).attr("data-id"),
            "valor": this.value
        };
            if(opc["valor"] == ""){
                alert("Por favor ingrese opciones válidas");
                return;
            }
            
            opcionesArr.push(opc);
        });
      
        if(car == ""){
            alert("ingrese una caracteristica válida");
            return;
        }
        if(tipo != 1 && tipo != 2){
            alert("ingrese un tipo válido");
            return;
        }

       $.ajax({ 
            url: "caracteristicas/caracteristicas_CNT.php",
            dataType:"JSON",
            type: "POST",
            data: {"id_caracteristica": id_car_ne, 
                    "caracteristica" : car, 
                    "unidad" : uni, 
                    "abreviatura" : abr, 
                    "tipo" : tipo, 
                    "valores" : opcionesArr}
        }).done(function(response){
            if(response["success"])
            {
                alert("Se agrego una caracteristica");
                location.href="main.php";
                
            }else {
                alert("error\n" + response["errorMessage"]);
            }

        }).fail(function(jqXHR, textStatus, errorThrown ){

            alert("Error al cargar los datos" + errorThrown);
            console.log(errorThrown);
            
        });
    })

    $('.edit-btn').click(function() {

        $("#btnCancel").css("display", "contents");

        $("#btnCancel").on('click', function(event) {
            location.href="main.php";
        });
        $('#selectTipo').prop('disabled', 'disabled');
        id_car = $(this).attr("id");
        var caracteristicaMod;
        $("#tablaOp").remove();

        $.ajax({ 
            url: "caracteristicas/caracteristicas_CNT.php",
            dataType:"JSON",
            type: "POST",
            data: {"id_caracteristica" : id_car}
        }).done(function(response){
            if(response["success"])
            {
                caracteristicaMod = response["carac_mod"];
                $("input[name = 'inId_caracteristica']").val(caracteristicaMod.id_caracteristica);
                $("input[name = 'inCar']").val(caracteristicaMod.caracteristica);
                $("input[name = 'inUni']").val(caracteristicaMod.unidad);
                $("input[name = 'inAbr']").val(caracteristicaMod.simbolo);
                $("select[name ='selTipo']").val(caracteristicaMod.tipo);
                if(caracteristicaMod.tipo == 2) opcionesLista(caracteristicaMod[0]);
            }else {
                alert(response["errorMessage"]);
            }

        }).fail(function(jqXHR, textStatus, errorThrown ){

            alert("Error al cargar los datos" + errorThrown);
            console.log(errorThrown);
            
        });
        $("#btnAgregar").html("Actualizar Caracteristica");
    })
})();