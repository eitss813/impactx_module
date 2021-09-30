<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_dashboard.css'); ?>

<div class="sitecrowdfunding_project_new_steps">
    <form class="global_form">
        <div>
            <div>
                <h3 >
                    Confirmation!
                </h3>
            </div>
        </div>
    </form>
    <div class="project_ready_intro">
        <br/>
        <h2 style="text-align: center;padding: 15px;font-weight: bold">
            Your project is ready. Here is link: <a style="color: #44AEC1;"
                                                    href="<?php echo $this->project->getHref() ?>"><?php echo (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'].$this->
                project->getHref() ?> </a>
        </h2>


        <br/>
        <br/>
        <div class="global_form">
            <div class="sitecrowdfunding_leaders">

                <div class="options_btn">
                    <div class="options_set_1_btn">
                        <?php echo $this->htmlLink(array('route' => 'sitecrowdfunding_specific','action' =>
                        'edit','project_id' => $this->project_id), $this->translate('Edit your project'), array('class'
                        => 'button view_project_btn')); ?>
                    </div>
                    <div class="options_set_2_btn">
                        <?php echo $this->htmlLink($this->project->getHref(), $this->translate('See Your Project Profile'),
                        array("class" => 'button view_project_btn')) ?>
                    </div>
                </div>

            </div>
        </div>
        <br/>
    </div>
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
        text-align: center;
        margin: 10px;
    }

    .form_sub_title {
        font-size: 17px;
        border-bottom: 1px solid #f2f0f0;
        padding: 10px 10px;
        margin: -10px -10px 10px -10px;
    }

    .sitecrowdfunding_leaders_list > div {
        display: inline-block;
        vertical-align: middle;
    }

    .sitecrowdfunding_members_details {
        background: none !important;
        border: none !important;
        padding: 0px !important;
        font-size: 14px !important;
    }

    .sitecrowdfunding_leaders_detail a:hover {
        color: #444;
    }

    .options_set_1_btn {
        float: left;
    }

    .options_set_2_btn {
        float: right;
    }

    .options_set_1_btn > a, .options_set_2_btn > a {
        font-weight: unset !important
    }
</style>

