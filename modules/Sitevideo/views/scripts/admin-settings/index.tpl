<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooRainbow.js');

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/mooRainbow.css');

if (!empty($this->isModsSupport)):
    foreach ($this->isModsSupport as $modName) {
        echo "<div class='tip' style='position:relative;'><span>" . $this->translate("Note: You do not have the latest version of the '%s'. Please upgrade it to the latest version to enable its integration with Advanced Videos / Channels / Playlists Plugin.", ucfirst($modName)) . "</span></div>";
    }
endif;
?>

<h2>
    <?php echo $this->translate("Advanced Videos / Channels / Playlists Plugin") ?>
</h2>

<?php if (count($this->navigation)): ?>

    <div class='seaocore_admin_tabs clr'>

        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
    <?php if (count($this->navigationGeneral)): ?>
        <div class='seaocore_admin_tabs'>
            <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php $coreSettings = Engine_Api::_()->getApi('settings', 'core'); ?>

<?php if ($coreSettings->getSetting('sitevideo.isActivate')) : ?>
    <script type="text/javascript">
        function dismissmessage(modName) {
            var d = new Date();
            // Expire after 1 Year.
            d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
            var expires = "expires=" + d.toGMTString();
            document.cookie = modName + "_dismiss" + "=" + 1 + "; " + expires;
            $('sitevideo_dismiss_modules').style.display = 'none';
        }
    </script>

    <?php
    $moduleName = 'sitevideo';
    if (!isset($_COOKIE[$moduleName . '_dismiss'])):
        ?>
        <div id="sitevideo_dismiss_modules">
            <div class="seaocore-notice">
                <div class="seaocore-notice-icon">
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
                </div>
                <div style="float:right;">
                    <button onclick="dismissmessage('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
                </div>
                <div class="seaocore-notice-text ">
                    <?php echo 'To change the layout of pages like: Videos Home, Browse Videos, Browse Channels, etc. please visit <a href="admin/sitevideo/settings/set-template">Layout Templates</a> tab of this plugin, select the desired pages and change the layout with a new and attractive user interface.'; ?>
                </div>	
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php $key = $coreSettings->getSetting('sitevideo.youtube.apikey', $coreSettings->getSetting('video.youtube.apikey')); ?>
<?php if ($coreSettings->getSetting('sitevideo.isActivate') && !$key) : ?>
    <script type="text/javascript">
        function dismissyoutubemessage(modName) {
            var d = new Date();
            // Expire after 1 Year.
            d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
            var expires = "expires=" + d.toGMTString();
            document.cookie = modName + "_dismiss" + "=" + 1 + "; " + expires;
            $('sitevideo_youtube_dismiss_modules').style.display = 'none';
        }
    </script>

    <?php
    $moduleName = 'sitevideo_youtube';
    if (!isset($_COOKIE[$moduleName . '_dismiss'])):
        ?>
        <div id="sitevideo_youtube_dismiss_modules">
            <div class="seaocore-notice">
                <div class="seaocore-notice-icon">
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
                </div>
                <div style="float:right;">
                    <button onclick="dismissyoutubemessage('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
                </div>
                <div class="seaocore-notice-text ">
                    <?php echo 'Please configure the YouTube API key to enable the YouTube video upload option on your site. Please <a href="admin/sitevideo/settings/video-settings">click here</a> to enter the YouTube API key.'; ?>
                </div>	
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>    

