<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: uploadalbum.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepagealbum/externals/styles/style_sitepagealbum.css');
?>
<script type="text/javascript">
  var updateTextFields = function()
  {
    var album = document.getElementById("album");
    var name = document.getElementById("title-wrapper");
    var auth_tag = document.getElementById("auth_tag-wrapper");

    if (album.value == 0)
    {
      name.style.display = "block";
      auth_tag.style.display = "block";
    }
    else
    {
      name.style.display = "none";
      auth_tag.style.display = "none";
    }
  }
  en4.core.runonce.add(updateTextFields);
  
  var album_id = '<?php echo $this->album_id ?>';
  var page_id = '<?php echo $this->sitepage->page_id; ?>';
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>

<?php $albumid = Zend_Controller_Front::getInstance()->getRequest()->getParam('album_id', null); ?>
<?php if (!empty($albumid)): ?>
  <?php $albums = Engine_Api::_()->getItem('sitepage_album', $albumid); ?>
<?php endif; ?>
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
    <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Albums')) ?>
    <?php if (!empty($albumid) && empty($this->can_edit)) : ?>
      <?php echo $this->translate('&raquo; '); ?>
      <?php echo $albums->title; ?>
    <?php endif; ?>
  </h2>  
</div>	
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adalbumcreate', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)): ?>
  <div class="layout_right" id="communityad_uploadalbum">
    <?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adalbumcreate', 3),"loaded_by_ajax"=>1,'widgetId'=>'page_uploadalbum'))?>
  </div>
<?php endif; ?>
<div class="layout_middle">
  <?php echo $this->form->render($this) ?>
</div>	