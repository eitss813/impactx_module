<?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project-create/common.tpl'; ?>
<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_dashboard.css'); ?>

<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepagemember/externals/styles/style_sitepagemember.css'); ?>

<div class="sitecrowdfunding_project_new_steps">

    <h3 class="form_title"><?php echo $this->translate('Settings'); ?> </h3>

    <!-- Admin List -->
    <div class="sitecrowdfunding_admin_container">
        <div class="sitecrowdfunding_leaders">
            <h3 class="form_sub_title"><?php echo $this->translate('Project Administrator(s)'); ?> </h3>
            <br/>
            <?php foreach ($this->members as $member): ?>
            <div id='<?php echo $member->user_id ?>_page_main' class='sitecrowdfunding_leaders_list'>
                <div class='sitecrowdfunding_leaders_thumb' id='<?php echo $member->user_id ?>_pagethumb'>
                    <a href="../../../../../../../index.php">
                        <?php echo $this->itemBackgroundPhoto($member, null, null, array('tag' => 'i')); ?>
                    </a>
                </div>
                <div id='<?php echo $member->user_id ?>_page' class="sitecrowdfunding_leaders_detail">
                    <?php echo $this->htmlLink($member->getHref(), $member->getTitle()) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <form method='post' class="mtop10" id="project_admins_list">
            <div class="fleft sitecrowdfunding_leaders_guest">
                <div>
                    <?php if (!empty($this->message)): ?>
                    <div class="tip">
                            <span>
                                <?php echo $this->message; ?>
                            </span>
                    </div>
                    <?php endif; ?>

                    <div class="fright">
                        <a style="font-weight: unset !important;" class="add_project_admin_btn button smoothbox icon seaocore_icon_add"
                           href="../../../../../../../index.php">
                            <?php echo $this->translate("Add Project Administrator"); ?>
                        </a>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <br/>
    <br/>

    <!-- Privacy Settings-->
    <div class="sitecrowdfunding_privacy_settings">
        <?php echo $this->form->render(); ?>
    </div>

    <br/>

</div>


<style>
    #auth_topic-wrapper{
        display: none;
    }
    .sitecrowdfunding_admin_container {
        padding: 15px;
        margin-top: 15px;
    }

    .sitecrowdfunding_project_new_steps {
        min-width: 430px;
        max-width: 850px;
        padding: 20px;
        margin-left: auto;
        margin-right: auto;
        position: relative;
        border-radius: 3px;
        margin-bottom: 30px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
        background: rgba(255, 255, 255, .9)
    }

    .form_title {
        font-weight: 800 !important;
        font-size: 25px !important;
        text-align: center !important;
        padding: 20px !important;
        border-bottom: 1px solid #f2f0f0;
    }

    .form_sub_title {
        padding: 10px 10px;
        margin: -10px -10px 10px -10px;
        font-size: 17px;
        border-bottom: 1px solid #f2f0f0;
    }

    .sitecrowdfunding_leaders_detail a:hover {
        color: black !important;
    }

    .sitecrowdfunding_leaders_detail {
        padding: 0 !important;
        font-size: 20px !important;
        background: none !important;
        border: none !important;
        margin-top: -12px;
    }

    .sitecrowdfunding_leaders_guest {
        background: none !important;
        border: none !important;
    }

    #execute{
        float: right !important;
    }

    #previous{
        float: left !important;
    }

</style>