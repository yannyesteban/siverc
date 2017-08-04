<?php
include("../../bd_constante.php");

define ("C_MODULO_PRINCIPAL","geoportal");
define ("C_EST_DEFAULT","");
define ("C_SG_USUARIO","user");
define ("C_SG_CLAVE","123");

define ("C_PATH","../../../sigefor_postgres/");
define ("C_PATH_CONFIGURACION","../../../sigefor_postgres/");
define ("C_PATH_IMAGENES","../../imagenes/");
define ("C_PATH_CSS","css/");

define ("C_PATH_PLANTILLAS","../../plantillas/");
define ("C_PATH_ARCHIVOS","../../archivos/");
define ("C_PATH_GRAFICO","../jpgraph/");
define ("C_PATH_REPORTES","reportes/");
define ("C_PANEL_DEFAULT","4");
define ("C_PANEL_DEBUG","8");
//<link rel="stylesheet" type="text/css" href="leaflet/leaflet2.css">

define ("C_PLANTILLA_DEF","0");//0=Default, 1=Diagrama, 2=Archivo

define ("C_TEMA_DEFAULT","primavera");
define ("C_CLASE_DEFAULT","sigefor");

define("C_METODO","POST");//GET,POST,''

define ("C_HOJA_CSS",
		"../../css/sigefor1.css,
		../../css/geoportal.css,
		../../../geo/leaflet/leaflet2.css,
		
		../../css/cluster.css,
		
	
		
		");
define ("C_JAVASCRIPT",
		"../../../geo/js/jquery-3.1.1.js,
		../../../geo/leaflet/leaflet-src.js,
		
		
		../../../geo/Leaflet.markercluster-master/src/MarkerCluster.js,

		../../../geo/Leaflet.markercluster-master/src/MarkerClusterGroup.js,
		../../../geo/Leaflet.markercluster-master/src/MarkerClusterGroup.Refresh.js,
		../../../geo/Leaflet.markercluster-master/src/MarkerCluster.Spiderfier.js,
		../../../geo/Leaflet.markercluster-master/src/MarkerCluster.QuickHull.js,
		../../../geo/Leaflet.markercluster-master/src/MarkerOpacity.js,
		../../../geo/Leaflet.MarkerCluster.Freezable-master/src/freezable.js,
		../../../geo/Leaflet.markercluster-master/src/DistanceGrid.js,



	
		
		
		../../../geo/js/geoTabs.js,
		../../js/_wcMaps.js,
		../../js/geoportal.js");
?>
