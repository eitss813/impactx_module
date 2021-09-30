<?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project-create/common.tpl'; ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_dashboard.css'); ?>

<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/_commonFunctions.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/selectric/jquery.selectric.js');
?>

<link href="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/selectric/selectric.css' ?>"
      rel="stylesheet">

<div class="sitecrowdfunding_project_new_steps">
    <h3 class="form_title"><?php echo $this->translate('United Nations Substainable Development Goals'); ?> </h3>

    <br/>
    <h3 class="form_description">The Sustainable Development Goals (SDGs) are a collection of 17 global goals designed to be a "blueprint to achieve a better and more sustainable future for all".</h3>
    <br/>
    <h3 class="form_description">Please add the goals which applicable to the project.</h3>
    <br/>

    <div class="clr fright">
        <a style="font-weight: unset !important; float: left" class="button smoothbox icon seaocore_icon_add"
           href="../../../../../../../index.php">
            <?php echo $this->translate("Add Goal"); ?>
        </a>
    </div>

    <?php if(count($this->goals) > 0): ?>
        <?php echo $this->content()->renderWidget("sitecrowdfunding.development-goals"); ?>
    <?php endif; ?>

    <br/>
    <br/>
    <div class="button_group">
        <!-- Previous -->
        <a style="font-weight: unset !important; float: left" class="prev_btn button"
           href="../../../../../../../index.php">
            <?php echo $this->translate("Previous"); ?>
        </a>
        <!-- Next -->
        <a style="font-weight: unset !important; float: right " class="next_btn button"
           href="../../../../../../../index.php">
            <?php echo $this->translate("Next"); ?>
        </a>

    </div>
    <br/>

</div>

<style>
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

    .layout_sitecrowdfunding_development_goals > div > h1 {
        display: none;
    }

    .layout_sitecrowdfunding_development_goals {
        padding: 20px;
        margin: 20px;
    }

    .common_information .targetcon {
        margin-left: 15px;
    }

    .global_form > div > div {
        padding: 0px !important;
    }
</style>
