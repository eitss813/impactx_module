<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitepagediscussion/externals/styles/style_sitepagediscussion.css')
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>

<div class="sitepage_viewpages_head">
  <?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
  <?php if(!empty($this->can_edit)):?>
		<div class="fright">
			<a href='<?php echo $this->url(array('page_id' => $this->sitepage->page_id), 'sitepage_edit', true) ?>' class='buttonlink icon_sitepages_dashboard'><?php echo $this->translate('Dashboard');?></a>
		</div>
	<?php endif;?>
  <h2>	
    <?php echo $this->sitepage->__toString() ?>	
    <?php echo $this->translate('&raquo; '); ?>
    <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Discussions')) ?>
  </h2>  
  <?php if($this->resource_type && $this->resource_id):?>
  <?php $resource=Engine_Api::_()->getItem($this->resource_type, $this->resource_id);?>
     <span>
      <?php echo $this->translate("In ".$resource->getMediaType().":") ?>
      <?php echo $this->htmlLink($resource->getHref(), $resource->getTitle()) ?>
    </span>
  <?php endif;?>
</div>	
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addiscussioncreate', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)): ?>
  <div class="layout_right" id="communityad_topiccreate">
     <?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addiscussioncreate', 3),"loaded_by_ajax"=>1,'widgetId'=>'page_topiccreate'))?>
  </div>
<?php endif; ?>
<div class="layout_middle">
  <?php echo $this->form->render($this) ?>
</div>	