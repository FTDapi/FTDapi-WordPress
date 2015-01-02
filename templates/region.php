<?php
// FTDAPI FOR WORDPRESS
// TEMPLATE FOR REGION

// REGISTER JAVASCRIPT AND CSS
wp_enqueue_style('ftdapi-base');
?>
<div class="ftd-container">
	<h3 class="ftd-headline"><?php echo __('Current tour dates', 'ftdapiwordpress'); ?></h3>
	<div class="ftd-items">
		 <?php foreach ($request as $item) : ?>
		 <div class="ftd-item media">
		 <div class="media-body">
		 <p>
		 <?php if ($atts['time_interval'] == 'today') : ?>
			 <span><?php echo __('Today', 'ftdapiwordpress'); ?></span>
		<?php endif; ?>
		 <?php echo $item['truck']; ?> / <strong><?php echo date(__('H:i', 'ftdapiwordpress'), $item['startTime']); ?> <?php echo __('to', 'ftdapiwordpress'); ?> <?php echo date(__('H:i', 'ftdapiwordpress'), $item['endTime']); ?>
		 <a href="http://maps.google.com/maps?q=<?php echo $item['map']['latitude']; ?>,<?php echo $item['map']['longitude']; ?>" target="_blank"><?php echo $item['locationname']; ?> (<?php echo $item['sponsorname']; ?>)</a></strong>
		 </p>
		 <p><?php echo date(__('l', 'ftdapiwordpress'), $item['startTime']); ?> <?php echo date(__('d.m.Y', 'ftdapiwordpress'), $item['startTime']); ?> - <?php echo $item['address']['full']; ?></p>
		 </div>
		 </div>
		 <?php endforeach; ?>
	</div>
</div>
<?php
if($atts['show_map'] == 1):
	include ($this -> getTemplateFile('map'));
endif;	
?>
<div class="ftd-cowork"><?php echo __('In cooperation with', 'ftdapiwordpress') ?> <img class="ftd-logo" src="http://www.nuernberg-und-so.de/assets/images/logo-food-trucks-in-deutschland.png" alt="Logo Food Trucks in Deutschland"/></div>