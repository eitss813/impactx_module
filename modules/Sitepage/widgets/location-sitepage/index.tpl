<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
	$siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
  $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/styles/sitepage-tooltip.css');
  $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1);
  $sitepage = $this->sitepage;  
  if($postedBy && $sitepage->show_name) {
		$textPostedBy = $this->string()->escapeJavascript($this->translate('posted by'));
		$textPostedBy.= " " . $this->htmlLink($sitepage->getOwner()->getHref(), $this->string()->escapeJavascript($sitepage->getOwner()->getTitle()));
	}
?>
<script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href ;
		Smoothbox.open(Obj_Url);
	}
</script>

<?php if (!empty($this->multiple_location)) : ?>
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
		var current_page = '<?php echo $this->current_page; ?>';
		var paginatePageLocations = function(page) {
			document.getElementById('page_location_loding_image').style.display ='';
			var url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/location-sitepage';
			en4.core.request.send(new Request.HTML({
				'url' : url,
				'data' : {
					'format' : 'html',
					'is_ajax' : 1,
					'subject' : en4.core.subject.guid,
					'page' : page
				},
				onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
					document.getElementById('page_location_loding_image').style.display ='none';
					document.getElementById('sitepage_location_map_anchor').getParent().innerHTML = responseHTML;
					en4.core.runonce.trigger();
				}
			}));
		}

		var pageAction = function(page) {
			paginatePageLocations(page);
		}
	</script>

	<?php if (empty($this->is_ajax)) : ?>
		<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
	<?php endif; ?>

	<script type="text/javascript">
		var myLatlng;
		function initialize(latitude, longitude, location_id) {
			var myLatlng = new google.maps.LatLng(latitude,longitude);
		
			var myOptions = {
				zoom: 10,
				center: myLatlng,
			//  navigationControl: true,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}

			var map = new google.maps.Map(document.getElementById("map_canvas_sitepage_browse_"+location_id), myOptions);

			var marker = new google.maps.Marker({
				position: myLatlng,
				map: map,
				title: "<?php echo str_replace('"', ' ',$sitepage->getTitle())?>"
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
				//infowindow.close();
				google.maps.event.trigger(map, 'resize');
				map.setZoom(10);
				map.setCenter(myLatlng);
			});
		}
	</script>
		
	<?php foreach ($this->location as $item):  ?>
		<script type="text/javascript">
			en4.core.runonce.add(function() {
				window.addEvent('domready',function(){
					initialize('<?php echo $item->latitude ?>','<?php echo $item->longitude ?>','<?php echo $item->location_id ?>');
					});
			});
		</script>
	<?php endforeach; ?>
	
	<?php if(!empty($this->MainLocationObject)) : ?>
		<script type="text/javascript">
			en4.core.runonce.add(function() {
				window.addEvent('domready',function(){
					initialize('<?php echo $this->MainLocationObject->latitude ?>','<?php echo $this->MainLocationObject->longitude ?>','<?php echo $this->MainLocationObject->location_id ?>');
				});
			});
		</script>
	<?php endif; ?>

		
		<a id="sitepage_location_map_anchor" class="pabsolute"></a>
		<?php if (empty($this->is_ajax)) : ?>
			<div id='id_<?php echo $this->content_id; ?>'>
		<?php endif; ?>
		
		<?php if($this->showtoptitle == 1):?>
			<div class="layout_simple_head" id="layout_map">
				<?php echo $this->translate($this->sitepage->getTitle());?><?php echo $this->translate("'s Map");?>
			</div>
		<?php endif;?>
		
		<div class="layout_middle">
		<?php if (!empty($this->isManageAdmin)) : ?>
			<div class='clr seaocore_add '>
				<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->sitepage->page_id, 'action' => 'add-location', 'tab' => $this->identity_temp), $this->translate('Add Location'), array('class' => 'smoothbox icon_sitepages_map_add buttonlink fright')); ?>
			</div>
		<?php endif; ?>
		
				
		
		<?php if (!empty($this->sitepage->location) && !empty($this->MainLocationObject)) : ?>
		<div class='profile_fields sitepage_location_fields sitepage_list_highlight'>
				<div class="sitepage_location_fields_head">
					<?php if (!empty($this->MainLocationObject->locationname)) : ?>
						<?php echo $this->MainLocationObject->locationname ?>
					<?php else: ?>
						<?php echo $this->translate('Main Location'); ?>
					<?php endif; ?>
				</div>
				<div class="sitepage_location_fields_map_wrapper fright seaocore_map" id="sitepage_location_fields_map_wrapper_<?php echo $this->MainLocationObject->location_id ?>">
					<div class="sitepage_location_fields_map b_dark">
						<div class="sitepage_map_container_topbar b_dark" id='sitepage_map_container_topbar' >
							<a id="largemap_<?php echo $this->MainLocationObject->location_id ?>" href="javascript:void(0);"  class="bold fright">&laquo; <?php echo $this->translate('Large Map'); ?></a>
							<a id="smallmap_<?php echo $this->MainLocationObject->location_id ?>" href="javascript:void(0);"  class="bold fright" style="display:none"><?php echo $this->translate('Small Map'); ?> &raquo;</a>
						</div>
						<div class="sitepage_location_fields_map_canvas" id="map_canvas_sitepage_browse_<?php echo $this->MainLocationObject->location_id ?>" style="width:200px;"></div>
					</div>
				</div>
					
					<ul class="sitepage_location_fields_details">
						<li>
							<span><?php echo $this->translate('Location:'); ?> </span>
							<span><b><?php echo  $this->MainLocationObject->location; ?></b> - <span class="location_get_direction"><b>
								<?php if (!empty($this->mobile)) : ?>
								<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->MainLocationObject->page_id, 'resouce_type' => 'sitepage_page', 'location_id' => $this->MainLocationObject->location_id, 'is_mobile' => $this->mobile, 'flag' => 'map'), $this->translate("Get Directions"), array('target' => '_blank')) ; ?>
								<?php else: ?>
								<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $this->MainLocationObject->page_id, 'resouce_type' => 'sitepage_page', 'location_id' => $this->MainLocationObject->location_id, 'flag' => 'map'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')) ; ?>
								<?php endif; ?>
								</b></span>
							</span>
						</li>
						<?php if(!empty($this->MainLocationObject->formatted_address)):?>
							<li>
								<span><?php echo $this->translate('Formatted Address:'); ?> </span>
								<span><?php echo $this->MainLocationObject->formatted_address; ?> </span>
							</li>
						<?php endif; ?>
						<?php if(!empty($this->MainLocationObject->address)):?>
							<li>
								<span><?php echo $this->translate('Street Address:'); ?> </span>
								<span><?php echo $this->MainLocationObject->address; ?> </span>
							</li>
						<?php endif; ?>
						<?php if(!empty($this->MainLocationObject->city)):?>
							<li>
								<span><?php echo $this->translate('City:'); ?></span>
								<span><?php echo $this->MainLocationObject->city; ?> </span>
							</li>
						<?php endif; ?>
						<?php if(!empty($this->MainLocationObject->zipcode)):?>
							<li>
								<span><?php echo $this->translate('Zipcode:'); ?></span>
								<span><?php echo $this->MainLocationObject->zipcode; ?> </span>
							</li>
						<?php endif; ?>
						<?php if(!empty($this->MainLocationObject->state)):?>
							<li>
								<span><?php echo $this->translate('State:'); ?></span>
								<span><?php echo $this->MainLocationObject->state; ?></span>
							</li>
						<?php endif; ?>
						<?php if(!empty($this->MainLocationObject->country)):?>
							<li>
								<span><?php echo $this->translate('Country:'); ?></span>
								<span><?php echo $this->MainLocationObject->country; ?></span>
							</li>
						<?php endif; ?>
						<?php if (!empty($this->isManageAdmin)) : ?>
							<li class='sitepage_location_fields_option clr'>
								<?php if (empty($this->is_ajax)): ?>
									<?php //echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->sitepage->page_id, 'location_id' => $item->location_id, 'action' => 'edit-address', 'tab' => $this->identity_temp), $this->translate('Edit Address'), array('class' => 'smoothbox icon_sitepages_map_edit buttonlink ')); ?>
									<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->sitepage->page_id, 'location_id' => $this->MainLocationObject->location_id, 'action' => 'edit-location'), $this->translate('Edit Location'), array('class' => 'icon_sitepages_map_edit buttonlink ')); ?>
									<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->sitepage->page_id, 'location_id' => $this->MainLocationObject->location_id, 'action' => 'delete-location', 'tab' => $this->identity_temp), $this->translate('Delete'), array('class' => 'smoothbox icon_sitepages_map_delete buttonlink ')); ?>
								<?php else: ?>
									<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->sitepage->page_id, 'location_id' => $this->MainLocationObject->location_id, 'action' => 'edit-location'), $this->translate('Edit Location'), array('class' => 'icon_sitepages_map_edit buttonlink ')); ?>
									<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->sitepage->page_id, 'location_id' => $this->MainLocationObject->location_id, 'action' => 'delete-location', 'tab' => $this->identity_temp), $this->translate('Delete'), array('onclick' => 'owner(this);return false', 'class' => 'smoothbox icon_sitepages_map_delete buttonlink ')); ?>
								<?php endif; ?>
							</li>
						<?php endif; ?>
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
						<?php echo $this->translate('Location Information'); ?>
					<?php endif; ?>
					</span>
				</h4>
				<div class="sitepage_location_fields_map_wrapper fright seaocore_map" id="sitepage_location_fields_map_wrapper_<?php echo $item->location_id ?>">
					<div class="sitepage_location_fields_map b_dark">
						<div class="sitepage_map_container_topbar b_dark" id='sitepage_map_container_topbar' >
							<a id="largemap_<?php echo $item->location_id ?>" href="javascript:void(0);"  class="bold fright">&laquo; <?php echo $this->translate('Large Map'); ?></a>
							<a id="smallmap_<?php echo $item->location_id ?>" href="javascript:void(0);"  class="bold fright" style="display:none"><?php echo $this->translate('Small Map'); ?> &raquo;</a>
						</div>
						<div class="sitepage_location_fields_map_canvas" id="map_canvas_sitepage_browse_<?php echo $item->location_id ?>" style="width:200px;"></div>
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
					<?php if (!empty($this->isManageAdmin)) : ?>
						<li class='sitepage_location_fields_option clr'>
							<?php if (empty($this->is_ajax)): ?>
								<?php //echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->sitepage->page_id, 'location_id' => $item->location_id, 'action' => 'edit-address', 'tab' => $this->identity_temp), $this->translate('Edit Address'), array('class' => 'smoothbox icon_sitepages_map_edit buttonlink ')); ?>
								<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->sitepage->page_id, 'location_id' => $item->location_id, 'action' => 'edit-location'), $this->translate('Edit Location'), array('class' => 'icon_sitepages_map_edit buttonlink ')); ?>
								<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->sitepage->page_id, 'location_id' => $item->location_id, 'action' => 'delete-location', 'tab' => $this->identity_temp), $this->translate('Delete'), array('class' => 'smoothbox icon_sitepages_map_delete buttonlink ')); ?>
							<?php else: ?>
								<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->sitepage->page_id, 'location_id' => $item->location_id, 'action' => 'edit-location'), $this->translate('Edit Location'), array('class' => 'icon_sitepages_map_edit buttonlink ')); ?>
								<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->sitepage->page_id, 'location_id' => $item->location_id, 'action' => 'delete-location', 'tab' => $this->identity_temp), $this->translate('Delete'), array('onclick' => 'owner(this);return false', 'class' => 'smoothbox icon_sitepages_map_delete buttonlink ')); ?>
							<?php endif; ?>
						</li>
					<?php endif; ?>
				</ul>
			</div>	
		<?php endforeach; ?>
		
		<div class="clr sitepage_browse_location_paging" style="margin-top:10px;">
			<?php echo $this->paginationControl($this->location, null, array("pagination/pagination.tpl", "sitepage"), array("orderby" => $this->orderby)); ?>
			<?php if( count($this->location) > 1 ): ?>
				<div class="fleft" id="page_location_loding_image" style="display: none;margin:5px;">
				<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' alt="" />
				</div>
			<?php endif; ?>
		</div>
	<?php if (empty($this->is_ajax)) : ?>
	</div>
	<?php endif; ?>
