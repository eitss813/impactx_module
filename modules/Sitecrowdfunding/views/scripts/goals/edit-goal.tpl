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
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/_commonFunctions.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/selectric/jquery.selectric.js');
?>
<link href="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/selectric/selectric.css' ?>" rel="stylesheet">

<?php if($this->layoutType !='projectStepsCreate'): ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
<?php endif; ?>

<div class="sitecrowdfunding_dashboard_content">

    <?php if($this->layoutType !='projectStepsCreate'): ?>
        <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle'=>'Edit Goal', 'sectionDescription'=> 'Edit Goal using below form for this project.')); ?>
    <?php endif; ?>

    <?php if($this->layoutType =='projectStepsCreate'): ?>
    <h2>Edit Goal</h2>
    <?php endif; ?>

    <div class="sitecrowdfunding_project_form">
        <?php echo $this->form->render(); ?>
    </div>
</div>
</div>
</div>

<style type="text/css">
    /*edit funding form*/
    .sitecrowdfunding_project_form .global_form > div
    {
        padding: 0px !important;
    }
</style>
<script>
    var $j = jQuery.noConflict();
    $j(document).ready(function() {
        $j('#sdg_target_id').selectric();
        $j('#sdg_goal_id').selectric({
            onChange: function(element) {
                let selectedval = $j(element).val()
                updateTargetSelect(selectedval, true)
            },
        });
        var selectedValue = "<?php echo $this->goal['sdg_goal_id']; ?>"
        updateTargetSelect(selectedValue, false)
    })

    function updateTargetSelect(id, flag){
        $j("#sdg_target_id-element ul li").removeClass('selected')
        $j("#sdg_target_id-element ul li").removeClass('highlighted')
        $j("#sdg_target_id-element ul li").hide();
        if (id){
            let setSelectedValue = false
            var selectedTargetValue = "<?php echo $this->goal['sdg_target_id']; ?>"
            $j('#sdg_target_id-element ul li').each(function() {
                let thisval = $j(this).data('custom')
                thisval = thisval.split('-')
                if(thisval[0] == id){
                    $j(this).show()
                    if(flag){
                        if(!setSelectedValue){
                            setSelectedValue = true
                            $j(this).addClass('selected');
                            $j(this).addClass('highlighted');
                            $j("#sdg_target_id-element .selectric .label").html($j(this).text())
                            $j('#sdg_target_id').val($j(this).data('custom'));
                        }
                    }else{
                        if(selectedTargetValue == thisval[1]){
                            $j(this).addClass('selected');
                            $j(this).addClass('highlighted');
                            $j("#sdg_target_id-element .selectric .label").html($j(this).text())
                        }
                    }
                }else{
                    $j(this).hide()
                }
            })
        }
    }

</script>
