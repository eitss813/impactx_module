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

$coreSettings = Engine_Api::_()->getApi('settings', 'core');
$this->tinyMCESEAO()->addJS();
if (!empty($this->isModulesActivated)):
  foreach ($this->isModulesActivated as $modKey => $modValue) {
    $url = $this->url(array('module' => $modKey, 'controller' => 'settings', 'action' => 'readme'), 'admin_default', true);
    if ($modKey == 'sitepageinvite') {
      $url = $this->url(array('module' => $modKey, 'controller' => 'global', 'action' => 'readme'), 'admin_default', true);
    }
		$showMessage = Zend_Registry::get('Zend_Translate')->_('<div class="tip"><span>Note: You have installed the "%s" plugin but have not activated it on your site. Please activate it first. <a href=%s>Click here</a> to activate the "%s" plugin.</span></div>');
		$showMessage = sprintf($showMessage, $modValue, $url, $modValue );
		echo $showMessage;
  }
endif;

if( !empty($this->isModsSupport) ):
	foreach( $this->isModsSupport as $modName ) {
		echo $this->translate('<div class="tip"><span>Note: You do not have the latest version of the "%s". Please upgrade it to the latest version to enable its integration with Directory/Pages Plugin.</span></div>', ucfirst($modName));
	}
endif;
?>
 <?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<script type="text/javascript">
  if(document.getElementById('sitepage_map_city')) {
    window.addEvent('domready', function() {
      new google.maps.places.Autocomplete(document.getElementById('sitepage_map_city'));
    });
  }
</script>

<h2 class="fleft"><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/manageExtensions.tpl'; ?>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>
<?php if (count($this->subnavigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->subnavigation)->render() ?>
    </div>
<?php endif; ?>

<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_upgrade_messages.tpl'; ?>
<?php
$moduleName = 'documentintegration';
if (!isset($_COOKIE[$moduleName . '_dismiss'])):
    ?>
    <?php if (!Engine_Api::_()->hasModuleBootstrap('documentintegration')): ?>
        <div id="dismissintegration_modules">
            <div class="seaocore-notice">
                <div class="seaocore-notice-icon">
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
                </div>
                <div style="float:right;">
                    <button onclick="dismissintegration('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
                </div>
                <div class="seaocore-notice-text ">
                    <?php echo 'To set up a robust Documents System with <a href="https://www.socialengineaddons.com/socialengine-directory-pages-plugin">"Directory / Pages plugin"<a/>, you can purchase our awesome <a  target="_blank" href="https://www.socialengineaddons.com/socialengine-documents-product-kit">"Documents Sharing - Product Kit"</a>.'; ?>
                </div>	
            </div>
        </div>
    <?php else: ?>
<?php if(Engine_Api::_()->hasModuleBootstrap('document') && !Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))):?>
        <div id="dismissintegration_modules">
            <div class="seaocore-notice">
                <div class="seaocore-notice-icon">
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
                </div>
                <div style="float:right;">
                    <button onclick="dismissintegration('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
                </div>
                <div class="seaocore-notice-text ">
                    <?php echo 'You have installed <a href="https://www.socialengineaddons.com/documentextensions/socialengine-documents-sharing-pages-businesses-groups-listings-events-stores-extension" target="_blank">Documents- Pages, Businesses, Groups, Events, Stores, etc Extension</a> installed on your website. If you want to display documents using the Documents Plugin on your website so that all documents can be place all together then please <a  target="_blank" href="admin/documentintegration/modules">click here</a> to integrate it.'; ?>
                </div>	
            </div>
        </div>
 <?php endif; ?>
    <?php endif; ?>

<?php endif; ?>


<?php
$moduleName = 'sitevideointegration';
if (!isset($_COOKIE[$moduleName . '_dismiss'])):
    ?>
    <?php if (!Engine_Api::_()->hasModuleBootstrap('sitevideointegration')): ?>
        <div id="dismissintegration_modules">
            <div class="seaocore-notice">
                <div class="seaocore-notice-icon">
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
                </div>
                <div style="float:right;">
                    <button onclick="dismissintegration('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
                </div>
                <div class="seaocore-notice-text ">
                    <?php echo 'To set up a robust Videos System with <a href="https://www.socialengineaddons.com/socialengine-directory-pages-plugin">"Directory / Pages plugin"<a/>, you can purchase our awesome <a  target="_blank" href="https://www.socialengineaddons.com/socialengine-videos-product-kit">"Advanced Videos - Product Kit"</a>.'; ?>
                </div>	
            </div>
        </div>
    <?php else: ?>