</div>

<?php else: ?>
<script type="text/javascript">
	var page_communityads;
			var contentinformtion;
			var page_showtitle;
      var globalContentElement = en4.seaocore.getDomElements('content');
	if(contentinformtion == 0) {
		if($(globalContentElement).getElement('.layout_activity_feed')) {
			$(globalContentElement).getElement('.layout_activity_feed').style.display = 'none';
		}		
		if($(globalContentElement).getElement('.layout_core_profile_links')) {
			$(globalContentElement).getElement('.layout_core_profile_links').style.display = 'none';
		}
		if($(globalContentElement).getElement('.layout_sitepage_info_sitepage')) {
			$(globalContentElement).getElement('.layout_sitepage_info_sitepage').style.display = 'none';
		}	
		
		if($(globalContentElement).getElement('.layout_sitepage_location_sitepage')) {
			$(globalContentElement).getElement('.layout_sitepage_location_sitepage').style.display = 'none';
		}	
	}
</script>

<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<script type="text/javascript">
  var myLatlng;
  function initialize() {
    var myLatlng = new google.maps.LatLng(<?php echo $this->location->latitude; ?>,<?php echo $this->location->longitude; ?>);
    var myOptions = {
      zoom: <?php echo $this->location->zoom; ?> ,
      center: myLatlng,
    //  navigationControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    var map = new google.maps.Map(document.getElementById("map_canvas_sitepage_browse"), myOptions);

    var contentString = '<div id="content">'+
      '<div id="siteNotice">'+
      '</div>'+'<ul class="sitepages_locationdetails"><li>'+
			'<div class="sitepages_locationdetails_info_title">'+
	    	"<?php echo $this->string()->escapeJavascript($sitepage->getTitle())?>"+
        '<div class="fright">'+
          '<span >'+
            <?php if ($sitepage->featured == 1): ?>
	            '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Featured')))) ?>'+	            <?php endif; ?>
          '</span>'+
          '<span>'+
            <?php if ($sitepage->sponsored == 1): ?>
	            '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Sponsored')))) ?>'+
          	<?php endif; ?>
          '</span>'+
        '</div>'+
        '<div class="clr"></div>'+
      '</div>'+
      '<div class="sitepages_locationdetails_photo" >'+
    		'<?php echo  $this->itemPhoto($sitepage, 'thumb.normal') ?>'+
	    '</div>'+ 
      '<div class="sitepages_locationdetails_info">'+
				<?php if ($this->ratngShow): ?>
					<?php if (($sitepage->rating > 0)): ?>

						<?php 
							$currentRatingValue = $sitepage->rating;
							$difference = $currentRatingValue- (int)$currentRatingValue;
							if($difference < .5) {
								$finalRatingValue = (int)$currentRatingValue;
							}
							else {
								$finalRatingValue = (int)$currentRatingValue + .5;
							}	
						?>

						'<span class="clr" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">'+
							<?php for ($x = 1; $x <= $sitepage->rating; $x++): ?>
								'<span class="rating_star_generic rating_star" ></span>'+
							<?php endfor; ?>
							<?php if ((round($sitepage->rating) - $sitepage->rating) > 0): ?>
								'<span class="rating_star_generic rating_star_half"></span>'+
							<?php endif; ?>
						'</span>'+
					<?php endif; ?>
				<?php endif; ?>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1)):?>
          '<div class="sitepages_locationdetails_info_date">'+
            '<?php echo $this->timestamp(strtotime($sitepage->creation_date)) ?> - <?php echo $this->string()->escapeJavascript($this->translate('posted by')); ?> '+
            '<?php echo $this->htmlLink($sitepage->getOwner()->getHref(), $this->string()->escapeJavascript($sitepage->getOwner()->getTitle())) ?>'+
          '</div>'+
        <?php endif; ?>
        '<div class="sitepages_locationdetails_info_date">'+
	      	'<?php echo $this->string()->escapeJavascript($this->translate(array('%s comment', '%s comments', $sitepage->comment_count), $this->locale()->toNumber($sitepage->comment_count))) ?>,&nbsp;'+
	        '<?php echo $this->string()->escapeJavascript($this->translate(array('%s view', '%s views', $sitepage->view_count), $this->locale()->toNumber($sitepage->view_count))) ?>'+
		    '</div>'+
				'<div class="sitepages_locationdetails_info_date">'+
				<?php if (!empty($sitepage->phone)): ?>
				"<?php  echo  $this->string()->escapeJavascript($this->translate("Phone: ")) . $sitepage->phone ?><br />"+
				<?php endif; ?>
				<?php if (!empty($sitepage->email)): ?>
				"<?php  echo  $this->string()->escapeJavascript($this->translate("Email: ")) . $sitepage->email ?><br />"+
				<?php endif; ?>
				<?php if (!empty($sitepage->website)): ?>
				"<?php  echo  $this->string()->escapeJavascript($this->translate("Website: ")) .$sitepage->website ?>"+
				<?php endif; ?>
				'</div>'+
        <?php if($sitepage->price && $this->enablePrice): ?>
                '<div class="sitepages_locationdetails_info_date">'+
								"<i><b>"+"<?php echo Engine_Api::_()->sitepage()->getPriceWithCurrency($sitepage->price); ?>"+ "</b></i>"+
							'</div>'+
        <?php endif; ?>
			'<div class="sitepages_locationdetails_info_date">'+
				"<i><b>"+"<?php echo $this->string()->escapeJavascript( $this->location->location); ?>"+ "</b></i>"+
	      '</div>'+
        
 		  '</div>'+
 		  '<div class="clr"></div>'+
	 ' </li></ul>'+


      '</div>';
   
    var infowindow = new google.maps.InfoWindow({
      content: contentString ,
      size: new google.maps.Size(250,50)

    });

    var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      title: "<?php echo str_replace('"', ' ',$sitepage->getTitle())?>"
    });
    google.maps.event.addListener(marker, 'click', function() {
       
      infowindow.open(map,marker);
    });

    <?php if(!empty($this->showtoptitle)):?>
      $$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function() {
      google.maps.event.trigger(map, 'resize');
      map.setZoom(<?php echo $this->location->zoom; ?> );
      map.setCenter(myLatlng);
    });
    <?php else:?>
      $$('.tab_layout_sitepage_location_sitepage').addEvent('click', function() {
      google.maps.event.trigger(map, 'resize');
      map.setZoom(<?php echo $this->location->zoom; ?> );
      map.setCenter(myLatlng);
    });
    <?php endif;?>

    google.maps.event.addListener(map, 'click', function() {
      
      infowindow.close();
			google.maps.event.trigger(map, 'resize');
      map.setZoom(<?php echo $this->location->zoom; ?> );
      map.setCenter(myLatlng);
    });
  }
