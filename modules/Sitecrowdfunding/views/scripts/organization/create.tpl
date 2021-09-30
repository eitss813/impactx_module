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
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sitecrowdfunding_dashboard_content">

    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle' => 'Add New Organization', 'sectionDescription' => 'Add New Organization of this project using below form.')); ?>
    <div class="sitecrowdfunding_project_form">
        <?php echo $this->form->render(); ?>
    </div>
    <!-- <div class="clr">
        <?php echo $this->htmlLink(array('route' => "sitecrowdfunding_organizationspecific", 'action'=>'editorganizations', 'project_id' => $this->project_id), $this->translate('Back to Manage Organizations'), array('class' => 'icon seaocore_icon_back')) ?>
    </div> -->
</div>
</div>
</div>
<script type="text/javascript">

    var $j = jQuery.noConflict();
    function checkIsInternal(value) {

        if (!value || value == '0') {
            $("title-wrapper").style.display = "block";
            $("description-wrapper").style.display = "block";
            $("link-wrapper").style.display='block';
            $("photo-wrapper").style.display='block';
            $("organization_id-wrapper").style.display='none';
        } else {
            $("title-wrapper").style.display = "none";
            $("description-wrapper").style.display = "none";
            $("link-wrapper").style.display='none';
            $("photo-wrapper").style.display='none';
            $("organization_id-wrapper").style.display='block';
        }
    }
    $j(document).ready(function() {
        checkIsInternal($j("input[name='is_internal']:checked").val());
        $("others-wrapper").style.display='none';
        $j('#organization_type').on('change', function() {
            if(this.value == 'others'){
                $("others-wrapper").style.display='block';
            }else{
                $("others-wrapper").style.display='none';
            }
        });

    });

</script>
<style type="text/css">
    /*edit funding form*/
    .sitecrowdfunding_project_form .global_form > div
    {
        padding: 0px !important;
    }
</style>