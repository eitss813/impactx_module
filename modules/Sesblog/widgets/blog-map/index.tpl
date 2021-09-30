<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: index.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<script type="text/javascript">
	var latLngMap;
	function initializeItemMap() {
	
		var latLngMap = new google.maps.LatLng(<?php echo $this->locationLatLng->lat; ?>, <?php echo $this->locationLatLng->lng; ?>);
		
		var myOptions = {
			zoom: 13,
			center: latLngMap,
			navigationControl: true,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		
		var map = new google.maps.Map(document.getElementById("sesblog_map_container"), myOptions);
		var marker = new google.maps.Marker({
			position: latLngMap,
			map: map,
		});
		
		//trigger map resize on every call
		sesJqueryObject(document).on('click','ul#main_tabs li.tab_layout_sesblog_blog_map',function (event) {
			google.maps.event.trigger(map, 'resize');
			map.setZoom(13);
			map.setCenter(latLngMap);
		});
		
		google.maps.event.addListener(map, 'click', function() {
			google.maps.event.trigger(map, 'resize');
			map.setZoom(13);
			map.setCenter(latLngMap);
		});
	}
	
	var tabId_map = <?php echo $this->identity; ?>;
  window.addEvent('domready', function() {
		tabContainerHrefSesbasic(tabId_map);	
	});
	window.addEvent('domready', function() {
		initializeItemMap();
	});
</script>
<div class="sesblog_profile_map_container sesbasic_clearfix">
	<div class="sesblog_profile_map sesbasic_clearfix sesbd" id="sesblog_map_container"></div>
	<div class="sesblog_profile_map_address_box sesbasic_bxs">
		<b><a href="<?php echo $this->url(array('resource_id' => $this->subject->blog_id,'resource_type'=>'sesblog_blog','action'=>'get-direction'), 'sesbasic_get_direction', true); ?>" class="openSmoothbox"><?php echo $this->subject->location ?></a></b>
	</div>
</div>
