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
<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/_commonFunctions.js');
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sitecrowdfunding_dashboard_content">

    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle'=>'Add New Milestone', 'sectionDescription'=> 'Add New Milestone using below form for this project.')); ?>
    <div class="sitecrowdfunding_project_form">
        <?php echo $this->form->render(); ?>
    </div>
</div>
</div>
</div>
<script type="text/javascript">
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

</script>
<style type="text/css">
    /*edit funding form*/
    .sitecrowdfunding_project_form .global_form > div
    {
        padding: 0px !important;
    }
</style>