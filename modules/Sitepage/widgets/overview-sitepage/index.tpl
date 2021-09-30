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
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>

<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage_profile.css')
?>


<?php 
	$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/hideWidgets.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/hideTabs.js');
?>
<?php //if (empty($this->isajax)) : ?>
	<div id="id_<?php echo $this->content_id; ?>">
<?php //endif;?>

<?php //if (!empty($this->show_content)) : ?>
	<?php if($this->showtoptitle == 1):?>
		<div class="layout_simple_head" id="layout_overview">
			<?php echo $this->translate($this->sitepage->getTitle(). "'s ");?><?php echo $this->translate("Overview");?>
		</div>
	<?php endif;?>
		<div class="generic_layout_container">
			<h3>
				<?php echo "Overview"; ?>
			</h3>
		</div>
		</br>
		<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adoverviewwidget', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)) : ?>
			<div class="layout_right" id="communityad_overview">
              <?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adoverviewwidget', 3),"loaded_by_ajax"=>0,'widgetId'=>'page_overview'))?>
			</div>
			<div class="layout_middle">
		<?php endif;?>
		<?php if ($this->can_edit && $this->can_edit_overview):?>
			<?php if(!empty($this->sitepage->overview)):?>
				<div class="seaocore_add">
					<a href='<?php echo $this->url(array('page_id' => $this->sitepage->page_id, 'action' => 'overview'), 'sitepage_dashboard', true) ?>'  class="icon_sitepages_overview buttonlink"><?php echo $this->translate('Edit Overview'); ?></a>
				</div>
			<?php endif;?>
		<?php endif;?>
	<div>

	<?php if(!empty($this->sitepage->overview)):?>
		<?php echo $this->viewMore(nl2br($this->sitepage->overview),500,5000) ?>
		<?php else:?>
		<div class="tip">
			<span>
				<?php   echo $this->translate("No overview has been composed for this Page yet.");?>
				<?php if($this->can_edit && $this->can_edit_overview):?>
					<?php   echo $this->translate("Click ").$this->htmlLink(
										array('route' => 'sitepage_dashboard', 'action' => 'overview','page_id' => $this->sitepage->page_id),
										$this->translate('here')

									).  $this->translate(" to compose it.");?>
				<?php endif; ?>
			</span>
		</div>
	<?php endif;?>
</div>
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adoverviewwidget', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)) : ?>
</div>
<?php endif;?>
<?php //endif;?>
<?php //if (empty($this->isajax)) : ?>
	</div>
<?php //endif;?>
<script type="text/javascript">
    var page_communityad_integration = '<?php echo $page_communityad_integration; ?>';
    var show_widgets = '<?php echo $this->widgets ?>';
    var adwithoutpackage = '<?php echo Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage) ?>';
		var page_communityads;
    var contentinformtion;
    var page_showtitle;
    var overview_ads_display = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adoverviewwidget', 3);?>';

    $$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function(event) {
      var globalContentElement = en4.seaocore.getDomElements('content');
    	$(globalContentElement).getElement('.layout_sitepage_overview_sitepage').style.display = 'block';
     
   	  if(page_showtitle != 0) {
   	  	if($('profile_status') && show_widgets == 1) {
	  	    $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitepage->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Overview');?></h2>";	
   	  	}	
   	  }
      hideWidgetsForModule('sitepageoverview');
      $('id_' + <?php echo $this->content_id ?>).style.display = "block";
	    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
	      $$('.'+ prev_tab_class).setStyle('display', 'none');      
	    }


	    prev_tab_id = '<?php echo $this->content_id; ?>';	
	  	prev_tab_class = 'layout_sitepage_overview_sitepage';

	    if(page_showtitle == 1 && page_communityads == 1 && overview_ads_display != 0 && page_communityad_integration != 0 && adwithoutpackage != 0) {
				setLeftLayoutForPage();    	
	    } else if(page_showtitle == 0 && page_communityads == 1 && overview_ads_display != 0 && page_communityad_integration != 0 && adwithoutpackage != 0) {
				setLeftLayoutForPage();
	    }

	    if(page_communityads == 1 && overview_ads_display == 0 ) {
				setLeftLayoutForPage();   	   	
	    }
	     if($(event.target).get('tag') !='div' && ($(event.target).getParent('.layout_sitepage_overview_sitepage')==null)){
         scrollToTopForPage($(globalContentElement).getElement(".layout_sitepage_overview_sitepage"));
       }	        
   });
 
</script>
<style>
	a.view_more_link
	{
		background: #44AEC1;
		color: #fff;
		border-radius: 5px;
		padding: 5px;
	}
	a.view_less_link {
		display: none !important;
	}
</style>