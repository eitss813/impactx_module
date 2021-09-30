<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageadmincontact
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-11-15 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  var package_id='';
  var categories_id='';
  function getValues() {
    if($('admincontact').packages) {
      for (i=0; i<$('admincontact').packages.length;i++)
      {
      if ($('admincontact').packages[i].selected)
       package_id = package_id  + $('admincontact').packages[i].value + ',';   
      }
    }
    
    if($('admincontact').categories) {
      for (i=0; i<$('admincontact').categories.length;i++)
      {
      if ($('admincontact').categories[i].selected)
        categories_id = categories_id  + $('admincontact').categories[i].value + ',';   
      }
    }
    
    if($('admincontact').status) {
      for (i=0; i<$('admincontact').status.length;i++)
      {
      if ($('admincontact').status[i].selected)
        status = status  + $('admincontact').status[i].value + ',';   
      }
    }
    
    <?php
      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $url = $view->url(array('action' => 'index'), 'sitepageadmincontact_messages_general', true);
    ?>
        
    var url = '<?php echo $url; ?>';
    <?php if (Engine_Api::_()->sitepage()->hasPackageEnable())  :?>
      url = url + '?package_id=' + package_id + '&categories_id=' + categories_id+ '&status=' + status;
    <?php else: ?>
      url = url + '?categories_id=' + categories_id + '&status=' + status;
    <?php endif; ?>  
    package_id = '';    
    categories_id='';
    status='';
    Smoothbox.open(url);
  }

</script>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/pluginLink.tpl'; ?>
<h2><?php echo $this->translate('Directory / Pages - Contact Page Owners Extension') ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='clear sitepage_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>