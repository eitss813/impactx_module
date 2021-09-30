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
<?php if($this->layoutType != 'fundingDetails'): ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
<?php endif; ?>

<div class="sitecrowdfunding_dashboard_content">

    <?php if($this->layoutType != 'fundingDetails'): ?>
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle'=>'Edit External funding', 'sectionDescription'=> 'Edit external funding using below form for this project.')); ?>
    <?php endif; ?>

    <div class="sitecrowdfunding_project_form">

        <?php if($this->layoutType == 'fundingDetails'): ?>
            <h3 class="form_title">Edit External funding</h3>
        <?php endif; ?>

        <?php echo $this->form->render(); ?>
    </div>
</div>
</div>
</div>
<script type="text/javascript">
    function getIsOrganisationListedValue(){
        var value = null;
        var checkboxes = $$('input[name="is_organisation_listed"]');
        checkboxes.each(function(item, index){
            var checked = item.get('checked');
            if(checked===true){
                value = item.get('value');
            }
        });
        return value;
    }
    function checkIsOrganisationListed(value) {
        if(value=='yes'){
            $("organization_id-wrapper").style.display = "block";
            $("organization_name-wrapper").style.display = "none";
        }else{
            $("organization_id-wrapper").style.display = "none";
            $("organization_name-wrapper").style.display = "block";
        }
    }
    function checkingResourceType(value) {
        if (value == 'organization') {
            $("is_organisation_listed-wrapper").style.display = "block";
            $("member_id-wrapper").style.display = "none";
            $("mem_nav").style.display = "none";

            var isOrganisationListedValue = getIsOrganisationListedValue();
            if(isOrganisationListedValue !== null){
                checkIsOrganisationListed(isOrganisationListedValue);
            }else{
                checkIsOrganisationListed('yes');
            }
        } else {
            $("is_organisation_listed-wrapper").style.display = "none";
            $("member_id-wrapper").style.display = "block";
            $("mem_nav").style.display = "block";
            $("organization_id-wrapper").style.display = "none";
            $("organization_name-wrapper").style.display = "none";
        }
    }
    window.addEvent('domready', function () {
        var defaultvalue = '<?php echo $this->selectedType; ?>'
        checkingResourceType(defaultvalue);
        if ($('funding_date-minute')) {
            $('funding_date-minute').value = 0
            $('funding_date-minute').style.display = 'none';
        }
        if ($('funding_date-ampm')) {
            $('funding_date-ampm').value = 'AM'
            $('funding_date-ampm').style.display = 'none';
        }
        if ($('funding_date-hour')) {
            $('funding_date-hour').value = 12
            $('funding_date-hour').style.display = 'none';
        }
    });

</script>
<style type="text/css">
    /*edit funding form*/
    .sitecrowdfunding_project_form .global_form > div
    {
        padding: 0px !important;
    }
    .form_title{
        padding: 15px;
    }
    #org_nav, #mem_nav{
        margin-bottom: 10px;
    }
    #org_nav, #mem_nav{
        display: none !important;
    }
</style>