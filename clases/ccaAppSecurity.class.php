<?

/**
 * Esta clase debe ser llamada después de haber
 * cargado los includes de conexión a BD
 *
 *
*/
class ccaAppSecurity {

	var $debug_mode = false;
	var $debug_msg = "";
	var $DB;
	var $lasterror;
	var $id_app;
	var $llave_sesion_valida;
	/**
	 *@param catalog
	 *La base de datos donde se encuentran las
	 *tablas de módulo de seguridad
	*/
	var $catalog;
	

	function __construct($link, $id_app, $catalog = "", $debug_mode = false){

		$this->DB			= 	$link;
		$this->id_app		= 	$id_app;
		$this->catalog		= 	$catalog;	
		$this->debug_mode	=	$debug_mode;

		if($debug_mode)
		{
			echo "Objeto seguridad creado!";
		}
		
	}

	function isdomainuser($usuario_valida, $password_valida, $domain = "alumnos.uia", $ldap_server = "172.16.2.16"){

		
		$link_ldap = @ldap_connect($ldap_server);

		if(!$link_ldap)
		{
			//No se puede establecer conexión con el servidor LDAP
			$this->lasterror = "Error de comunicación con servidor de autenticación";
			if($this->debug_mode)
			{
				echo $lasterror;
			}
			return false;
		}

		if(!@ldap_set_option($link_ldap, LDAP_OPT_PROTOCOL_VERSION, 3))
		{
			//No se ha podido establecer el protocolo a LDAP version 3
			$this->lasterror = "Error al establecer la versión del protocolo de autenticación";
			if($this->debug_mode)
			{
				echo $lasterror;
			}
			return false;
		}

		if(!@ldap_set_option($link_ldap, LDAP_OPT_REFERRALS, 0))
		{
			//No se han podido establecer las referencias de servidor
			$this->lasterror = "Error al establecer la configuración con el servidor de autenticación";
			if($this->debug_mode)
			{
				echo $lasterror;
			}
			return false;
		}

		$user_rdn = $usuario_valida."@" . $domain ;
		
		
		$conecta = @ldap_bind($link_ldap, $user_rdn, $password_valida);

		@ldap_unbind($link_ldap);
		
		if($this->debug_mode)
		{
				echo ( $conecta ? "Usuario de dominio" : "Usuario inválido" );
		}
		return $conecta;

	}

	/**
	 * Determina si el usuario cuenta con permiso de uso
	 * de algún módulo de la aplicación.
	 * @param $direcc_ip
	 * 			Si el registo del usuario en BD tiene una ip != 0
	 *			el parámetro $direcc_ip deberá conicidir con este
	 *			para que se considere usuario válido
	 */
	function isValidAppUser($user){

		$sql_chk_user = "	SELECT 	1
							FROM 	$this->catalog.usuario_perfil up
									
									INNER JOIN $this->catalog.perfiles p ON
									p.id_perfil		=	up.id_perfil
								AND	p.id_aplicativo = 	?

									INNER JOIN $this->catalog.usuarios u ON
									up.id_usuario	= 	u.id_usuario
							WHERE	u.usuario 		= 	?
							UNION
							SELECT 	2
							FROM	$this->catalog.usuario_modulo um
									
									INNER JOIN $this->catalog.modulos m ON
									m.id_modulo		=	um.id_modulo
								AND	m.id_aplicativo	=	?
							INNER JOIN $this->catalog.usuarios u ON
									um.id_usuario	= 	u.id_usuario
							WHERE	u.usuario 		= 	?";

		$res = $this->DB->query($sql_chk_user, array(	$this->id_app,
														$user, 
														$this->id_app,
														$user)
							);

		if(!$res) {

			$this->lasterror = $this->DB->getLastError();
			if($this->debug_mode)
			{
				echo  "Error en consulta a privilegios de app: $this->lasterror";
			}
			return false;

		}

		if($res->rowCount() <= 0){

			$this->debug_msg = "Usuario $user Login: " . (new DateTime('NOW'))->format("Y-m-d H:i:s")
								. " sin acceso a aplicativo $this->id_app";
			if($this->debug_mode)
			{
				echo  $this->debug_msg;
			} 
			return false;
		}
		
		if($this->debug_mode)
		{
			echo  "Usuario $user con acceso al aplicativo";
		}
		return true;
	}