<?php
$moduleName = 'sitevideointegration';
if (!isset($_COOKIE[$moduleName . '_dismiss'])):
    ?>
    <?php if (!Engine_Api::_()->hasModuleBootstrap('sitevideointegration') && Engine_Api::_()->sitevideo()->isModulesEnabled()): ?>
        <div id="dismiss_modules">
            <div class="seaocore-notice">
                <div class="seaocore-notice-icon">
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
                </div>
                <div style="float:right;">
                    <button onclick="dismissintegration('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
                </div>
                <div class="seaocore-notice-text ">
                    <?php echo 'To display videos at one place on your website and provide quick video uploading via lightbox along with other useful features, you may purchase <a href="https://www.socialengineaddons.com/videoextensions/socialengine-advanced-videos-pages-businesses-groups-listings-events-stores-extension" target="_blank">"Advanced Videos - Pages, Businesses, Groups, Multiple Listing Types, Events, Stores, etc Extension"</a>.'; ?>
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
    window.addEvent('domready', function () {
        showads('<?php echo Engine_Api::_()->sitevideo()->showLightBoxVideo() ?>');
    });

    function showlightboxads(option) {
        if ($('sitevideo_adtype-wrapper')) {
            if (option == 0) {
                $('sitevideo_adtype-wrapper').style.display = 'none';
            }
            else {
                $('sitevideo_adtype-wrapper').style.display = 'block';

            }
        }
    }

    function showads(option) {
        if (option == 1) {
            if ($('sitevideo_lightboxads-wrapper')) {
                $('sitevideo_lightboxads-wrapper').style.display = 'block';
                showlightboxads($('sitevideo_lightboxads-1').checked);
            }
        }
        else {
            if ($('sitevideo_lightboxads-wrapper')) {
                $('sitevideo_lightboxads-wrapper').style.display = 'none';
                showlightboxads(0);
            }
        }
    }

    function showwarning(option)
    {
        if (option == 1) {
            Smoothbox.open('<div style="padding: 5px;margin-right:px;"><?php echo $this->string()->escapeJavascript($this->translate('If you have previously made any changes mentioned in \'FAQ\' > \'Customize\' section in FAQ 1 or 2, then please remove the changes done in code as mentioned in Step 3 there. Please click ')) . $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitevideo', 'controller' => 'settings', 'action' => 'faq', 'faq_type' => 'customize'), $this->translate('here'), array('target' => "blank", "style" => "color:#5F93B4;")) . $this->string()->escapeJavascript($this->translate(' to follow further process.')) ?></div>');
        } else {
            Smoothbox.open('<div style="padding: 5px; margin-right:5px;"><?php echo $this->string()->escapeJavascript($this->translate('If you do not want to show these channels on Browse Channels Page and Member Profile Page also, then please click ')) . $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitevideo', 'controller' => 'settings', 'action' => 'faq', 'faq_type' => 'customize'), $this->translate('here'), array('target' => "blank", "style" => "color:#5F93B4;")) . $this->string()->escapeJavascript($this->translate(' to follow further process.')) ?></div>');
        }
    }

    window.addEvent('domready', function () {
        showUpdateratingSetting('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.rating', 1) ?>');

    });
    function showUpdateratingSetting(options) {
        if (options == 0) {
            if ($('sitevideorating_update-wrapper'))
                $('sitevideorating_update-wrapper').style.display = 'none';
        }
        else {
            if ($('sitevideorating_update-wrapper'))
                $('sitevideorating_update-wrapper').style.display = 'block';
        }
    }

</script>


<div style="padding: 5px;  "></div>
<div id="light" class="sitevideo_white_content">
    <?php echo $this->translate('Default installation work is going on during the Plugin Activate. Please wait...'); ?>
    <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Seaocore/externals/images/core/loading.gif" alt="" />
</div>

<div id="fade" class="sitevideo_black_overlay"></div>

<script type="text/javascript">

    function showlightbox() {
        document.getElementById('light').style.display = 'block';
        document.getElementById('fade').style.display = 'block';
    }
    function selectAutoUpload(value) {
        if ($('sitevideo_automatic_videouploadallow-1').checked) {
            $('sitevideo_automatic_uploadvideoscount-wrapper').show();
            $('sitevideo_automatic_uploadvideoindays-wrapper').show();
        } else {
            $('sitevideo_automatic_uploadvideoscount-wrapper').hide();
            $('sitevideo_automatic_uploadvideoindays-wrapper').hide();
        }
    }
    window.addEvent('domready', function () {
        selectAutoUpload();
    });



</script>

<style type="text/css">
    .sitevideo_white_content{
        display: none;	
        position: fixed;
        left: 40%;
        width: 30%;
        top:25%;
        padding:30px 16px;
        border:4px solid #525252;
        background-color: #fff;	
        z-index:1002;	
        overflow: auto;	
        text-align:center;
    }
    .sitevideo_black_overlay{	
        display: none;
        position: fixed;
        top: 0%;
        left: 0%;
        width: 100%;
        height: 100%;
        background-color: black;
        z-index:1001;
        -moz-opacity: 0.8;
        opacity:.50;
        filter: alpha(opacity=50);
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
<?php if (strlen($this->route) > 0) : ?>
        Smoothbox.open(en4.core.baseUrl + 'admin/sitevideo/settings/template');
<?php endif; ?>
</script>