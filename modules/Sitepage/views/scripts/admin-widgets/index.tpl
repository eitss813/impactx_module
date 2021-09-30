<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='tabs'>
  <ul class="navigation">
    <li class="active">
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'widgets', 'action' => 'index'), $this->translate('General Settings'), array()) ?>

    </li>
    <li>
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'items', 'action' => 'day'), $this->translate('Page of the Day'), array()) ?>
    </li>
  </ul>
</div>

<div class="tip">
	<span>
		<?php echo $this->translate('We have moved these "General Settings" to "Layout Editor". You can change the desired settings of the respective widgets from "Layout Editor" by clicking on the "edit" link.');?>
	</span>
</div>