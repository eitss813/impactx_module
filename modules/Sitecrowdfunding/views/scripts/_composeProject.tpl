<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _composeProject.tpl 10109 2017-05-15 01:53:50Z andres $
 * @author     SocialEngineAddOns
 */
?>
<?php
$parent_type = 'user';
$parent_id = Engine_Api::_()->user()->getViewer()->getIdentity();
if (Engine_Api::_()->core()->hasSubject()) {
  $hasIntegrated = false;
  $subject = Engine_Api::_()->core()->getSubject();
  $moduleName = strtolower($subject->getModuleName());
  if ($moduleName == 'user') {
    if (!$subject->isSelf(Engine_Api::_()->user()->getViewer()))
      return;
  } else {
    
    if ($moduleName == 'sitereview' && isset($subject->listingtype_id)) {
      if ((Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $subject->listingtype_id, 'item_module' => 'sitereview'))))
        $hasIntegrated = true;
    } else {
      if ((Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => $subject->getType(), 'item_module' => strtolower($subject->getModuleName())))))
        $hasIntegrated = true;
    }

    if (!$hasIntegrated) {
      return;
    }
    
    $parent_type = $subject->getType();
    $parent_id = $subject->getIdentity();
   
  }
}
 $isCreatePrivacy = Engine_Api::_()->sitecrowdfunding()->isCreatePrivacy($parent_type, $parent_id);
    if (empty($isCreatePrivacy))
      return false;
?>

<?php if(Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()):?>
    <?php $url = $this->url(array('action' => 'index', 'parent_type' => $parent_type, 'parent_id' => $parent_id, 'return_url' => 'SE64-' . base64_encode($_SERVER['REQUEST_URI'])), "sitecrowdfunding_package", true) ?>
    <?php else:?>
      <?php $url = $this->url(array('action' => 'create', 'parent_type' => $parent_type, 'parent_id' => $parent_id, 'return_url' => 'SE64-' . base64_encode($_SERVER['REQUEST_URI'])), "sitecrowdfunding_project_general", true) ?>
<?php endif; ?>
<?php
    if ((empty($this->isAFFWIDGET) &&  empty($this->isAAFWIDGETMobile))):
        return;
    endif;
?>
<?php $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/composer_project.js');
?>
<script type="text/javascript">
  en4.core.runonce.add(function() {
        composeInstance.addPlugin(new Composer.Plugin.Sitecrowdfunding({
          title: '<?php echo $this->translate('Create a Project') ?>',
          lang: {
            'Create a Project': '<?php echo $this->string()->escapeJavascript($this->translate('Create a Project')) ?>'
          },
          loadJSFiles: ['<?php echo $this->tinyMCESEAO()->addJS(true) ?>'],
          packageEnable : '<?php echo Engine_Api::_()->sitecrowdfunding()->hasPackageEnable();?>',
          requestOptions: {
            'url': '<?php echo $url ?>'
          }
        }));
  });
</script>
