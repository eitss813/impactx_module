<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: all-location.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="generic_layout_container layout_middle">
<div class="generic_layout_container layout_core_content">
<?php
	include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl';
?>
		<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<script type="text/javascript">
  function smallLargeMap(option, location_id) {
		if(option == '1') {
		  $('map_canvas_sitepage_browse_'+ location_id).setStyle("height",'300px');
		  $('map_canvas_sitepage_browse_' + location_id).setStyle("width",'550px');
			$('sitepage_location_fields_map_wrapper_' + location_id).className='sitepage_location_fields_map_wrapper fright seaocore_map map_wrapper_extend';	
			$('map_canvas_sitepage_browse_' + location_id).className='sitepage_location_fields_map_canvas map_extend';	
			document.getElementById("largemap_" + location_id).style.display = "none";
			document.getElementById("smallmap_" + location_id).style.display = "block";
		} else {
			  $('map_canvas_sitepage_browse_'+ location_id).setStyle("height",'200px');
		  $('map_canvas_sitepage_browse_' + location_id).setStyle("width",'200px');
			$('sitepage_location_fields_map_wrapper_' + location_id).className='sitepage_location_fields_map_wrapper fright seaocore_map';
			$('map_canvas_sitepage_browse_' + location_id).className='sitepage_location_fields_map_canvas';	
			document.getElementById("largemap_" + location_id ).style.display = "block";
			document.getElementById("smallmap_" + location_id).style.display = "none";	
		}
		//setMapContent();
	//	google.maps.event.trigger(map, 'resize');
	}
</script>
<script type="text/javascript">
  var myLatlng;
  function initialize(latitude, longitude, location_id) {
    var myLatlng = new google.maps.LatLng(latitude,longitude);
    var myOptions = {
      zoom: 10,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    var map = new google.maps.Map(document.getElementById("map_canvas_sitepage_browse_"+location_id), myOptions);

    var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      title: "<?php echo str_replace('"', ' ',$this->sitepage->getTitle())?>"
    });

    <?php if(!empty($this->showtoptitle)):?>
      $$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function() {
      google.maps.event.trigger(map, 'resize');
      map.setZoom(10);
      map.setCenter(myLatlng);
    });
    <?php else:?>
      $$('.tab_layout_sitepage_location_sitepage').addEvent('click', function() {
      google.maps.event.trigger(map, 'resize');
      map.setZoom(10);
      map.setCenter(myLatlng);
    });
    <?php endif;?>

    document.getElementById("largemap_" + location_id).addEvent('click', function() {
        smallLargeMap(1,location_id);
				google.maps.event.trigger(map, 'resize');
				map.setZoom(10);
				map.setCenter(myLatlng);
			});
      document.getElementById("smallmap_" + location_id).addEvent('click', function() {
         smallLargeMap(0,location_id);
				google.maps.event.trigger(map, 'resize');
				map.setZoom(10);
				map.setCenter(myLatlng);
			});
      
    google.maps.event.addListener(map, 'click', function() {
			google.maps.event.trigger(map, 'resize');
      map.setZoom(10);
      map.setCenter(myLatlng);
    });
  }
</script>

<?php if(!empty($this->mainLocationObject)) : ?>
	<script type="text/javascript">
	en4.core.runonce.add(function() {
		window.addEvent('domready',function(){
			initialize('<?php echo $this->mainLocationObject->latitude ?>','<?php echo $this->mainLocationObject->longitude ?>','<?php echo $this->mainLocationObject->location_id ?>');
		});
	});
	</script>
<?php endif; ?>

<?php foreach ($this->location as $item):  ?>
	<script type="text/javascript">
		window.addEvent('domready',function(){
			initialize('<?php echo $item->latitude ?>','<?php echo $item->longitude ?>','<?php echo $item->location_id ?>');
		});
	</script>
<?php endforeach; ?>

<script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href ;
		Smoothbox.open(Obj_Url);
	}
</script>

