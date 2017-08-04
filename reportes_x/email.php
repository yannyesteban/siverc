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
	require_once('../../PHPMailer_v5.1/class.phpmailer.php');
	include("../../PHPMailer_v5.1/class.smtp.php"); // class.phpmailer.php 
	//***Envio de correo 
	$mail = new PHPMailer(true); // El parametro true significa que este enviara excepciones de error, los cuales se deben capturar
	$emailPara = $d_correo;
	$name = $d_nombre;
	$subject ='Sistema Automatizado de Contraloria Sanitaria SIACS';
	$mail->IsSMTP(); // Indicar a la Clase que use SMTP 
	$error=0;
	
    try {
         


//$mail->SMTPAuth = true;
//$mail->SMTPSecure = "ssl";
//$mail->Host = "smtp.gmail.com";
//$mail->Port = 465;


//$mail->Host       = "mailserver.mpps.gob.ve"; // SMTP server
         $mail->SMTPDebug  = 0;                     // Habilita el Depurador SMTP (testing)
         $mail->SMTPAuth   = true;                  // Habilita la Autenticacion SMTP
         //$mail->Host       = "mailserver.mpps.gob.ve"; // Establece the SMTP server
         //$mail->Host       = "10.29.65.22"; // Establece the SMTP server
         //$mail->Host       = "smtp.gmail.com"; // Establece the SMTP server
         
		 
		 
		 $mail->Host       = "10.29.65.22"; // ERRRRRRRRRRRRRRRRRRRRRR
		 
		 
		 
		 
         $mail->Port       = 25;                    // Establece el puerto SMTP port para  Servidor GMAIL
 //$mail->Port = 465;
	  	$mail->Username   = "egarcia@mpps.gob.ve";//"informaticasacs@gmail.com";//"webmastersacs@mpps.gob.ve"; // Cuenta de Usuario SMTP

         $mail->Password   = "glej1504";//"sacs_informatica";//"sacs123";        // Password de la Cuenta SMTP
      //******
         $mail->AddReplyTo('egarcia@mpps.gob.ve', 'webmaster');
         $mail->AddAddress($emailPara, $name);
         $mail->SetFrom($emailPara, $name);
         $mail->Subject = $subject;
		 $mail->sender ='SACS';
      //**
         $mail->MsgHTML($mensaje);
         $mail->Send();
      //**
        }catch (phpmailerException $e) {
			$error = 2;
          //return "$d_correo, ".$e->errorMessage(); //Envia messages de error desde PHPMailer
        }catch (Exception $e) {
			$error = 1;
          //return "$d_correo, ".$e->getMessage(); //Mensajes de Errores Aburridos desde otro!
		}  
		//*****************
	  //****Fin del Envio de correo
	

	
	return $error;


}// end function

function gen_id($prefijo){
	return uniqid($prefijo);

}// end function


?>