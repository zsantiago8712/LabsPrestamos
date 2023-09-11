<?php
/*El query deberá tener la siguiente forma:
	SELECT  columna1,
			columna2...
	WHERE	cond1
	-- opcional
	GROUP BY
	-- opcional
	HAVING cond2

 Las condiciones se irán agregando a un arreglo asociativo con las llaves
 cond1, cond2, condn. Cada condición se agregará a la anterior por un conector AND u OR
 la condición cero o default será 1 = 1 para que la cláusula WHERE tenga al menos una.
 Al agregar una condición, sus parámetros deberán agregarse a la variable de instancia con el método addParams
 $params, estos parámetros son los que se reemplazarán en el lugar de los placeholders y los que se enviarán al método query de la clase
 ccaMySQL

 Ejemplo: 
*/
class QueryHelper{

	private $query = "";

	// Array asociativo con las condiciones por llave a reemplazar en query
	private $condiciones = array();

	// Los parámetros ordenados a reemplazar en los placeholders ?
	private $params = array();

	
	function setQuery($query){
		$this->query = $query;
	}

	//Retorna el query con el conjunto de condiciones reemplazadas
	function getQuery(){

		foreach (array_keys($this->condiciones) as $value) {
			$this->query = str_replace(	$value, 
										$this->condiciones[$value],
										$this->query);
		}

		return $this->query;

	}

	//Registrar las cadenas dentro del query que representan los placeholders
	//a reemplazar por el conjunto de condiciones
	function registerConditions($cond){
		foreach ($cond as $key => $value) {
			$this->condiciones[$value] = " (1 = 1) ";
		}
	}

	//Agrega parámetros al arreglo de parámetros
	function addParams($params = array()){

		$this->params = array_merge($this->params, $params );

	}

	function getParams(){
		return $this->params;
	}
    /*
     @cond: string la cadena en el query que representa la condición (cond1, cond2, etc)
     @params: los parámetros que reemplazarán a los placeholders (?) al ejecutar el query
    */
	function AND_($key, $cond, $params = array() ){
		$this->addCondition($key, $cond, $connector = "AND");
	}

	function OR_( $key, $cond, $params = array() ){
		$this->addCondition($key, $cond, $connector = "OR");
	}

	function LIKE_OR($key, $column, $params = array(), $connector = "AND" ){

		if(count($params) ==0 ){
			$this->addCondition($key, " 1 = 1 ", $params, $connector);
			return;
		}

		$cond = QueryHelper::formParamCondition("$column LIKE ? %R%",
										   " OR $column LIKE ? ",
										   count($params));

		$this->addCondition($key, $cond, $params, $connector);

	}

	function LIKE_AND($key, $column, $params = array(), $connector = "AND" ){

		if(count($params) ==0 ){
			$this->addCondition($key, " 1 = 1 ", $params, $connector);
			return;
		}		

		$cond = QueryHelper::formParamCondition("$column LIKE ? %R%",
										   " AND $column LIKE ? ",
										   count($params));

		
		$new_params = array_map(function($item){return '%'.$item.'%';}, $params);

		$this->addCondition($key, $cond, $new_params, $connector);


	}

	function IN($key, $column, $params = array(), $connector = "AND" ){

		if(count($params) ==0 ){
			$this->addCondition($key, " 1 = 1 ", $params, $connector);
			return;
		}

		$cond = QueryHelper::formParamCondition("$column IN(? %R%)",
										   ",?",
										   count($params));


		$this->addCondition($key, $cond, $params, $connector);

		

	}


	private function addCondition($key, $cond, $params = array() , $connector = "AND" ){

		if(!array_key_exists($key, $this->condiciones)){
			$this->condiciones[$key] == "(1 = 1)";
		}

		$this->condiciones[$key] = "( " . 
								   $this->condiciones[$key] . 
								   "\n" .
								   $connector .
								   "(". 
								   $cond .
								   ")" . 
								   ") ";
		$this->addParams($params);

	}

	/*
	   Para repetir $repeat_expression dentro de $pattern $num_veces,
	   uitlizar la cadena %R% dentro de $pattern
	*/
	static function formParamCondition($pattern, $repeat_expression, $num_veces){
		
		$num_veces--;
		$nw_pattern = str_replace("%R%", str_repeat("%R%", $num_veces),$pattern);
		return str_replace("%R%", $repeat_expression, $nw_pattern);
		
		
	}
	
}