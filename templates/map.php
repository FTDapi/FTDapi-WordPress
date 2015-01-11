<?php
// FTDAPI FOR WORDPRESS
// TEMPLATE FOR MAP

// REGISTER JAVASCRIPT AND CSS
wp_enqueue_style('ftdapi-leaflet');
wp_enqueue_style('ftdapi-leaflet-markercluster');
wp_enqueue_style('ftdapi-leaflet-markercluster-default');

wp_enqueue_script('jquery');
wp_enqueue_script('ftdapi-leaflet');
wp_enqueue_script('ftdapi-leaflet-markercluster');

if($atts['map_dataprovider'] == 'google'):
	wp_enqueue_script('ftdapi-googlemaps');
	wp_enqueue_script('ftdapi-leaflet-google');
endif;

wp_enqueue_script('ftdapi-map');

$mapID = wp_generate_password(5, false);

$dataArray = array(
	'mapID' => $mapID,
	'dataProvider' => $atts['map_dataprovider'],
	'language' => array('on' => __('on', 'ftdapiwordpress'), 'from' => __('from', 'ftdapiwordpress'), 'to' => __('to', 'ftdapiwordpress')),
	'data' => $request
);
?>
<script type="text/javascript">
	var FTDapiMap = FTDapiMap || [];
	
	FTDapiMap.push(<?php echo json_encode($dataArray); ?>); 
</script>
<div class="ftd-container">
	<div id="map_<?php echo $mapID; ?>" style="height:<?php echo $atts['map_height']; ?>px;"></div>
</div>