<?php
require_once ("../../sigefor_postgres/clases/cls_catalogo.php");
require_once '../clienteRest/CommunController.php';


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
	$subject ='Sistema Automatizado de Contraloria Sanitaria SIACS';
	
	
	$commun = new CommunController();
	
	$apiRest='http://sacs.mpps.gob.ve/api-sacs/main.php'; // RUTA TENTATIVA
	$dataBasic = array(
		'tocken' => 'cl13nt3_s4cs',
		'secret' => 'cl13nt3_s4cs',
		'method' => 'POST'
		);
	$system_id=3; // Sistema de Titulos Siempre
	
	$dataPost = array(
		'system_id'=> $system_id,
		'usuario_system_id'=>'10',
		'entidad'=>'NombreTabla',
		'registro_id'=>'10', 
		'correo_destinos'=>$d_correo,
		'nombre_destinos'=>$d_name,
		'asunto'=>$subject,
		'texto_alternativo'=>'Sistema Automatizado de Contraloria Sanitaria SIACS',
		'contenido_mensaje'=>utf8_encode(trim($mensaje))
	);
	// Fucionar las rutas para poder enviarla como config al apiRest
	$dataBasic = array_merge(array('apRest' => $apiRest.'/notificaciones/sendCorreo'), $dataBasic);
	// Consultar api rest encargado de gestionar con la capa de base de datos
	$dataProcesada = $commun->ClientRestBase($dataBasic, $dataPost);  
	

	$msg = json_decode($dataProcesada);

	
	return $msg->error;


}// end function

function gen_id($prefijo){
	return uniqid($prefijo);

}// end function


?>