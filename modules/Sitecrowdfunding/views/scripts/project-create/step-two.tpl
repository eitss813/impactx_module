<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project-create/common.tpl'; ?>

<?php
$this->headLink()
->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_dashboard.css')
->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/_commonFunctions.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');
$this->tinyMCESEAO()->addJS();
?>

<?php $coreSettings = Engine_Api::_()->getApi('settings', 'core'); ?>
<script type="text/javascript">
    en4.core.runonce.add(function () {
        if(document.getElementById('location') && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
            var autocompleteSECreateLocation = new google.maps.places.Autocomplete(document.getElementById('location'));
        <?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl'; ?>
        }
    });
</script>

<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
//'topLevelId' => (int) @$this->topLevelId,
//'topLevelValue' => (int) @$this->topLevelValue
))
?>

<div class='sitecrowdfunding_dashboard_form'>
    <div class="sitecrowdfunding_project_new_steps">
        <?php echo $this->form->setAttrib('class', 'global_form sitecrowdfunding_create_list_form')->render($this); ?>
        <div class="common_star_info"> <span>* </span> Means required information</div>
    </div>
</div>
<script>
    window.addEvent('domready', function () {

        if ($('starttime-minute')) {
            $('starttime-minute').value = 0
            $('starttime-minute').style.display = 'none';
        }
        if ($('starttime-ampm')) {
            $('starttime-ampm').value = 'AM'
            $('starttime-ampm').style.display = 'none';
        }
        if ($('starttime-hour')) {
            $('starttime-hour').value = 12
            $('starttime-hour').style.display = 'none';
        }
        if ($('endtime-minute')) {
            $('endtime-minute').value = 50
            $('endtime-minute').style.display = 'none';
        }
        if ($('endtime-ampm')) {
            $('endtime-ampm').value = 'PM'
            $('endtime-ampm').style.display = 'none';
        }
        if ($('endtime-hour')) {
            $('endtime-hour').value = 11
            $('endtime-hour').style.display = 'none';
        }

    });

    en4.core.runonce.add(function () {

        if ($('lifetime-0')) {
            if ($('lifetime-0').checked) {
                initializeCalendar(0,'<?php echo date('Y-m-d'); ?>');
            } else {
                initializeCalendar(1,'<?php echo date('Y-m-d'); ?>');
            }
        } else {
            initializeCalendar(0,'<?php echo date('Y-m-d'); ?>');
        }
    })
</script>

<style>
    .mce-tinymce{
        width: auto !important;
    }
    #starttime-wrapper,#endtime-wrapper{
        display: none !important;
    }
    .global_form div > p{
        max-width: 800px !important;
    }
</style>


<?php
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');
?>

<script type="text/javascript">
    var $j = jQuery.noConflict();
    $j(document).ready(function() {
        // setTimeout(function() {
        //     $j('#description-element div').hide();
        //     $j('#description-element textarea').show();
        // }, 1000)
    });

</script>
<style>
    #description-element{
        margin-bottom: 9px !important;
        border-bottom: 1px solid rgba(0,0,0,0.2) !important;
    }
    #description-element .mce-toolbar,#description-element .mce-toolbar-grp{
        display: none !important
    }
</style>
