<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: overview.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<style>
    .mce-content-body {
        font-size: 12pt !important;
    }
</style>
<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sitecrowdfunding_dashboard_content">
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle'=> 'About The Project',
    'sectionDescription' => '')); ?>
    <?php if (!empty($this->success)): ?>
        <ul class="form-notices" >
            <li>
                <?php echo $this->translate($this->success); ?>
            </li>
        </ul>
    <?php endif; ?>
    <?php echo $this->form->render($this); ?>
</div>
    <script type="text/javascript">
        var catdiv1 = $('overview-label');
        var catdiv2 = $('save-label');
        var catarea1 = catdiv1.parentNode;
        ///catarea1.removeChild(catdiv1);
        var catarea2 = catdiv2.parentNode;
        catarea2.removeChild(catdiv2);
    </script>
</div>
</div>
<?php $coreSettings = Engine_Api::_()->getApi('settings', 'core'); ?>
<script type="text/javascript">
    en4.core.runonce.add(function () {
        if(document.getElementById('location') && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
            var autocompleteSECreateLocation = new google.maps.places.Autocomplete(document.getElementById('location'));
        <?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl'; ?>
        }
    });
</script>
<style>
    body.mce-content-body {
        font-size: 12pt !important;
        background-color: red;
    }
</style>