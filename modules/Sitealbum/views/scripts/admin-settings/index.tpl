<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/scripts/mooRainbow.js');

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/mooRainbow.css');

if (!empty($this->isModsSupport)):
  foreach ($this->isModsSupport as $modName) {
    echo "<div class='tip' style='position:relative;'><span>" . $this->translate("Note: You do not have the latest version of the '%s'. Please upgrade it to the latest version to enable its integration with Advanced Photo Albums Plugin.", ucfirst($modName)) . "</span></div>";
  }
endif;
?>

<h2>
  <?php echo $this->translate("Advanced Photo Albums Plugin") ?>
</h2>

<?php if (count($this->navigation)): ?>

  <div class='seaocore_admin_tabs clr'>

    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
  <?php
  $url = '';
  $desc = '';
  if (empty($this->content_id)) {
    $url = $this->layout()->staticBaseUrl . 'admin/content?page=' . $this->page_id;
    $desc = $this->translate("The \"Photo Lightbox Viewer\" widget has been removed from the Layout Editor. Please <a href='$url' target='_blank'>click here</a> to place this widget in the Site Header for enabling the \"Photo Lightbox Viewer\" for your site.");
    echo "<ul class='form-errors'><li><ul class='errors'><li>$desc</li></ul></li></ul>";
  }
  ?>
<?php endif; ?>
<?php if (Engine_Api::_()->hasModuleBootstrap('sitetagcheckin') && ($this->results->toArray() || $this->resultss->toArray()) ):  ?>
    <div class="tip">
        <span> 
           <?php echo 'To integrate albums / photos locations with our Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin, please <a href="' . $this->url(array('action' => 'sink-location')) . '" class="smoothbox">click here</a>.'; ?>
        </span>
    </div>
<?php endif;  ?>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin') && !empty($this->syncAlbumCount)): ?> <br />
  <div class="tip">
      <span> 
           <?php echo 'To need to integrate our Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin with albums / photos locations, please <a href="' . $this->url(array('action' => 'sinkcheckinlocation')) . '" class="smoothbox">click here</a>.'; ?>
       </span>
  </div>
<?php endif; ?>

<?php $coreSettings = Engine_Api::_()->getApi('settings', 'core');?>

<?php if ( $coreSettings->getSetting('sitealbum.isActivate')) : ?>
    <script type="text/javascript">
        function dismissmessage(modName) {
            var d = new Date();
            // Expire after 1 Year.
            d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
            var expires = "expires=" + d.toGMTString();
            document.cookie = modName + "_dismiss" + "=" + 1 + "; " + expires;
            $('dismiss_modules').style.display = 'none';
        }
    </script>

    <?php
    $moduleName = 'sitealbum';
    if (!isset($_COOKIE[$moduleName . '_dismiss'])):
        ?>
        <div id="dismiss_modules">
            <div class="seaocore-notice">
                <div class="seaocore-notice-icon">
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
                </div>
                <div style="float:right;">
                    <button onclick="dismissmessage('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
                </div>
                <div class="seaocore-notice-text ">
                    <?php echo 'To change the layout of pages like: Albums Home, Browse Albums, Browse Photos, etc. please visit <a href="admin/sitealbum/settings/set-template">Layout Templates</a> tab of this plugin, select the desired pages and change the layout with a new and attractive user interface.'; ?>
                </div>	
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="clear seaocore_settings_form">
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>



<script type="text/javascript">

  window.addEvent('domready', function() {
    showDefaultNetwork('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.network', 0) ?>');
  });

  function showDefaultNetwork(option) {
    if ($('sitealbum_default_show-wrapper')) {
      if (option == 0) {
        $('sitealbum_default_show-wrapper').style.display = 'block';
        showDefaultNetworkType($('sitealbum_default_show-1').checked);
        //$('sitealbum_privacybase-wrapper').style.display = 'block';
        $('sitealbum_networkprofile_privacy-wrapper').style.display = 'none';
      } else {
        showDefaultNetworkType(1);
        $('sitealbum_default_show-wrapper').style.display = 'none';
       // $('sitealbum_privacybase-wrapper').style.display = 'none';
        $('sitealbum_networkprofile_privacy-wrapper').style.display = 'block';
      }
    }
  }

  function showDefaultNetworkType(option) {
    if ($('sitealbum_networks_type-wrapper')) {
      if (option == 1) {
        $('sitealbum_networks_type-wrapper').style.display = 'block';
      } else {
        $('sitealbum_networks_type-wrapper').style.display = 'none';
      }
    }
  }

