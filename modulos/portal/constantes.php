<?php
include("../../bd_constante.php");

define ("C_MODULO_PRINCIPAL","geoportal");
define ("C_EST_DEFAULT","");
define ("C_SG_USUARIO","user");
define ("C_SG_CLAVE","123");

define ("C_PATH","../../../sigefor2017/");
define ("C_PATH_SEVIAN","../../../sevian/");
define ("C_PATH_CONFIGURACION","../../../sigefor2017/");
define ("C_PATH_IMAGENES","../../imagenes/");
define ("C_PATH_CSS","../../css/");
define ("C_PATH_PLANTILLAS","plantillas/");
define ("C_PATH_ARCHIVOS","archivos/");
define ("C_PATH_GRAFICO","../../sigefor20/pchart/pChart/");
define ("C_PATH_REPORTES","");
define ("C_PANEL_DEFAULT","4");
define ("C_PANEL_DEBUG","8");

$PATH = C_PATH;
$PATH_SEVIAN = C_PATH_SEVIAN;
$GEO = "../../_geo/";

define("C_HOJA_CSS",
		"
	../../css/dem_2017.css,
	../../css/sigefor11.css,
	../../css/geoportal1.css,
	{$GEO}leaflet/leaflet2.css,

	../../css/cluster.css,

	{$PATH_SEVIAN}css/sgMenu_.css,
	{$PATH_SEVIAN}css/sgWindow.css,
	{$PATH_SEVIAN}css/sgCalendar.css,
	{$PATH_SEVIAN}css/selectText.css,
	{$PATH_SEVIAN}css/sgTab.css,
	{$PATH_SEVIAN}css/sgAjax.css,
	{$PATH}css/query.css,
		
	");

define("C_JAVASCRIPT",	"
	{$PATH_SEVIAN}_js/_sgQuery.js,
	{$PATH_SEVIAN}js/sgAjax.js,
	{$PATH_SEVIAN}js/drag.js,
	{$PATH_SEVIAN}js/sgWindow.js,
	{$PATH_SEVIAN}js/sgMenu.js,
	{$PATH_SEVIAN}js/sgCalendar.js,
	{$PATH_SEVIAN}js/selectText.js,
	{$PATH_SEVIAN}js/sgTab.js,
	{$PATH}js/sgTipsPopup.js,
	{$PATH}js/datePicker.js,
	{$PATH}js/sgPanel.js,
		
	{$GEO}js/jquery-3.1.1.js,
	{$GEO}leaflet/leaflet-src.js,
	{$GEO}Leaflet.markercluster-master/src/MarkerCluster.js,
	{$GEO}Leaflet.markercluster-master/src/MarkerClusterGroup.js,
	{$GEO}Leaflet.markercluster-master/src/MarkerClusterGroup.Refresh.js,
	{$GEO}Leaflet.markercluster-master/src/MarkerCluster.Spiderfier.js,
	{$GEO}Leaflet.markercluster-master/src/MarkerCluster.QuickHull.js,
	{$GEO}Leaflet.markercluster-master/src/MarkerOpacity.js,
	{$GEO}Leaflet.MarkerCluster.Freezable-master/src/freezable.js,
	{$GEO}Leaflet.markercluster-master/src/DistanceGrid.js,
	{$GEO}js/geoTabs.js,
	../../js/_wcMaps.js,
	../../js/geoportal.js");

define ("C_PLANTILLA_DEF","0");//0=Default, 1=Diagrama, 2=Archivo
define ("C_TEMA_DEFAULT","primavera");
define ("C_CLASE_DEFAULT","sigefor");
define("C_METODO","POST");//GET,POST,''
?>