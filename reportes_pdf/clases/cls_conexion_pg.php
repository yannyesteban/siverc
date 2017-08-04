<?php
/*****************************************************************
creado: 01/05/2007
modificado: 28/09/2007
por: Yanny Nuñez
*****************************************************************/
//require_once("sg_constantes.php");
//require_once("../constantes.php");
//require_once("funciones.php");

/*

define ("C_ERROR_CNN_FALLIDA","Error: No se pudo hacer la conexión al servidor DB");
define ("C_ERROR_BD_FALLIDA","Error: No se pudo conectar a la base de datos");
define ("C_ERROR_QUERY","Error: No se pudo realizar la consulta");
define ("C_MERROR_DUPLICADO","4");
define ("C_MERROR_RESTRICCION","1");

define ("C_ERROR_RESTRICCION","Error: fallo en la restricciones de la tabla");
define ("C_ERROR_ELIMINACION","Error: No se pudo hacer la eliminación, existe una restricción en la tabla");
define ("C_ERROR_COLUMNA","Error: columna desconocida en la consulta ejecutada");
define ("C_ERROR_DUPLICADO","Error: El registro que se intentó agregar ya existe");
define ("C_ERROR_GENERAL","Error: Transacción Fallida");
define ("C_ERROR_EXISTE_DB","No se puede crear la Base de Datos, ya existe");
define ("C_ERROR_UPD_DEL_FK","Este registro tiene datos asociados, y no puede ser eliminado o actualizado");
*/
class descrip_campos{
	var $tabla = "";
	var $campo = "";
	var $aux = "";
	var $tipo = "";
	var $longitud = "";
	var $primary = false;
	var $index = false;
	var $serial = false;
	var $default = "";
	var $null = true;
	var $unique = false;
	var $meta = "";
	var $num = false;
}// end class

