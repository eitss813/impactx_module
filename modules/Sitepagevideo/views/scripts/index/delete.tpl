<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.advideodelete', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)): ?>
  <div class="layout_right" id="communityad_videodelete">

		<?php
			echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.advideodelete', 3),"loaded_by_ajax"=>0,'widgetId'=>"page_videodelete")); 			 
		?>
  </div>
<?php endif; ?>
<div class="layout_middle">
  <div class="sitepage_viewpages_head">
    <?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
    <h2>	
      <?php echo $this->sitepage->__toString() ?>	
      <?php echo $this->translate('&raquo; '); ?>
      <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Videos')) ?>
    </h2>
  </div>
  <div class='global_form'>
    <form method="post" class="global_form">
      <div>
        <div>
          <h3><?php echo $this->translate('Delete Page Video ?'); ?></h3>
          <p> 
            <?php echo $this->translate('Are you sure that you want to delete the page video titled "%1$s" last modified %2$s? It will not be recoverable after being deleted.', $this->title, $this->timestamp($this->sitepagevideo->modified_date)) ?>
          </p>
          <br />
          <p>
            <input type="hidden" name="confirm" value="true"/>
            <button type='submit' ><?php echo $this->translate('Delete'); ?></button>
            	<?php echo $this->translate('or'); ?> <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)),$this->translate('cancel')) ?>
          </p>
        </div>
      </div>
    </form>
  </div>
</div>	