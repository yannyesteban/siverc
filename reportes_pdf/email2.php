<?php
/**
* @propiedad: SACS
* @Fecha de Modificacion: 10/10/2014 15:37
* @Descripcion: Scriptr para el envio de correo de gmail
* @Editado por: GREGORIO JOSE BOLIVAR BOLIVAR
* @package: email.php
* @version: 1.0
*/
require_once ("../../sigefor_postgres/clases/cls_catalogo.php");
function enviar_email($nombre){
	$ins = "";
	if(isset($_GET["cfg_ins_aux"]) or isset($_POST["cfg_ins_aux"])){
		$ins = $_POST["cfg_ins_aux"];
		$aut = $_SESSION["VSES"][$ins]["SS_AUT"];
		$ses = &$_SESSION["VSES"][$ins];
	}else{	
		$aut = false;
	}
	if(!$aut){
		echo "no tiene autorizacion";
		exit;
	}

	$rep = new cls_catalogo;
	$rep->vses = &$ses;
	$rep->vform = &$_POST;
	
	$mensaje = $rep->control($nombre);

	$d_correo = $rep->vreg["correo_electronico"];
	$d_name = $rep->vreg["nombre1"]." ".$rep->vreg["apellido1"];

	// Incluyo la libreria 
	require_once('../../PHPMailer_v5.1/class.phpmailer.php');
	include("../../PHPMailer_v5.1/class.smtp.php"); // class.phpmailer.php 

	// El parametro true significa que este enviara excepciones de error, los cuales se deben capturar
	$mail = new PHPMailer(true); 
	$emailPara = $d_correo;
	$name = $d_nombre;
	$subject ='Sistema Automatizado de Contraloria Sanitaria SIACS';
	// Indicar a la Clase que use SMTP 
	$mail->IsSMTP(); 
	$error=0;
	
    	try {
         	$mail->SMTPDebug  = 1;                     // Habilita el Depurador SMTP (testing)
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = "ssl";
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 465;
		$mail->Username   = "informaticasacs@gmail.com";
		$mail->Password   = "sacs_informatica";
		$mail->AddReplyTo('informaticasacs@gmail.co', 'webmaster');
		$mail->SetFrom($emailPara, $name);
		$mail->Subject = $subject;
		//$mail->sender ='SACS';
		$mail->MsgHTML($mensaje);
		$mail->Send();
	}catch (phpmailerException $e) {
		$error = 2;
	  	echo "$emailPara, ".$e->errorMessage(); //Envia messages de error desde PHPMailer
	}catch (Exception $e) {
		$error = 1;
	  	echo "$emailPara, ".$e->getMessage(); //Mensajes de Errores Aburridos desde otro!
	}   
	return $error;

}// end function

function gen_id($prefijo){
	return uniqid($prefijo);
}// end function


?>

