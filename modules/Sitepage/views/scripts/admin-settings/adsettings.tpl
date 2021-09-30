<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: adsettings.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2 class="fleft"><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/manageExtensions.tpl'; ?>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>

<div class='clear sitepage_settings_form'>
  <div class='settings'> 
    <?php echo $this->form->render($this); ?>
  </div>
</div> 

<script type="text/javascript">
//var show_community_ad = '<?php //echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1); ?>';
  window.addEvent('domready', function() {
    showads('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1); ?>');
    //showlightboxads('<?php //echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.lightboxads', 1); ?>');
  });

//   function showlightboxads(option) {
//     if(show_community_ad == 1) {
// 			if($('sitepage_adtype-wrapper')) {
// 				if(option == 1) {
// 					$('sitepage_adtype-wrapper').style.display = 'block';
// 				}
// 				else {
// 					$('sitepage_adtype-wrapper').style.display = 'none';
// 				}
// 			}
// 		}
//   }

  function showads(option) {	 	
    if(option == 1) {
//       if($('sitepage_lightboxads-wrapper')) {
//         $('sitepage_lightboxads-wrapper').style.display = 'block';
//         if($('sitepage_lightboxads-1').checked) {
//           $('sitepage_adtype-wrapper').style.display = 'block';
//         }
//         else {
//           $('sitepage_adtype-wrapper').style.display = 'none';
//         }
//       }
			if($('sitepage_admylikes-wrapper')) {
				$('sitepage_admylikes-wrapper').style.display = 'block';
			}
      if(<?php echo $this->isnoteenabled ?>) {
        $('sitepage_adnotewidget-wrapper').style.display = 'block';
        $('sitepage_adnoteview-wrapper').style.display = 'block';
        $('sitepage_adnotebrowse-wrapper').style.display = 'block';
        $('sitepage_adnotecreate-wrapper').style.display = 'block';
        $('sitepage_adnoteedit-wrapper').style.display = 'block';
        $('sitepage_adnotedelete-wrapper').style.display = 'block';
        $('sitepage_adnoteaddphoto-wrapper').style.display = 'block';
        $('sitepage_adnoteeditphoto-wrapper').style.display = 'block';
        $('sitepage_adnotesuccess-wrapper').style.display = 'block';
      }
		  
      if(<?php echo $this->iseventenabled ?>) {
        $('sitepage_adeventwidget-wrapper').style.display = 'block';
        $('sitepage_adeventcreate-wrapper').style.display = 'block';
        $('sitepage_adeventedit-wrapper').style.display = 'block';
        $('sitepage_adeventdelete-wrapper').style.display = 'block';
        $('sitepage_adeventview-wrapper').style.display = 'block';
        $('sitepage_adeventbrowse-wrapper').style.display = 'block';
        	$('sitepage_adeventaddphoto-wrapper').style.display = 'block';
				$('sitepage_adeventeditphoto-wrapper').style.display = 'block';
      }
		  
      if(<?php echo $this->isalbumenabled ?>) {
        $('sitepage_adalbumwidget-wrapper').style.display = 'block';
        $('sitepage_adalbumview-wrapper').style.display = 'block';
         $('sitepage_adalbumbrowse-wrapper').style.display = 'block';
        $('sitepage_adalbumcreate-wrapper').style.display = 'block';
        $('sitepage_adalbumeditphoto-wrapper').style.display = 'block';
      }
		  
      if(<?php echo $this->isdiscussionenabled ?>) {
        $('sitepage_addicussionwidget-wrapper').style.display = 'block';
        $('sitepage_addiscussionview-wrapper').style.display = 'block';
        $('sitepage_addiscussioncreate-wrapper').style.display = 'block';
        $('sitepage_addiscussionreply-wrapper').style.display = 'block';				
      }
	
      if(<?php echo $this->isdocumentenabled ?>) {
        $('sitepage_addocumentwidget-wrapper').style.display = 'block';
        $('sitepage_addocumentview-wrapper').style.display = 'block';
        $('sitepage_addocumentbrowse-wrapper').style.display = 'block';
        $('sitepage_addocumentcreate-wrapper').style.display = 'block';
        $('sitepage_addocumentedit-wrapper').style.display = 'block';
        $('sitepage_addocumentdelete-wrapper').style.display = 'block';		 		
      }
		  
      if(<?php echo $this->isvideoenabled ?>) {
        $('sitepage_advideowidget-wrapper').style.display = 'block';
        $('sitepage_advideoview-wrapper').style.display = 'block';
        $('sitepage_advideobrowse-wrapper').style.display = 'block';
        $('sitepage_advideocreate-wrapper').style.display = 'block';
        $('sitepage_advideoedit-wrapper').style.display = 'block';
        $('sitepage_advideodelete-wrapper').style.display = 'block';			 		
      }
		  
      if(<?php echo $this->ispollenabled ?>) {
        $('sitepage_adpollwidget-wrapper').style.display = 'block';
        $('sitepage_adpollview-wrapper').style.display = 'block';
        $('sitepage_adpollbrowse-wrapper').style.display = 'block';
        $('sitepage_adpollcreate-wrapper').style.display = 'block';
        $('sitepage_adpolldelete-wrapper').style.display = 'block';
      }
	
      if(<?php echo $this->isreviewenabled ?>) {
        $('sitepage_adreviewwidget-wrapper').style.display = 'block';
        $('sitepage_adreviewcreate-wrapper').style.display = 'block';
        $('sitepage_adreviewedit-wrapper').style.display = 'block';
        $('sitepage_adreviewdelete-wrapper').style.display = 'block';			
        $('sitepage_adreviewview-wrapper').style.display = 'block';	
        $('sitepage_adreviewbrowse-wrapper').style.display = 'block';		
      }
		  
      if(<?php echo $this->isofferenabled ?>) {
        $('sitepage_adofferwidget-wrapper').style.display = 'block';		
        $('sitepage_adofferpage-wrapper').style.display = 'block';
        $('sitepage_adofferlist-wrapper').style.display = 'block';
      }
			
      if(<?php echo $this->isformenabled ?>) {
        $('sitepage_adformwidget-wrapper').style.display = 'block';
        $('sitepage_adformcreate-wrapper').style.display = 'block';
      }
	
      if(<?php echo $this->isinviteenabled ?>) {
        $('sitepage_adinvite-wrapper').style.display = 'block';
      }
			
      if(<?php echo $this->isbadgeenabled ?>) {
        $('sitepage_adbadgeview-wrapper').style.display = 'block';
      }		
			
      if(<?php echo $this->ismoduleenabled ?>) {
        $('sitepage_adlocationwidget-wrapper').style.display = 'block';
        $('sitepage_adoverviewwidget-wrapper').style.display = 'block';
        $('sitepage_adinfowidget-wrapper').style.display = 'block';
        $('sitepage_adclaimview-wrapper').style.display = 'block';
        $('sitepage_adtagview-wrapper').style.display = 'block';
      }		
      
      if(<?php echo $this->ismusicenabled ?>) {
        $('sitepage_admusicwidget-wrapper').style.display = 'block'; 	
        $('sitepage_admusicview-wrapper').style.display = 'block';
        $('sitepage_admusicbrowse-wrapper').style.display = 'block';
        $('sitepage_admusiccreate-wrapper').style.display = 'block';
        $('sitepage_admusicedit-wrapper').style.display = 'block';
      }
      
	    //START FOR INRAGRATION WORK WITH OTHER PLUGIN.
      if(<?php echo $this->issitepageintegrationenabled ?>) {
       <?php if(!empty($this->mixSettingsResults)):?>
					<?php	foreach($this->mixSettingsResults as $modNameValue) { ?>
						$('sitepage_ad_<?php echo $modNameValue['resource_type'] . '_' . $modNameValue['listingtype_id'] ?>-wrapper').style.display = 'block';
					<?php  } ?>
       <?php endif;?>
      }
   	  //END FOR INRAGRATION WORK WITH OTHER PLUGIN.
      if(<?php echo $this->istwitterenabled ?>) {
        $('sitepage_adtwitterwidget-wrapper').style.display = 'block'; 	
      }
			if(<?php echo $this->ismemberenabled ?>) {
        $('sitepage_admemberwidget-wrapper').style.display = 'block'; 	
        $('sitepage_admemberbrowse-wrapper').style.display = 'block'; 	
      }

    } 
    else {
      if($('sitepage_lightboxads-wrapper')) {
        $('sitepage_lightboxads-wrapper').style.display = 'none';
        $('sitepage_adtype-wrapper').style.display = 'none';
      }
			if($('sitepage_admylikes-wrapper')) {
				$('sitepage_admylikes-wrapper').style.display = 'none';
			}
      if(<?php echo $this->isnoteenabled ?>) {
        $('sitepage_adnotewidget-wrapper').style.display = 'none';
        $('sitepage_adnoteview-wrapper').style.display = 'none';
        $('sitepage_adnotebrowse-wrapper').style.display = 'none';
        $('sitepage_adnotecreate-wrapper').style.display = 'none';
        $('sitepage_adnoteedit-wrapper').style.display = 'none';
        $('sitepage_adnotedelete-wrapper').style.display = 'none';
        $('sitepage_adnoteaddphoto-wrapper').style.display = 'none';
        $('sitepage_adnoteeditphoto-wrapper').style.display = 'none';
        $('sitepage_adnotesuccess-wrapper').style.display = 'none';
      }
			  
      if(<?php echo $this->iseventenabled ?>) {
        $('sitepage_adeventwidget-wrapper').style.display = 'none';
        $('sitepage_adeventcreate-wrapper').style.display = 'none';
        $('sitepage_adeventedit-wrapper').style.display = 'none';
        $('sitepage_adeventdelete-wrapper').style.display = 'none';
        $('sitepage_adeventview-wrapper').style.display = 'none';
        $('sitepage_adeventbrowse-wrapper').style.display = 'none';
        $('sitepage_adeventaddphoto-wrapper').style.display = 'none';
				$('sitepage_adeventeditphoto-wrapper').style.display = 'none';
      }
		  
      if(<?php echo $this->isalbumenabled ?>) {
        $('sitepage_adalbumwidget-wrapper').style.display = 'none';
        $('sitepage_adalbumview-wrapper').style.display = 'none';
        $('sitepage_adalbumbrowse-wrapper').style.display = 'none';
        $('sitepage_adalbumcreate-wrapper').style.display = 'none';
        $('sitepage_adalbumeditphoto-wrapper').style.display = 'none';
      }
			  
      if(<?php echo $this->isdiscussionenabled ?>) {
        $('sitepage_addicussionwidget-wrapper').style.display = 'none';
        $('sitepage_addiscussionview-wrapper').style.display = 'none';
        $('sitepage_addiscussioncreate-wrapper').style.display = 'none';
        $('sitepage_addiscussionreply-wrapper').style.display = 'none';								
      }
		
      if(<?php echo $this->isdocumentenabled ?>) {
        $('sitepage_addocumentwidget-wrapper').style.display = 'none';
        $('sitepage_addocumentview-wrapper').style.display = 'none';
        $('sitepage_addocumentbrowse-wrapper').style.display = 'none';
        $('sitepage_addocumentcreate-wrapper').style.display = 'none';
        $('sitepage_addocumentedit-wrapper').style.display = 'none';
        $('sitepage_addocumentdelete-wrapper').style.display = 'none';
      }
			  
      if(<?php echo $this->isvideoenabled ?>) {
        $('sitepage_advideowidget-wrapper').style.display = 'none';
        $('sitepage_advideoview-wrapper').style.display = 'none';
        $('sitepage_advideobrowse-wrapper').style.display = 'none';
        $('sitepage_advideocreate-wrapper').style.display = 'none';
        $('sitepage_advideoedit-wrapper').style.display = 'none';
        $('sitepage_advideodelete-wrapper').style.display = 'none';			 		
      }
			  
      if(<?php echo $this->ispollenabled ?>) {
        $('sitepage_adpollwidget-wrapper').style.display = 'none';
        $('sitepage_adpollview-wrapper').style.display = 'none';
        $('sitepage_adpollbrowse-wrapper').style.display = 'none';
        $('sitepage_adpollcreate-wrapper').style.display = 'none';
        $('sitepage_adpolldelete-wrapper').style.display = 'none';
      }
		
      if(<?php echo $this->isreviewenabled ?>) {
        $('sitepage_adreviewwidget-wrapper').style.display = 'none';
        $('sitepage_adreviewcreate-wrapper').style.display = 'none';
        $('sitepage_adreviewedit-wrapper').style.display = 'none';
        $('sitepage_adreviewdelete-wrapper').style.display = 'none';
        $('sitepage_adreviewview-wrapper').style.display = 'none';
        $('sitepage_adreviewbrowse-wrapper').style.display = 'none';						
      }
		  
      if(<?php echo $this->isofferenabled ?>) {
        $('sitepage_adofferwidget-wrapper').style.display = 'none';		
        $('sitepage_adofferpage-wrapper').style.display = 'none';
        $('sitepage_adofferlist-wrapper').style.display = 'none';
      }
		  			
      if(<?php echo $this->isformenabled ?>) {
        $('sitepage_adformwidget-wrapper').style.display = 'none';
        $('sitepage_adformcreate-wrapper').style.display = 'none';
      }
	
      if(<?php echo $this->isinviteenabled ?>) {
        $('sitepage_adinvite-wrapper').style.display = 'none';
      }
			
      if(<?php echo $this->isbadgeenabled ?>) {
        $('sitepage_adbadgeview-wrapper').style.display = 'none';
      }			
				
      if(<?php echo $this->ismoduleenabled ?>) {
        $('sitepage_adlocationwidget-wrapper').style.display = 'none';
        $('sitepage_adoverviewwidget-wrapper').style.display = 'none';
        $('sitepage_adinfowidget-wrapper').style.display = 'none';
        $('sitepage_adclaimview-wrapper').style.display = 'none';
        $('sitepage_adtagview-wrapper').style.display = 'none';
      }
      
      if(<?php echo $this->ismusicenabled ?>) {
        $('sitepage_admusicwidget-wrapper').style.display = 'none'; 	
        $('sitepage_admusicview-wrapper').style.display = 'none';
        $('sitepage_admusicbrowse-wrapper').style.display = 'none';
        $('sitepage_admusiccreate-wrapper').style.display = 'none';
        $('sitepage_admusicedit-wrapper').style.display = 'none';
       }

	    //START FOR INRAGRATION WORK WITH OTHER PLUGIN.
      if(<?php echo $this->issitepageintegrationenabled ?>) {
       <?php if(!empty($this->mixSettingsResults)):?>
					<?php	foreach($this->mixSettingsResults as $modNameValue) { ?>
						$('sitepage_ad_<?php echo $modNameValue['resource_type'] . '_' . $modNameValue['listingtype_id'] ?>-wrapper').style.display = 'none';
					<?php  } ?>
       <?php endif;?>
      }

			if(<?php echo $this->ismemberenabled ?>) {
        $('sitepage_admemberwidget-wrapper').style.display = 'none'; 	
        $('sitepage_admemberbrowse-wrapper').style.display = 'none'; 	
      }

	    //END FOR INRAGRATION WORK WITH OTHER PLUGIN.
			if(<?php echo $this->istwitterenabled ?>) {
        $('sitepage_adtwitterwidget-wrapper').style.display = 'none'; 	
      }
    } 	
  } 
</script>
