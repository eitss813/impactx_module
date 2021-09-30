<?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project-create/common.tpl'; ?>
<?php $defaultLogo = $this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png'; ?>
<div class="sitecrowdfunding_project_new_steps">
    <?php echo $this->form_dummy->render(); ?>

    <div style="display: flex;justify-content: center">
        <?php echo $this->htmlLink(array('module'=>'sitecrowdfunding', 'controller'=> 'project-create' , 'action'=>'add-organization', 'project_id' => $this->project_id), $this->translate('Add Organization Name'), array('class' => 'add_org_button button icon smoothbox seaocore_icon_add')) ?>
    </div>
    <br/>

    <div class="show_notice_custom">
        Note: Below added organization will be deleted on clicking the Next button.
    </div>

    <?php if(
        count($this->internalorganizations) > 0 ||
    count($this->externalorganizations) > 0
    ): ?>
    <div class="organization-div">
        <?php foreach($this->internalorganizations as $org): ?>
        <div class="org_container">
            <div class="org_left">
                <div class="org_logo">
                    <img style="width: 80px;height: 80px" src="<?php echo $org['logo'] ?>"/>
                    <p><?php echo  $org['organization_type']; ?></p>
                </div>
                <div class="org_title_desc">
                    <h3 class="organization-header">
                        <?php echo $this->htmlLink($org['link'],  $org['title'], array('target' => '_blank')) ?>
                    </h3>
                    <div>
                        <?php echo  $org['description']; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach;?>
        <?php foreach($this->externalorganizations as $org): ?>
        <div class="org_container">
            <div class="org_left">
                <div class="org_logo">
                    <img style="width: 80px;height: 80px" src="<?php echo !empty($org['logo']) ? $org['logo'] : $defaultLogo; ?>" />
                    <?php if($org['organization_type'] === 'others'): ?>
                    <p><?php echo  $org['others']; ?></p>
                    <?php else:?>
                    <p><?php echo  $org['organization_type']; ?></p>
                    <?php endif; ?>
                </div>
                <div class="org_title_desc">
                    <?php if(!empty($org['link'])):?>
                    <h3 class="organization-header">
                        <?php echo $this->htmlLink($org['link'],  $org['title'], array('target' => '_blank')) ?>
                    </h3>
                    <?php else: ?>
                    <h3><?php echo $org['title']; ?></h3>
                    <?php endif; ?>
                    <div>
                        <?php echo  $org['description']; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach;?>
    </div>
    <?php endif; ?>


    <div style="min-height: 100px;margin-right: 10px;margin-left: 10px">
        <button name="previous" id="previous" type="button" onclick="window.location.href='<?php echo $this->backURL; ?>'">Previous</button>
        <button name="execute" id="execute"  type="button" onclick="checkNextFun()">Next</button>
    </div>

    <div class="common_star_info"> <span>* </span> Means required information</div>

</div>

<?php
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');
?>
<script type="text/javascript">

    var $j = jQuery.noConflict();

    function checkNextFun() {
        $j('#sitecrowdfunding_project_new_step_four_dummy').submit()
    }

    function checkIsAssociated(value){
        if (!value || value == '0') {
            $j('.add_org_button').hide();

        <?php if($this->show_notice): ?>
            $j('.show_notice_custom').show();
        <?php endif; ?>

        }else{
            $j('.add_org_button').show();

        <?php if($this->show_notice): ?>
            $j('.show_notice_custom').hide();
        <?php endif; ?>
        }
    }
    $j(document).ready(function() {
        $j('.show_notice_custom').hide();
    });
</script>



<style type="text/css">
    .show_notice_custom{
        color: red;
        padding-left: 15px;
    }
    .add_org_button{
        font-weight: unset;
        /*padding-left: 15px;*/
    }
    .organization-div h3{
        padding-left: 20px;
    }
    .org_container{
        display: flex;
        border: 1px solid #f2f0f0;
        margin: 20px;
        padding: 10px;
        justify-content: space-between;
    }
    .org_left{
        display: flex;
    }
    .org_logo{
        padding-right: 15px;
        text-align: center;
        width: auto;
    }
    .organization-div{
        border: 1px solid #f2f0f0;
        border-radius: 5px;
        padding-top: 20px;
        margin: 10px
    }
    .organization-header{
        text-decoration: underline;
        font-weight: bold;
    }
</style>