</script>





<script type="text/javascript">
  window.addEvent('domready', function() {
    showads('<?php echo Engine_Api::_()->sitealbum()->showLightBoxPhoto() ?>');
  });

  function showlightboxads(option) {
    if ($('sitealbum_adtype-wrapper')) {
      if (option == 0) {
        $('sitealbum_adtype-wrapper').style.display = 'none';
      }
      else {
        $('sitealbum_adtype-wrapper').style.display = 'block';

      }
    }
  }

  function showads(option) {
    if (option == 1) {
      if ($('sitealbum_lightboxads-wrapper')) {
        $('sitealbum_lightboxads-wrapper').style.display = 'block';
        showlightboxads($('sitealbum_lightboxads-1').checked);
      }
    }
    else {
      if ($('sitealbum_lightboxads-wrapper')) {
        $('sitealbum_lightboxads-wrapper').style.display = 'none';
        showlightboxads(0);
      }
    }
  }

  function showwarning(option)
  {
    if (option == 1) {
      Smoothbox.open('<div style="padding: 5px;margin-right:px;"><?php echo $this->string()->escapeJavascript($this->translate('If you have previously made any changes mentioned in \'FAQ\' > \'Customize\' section in FAQ 1 or 2, then please remove the changes done in code as mentioned in Step 3 there. Please click ')) . $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitealbum', 'controller' => 'settings', 'action' => 'faq', 'faq_type' => 'customize'), $this->translate('here'), array('target' => "blank", "style" => "color:#5F93B4;")) . $this->string()->escapeJavascript($this->translate(' to follow further process.')) ?></div>');
    } else {
      Smoothbox.open('<div style="padding: 5px; margin-right:5px;"><?php echo $this->string()->escapeJavascript($this->translate('If you do not want to show these albums on Browse Albums Page and Member Profile Page also, then please click ')) . $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitealbum', 'controller' => 'settings', 'action' => 'faq', 'faq_type' => 'customize'), $this->translate('here'), array('target' => "blank", "style" => "color:#5F93B4;")) . $this->string()->escapeJavascript($this->translate(' to follow further process.')) ?></div>');
    }
  }

  window.addEvent('domready', function() {
    showProximitySearchSetting('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1) ?>');
    showUpdateratingSetting('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.rating', 1) ?>');

  });
  function showProximitySearchSetting(options) {

    if (options == 0) {
      if ($('sitealbum_proximity_search_kilometer-wrapper'))
        $('sitealbum_proximity_search_kilometer-wrapper').style.display = 'none';
      if ($('seaocore_locationdefault-wrapper'))
        $('seaocore_locationdefault-wrapper').style.display = 'none';
      if ($('seaocore_locationdefaultmiles-wrapper'))
        $('seaocore_locationdefaultmiles-wrapper').style.display = 'none';
    }
    else {
      if ($('sitealbum_proximity_search_kilometer-wrapper'))
        $('sitealbum_proximity_search_kilometer-wrapper').style.display = 'block';
      if ($('seaocore_locationdefault-wrapper'))
        $('seaocore_locationdefault-wrapper').style.display = 'block';
      if ($('seaocore_locationdefaultmiles-wrapper'))
        $('seaocore_locationdefaultmiles-wrapper').style.display = 'block';
    }
  }

function showUpdateratingSetting(options) {
    if (options == 0) {
      if ($('sitealbumrating_update-wrapper'))
        $('sitealbumrating_update-wrapper').style.display = 'none';
    }
    else {
      if ($('sitealbumrating_update-wrapper'))
        $('sitealbumrating_update-wrapper').style.display = 'block';
    }
  }

</script>


<div style="padding: 5px;  "></div>
