(function (){
    $("#btnAgregar").click(function (){
        $("#contenidoPrincipal").load("inventario/agregarNuevo.php", function (){
          $.getScript("js/agregarNuevo.js");
        });
      });
      $(".btnEditar").click(function (){
        let id = $(this).attr("id");
        let url = "inventario/agregarNuevo.php?id=" + id;
        $("#contenidoPrincipal").load(url, function (){
          $.getScript("js/agregarNuevo.js");
        });
      });
      
    $("#btnBusqueda").on("click",function(){
      let url = "inventario/mainAdmin.php";
      let banderaDescripcion = 0;
      let lab = "";
      if($("#Busqueda").val() != "")
      {
        let busqueda =  $("#Busqueda").val();
        url = url + "?descripcion=" +busqueda.replaceAll(" ", "_");
        banderaDescripcion = 1;
      }
      $(".btnLab:checked").each(function (){
        lab = lab + $(this).val() + ","
      });
      if(lab != "")
      {
        lab = lab.substring(0,(lab.length - 1));
      }
      if(banderaDescripcion == 1)
      {
        if(lab != ""){
          url = url + "&id=" + lab;
        } 
      }else{
        if(lab != ""){
          url = url + "?id=" + lab;
        } 
      }
      $("#contenidoPrincipal").load(url,function (){
        $.getScript("js/mainAdmin.js", function (){});
    }); 
    });
    

})();
