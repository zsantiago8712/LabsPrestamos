<?       
    class ccaMySQL{

        var $resultQuery; //Objeto con las filas recuperadas de un query.
        var $dbHandle;  //Objeto PDO conectado a una base de datos.
        var $lasterror; //Mensaje del último error en la base de datos.
        var $lasterrornum; //Código del último error en la base de datos.
        var $numrows; //Número de filas recuperadas desde una consulta.
        var $affectedrows; //Número de filas afectadas por una consulta de cambio.
        var $lastid; //Id asignado por la base de datos en un campo auto_increment.

        function __construct($link){
            
            $this->dbHandle = $link;
        }

        /* Método público
           Ejecuta una consulta (SELECT) o modificación (INSERT,UPDATE,DELETE)
           Ejemplo:
           $query = "select nombre, apellido
                     from   catalogos.alumnos
                     where  cuenta = ?";
           $parametros = array(2501);

           Ejemplo in:
           $query = "select nombre, 
                            apellido

                     from   catalogos.alumnos
                     where  semestre in {in}
                        and nombre like ?";
           $parametros = array("{in}45,46,47", "Maria%")

           Ejemplo multiselect (ejecuta el mismo query en varias bases de datos)
           $query = "select {periodo}, 
                            {col}, 
                            other_col

                     from `{bd}`.table1
                     where id_1 in({in})
                       and id2 = ? and id3 = ?
                       and id_4 in({in})
                       and id_5 = ?";
            $arr = array(
             array("bd" => 'p2014-Uso', "periodo" => 'Otoño 2017' , "col" => '28'),
             array("bd" => 'o2014-uso', "periodo" => 'Otoño 2016' , "col" => '30'),
             array("bd" => 'p2015-uso', "periodo" => 'Otoño 2015' , "col" => '32')
             );
    
            $parametros = array("{in}1,2,3",4,5,"{in}6,7,8,9",10);
            $sep = "union all\n";
        */
        function query($query, $parametros=array(), $multi_query=array(), $sep = ""){

            if(count($multi_query)){

                $org = $parametros;

                //Se genera el query multi base reemplazando las llaves con sus valores
                //por cada array asociativo en multi_query
                $query = $this->multi_query($query, $multi_query, $sep);

                //El arreglo parámetros se n-plica para multiqueries
                for( $j = 1; $j < count($multi_query); $j++ )
                {
                    $parametros = array_merge($parametros,$org);
                }
            }

            $arr_tmp    = $this->extrae_param_arreglos($parametros);
            $query      = $this->prepara_query_in($query,$arr_tmp);
            $parametros = $this->expande_param_array($parametros);

            try {
                 
                $stmt = $this->dbHandle->prepare($query);
                $stmt->execute($parametros);
                $this->resultQuery = $stmt;
                    
            } catch (PDOException $e) {
                    
               $this->lasterror = $e->getMessage();
               $this->lasterrornum = (int) $e->getCode();
               return 0;
            }    
            
            $this->numrows = $stmt->rowCount();
            return new ccaResultSet($stmt);
            //return 1;

        }

        /* Método interno, utilizar query
           Genera un nuevo query a partir de $query que se repite de acuerdo
           al número de arreglos asociativos contenidos en $parametros.
           
           A cada uno de los queries se le reemplazan las llaves (en formato
           {llave}) por el valor corresponiente a esta en el arrego asociativo
           de cada iteración.

           Ejemplo:
           $query = "select ventas from `{basedatos}`.facturacion";
           $parametros = "[[basedatos=>ventas2020],[basedatos=>ventas2021]]";
           $sep = "union all\n";

           salida:
            select ventas from `ventas2020`.facturacion
            union all
            select ventas from `ventas2021`.facturacion
        */
        function multi_query($query, $parametros = array(), $sep = "\n"){

            $retval = "";

            foreach($parametros as $key => $value){
    
                $temp = $query;
    
                foreach($value as $key2 => $value2){
        
                    $temp = str_replace('{'.$key2.'}', $value2 , $temp);
        
                }
    
                if( ($key +1)  < count( $parametros)){
                    $temp .= " " . $sep;
                }
                
                $retval .= $temp;
            
            }

            return $retval;
        }

        function getLastError(){

            return $this->lasterror;
        }

        function insert($query, $parametros=array()){

            $this->numrows = 0;

            $chk = $this->query($query,$parametros);

            if($chk)
                $this->lastid   =   $this->dbHandle->lastInsertId();

            $this->affectedrows = $this->numrows;
            return $chk;
        }

        function update($query, $parametros=array()){

            $this->numrows = 0;

            $chk = $this->query($query,$parametros);

            $this->affectedrows = $this->numrows;
            return $chk;
        }

        function delete($query, $parametros=array()){
            
            $this->numrows = 0;

            $chk = $this->query($query,$parametros);

            $this->affectedrows = $this->numrows;
            return $chk;
        }

        function fetchAllArrayAsoc(){

           return $this->resultQuery->fetchAll(PDO::FETCH_ASSOC);  

        }

        function fetchAllArray(){

           return $this->resultQuery->fetchAll(PDO::FETCH_NUM);  

        }

        function fetchAllObjects(){

            return $this->resultQuery->fetchAll(PDO::FETCH_OBJ); 
        }

        function fetchArray(){

            return $this->resultQuery->fetch(PDO::FETCH_NUM);
        }

        function start_transaction(){

            $this->dbHandle->beginTransaction();

        }

        function commit(){

             $this->dbHandle->commit();

        }
        
        function rollback(){

             $this->dbHandle->rollback();

        }

        /* 
            Método interno
            Retorna arreglo de arreglos asociativos con la posición en el arreglo 
            parametros y arreglo con los valores expandidos de la cadena
            {in}val1,val2... en el arreglo parametros
            La salida se utiliza en el método prepara_query_in
        */

        function extrae_param_arreglos( $parametros = array()){
            
            $tmp = array();
            
            foreach($parametros as $key => $value){
                
                if(substr($value,0,4) == "{in}"){
                    
                    $tmp[] = array("arr" => explode(",",substr($value,4)),
                                   "pos" => $key);
                }
            }
            return $tmp;
        }

        /*  Método interno
            Regresa arreglo expandido de los elementos en parametros que tienen
            la forma {in}1,2,3

            Ejemplo:
            Entrada
             $parametros = array(3453,"GO%","{in}123,145,323");
            Salida:
             array(3453,"GO%",123,145,323);
        */
        function expande_param_array( $parametros ){

            $tmp = array();

            foreach($parametros as $key => $value){
                
                if(substr($value,0,4) == "{in}"){
                    
                    $tmp = array_merge($tmp, explode(",",substr($value,4)));

                }else{
                    $tmp[] = $parametros[$key];
                }
            }
            return $tmp;
        }

        /*Regresa el query con {in} reemplazado por ?,?,? para expresiones in(...)*/
        function prepara_query_in($query,$arr_in){
            
            foreach($arr_in as $key => $value ){
                
                $in  = str_repeat('?,', count($value["arr"]) - 1) . '?';
                
                
                $pos = strpos($query, "{in}");
                
                if ($pos !== false) {

                    $query = substr_replace($query, $in, $pos, strlen("{in}"));
                    
                }
                
            }
            return $query;
        }

    } 
    //http://sandbox.onlinephpfunctions.com/code/3311254ec55bcb623787fe701b30f8b19027783f
    //Basado en https://phpdelusions.net/pdo

    //Clase que encapsula un result set PDO y genera métodos más parecidos a los de la antigua
    //biblioteca mysql, esto falcilitará la migración del código que sobre un result set vuelve
    //a consultar la base de datos
    class ccaResultSet{

        var $resultSet; //Objeto con las filas recuperadas de un query.

        function __construct($statement){
            
            $this->resultSet = $statement;
        }

        function fetchAllArrayAsoc(){

           return $this->resultSet->fetchAll(PDO::FETCH_ASSOC);  

        }

        function fetchAllArray(){

           return $this->resultSet->fetchAll(PDO::FETCH_NUM);  

        }

        function fetchAllObjects(){

            return $this->resultSet->fetchAll(PDO::FETCH_OBJ); 
        }

        function fetchArray(){

            return $this->resultSet->fetch(PDO::FETCH_NUM);
        }

	function fetchArrayAsoc(){

            return $this->resultSet->fetch(PDO::FETCH_ASSOC);
        }

        function fetchColumn(){
            return $this->resultSet->fetchAll(PDO::FETCH_COLUMN);
        }

        function rowCount(){

            return $this->resultSet->rowCount();

        }
    }
?>