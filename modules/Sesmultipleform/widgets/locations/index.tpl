<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesmultipleform/externals/styles/styles.css'); ?>
<?php $id = $this->identity; 
 $widgetId = 'sesmultipleform_'.$id;
 $this->headScript()->appendFile('https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('ses.mapApiKey', ''));
 ?>
<script type="text/javascript">
sesJqueryObject(document).ready(function(){
	initializeSesmultipleformMap<?php echo $widgetId; ?>();
});
var map<?php echo $widgetId; ?>;
var infowindow<?php echo $widgetId; ?>;
var marker<?php echo $widgetId; ?>;
var mapLoad<?php echo $widgetId; ?> = true;
function initializeSesmultipleformMap<?php echo $widgetId; ?>() {
  var latlng<?php echo $widgetId; ?> = new google.maps.LatLng('<?php echo $this->lat; ?>', '<?php echo $this->lng; ?>');
  var mapOptions<?php echo $widgetId; ?> = {
    center: latlng<?php echo $widgetId; ?>,
    zoom: <?php echo $this->mapzoom; ?>
  };
	map<?php echo $widgetId; ?> = new google.maps.Map(document.getElementById('<?php echo $widgetId; ?>'),
    mapOptions<?php echo $widgetId; ?>);
	 infowindow<?php echo $widgetId; ?> = new google.maps.InfoWindow({
				content: '<?php echo $this->location; ?>'
	 });
   marker<?php echo $widgetId; ?> = new google.maps.Marker({
    map: map<?php echo $widgetId; ?>,
     position: latlng<?php echo $widgetId; ?>,
     title: '<?php  echo $this->location;?>',
		 icon: '<?php echo $this->layout()->staticBaseUrl?>application/modules/Sesmultipleform/externals/images/map-marker.png'
  });
 google.maps.event.addListener(marker<?php echo $widgetId; ?>, 'click', function () {
	infowindow<?php echo $widgetId; ?>.open(map<?php echo $widgetId; ?>, marker<?php echo $widgetId; ?>);
 });
}
</script> 
<div class='clear sesmultipleform_map_container sesbm'>
	<div id="sesmultipleform_<?php echo $id; ?>" style="height:<?php echo $this->height.'px'; ?>; width:100%; display:block;"></div>
  <div class="sesmultipleform_map_info_box">
    <ul class="sesmultipleform_map_info">
    <?php if($this->aboutdescr): ?>
      <li class="sesbasic_clearfix">
        <div>
          <?php echo $this->translate(nl2br($this->aboutdescr)); ?>
        </div>
      </li>
    <?php endif; ?>
    <?php if($this->address): ?>
      <li class="sesmultipleform_map_info_field sesbasic_clearfix">
        <i class="fa fa-map-marker"></i>
        <div>
          <span><?php echo $this->translate('Address:'); ?></span>
          <p><?php echo $this->translate(nl2br($this->address)); ?></p>
        </div>
      </li>
    <?php endif; ?>
    <?php if($this->company): ?>
      <li class="sesmultipleform_map_info_field sesbasic_clearfix">
        <i class="fa fa-info-circle"></i>
        <div>
          <span><?php echo $this->translate('Company Registration Info:'); ?></span>
          <p><?php echo $this->translate($this->company); ?></p>
        </div>
      </li>
    <?php endif; ?>  
    <?php if($this->email): ?>
      <li class="sesmultipleform_map_info_field sesbasic_clearfix">
        <i class="fa fa-at"></i>
        <div>
          <span><?php echo $this->translate('E-mail:'); ?></span>
          <p><a href='mailto:<?php echo $this->email ?>' target="_blank"><?php echo $this->email ?></a></p>
        </div>
      </li>
    <?php endif; ?>
    <?php if($this->phone): ?>
      <li class="sesmultipleform_map_info_field sesbasic_clearfix">
        <i class="fa fa-mobile"></i>
        <div>
          <span><?php echo $this->translate('Phone:'); ?></span>
          <p><?php echo $this->phone; ?></p>
        </div>  
      </li>
    <?php endif; ?>
    <?php if($this->skype): ?>
      <li class="sesmultipleform_map_info_field sesbasic_clearfix">
        <i class="fab fa-skype"></i>
        <div>
          <span><?php echo $this->translate('Skype:'); ?></span>
          <p><?php echo $this->skype; ?></p>
        </div>  
      </li>
    <?php endif; ?>
    <li class="sesbasic_clearfix">
      <span><?php echo $this->translate('Social Links:'); ?></span>
      <p class="socialicons">
        <?php if(!empty($this->facebook)):?>
          <a href="<?php echo $this->facebook; ?>" target="_blank" title="<?php echo $this->translate('Facebook'); ?>">
            <i class="fab fa-facebook-square"></i>
          </a>
        <?php endif;?>
        <?php if(!empty($this->twitter)):?>
          <a href="<?php echo $this->twitter;?>" target="_blank" title"<?php echo $this->translate('Twitter'); ?>">
            <i class="fab fa-twitter-square"></i>
          </a>
        <?php endif;?>
        <?php if(!empty($this->youtube)):?>
          <a href="<?php echo $this->youtube;?>" target="_blank" title="<?php echo $this->translate('YouTube'); ?>">
            <i class="fab fa-youtube-square"></i>
          </a>
        <?php endif;?>
        <?php if(!empty($this->linkdin)):?>
          <a href="<?php echo $this->linkdin;?>" target="_blank" title="<?php echo $this->translate('Linked In'); ?>">
            <i class="fab fa-linkedin"></i>
          </a>
        <?php endif;?>
        <?php if(!empty($this->googleplus)):?>
          <a href="<?php echo $this->googleplus;?>" target="_blank" title="<?php echo $this->translate('Google Plus'); ?>">
            <i class="fab fa-google-plus-square"></i>
          </a>
        <?php endif;?>
        <?php if(!empty($this->rssfeed)):?>
          <a href="<?php echo $this->rssfeed;?>" target="_blank" title="<?php echo $this->translate('RSS Feed'); ?>">
            <i class="fa fa-rss-square"></i>
          </a>
        <?php endif;?>
        <?php if(!empty($this->pinterest)):?>
          <a href="<?php echo $this->pinterest;?>" target="_blank" title="<?php echo $this->translate('Pintrest'); ?>">
            <i class="fab fa-pinterest-square"></i>
          </a>
        <?php endif;?>
      </p>
    </li>
  </ul>
 </div>
</div>