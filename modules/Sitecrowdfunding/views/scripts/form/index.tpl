<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editphotos.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="sitecrowdfunding_dashboard_content">
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle'=>'Form', 'sectionDescription'=> '')); ?>
    <div class="sitecrowdfunding_dashboard_form">

        <br/>

        <!-- menu -->
        <div class="initiative_menu headline sitecrowdfunding_inner_menu">
            <div class='tabs sitecrowdfunding_nav'>
                <ul class='initiative_menu_nav navigation'>
                    <li>
                        <a id="forms_assigned" class="active" href="javascript:void(0);" onclick="selected_ui('forms_assigned')" >
                            <?php echo $this->translate('Forms Assigned To You'); ?>
                        </a>
                    </li>
                    <li>
                        <a id="forms_submitted" href="javascript:void(0);" onclick="selected_ui('forms_submitted')" >
                            <?php echo $this->translate('Forms Submitted By You'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <br/>
        <!-- content-->
        <div id="forms_content">

            <!-- forms assigned -->
            <?php if ( $this->tab_link == 'forms_assigned') : ?>
                <div class="sitecrowdfunding_dashboard_content">
                    <?php if(count($this->paginator) > 0): ?>
                        <?php foreach ($this->paginator as $val): ?>
                            <?php $form_id = $val['form_id']; ?>
                            <div class="sitecrowdfunding_dashboard_content">
                                <?php $form = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);?>
                                <?php echo $this->partial('_formItem.tpl', 'sitecrowdfunding', array('item' => $form,'project_id' => $this->project_id, 'mode_view' => 'list', 'category' => $this -> category,'type'=>'assign')); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="tip">
                            <span>
                                <?php echo $this->translate("There are no forms available.") ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- forms submitted-->
            <?php if ( $this->tab_link == 'forms_submitted') : ?>
                <div class="sitecrowdfunding_dashboard_content">
                    <?php foreach ($this->paginator as $val): ?>
                        <?php $form_id = $val['form_id']; ?>
                        <div class="sitecrowdfunding_dashboard_content">
                            <?php $form = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);?>
                            <?php echo $this->partial('_formItem.tpl', 'sitecrowdfunding', array('item' => $form,'project_id' => $this->project_id, 'mode_view' => 'list', 'category' => $this -> category,'type'=>'submit')); ?>
                        </div>
                    <?php endforeach; ?>

                    <?php if(count($this->paginator) == 0): ?>
                        <div class="tip">
                            <span>
                                <?php echo $this->translate("You have not submitted any forms") ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>

        <div id="hidden_ajax_data"></div>

    </div>
</div>

<script>
    function selected_ui(tabLink){

        if(tabLink == 'forms_assigned') {
            $(tabLink).addClass('active');
            $('forms_submitted').removeClass('active');
        }
        else if(tabLink == 'forms_submitted') {
            $(tabLink).addClass('active');
            $('forms_assigned').removeClass('active');
        }
        $('forms_content').innerHTML = '<div class="clr"></div><div class="seaocore_content_loader"></div>';
        var params = {
           requestParams:<?php echo json_encode($this->params) ?>
        };

        var request = new Request.HTML({
            url: en4.core.baseUrl + "projects/form/index",
            data: {
                format: 'html',
                subject: en4.core.subject.guid,
                project_id: <?php echo $this->project_id?>,
                tab_link: tabLink
            },
            evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('hidden_ajax_data').innerHTML = responseHTML;
                if($('hidden_ajax_data').getElement('#forms_content')) {
                    $('forms_content').innerHTML = $('hidden_ajax_data').getElement('#forms_content').innerHTML;
                }
                $('hidden_ajax_data').innerHTML = '';
                Smoothbox.bind($('forms_content'));
                en4.core.runonce.trigger();
                if(tabLink =='forms_assigned'){
                    $(tabLink).addClass('active');
                    $('forms_submitted').removeClass('active');
                }
                if(tabLink =='forms_submitted'){
                    $(tabLink).addClass('active');
                    $('forms_assigned').removeClass('active');
                }
            }
        });
        request.send();
    }
</script>

<style>
    .yndform_form_title {
        display: flex;
        justify-content: space-between;
    }

    .headline .tabs > ul > li > a.active {
        border-color: #44AEC1;
        color: #44AEC1;
    }
    .section_header_details h3 {
        font-size: 24px !important;
        border-bottom: 1px solid lightgray;
        padding-bottom: 10px;
    }
    .section_header_details {
        padding-top: 19px;
        padding-bottom: 11px;
        padding-left: 8px;
    }
    h3 {
        font-size: 15px;
        margin-bottom: 5px;
        color: #222;
        font-weight: normal;
        line-height: 20px;

    }
    .inner_content{
        float: right;
        width: 78%;
    }
    .generic_layout_container.o_hidden {
      width: 100%;
    }
    .layout_middle {
        display: flex;
    }
    .layout_right > div,
    .layout_left > div,
    .layout_middle > div,
    .notifications_leftside,
    .notifications_rightside,
    #global_page_core-error-notfound #global_content {
        -moz-border-radius: 6px;
        -webkit-border-radius: unset !important;
         border-radius: unset !important;
        -moz-box-shadow: unset !important; ;
         -webkit-box-shadow: unset !important; ;
         box-shadow:unset !important;
    }
    @media (max-width: 927px){
        .layout_middle{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
    }
</style>