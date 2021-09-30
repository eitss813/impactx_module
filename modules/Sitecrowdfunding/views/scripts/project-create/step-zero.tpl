
<?php if($this->is_initiative_exist ==true ): ?>

<?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project-create/common.tpl'; ?>
<?php
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');
?>

<div class="sitecrowdfunding_project_new_steps">
    <?php echo $this->form->render(); ?>
    <div class="common_star_info"> <span>* </span> Means required information</div>
</div>

<script type="text/javascript">
    var $j = jQuery.noConflict();

    // clear drop-down
    function clear(element) {
        for (var i = (element.options.length - 1); i >= 0; i--) {
            element.options[ i ] = null;
        }
    }

    // initiative drop-down
    function initiativeOptions(page_id,initiative_id) {
        var url = '<?php echo $this->url(array('action' => 'get-initiatives'), "sitepage_initiatives") ?>';
        if(page_id){
            en4.core.request.send(new Request.JSON({
                url: url,
                data: {
                    format: 'json',
                    page_id: page_id,
                    initiative_id:null
                },
                onSuccess: function (responseJSON) {
                    var initiatives = responseJSON.initiatives;
                    if (initiatives.length > 0) {
                        var element = $('initiative_id');
                        clear(element);

                        var option = document.createElement("OPTION");
                        option.text = "";
                        option.value =  "";
                        element.options.add(option);

                        for (let i = 0; i < initiatives.length; i++) {
                            var option = document.createElement("OPTION");
                            if (
                                (initiatives[i]['text'] !== null && initiatives[i]['value'] !== null
                                    && initiatives[i]['text'] !== '' && initiatives[i]['value'] !== '')
                            ) {
                                option.text = initiatives[i]['text'];
                                option.value = initiatives[i]['value'];
                                element.options.add(option);
                            }
                        }

                        // set default value
                        if(element.options.length > 0){
                            if(initiative_id){
                                element.value = initiative_id;
                            }
                        }

                    }else{
                        var element = $('initiative_id');
                        clear(element);
                    }
                }
            }), {'force': true});
        }
    }

    window.addEventListener('DOMContentLoaded', function () {
        var page_id = $('page_id').value;
        var initiative_id = "<?php echo $this->initiative_id; ?>";
        var project_id = "<?php echo $this->project_id; ?>";
        if(page_id && initiative_id){
            initiativeOptions(page_id,initiative_id);
        }
        if(page_id && !initiative_id){
            initiativeOptions(page_id,null);
        }
    });

</script>
<style>
    select#initiative_id {
        width: 100% !important;
        max-width: 100% !important;
    }
    select{
        width: 100% !important;
        max-width: 100% !important;
    }

</style>
<?php if( $this->is_initiative_exist ==true && $this->create_new == false): ?>
<style>


    div#page_id-wrapper {
        display: none;
    }
</style>
<?php endif; ?>
<?php endif; ?>

<!-- if not exists-->
<?php if($this->is_initiative_exist == false): ?>
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
    <!-- <div class="common_star_info"> <span>* </span> Means required information</div> -->
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

<?php if($this->page_id): ?>
<style>
    #previous {
        display: none;
    }
</style>
<?php endif; ?>
<?php endif; ?>
<style>
    button#previous {
        display: none;
    }
</style>

