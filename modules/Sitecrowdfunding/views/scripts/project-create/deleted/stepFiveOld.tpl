
<?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project-create/common.tpl'; ?>
<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/_commonFunctions.js');
?>
<?php $defaultLogo = $this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png'; ?>
<div class="sitecrowdfunding_project_new_steps">

    <form class="global_form">
        <div>
            <div>
                <h3 >
                    Project Outcomes
                </h3>
            </div>
        </div>
    </form>

    <div>
        <?php echo $this->form->render($this); ?>
    </div>

    <!-- <div class="custom_sub_question">

    </div> -->

    <?php //echo $this->htmlLink(array('module'=>'sitecrowdfunding', 'controller'=> 'project-create' , 'action'=>'add-outcome', 'project_id' => $this->project_id), $this->translate('Add Outcome'), array('class' => 'add_outcome_button button fright icon smoothbox seaocore_icon_add')) ?>

    <?php /* if(count($this->outcomes) > 0): ?>

    <div style="padding-left: 10px;
    padding-right: 10px;
    margin-top: 20px;
    margin-bottom: 20px;
    border: 1px solid #f2f0f0;">
        <div class="organization-div">

            <div class="organization-list">
                <?php foreach($this->outcomes as $outcome): ?>
                <div class="outcome_item">
                    <div class="outcome_info">
                        <h3 class="outcome_name"><b><?php echo $outcome['title']; ?></b></h3>
                    </div>
                    <div class="outcome_description">
                        <?php echo $outcome['description']; ?>
                    </div>
                </div>
                <?php endforeach;?>
            </div>
        </div>
    </div>
    <?php endif; */ ?>


    <div style="min-height: 100px;margin-right: 10px;margin-left: 10px">
        <button name="previous" id="previous" type="button" onclick="window.location.href='<?php echo $this->backURL; ?>'">Previous</button>
        <button name="execute" id="execute"  type="button" onclick="checkNextFun()">Next</button>
    </div>

    <div class="common_star_info"> <span>* </span> Means required information</div>

</div>

<style type="text/css">
    .add_outcome_button{
        /*padding-left: 15px;*/
        font-weight: unset;
        margin: 10px;
    }
    .custom_sub_question{
        margin-top: 10px;
        padding-left: 15px;
        font-size: 18px;
    }
    .outcome_item{
        padding: 10px;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        margin: 10px;
    }

    .organization-list{
        border-bottom: 1px solid #f2f0f0;
        padding: 10px 0px 10px 0px;
    }
    .organization-list li span{
        display: block;
        float: left;
        overflow: hidden;
        width: 175px;
        margin-right: 15px;
    }
    .organization-list > li > span + span {
        min-width: 0px;
        display: block;
        float: none;
        overflow: hidden;
        width: 400px
    }
    .organization-list > li > span + span {
        display: inline-block !important;
    }
</style>


<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');?>

<script type="text/javascript">
    var $j = jQuery.noConflict();

    function checkNextFun(){
        $j('#sitecrowdfunding_project_new_step_seven_custom').submit()
    }
</script>