<?php if(Engine_Api::_()->hasModuleBootstrap('sitevideo') && !Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))):?>
        <div id="dismissintegration_modules">
            <div class="seaocore-notice">
                <div class="seaocore-notice-icon">
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
                </div>
                <div style="float:right;">
                    <button onclick="dismissintegration('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
                </div>
                <div class="seaocore-notice-text ">
                    <?php echo 'You have installed <a href="https://www.socialengineaddons.com/videoextensions/socialengine-advanced-videos-pages-businesses-groups-listings-events-stores-extension" target="_blank">Advanced Videos - Pages, Businesses, Groups, Multiple Listing Types, Events, Stores, etc Extension</a> installed on your website. If you want to display videos using the Advanced Videos Plugin on your website so that all videos can be place all together then please <a  target="_blank" href="admin/sitevideointegration/modules">click here</a> to integrate it.'; ?>
                </div>	
            </div>
        </div>
 <?php endif; ?>
    <?php endif; ?>

<?php endif; ?>



<div>

<?php if (!$coreSettings->getSetting('seaocore.google.map.key')):  ?>
		<?php 
		 $URL_MAP = $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'map'), 'admin_default', true);
		echo $this->translate('<div class="tip"><span>Note: You have not entered Google Places API Key for your website. Please <a href="%s" target="_blank"> Click here </a></span></div>', $URL_MAP); ?>
<?php endif;  ?>

<?php if(!$this->hasLanguageDirectoryPermissions):?>
<div class="seaocore_tip">
  <span>
    <?php echo "Please log in over FTP and set CHMOD 0777 (recursive) on the application/languages/ directory for  change the pharse pages and page." ?>
  </span>
</div>
<?php endif; ?>

<div class='clear sitepage_settings_form'>
  <div class='settings'>
<?php echo $this->form->render($this); ?>
  </div>
</div>

<script type="text/javascript">
  var display_msg=0;
  window.addEvent('domready', function() {
   // $('translation_file').checked=false;
  //  toogleLaguagePhase('none');
    showlocationKM('<?php echo $coreSettings->getSetting('sitepage.proximitysearch', 1); ?>');	  showcategoryblock	('<?php echo $coreSettings->getSetting('sitepage.category.edit', 0); ?>');
    showclaim('<?php echo $coreSettings->getSetting('sitepage.claimlink', 1); ?>');
    showlocationOption('<?php echo $coreSettings->getSetting('sitepage.locationfield', 1); ?>');
        showOption('<?php echo $coreSettings->getSetting('sitepage.footer.enabled', 0); ?>');
        showcustomOption('<?php echo $coreSettings->getSetting('sitepage.package.view', 1); ?>');
        showpackageOption('<?php echo $coreSettings->getSetting('sitepage.package.enable', 1); ?>');
        showtipOption('<?php echo $coreSettings->getSetting('sitepage.tips.enable', 1); ?>');
    showDefaultNetwork('<?php echo $coreSettings->getSetting('sitepage.network', 0) ?>');
    showMapOptions('<?php echo $coreSettings->getSetting('sitepage.location', 1) ?>');
    display_msg=1;
  });

  //  HERE WE CREATE A FUNCTION FOR SHOWING THE LOCATION IN KM OR MILES
  function showpackageOption(option) {
    if($('sitepage_package_enable-wrapper')) {
      if(option == 1) {
        if($('sitepage_currency-wrapper'))
          $('sitepage_currency-wrapper').style.display='block';
        if($('sitepage_payment_benefit-wrapper'))
          $('sitepage_payment_benefit-wrapper').style.display='block';
          if($('sitepage_package_view-wrapper'))
          $('sitepage_package_view-wrapper').style.display='block';
      
          if($('sitepage_package_information-wrapper'))
          $('sitepage_package_information-wrapper').style.display='block'; 
          
          if(($('sitepage_package_view-2').checked)) {
              var daer = 2;
          showcustomOption(daer);
        }
          
      }
      else{
        if($('sitepage_currency-wrapper'))
          $('sitepage_currency-wrapper').style.display='none';
        if($('sitepage_payment_benefit-wrapper'))
          $('sitepage_payment_benefit-wrapper').style.display='none';
        if($('sitepage_package_view-wrapper'))
          $('sitepage_package_view-wrapper').style.display='none';
      
         if($('sitepage_package_information-wrapper'))
          $('sitepage_package_information-wrapper').style.display='none';
      
         if($('sitepage_footer_enabled-wrapper'))
          $('sitepage_footer_enabled-wrapper').style.display='none';
      
         if($('sitepage_footer_text-wrapper'))
          $('sitepage_footer_text-wrapper').style.display='none';
      }
    }
  }
  function showtipOption(option) {
    if($('sitepage_tips_enable-wrapper')) {
      if(option == 1) {
        if($('sitepage_tip-wrapper'))
          $('sitepage_tip-wrapper').style.display='block';
      }
      else{
        if($('sitepage_tip-wrapper'))
          $('sitepage_tip-wrapper').style.display='none';
      }
    }
  }

  en4.core.runonce.add(function() {
    document.forms[0].addEvent('submit', function() {
      if ($("sitepage_tip-wrapper").style.display === 'block' && !tinyMCE.editors['sitepage_tip'].getContent() ) {
        window.alert("Tips must be filled");
        return false;
      }
      return true;
    })
  })
