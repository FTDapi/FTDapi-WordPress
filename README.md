# FTDapi for WordPress

To integrate the FTDapi in your pages/posts or widgets you just have to use the following shortcode.

##Available Shortcode

- The easiest ftdapi call looks like this: `[ftd-api]`
 - This ftdapi call uses the default settings of the plug-ins that can be adapted to the general options page: `yoursiteurl/wp-admin/options-general.php?page=ftdapi_for_wordpress`

###All available parameters
- token: yourtoken
- cache: integer
- time_interval: today, tomorrow, twodays, week
- selection: region, provider, truck
- foodtruck_id: yourftid
- show_map: 1 or 0
- map_dataprovider: openstreetmap, google
- map_height: integer

###Let's play with paramaters

- By setting appropriate parameters, the default value will be overwritten
 - Get a specific FTD provider your call looks like: `[ftd-api token="yourtoken" selection="provider"]` 
 - Get a specific Truck your call should look like: `[ftd-api selection="truck" foodtruck_id="NEO2"]`

##The options page
- `yoursiteurl/wp-admin/options-general.php?page=ftdapi_for_wordpress`

###General
- API Token: yourtoken
- Cache time: value in minutes

###Standard
- period of time: today, tomorrow, 2 days, 1 week
- layout: region, provider, truck
- Food Truck ID: yourftid
- Map: yes/no
- Map provider: openstreetmap, google maps
- height of the map: value in px


> Food Trucks Germany (FTD) is a project by Klaus P. Wünsch together with Markus Wolf.
The project FTDapi of the tec-promotion GmbH has no legal connection to FTD. Based on the available API provided by FTD all connected Food Trucks can be displayed with the FTDapi plugins for Joomla !, WordPress, TYPO3 CMS and not based on PHP based websites.

Find more informations and a WordPress demo site here [wordpress.ftdapi.de](http://wordpress.ftdapi.de)

Get your token by mail token@ftdapi.de
