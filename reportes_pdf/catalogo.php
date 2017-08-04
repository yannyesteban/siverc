<?php
session_start();
header('Content-Type: text/html; charset=iso-8859-1');
require_once ("constantes.php");
require_once ("clases/sg_constantes.php");
require_once ("clases/cls_conexion_pg.php");
require_once ("clases/funciones.php");
require_once ("clases/funciones_sg.php");
require_once ("clases/cls_catalogo.php");
require_once ("clases/cls_documento.php");
$rep = new cls_catalogo;
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

$rep->vses = &$ses;

$rep->vform = &$_GET;
$doc = new cls_documento;
$doc->body = $rep->control($rep->vform["rep_nombre"]);
$doc->title = $rep->titulo;
echo $doc->control();
?>