function showcustomOption(option) {
      if(option == 2) {
          if($('sitepage_footer_enabled-wrapper'))
          $('sitepage_footer_enabled-wrapper').style.display='block';
          
          if($('sitepage_footer_text-wrapper')) {
              var truth = $('sitepage_footer_enabled').value;
              showOption(truth);
          }

      }
      else{
          
          if($('sitepage_footer_enabled-wrapper'))
          $('sitepage_footer_enabled-wrapper').style.display='none';
      
         if($('sitepage_footer_text-wrapper'))
          $('sitepage_footer_text-wrapper').style.display='none';
      }
  }

function showOption(option) {
                  if(option == 1) {
                        $('sitepage_footer_text-wrapper').style.display='block';
                  }
                  else {
                      $('sitepage_footer_text-wrapper').style.display='none';
                  }
}

  function showlocationOption(option) {

		
    if(option == 1) {
      if($('sitepage_location-wrapper'))
        $('sitepage_location-wrapper').style.display='block';
      if($('sitepage_proximitysearch-wrapper'))
        $('sitepage_proximitysearch-wrapper').style.display='block';
      if($('sitepage_proximitysearch-1'))
        if($('sitepage_proximitysearch-1').checked)
          showlocationKM(1);
      else
        showlocationKM(0);
      if($('sitepage_location-1'))
        if($('sitepage_location-1').checked)
          showMapOptions(1);
      else
        showMapOptions(0);
              if($('sitepage_multiple_location-wrapper'))
        $('sitepage_multiple_location-wrapper').style.display='block';
    }
    else{
      if($('sitepage_location-wrapper'))
        $('sitepage_location-wrapper').style.display='none';
      if($('sitepage_proximitysearch-wrapper'))
        $('sitepage_proximitysearch-wrapper').style.display='none';
      if($('sitepage_proximity_search_kilometer-wrapper'))
        $('sitepage_proximity_search_kilometer-wrapper').style.display='none';
              if($('sitepage_multiple_location-wrapper'))
        $('sitepage_multiple_location-wrapper').style.display='none';
      showMapOptions(0);
    }
		
  }
  //  HERE WE CREATE A FUNCTION FOR SHOWING THE LOCATION IN KM OR MILES
  function showlocationKM(option) {
    if($('sitepage_proximity_search_kilometer-wrapper')) {
      if(option == 1) {
        if($('sitepage_proximity_search_kilometer-wrapper'))
          $('sitepage_proximity_search_kilometer-wrapper').style.display='block';	
      }
      else{
        if($('sitepage_proximity_search_kilometer-wrapper'))
          $('sitepage_proximity_search_kilometer-wrapper').style.display='none';
      }
    }
  }

  //  HERE WE CREATE A FUNCTION FOR SHOWING BOUNCING
  function showMapOptions(option) {
    if($('sitepage_location-wrapper')) {
      if(option == 1) {
        if($('sitepage_map_sponsored-wrapper'))
          $('sitepage_map_sponsored-wrapper').style.display='block';
          if($('sitepage_map_zoom-wrapper'))
						$('sitepage_map_zoom-wrapper').style.display='block';
           if($('sitepage_map_city-wrapper'))
						$('sitepage_map_city-wrapper').style.display='block';
      }
      else{
        if($('sitepage_map_sponsored-wrapper'))
          $('sitepage_map_sponsored-wrapper').style.display='none';
         if($('sitepage_map_zoom-wrapper'))
					$('sitepage_map_zoom-wrapper').style.display='none';
         if($('sitepage_map_city-wrapper'))
					$('sitepage_map_city-wrapper').style.display='none';
      }
    }
  }

  function showclaim(option) 
  {
    if($('sitepage_claim_show_menu-wrapper')) {
      if(option == 1) { 
        $('sitepage_claim_show_menu-wrapper').style.display='block';	
      }
      else{
        $('sitepage_claim_show_menu-wrapper').style.display='none';
      }		
    }
    if($('sitepage_claim_email-wrapper')) {
      if(option == 1) { 
        $('sitepage_claim_email-wrapper').style.display='block';	
      }
      else{
        $('sitepage_claim_email-wrapper').style.display='none';
      }		
    }
  }

  function showcategoryblock(option)
  {
    if(option == 1 && display_msg == 1) {
      alert("<?php echo $this->string()->escapeJavascript($this->translate('After giving this permission members can edit categories but it can cause content loss like reviews rating data, profile type details etc.')) ?>");
    }
  }
  function showDefaultNetwork(option) {
    if($('sitepage_default_show-wrapper')) {
      if(option == 0) {
        $('sitepage_default_show-wrapper').style.display='block';        
        showDefaultNetworkType($('sitepage_default_show-1').checked);
        $('sitepage_privacybase-wrapper').style.display='block';
         $('sitepage_networkprofile_privacy-wrapper').style.display='none';
      }else{
        showDefaultNetworkType(1);
        $('sitepage_default_show-wrapper').style.display='none';
        $('sitepage_privacybase-wrapper').style.display='none';
        $('sitepage_networkprofile_privacy-wrapper').style.display='block';
      }
    }
  }

  function showDefaultNetworkType(option) {
    if($('sitepage_networks_type-wrapper')) {
      if(option == 1) {
        $('sitepage_networks_type-wrapper').style.display='block';
      }else{
        $('sitepage_networks_type-wrapper').style.display='none';
      }
    }
  }
  
