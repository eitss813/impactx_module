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
  $description = "Below you can select the type of layout for the main profile of the directory items / pages on your site. You can choose from amongst 2 attractive AJAX based layouts.";
  if(Engine_Api::_()->getDbtable('modules','core')->isModuleEnabled('sitepageintegration')) {
		$description .= "<br />Note: If you change the layout you have also set again all the content integrated widgets in the Page Profile layout.";
	}  
  $this->form->setTitle('Page Profile Layout Type');
  $this->form->setDescription($this->translate("$description"));
  $this->form->getDecorator('Description')->setOption('escape', false);
?>
<script type="text/javascript">

  function showpreview() {
    var layoutvalue = '';
    if($('sitepage_layout_setting-1').checked) {
      Smoothbox.open('<div class="sitepage-layout-popup"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/admin/tabbedlayout.png" alt=""  /></div>');
    } 
    else {
      Smoothbox.open('<div class="sitepage-layout-popup"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/admin/withouttabbedlayout.png" alt="" /></div>');
    }
  }

  function savevalues() {
    Smoothbox.open('<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'defaultlayout', 'action' => 'savelayout'), 'admin_default') ?>')	
  }
</script>

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
		<li class="active">
			<?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitepage','controller'=>'defaultlayout','action'=>'index'), $this->translate('Page Profile Layout Type'), array())
			?>
		</li>

		<li>
			<?php
			echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitepage','controller'=>'layout','action'=>'layout', 'page' => $this->page_id), $this->translate('Page Profile Layout Editor'), array())
		  ?>
		</li>

    <?php if(Engine_Api::_()->sitepage()->checkEnableForMobile('sitepage')):?>
			<li>
				<?php
					echo $this->htmlLink(array('route'=>'admin_default', 'module'=>'sitepage','controller'=>'mobile-layout','action'=>'layout', 'page' => $this->mobile_page_id), $this->translate('Page Profile Layout Editor for Mobile / Tablet'), array())
				?>
			</li>	
    <?php endif;?>
	</ul>
</div>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0)):?>
<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<?php if(!Engine_Api::_()->getApi("settings", "core")->getSetting('sitepage.layout.coverphotoenabled', 0)) :?>
<br />
	<div class='clear'>
		<div class='settings'>
			<?php echo $this->coverForm->render($this); ?>
		</div>
	</div>

	<script type="text/javascript">

		formElement = $('cover-form-upload');

		if (typeof formElement != 'undefined' ) {
			formElement.addEvent('submit', function(event) {
				event.stop();
				Smoothbox.open('<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'defaultlayout', 'action' => 'set-cover-page-layout'), 'admin_default') ?>');
			})};

		function continuePageLayout() {
			$('cover-form-upload').submit();
			parent.Smoothbox.close();	
		}

	</script>

<?php endif;?>

<script type="text/javascript">

  formElement = $('form-upload');

  if (typeof formElement != 'undefined' ) {
    formElement.addEvent('submit', function(event) {
      event.stop();
      Smoothbox.open('<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'defaultlayout', 'action' => 'savelayout'), 'admin_default') ?>');
    })};


  function showpreview() {
    var layoutvalue = '';
    if($('sitepage_layout_setting-1').checked) {
      Smoothbox.open('<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/admin/tabbedlayout.png" alt=""  />');
    } 
    else {
      Smoothbox.open('<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/admin/withouttabbedlayout.png" alt="" />');
    }
  }

  function continuelayout() {
    $('form-upload').submit();
    parent.Smoothbox.close();	
  }

</script>
<?php else :?>

    <div class="tip">
    <span><?php echo $this->translate('You cannot access this setting as you have disabled Page Profile Layout editing by their owners from the "Edit Page Layout" field in Global Settings. '); ?></span>
    </div>

<?php endif;?>
