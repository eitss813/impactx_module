<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: layout-block.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="fleft"><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/manageExtensions.tpl'; ?>
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<div class='tabs'>
	<ul class="navigation">
		<li >
			<?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitepage','controller'=>'defaultlayout','action'=>'index'), $this->translate('Page Profile Layout Type'), array())
			?>
		</li>

		<li>
			<?php
			echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitepage','controller'=>'layout','action'=>'layout', 'page' => $this->page_id), $this->translate('Page Profile Layout Editor'), array())
		  ?>
		</li>

    <li class="active">
			<?php
			echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitepage','controller'=>'layout','action'=>'layout-block'), $this->translate('Page Profile Layout Settings'), array())
		  ?>
		</li>
	</ul>
</div>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0)):?>
  <div class='clear sitepage_settings_form'>
    <div class='settings'>
      <?php echo $this->form->render($this); ?>
    </div>
  </div>
<?php else :?>

		<div class="tip">
	  	<span><?php echo $this->translate('You have disabled Page Profile Layout editing by their owners from the "Edit Page Layout" field in Global Settings. If you enable it, then from here you will be able to choose which blocks / widgets of "Core" and "SocialEngineAddOns" modules should be available to users on their Page Profile. Currently, you can configure Page Profile Layout from the "Layout" > "Layout Editor" section by selecting "Page Profile" from the "Editing" dropdown.'); ?></span>
	  </div>

<?php endif;?>
