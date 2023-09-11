(function (){
    $("#contenidoPrincipal").load("inventario/mainAdmin.php",function (){
        $.getScript("js/mainAdmin.js", function (){});
    }); 
    $('.module_menu').click(function(){
        let ruta = $(this).attr("data-ruta");
        let script = $(this).attr("data-script");

        $.ajax({
            url: ruta,
            dataType: "html"
        })
        .done(function(data){
            $("#contenidoPrincipal").html(data);
            if(script){
                $.getScript(script);
            }
            
        })
        .fail(function(data){
            alert("Error");
        });
    });
    $("#btnCerrarSession").click(function (){
        location.href="index.php?sessionClose=1";
    });

    $("#btnCerrarModalWaring").on("click",function(){
        $("#modalWaring").modal("hide");
    });
})();