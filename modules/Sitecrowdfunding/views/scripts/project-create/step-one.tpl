<?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project-create/common.tpl'; ?>
<?php
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');
?>

<div class="sitecrowdfunding_project_new_steps">
    <?php echo $this->form->render(); ?>
    <div style="min-height: 100px;margin-right: 10px;margin-left: 10px">
        <button name="previous" id="previous" type="button" onclick="window.location.href='<?php echo $this->backURL; ?>'">Previous</button>
        <button name="execute" id="execute"  type="button" onclick="checkNextFun()">Next</button>
    </div>
    <div class="common_star_info"> <span>* </span> Means required information</div>
</div>

<script type="text/javascript">
    var $j = jQuery.noConflict();

    function checkNextFun(){
        $j('#sitecrowdfunding_project_new_step_one').submit()
    }

    function checkIsFundable(value){
        if (!value || value == '0') {
            $("goal_amount-wrapper").style.display = "none";
            $("invested_amount-wrapper").style.display = "none";
        }else{
            $("goal_amount-wrapper").style.display = "block";
            $("invested_amount-wrapper").style.display = "block";
        }
    }
    $j(document).ready(function() {
        $("search-wrapper").style.display = "none";
        $("auth_topic-wrapper").style.display = "none";
        $("auth_comment-wrapper").style.display = "none";
        $("auth_view-wrapper").style.display = "none";
        var is_fund_raisable = $j("input[name=is_fund_raisable]:checked").val();
        checkIsFundable(is_fund_raisable);

        $j('input[name=goal_amount]').one('focus', function() {
            $j(this).val('');
        });
        $j('input[name=invested_amount]').one('focus', function() {
            $j(this).val('');
        });
    });

</script>
<style>
    button#previous {
        display: none;
    }
</style>