</script>

<div id='id_<?php echo $this->content_id; ?>'>
  <?php if($this->showtoptitle == 1):?>
		<div class="layout_simple_head" id="layout_map">
      <?php echo $this->translate($this->sitepage->getTitle());?><?php echo $this->translate("'s Map");?>
		</div>
	<?php endif;?>

	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adlocationwidget', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)):?>
			<div class="layout_right" id="communityad_location">
               <?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adlocationwidget', 3),"loaded_by_ajax"=>0,'widgetId'=>'page_location'))?>
			</div>
	
		<div class="layout_middle">
	<?php endif;?>
			<div class='profile_fields'>
			  <ul class="sitepage_profile_location">
			    <li class="seaocore_map">
			  		<div id="map_canvas_sitepage_browse"></div>
			  			<?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
							<?php if (!empty($siteTitle)) : ?>
							<div class="seaocore_map_info"><?php echo "Locations on "; ?><a href="" target="_blank"><?php echo $siteTitle; ?></a></div>
							<?php endif; ?>
			    </li>
			  </ul>
			  <h4>
			    <span><?php echo $this->translate('Location Information') ?></span>
			  </h4>
			  <ul>
			    <li>
		        <span><?php echo $this->translate('Location:'); ?> </span>
		        <span><b><?php echo  $this->location->location; ?></b> - <b>
              <?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $this->location->page_id, 'resouce_type' => 'sitepage_page'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')) ; ?>
		        </b></span>
			    </li>
			    <?php if(!empty($this->location->formatted_address)):?>
				    <li>
				      <span><?php echo $this->translate('Formatted Address:'); ?> </span>
				      <span><?php echo $this->location->formatted_address; ?> </span>
				    </li>
			    <?php endif; ?>
			    <?php if(!empty($this->location->address)):?>
				    <li>
				      <span><?php echo $this->translate('Street Address:'); ?> </span>
				      <span><?php echo $this->location->address; ?> </span>
				    </li>
			    <?php endif; ?>
			    <?php if(!empty($this->location->city)):?>
				    <li>
				      <span><?php echo $this->translate('City:'); ?></span>
				      <span><?php echo $this->location->city; ?> </span>
				    </li>
			    <?php endif; ?>
			    <?php if(!empty($this->location->zipcode)):?>
				    <li>
				      <span><?php echo $this->translate('Zipcode:'); ?></span>
				      <span><?php echo $this->location->zipcode; ?> </span>
				    </li>
			    <?php endif; ?>
			    <?php if(!empty($this->location->state)):?>
				    <li>
				      <span><?php echo $this->translate('State:'); ?></span>
				      <span><?php echo $this->location->state; ?></span>
				    </li>
			    <?php endif; ?>
			    <?php if(!empty($this->location->country)):?>
						<li>
						  <span><?php echo $this->translate('Country:'); ?></span>
						  <span><?php echo $this->location->country; ?></span>
						</li>
			    <?php endif; ?>
			  </ul>
			</div>
	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adlocationwidget', 3)  && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)):?>
		</div>
	<?php endif; ?>
