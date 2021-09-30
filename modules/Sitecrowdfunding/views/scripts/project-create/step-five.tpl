
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
                    Questions related to <?php echo $this->initiative['title'];?>
                </h3>
            </div>
        </div>
    </form>

    <div>
        <?php echo $this->form->render($this); ?>
    </div>

    <div style="min-height: 100px;margin-right: 10px;margin-left: 10px">
        <button name="previous" id="previous" type="button" onclick="window.location.href='<?php echo $this->backURL; ?>'">Previous</button>
        <button name="execute" id="execute"  type="button" onclick="checkNextFun()">Next</button>
    </div>

    <div class="common_star_info"> <span>* </span> Means required information</div>

</div>

<style type="text/css">
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
    .global_form div > p {
        max-width: 100% !important;
    }
</style>


<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');?>

<script type="text/javascript">
    var $j = jQuery.noConflict();

    function checkNextFun(){
        $j('#sitecrowdfunding_create_project_step_initiative_question').submit()
    }
</script>