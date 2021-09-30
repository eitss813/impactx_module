<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
 ?>
<?php 
  $defaultProfileFieldId = Engine_Api::_()->getDbTable('metas', 'sitealbum')->defaultProfileId();
  $defaultProfileFieldId = "0_0_$defaultProfileFieldId";
  ?>
<script type="text/javascript">

	sm4.core.runonce.add(function() {
		sm4.core.Module.autoCompleter.attach("tags", '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'index', 'action' => 'tag-suggest'), 'default', true) ?>', {'singletextbox': true, 'limit':10, 'minLength': 1, 'showPhoto' : false, 'search' : 'text'}, 'toValues'); 
	});

  sm4.core.runonce.add(function()
  {
     var defaultProfileId = '<?php echo $defaultProfileFieldId ?>'  + '-wrapper';
     if($.type($.mobile.activePage.find('#'+defaultProfileId)) && typeof $.mobile.activePage.find('#'+defaultProfileId) != 'undefined') {
        $.mobile.activePage.find('#'+defaultProfileId).css('display', 'none');
     }
  });   
</script>

<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
        //'topLevelId' => (int) @$this->topLevelId,
        //'topLevelValue' => (int) @$this->topLevelValue
))
?>
  <?php echo $this->form->render(); ?>