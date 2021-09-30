<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
 <?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<script type="text/javascript">
if(document.getElementById('sitecrowdfunding_map_city')) {
	window.addEvent('domready', function() {
		new google.maps.places.Autocomplete(document.getElementById('sitecrowdfunding_map_city'));
	});
}
</script>

<?php
if (!empty($this->isModsSupport)):
    foreach ($this->isModsSupport as $modName) {
        echo "<div class='tip'><span>" . $this->translate("Note: You do not have the latest version of the '%s'. Please upgrade it to the latest version to enable its integration with Crowdfunding plugin.", ucfirst($modName)) . "</span></div>";
    }
endif;
?>
<?php $url = $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'upgrade'), 'admin_default', true); ?>

<h2>
    <?php echo $this->translate('Crowdfunding / Fundraising / Donations Plugin'); ?>
</h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<?php if (count($this->navigationGeneral)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
    </div>
<?php endif; ?>
<div class='seaocore_settings_form sitecrowdfunding_global_settings'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>

<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>

<script type="text/javascript">

    function dismissNote() {
        $('is_remove_note').value = 1;
        $('review_global').submit();
    }

    window.addEvent('domready', function() {
        showDefaultNetwork('<?php echo $settings->getSetting('sitecrowdfunding.network', 0) ?>');
        
        showTimezoneSetting('<?php echo $settings->getSetting('sitecrowdfunding.datetime.format', 'medium') ?>');

        showLocationSettings('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1) ?>');
    });
    
    function showTimezoneSetting(value) {
      if( value == 'full' || value == 'long' ) {
        $('sitecrowdfunding_timezone-wrapper').style.display = 'block';
      } else {
        $('sitecrowdfunding_timezone-wrapper').style.display = 'none';
      }
    }

    function showDefaultNetwork(option) {
        if ($('sitecrowdfunding_default_show-wrapper')) {
            if (option == 0) {
                $('sitecrowdfunding_default_show-wrapper').style.display = 'block';
                showDefaultNetworkType($('sitecrowdfunding_default_show-1').checked);
                $('sitecrowdfunding_networkprofile_privacy-wrapper').style.display = 'none';
            }
            else {
                showDefaultNetworkType(1);
                $('sitecrowdfunding_default_show-wrapper').style.display = 'none';
                $('sitecrowdfunding_networkprofile_privacy-wrapper').style.display = 'block';
            }
        }
    }

    function showDefaultNetworkType(option) {
        if ($('sitecrowdfunding_networks_type-wrapper')) {
            if (option == 1) {
                $('sitecrowdfunding_networks_type-wrapper').style.display = 'block';
            } else {
                $('sitecrowdfunding_networks_type-wrapper').style.display = 'none';
            }
        }
    }

    function showLocationSettings(option) {

        if ($('sitecrowdfunding_veneuname-wrapper')) {
            if (option == 1) {
                $('sitecrowdfunding_veneuname-wrapper').style.display = 'block';
            } else {
                $('sitecrowdfunding_veneuname-wrapper').style.display = 'none';
            }
        }

        if ($('sitecrowdfunding_map_sponsored-wrapper')) {
            if (option == 1) {
                $('sitecrowdfunding_map_sponsored-wrapper').style.display = 'block';
            } else {
                $('sitecrowdfunding_map_sponsored-wrapper').style.display = 'none';
            }
        }

        if ($('sitecrowdfunding_proximity_search_kilometer-wrapper')) {
            if (option == 1) {
                $('sitecrowdfunding_proximity_search_kilometer-wrapper').style.display = 'block';
            } else {
                $('sitecrowdfunding_proximity_search_kilometer-wrapper').style.display = 'none';
            }
        }

        if ($('seaocore_locationdefaultmiles-wrapper')) {
            if (option == 1) {
                $('seaocore_locationdefaultmiles-wrapper').style.display = 'block';
            } else {
                $('seaocore_locationdefaultmiles-wrapper').style.display = 'none';
            }
        }

        if ($('seaocore_locationdefault-wrapper')) {
            if (option == 1) {
                $('seaocore_locationdefault-wrapper').style.display = 'block';
            } else {
                $('seaocore_locationdefault-wrapper').style.display = 'none';
            }
        }

        if ($('sitecrowdfunding_map_city-wrapper')) {
            if (option == 1) {
                $('sitecrowdfunding_map_city-wrapper').style.display = 'block';
            } else {
                $('sitecrowdfunding_map_city-wrapper').style.display = 'none';
            }
        }

        if ($('sitecrowdfunding_map_zoom-wrapper')) {
            if (option == 1) {
                $('sitecrowdfunding_map_zoom-wrapper').style.display = 'block';
            } else {
                $('sitecrowdfunding_map_zoom-wrapper').style.display = 'none';
            }
        }
    }

</script>

<style type="text/css">
    .seaocore-notice-text ul {
        list-style: disc outside none;
        margin: 3px 0 0 18px;
    }
    .seaocore-notice-text ul li{
        margin: 2px 0 2px 0px;
    }
</style>

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