</div>

<style type="text/css">
  #map_canvas_sitepage_browse {
    width: 100%;
    height: 500px;
  
  }
  #map_canvas_sitepage_browse > div{
    height: 500px;
  }
  #infoPanel {
    float: left;
    margin-left: 10px;
  }
  #infoPanel div {
    margin-bottom: 5px;
  }
</style>
<script type="text/javascript">
	window.addEvent('domready',function(){
		initialize();
	});
</script>
<?php endif; ?>

<?php if (empty($this->is_ajax)) : ?>
	<script type="text/javascript">
		var page_communityad_integration = '<?php echo $page_communityad_integration; ?>';
		var adwithoutpackage = '<?php echo Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage) ?>';
		var location_ads_display = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adlocationwidget', 3);?>';
		$$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function(event) 
		{ 
			if(page_showtitle != 0) {
				if($('profile_status')) {
					$('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitepage->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Map');?></h2>";	
				}
				if($('layout_map')) {
					$('layout_map').style.display = 'block';
				}	    	
			}   
			hideWidgetsForModule('sitepagelocation');
			if($('id_' + <?php echo $this->content_id ?>))
			$('id_' + <?php echo $this->content_id ?>).style.display = "block";
			if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
				$(globalContentElement).getElement('.'+ prev_tab_class).style.display = 'none';
        //scrollToTopForPage($('id_<?php echo $this->content_id; ?>'));
			}
			prev_tab_id = '<?php echo $this->content_id; ?>';	
			prev_tab_class = 'layout_sitepage_location_sitepage';

			if(page_showtitle == 1 && page_communityads == 1 && location_ads_display != 0 && page_communityad_integration != 0 && adwithoutpackage != 0) {
				setLeftLayoutForPage();  	
			} else if(page_showtitle == 0 && page_communityads == 1 && location_ads_display != 0 && page_communityad_integration != 0 && adwithoutpackage != 0) {
					setLeftLayoutForPage(1); 
			}
			
			if(page_communityads == 1 && location_ads_display == 0) {
				setLeftLayoutForPage();  	
			}
      if($(event.target).get('tag') !='div' && ($(event.target).getParent('.layout_sitepage_location_sitepage')==null)){
      scrollToTopForPage($(globalContentElement).getElement(".layout_sitepage_location_sitepage"));
    }
		});
	</script>
<?php endif; ?>