	function isLogged(){

		if( !isset($_SESSION) || !isset($_SESSION[$this->llave_sesion_valida]) ){

			if($this->debug_mode)
			{
				echo  "Sin login";
			}

			return false;

		}

		if($this->debug_mode)
		{
			echo  "Login correcto";
		}
		return true;


	}

	function isPageProtected($page){

		$sql_chk_page = "	SELECT 	1
							FROM 	$this->catalog.modulos m

									INNER JOIN $this->catalog.sub_modulo sm ON
									sm.id_modulo	=	m.id_modulo

							WHERE	sm.ruta = ?
								and m.id_aplicativo = ?";

		$res = $this->DB->query($sql_chk_page, array(	$page, 
												$this->id_app)
					);

		if(!$res) {

			$this->lasterror = $this->DB->getLastError();
			if($this->debug_mode)
			{
				echo "Error en consulta: $this->lasterror";
			}

			return false;

		}

		if($res->rowCount() == 0){

			if($this->debug_mode)
			{
				echo "Página $page es pública!";
			}

			return false;
		}
		
		if($this->debug_mode)
		{
			echo "Página $page protegida!";
		}
		return true;

	}


	function userHasPageAccess($user, $page, & $ip){

		$sql_chk_page_access 
						= "	SELECT 	ifnull(uip.ip_acceso,'0') as ip_acceso

							FROM 	$this->catalog.usuarios u
									
									INNER JOIN $this->catalog.usuario_perfil up ON
									up.id_usuario		=	u.id_usuario

									INNER JOIN $this->catalog.modulo_perfil mp ON
									mp.id_perfil	=	up.id_perfil

									INNER JOIN $this->catalog.modulos m ON
									m.id_modulo		=	mp.id_modulo

									INNER JOIN $this->catalog.sub_modulo sub ON
									sub.id_modulo	=	m.id_modulo

									LEFT JOIN $this->catalog.usuario_perfil_ip uip ON
									uip.id_usuario	=	u.id_usuario
								AND	uip.id_perfil	=	up.id_perfil

							WHERE	u.usuario		= 	?
								and sub.ruta 		= 	?
								and m.id_aplicativo	=	?

							UNION
							SELECT	ifnull( umi.ip_acceso, '0') as ip_acceso
							FROM	$this->catalog.usuarios u

									INNER JOIN $this->catalog.usuario_modulo um ON
									um. id_usuario	=	u.id_usuario

									INNER JOIN $this->catalog.modulos m ON
									m.id_modulo		=	um.id_modulo

									INNER JOIN $this->catalog.sub_modulo sub ON
									sub.id_modulo	=	m.id_modulo

									LEFT JOIN $this->catalog.usuario_modulo_ip umi ON
									umi.id_modulo	=	m.id_modulo
								AND	umi.id_usuario	=	u.id_usuario

							WHERE	u.usuario		=	?
								AND	sub.ruta		=	?
								AND	m.id_aplicativo	=	?

							ORDER BY ip_acceso";

		$res = $this->DB->query($sql_chk_page_access, array(	$user,
														$page,
														$this->id_app,
														$user,
														$page,
														$this->id_app
													)
								);

		if(!$res) {

			$this->lasterror = $this->DB->getLastError();
			if($this->debug_mode)
			{
				echo "Error en consulta: $this->lasterror";
				echo "<pre> $sql_chk_page_access </pre>";
			}
			return false;

		}

		if($res->rowCount() <= 0){

			$this->debug_msg = "Usuario $user Login: " . (new DateTime('NOW'))->format("Y-m-d H:i:s")
								. " sin acceso a página: $page de aplicativo:  $this->id_app";

			if($this->debug_mode)
			{
				echo $this->debug_msg;
			}

			return false;
		}
		
		$row	=	$res->fetchColumn();
		$ip		=	$row;
		if($this->debug_mode)
		{
			echo "Usuario: $user con acceso a $page desde ip: $ip";
		}
		return true;

	}

	function logAccess(){


	}
}