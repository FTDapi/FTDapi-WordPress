<div class="wrap">
    <h2><?php echo __('FTDapi for WordPress', 'ftdapiwordpress'); ?></h2>
    <form method="post" action="options.php"> 
        <?php @settings_fields('ftdapi_for_wordpress-group'); ?>
        <?php @do_settings_fields('ftdapi_for_wordpress-group'); ?>
		
		<h3 class="title"><?php echo __('General', 'ftdapiwordpress'); ?></h3>
		
		<p><?php echo __('Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut', 'ftdapiwordpress'); ?></p>
		
        <table class="form-table">  
            <tr valign="top">
                <th scope="row"><label for="ftdapi_token"><?php echo __('API Token', 'ftdapiwordpress'); ?></label></th>
                <td>
                	<input class="regular-text" type="text" name="ftdapi_token" id="ftdapi_token" value="<?php echo get_option('ftdapi_token'); ?>" />
                	<p class="description"><?php echo __('Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam', 'ftdapiwordpress'); ?></p>
                </td>
            </tr>
            <?php if(get_option('ftdapi_cacheable') == 1): ?>
                <tr valign="top">
	                <th scope="row"><label for="ftdapi_cache"><?php echo __('Cache Duration', 'ftdapiwordpress'); ?></label></th>
	                <td>
	                	<input class="regular-text" type="text" name="ftdapi_cache" id="ftdapi_cache" value="<?php echo get_option('ftdapi_cache', '360'); ?>" />
	                	<p class="description"><?php echo __('Angebe in Minuten Lorem ipsum dolor sit amet, consetetur sadipscing elitr', 'ftdapiwordpress'); ?></p>
	                </td>
	            </tr>
            <?php endif; ?>	
        </table>
        
        <h3 class="title"><?php echo __('Default', 'ftdapiwordpress'); ?></h3>
        
        <p><?php echo __('Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor', 'ftdapiwordpress'); ?></p>
        
        <table class="form-table">  
        	<tr valign="top">
                <th scope="row"><label for="ftdapi_time_interval"><?php echo __('Time Interval', 'ftdapiwordpress'); ?></label></th>
                <td>
                	<select id="ftdapi_time_interval" name="ftdapi_time_interval">
				   		<option value="today" <?php selected(get_option('ftdapi_time_interval', 'today'), 'today'); ?>><?php echo __('Today', 'ftdapiwordpress'); ?></option>
				      	<option value="tomorrow" <?php selected(get_option('ftdapi_time_interval', 'today'), 'tomorrow'); ?>><?php echo __('Tomorrow', 'ftdapiwordpress'); ?></option>
				      	<option value="twodays" <?php selected(get_option('ftdapi_time_interval', 'today'), 'twodays'); ?>><?php echo __('2 Days', 'ftdapiwordpress'); ?></option>
				     	<option value="week" <?php selected(get_option('ftdapi_time_interval', 'today'), 'week'); ?>><?php echo __('1 Week', 'ftdapiwordpress'); ?></option>
				    </select>
				    <p class="description"><?php echo __('Lorem ipsum dolor sit amet, consetetur sadipscing', 'ftdapiwordpress'); ?></p>
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row"><label for="ftdapi_selection"><?php echo __('Selection', 'ftdapiwordpress'); ?></label></th>
                <td>
                	<select id="ftdapi_selection" name="ftdapi_selection">
				   		<option value="region" <?php selected(get_option('ftdapi_selection', 'region'), 'region'); ?>><?php echo __('Region', 'ftdapiwordpress'); ?></option>
				      	<option value="provider" <?php selected(get_option('ftdapi_selection', 'region'), 'provider'); ?>><?php echo __('Provider', 'ftdapiwordpress'); ?></option>
				      	<option value="truck" <?php selected(get_option('ftdapi_selection', 'region'), 'truck'); ?>><?php echo __('Truck', 'ftdapiwordpress'); ?></option>
				    </select>
				    <p class="description"><?php echo __('Lorem ipsum dolor sit amet, consetetur sadipscing', 'ftdapiwordpress'); ?></p>
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row"><label for="ftdapi_foodtruck_id"><?php echo __('Foodtruck ID', 'ftdapiwordpress'); ?></label></th>
                <td>
                	<input class="regular-text" type="text" name="ftdapi_foodtruck_id" id="ftdapi_foodtruck_id" value="<?php echo get_option('ftdapi_foodtruck_id'); ?>" />
                	<p class="description"><?php echo __('Lorem ipsum dolor sit amet, consetetur sadipscing', 'ftdapiwordpress'); ?></p>
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row"><?php echo __('Map', 'ftdapiwordpress'); ?></th>
                <td>
                	<label for="ftdapi_show_map"><input type="checkbox" name="ftdapi_show_map" id="ftdapi_show_map" value="1" <?php checked(get_option('ftdapi_show_map', '0')); ?> /> <?php echo __('Show Map', 'ftdapiwordpress'); ?></label>
                	<p class="description"><?php echo __('Lorem ipsum dolor sit amet, consetetur sadipscing', 'ftdapiwordpress'); ?></p>
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row"><label for="ftdapi_token"><?php echo __('Map height', 'ftdapiwordpress'); ?></label></th>
                <td>
                	<input class="regular-text" type="text" name="ftdapi_map_height" id="ftdapi_map_height" value="<?php echo get_option('ftdapi_map_height', '350'); ?>" />
                	<p class="description"><?php echo __('Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam', 'ftdapiwordpress'); ?></p>
                </td>
            </tr>
        </table>

        <?php @submit_button(); ?>
    </form>
</div>