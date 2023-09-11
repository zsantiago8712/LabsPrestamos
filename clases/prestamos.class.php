<?
class prestamos {
    var $usuario = Array(); //Sera utilizado para identificar un usuario
    var $prestamos = Array(); // Sera identificado para guardar la información general de los prestamos activos/solicitdados/adeudos no resueltos
    var $Activos = Array(); // Se utilizara para guardar los prestamos activos de un usuario
    var $Solicitados = Array(); // Se utilizara para guardar los prestamos solicitados de un usuario
    var $Adeudos = Array(); // Se utilizara para guardar los adeudos de un usuario;
    var $id_lab;
    var $link;
    var $lasterror;

    function __construct($link)
    {
        $this->link = $link;
        
    }
    function load($id_lab,$id_usuario=0)
    {
        if($id_usuario == 0)
        {
            $query = "SELECT b.nombres, b.ap_paterno, b.ap_materno, b.cuenta, b.id_usuario, date_format(a.fecha_solicitud,'%d-%m-%y') as fecha_solicitud, a.id_status_prestamo,
            a.adeudo_saldado, a.id_status_prestamo, a.id_existencia_lab, c.id_laboratorio 
                FROM inventarios_deii.inventario_prestamos AS a INNER JOIN inventarios_deii.usuarios_prestamos as b on a.id_usuario = b.id_usuario
                INNER JOIN inventarios_deii.inventario_existencia_laboratorio as c on a.id_existencia_lab = c.id_existencia_lab 
                WHERE c.id_laboratorio = ? AND (a.id_status_prestamo = 1 OR a.id_status_prestamo = 2 OR a.id_status_prestamo = 3 OR a.id_status_prestamo = 5)";
            $resultado = $this->link->query($query, array($id_lab));
            if(!$resultado)
            {
                $this->lasterror = $this->link->lasterror;
                return false;
            }
            $resultado = $resultado->fetchAllArrayAsoc();
            $this->prestamos = $resultado;

        }else{
            if(!$this->get_usuario($id_usuario))
            {
                return false;
            }
            if(!$this->get_Solicitados($id_usuario,$id_lab))
            {
                return false;
            }
           
            if(!$this->get_Activos($id_usuario,$id_lab))
            {
                return false;
            }
            if(!$this->get_Adeudos($id_usuario,$id_lab))
            {
                return false;
            }
            
        }
        return true;
    }
    function get_usuario($id_usuario)
    {
        $query = "SELECT id_usuario,cuenta, nombres, ap_paterno, ap_materno FROM inventarios_deii.usuarios_prestamos WHERE id_usuario = ?";
        $resultado = $this->link->query($query, array($id_usuario));
            if(!$resultado)
            {
                $this->lasterror = $this->link->lasterror;
                return false;
            }
            $resultado = $resultado->fetchAllArrayAsoc();
            $this->usuario["usuario"] = $resultado[0];
            
        return true;
    }
    function get_Activos($id_usuario,$id_lab)
    {
        $query = "SELECT a.id_inventario_prestamo, a.id_serie_lote,
                        z.disponible, z.id_existencia_lab, 
                        d.id_inventario, d.id_cuantificacion, a.cantidad_solicitada, 
                        a.cantidad_entregada, a.fecha_recepcion,
                        date_format(a.fecha_entrega_programada,'%d-%m-%y') as fecha_entrega_programada, 
                        a.fecha_solicitud,  
        concat(d.descripcion, ' ',
        IFNULL(
          group_concat(
              case c.tipo
                  when 1 
                      then concat(ci.valor,' ',u.simbolo, ' ', c.simbolo )
                  else
                      concat(c.caracteristica, ' : ' , il.valor)
              end 
          ORDER BY c.caracteristica SEPARATOR ' '),
          '')) as descripcion
            FROM inventarios_deii.inventario_prestamos as a
            INNER JOIN inventarios_deii.inventario_existencia_laboratorio as z on
                         a.id_existencia_lab = z.id_existencia_lab
        INNER JOIN inventarios_deii.inventario_item as d on 
                        z.id_inventario = d.id_inventario
        LEFT JOIN inventarios_deii.inventario_caracteristica_item ci on 
                    ci.id_inventario = d.id_inventario
        LEFT JOIN inventarios_deii.inventario_caracteristica c on c.id_caracteristica = ci.id_caracteristica
        LEFT JOIN inventarios_deii.unidad_prefijos u on 
                    u.id_prefijo = ci.id_prefijo
        LEFT JOIN inventarios_deii.inventario_caracteristica_lista as il on
            il.id_lista 			= ci.id_lista
        and	il.id_caracteristica 	= ci.id_caracteristica
        WHERE a.id_status_prestamo = 2 AND z.id_laboratorio = ? AND a.id_usuario = ?
        GROUP by  a.id_inventario_prestamo,
					z.disponible, 
                    z.id_existencia_lab, 
                    d.id_inventario, 
                    a.cantidad_solicitada,
                    a.cantidad_entregada, 
                    a.fecha_recepcion,
                    a.fecha_solicitud";
        $resultado = $this->link->query($query, array($id_lab,$id_usuario));
            if(!$resultado)
            {
                $this->lasterror = $this->link->lasterror;
                return false;
            }
            $resultado = $resultado->fetchAllArrayAsoc();
            $this->usuario["activos"] = $resultado;
        return true;
    }
    function get_Solicitados($id_usuario,$id_lab)
    { 
        $query = "SELECT a.id_inventario_prestamo,
                        z.disponible, 
                        z.id_existencia_lab,z.prestamos as cant_max,
                         d.id_inventario, 
                         d.id_cuantificacion, 
                         a.cantidad_solicitada, 
                         a.fecha_recepcion,
                         date_format(a.fecha_entrega_programada,'%d-%m-%y') as fecha_entrega_programada, 
                         a.fecha_solicitud, 
        concat(d.descripcion, ' ',
        IFNULL(
          group_concat(
              case c.tipo
                  when 1 
                      then concat(ci.valor,' ',u.simbolo, ' ', c.simbolo )
                  else
                      concat(c.caracteristica, ' : ' , il.valor)
              end 
          ORDER BY c.caracteristica SEPARATOR ' '),
          '')) as descripcion
        FROM inventarios_deii.inventario_prestamos as a
        INNER JOIN inventarios_deii.inventario_existencia_laboratorio as z on a.id_existencia_lab = z.id_existencia_lab
        INNER JOIN inventarios_deii.inventario_item as d on z.id_inventario = d.id_inventario
        LEFT JOIN inventarios_deii.inventario_caracteristica_item ci on ci.id_inventario = d.id_inventario
        LEFT JOIN inventarios_deii.inventario_caracteristica c on c.id_caracteristica = ci.id_caracteristica
        LEFT JOIN inventarios_deii.unidad_prefijos u on u.id_prefijo = ci.id_prefijo
        LEFT JOIN inventarios_deii.inventario_caracteristica_lista as il on
            il.id_lista 			= ci.id_lista
        and	il.id_caracteristica 	= ci.id_caracteristica
        WHERE a.id_status_prestamo = 100 AND z.id_laboratorio = ? AND a.id_usuario = ?
        GROUP by  a.id_inventario_prestamo,
					z.disponible, 
                    z.id_existencia_lab, 
                    d.id_inventario, 
                    a.cantidad_solicitada,
                    a.cantidad_entregada, 
                    a.fecha_recepcion,
                    a.fecha_solicitud";
        $resultado = $this->link->query($query, array($id_lab,$id_usuario));
            if(!$resultado)
            {
                $this->lasterror = $this->link->lasterror;
                return false;
            }
            $resultado = $resultado->fetchAllArrayAsoc();
            $this->usuario["solicitud"] = $resultado;
        return true;
    }
    function get_Adeudos($id_usuario, $id_lab)
    {
        $query = "SELECT a.id_inventario_prestamo,
        z.disponible, 
        z.id_existencia_lab,z.prestamos as cant_max,
         d.id_inventario, 
         a.cantidad_entregada,
         d.id_cuantificacion, 
         a.cantidad_solicitada, 
         g.comentarios,
         g.id_adeudo,
         a.fecha_recepcion,
         date_format(a.fecha_entrega_programada,'%d-%m-%y') as fecha_entrega_programada, 
         a.fecha_solicitud, 
concat(d.descripcion, ' ',
IFNULL(
group_concat(
case c.tipo
  when 1 
      then concat(ci.valor,' ',u.simbolo, ' ', c.simbolo )
  else
      concat(c.caracteristica, ' : ' , il.valor)
end 
ORDER BY c.caracteristica SEPARATOR ' '),
'')) as descripcion
FROM inventarios_deii.inventario_prestamos as a
INNER JOIN inventarios_deii.inventario_existencia_laboratorio as z on a.id_existencia_lab = z.id_existencia_lab
INNER JOIN inventarios_deii.inventario_item as d on z.id_inventario = d.id_inventario
LEFT JOIN inventarios_deii.inventario_caracteristica_item ci on ci.id_inventario = d.id_inventario
LEFT JOIN inventarios_deii.inventario_caracteristica c on c.id_caracteristica = ci.id_caracteristica
LEFT JOIN inventarios_deii.unidad_prefijos u on u.id_prefijo = ci.id_prefijo
LEFT JOIN inventarios_deii.inventario_adeudos g on g.id_usuario = a.id_usuario
LEFT JOIN inventarios_deii.inventario_caracteristica_lista as il on
il.id_lista 			= ci.id_lista
and	il.id_caracteristica 	= ci.id_caracteristica
WHERE a.id_status_prestamo = 6 AND z.id_laboratorio = ? AND a.id_usuario = ? AND g.activo = 1
GROUP by  a.id_inventario_prestamo,
    z.disponible, 
    z.id_existencia_lab, 
    d.id_inventario, 
    a.cantidad_solicitada,
    a.cantidad_entregada, 
    a.fecha_recepcion,
    a.fecha_solicitud";
$resultado = $this->link->query($query, array($id_lab,$id_usuario));
if(!$resultado)
{
$this->lasterror = $this->link->lasterror;
return false;
}
$resultado = $resultado->fetchAllArrayAsoc();
$this->usuario["adeudos"] = $resultado;
return true;
    }
    function get_Alumno($cuenta,$lab)
    {
        $query = "SELECT 	up.nombres, 
		up.ap_paterno, 
        up.ap_materno, 
        up.cuenta, 
        up.id_usuario,
        if(
        sum( case
               ip.id_status_prestamo
			when 6
			 then 1
			else 0
            end
            ) > 0, 1, 0)
            as adeudos,
            if(
		sum( case
             when fecha_entrega_programada < cast(now() as date)
				and ip.id_status_prestamo = 2
			then 1
            else 0
            end) > 0,1,0) as vencidos
               
FROM 	inventarios_deii.inventario_prestamos as ip

        INNER JOIN inventarios_deii.inventario_existencia_laboratorio as el on 
        ip.id_existencia_lab = el.id_existencia_lab
        
        INNER JOIN inventarios_deii.usuarios_prestamos as up on 
        ip.id_usuario = up.id_usuario
        
WHERE 	up.cuenta = ?
	and el.id_laboratorio = ?
    
GROUP BY	up.nombres, 
			up.ap_paterno, 
			up.ap_materno, 
			up.cuenta, 
			up.id_usuario";
        $resultado = $this->link->query($query, array($cuenta, $lab));
        if(!$resultado)
        {
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        if($this->link->numrows == 0)
        {
            return Array();
        }
        $resultado = $resultado->fetchAllArrayAsoc();
        $this->usuario = $resultado[0];
        return  $resultado;
    }
    function check_disponible($id_existencia_lab)
    {
        $query = "SELECT disponible from inventarios_deii.inventario_existencia_laboratorio WHERE id_existencia_lab = ?";
        $resultado = $this->link->query($query, array($id_existencia_lab));
        if(!$resultado)
        {
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        $resultado = $resultado->fetchAllArrayAsoc();
        return $resultado[0]["disponible"];

    }
    function rechazar($existencia)
    {
        $query = "UPDATE inventarios_deii.inventario_prestamos set id_status_prestamo = ? WHERE id_inventario_prestamo = ?";
        $resultado = $this->link->query($query, array(8,$existencia));
        if(!$resultado)
        {
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        return true;
    }
    function check_entregado($entregado)
    {
        $query = "SELECT cantidad_entregada from inventarios_deii.inventario_prestamos WHERE id_inventario_prestamo = ?";
        $resultado = $this->link->query($query, array($entregado));
        if(!$resultado)
        {
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        $resultado = $resultado->fetchAllArrayAsoc();
        return $resultado[0]["cantidad_entregada"];
    }
    function aceptarSolicitud($datos)
    {
        if(!$this->restarDisponibles($datos["id"], $datos["cantidad"]))
        {
            return false;
        }
        $query = "UPDATE inventarios_deii.inventario_prestamos set id_status_prestamo = ?, fecha_recepcion = date_format(NOW(),'%Y-%m-%d'), cantidad_entregada = ? WHERE id_inventario_prestamo = ?";
        $resultado = $this->link->query($query, array(2,$datos["cantidad"],$datos["id_prestamo"]));
        if(!$resultado)
        {
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        return true;

    }
    function entregarSolicitud($datos)
    {
        if(!$this->sumarDisponibles($datos["id"], $datos["cantidad"]))
        {
            return false;
        }
        $query = "UPDATE inventarios_deii.inventario_prestamos set id_status_prestamo = ?,fecha_entrega_real = date_format(NOW(),'%Y-%m-%d') WHERE id_inventario_prestamo = ?";
        $resultado = $this->link->query($query, array(5,$datos["id_prestamo"]));
        if(!$resultado)
        {
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        return true;

    }
    function Bloquear_existencia_lab($id_existencia_lab)
    {
        $query = "SELECT id_inventario, id_laboratorio FROM inventarios_deii.inventario_existencia_laboratorio WHERE id_existencia_lab = ? for update";
        $resultado = $this->link->query($query, array($id_existencia_lab));
        if(!$resultado)
        {
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        return true;
    }
    function restarDisponibles($id_exi, $cantidad)
    {
        $query = "UPDATE inventarios_deii.inventario_existencia_laboratorio set disponible = (disponible - ?) WHERE id_existencia_lab = ?";
        $resultado = $this->link->query($query, array($cantidad,$id_exi));
        if(!$resultado)
        {
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        return true;
    }
    function sumarDisponibles($id_exi, $cantidad)
    {
        $query = "UPDATE inventarios_deii.inventario_existencia_laboratorio set disponible = (disponible + ?) WHERE id_existencia_lab = ?";
        $resultado = $this->link->query($query, array($cantidad,$id_exi));
        if(!$resultado)
        {
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        return true;
    }
}
?>