class cls_conexion{
	var $servidor = C_SERVIDOR;
    var $bdatos = C_BDATOS;
    var $usuario = C_USUARIO;
	var $password = C_PASSWORD;
	var $puerto = "5432";
	//======================
	var $conexion;
    var $estado = false;
	//======================
	var $query;
	var $result;
	//======================
	var $paginacion = C_NO;
	var $pagina = 1;
    var $reg_ini = C_REG_INI;
    var $reg_pag = C_REG_PAG;
	//======================
	var $filas_afectadas = 0;
	var $insert_id;
    var $reg_total = 0;
	var $nro_paginas = 0;
    var $nro_filas;
    var $nro_campos;
    //======================
	var $con_transaccion = C_NO;
	var $error=false;
    var $errno=0;
	var $errno_m;
    var $errmsg="";
	var $errabs = 0;
	var $mostrar_error = true;
	// propiedades para los registros
    var $con_descrip=false;
	var $taux = C_TABLA_AUX;
	var $ckeys = "";
	var $claves;
	var $add_claves = "";
	var $add_clave = array();
	var $es_clave;
	//==========================================================
	// Funcion constructora de la clase
	//===========================================================
	function cls_conexion($servidor="",$usuario="",$password="",$bdatos="",$puerto=""){
	
    	if ($servidor!=""){
			$this->servidor = $servidor;
        }// end if
    	if ($usuario!=""){
			$this->usuario = $usuario;
        }// end if
    	if ($password!=""){
			$this->password = $password;
        }// end if
    	if ($servidor!=""){
			$this->bdatos = $bdatos;
        }// end if
    	if ($puerto!=""){
			$this->puerto = $puerto;
        }// end if
		
	    if($this->conexion = pg_connect("host=$this->servidor dbname=$this->bdatos user=$this->usuario password=$this->password port=$this->puerto")
				or die (C_ERROR_CNN_FALLIDA)){
			$this->estado = true;					
		}// end if
    }// end if
	//===========================================================
    function ejecutar($query_x=""){
		if (!$this->conexion){
			$this->cls_conexion();
		}// end if
		if ($this->con_transaccion==C_SI and $this->errabs>0){
			return false;
		}// end if
		if ($query_x!=""){
			$this->query = $query_x;
        }// end if
		$this->query = $this->hacer_query($this->query);
		//hr($this->query,"red");
		pg_send_query($this->conexion,$this->query);
		//pg_query($this->query);
        //hr(2);
		$this->result = pg_get_result($this->conexion);
		if (@pg_last_error()){
			$this->errabs++;
	        $this->es_error(true);
	        return false;
        }// end if
        if ($this->es_select($this->result)){
			$this->con_result = true;
        	$this->nro_filas = @pg_num_rows($this->result);
            $this->nro_campos = @pg_num_fields($this->result);
            $this->reg_total = $this->nro_filas;
	        if ($this->paginacion == C_SI 
						and is_numeric($this->pagina)
						and is_numeric($this->reg_pag) 
						and $this->reg_total > 0 
						and $this->reg_pag > 0
						and preg_match("/^([^\w]+|\s*)\bselect\b/i", $this->query)
						and !preg_match("/ limit\s+[0-9]/i", $this->query)){
				$this->nro_paginas = ceil($this->reg_total/$this->reg_pag);
				if($this->pagina > $this->nro_paginas){
					$this->pagina = $this->nro_paginas;
				}if($this->pagina<=0){
					$this->pagina = 1;
				}// end if
				$this->reg_ini = $this->reg_pag * ($this->pagina-1);
                $this->result = @pg_query($this->query." LIMIT $this->reg_pag OFFSET $this->reg_ini");
				$this->nro_filas = @pg_num_rows($this->result);
	        }// end if
        }else{
        	$this->filas_afectadas = @pg_affected_rows();
            $this->insert_id = @pg_last_oid();
			$this->con_result = false;
        }// end if
        return $this->result;
    }// end function
	//===========================================================
    function consultar($result_x=""){
		if(!$this->es_consulta){
			return false;
		}// end if
    	if ($result_x!=""){
			$this->result = $result_x;
        }// end if
		return pg_fetch_array($this->result);
    }// end function
	//===========================================================
    function consultar_asoc($result_x=""){
		if(!$this->es_consulta){
			return false;
		}// end if
    	if ($result_x!=""){
			$this->result = $result_x;
        }// end if
		return pg_fetch_assoc($this->result);
    }// end function
	//===========================================================
    function consultar_simple($result_x=""){
		if(!$this->es_consulta){
			return false;
		}// end if
    	if ($result_x!=""){
			$this->result = $result_x;
        }// end if
		return pg_fetch_row($this->result);
    }// end function
	//===========================================================
    function resetear($result_x=""){
		if(!$this->es_consulta){
			return false;
		}// end if
    	if ($result_x!=""){
			$this->result = $result_x;
        }// end if
		return pg_result_seek($this->result,0);
    }// end function
	//===========================================================
	function ejecutar_m($query_x=""){
		if ($query_x!=""){
			$this->query = $query_x;
        }// end if
		if (!$this->conexion){
			$this->cls_conexion();
		}// end if
		if ($this->con_transaccion==C_SI and $this->errabs>0){
			return false;
		}// end if
		$array = preg_split("/(?<!\\\)".C_SEP_Q."/",$this->query);
		$this->nro_query = count($array);
		for ($i=0; $i<$this->nro_query;$i++){
			$this->query_m[$i]=$array[$i];
			$this->result_m[$i] = @pg_query($array[$i]);
			$this->errno_m[$i] = @pg_last_error();
			if (@pg_last_error()){
				$this->errabs++;
			}// end if
		}// next
	}// end if
	//===========================================================
    function begin_trans(){
        pg_query("BEGIN");
		$this->errabs = 0;
    }// end function
	//===========================================================
    function end_trans($tipo_x=C_COMMIT){
    	switch($tipo_x){
		case C_COMMIT:
        	$this->commit();
            break;
		case C_ROLLBACK:
        	$this->rollback();
            break;
		case C_IGNORAR_TRANS:
            // no hace nada
            break;
        }// end switch
		$this->errabs = 0;
    }// end function
	//===========================================================
    function rollback(){
        pg_query("ROLLBACK");
		$this->errabs = 0;
    }// end function
	//===========================================================
    function commit(){
        pg_query("COMMIT");
		$this->errabs = 0;
    }// end function
	//===========================================================
    function descrip_campos($result=""){
		if($result!=""){
			$this->result = $result;
		}// end if
		unset($this->tablas);
		$this->nro_campos = @pg_num_fields($this->result);
		$this->nro_filas = @pg_num_rows($this->result);

		if($this->add_claves !=""){
			$aux = explode(C_SEP_L,$this->add_claves);
			foreach($aux as $k => $v){
				$this->add_clave[$v] = $v;
			}// next
		}// end if
		
    	for ($i=0;$i< $this->nro_campos;$i++){
            $tabla = @pg_field_table($this->result,$i);
			$campo = @pg_field_name($this->result,$i);
			
			if($tabla != null and $tabla != ""){
				$aux = false;
			}else{
				$aux = true;
				$tabla = $this->taux;
			}// end if
			$this->campo[$tabla][$campo] = new descrip_campos;
			$this->campo[$i] = &$this->campo[$tabla][$campo];
			$this->campo[$tabla][$campo]->nombre = $campo;
			$this->campo[$tabla][$campo]->campo = $campo;
			$this->campo[$tabla][$campo]->tabla = $tabla;
			$this->campo[$tabla][$campo]->titulo = $campo;
			$this->campo[$tabla][$campo]->aux = $aux;
			$this->campo[$tabla][$campo]->tipo = @pg_field_type($result,$i);
			$this->campo[$tabla][$campo]->longitud = (@pg_field_size($result,$i)>0)?@pg_field_size($result,$i):"";
			$this->campo[$tabla][$campo]->meta = $this->tipo_meta($this->campo[$tabla][$campo]->tipo);
			$this->campo[$tabla][$campo]->num = $i;
			if($this->add_clave[$v]==$campo){
				$this->campo[$tabla][$campo]->clave = true;
			}// end if
			if(!$aux){
				$this->tablas[$tabla] = "1";
			}else{
				$this->tablas[$tabla] = "2";
			}// end if
		}// next
		if(!$this->tablas){
			return true;
		}// end if
		$this->ckeys = "";	
		foreach ($this->tablas as $tabla => $v){
			$query_x = "SELECT a.attname as nombre,
						indisprimary as primary,
						indisunique as unique,
						attnotnull,
						CASE  WHEN attlen <0 THEN a.atttypmod -4  ELSE attlen END as longitud,
						CASE WHEN b.adsrc='NULL::character varying' THEN null
						ELSE NULLIF(btrim(split_part(b.adsrc,':',1),'\''),'') 
						END as default
						FROM pg_attribute as a
						INNER JOIN pg_type as c ON a.attrelid=c.typrelid AND attnum>=1 AND typname = '$tabla'
						LEFT JOIN pg_attrdef as b ON b.adrelid = a.attrelid AND b.adnum = a.attnum
						LEFT JOIN pg_index as d ON d.indrelid = c.typrelid AND a.attnum = any (d.indkey)";
			$result = @pg_query($query_x);
			
			$tabla_x=$tabla;
			while ($rs = @pg_fetch_array($result)){	
				$campo = $rs["nombre"];
				if(!$this->campo[$tabla][$campo]->nombre){
					continue;
				}// end if
				if(substr($rs["default"],0,8)=="nextval("){
					$this->campo[$tabla][$campo]->serial = true;
					$this->serial[$tabla] = C_CLAVE_SERIAL;
				}else{
					$this->campo[$tabla][$campo]->default = $rs["default"];
				}// end if
				$this->campo[$tabla][$campo]->longitud = $rs["longitud"];
				$this->campo[$tabla][$campo]->null = ($rs["attnotnull"]=="t")?false:true;
				$this->campo[$tabla][$campo]->unique = ($rs["unique"]=="t")?true:false;
				if($rs["primary"]=="t"){
					$this->campo[$tabla][$campo]->clave = true;
					$this->ckeys .= (($this->ckeys!="")?C_SEP_L:"").$tabla.".".$campo;
				}// end if
				if($this->campo[$tabla_x][$nombre_x]->meta==C_TIPO_D){
					$this->campo[$tabla][$campo]->longitud = "10";	
				}// end if
			}// end while
		}// next			
    }// end function
	//===========================================================
	function es_select($result_x){
		if (substr($result_x."",0,8)=="Resource"
        	or substr_count(strtoupper(substr($this->query,0,10)),strtoupper("SELECT "))>0){
			$this->es_consulta = true;
			return true;
        }// end if
		$this->es_consulta = false;
        return false;
    }// end function
	//===========================================================
	function hacer_query($query_x){
		if(!preg_match("|[ ]+|", trim($query_x))){
			return "SELECT * FROM ".$query_x;
		}else{
			return $query_x;	
		}// end if
    }// end function
	//===========================================================
	function es_error($error=false){
		$this->error = false;
		if ($error==false){
			return true;
		}// end if
		$this->error = true;
        $this->errno = pg_result_error_field($this->result,PGSQL_DIAG_SQLSTATE);
        $this->errmsg_o = pg_last_error();
		$this->errmsg = $this->msg_errores($this->errno,$this->errmsg_o);
		if ($this->mostrar_error){
			$errmsg=str_replace(chr(10),"",$this->errmsg." Query: $this->query");
			$errmsg=str_replace(chr(13)," ",$errmsg);
			//hr($this->errmsg_o);
			alert(addslashes($this->errmsg_o));
			//alert(addslashes($errmsg));
		}// end if
		return false;
	}// end function
	//===========================================================
	function show($msg){
		echo "<hr>$msg<hr>";
    }// end function
	//===========================================================
	function tipo_meta($tipo_x){
    	switch (strtoupper($tipo_x)){
        	case "int":
            	return C_TIPO_I;
            case 'MONEY': // stupid, postgres expects money to be a string
            case 'INTERVAL':
            case 'CHAR':
            case 'CHARACTER':
            case 'VARCHAR':
            case 'NAME':
            case 'BPCHAR':
            case '_VARCHAR':
            	return C_TIPO_C;
        	case "TEXT":
            	return C_TIPO_X;
        	case "DECIMAL":
            case 'SMALLINT':
            case 'BIGINT':
            case 'INTEGER':
            case 'INT8':
            case 'INT4':
            case 'INT2':
			case 'OID':
			case 'SERIAL':
            	return C_TIPO_N;
        	case "DATE":
        	case "TIMESTAMP":
            	return C_TIPO_D;
        	case "TIME":
            	return C_TIPO_T;
            default:
            	return C_TIPO_C;
        }// end switch
    }// end function
	//===========================================================
    function usar_bd($bd_x=""){
		if($bd_x!=""){
			$this->bdatos = $bd_x;
		}// end if
		$this->cls_conexion("","","",$this->bdatos);
	}// end fucntion
	//===========================================================
    function extraer_bdatos(){
        $query_x = "select datname from pg_database where datname not in ('template0','template1') order by 1";
		$result_x =  @pg_query($query_x);
		$i=0;
	    while ($rs=@pg_fetch_array($result_x)){
			$bdatos[$i] = $rs[0];
			$i++;
		}// end while
		return $bdatos;
    }// end function
	//===========================================================
    function extraer_tablas($db_x=""){
    	if ($db_x != ""){
			$bd_org = $this->bdatos;
			$this->usar_bd($db_x);
        }// end if
	    $result_x = @pg_query("select tablename,'T' from pg_tables where tablename not like 'pg\_%'
			and tablename not in ('sql_features', 'sql_implementation_info', 'sql_languages',
	 		'sql_packages', 'sql_sizing', 'sql_sizing_profiles','sql_parts')");
		$i=0;
	    while ($rs=@pg_fetch_array($result_x)){
			$tablas[$i] = $rs[0];
			$i++;
		}// end while
		if($db_x!=$bd_org){
			$this->usar_bd($bd_org);
		}// end if
       	return $tablas;
    }// end function
	//===========================================================
	function extraer_campos($tabla_x="",$db_x=""){
    	if ($tabla_x==""){
        	return false;
        }// end if
    	if ($db_x != ""){
			$bd_org = $this->bdatos;
			$this->usar_bd($db_x);
        }// end if
		$result_x = @pg_query("select * from $tabla_x where false");
        $nro_campos = @pg_num_fields($result_x);
        for ($i=0;$i<$nro_campos;$i++){
            $campos[$i] = @pg_field_name($result_x,$i);
        }// next
		if($db_x!=$bd_org){
			$this->usar_bd($bd_org);
		}// end if
        return $campos;
    }// end function
	//===========================================================
    function test($query_x=""){
    	if ($query_x==""){
			$query_x = $this->query;
        }// end if
		
		$this->paginacion = true;
		$this->reg_pag = 10;
        $result = $this->ejecutar($query_x);
		$cadena = "<table border='1'>";
        if (!$this->es_select($result)){
			return "consulta no valida";
        }// end if
        $this->descrip_campos($result);
       	$cadena .= "<tr>";
        for ($i=0;$i<$this->nro_campos;$i++){
            $cadena .= "<th>".$this->campo[$i]->nombre." ".$this->campo[$i]->meta.$this->campo[$i]->longitud."<br>(Dft:".$this->campo[$i]->default.")"."</th>";
        }// next
        $cadena .= "</tr>";
        while($this->arreglo = $this->consultar($result)){
        	$cadena .= "<tr>";
            for ($i=0;$i<$this->nro_campos;$i++){
	        	$cadena .= "<td>".$this->arreglo[$i]."</td>";
            }// next
            $cadena .= "</tr>";
        }// wend
        $cadena .= "</table>";
        return $cadena;
    }// end function
	//===========================================================
    function desconectar(){
    	if ($this->estado){
			$this->estado = false;
			pg_close($this->conexion);

        }// end if
	}// end function
	//===========================================================
	function msg_errores($nro_error,$msg_error=""){
		switch ($nro_error){
		case "00000":
			$this->meta_error = 1;
			return C_ERROR_RESTRICCION;
			break;
		case "00000":
			$this->meta_error = 2;
			return C_ERROR_ELIMINACION;
			break;
		case "00000":
			$this->meta_error = 3;
			return C_ERROR_COLUMNA;
			break;
		case "23505":
			$this->meta_error = 4;
			return C_ERROR_DUPLICADO;
			break;
		case "42P01":
			$this->meta_error = 5;
			return C_ERROR_TABLA;
			break;
		case "xxxx":
			$this->meta_error = 8;
			return C_ERROR_UPD_DEL_FK;
			break;
			
		default:
			$this->meta_error = 6;
			return $msg_error." N° de error: ".$nro_error;
		}// end switch
	}// end function
		
}// end class

/*$cn = new cls_conexion("localhost","pepe","123","farmacia","5432");

$ta=$cn->extraer_campos("agenda","prueba");
foreach($ta as $t => $v){
	echo "<br>".$v;

}// next


$cn->query = "select * from personas";
echo $cn->test($cn->query);


$result = $cn->ejecutar($cns->query);
$cn->config_avanzada($result);
while($rs=$cn->consultar()){
echo "<br>".$rs[0]." - ".$rs[1]." - ".$rs[2]." - ".$rs[3]." Nombres: ".$rs["nombres"];

}

echo "<hr>";
foreach ($cn->claves as $tabla =>$v){
	foreach($v as $t => $valores)
	echo "<br>$tabla   ".$t." = ".$valores;

}
echo "...".$cn->ckeys;

//$cn->ejecutar("insert into personasd values ('57','carlos','lopez','33','maracay');");
//hr($cn->error);
*/
?>