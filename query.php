<?php
session_start();
include("../sigefor2017/clases/funciones.php");
include("../sigefor_postgres/clases/cls_conexion_pg.php");
require_once("bd_constante.php");
$ins = "";




if(isset($_GET["cfg_ins_aux"]) or isset($_POST["cfg_ins_aux"])){
	$ins = $_GET["cfg_ins_aux"];
	$aut = $_SESSION["VSES"][$ins]["SS_AUT"];
	$ses = &$_SESSION["VSES"][$ins];

}else{	
	$aut = false;

}
if(!$aut){
	echo "no tiene autorizacion";
	exit;
}// end if


?>


<title>Query 2004</title>
<link rel="stylesheet" type="text/css" href="../sigefor2017/css/sigefor.css">
<div>
  <div align="center"><img src="../sigefor2017/imagenes/sigefor.png" /></div>
</div>

<?php

/*
<input name="query" type="text" value="<?php echo $query; ?>" size="75" maxlength="255">


*/

//$DB_conn = mysql_connect($ses["SS_BD_SERVIDOR"],$ses["SS_BD_USUARIO"],$ses["SS_BD_PASSWORD"]) or die ('Error: No se puede conectar a la Base de Datos');
$DB_conn = pg_connect("host=".$ses["SS_BD_SERVIDOR"]." dbname=".$ses["SS_BDATOS"]." user=".$ses["SS_BD_USUARIO"]." password=".$ses["SS_BD_PASSWORD"]." port=5432");

//$DB_conn = mysql_connect("10.29.65.20","root","hackcity") or die ('Error: No se puede conectar a la Base de Datos');
$dbase = $ses["SS_BDATOS"];

if(!isset($_GET["query"])){
	$dbase = $ses["SS_BDATOS"];
	$iquery = "SELECT NOW()";}
else{
	//$dbase = $_GET["lst_db"];
	$iquery = stripslashes($_GET["query"]);}



$iquery2 = leer_var($iquery,$_SESSION["VSES"][$ins],"@",true);	