<div class='layout_middle'>
	<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
  <div class="sitepage_edit_content">
		<div class="sitepage_edit_header">
			<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepage->page_id, $this->sitepage->owner_id, $this->sitepage->getSlug()),$this->translate('VIEW_PAGE')) ?>
			<?php if($this->sitepage->draft == 0 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0)) echo $this->htmlLink(array('route' => 'sitepage_publish', 'page_id' => $this->sitepage->page_id), $this->translate('Mark As Live'), array('class'=>'smoothbox')) ?>
			<h3><?php echo $this->translate('Dashboard: ').$this->sitepage->title; ?></h3>
		</div>

		<div id="show_tab_content">
			<div class="sitepage_editlocation_wrapper">
				<?php //if (!empty($this->location)): ?>
				<?php if (count($this->location) > 0 || !empty($this->sitepage->location)) : ?>
					<h4><?php echo $this->translate('Manage Multiple Locations') ?></h4>
					<p><?php echo $this->translate('Below, you can manage multiple locations for your page. You can add a new location and delete any existing one.') ?></p>
					<?php //if (count($this->location) > 0) : ?>
						<div class='clr'>	<br />
							<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->sitepage->page_id, 'action' => 'add-location'), $this->translate('Add Location'), array('class' => 'smoothbox icon_sitepages_map_add buttonlink')); ?>
						</div>
					<?php //endif; ?>
	<?php if (!empty($this->sitepage->location) && $this->mainLocationObject) : ?>
		<div class='profile_fields sitepage_location_fields sitepage_list_highlight'>
				<div class="sitepage_location_fields_head">
					<?php if (!empty($this->mainLocationObject->locationname)) : ?>
						<?php echo $this->mainLocationObject->locationname ?>
					<?php else: ?>
						<?php echo $this->translate('Main Location'); ?>
					<?php endif; ?>
				</div>
				<div class="sitepage_location_fields_map_wrapper fright seaocore_map"  id="sitepage_location_fields_map_wrapper_<?php echo $this->mainLocationObject->location_id ?>">
					<div class="sitepage_location_fields_map b_dark">
											<div class="sitepage_map_container_topbar b_dark" id='sitepage_map_container_topbar' >
							<a id="largemap_<?php echo $this->mainLocationObject->location_id ?>" href="javascript:void(0);" class="bold fright">&laquo; <?php echo $this->translate('Large Map'); ?></a>
							<a id="smallmap_<?php echo $this->mainLocationObject->location_id ?>" href="javascript:void(0);" class="bold fright" style="display:none"><?php echo $this->translate('Small Map'); ?> &raquo;</a>
						</div>
						<div class="sitepage_location_fields_map_canvas" id="map_canvas_sitepage_browse_<?php echo $this->mainLocationObject->location_id ?>" style="width:200px;"></div>
					</div>
				</div>
				<ul class="sitepage_location_fields_details">
					<li>
						<span><?php echo $this->translate('Location:'); ?> </span>
						<span><b><?php echo  $this->mainLocationObject->location; ?></b> - <span class="location_get_direction"><b>
							<?php if (!empty($this->mobile)) : ?>
							<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->mainLocationObject->page_id, 'resouce_type' => 'sitepage_page', 'location_id' => $this->mainLocationObject->location_id, 'is_mobile' => $this->mobile, 'flag' => 'map'), $this->translate("Get Directions"), array('target' => '_blank')) ; ?>
							<?php else: ?>
							<?php if($this->mainLocationObject)
              echo  $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $this->mainLocationObject->page_id, 'resouce_type' => 'sitepage_page', 'location_id' => $this->mainLocationObject->location_id, 'flag' => 'map'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')) ; ?>
							<?php endif; ?>
							</b></span>
						</span>
					</li>
					<?php if(!empty($this->mainLocationObject->formatted_address)):?>
						<li>
							<span><?php echo $this->translate('Formatted Address:'); ?> </span>
							<span><?php echo $this->mainLocationObject->formatted_address; ?> </span>
						</li>
					<?php endif; ?>
					<?php if(!empty($this->mainLocationObject->address)):?>
						<li>
							<span><?php echo $this->translate('Street Address:'); ?> </span>
							<span><?php echo $this->mainLocationObject->address; ?> </span>
						</li>
					<?php endif; ?>
					<?php if(!empty($this->mainLocationObject->city)):?>
						<li>
							<span><?php echo $this->translate('City:'); ?></span>
							<span><?php echo $this->mainLocationObject->city; ?> </span>
						</li>
					<?php endif; ?>
					<?php if(!empty($this->mainLocationObject->zipcode)):?>
						<li>
							<span><?php echo $this->translate('Zipcode:'); ?></span>
							<span><?php echo $this->mainLocationObject->zipcode; ?> </span>
						</li>
					<?php endif; ?>
					<?php if(!empty($this->mainLocationObject->state)):?>
						<li>
							<span><?php echo $this->translate('State:'); ?></span>
							<span><?php echo $this->mainLocationObject->state; ?></span>
						</li>
					<?php endif; ?>
					<?php if(!empty($this->mainLocationObject->country)):?>
						<li>
							<span><?php echo $this->translate('Country:'); ?></span>
							<span><?php echo $this->mainLocationObject->country; ?></span>
						</li>
					<?php endif; ?>
						<li class="sitepage_location_fields_option clr">
							<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->sitepage->page_id, 'location_id' => $this->mainLocationObject->location_id, 'action' => 'edit-location'), $this->translate('Edit Location'), array('class' => 'icon_sitepages_map_edit buttonlink')); ?>
							<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->sitepage->page_id, 'location_id' => $this->mainLocationObject->location_id, 'action' => 'delete-location'), $this->translate('Delete'), array('class' => 'smoothbox icon_sitepages_map_delete buttonlink')); ?>
						</li>
				</ul>
			<div class="clr"></div>
			</div>	
		<?php endif; ?>
					
					
					
					

					<?php foreach ($this->location as $item): ?>
						<div class='profile_fields sitepage_location_fields'>
							<h4>
								<span>
								<?php if (!empty($item->locationname)) : ?>
									<?php echo $item->locationname ?>
								<?php else: ?>
									<?php echo $this->translate('Location'); ?>
								<?php endif; ?>
								</span>
							</h4>
							
							<div class="sitepage_location_fields_map_wrapper fright seaocore_map"  id="sitepage_location_fields_map_wrapper_<?php echo $item->location_id ?>">
								<div class="sitepage_location_fields_map b_dark">
														<div class="sitepage_map_container_topbar b_dark" id='sitepage_map_container_topbar' >
							<a id="largemap_<?php echo $item->location_id ?>" href="javascript:void(0);"  class="bold fright">&laquo; <?php echo $this->translate('Large Map'); ?></a>
							<a id="smallmap_<?php echo $item->location_id ?>" href="javascript:void(0);" class="bold fright" style="display:none"><?php echo $this->translate('Small Map'); ?> &raquo;</a>
						</div>
									<div class="sitepage_location_fields_map_canvas" id="map_canvas_sitepage_browse_<?php echo $item->location_id ?>"  style="width:200px;"></div>
								</div>
							</div>
							
							<ul class="sitepage_location_fields_details">
								<li>
									<span><?php echo $this->translate('Location:'); ?> </span>
									<span><b><?php echo  $item->location; ?></b> - <span class="location_get_direction"><b>
										<?php if (!empty($this->mobile)) : ?>
										<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $item->page_id, 'resouce_type' => 'sitepage_page', 'location_id' => $item->location_id, 'is_mobile' => $this->mobile, 'flag' => 'map'), $this->translate("Get Directions"), array('target' => '_blank')) ; ?>
										<?php else: ?>
										<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $item->page_id, 'resouce_type' => 'sitepage_page', 'location_id' => $item->location_id, 'flag' => 'map'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')) ; ?>
										<?php endif; ?>
										</b></span>
									</span>
								</li>
								<?php if(!empty($item->formatted_address)):?>
									<li>
										<span><?php echo $this->translate('Formatted Address:'); ?> </span>
										<span><?php echo $item->formatted_address; ?> </span>
									</li>
								<?php endif; ?>
								<?php if(!empty($item->address)):?>
									<li>
										<span><?php echo $this->translate('Street Address:'); ?> </span>
										<span><?php echo $item->address; ?> </span>
									</li>
								<?php endif; ?>
								<?php if(!empty($item->city)):?>
									<li>
										<span><?php echo $this->translate('City:'); ?></span>
										<span><?php echo $item->city; ?> </span>
									</li>
								<?php endif; ?>
								<?php if(!empty($item->zipcode)):?>
									<li>
										<span><?php echo $this->translate('Zipcode:'); ?></span>
										<span><?php echo $item->zipcode; ?> </span>
									</li>
								<?php endif; ?>
								<?php if(!empty($item->state)):?>
									<li>
										<span><?php echo $this->translate('State:'); ?></span>
										<span><?php echo $item->state; ?></span>
									</li>
								<?php endif; ?>
								<?php if(!empty($item->country)):?>
									<li>
										<span><?php echo $this->translate('Country:'); ?></span>
										<span><?php echo $item->country; ?></span>
									</li>
								<?php endif; ?>
								<li class="sitepage_location_fields_option clr">
									<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->sitepage->page_id, 'location_id' => $item->location_id, 'action' => 'edit-location'), $this->translate('Edit Location'), array('class' => 'icon_sitepages_map_edit buttonlink')); ?>
									<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->sitepage->page_id, 'location_id' => $item->location_id, 'action' => 'delete-location'), $this->translate('Delete'), array('class' => 'smoothbox icon_sitepages_map_delete buttonlink')); ?>
  								
								</li>
							</ul>
						</div>	
					<?php endforeach; ?>
				<?php //endif; ?>
				<?php else: ?>
					<div class="tip">
						<span>
						<?php echo $this->translate('You have not added a location for your page. Click'); ?>
							<a  onclick="javascript:Smoothbox.open('<?php echo Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'add-location', 'page_id' => $this->sitepage->page_id), 'sitepage_dashboard', true) ?>');" href="javascript:void(0);"><?php echo $this->translate('here'); ?></a>
							<?php echo $this->translate('to add it.'); ?>
						</span>
					</div>
				<?php endif; ?>
				<?php echo $this->paginationControl($this->paginator, null, null, array('pageAsQuery' => true, 'query' => $this->formValues,
				//'params' => $this->formValues,
				)); ?>
			</div>
		</div>
  </div>
    </div>
  </div>