//   function showUpdateWarning(){
//     if( $('translation_file').checked){
//       var r=confirm("Are you sure that you want to replace language files for Directory / Pages Plugin and Directory / Pages Plugin Extension installed at your site?");
//       if (r==false)
//       {
//         $('translation_file').checked=false;
//       }
//     }
//     
//     if($('translation_file').checked)
//       toogleLaguagePhase('block');
//     else
//       toogleLaguagePhase('none');
//   }
/*  
  function toogleLaguagePhase(display){
    <?php // $elements = Engine_Api::_()->getApi('language', 'sitepage')->getDataWithoutKeyPhase();
    //foreach($elements as $key=>$element):?>
			if($('<?php // echo $key ?>-wrapper'))
        $('<?php //echo $key ?>-wrapper').style.display=display;
    <?php //endforeach; ?>
  }*/
</script>

<script type="text/javascript">
    function dismissintegration(modName) {
        var d = new Date();
        // Expire after 1 Year.
        d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toGMTString();
        document.cookie = modName + "_dismiss" + "=" + 1 + "; " + expires;
        $('dismissintegration_modules').style.display = 'none';
    }

</script>


<style type="text/css">
.settings div.form-element .description {
    min-width: 100%;
}
</style>


<script type="text/javascript">
    if($('sitepage_footer_text-wrapper')) {
        tinyMCE.init({
            mode: "exact",
            elements : "sitepage_footer_text",
            menubar: false,
            theme: "modern",
            plugins: "table,fullscreen,media,preview,paste,code,textcolor,link",
            toolbar1: "undo,redo,removeformat,pastetext,|,code,link, fullscreen,preview,|,bullist,numlist",
            toolbar2: "fontselect,fontsizeselect,bold,italic,underline,strikethrough,forecolor,backcolor,|,alignleft,aligncenter,alignright,alignjustify,|,outdent,indent,blockquote",
            height: "225px",
        });
    }
</script>
