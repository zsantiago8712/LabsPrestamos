/*
* Creado por: Derek Escamilla.
* fecha: 3/27/2023
* Ubicación: Universidad Iberoamericana
* Este programa se encarga de manejar las acciones del jstree y generar eventos de estos. El propgrama controla el agregar, editar y borrar. 
*Cuando se genera aprieta el boton guardar, este programa manda un ajax a clasificacionesCNT.php con dos arreglos: agregar y editar.
*
*/
(function (){
    //Se declaran los arreglos que se mandaran al ajax.
    var editar = [];
    var agregar = [];
    //Generamos el jsTree usando como datos 'dtsTree' que es una variable declarada en el clasifiacion.php
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
      
// Este evento se activa cuando se cambia el nombre a un nodo. Lo usamos para el boton editar y agregar.
  $('#jTree').on('rename_node.jstree', function (e, data) {
    generarArbol(data, "agregar");
    console.log(data);
  }).jstree();
  //Este evento se activa cuando se borra un nodo.
  $('#jTree').on('delete_node.jstree', function (e, data) {
    console.log(data);
    let id =  data.node.id;
    if(id.search("j") >= 0)
    {
        borrarNodo(data);
        console.log("Se borro: " + id);
        console.log(id.search("j") );
    }else {
        generarArbol(data, "borrar");
    }
    console.log(id.search("j"));
  }).jstree();
  //Este evento se activa 
  $('#jTree').on('move_node.jstree', function (e, data) {
    generarArbol(data, "mover");
  }).jstree();
    $("#Agregar").click(function (){ 
        var ref = $('#jTree').jstree(true),
            sel = ref.get_selected();
            sel = sel[0];
            sel = ref.create_node(sel, {"type":"file"},"last", function () {});
            if(sel) {
                ref.edit(sel);
            }
    });
    $("#Borrar").click(function (){
        let ref = $('#jTree').jstree(true),
        sel = ref.get_selected();
        if(ref.get_node(sel).children.length > 0) {return; };
        if(!sel.length){return false}
        ref.delete_node(sel);
    })
    $("#Editar").click(function (){
        let ref = $('#jTree').jstree(true),
		sel = ref.get_selected();
	    if(!sel.length) { return false; }
		sel = sel[0];
		ref.edit(sel);
    })
    $("#Guardar").click(function (){
        $.ajax({ 
            url: "clasificacion/clasificacionCNT.php",
            dataType:"JSON",
            type: "POST",
            data: {"nuevos" : agregar, "bd" : editar}
        }).done(function(response){
            if(response["success"])
            {
                alert("Se han guardado los cambios.");
                location.href="main.php";
            }else {
                alert(response["errorMessage"]);
                location.href="main.php"
            }

        }).fail(function(jqXHR, textStatus, errorThrown ){
            alert("Error al guardar el arbol. Error: " + errorThrown);
        });
    });
    function generarArbol(data, accion) {
        if(parseInt(data.node.id))
    {
        let bandera = 0;
        if(editar.length != 0)
        {
            let id = data.node.id;
            editar.forEach(function(item){
                console.log(item[0].id);
                if(item[0].id == id)
                {
                    if(accion == "agregar")
                    {
                        item[0].text = data.node.text;
                        item[0].old = data.old;

                    }else if(accion == "mover"){
                        item[0].parent = data.node.parent;
                        item[0].old_parent = data.old_parent;
                    }else {
                        item[0].borrar = 1;
                    }
                    console.log(editar);
                    bandera = 1;
                }
            });
        }
        if(bandera == 1){return;}
        let datos = []
        if(accion == "agregar")
        {
             datos = [{"id": data.node.id, "parent" : data.node.parent, "old_parent": "0", "text": data.node.text, "old" : data.old ,"borrar" : 0}];

        }else if(accion == "mover"){
             datos = [{"id": data.node.id, "parent" : data.node.parent, "old_parent": data.old_parent, "text": data.node.text, "old" : "0" ,"borrar" : 0}];
        }else {
             datos = [{"id": data.node.id, "parent" : data.node.parent, "old_parent": "0", "text": data.node.text, "old" : "0" ,"borrar" : 1}];
        }
        editar.push(datos);
        console.log(editar);

    }else{
        let bandera = 0;
        if(agregar.length != 0)
        {
            let id = data.node.id;
            agregar.forEach(function(item){
                console.log(item[0].id);
                if(item[0].id == id)
                {
                    if(accion == "agregar")
                    {
                        item[0].text = data.node.text;

                    }else if(accion == "mover"){
                        item[0].parent = data.node.parent;
                        item[0].old_parent = data.old_parent;
                    }else {
                        item[0].borrar = 1;
                    }
                    console.log(agregar);
                    bandera = 1;
                }
            });
            
            
        }
        if(bandera == 1){return;}
        let datos = []
        if(accion == "agregar")
        {
             datos = [{"id": data.node.id, "parent" : data.node.parent, "old_parent": "0", "text": data.node.text,"borrar" : 0}];

        }else if(accion == "mover"){
             datos = [{"id": data.node.id, "parent" : data.node.parent, "old_parent": data.old_parent, "text": data.node.text,"borrar" : 0}];
        }else {
             datos = [{"id": data.node.id, "parent" : data.node.parent, "old_parent": "0", "text": data.node.text,"borrar" : 1}];
        }
        agregar.push(datos);
        console.log(agregar); 
    }
    }
    function  borrarNodo(data) 
    {
        let contador = 0;
        let posicion;
        agregar.forEach(function(item)
        {
            if(data.node.id == item[0].id )
            {
               posicion = contador; 
            }
            contador++;
        })
        agregar.splice(posicion, 1);
        console.log(agregar)
    }
})();