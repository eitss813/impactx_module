<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: overview.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_metrics_dashboard_main_header.tpl'; ?>

<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">

        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/metrics_edit_tabs.tpl'; ?>
            <?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_metrics_dashboard_section_header.tpl', array( 'metric_id'=>$this->metric_id,'sectionTitle'=> 'Test', 'sectionDescription' => '')); ?>
            <div class="sitepage_edit_content">
                <div id="show_tab_content">
                    <div class="sitepage_overview_editor">
                        <?php echo $this->form->render($this); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    var $j = jQuery.noConflict();

    // load image in bottom
    function loadImage(){
        var htmlStr = `
             <div id="custom-logo-wrapper" class="form-wrapper">
                <div id="custom-logo-label" class="form-label">
                    <label for="title">Metric Logo</label>
                </div>
                <div id="custom-logo-element" class="form-element">
                    <img id="display_photo_custom_id" style="height: 250px;width: 300px;object-fit: contain;" src="<?php echo $this->metricDetails->getLogoUrl('thumb.cover'); ?>" />
                <div>
             <div>
        `;

        var newNode = document.createElement('div');
        newNode.innerHTML = htmlStr;

        // Get the reference node
        var referenceNode = document.querySelector('#metric_name-wrapper');

        // Insert the new node before the reference node
        referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);

        $j('#logo_edit_options-wrapper').append('<br/>');

    }

    window.addEventListener('DOMContentLoaded', function () {
        <?php if(!empty($this->metricDetails['logo'])):?>
            // Load image
            loadImage();
        <?php endif; ?>
    });

    function openChangeModal(){
        Smoothbox.open('<?php echo $this->url(array('action' => 'upload-cover-photo', 'metric_id' => $this->metric_id), 'sitepage_metrics', true) ?>');
    }

    function openRepositionModal(){
        Smoothbox.open('<?php echo $this->url(array('action' => 'reposition-cover-photo', 'metric_id' => $this->metric_id), 'sitepage_metrics', true) ?>');
    }

    function openRemoveModal(){
        Smoothbox.open('<?php echo $this->url(array('action' => 'remove-cover-photo', 'metric_id' => $this->metric_id), 'sitepage_metrics', true) ?>');
    }
</script>
<style>
    #logo_edit_options-wrapper{
        margin-bottom: 10px;
    }
</style>