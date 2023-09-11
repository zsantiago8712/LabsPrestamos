<?
class Usuario {
	
	var $DB;
	var $lasterror;
	var $organigrama;
	var $id_usuario = 0;
	var $usuario;
	var $nombre_usuario;
	var $correo;
	var $perfiles;
	var $rutas;
	var $is_admin;
	var $cve_empleado;
	var $dominio;
	var $ldap_server;
	var $niveles   = array();

	static function isdomainuser($usuario_valida, $password_valida){

			/*Crea la conexión con el servidor de cuentas de usuario*/
				
				$link_ldap = @ldap_connect("172.16.2.16");

				if(!$link_ldap)
				{
					//No se puede establecer conexión con el servidor LDAP
					return false;
				}

				if(!@ldap_set_option($link_ldap, LDAP_OPT_PROTOCOL_VERSION, 3))
				{
					//No se ha podido establecer el protocolo a LDAP version 3
					return false;
				}

				if(!@ldap_set_option($link_ldap, LDAP_OPT_REFERRALS, 0))
				{
					//No se han podido establecer las referencias de servidor
					return false;
				}

				$user_rdn = $usuario_valida."@alumnos.uia";
				
				/*Se conecta a un directorio LDAP con el usuario especificado y su contraseña. Devuelve TRUE si todo se llevó a cabo correctamente, FALSE en caso de fallo*/
				$conecta = @ldap_bind($link_ldap, $user_rdn, $password_valida);
				@ldap_unbind($link_ldap);
				
				return true;
				if ($conecta){

					return true;

				}

				else{

					return false;

				}

	}

	function __construct($link,$user){

		$this->DB = $link;
		$this->usuario = $user;
		$this->load();
	}

	function load(){

		$sql = "SELECT		u.id_usuario,
							u.usuario,
							u.cve_empleado,
							u.nombre_usuario,
							u.correo,
							u.dominio,
							u.ldap_server

				from 		aplicaciones_cca.usuarios u
				where		u.usuario		= ? and
							u.habilitado	= 1";


		if( !$this->DB->query($sql,array($this->usuario)) || 
			 $this->DB->numrows == 0){
				echo $this->DB->lasterror;
				$this->lasterror = $this->DB->lasterror;
				return 0;

		}

		$result = $this->DB->fetchAllArrayAsoc();

		$this->id_usuario		=	$result[0]['id_usuario'];
		$this->nombre_usuario	=	$result[0]['nombre_usuario'];
		$this->correo			=	$result[0]['correo'];
		$this->dominio			=	$result[0]['dominio'];
		$this->ldap_server		=	$result[0]['ldap_server'];
		$this->cve_empleado		= 	$result[0]['cve_empleado'];
		return 1;
	}

}

?>