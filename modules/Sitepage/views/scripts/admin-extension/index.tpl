<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Socialengineaddon
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upgrade.tpl 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate('Extensions for Directory / Pages Plugin'); ?></h3>
<?php echo $this->translate(''); ?>
<div class='tabs'>
  <ul class="navigation">
    <li>
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'extension', 'action' => 'upgrade'), $this->translate('Extension Upgrade'), array()) ?>
    </li>
    <li >
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'extension', 'action' => 'information'), $this->translate('Extension Information'), array()) ?>
    </li>
    <li class="active">
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'extension', 'action' => 'index'), $this->translate('Manage Extensions'), array()) ?>
    </li>
  </ul>
</div>
<div class='clear sitepage_settings_form'>
  <div class='settings'>
    <?php //echo $this->form->render($this) ?>
  </div>
</div>

<?php echo $this->content()->renderWidget('sitepage.extension-show') ?>