$aux = explode(";",$iquery2);	
foreach($aux as $k => $v){
	if(!trim($v)){
		continue;
	}//
	$query = $v;


	$aut = "";
	$tabla="";
	if($query =="show tables"){
		$query=var_tablas($ins);
		$aut = "show tables";
	}// end if
	
	if(isset($_GET["describe"]) and $_GET["describe"]!=""){
		$query=describe_tabla($_GET["describe"],$ins);
		$tabla = $_GET["describe"];
		$aut = "describe ".$_GET["describe"];
	
	}
	if(substr($query,0,9)=="describe "){
		$query=describe_tabla(substr($query,9),$ins);
		$tabla = substr($query,9);
		$aut = "describe ".$tabla;
	
	}




	
	//mysql_select_db($dbase) or die ('Error: Acceso a Base de datos fallida');
	$result = @pg_query($query);
	
	
	
	
	if ((substr_count (strtoupper($query), strtoupper("UPDATE "))==0 AND
		substr_count (strtoupper($query), strtoupper("INSERT "))==0 AND
		substr_count (strtoupper($query), strtoupper("DELETE "))==0 AND
		substr_count (strtoupper($query), strtoupper("BEGIN"))==0 AND
		substr_count (strtoupper($query), strtoupper("ROLLBACK"))==0 AND
		substr_count (strtoupper($query), strtoupper("COMMIT"))==0 AND
		substr_count (strtoupper($query), strtoupper("GRANT "))==0 AND
		
		substr_count (strtoupper($query), strtoupper("EXIT"))==0 AND
		substr_count (strtoupper($query), strtoupper("DROP "))==0 AND
		substr_count (strtoupper($query), strtoupper("TRUNCATE "))==0 AND
		substr_count (strtoupper($query), strtoupper("ALTER "))==0 AND
		substr_count (strtoupper($query), strtoupper("CREATE "))==0)
		OR substr_count (strtoupper($query), strtoupper("SHOW "))>0){
		$number_cols = @pg_num_fields($result);
		echo "<table border = 0 cellspacing=3 class='query'>\n";
		echo "<tr align=center>\n";
		if(isset($_GET["describe"])){
			$tabla = $_GET["describe"];
		}else{
			if(@pg_field_table($result,0)!="COLUMNS"){
				$tabla=@pg_field_table($result,0);
			}else{
				$tabla = $_GET["tabla"];
			}
		}
		
		//$tabla=mysql_field_table($result,0);
		if (substr_count(strtoupper($query), strtoupper("SHOW "))>0){
			$hacer_link = true;
			}
		else{
			$hacer_link = false;
		}// end if
		for ($i=0; $i<$number_cols; $i++){
			echo "<th class='query'>" . @pg_field_name($result, $i). "</th>\n";
			echo ($hacer_link)?"<th class='query'>Descripción</th>":"";
	
		}// next
		echo "</tr>\n";
		while ($row = @pg_fetch_row($result)){
			echo "<tr align=left>\n";
			for ($i=0; $i<$number_cols; $i++){
				echo "<td class='query'>";
				if ($row[$i]==""){
					echo "&nbsp;";
					}
				else{
					echo (!$hacer_link)?($row[$i]):("<a class='query' href='?query=SELECT * FROM ".$row[$i]."&tabla=".$row[$i]."&lst_db=$dbase&cfg_ins_aux=$ins'>".$row[$i]."</a>
					<td class='query'><a class='query' href='?query=DESCRIBE ".$row[$i]."&tabla=".$row[$i]."&lst_db=$dbase&cfg_ins_aux=$ins'>Detalle</a></td>");
				}// end if
				echo "</td>\n";
			}// next
			echo "</tr>\n";
		}// end while
		echo "</table>";
		$tabla = ($tabla=="" or $tabla==null)?(isset($_GET["tabla"])?$_GET["tabla"]:$tabla):$tabla;
	}
	else {
		$tabla = $_GET["tabla"];
		echo("ok");
	}
	if(pg_last_error() ){
		echo "<b><img src=\"imagenes/error.png\" width=\"160\" height=\"160\" /><br>".pg_last_error()."<b><br><br>";
	}
}// next
?>

<form action="query.php" method="<?php echo $_GET["metodo"];?>">
  <select name="lst_db">

<?php
/*
	$lista_db = array();//mysql_list_dbs();
    $i = 0;
    $cnt = mysql_num_rows($lista_db);
    while ($i < $cnt) {

		$db_name = mysql_db_name($lista_db, $i);
		if ($db_name ==$dbase){
			$db_nameIndex = $db_name." selected ";}
		else {
			$db_nameIndex = $db_name;}
    	echo ("<option value=$db_nameIndex>$db_name</option>");
		$i++;}
		
	*/
		 pg_free_result($result);
	pg_close($DB_conn);
	
function var_tablas($ins){
	$query = "select 
				'<a href=''?&cfg_ins_aux=$ins&query=select * from ' || tablename || '''>' || tablename || '</a>' as Tabla,	
	
	'<a href=''?&cfg_ins_aux=$ins&describe=' || tablename || '''>' || 'Describe' ||'</a>'as describe from pg_tables where tablename not like 'pg\_%'
			and tablename not in ('sql_features', 'sql_implementation_info', 'sql_languages',
	 		'sql_packages', 'sql_sizing', 'sql_sizing_profiles','sql_parts') order by tablename;";
	return $query;

}// end function	
function describe_tabla($tabla, $ins){
	$query = "SELECT a.attname as nombre,
	case atttypid
	when 1043 then 'varchar'
	when 23 then 'integer'
	when 25 then 'text'
	when 1082 then 'date'
	else 'desconocido'
	end as TYPE,
	
	
						case indisprimary when 't' then 'YES' else 'NO' end as KEY,
						case indisunique when 't' then 'YES' else 'NO' end as UNIQUE,
						case attnotnull when 't' then 'NO' else 'YES' end as NULL,
						CASE  WHEN attlen <0 THEN a.atttypmod -4  ELSE attlen END as longitud,
						NULLIF(btrim(split_part(b.adsrc,':',1),''''),'') as default
						FROM pg_attribute as a
						INNER JOIN pg_type as c ON a.attrelid=c.typrelid AND attnum>=1 AND typname = '$tabla'
						LEFT JOIN pg_attrdef as b ON b.adrelid = a.attrelid AND b.adnum = a.attnum
						LEFT JOIN pg_index as d ON d.indrelid = c.typrelid AND a.attnum = any (d.indkey)";
	return $query;

}// end function	
	
?>

  </select>
  <select name="metodo" id="metodo" onchange="this.form.method=this.value">
    
  </select>
  <br>

<textarea name="query" cols="100" rows="12" style="width:96%"><?php echo $iquery; ?></textarea>
<br>
<input type="submit">
<input type="submit" value = "Mostrar Tablas" onclick="query.value='show tables'">
<hr><input type="button" value = "Insert" onclick="query.value='INSERT INTO '+tabla.value+' VALUES ()'">
<input type="Submit" value = "Select" onclick="query.value='SELECT * FROM '+tabla.value">
<input type="Submit" value = "Describe" onclick="describe.value=tabla.value">
<input name="tabla" type="text" id="tabla" value="<?php echo $tabla;?>">
<input type="hidden" name="cfg_ins_aux" id="cfg_ins_aux"  value="<?php echo $ins;?>"/>
<input type="hidden" name="describe" id="describe"  value=""/>
<a href="query.php?cfg_ins_aux=<?php echo $ins;?>" target="_blank">Query</a>
<hr>
<input name="Submit" type="HIDDEN" onclick="query.value='TRUNCATE '+tabla.value" value = "Eliminar">
</form>
