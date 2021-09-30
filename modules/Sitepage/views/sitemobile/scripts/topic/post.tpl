<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: post.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
 // include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitepagediscussion/externals/styles/style_sitepagediscussion.css')
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>

<div class="sitepage_viewpages_head">
  <?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
  <h2>	
    <?php echo $this->sitepage->__toString() ?>	
    <?php echo $this->translate('&raquo; '); ?>
    <?php echo $this->htmlLink(array('route' => 'sitepage_entry_view', 'page_url' => Engine_Api::_()->sitepage()->getPageUrl($this->sitepage->page_id), 'tab' => $this->tab_selected_id), $this->translate('Discussions')) ?>
    <?php echo $this->translate('&raquo; '); ?>
    <?php echo $this->topic->getTitle() ?>
  </h2>
</div>
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addiscussionreply', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)): ?>
  <div class="layout_right" id="communityad_post">
		<?php
			echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addiscussionreply', 3),"loaded_by_ajax"=>1,'widgetId'=>'page_post')); 			 
		?>
  </div>
<?php endif; ?>
<div class="layout_middle">
  <?php if ($this->message)
    echo $this->message ?>
  <?php if ($this->form)
    echo $this->form->render($this) ?>
</div>