(function (){
  var num = 1;
  var General = new Object();
  var CaracteristicasBorradas = Array();
  var modelo = Array();
  General["clasificacion"] = "0";
  modelo.push('SIN MODELO');
  var Modelos = null;
  $("#Modelo").select2({
    tags: true,
    selectionCssClass: "mayus"
  });
  $("#marca").select2({
    tags: true,
    selectionCssClass: "mayus"
  });
  $("#marca").on("change", function() {
    console.log($(this).val().toUpperCase());
    let marca = $(this).val().toUpperCase();
    $.ajax({
      url: "inventario/marcaCNT.php",
      type: "POST",
      data: {"name" : marca},
      dataType:"JSON"
    }).done(function (response){
      if(response["success"])
      { 
        console.log(response["resultados"]);
        if(response["resultados"].id == 0)
        {
          General["marca"] = marca;
          response["resultados"].modelos.forEach(function (item){
            let option = $("<option value='"+ item.id_modelo +"'>"+ item.modelo +"</option>");
            $("#Modelo").append(option);
          });
          return;
        }
        General["marca"] = response["resultados"].id;
        console.log(General["marca"]);
        if(response["resultados"].modelos == 0)
        {
          $("#Modelo").html("");
          return;
        }
        $("#Modelo").html("");
        response["resultados"].modelos.forEach(function (item){
          let option = $("<option value='"+ item.id_modelo +"'>"+ item.modelo +"</option>");
          $("#Modelo").append(option);
        });
      }else{
        alert(response["errorMessage"]);
      }

    }).fail(function(){
      alert("Error");

    });
 });
 
 $( "#Modelo" ).on("change",function(){
  General["modelo"] = $("#Modelo").val().toUpperCase();
  if(Modelos == null)
  {
    if(General["modelo"] == "SIN MODELO")
    {
      General["modelo"] = "1";
    }
    return;
  }
  Modelos.forEach(function (item){
    if(General["modelo"] == item.modelo)
    {
      General["modelo"] = item.id_modelo;
      console.log(General["modelo"]);
    }
    if(General["modelo"] == "SIN MODELO")
    {
      General["modelo"] = "1";
    }
  });
 });
    
    $("#CaracteristicaPrincipal").css("display", "none");
    $("#EstatusPrincipal").css("display","none");
    $("#Prefijos").css("display", "none");
    //Esta variable serieLote es la que generara el formulario para los laboratorios en base a si es serie o lote
      $( "#tabs" ).tabs();
      $('#jTree').jstree({
        'core': {
            'data': dtsTree,
            'check_callback': true,
            'types': {
                '#': {'max_children': 20, 'max_depth': 20, 'valid_children': ['root']},
                'root': {'icon': '/static/3.3.15/assets/images/tree_icon.png', 'valid_children': ['default']},
                'file': {'icon': 'glyphicon glyphicon-file', 'valid_children': []}
            },
        },
        //Este atributo permite que se puedan generar movimeintos en el arbol. (Arratrar)
        'plugins': ['dnd'],
    });
    $('#jTree').on('changed.jstree', function (e, data) {
    General["clasificacion"] = data.node.id;
    $("#btnJstree").attr("data-id", data.node.id);
    let name = ""
    data.node.parents.forEach(element => {
      if(element != "1" && element != "#")
      {
        name += $("a[id="+element +"_anchor]").text() + ".";
      }
    });
    $("#btnJstree").text(name + data.node.text)
  }).jstree();
      function crearSerie(dato)
      {
        let count = +($(dato).parent().children("input").val()) + 1;
        let fila = $("<tr class='filaSerie'></tr>");
        fila.append($("<td><button style='height: 10px; width: 10px' class='btn btn-danger btnBorrarCaracteristica'>X</button></td>"))
        fila.append($("<td><span>"+ count +"</span></td>"));
        fila.append($("<td><input name='serie[]' data-id='0' type = 'text' class='form-control' style='width: 10rem'></td>"));
        fila.append($("<td><input name='uia[]' type = 'text' class='form-control' style='width: 10rem'></td>"));
        fila.append($("<td><input name='qr[]' type = 'text' class='form-control' style='width: 10rem'></td>"));
        let select = $("#EstatusPrincipal").clone().css("display","table-cell").attr("id","");
        fila.append($("<td></td>").append(select));
        return fila;
      }
      function crearLote(dato)
      {
        let count = +($(dato).parent().children("input").val()) + 1;
        let fila = $("<tr class='filaSerie'></tr>");
        fila.append($("<td><button style='height: 10px; width: 10px' class='btn btn-danger btnBorrarCaracteristica'>X</button></td>"))
        fila.append($("<td><span>"+ count +"</span></td>"));
        fila.append($("<td><input name='serie[]' data-id='0' type = 'text' class='form-control' style='width: 10rem'></td>"));
        fila.append($("<td><input name='qr[]' type = 'text' class='form-control' style='width: 10rem'></td>"));
        fila.append($("<td></td>").append($("<input size='16' name='fecha[]' type='date' class='form-control' id='datetime' required>")));
        fila.append("<td><input name='cantidadLote[]' type = 'text' class='form-control' style='width: 10rem'></td>");
        return fila;
      }
      $("#cuanti input").click(function (){
        //Aqui se establece el valor de General["cuantificacion"] para poder diferenciar entre las opciones.
        General["cuantificacion"] = $(this).attr("data-value");
        if($(this).attr("id") == "NÚMERO SERIE")
        {
          $(".none").css("display", "block");
          $(".label").text($(this).attr("id"));
          $(".tableSerie").children("tr").remove();
          $(".tableSerie").prepend($("<tr></tr>").append("<th>Borrar</th><th>#</th><th class='tabDosLabel'>Serie</th><th>Num.UIA</th><th>QR</th><th>Estatus</th>"));
          $(".tabDosLabel").text($(this).attr("id"));
          $(".laboratoriosFormularioCantidad").css("display", "none");
          $(".laboratoriosFormularioSerie").attr("data-name","formularioLaboratorios");
          $(".laboratoriosFormularioCantidad").attr("data-name","formularioLaboratoriosCancel");
          $(".contadorSerie").val("0");
        }
        else if($(this).attr("id") == "LOTE/CADUCIDAD")
        {
          $(".none").css("display", "block");
          $(".label").text($(this).attr("id"));
          $(".tableSerie").children("tr").remove();
          $(".tableSerie").prepend($("<tr></tr>").append("<th>Borrar</th><th>#</th><th class='tabDosLabel'>Serie</th><th>QR</th><th>Fecha de Caducidad:</th><th>Cantidad</th>"));
          $(".tabDosLabel").text($(this).attr("id"));
          $(".laboratoriosFormularioCantidad").css("display", "none");
          $(".laboratoriosFormularioSerie").attr("data-name","formularioLaboratorios");
          $(".laboratoriosFormularioCantidad").attr("data-name","formularioLaboratoriosCancel");
          $(".contadorSerie").val("0");
          
        }else {
          $(".none").css("display", "none");
          $(".Cant").css("display", "block");
          $(".laboratoriosFormularioCantidad").attr("data-name","formularioLaboratorios");
          $(".laboratoriosFormularioCantidad").css("display", "block");
          $(".laboratoriosFormularioSerie").attr("data-name","formularioLaboratoriosCancel");
        }
      });
      $(".btnMas").click(function (){
        let datos = $(this);
        let fila = "";
        let count = +($(this).parent().children("input").val()) + 1;
        if(General["cuantificacion"] == "2")
        {
          fila = crearSerie(datos);
        }else {
          fila = crearLote(datos);
        }
        $(this).parent().children("input").val(count);
        $(this).parent().children(".laboratoriosFormularioSerie").children("table").append(fila);
        $(".btnBorrarCaracteristica").click(function ()
        {
          $(this).closest("tr").remove();
        });

      });
      $(".btnBorrarCaracteristicaActual").click(function (){
        CaracteristicasBorradas.push($(this).parent().siblings(1).children(0).attr("data-id"));
        $(this).closest("tr").remove();

      });
      $("#btnMasCar").click(function (){
        let fila = $("<tr class='my-1 caracteristicaGeneral'></tr>");
        fila.append($("<td><button style='height: 10px; width: 10px' class='btn btn-danger btnBorrarCaracteristica'>X</button></td>"))
        fila.append($("<td>"+ num +"</td>"));
        let select =  $("#CaracteristicaPrincipal").clone().css("display","table-cell").attr("id","");
        $(select).on("change",function () {
          let fila = $(this).closest("tr");
          console.log(fila.children().length);
          if(fila.children().length > 3)
          {
            fila.children().last().remove();
            fila.children().last().remove();
          }
          console.log(fila.children());
          let tipo = "0";
          let lista = null;
          $.ajax({
            url: "inventario/getLista.php",
            data: {"id" : $(this).val()},
            dataType: "JSON",
            type: "POST"
          }).done(function(response)
          {
            if(response["success"])
            {
               tipo = response["tipo"];
               console.log("Tipo: " + tipo)
              if(tipo == "2")
              {
                lista = response["lista"] 
              }
              console.log(lista)
              if($(this).closest("tr").children().length > 3)
              {
                if($(this).val() == "2"){return;}
              }
              let formulario = $("<td style='40%'></td>");
              if(tipo != "2")
              {
                console.log(tipo);
                let prefijos = $("#Prefijos").clone().css("display", "table-cell").attr("id", "");
                fila.append($("<td></td>").append(prefijos));
                formulario.append($("<input name='valor[]'' type='text' class ='form-control'>"));
              
              }else {
                fila.append($("<td>Sin prefijo</td>"));
                let opc = $("<select name='valor[]' class='form-select'></select>");
                lista.forEach(function (item)
                {
                  let opcion = $("<option value='"+item.id +"'>"+ item.valor +"</option>");
                  opc.append(opcion);
                });
                formulario.append(opc);
              }
              fila.append(formulario);
              num++;
            }else {
              alert("Error: Error Error");
            }
          }).fail(function ()
          {
            alert("Error");
          })
         
      });
      fila.append($("<td></td>").append(select));
        $("#tabs-2 table").append(fila);
        
      $(".btnBorrarCaracteristica").click(function ()
        {
          $(this).closest("tr").remove();
        });
      });
      
      $("#btnRegresar").click(function () {
        location.href="main.php";
      });
      $("#Gunidad").on("change",function (){
        let option = $("#Gunidad option[value='" + $(this).val() +"']").text()
        $(".EtiquetaUnidad").text(option);
      });
      $('#btnGuardar').off("click");
      $("#btnGuardar").click(function () {
        //Guardamos los datos del formulario General
        var Caracteristicas = [];
        var Laboratorios = new Object();
        var LaboratorioGeneral = new Object();
        var controlador = 0;
        var id = $("#General").attr("data-tipo")
        General["descripcion"] = $("#General input[name='descripcion']").val().toUpperCase();
        General["consumo"] = $("#General select[name='consumo']").val();
        General["modelo"] = $("#Modelo").val().toUpperCase();
        General["marca"] = $("#marca").val().toUpperCase();
        if($("#marca").val() == $("#select2-marca-container").text())
        {
          General["marca"] = "0";
          General["descripMarca"] = $("#marca").val().toUpperCase();
        }
        if($("#Modelo").val() == $("#select2-Modelo-container").text())
        {
          General["modelo"] = "0";
          General["descripModelo"] = $("#Modelo").val().toUpperCase();
        }
        General["numParte"] = $("#numParte").val().toUpperCase();
        General["clasificacion"] = '0';
        General["unidad"] = $("#Gunidad").val();
        General["prefijo"] = $("#unidadPrefijo").val();
        if(General["numParte"] == "")
        {
          General["numParte"] = null;
        }
        if($("#btnJstree").attr("data-id") != "0")
        {
          General["clasificacion"] = $("#btnJstree").attr("data-id");
        }
        if(General["unidad"] == "0" || General["cuantificacion"] == undefined ||  General["descripcion"] == "" || General["consumo"] == '0' || General["clasificacion"] == "0" )
        {
          alert("Complete todos los campos del formulario General.");
          return;
        }
        if(General["clasificacion"] == "1")
        {
          alert("No se puede elegir esta Clasificación.");
          return;
        }
        console.log(General);
        //Caracteristicas
        $("#tabs-2 .caracteristicaGeneral").each(function (){
          let caracter = $(this).find("select[name^=caracteristica]").val();
          let idCara = $(this).find("select[name^=caracteristica]").attr("data-id");
          let prefijo = "";
          let tipo = "";
          let valor = ""
          if($(this).find("select[name^=prefijo]").val() == undefined) 
          {
            prefijo = "0";
            tipo = "2";
            valor = $(this).find("select[name^=valor").val();
          }else {
            prefijo = $(this).find("select[name^=prefijo]").val();
            tipo = "1"
            valor = $(this).find("input[name^=valor").val();
          }
          if(valor == "" || caracter == "0")
          {
            alert("Llene todos los campos de Característica.");
            controlador = 1;
            return;
          }
          Caracteristicas.push({"caracteristica" : caracter,"tipo" : tipo, "prefijo" : prefijo, "valor" : valor, "id" : idCara});
          
        });
        console.log(Caracteristicas);
        //Laboratorios. 
        let lugarLaboratorio = 0;
        $("span[data-name='formularioLaboratorios']").each(function () 
          {
            let laboratorio = window.document.laboratorios[lugarLaboratorio];
            lugarLaboratorio++;
            if($(this).children(0).attr("id") == "tableCantidad")
            {
              let cantidad = $(this).first().find("input[name^='cantidad']").val();
              if(cantidad == "")
              {
                cantidad = null;
              }
              let prestamos = $(this).parent().find("input[name^='prestamo']").val();
              let dias = $(this).parent().find("input[name^='dias']").val();
              let renovar = $(this).parent().find("select[name^='renovar']").val();
              let idNuevo = $(this).parent().find("input[name^='prestamo']").attr("data-id");
              let visibilidad = 0;
              if(  $(this).parent().parent().find(".serieVisibilidad").is(':checked'))
              {
                visibilidad = 1;
              }
              
              if(idNuevo == "")
              {
                idNuevo = 0;
              }
              if(cantidad != null)
              {
                if(+dias > 30 || +dias < 0)
            {
              alert("Solo se pueden prestar mínimo 0 días y máximo 30 días.");
              controlador = 1;
              return;
            }
                if(prestamos == "")
                {
                  prestamos = 1;
                }
                if(dias == "")
                {
                  dias = 30
                }
                LaboratorioGeneral[laboratorio] = {"prestamos" : prestamos, "dias" :dias, "renovar":renovar,"id" : idNuevo, "visibilidad" : visibilidad};
              }
              if(cantidad != null)
              {
                Laboratorios[laboratorio] = [{"cantidad" : +cantidad}];
              }
            }else if(General["cuantificacion"] == "3") 
            {
              let valoresSerie = [];
              let numserie = 0;
              $(this).find(".filaSerie").each(function (){
                numserie++;
                let serie = $(this).find("input[name^=serie]").val().toUpperCase();
                let idSerie = $(this).find("input[name^=serie]").attr("data-id");
                let qr = $(this).find("input[name^=qr]").val().toUpperCase();
                if(serie == "")
                {
                  alert("Por favor complete el campo Lote/Caducidad.");
                  controlador = 1;
                  return;
                }
                let fecha = $(this).find("input[name^=fecha").val();
                let cantidad = $(this).find("input[name^=cantidadLote").val();
                valoresSerie.push({"Lote" : serie, "fecha": fecha, "cantidad": cantidad, "id" : idSerie , "qr" : qr});
              });
              let prestamos = $(this).parent().parent().find("input[name^='prestamo']").val();
              let dias = $(this).parent().parent().find("input[name^='dias']").val();
              let renovar = $(this).parent().parent().find("select[name^='renovar']").val();
              let idNuevo = $(this).parent().parent().find("input[name^='prestamo']").attr("data-id");
              let visibilidad = 0;
              if( $(this).parent().parent().find(".serieVisibilidad").is(':checked'))
              {
                visibilidad = 1;
              }
              if(idNuevo == "")
              {
                idNuevo = 0;
              }
              if(numserie){ 
              if(prestamos == "")
              {
                prestamos = 1;
              }
              if(dias == "")
              {
                dias = 30
              }
              if(+dias > 30 || +dias < 0)
            {
              alert("Solo se pueden prestar mínimo 0 días y máximo 30 días.");
              controlador = 1;
              return;
            }
          }
              LaboratorioGeneral[laboratorio] = {"prestamos" : prestamos, "dias" :dias, "renovar":renovar, "id" : idNuevo, "visibilidad" : visibilidad};
             Laboratorios[laboratorio] = valoresSerie; 

            }else{ 
              let valoresSerie = [];
              let numserie = 0;
              $(this).children(0)
              $(this).find(".filaSerie").each(function (){
                numserie++;
                let serie = $(this).find("input[name^=serie").val().toUpperCase();
                let uia = $(this).find("input[name^=uia]").val();
                let idSerie = $(this).find("input[name^=serie]").attr("data-id");
                let qr = $(this).find("input[name^=qr]").val().toUpperCase();
                if(serie == "")
                {
                  alert("Por favor complete el campo Número de serie.");
                  controlador = 1;
                  return;
                }
                let estatus = $(this).find("select[name^=estado").val();
                valoresSerie.push({"serie" : serie, "estado": estatus, "uia" : uia, "id" : idSerie, "qr" : qr});
              });
              let prestamos = $(this).parent().parent().find("input[name^='prestamo']").val();
              let dias = $(this).parent().parent().find("input[name^='dias']").val();
              let renovar = $(this).parent().parent().find("select[name^='renovar']").val();
              let idNuevo = $(this).parent().parent().find("input[name^='prestamo']").attr("data-id");
              let visibilidad = 0;
              if( $(this).parent().parent().find(".serieVisibilidad").is(':checked'))
              {
                visibilidad = 1;
              }
              if(numserie){ 
                if(prestamos == "")
                {
                  prestamos = 1;
                }
                if(dias == "")
                {
                  dias = 30
                }
                if(+dias > 30 || +dias < 0)
              {
                alert("Solo se pueden prestar mínimo 0 días y máximo 30 días.");
                controlador = 1;
                return;
              }
            }
              if(idNuevo == "")
              {
                idNuevo = 0;
              }
              LaboratorioGeneral[laboratorio] = {"prestamos" : prestamos, "dias" :dias, "renovar":renovar,"id" : idNuevo,"visibilidad" : visibilidad};
             Laboratorios[laboratorio] = valoresSerie; 
            }
            
          }
          
        );
       if(controlador)// Se activa si al revisar los laboratorios encuentra un error.
       {
        return;
       }
        console.log(Laboratorios);

        console.log(LaboratorioGeneral);
        $('#btnGuardar').prop("disabled",true);
        $.ajax({
          url: "inventario/agregarItemCNT.php",
            dataType:"JSON",
            type: "POST",
            data: {"general" : General, "caracteristicas" : Caracteristicas, "laboratorios" : Laboratorios, "id" : id, "caraBorrar" : CaracteristicasBorradas,"generalLab": LaboratorioGeneral}
        }).done(function (response){
          if(response["success"])
          {
            alert("Se han guardado los cambios.")
            location.href= "main.php";
          }else{
            alert(response["errorMessage"]);
            $('#btnGuardar').prop("disabled",false);
          }
          
        }).fail(function (jqXHR, textStatus, errorThrown){
          alert("Ha ocurrido un error. Error: " + errorThrown);
          $('#btnGuardar').prop("disabled",false);
        });
      });
      $("input:radio[name='cuantificacion']:checked").trigger("click");
  })()