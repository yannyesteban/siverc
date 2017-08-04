<?php
require_once ("../../sigefor_postgres/clases/cls_catalogo.php");
function enviar_email($nombre){
	$ins = "";
	if(isset($_GET["cfg_ins_aux"]) or isset($_POST["cfg_ins_aux"])){
		$ins = $_POST["cfg_ins_aux"];
		$aut = $_SESSION["VSES"][$ins]["SS_AUT"];
		$ses = &$_SESSION["VSES"][$ins];
	}else{	
		$aut = false;
	}// end if
	if(!$aut){
		echo "no tiene autorizacion";
		exit;
	}// end if
	
	$rep = new cls_catalogo;
	$rep->vses = &$ses;
	$rep->vform = &$_POST;
	
	
	$mensaje = $rep->control($nombre);
	//hr($rep->vreg["correo_electronico"]);exit;

	$d_correo = $rep->vreg["correo_electronico"];
	$d_name = $rep->vreg["nombre1"]." ".$rep->vreg["apellido1"];
	//****Script para el envio de Correo
	//Incluyo la libreria 
	
	

	$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
	$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
	// Cabeceras adicionales
	$cabeceras .= "To: $d_correo" . "\r\n";
	$cabeceras .= "From: Servicio Autónomo de Contraloría Sanitaria <sacs@mpps.gob.ve>" . "\r\n";

	
	try{
	 
		$error=0;
		mail(	$d_correo, 
				"Sistema Automatizado de Contraloría Sanitaria SIACS", 
				$mensaje,
				$cabeceras);	
	
	}catch (phpmailerException $e) {
		$error = 2;
	  	return "$d_correo, ".$e->errorMessage(); //Envia messages de error desde PHPMailer
	}catch (Exception $e) {
		$error = 1;
	  return "$d_correo, ".$e->getMessage(); //Mensajes de Errores Aburridos desde otro!
	}  
	//*****************
	//****Fin del Envio de correo
	

	
	return $error;


}// end function

function gen_id($prefijo){
	return uniqid($prefijo);

}// end function


?>
