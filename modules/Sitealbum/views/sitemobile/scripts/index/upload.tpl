<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */  
?><?php  
  $request = Zend_Controller_Front::getInstance()->getRequest();
  $module = $request->getModuleName();
  $controller = $request->getControllerName();
  $action = $request->getActionName();
  $defaultProfileFieldId = Engine_Api::_()->getDbTable('metas', 'sitealbum')->defaultProfileId();
  $defaultProfileFieldId = "0_0_$defaultProfileFieldId";
  ?>
<script type="text/javascript">
//Location auto suggest
sm4.core.runonce.add(function(){  
if($.mobile.activePage.find('#sitealbum_location').get(0)) {
    var autocomplete = new google.maps.places.Autocomplete($.mobile.activePage.find('#sitealbum_location').get(0));
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
      var place = autocomplete.getPlace();
      if (!place.geometry) {
        return;
      }

    $.mobile.activePage.find('#latitude').val(place.geometry.location.lat());
    $.mobile.activePage.find('#longitude').val(place.geometry.location.lng());

  });  
}
});

//Tag Autosuggest
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.tags.enabled', 1)): ?>
	sm4.core.runonce.add(function() {
		sm4.core.Module.autoCompleter.attach("tags", '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'index', 'action' => 'tag-suggest'), 'default', true) ?>', {'singletextbox': true, 'limit':10, 'minLength': 1, 'showPhoto' : false, 'search' : 'text'}, 'toValues'); 
	});
<?php endif; ?>


//Hide profile type field at on load
sm4.core.runonce.add(function() {
  var defaultProfileId = '<?php echo $defaultProfileFieldId ?>'  + '-wrapper';
  if ($.type($.mobile.activePage.find('#'+defaultProfileId)) && typeof $.mobile.activePage.find('#'+defaultProfileId) != 'undefined') {
    $.mobile.activePage.find('#'+defaultProfileId).css('display', 'none');
  }   
});

  var updateTextFields = function()
  { 
    var album = $.mobile.activePage.find("#album");
     var fieldToggleGroup = ['#title-wrapper', '#category_id-wrapper', '#description-wrapper', '#search-wrapper', '#sitealbum_location-wrapper',
      '#auth_view-wrapper', '#auth_comment-wrapper', '#auth_tag-wrapper', '#tags-wrapper'];
    fieldToggleGroup = $.mobile.activePage.find(fieldToggleGroup.join(','))
    if (album.val() == 0){
      fieldToggleGroup.show();
    } else {
      fieldToggleGroup.hide();
    }
  }
  sm4.core.runonce.add(updateTextFields);
</script>
<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
        //'topLevelId' => (int) @$this->topLevelId,
        //'topLevelValue' => (int) @$this->topLevelValue
))
?>
<?php echo $this->form->render($this) ?>

<script type="text/javascript">

  sm4.core.runonce.add(function() { 
    if (DetectAllWindowsMobile()) {
      $.mobile.activePage.find('#form-upload').css('display', 'none');
      $.mobile.activePage.find('#show_supported_message').css('display', 'block');
    } else {
      $.mobile.activePage.find('#form-upload').css('display', 'block');
      $.mobile.activePage.find('#show_supported_message').css('display', 'none');
    }    
   
    //PUT THE SEARCH ALBUM WRAPPER AT BOTTOM.
    $.mobile.activePage.find( "#file-wrapper" ).before( $.mobile.activePage.find('#search-wrapper') );
    
  });

</script>


<div style="display:none" id="show_supported_message" class='tip'>

  <span><?php echo $this->translate("Sorry, the browser you are using does not support Photo uploading. You can create an album from your Desktop."); ?><span>

</div>

