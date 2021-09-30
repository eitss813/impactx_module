<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: choose-project.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript" >
  var submitformajax = 1;
</script>
<?php
	$this->headScript()
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
	?>
<?php if (empty($this->is_ajax)) : ?>
<div class="generic_layout_container layout_middle">
<div class="generic_layout_container layout_core_content">
	<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
	<div class="layout_middle">
		<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
		<div class="sitepage_edit_content">
			<div class="sitepage_edit_header">
				<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepage->page_id, $this->sitepage->owner_id, $this->sitepage->getSlug()),$this->translate('VIEW_PAGE')) ?>
				<?php if($this->sitepage->draft == 0 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0)) echo $this->htmlLink(array('route' => 'sitepage_publish', 'page_id' => $this->sitepage->page_id), $this->translate('Mark As Live'), array('class'=>'smoothbox')) ?>
				<h3><?php echo $this->translate('Dashboard: ').$this->sitepage->title; ?></h3>
			</div>
      <div id="show_tab_content">
<?php endif; ?> 
  
	<?php echo $this->form->render($this); ?>
	<?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/_chooseProject.tpl', array('owner_id' => $this->owner_id, 'parent_type' => $this->parent_type)); ?>
	<br />
  <div id="show_tab_content_child">
  </div>
	<?php if (empty($this->is_ajax)) : ?>
	    </div>
	  </div>
  </div>
    
</div>
</div>
<?php endif; ?> 