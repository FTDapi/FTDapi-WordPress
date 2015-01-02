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
wp_enqueue_script('ftdapi-map');
?>
<script type="text/javascript">
	var FTDapiMapData = <?php echo json_encode($request); ?>;
	var FTDapiLanguage = <?php echo json_encode(array('on' => __('on', 'ftdapiwordpress'), 'from' => __('from', 'ftdapiwordpress'), 'to' => __('to', 'ftdapiwordpress'))); ?>;
</script>
<div class="ftd-container">
	<div id="map" style="height:<?php echo $atts['map_height']; ?>px;"></div>
</div>