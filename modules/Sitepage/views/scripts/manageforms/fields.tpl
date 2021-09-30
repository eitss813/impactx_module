
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    edit.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $action = $front->getRequest()->getActionName();
    $controller = $front->getRequest()->getControllerName();

    if( ($module == 'sitepage') && ($controller == 'manageforms') && ($action == 'fields') ):
?>

<script>
    setTimeout(function(){ 
       var x = document.getElementsByClassName('yndform_draggables');
       for (var i = 0; i < x.length; i++) {
           x[i].style.top = 'unset';
       }  
    }, 3000);    
</script>

<style>
    /* START CSS code to enable the scrolling within the div */
    .yndform_draggables {
        position: unset !important;
        border-left: 6px solid #c6c6c6 !important;
    }

    .yndform_manage_fields_fields {
        padding-right: 5px !important;
        width: 63% !important;
    }

    .yndform_manage_fields_options{
         padding-right: 5px;
    }

    li.yndform_manage_fields_item{
        margin: 5px !important;
    }

    .yndform_manage_fields_options, .yndform_manage_fields_fields{
        max-height: 670px;
        overflow-y: scroll;
    }

    .yndform_manage_fields_options::-webkit-scrollbar-thumb {
        background: #c1c1c1; 
         border-radius: 50px;
    }

    .yndform_manage_fields_options::-webkit-scrollbar {
        width: 5px;
    }

    .yndform_manage_fields_fields::-webkit-scrollbar-thumb {
        background: #c1c1c1; 
        border-radius: 50px;
    }

    .yndform_manage_fields_fields::-webkit-scrollbar {
        width: 5px;
    }
    /* END CSS code to enable the scrolling within the div */
</style>
<?php endif; ?>


<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>
<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">
        <?php // include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>

            <?php
            $formId = isset($_GET['id']) ? $_GET['id'] : 0;
             $formmappingTable = Engine_Api::_()->getDbtable('formmappings', 'impactx');
               $formmappingTable->isRoleForm($formId);
               
               if($formmappingTable->isRoleForm($formId)) { ?>
               
                <div  style="display: flex;justify-content: space-between;">
                 <div style="display: flex">
                     <a href="javascript:void(0)" onclick="goBack()" class="yndform_backform" ><span style="font-size: 18px;" class="ynicon yn-arr-left">Back &nbsp;</span></a>
                     
                      <?php echo $this->
                     partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array(
                     'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Manage fields : '.$this->form->getTitle(), 'sectionDescription' => '')); ?>

                 </div>
                
                 <div class="yndform_manage_fields_back" style="float:right;    margin: unset !important;">
                     <?php $url = 'dynamic-form/form/'.$this->form->getIdentity().'/'.$this->form->title.'/type/preview/page_id/'.$this->sitepage->page_id; ?>
                     <a style="font-size: 17px;" href="<?php  echo $url;?>" target="_blank">Preview</a>
                 </div>

             </div>

<?php
              
               }else {
             ?>
             <div  style="display: flex;justify-content: space-between;">
                 <div style="display: flex">
                     <?php echo $this->htmlLink(array(
                     'controller' => 'manageforms',
                     'action' => 'manage',
                     'page_id'=>$this->page_id
                     ), '<span style="font-size: 18px;" class="ynicon yn-arr-left">'.$this->translate('Back ').'&nbsp;</span>',array(
                     'class' => 'yndform_backform'
                     ))
                     ?>
                     <?php echo $this->
                     partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array(
                     'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Manage fields : '.$this->form->getTitle(), 'sectionDescription' => '')); ?>

                 </div>
                
                 <div class="yndform_manage_fields_back" style="float:right;    margin: unset !important;">
                     <?php $url = 'dynamic-form/form/'.$this->form->getIdentity().'/'.$this->form->title.'/type/preview/page_id/'.$this->sitepage->page_id; ?>
                     <a style="font-size: 17px;" href="<?php  echo $url;?>" target="_blank">Preview</a>
                 </div>

             </div>
             <?php }?>
            <div class="sitepage_edit_content">

                  <!-- inner content start -->
                    <?php
                            // Render the admin js
                           echo $this->render('_jsAdmin.tpl');
                    ?>
                <div class="yndform_title_parent" style="#44AEC1 !important;">

                </div>
                <!-- <h2><?php echo $this->translate("Dynamic Form Plugin") ?></h2>
                    <?php if( count($this->navigation) ): ?>
                    <div class='tabs'>
                        <?php
                           // Render the menu
                           //->setUlClass()
                           //   echo $this->navigation()->menu()->setContainer($this->navigation)->render()
                        ?>
                    </div>
                    <?php endif; ?>

                    <h3><?php echo $this->form->getTitle() . ' &#187; ' . $this->translate('Manage Fields') ?></h3>
       -->


                    <div class="admin_fields_options">
                        <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;"><?php echo $this->translate("Save Order") ?></a>
                    </div>

                    <div class="admin_fields_type">
                    </div>

                    <div class="yndform_manage_fields_fields droppable" id="test">
                        <?php if(count($this->secondLevelMaps) == 0): ?>
                        <?php // $this->secondLevelMapsss = array_reverse($this->secondLevelMaps);  ?>
                            <div class="yndform_manage_fields_fields_bg" style="">
                                <ul class="admin_fields">
                                    <?php foreach( $this->secondLevelMaps as $map ): ?>
                                    <?php echo $this->adminFieldMeta($map) ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="yndform_manage_fields_desc">
                                <?php echo $this->translate('YNDYNAMICFORM_MANAGE_FIELDS_DESCRIPTION') ?>
                            </div>
                        <?php endif;?>
                        <?php if(count($this->secondLevelMaps) > 0): ?>
                        <?php  // $this->secondLevelMapsss = array_reverse($this->secondLevelMaps);  ?>
                        <div class="yndform_manage_fields_fields_bg" style="background-color: #f3faff;border: 2px dashed #5f93b4;background-image: none ;">
                            <ul class="admin_fields">
                                <?php foreach( $this->secondLevelMaps as $map ): ?>
                                <?php echo $this->adminFieldMeta($map) ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                    </div>

                    <div class="yndform_manage_fields_options">
                        <div class="yndform_fields_option_items">
                            <div class="yndform_item_name">
                                <span class="yndform_item_name_fields"><?php echo $this->translate('Standard fields') ?></span>
                                <span class="ynicon yn-arr-down yndform_collap  yndform_show"></span>
                                <span class="ynicon yn-arr-up yndform_collap yndform_hide"></span>
                            </div>

                            <?php foreach($this->standardFields as $catLabel => $fieldByCat): ?>
                            <div class="yndform_item_label_fields"><?php echo $catLabel ?></div>
                            <ul class="yndform_manage_fields_items clearfix">
                                <?php foreach($fieldByCat as $type => $label): ?>
                                <?php if(($catLabel == 'Specific') &&
                                      ( $type == 'first_name' || $type == 'last_name' ||    $type == 'gender' ||
                                        $type == 'birthdate' ||   $type == 'website' ||   $type == 'city' ||
                                        $type == 'country' || $type == 'location' || $type == 'zip_code' || $type == 'phone' ||
                                        $type == 'education_level')
                                      ):
                                ?>
                                    <li class="yndform_manage_fields_item">
                                        <span class="yndform_draggables" id="yndform_draggables" data_type="<?php echo $type ?>"><?php echo $label ?></span>
                                    </li>
                                <?php endif;?>
                                <?php if($catLabel == 'Generic'): ?>
                                   <?php if($type != 'integer' ): ?>
                                       <li class="yndform_manage_fields_item">
                                           <span class="yndform_draggables" id="yndform_draggables"  data_type="<?php echo $type ?>"><?php echo $label ?></span>
                                       </li>
                                   <?php endif;?>
                                <?php endif;?>
                                <?php endforeach; ?>
                            </ul>
                            <?php endforeach; ?>
                        </div>
                        <div class="yndform_fields_option_items">
                            <div class="yndform_item_name">
                                <span class="yndform_item_name_fields"><?php echo $this->translate('Advanced fields') ?></span>
                                <span class="ynicon yn-arr-down yndform_collap yndform_show"></span>
                                <span class="ynicon yn-arr-up yndform_collap yndform_hide"></span>
                            </div>
                            <?php $i=0; ?>
                            <ul class="yndform_manage_fields_items clearfix">
                                <?php  foreach($this->advancedFields as $type => $label): ?>
                                <li class="yndform_manage_fields_item">
                                    <div class="yndform_draggables" id="yndform_draggables"   data_type="<?php echo $type ?>">
                                        <?php if($this->advancedFields[$type] == "HTML Content (predefined content)" ): ?>
                                            <div style="display: flex;flex-wrap: wrap;text-align: center;justify-content: center;">
                                                <span style="color: #555 !important;font-size: 12px;text-align: center;justify-content: center;">HTML Content </span>
                                               <span style="color: #555 !important;font-size: 11px;"> (predefined content)</span>
                                            <div>
                                         <?php elseif($this->advancedFields[$type] == "Text Editor (predefined content)" ): ?>
                                                <div style="display: flex;flex-wrap: wrap;text-align: center;justify-content: center;">
                                                    <span style="color: #555 !important;font-size: 12px;text-align: center;justify-content: center;">Text Editor </span>
                                                    <span style="color: #555 !important;font-size: 11px;"> (predefined content)</span>
                                                 <div>
                                        <?php else:?>
                                           <?php print_r($this->advancedFields[$type]); ?>
                                        <?php endif;?>
                                    </div>
                                </li>
                                <?php $i++; ?>
                                <?php  endforeach; ?>
                            </ul>
                        </div>
                        <div class="yndform_fields_option_items">
                            <div class="yndform_item_name">
                                <span class="yndform_item_name_fields"><?php echo $this->translate('User analytics fields') ?></span>
                                <span class="ynicon yn-arr-down yndform_collap yndform_show"></span>
                                <span class="ynicon yn-arr-up yndform_collap yndform_hide"></span>
                            </div>
                            <ul class="yndform_manage_fields_items clearfix">
                                <?php foreach($this->analyticsFields as $type => $label): ?>
                                <li class="yndform_manage_fields_item">
                                    <div class="yndform_draggables" id="yndform_draggables"  data_type="<?php echo $type ?>"><?php print_r($this->analyticsFields[$type]); ?></div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                <!-- inner content end -->

            </div>

        </div>
    </div>
</div>


<script>
    
    function goBack() {
  window.history.back();
}
    
    
    var $j = jQuery.noConflict();
    $j(document).ready(function() {
        console.log('--------------------------------------------------1111222');
        $j('html, body').animate({
            scrollTop: 0
        });
    });
</script>

<script>
    var fieldContainer;
    var draggableObject = {};

    // These fields can be inserted once only
    var singleFields = ['recaptcha'];

    <?php foreach($this->analyticsFields as $field => $label): ?>
        singleFields.push('<?php echo $field ?>');
    <?php endforeach; ?>
    window.addEvent('load', function() {
        window.scrollTo(0, 0);
    });
    window.addEvent('domready', function() {
        $$('.yndform_item_name').addEvent('click', function() {
            $$('.yndform_draggables').each(function(el){
                yndformRemoveDragEvent(el);
            });
            this.getParent('.yndform_fields_option_items').toggleClass('yndform_show_less');
            $$('.yndform_draggables').each(function(el){
                yndformAddDragEvent(el);
            });
        });

        fieldContainer = $$('.yndform_manage_fields_fields')[0];
        $$('.yndform_draggables').each(function(el){
            yndformAddDragEvent(el);
        });
        yndformUpdateFieldsBG();
    });

    function yndformAddDragEvent(el) {
        if (el.hasClass('disabled')) {
            return;
        }
        draggableObject[el.get('data_type')] = new Drag.Move(el, {

            droppables: '.droppable',

            onStart: function() {
                var clonedButton = el.clone(true);
                var container = el.getParent('.yndform_manage_fields_item');
                
                el.inject(container, 'before');
                el.setStyle('z-index', '2');
                el.addClass('yndform_manage_fields_dragging');

                clonedButton.inject(container, 'top');
                yndformAddDragEvent(clonedButton);
            },

            onDrop: function (element, droppable, event) {
                if (droppable){
                    uiSmoothTopFieldCreate(element.get('data_type'));
                }
                element.destroy();
                yndformUpdateFieldsBG();
            },

            onEnter: function (element, droppable) {
            },

            onLeave: function (element, droppable) {
            }
        });
    }

    function yndformRemoveDragEvent(el) {
        if (el.hasClass('disabled')) {
            return;
        }
        if (draggableObject.hasOwnProperty(el.get('data_type'))) {
            draggableObject[el.get('data_type')].detach();
            el.setStyle('position', '');
            el.setStyle('top', '');
            el.setStyle('left', '');
        }
    }

    function yndformUpdateFieldsBG() {
        var fieldList = fieldContainer.getElement('.admin_fields').getChildren('.admin_field');
        if (fieldList.length) {
            fieldContainer.addClass('yndform_manage_fields_bg');
        } else {
            fieldContainer.removeClass('yndform_manage_fields_bg');
        }
        yndformUpdateButtons();
    }

    function yndformDisableSingleButtons(type) {
        var el = $$('.yndform_draggables[data_type=' + type + ']')[0];
        yndformRemoveDragEvent(el);
        el.addClass('disabled');
    }

    function yndformEnableSingleButtons(type) {
        var el = $$('.yndform_draggables[data_type=' + type + ']')[0];
        if (el && el.hasClass('disabled')) {
            el.removeClass('disabled');
            yndformAddDragEvent(el);
            console.log('test');
        }
    }

    function yndformUpdateButtons() {
        var fieldList = fieldContainer.getElement('.admin_fields').getChildren('.admin_field');
        var fieldExist = false;
        singleFields.each(function(type){
            fieldExist = false;
            fieldList.each(function(field) {
                // this field is added
                if (field.get('type') == type) {
                    fieldExist = true;
                }
            });
            if (fieldExist) {
                yndformDisableSingleButtons(type);
            } else {
                yndformEnableSingleButtons(type);
            }
        });
    }
</script>

<style>
    .yndform_manage_fields_bg {

    }
    span.field,span.heading {
        width: 100% !important;
    }
    .item_handle {
        display: none !important;
    }

    i.fa.fa-arrows {
        cursor: move;
    }
    div#error-wrapper {
        display: none;
    }
    div#show_guest-wrapper {
        display: none;
    }
    div#show_registered-wrapper {
        display: none;
    }
    li.yndform_manage_fields_item {
        width: 44%;
        margin: 10px;
    }
    .yndform_manage_fields_items.clearfix {
        display: flex !important;
        flex-wrap: wrap;
    }
    .demo {
        display: none;
    }
    .yndform_manage_fields_dragging{
        color: white !important;
        background-color: #0087c3 !important;
        position: absolute !important;
        height: 40px;
        width: 142px;
        line-height: 40px;
        text-align: center;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2) !important;
        border-left: unset !important;
        cursor: move;
    }
    .yndform_manage_fields_options ul.yndform_manage_fields_items li.yndform_manage_fields_item .yndform_draggables:before {
        content: "";
        width: 5px;
        height: 40px;
        position: absolute;
        left: 0;
        top: 0;
        background-color: #ddd;

    }
    .yndform_manage_fields_item{
        width: 50%;
        float: left;
        cursor: move;
        display: inline-block;
        margin-bottom: 10px;
        padding: 0 10px;
        height: 40px;
    }
    .yndform_draggables{

        border-left: 6px solid lightgray;
        cursor: move;
        height: 40px;
        margin-bottom: 8px;
        display: block;
        width: 145px;
        line-height: 40px;
        text-align: center;
        background: #f8f8f8;
        font-size: 12px;
        font-weight: normal;
        color: #555;
        overflow: hidden;
        white-space: nowrap;
        word-break: break-word;
        word-wrap: break-word;
        text-overflow: ellipsis;
        padding-left: 10px;
        padding-right: 5px;
    }
    
    li.admin_field {

    }
    .edit_choice{

    }
    form.global_form_smoothbox {
        padding: 15px 15px 5px 15px !important;
    }
    .form-elements {
        padding: 15px 15px 5px 15px;
    }
    div#label-wrapper {
        margin: 10px auto;
    }
    div#label-label {
        text-transform: uppercase;
        font-size: .7em;
        margin-bottom: 4px;
    }
    .yndform_manage_fields_fields_bg {
        background-image: url(/application/modules/Yndynamicform/externals/images/manga_field_bg.png?c=21);
        background-position: center 100px;
        background-repeat: no-repeat;
        height: 100%;
        padding: 15px;
        width: 100%;
        min-height: 1000px;
    }
    .yndform_manage_fields_options .yndform_fields_option_items {
        background-color: #fff;
        border: 1px solid #ddd;
        margin-bottom: 10px;
    }
    .yndform_manage_fields_options .yndform_fields_option_items .yndform_item_name {
        display: flex;
        display: -moz-flex;
        display: -webkit-flex;
        align-items: center;
        -moz-align-items: center;
        -webkit-align-items: center;
        width: 100%;
        height: 40px;
        cursor: pointer;
        padding: 0 15px;
        border-bottom: 1px solid #ddd;
        -webkit-transition: all 300ms;
        -moz-transition: all 300ms;
        transition: all 300ms;
    }
    .yndform_manage_fields_options {
        width: 35%;
        display: inline-block;
        float: right;
    }
    .yndform_manage_fields_options .yndform_fields_option_items .yndform_item_name .yndform_item_name_fields {
        font-weight: 700;
        font-size: 12px;
        color: #555;
        text-transform: uppercase;
        float: left;
        display: inline-block;
        width: 100%;
        text-align: left;
    }
    .yndform_manage_fields_desc {
        position: absolute;
        top: 420px;
        padding: 0 110px;
        box-sizing: border-box;
        text-align: center;
        line-height: 22px;
        font-size: 16px;
        font-weight: 400;
        color: #ccc;
    }
    ul.admin_fields .field_extraoptions > a {
        display: inline-block;
        font-weight: bold;
        padding: 6px 8px 5px 17px;
        background-repeat: no-repeat;
        background-position: 6px 6px;
        outline: none;
        font-size: .8em;
        margin-left: 24px;
    }
    ul.admin_fields .field_extraoptions.active > a {
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px;
        border-radius: 4px;
        -moz-border-radius-bottomright: 0px;
        -webkit-border-bottom-right-radius: 0px;
        border-radius-bottomright: 0px;
        -moz-border-radius-bottomleft: 0px;
        -webkit-border-bottom-left-radius: 0px;
        border-radius-bottomleft: 0px;
        background-color: #8197ab;
        color: #fff !important;
        text-decoration: none;
    }
    ul.field_extraoptions_choices .field_extraoptions_choices_options {
        float: right;
        overflow: hidden;
        color: #aaa;
        padding: 5px;
        margin-left: 5px;
    }
    ul.field_extraoptions_choices .field_extraoptions_choices_label {
        display: block;
        overflow: hidden;
        padding: 7px;
        font-size: .8em;
    }
    ul.admin_fields .field_extraoptions_contents_wrapper {
        display: none;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px;
        -moz-border-radius-topleft: 0px;
        -webkit-border-top-left-radius: 0px;
        border-radius-topleft: 0px;
        overflow: hidden;
        position: absolute;
        min-width: 200px;
        padding: 4px;
        background-color: #fdfeff;
        border: 5px solid #8197ab;
        z-index: 99999999;
        margin-top: 22px;
        margin-left: 24px;
    }
    ul.admin_fields .field_extraoptions.active .field_extraoptions_contents_wrapper {
        display: block;
        cursor: default;
    }
    .clearfix:after {
        visibility: hidden;
        display: block;
        font-size: 0;
        content: " ";
        clear: both;
        height: 0;
    }
    .clearfix { display: inline-block; }

    * html .clearfix { height: 1%; }
    .clearfix { display: block; }

    .yn-view-modes-block{
        float: right;
        display: inline-block;
    &:before,
    &:after {
         content: "";
         display: table;
     }
    &:after {
         clear: both;
     }


    .yn-view-modes{
        float: right;

    .yn-view-mode{
        font-size: 0;
        display: inline-block;
        padding: 5px;
        border-radius: 3px;
        cursor: pointer;
        color: #878787;
    +yndform-transition;

    .ynicon{
        font-size: 16px;
    }

    &.active,
    &:hover{
         background: #555;
         color: #FFF;
     }
    }
    }
    }

    /* Default Template */
    .yn-view-modes{
        margin-top: -35px;
        margin-right: 2px;
    }

    .layout_core_container_tabs .yn-view-modes{
        margin-top: -45px;
    }

    /* Responsive Template */
    @media screen and (max-width: 768px){
        .yn-view-modes,
        .layout_core_container_tabs .yn-view-modes{
            margin-top: 0 !important;
            margin-bottom: 10px;
            margin-right: 0px !important;
        }
    }

    /* Purity Template */
    body[class^=ynresponsivepurity-]{
    .yn-view-modes,
    .layout_core_container_tabs .yn-view-modes{
        margin-top: -40px;
    }
    }

    /* Metro UI */
    body.ynresponsive-metro{
    .yn-view-modes{
        margin-top: -49px;
        margin-right: 10px;
        float: right;
        position: relative;
        z-index: 1;
    }

    .layout_core_container_tabs .yn-view-modes{
        margin-top: -44px;
        margin-right: 5px;
    }
    }

    /* Clean Template */
    body.ynresponsive1,
    body[class^=ynresponsiveclean-]{
    .yn-view-modes{
        margin-top: -55px;
    }
    .layout_core_container_tabs .yn-view-modes{
        margin-top: -45px;
    }
    }


    /* Photo Template */
    body.ynresponsive-photo{
    .yn-view-modes{
        margin-top: -44px;
        margin-right: 5px;
        float: right;
        position: relative;
        z-index: 1;
    }

    .layout_core_container_tabs .yn-view-modes{
        margin-top: -44px;
        margin-right: 5px;
    }
    }

    /* Event Template */
    body.ynresponsive-event{
    .yn-view-modes{
        margin-top: -45px;
    }

    .layout_core_container_tabs .yn-view-modes{
        margin-top: -48px;
        margin-right: 10px;
    }
    }

    /* Xmas Template */
    body[class^=ynresponsivechristmas-]{
    .yn-view-modes{
        margin-top: -45px;
    }

    .layout_core_container_tabs .yn-view-modes{
        margin-top: -45px;
        margin-right: 5px;
    }
    }

    =font-ynicon{
         font-family: 'ynicon' !important;
         speak: none;
         font-style: normal;
         font-weight: normal;
         font-variant: normal;
         text-transform: none;
         line-height: 1;
         -webkit-font-smoothing: antialiased;
         -moz-osx-font-smoothing: grayscale;
     }
    =yndform-box-shadow($shadow){
        box-shadow: $shadow;
    }
    =yndform-box-sizing{
         box-sizing: border-box;
     }
    =yndform-transition{
         transition: all 300ms;
     }
    =yndform-display{
         display: flex;
     }
    =yndform-flex{
         flex-direction: column;
     }
    =yndform-truncate{
         overflow: hidden;
         white-space: nowrap;
         word-break: break-word;
         word-wrap: break-word;
         text-overflow: ellipsis;
     }
    =yndform-border-radius($radius){
        -webkit-border-radius: $radius;
        -moz-border-radius: $radius;
        border-radius: $radius;
    }
    =yndform-text-clamp($line){
        word-break: break-word;
        word-wrap: break-word;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        line-height: normal;
        -webkit-line-clamp: $line;
        line-height: 18px;
        height: calc(18*$line)px;
    }

    *[class^=yndform]{
    &,*,&:after,&:before{
                 +yndform-box-sizing;
                 }
    }

    body[id^=global_page_yndynamicform-]{

    .generic_layout_container h3{
        pointer-events: none;
    }

    #form_detail{
    &,*{
      +yndform-box-sizing;
      }
    }

    .ui-datepicker{
    .ui-datepicker-prev,
    .ui-datepicker-next{
        top: 50%;
        margin-top: -11px;
    }
    }

    .h1{
        font-size: 24px;
        font-weight: normal;
        margin-top: 0;
    }
    .ui-datepicker{
        width: auto !important;
        font-size: 1.1em !important;
    }
    #global_wrapper{
        min-height: 450px;
    }
    .h3{
        font-size: 20px;
        font-weight: 700;
        margin-top: 0;
        background-color: transparent;
        padding: 0 !important;
        border-bottom: 0;
        display: inline-block;
        line-height: normal;
    }
    .form-errors{
        float: left;
        display: block;
        width: 100%;
    }
    #global_content{
    input[type="text"],select{
        max-height: none;
        max-width: 100%;
        min-height: initial;
        min-width: initial;
        height: 36px;
        padding: 8px;
    }
    textarea{
        min-height: 80px;
    }
    }

    .yn_dots{
        margin: 0 2px;
        font-style: normal;
    }
    }
    .layout_yndynamicform_single_form,
    .layout_yndynamicform_list_forms,
    .layout_yndynamicform_categories,
    .layout_yndynamicform_list_related_forms,
    .yndform_form_detail_info,
    #yndform_entry-print,
    .yndform_main_content,
    .layout_page_yndynamicform_entries_manage,
    .layout_page_yndynamicform_entries_list,
    .entry_breadcrum{
    a{
        display: inline-block;
        font-weight: normal;
    +yndform-transition;
        color: $theme_link_color;
    &:hover{
         color: $theme_link_color_hover !important;
         text-decoration: none;
     }
    }
    }
    .layout_yndynamicform_single_form{
    a{
    +yndform-truncate;
        max-width: 100%;
        display: inline;
    }
    }

    ul.yndform_forms_browse{
        margin-bottom: 15px;
    & > li{
          float: left;
      }
    }
    span.yndform_slash{
        display: inline-block;
    }
    span.yndform_backslash{
        display: none;
    }
    .yndform_form_category_entries{
        line-height: 18px;
        margin-bottom: 6px;
        color: $theme_font_color_light;
    .yndform_form_category_parent{
        display: inline-block;
        color: $theme_font_color_light;
        font-size: 12px;
        line-height: 18px;
    +yndform-truncate;
        vertical-align: bottom;
        max-width: 78%;
    a{
    &:hover{
         color: $theme_link_color_hover;
         text-decoration: underline;
     }
    }
    }
    .yndform_form_description{
        display: inline-block;
        color: $theme_font_color_light;
    a{
        display: inline-block;
        font-size: 12px;
        color: $theme_font_color_light;
    &:hover{
         color: $theme_link_color_hover;
         text-decoration: underline;
     }
    }
    }
    .yndform_form_entries{
        color: $theme_font_color_light;
        font-size: 12px;
    }
    }
    .yndform_form_center{
        height: 156px;
        border: 1px solid transparent;
        position: relative;
    .yndform_form_title{
    a{
        font-size: 16px;
        line-height: 24px;
        font-weight: bold;
        max-width: 100%;
    +yndform-truncate;
        display: block;
    }
    }
    span.ynicon{
        display: inline-block;
        margin-right: 4px;
        position: relative;
        bottom: -1px;
    }
    .yndform_form_description{
        font-size: $theme_font_size;
    +yndform-text-clamp(2);
        margin-bottom: 8px;
    }
    .yndform_image_parent{
        padding: 20px;
        height: 100%;
        width: 120px;
        float: left;
        display: inline-block;
        position: relative;
        z-index: 1;
    a{
        display: block;
        width: 100%;
        height: 100%;
        background-repeat: no-repeat;
        background-position: center;
        background-size: cover;
        background-origin: border-box;
        position: relative;
        z-index: 1;
    }
    .yndform_parent_opacity{
        opacity: 0.1;
    }
    .yndform_form_image_parent{
        padding: 4px;
        border: 1px solid $theme_border_color;
        width: 100%;
        height: 100%;
        background-color: #fff;
        z-index: 1;
        position: relative;
    +yndform-transition;
    }
    }
    .yndform_info_parent{
        padding: 20px;
        overflow: hidden;
        height: 100%;
        position: relative;
    .yndform_parent_opacity{
        opacity: 0.05;
    }
    & > span,
      div:not(.yndform_parent_opacity){
          z-index: 1;
          position: relative;
      }
    }
    .yndform_info_parent,
    .yndform_image_parent{
    .yndform_parent_opacity{
        content: "";
        width: 100%;
        height: 100%;
        background-color: $theme_link_color;
        display: block;
        bottom: 0;
        left: 0;
        position: absolute;
        z-index: 0;
    }
    }
    .yndform_post_time{
        display: inline-block;
        font-size: 12px;
        color: $theme_font_color_light;
        font-weight: normal;
        line-height: 18px;
    }
    .yndform_form_entries{
        font-size: $theme_font_size;
        color: $theme_font_color;
        font-weight: bold;
    }
    .yndform_opacity{
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
        position: absolute;
    +yndform-transition;
        opacity: 0.05;
    }
    &.yndform_form_no_photo{
    .yndform_image_parent{
    a{
        transition: none;
    }
    }
    &:hover{
    .yndform_image_parent{
    a{
        background-image: url(~/application/modules/Yndynamicform/externals/images/nophoto_form_thumb_hover.png) !important;
    }
    }
    }
    }
    &:hover{
    .yndform_form_image_parent{
        background-color: $theme_link_color;
    }
    }
    }
    .layout_right,
    .layout_left{
    .yndform_form_center{
        height: auto;
    .yndform_image_parent{
        display: none;
    }
    }
    }
    /*----- single form -----*/
    .layout_yndynamicform_single_form{
        margin-bottom: 15px;
    }
    .layout_right,
    .layout_left{
    .layout_yndynamicform_single_form{
    .yn_dots{
        display: none;
    }
    .yndform_form_description{
        margin-bottom: 5px;
    a{
        vertical-align: bottom;
        max-width: 100px;
    +yndform-truncate;
    }
    }
    }
    }
    /*----- listing form -----*/
    .layout_yndynamicform_list_forms{
        position: relative;
    #yndform_total_item_count{
        float: left;
        position: relative;
        color: $theme_font_color_light;
        font-size: 13px;
        width: 100%;
        display: block;
        border-bottom: 1px solid $theme_border_color;
        padding-bottom: 10px;
        text-transform: uppercase;
        font-weight: bold;
        margin-bottom: 10px;
    span{
        font-weight: bold;
    }
    }
    ul.yndform_forms_browse{
        margin: 0 -10px;
    & > li{
          padding: 0 10px;
          margin-bottom: 30px;
          width: 50%;
    .yndform_form_center{
        background-color: #fff;
        border: 1px solid $theme_border_color;
        width: 100%;
        height: 216px;
        border: 0;
    .yn_dots{
        display: none;
    }
    .yndform_max_height{
        max-height: 139px;
        overflow-y: hidden;
        margin-bottom: 3px;
    }
    .yndform_post_time,
    .yndform_form_category_parent{
        display: block;

    }
    .yndform_form_category_entries{
        margin-bottom: 0;
    }
    .yndform_form_category_parent{
        max-width: 100%;
    a{
    +yndform-truncate;
        max-width: 100%;
    }
    }
    .yndform_image_parent{
        width: 150px;
        height: 216px;
        padding: 10px;
        box-shadow: 4px 4px 0px 0px $theme_border_color;
        border: 1px solid #ccc;
    +yndform-transition;
    .yndform_parent_opacity{
        background-color: #fff;
    }
    }
    .yndform_info_parent{
        padding: 16px 0;
    & > div{
          padding: 12px 16px;
          height: 183px;
    .yndform_parent_opacity{
        background-color: #fff;
    }
    .yndform_parent_opacity_border{
        opacity: 0.4;
        width: 100%;
        height: 100%;
        position: absolute;
        border: 1px solid;
        border-color: transparent;
    +yndform-transition;
        left: 0;
        top: 0;
    }
    .yndform_form_title{
        margin-bottom: 2px;
    a{
    +yndform-text-clamp(2);
        line-height: 20px;
        height: auto;
        max-height: 48px;
        white-space: normal;
    }
    }
    }
    }
    .yndform_form_description{
    +yn-text-clamp(12px,18px,4);
        height: auto;
    }
    &:hover{
    .yndform_opacity{
        border-color: transparent;
    }
    .yndform_image_parent{
        box-shadow: 4px 4px 0px 0px $theme_link_color;
        border: 1px solid $theme_link_color_hover;
    }
    .yndform_info_parent{
    & > div{
    .yndform_parent_opacity{
        background-color: $theme_link_color;
        border-color: $theme_link_color;
    }
    .yndform_parent_opacity_border{
        border-color: $theme_link_color;
    }
    }
    }
    }
    }
    }
    }

    ul.yndform_list-view{
    li{
        width: 100%;
        margin-bottom: 0;
    .yndform_form_list_mode{
        padding: 20px 20px 22px 20px;
        height: 90px;
    +yndform-display;
        position: relative;
        border-bottom: 1px solid $theme_border_color;
    .yndofmr_list_view_opacity,
    .yndofmr_list_view_border{
        transition: all 0.3s;
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background-color: #fff;
        opacity: 0.05;
    }
    .yndofmr_list_view_border{
        background-color: transparent;
        opacity: 0.4;
        border: 1px solid;
        border-color: transparent;
    }
    .yndform_image_parent{
        width: 32px;
        height: 100%;
        border: 0;
        box-shadow: none;
        padding: 0;
        margin-right: 12px;
    a{
        border: 0;
    }
    }
    .yndform_info_parent{
        padding: 0;
        display: inline-flex;
        width: 100%;
        height: 120%;
    .yndform_form_title{
        padding: 0;
    a{
        font-size: 14px;
        line-height: 18px;
    }
    }
    .yndform_parent_opacity{
        display: none;
    }

    .yndform_form_title_parent{
        padding: 0;
        flex-grow: 1;
        -moz-flex-grow: 1;
        -webkit-flex-grow: 1;
        width: 50%;
        height: auto;
    a{
        vertical-align: sub;
        -webkit-line-clamp: 1 !important;
        line-height: 18px !important;
        height: 18px !important;
        max-height: 18px !important;
        padding-right: 10px;
    }
    & > *{
          font-size: 12px;
          text-align: initial;
    a{
        text-align: initial;
    }
    }
    span{
        display: inline-block;
        color: $theme_font_color_light;
    }
    .yndform_form_category_parent{
    a{
        max-width: 70%;
    }
    }
    }
    .yndform_form_content_parent{
        flex-grow: 1;
        -moz-flex-grow: 1;
        -webkit-flex-grow: 1;
        padding: 0;
        width: 50%;
    .yndform_form_content_child{
    +yndform-display;
        justify-content: space-between;
    .yndform_form_category_entries{
        display: inline-block;
    }
    }
    .yndform_form_description{
        color: $theme_font_color;
        text-align: initial;
    +yn-text-clamp(12px,18px,2);
        height: auto;
    }
    }
    }
    &:hover{
    .yndofmr_list_view_opacity{
        opacity: 0.05;
        background-color: $theme_link_color;
    }
    .yndofmr_list_view_border{
        border-color: $theme_link_color;
        opacity: 0.4;
    }
    .yndform_image_parent {
        box-shadow: none;
        -moz-box-shadow: none;
        -webkit-box-shadow: none;

        border: 0;
    }
    }
    }
    .yndform_form_no_photo{
    .yndform_image_parent{
    a{
        transition: none;
        -moz-transition: none;
        -webkit-transition: none;
    }
    }
    &:hover{
    .yndform_image_parent{
    a{
        background-image: url(~/application/modules/Yndynamicform/externals/images/nophoto_form_thumb_hover.png) !important;
    }
    }
    }
    }
    }
    }

    .yndform_form_center{
        display: none;
    }
    .yndform_list-view{
    .yndform_form_list_mode{
        display: block;
    }
    }
    .yndform_grid-view{
    .yndform_form_grid_mode{
        display: inline-block;
    }
    }
    }


    /*----- related form -----*/
    .layout_yndynamicform_list_related_forms{
    ul.yndform_forms_browse{
        margin-bottom: 15px;
    & > li{
          width: 100%;
          margin-bottom: 10px;
    .yndform_form_description{
    a{
        max-width: 100px;
        vertical-align: bottom;
    +yndform-truncate;
    }
    }
    &:first-of-type{
         padding-top: 0;
     }
    &:last-of-type{
         padding-bottom: 0;
         border-bottom: 0;
     }
    .yndform_form_center{
        border: 0;
        border: 1px solid $theme_border_medium_color;
    +yndform-transition;
        padding: 10px;
    .yn_dots{
        display: none;
    }
    .yndform_form_category_entries{
        margin-bottom: 0;
    }
    .yndform_post_time,
    .yndform_form_category_parent{
        display: block;
        max-width: 100%;
    }
    .yndform_form_category_parent{
    a{
    +yndform-truncate;
        max-width: 100%;
    }
    }
    .yndform_info_parent{
        padding: 0;
    .yndform_parent_opacity{
        background-color: #fff;
    }
    }
    .yndform_opacity{
    +yndform-transition;
        background-color: #fff;
        opacity: 0.05;
    }
    &:hover{
    .yndform_opacity{
        background-color: $theme_link_color;
    }
    }
    }
    }
    .yndform_form_entries{
        display: block;
    }
    }
    }
    .layout_middle{
    .layout_yndynamicform_list_related_forms{
    .yn_dots{
        display: inline-block !important;
    }
    ul{
        margin: 0 -10px;
    li{
        width: 50%;
        padding: 0 5px;
    .yndform_form_center{
        padding: 0;
        padding-top: 10px;
    .yndform_info_parent{
        padding: 20px;
    }
    .yndform_post_time,
    .yndform_form_category_parent{
        display: inline-block;
    }
    .yndform_form_category_parent{
    a{
        vertical-align: bottom;
    }
    }
    }
    }
    }
    }
    }
    /*----- search widget -----*/
    .layout_yndynamicform_browse_search{
        margin-bottom: 15px;
    select,input{
        width: 100%;
    }
    #filter_form{
        padding: 15px;
    .form-elements{
        padding: 0;
    .optional{
        font-size: $theme_font_size;
        line-height: 18px;
        color: $theme_font_color;
    }
    #order-wrapper,
    #category_id-wrapper,
    #keyword-wrapper{
        margin-bottom: 15px;
    .optional{
        margin-bottom: 8px;
    }
    }
    }
    }
    *{
    +yndform-box-sizing;
    }
    }
    /*----- category widget -----*/
    .layout_yndynamicform_categories{
    &,*{
      +yndform-box-sizing;
      }
    .yncategories_submenu{
        display: none;
    }
    .yncategories_mainmenu{
        display: inline-block;
    }
    .generic_list_widget{
    .yncategories_item{
    +yndform-transition;
        max-width: 100%;
        white-space: nowrap;
        border-bottom: 1px solid $theme_border_color;
        position: relative;
        padding-left: 32px;

    .yncategories_no_collapsed{
    .yncategories_mainmenu{
        display: none;
    }
    .yncategories_submenu{
        display: inline-block;
    }
    }
    .yncategories_have_child,
    .yncategories_dont_have_child{
        position: absolute;
        top: 0;
        left: 0;
    }
    .yncategories_last_sub_item{
    span.ynicon{
        color: #ccc;
    }
    }
    span.ynicon{
        text-align: center;
        width: 32px;
        font-size: 8px;
        position: relative;
        line-height: 32px;
        height: 32px;
        cursor: pointer;
        padding-top: 3px;
        vertical-align: middle;
        color: $theme_link_color;
    }
    a{
    +yndform-truncate;
        max-width: 100%;
        padding: 8px 0;
        font-size: $theme_font_size;
        line-height: 18px;
        font-weight: bold;
        display: inline-block;
        vertical-align: middle;
        height: 32px;
    &:before{
         content: "";
         position: absolute;
         width: 50%;
         height: 100%;
         right: 0;
         top: 0;
     }
    }
    &:last-of-type{
         border-bottom: 0;
     }
    &.level_2{
         padding-left: 52px;
    .yncategories_have_child,
    .yncategories_dont_have_child{
        left: 20px;
    }
    }
    &.level_3{
         padding-left: 70px;
    a{
        font-weight: normal;
    }
    .yncategories_have_child,
    .yncategories_dont_have_child{
        left: 40px;
    }
    }
    &:hover{
         background-color: #f3faff;
     }
    }
    }
    }

    /*----- form detail info -----*/
    .yndform_color_wrapper{
    .form-element{
        position: relative;
        width: 50% !important;
    input{
        width: 100% !important;
    }
    }
    .description{
        display: none;
    }
    }
    span.ui-datepicker-trigger{
        font-size: 16px;
        width: 35px;
        position: absolute;
        right: 1px !important;
        height: 34px;
        top: 1px !important;
        padding: 9px 9px;
        background-color: #eee;
        border-left: 1px solid #999;
        border-top-right-radius: 3px;
        border-bottom-right-radius: 3px;
        cursor: pointer;
    }
    .yndform_form_detail_info{
        margin-bottom: 15px;
        padding-top: 10px;
    .yndform_form_detail_info_title{
        margin-bottom: 5px;
    }
    .yndform_form_category_entries{
    .yndform_form_category_parent{
    a{
        color: $theme_link_color;
    }
    }
    .yndform_form_detail_info_creation_date{
        display: inline-block;
        color: $theme_font_color_light;
        line-height: 12px;
        font-size: 12px;
        font-weight: normal;
    }
    }
    .yndform_form_detail_info_description{
        font-size: $theme_font_size;
        color: $theme_font_color;
        line-height: 18px;
        margin-bottom: 10px;
    }
    .yndform_form_detail_info_parent{
        margin-bottom: 10px;
    .yndform_form_detail_info_stast{
        display: inline-block;
        float: left;
    .yndform_form_detail_info_stast_items{
    +yndform-display;
    .yndform_detail_count_item{
    +yndform-display;
    +yndform-flex;
        padding: 0 14px;
        border-right: 1px solid $theme_border_medium_color;
        box-sizing: content-box !important;
    &.yndform_no_border_right{
         border-right: 0;
     }
    &.yndform_no_padding{
         padding-left: 0;
     }
    .yndform_detail_count_number{
        font-size: 14px;
        color: $theme_font_color;
        font-weight: bold;
        vertical-align: bottom;
        margin-bottom: 5px;
        display: block;
        box-sizing: content-box !important;
    }
    .yndform_detail_count_label{
        font-size: 11px;
        font-weight: normal;
        line-height: 18px;
        color: $theme_font_color_light;
        text-transform: uppercase;
        display: block;
        box-sizing: content-box !important;
    }
    }
    }
    }
    .yndform_form_detail_info_button{
        display: inline-block;
        float: right;
        padding: 2px 0;
    a{
    +yndform-border-radius(3px);
        border: 1px solid #999;
        padding: 8px;
        min-height: 36px;
        max-height: 36px;
        line-height: normal;
        font-size: $theme_font_size;
        color: $theme_font_color;
        font-weight: bold;
        display: inline-block;
        background-color: #fff;
    &:hover{
         color: $theme_button_background_color !important;
         border-color: $theme_button_background_color;
     }
    &.yndform_share_button{
         margin: 0 5px;
     }
    &.yndform_liked{
         color: $theme_button_background_color;
         border-color: $theme_button_background_color;
     }
    span{
        display: inline-block;
        margin-right: 6px;
    }
    }
    }
    }
    }
    .yndform_rating{
        padding: 0 1px;
    span.ynicon{
        padding: 0 2px;
        font-size: 24px;
        font-weight: normal;
        color: #ccc;
        cursor: pointer;
        display: inline-block;
        vertical-align: bottom;
        margin: 0 -3px;
    &.rating{
         color: #ffa800;
     }
    .description{
        display: none;
    }
    }
    }
    #global_page_yndynamicform-entries-edit{
    #yndform_buttons_group-element{
        max-width: none;
    a{
        display: inline-block;
    span{
        margin: 0 5px;
    }
    }
    }
    .form-elements{
    .form-wrapper{
    .file-element{
        min-height: 30px;
        height: auto;
        border-bottom: 1px solid $theme_border_medium_color;
        line-height: 30px;
        width: 40%;
    span{
        margin-right: 5px;
    }
    a:last-of-type{
        float: right;
    &:hover{
         color: #d12f2f;
         text-decoration: none;
     }
    }
    a:first-of-type{
        max-width: 90%;
        display: inline-block;
        vertical-align: bottom;
    +yndform-truncate;
    }
    &:last-of-type{
         margin-bottom: 10px;
     }
    }
    }
    }
    }
    .layout_page_yndynamicform_form_detail,
    #global_page_yndynamicform-entries-edit{
    .global_form{
    & > div{
          width: 100%;
      }
    .form-elements{
        padding: 20px;
        border: 1px solid $theme_border_color;
        margin-bottom: 15px;
        overflow: hidden;
    /*-----title-----*/
    .yndform_section_break{
        padding-bottom: 10px;
        border-bottom: 1px solid $theme_border_color;
        margin-bottom: 15px;
    &.section_content{
         border-bottom: 0;
    & > .form-element{
          margin-bottom: 15px;
          border-bottom: 1px solid $theme_border_color;
          padding-bottom: 10px;
          margin-top: 2px;
      }
    & > .form-label{
          display: block;
      }
    }
    &.field_form{
         border-bottom: 0;
    .form-label{
        padding-bottom: 12px;
        border-bottom: 1px solid $theme_border_color;
        margin-bottom: 18px;
    }
    }
    &.field_html{
    .form-label{
        border-bottom: 0;
    }
    }
    .form-label{
        text-align: left;
        display: block;
        width: 100% !important;
        padding: 0;
        margin-bottom: 0 !important;
        max-width: none !important;
        padding-right: 0 !important;
    label{
        display: block;
        font-size: 18px;
        color: $theme_font_color;
        font-weight: bold;
    }
    }
    .form-element{
        display: block;
        width: 100%;
        max-width: none;
        min-width: auto;
        margin-bottom: 0;
        margin-top: 0;
    .description{
        max-width: none;
        min-width: auto;
        display: block;
        width: 100%;
        font-size: 12px;
        line-height: 18px;
        color: $theme_font_color_light;
        margin: 0;
        margin-top: 2px !important;
        padding: 0;
    }
    }
    .yndform_section_break_element{
        clear: both;
        float: none;
    .form-label{
        max-width: 150px !important;
    .optional{
        font-size: $theme_font_size;
    }
    }
    }
    }
    .form-wrapper-heading{
        padding-bottom: 10px;
        border-bottom: 1px solid $theme_border_color;
        margin-bottom: 15px;
        height: auto;
        margin-top: 0;

    span{
        display: block;
        font-size: 18px;
        color: #555;
        text-align: left;
        display: block;
        width: 100%;
        padding: 0;
        margin-bottom: 0;
        max-width: none !important;
        padding-right: 0 !important;
        position: relative;

    }
    }
    /*-----fields-----*/
    .form-wrapper{
    +yndform-display;
    +yndform-flex;
        margin-bottom: 15px;
        width: 100%;
    .form-label{
        padding: 0;
        margin: 0;
        width: 100% !important;
        font-size: $theme_font_size;
        color: $theme_font_color;
        text-align: left;
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    label{
        font-weight: bold;
    &.required{
    &:after{
         content: "*";
         margin-left: 2px;
         color: #d12f2f;
     }
    }
    }
    }
    .form-element{
        margin-bottom: 0;
        display: block;
        width: 100%;
        max-width: none;
    .yndform_page_next_text,
    .yndform_page_next_image{
        float: right;
    &:after{
         content: "\\e97d";
     +font-ynicon;
         font-size: 12px;
         vertical-align: middle;
         margin-left: 2px;
     }
    }
    .yndform_page_prev_text,
    .yndform_page_prev_image{
        float: right;
        margin-right: 10px;
    &:before{
         content: "\\e97f";
     +font-ynicon;
         font-size: 16px;
         vertical-align: bottom;
         margin-right: 2px;
     }
    }
    .description{
        margin: 0;
        margin-top: 7px;
        max-width: none;
    }
    select{
        width: 50%;
    }
    textarea,
    input[type="text"]{
        width: 100%;
        max-width: none;
        min-width: auto;
    }
    textarea{
        min-height: 80px;
    }
    select[multiple="multiple"]{
        height: auto;
        min-height: 80px !important;
        width: 100%;
    }
    .form-options-wrapper{
        margin-top: 0;
        margin-bottom: 0;
    li{
        display: block;
        margin-bottom: 2px;
    label{
        vertical-align: middle;
        margin-bottom: 0;
        display: inline-block;
    }
    &:after{
         visibility: hidden;
         display: block;
         font-size: 0;
         content: " ";
         clear: both;
         height: 0;
     }
    }
    &:after{
         visibility: hidden;
         display: block;
         font-size: 0;
         content: " ";
         clear: both;
         height: 0;
     }
    }
    }
    }
    .yndform_agreement{
        background-color: #f8f8f8;
        padding: 20px;
        max-height: 150px;
        overflow-y: scroll;
        margin-bottom: 10px;
    }
    .yndform_agreement_checkbox{
        float: right;
        text-align: right;
    }
    }
    .yndform_button_submit{
        float: right;
        margin-right: 0;
    }
    }
    }

    /*----- progress - bar - step -----*/
    div[class^=yndform_progress_indicator_]{
    }
    .yndform_progress_indicator{
        display: block;
        width: 100%;
        margin-bottom: 15px !important;
        float: none !important;
    .yndform_indicator_progress_step_parent{
        width: 100%;
    ul.yndform_indicator_progress_step_items{
        display: flex;
        border: 1px solid $theme_border_color;
        background-color: #fff;
        height: 50px;
    li.yndform_progress_step_item{
    +yndform-transition;
        flex-grow: 1;
        -moz-flex-grow: 1;
        -webkit-flex-grow: 1;
        position: relative;
        text-align: center;
        height: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding-left: 18px;
        background-color: #eee;
    &.active{
         background-color: $theme_button_background_color;
    .yndform_progree_step_break{
        border-top-color: #eee;
        border-bottom-color: #eee;
    &:before{
         border-left-color: $theme_button_background_color;
     }
    }
    .yndform_progree_step_text_inverse,
    .yndform_progree_step_text{
        background-color: #fff;
        color: $theme_button_background_color;
    }
    .yndform_progree_step_text{
        font-size: 11px;
        font-weight: bold;
        background-color: $theme_button_background_color;
        color: #fff;
    }
    }
    .yndform_progree_step_break{
        width: 0px;
        height: 0px;
        border-top: 24px solid transparent;
        border-bottom: 24px solid transparent;
        top: 0;
        right: -15px;
        position: absolute;
        border-left: 14px solid #ddd;
        z-index: 1;
    +yndform-transition;
    &:before{
     +yndform-transition;
         content: "";
         position: absolute;
         width: 0px;
         right: 2px;
         height: 0px;
         bottom: 0;
         margin-bottom: -24px;
         border-top: 24px solid transparent;
         border-bottom: 24px solid transparent;
         border-left: 14px solid #eee;
         z-index: 2;
     }
    }
    .yndform_progree_step_text_inverse{
        font-weight: bold;
        padding-top: 6px;
        font-size: 14px;
        background-color: #ddd;
        color: #888;
        border-radius: 100%;
        display: inline-block;
        min-width: 32px;
        z-index: 1;
        height: 32px;
        line-height: 20px;
        text-align: center;
    }
    .yndform_progree_step_text{
        font-size: 11px;
        text-transform: uppercase;
        color: #888;
        font-weight: bold;
    +yndform-truncate;
        display: inline-block;
        max-width: 100%;
        padding-left: 2px;
    }
    &:last-of-type{
    .yndform_progree_step_break{
        display: none;
    }
    }
    }
    }
    }

    .yndform_indicator_progress_bar_parent{
    .yndform_indicator_progress_bar_items{
        display: flex;
        padding: 25px 2px;
    .yndform_progress_bar_item{
        flex-grow: 1;
        -moz-flex-grow: 1;
        -webkit-flex-grow: 1;
        position: relative;
        height: 6px;
        background-color: #ddd;
        display: inline-block;
    &.active{
    .ynform_progress_bar_percen{
        display: block;
    }
    .ynform_progress_bar_bg{
        background-color: $theme_button_background_color;
    }
    .ynform_progress_bar_circle{
        background-color: #fff;
        border: 1px solid $theme_button_background_color;
        z-index: 1;
    .ynform_progress_bar_circle_child{
        display: inline-block;
        width: 17px;
        height: 17px;
        background-color: $theme_button_background_color;
        border-radius: 100%;
        width: 9px;
        height: 9px;
        right: 3px;
        top: 3px;
        position: absolute;
    }
    }
    }
    &.actived{
    .ynform_progress_bar_circle,
    .ynform_progress_bar_bg{
        background-color: #fff;
    }
    }
    .ynform_progress_bar_circle{
        display: inline-block;
        width: 17px;
        height: 17px;
        background-color: #ddd;
        position: absolute;
        top: -50%;
        right: 0;
        margin-top: -3px;
        border-radius: 100%;
    .ynform_progress_bar_circle_child_decs{
        position: absolute;
        bottom: -20px;
    +yndform-truncate;
        max-width: 100px;
        right: -30px;
        text-transform: uppercase;
        color: #888;
        font-size: 12px;
    }
    }
    .ynform_progress_bar_percen{
        position: absolute;
        right: -15px;
        top: -25px;
        font-size: 14px;
        font-weight: bold;
        color: $theme_link_color;
        line-height: 18px;
        display: none;
    }
    .ynform_progress_bar_bg{
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #ddd;
    +yndform-transition;
    }
    &:first-of-type{
    &,.ynform_progress_bar_bg{
          border-top-left-radius: 3px;

          border-bottom-left-radius: 3px;
      }
    }
    &:last-of-type{
    &,.ynform_progress_bar_bg{
          border-top-right-radius: 3px;

          border-bottom-right-radius: 3px;
      }
    .ynform_progress_bar_circle_child_decs{
        right: 0;
    }
    .ynform_progress_bar_percen{
        right: 0;
    }
    }
    }
    }
    }
    }

    /*----- print page -----*/
    #global_page_yndynamicform-entries-print{
    #global_content_simple{
        width: 100%;
    }
    #global_content{
        width: 100% !important;
    }
    }
    #yndform_entry-print{
        width: 715px;
        margin: 0 auto;
    .yndform_submitby{
        margin-bottom: 10px;
    }
    }
    #yndform_entry-print,
    #yndform_user_entry-print{
    &,& *{
        +yndform-box-sizing;
        }
    .yndform_top_nav{
        height: 60px;
        line-height: 60px;
        background-color: #eee;
        border-bottom: 1px solid $theme_border_medium_color;
        text-align: center;
        margin-bottom: 45px;
    .yndform_nav_parent{
        display: inline-block;
        height: 100%;
    .ynform_print_page_back_btn{
        float: left;
        text-transform: uppercase;
        border: 1px solid #999;
    +yndform-border-radius(3px);
        padding: 8px;
        line-height: normal;
        font-weight: bold;
        color: $theme_font_color;
        display: inline-block;
        margin-top: 12px;
    span{
        display: inline-block;
        font-size: 12px;
        vertical-align: middle;
    }
    &:hover{
         border-color: $theme_link_color_hover;
     }
    }
    .ynform_print_page_print_btn{
        float: right;
    +yndform-transition;
        margin-top: 16px;
        font-size: 12px;
        text-transform: uppercase;
        display: inline-block;
    span{
        display: inline-block;
        font-size: 12px;
        margin-right: 2px;
    }
    }
    }
    }
    .yndform_form_detail_info{
        padding-top: 0;
        margin: 0 auto;
    .yndform_form_detail_info_parent{
    .yndform_form_detail_info_stast{
    .yndform_form_detail_info_stast_items{
    .yndform_detail_count_item{
        flex-direction: initial;
        line-height: normal;
    .yndform_detail_count_number{
        margin-right: 2px;
        margin-bottom: 0;
    }
    }
    }
    }
    }
    }
    .yndform_main_content{
        margin: 0 auto;
        float: none;
        padding: 20px;
        border: 1px solid $theme_border_color;
    .form-wrapper{
    +yndform-display;
        margin-bottom: 15px;
        flex-direction: column;
    .form-label{
        text-align: left;
        font-weight: bold;
        font-size: $theme_font_size;
        padding-top: 0;
        padding-left: 0;
        margin-bottom: 0;
        display: block;
        width: 100% !important;
        padding-right: 0;
    }
    .form-element{
        color: $theme_font_color;
        font-size: $theme_font_size;
        margin-bottom: 0;
    .file-element{
        min-height: 30px;
        height: auto;
        border-bottom: 1px solid $theme_border_color;
        line-height: 30px;
    &:last-of-type{
         border-bottom: 0;
     }
    span.ynicon{
        color: $theme_font_color;
        margin-right: 4px;
        vertical-align: middle;
    }
    a{
        line-height: 18px;
        color: $theme_font_color;
    span{
        display: inline-block;
        margin-left: 5px;
    }
    }
    }
    .yndform_rating span.ynicon{
        margin-right: -1px;
    }
    }
    }
    .yndform_section_break{
        padding-bottom: 10px;
        border-bottom: 1px solid $theme_border_color;
        margin-bottom: 15px;
    &.section_content{
         border-bottom: 0;
    & > .form-element{
          margin-bottom: 15px;
          border-bottom: 1px solid $theme_border_color;
          padding-bottom: 10px;
          margin-top: 2px;
      }
    & > .form-label{
          display: block;
      }
    }
    &.field_form{
         border-bottom: 0;
    .form-label{
        padding-bottom: 12px;
        border-bottom: 1px solid $theme_border_color;
        margin-bottom: 18px;
    }
    }
    &.field_html{
    .form-label{
        border-bottom: 0;
    }
    }
    .form-label{
        text-align: left;
        display: block;
        width: 100% !important;
        padding: 0;
        margin-bottom: 0;
        max-width: none !important;
        padding-right: 0 !important;
    label{
        display: block;
        font-size: 18px;
        color: $theme_font_color;
    }
    }
    .form-element{
        display: block;
        width: 100%;
        max-width: none;
        min-width: auto;
        margin-bottom: 0;
        margin-top: 0;
    span{
        max-width: none;
        min-width: auto;
        display: block;
        width: 100%;
        font-size: 12px;
        line-height: 18px;
        color: $theme_font_color_light;
        margin: 0;
        margin-top: 2px !important;
        padding: 0;
    }
    }
    .yndform_section_break_element{
        clear: both;
        float: none;
    .form-label{
        max-width: 150px !important;
    .optional{
        font-size: $theme_font_size;
    }
    }
    }
    }
    }
    .yndform_form_detail_info_stast_items{
    .yndform_detail_count_item{
    &:first-of-type{
         padding-left: 0;
    span.ynicon{
        position: relative;
        bottom: -3px;
        margin-right: 4px;
    }
    }
    }

    }
    }
    #yndform_user_entry-print{
        margin-bottom: 20px;
    .yndform_section_break{
        padding-top: 15px;
    }
    .yndform_main_content{
        border-top: 0;
        border-left: 0;
        border-right: 0;
        padding: 0;
        width: 100%;
    }
    }
    .entry_breadcrum{
        width: 100%;
    .yndform_title_parent{
        border-bottom: 1px solid $theme_border_color !important;
        margin-bottom: 10px;
    }
    h1{
        display: inline-block;
        padding-bottom: 10px;
        max-width: 90%;
    }
    .yndform_text{
        font-size: 16px;
        font-weight: bold;
    a{
        font-weight: bold;
    }
    }
    .yndform_slash,
    .yndform_backslash{
        position: relative;
        top: -2px;
    }
    & > div:nth-of-type(2){
          margin-bottom: 10px;
      }
    a.yndform_backform{
        float: right;
        font-size: 14px;
        font-weight: bold;
        padding-top: 6px;
    span{
        font-size: 12px;
        margin-right: 2px;
        display: inline-block;
    }
    }
    & > div > a,.yndform_text_submit{
          font-size: 12px;
      }
    .yndform_text_submit{
        color: $theme_font_color_light;
    }
    }
    #global_page_yndynamicform-entries-view{
    #yndform_buttons_group-element{
    span.ynicon{
        margin-right: 4px;
        display: inline-block;
    }
    }
    .entry_detail{
        display: block;
        width: 100%;
    }
    }
    /*----- my entries -----*/
    .layout_page_yndynamicform_entries_manage,
    .layout_page_yndynamicform_entries_list{
    .yndform_my_entries{
        padding: 0;
        width: 100%;
        margin-bottom: 15px;
    +yndform-box-sizing;
    & > div{
          width: 100%;
      }
    .form-elements{
    button{
        height: 36px;
        margin-top: 0;
        margin-bottom: 0;
        margin-right: 0;
        width: 100%;
    }
    .form-wrapper{
        flex-grow: 1;
        -moz-flex-grow: 1;
        -webkit-flex-grow: 1;
        padding: 0 5px;
    +yndform-box-sizing;
        margin: 0;
    .form-label{
        display: none;
    }
    input[type="text"],
    select{
        padding: 8px;
        margin: 0;
        width: 100%;
    }
    .form-element{
        width: 100%;
        position: relative;
        margin-bottom: 0;
        min-width: auto;
        max-width: none;
        margin-right: 0;
    }
    }
    #keyword-wrapper{
        flex-grow: 2;
        -moz-flex-grow: 2;
        -webkit-flex-grow: 2;
    }
    #search-wrapper{
        flex-grow: initial;
        -moz-flex-grow: initial;
        -webkit-flex-grow: initial;
        width: 90px;
    }
    #start_date-wrapper,
    #to_date-wrapper,
    #entry_id-wrapper{
        width: 10%;
    }
    #advsearch-wrapper{
        width: 180px;
        flex-grow: initial;
        -moz-flex-grow: initial;
        -webkit-flex-grow: initial;
    #advsearch-element{
        display: inline-flex;
        justify-content: flex-start;
        align-items: center;
        padding: 0px 8px;
        background-color: #f7f7f7;
        height: 36px;
        border: 1px solid $theme_border_medium_color;
        border-radius: 3px;
    +yndform-box-sizing;
        line-height: 36px;
    input{
        margin-top: 0;
    }
    label{
        margin-bottom: 0;
        cursor: pointer;
    }
    }
    }
    }
    }
    table{
        width: 100%;

    th{
        padding: 10px;
    &:first-of-type{
         width: 4%;
     }
    }
    td{
        padding: 0 10px;
    &.yndform_attached{
    span{
        display: inline-block;
    }
    }
    }
    thead{
        border-bottom: 1px solid $theme_border_dark_color;
    tr{
    th{
        font-weight: bold;
    a{
        font-weight: bold;
    }
    }
    }
    }
    tbody{
    tr{
        border-bottom: 1px solid $theme_border_color;
    &:nth-child(even){
         background-color: #f8f8f8;
     }
    td{
    &:last-of-type{
         text-align: right;
    ul{
        margin: 5px 0;
        display: inline-flex;

    li{
    a{
        background-color: #fff;
        border: 1px solid $theme_border_medium_color;
        border-left: 1px solid $theme_border_medium_color;
        border-right: 0;
        padding: 5px;
        color: $theme_font_color;
        font-size: 12px;
    }
    &:first-of-type{
    a{
        border-left: 1px solid $theme_border_medium_color;
        border-top-left-radius: 3px;

        border-bottom-left-radius: 3px;
    }
    }
    &:last-of-type{
    a{
        border-right: 1px solid $theme_border_medium_color;
        border-top-right-radius: 3px;

        border-bottom-right-radius: 3px;
    }
    }
    }
    }
    }
    &:nth-child(2){
         min-height: 39px;
     }
    &:before{
         display: none;
     }
    }
    }
    }
    }
    }
    .layout_page_yndynamicform_entries_manage{
    .yndform_my_entries{
    .form-elements{
    +yndform-display;
        border: 1px solid #eee;
        padding: 15px;
    }
    }
    }
    .layout_page_yndynamicform_entries_list{
    .yndform_my_entries{
    .form-elements{
    #start_date-wrapper,
    #to_date-wrapper,
    #entry_id-wrapper{
        width: auto;
    }
    }
    }
    #elements-wrapper{
        border: 1px solid #eee;
        padding: 15px;
    +yndform-display;
    }
    table.yndform_my_entries_table{
    tbody{
    tr td span.yndform_span{
        font-size: 9px;
        font-weight: bold;
        color: #ff7800;
    }
    }
    }
    .yndform_title_parent{
        border-bottom: 1px solid $theme_border_color;
    }
    h1{
        display: inline-block;
        padding-bottom: 10px;
        max-width: 90%;
    }
    .yndform_text{
        font-size: 16px;
        font-weight: bold;
        color: $theme_font_color;
        padding: 8px 0;
    }
    a.yndform_backform{
        float: right;
        font-size: 14px;
        font-weight: bold;
        padding-top: 6px;
    span{
        font-size: 12px;
        margin-right: 2px;
        display: inline-block;
    }
    }
    }
    /*----- logic -----*/
    #yndform_conditional_container{
        background-color: #f8f8f8;
        border: 1px solid $theme_border_color;
        border-top: 0;
        padding: 15px;
    .form-wrapper{
        margin-right: 6px;
        display: inline-flex;
    select{
        width: 80px;
    }
    .form-element{
        width: auto;
    }
    .form-label{
        text-align: left;
        line-height: 36px;
        padding-left: 10px;
    }
    }
    #yndform_conditional_list{
        clear: both;
        display: flex;
        padding-top: 10px;
    +yndform-box-sizing;
        margin-bottom: 10px;
        flex-direction: column;
    select{
        margin-right: 10px;
        min-width: 180px;
        max-width: 180px;
        width: auto;
        flex-grow: 1;
        -moz-flex-grow: 1;
        -webkit-flex-grow: 1;
    }
    input{
        height: 36px;
        flex-grow: 2;
        -moz-flex-grow: 2;
        -webkit-flex-grow: 2;
    }
    .yndform_conditional_logic_item{
        display: flex;
        margin-bottom: 10px;
    span.ui-datepicker-trigger{
        font-size: 16px;
        position: absolute;
        right: 1px;
        top: 1px;
        padding: 9px 9px;
        background-color: #eee;
    +yndform-box-sizing;
        border-left: 1px solid #999;
        border-top-right-radius: 3px;
        border-bottom-right-radius: 3px;
        cursor: pointer;
    }
    .yndform_conditional_logic_options{
        width: auto;
        flex-grow: 1;
        -moz-flex-grow: 1;
        -webkit-flex-grow: 1;
        margin-right: 0;
        position: relative;
    input{
        width: 100%;
        flex-grow: initial;
        -moz-flex-grow: initial;
        -webkit-flex-grow: initial;
    }
    }
    .yndform_conditional_logic_buttons{
        margin-left: 10px;
        height: 36px;
    span{
        width: 36px;
        height: 36px;
        text-align: center;
        font-size: 16px;
        padding-top: 10px;
    +yndform-box-sizing;
        color: #888;
    +yndform-transition;
        cursor: pointer;
        display: inline-block;
        position: relative;
        height: 36px;
    +yndform-transition;
        background-color: #e0e0e0;
        border: 1px solid #ccc;
    &.yndform_conditional_logic_add{
         border-top-left-radius: 3px;
         border-bottom-left-radius: 3px;
     }
    &.yndform_conditional_logic_remove{
         border-top-right-radius: 3px;
         border-bottom-right-radius: 3px;
         border-left: 0;
     }
    &:hover{
         background-color: $theme_link_color;
         border-color: $theme_link_color;
         color: #fff;
     }
    }
    &.yndform_nominus{
    .yndform_conditional_logic_add{
        border-top-right-radius: 3px;
        border-bottom-right-radius: 3px;
    }
    .yndform_conditional_logic_remove{
        display: none;
    }
    }
    }
    }
    }
    }

    #global_page_yndynamicform-index-list-forms{
    .yn-view-modes-block{
        position: absolute;
        top: 0;
        right: 0;
        z-index: 1;
    .yn-view-modes{
        margin-top: 0;
        margin-right: 0;
        margin-bottom: 0 !important;
    }

    }
    }

    #yndform_moderator_search,
    #yndform_my_entries{
    *{
    +yndform-box-sizing;
    }
    }

    /*----------  check 4.9  ----------*/
    body[id^=global_page_yndynamicform-] {
    .layout_right,
    .layout_left{
    a{
        display: inline;
        overflow: visible;
        text-overflow: clip;
        white-space: normal;
    }
    }
    }
    body[id^=global_page_yndynamicform-][class*=insignia]{
    .layout_yndynamicform_browse_search #filter_form{
        padding: 0;
    }
    }

    /*----- rtl -----*/
    html[dir="rtl"]{

    .yndform_form_center{
    span.ynicon{
        margin-right: 0;
        margin-left: 4px;
    }
    .yndform_image_parent{
        float: right;
    }
    }

    .global_form{
    .form-elements{
    .yndform_section_break{
    .form-label{
        text-align: right;
    }
    }
    }
    }


    #yndform_conditional_container{
    #yndform_conditional_list{
    .yndform_conditional_logic_item{
    .yndform_conditional_logic_buttons{

    &.yndform_nominus{
    span.yndform_conditional_logic_add{
    +yndform-border-radius(3px);
    }
    }

    span.yndform_conditional_logic_add{
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;

        border-top-right-radius: 3px;
        border-bottom-right-radius: 3px;
    }

    span.yndform_conditional_logic_remove{
        border-top-left-radius: 3px;
        border-bottom-left-radius: 3px;

        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-left: 1px solid #ccc;
        border-right: 0;
    }
    }
    }
    }
    }

    #yndform_conditional_container{
    #yndform_conditional_list{
    select{
        margin-right: 0;
        margin-left: 10px;
    }
    .yndform_conditional_logic_item{
    .yndform_conditional_logic_buttons{
        margin-left: 0;
        margin-right: 10px;
    }
    }
    }

    }

    .layout_page_yndynamicform_entries_manage,
    .layout_page_yndynamicform_entries_list{
    table{
    tbody{
    tr{
    td:first-of-type{
        padding-left: 0;
        padding-right: 10px;
    }
    td:last-of-type{
        text-align: left;
        padding-right: 0;
        padding-left: 10px;
    }
    }
    }
    thead{
    tr{
    th{
        text-align: right;
    }
    }
    }
    ul li:first-of-type a{
        border-left: 0;
        border-right: 1px solid $theme_border_color;
        border-top-right-radius: 0;
        border-bottom-right-radius: 3px;
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
    ul li:last-of-type a{
        border-right: 0 !important;
        border-left: 1px solid $theme_border_color;
        border-top-left-radius: 3px;
        border-bottom-left-radius: 0;
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    }
    }

    #global_page_yndynamicform-entries-edit{
    .global_form{
    .form-elements{
    .form-wrapper{
    .form-element{
    .yndform_page_next_text,
    .yndform_page_prev_text{
        float: left;
    &:after{
         display: inline-block;
         content: "\\e97f";
         margin-left: 0;
         margin-right: 2px;
         vertical-align: middle;
     +font-ynicon;
     }
    }
    .yndform_page_prev_text{
    &:before{
         display: none;
     }
    &:after{
         content: "\\e97d";
     +font-ynicon;
         display: initial;
     }
    }
    }
    }
    }
    }
    }

    #global_page_yndynamicform-entries-view{
    #yndform_buttons_group-element{
    span.ynicon{
        margin-right: 0;
        margin-left: 4px;
    }
    }
    }

    #yndform_entry-print,
    #yndform_user_entry-print{
    .yndform_main_content{
    .yndform_section_break{
    .form-label{
    label{
        text-align: right;
    }
    }
    }
    .form-wrapper{
    .form-label{
        text-align: right;
    }
    .file-element{
    span.ynicon{
        margin-right: 0;
        margin-left: 4px;
    }
    a span{
        margin-left: 0;
        margin-right: 5px;
    }
    }
    }
    }
    }

    .layout_page_yndynamicform_form_detail{
    .global_form{
    .form-elements{
    .form-wrapper{
    .form-label{
    label.required:after{
        margin-left: 0;
        margin-right: 2px;
    }
    }
    .form-element{
    .yndform_page_next_text{
        float: left;
    &:after{
         content: none;
     }
    &:before{
         content: "\\e97f";
     +font-ynicon;
         font-size: 16px;
         vertical-align: middle;
         margin-right: 2px;
     }
    }
    .yndform_page_prev_text{
        float: left;
        margin-left: 10px;
        margin-right: 0;
    &:before{
         content: none;
     }
    &:after{
         content: "\\e97d";
     +font-ynicon;
         font-size: 16px;
         vertical-align: middle;
         margin-left: 2px;
     }
    }
    }
    }
    }
    }
    }

    .layout_yndynamicform_list_related_forms{
    .yndform_form_entries,
    .yndform_form_category_parent{
    span.ynicon{
        margin-right: 0;
        margin-left: 4px;
    }
    }
    }
    span.ui-datepicker-trigger{
        right: auto !important;
        left: 0 !important;
        margin-left: 1px;
        border-left: 0 !important;
        border-right: 1px solid #999 !important;

        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;

        border-bottom-left-radius: 3px !important;
        border-top-left-radius: 3px !important;
    }
    .layout_page_yndynamicform_form_detail{
    .global_form{
    .form-elements{
    .form-wrapper{
    .form-label{
        text-align: right;
        padding-right: 0;
    }
    }
    }
    }
    }
    .yndform_progress_indicator{
        float: none;
    .yndform_indicator_progress_bar_parent{
    .yndform_indicator_progress_bar_items{
    .yndform_progress_bar_item{
    .ynform_progress_bar_circle{
        right: auto;
        left: -1px;
    }
    .ynform_progress_bar_percen{
        right: auto;
        left: -8px;
    }
    &:last-of-type{
    .ynform_progress_bar_percen{
        right: auto;
        left: 0;
    }
    }
    &:first-of-type{
    .ynform_progress_bar_bg{
        border-top-right-radius: 3px;

        border-bottom-right-radius: 3px;
    }
    }
    &:last-of-type{
    .ynform_progress_bar_bg{
    +yndform-border-radius(0);
    }
    .ynform_progress_bar_circle_child_decs{
        right: auto;
        left: 0;
    }
    }
    }
    }
    }
    .yndform_indicator_progress_step_parent{
    ul.yndform_indicator_progress_step_items{
    li.yndform_progress_step_item{
        padding-left: 0;
        padding-right: 18px;
    .yndform_progree_step_text{
        padding-left: 0;
        padding-right: 4px;
    }
    .yndform_progree_step_break{
        transform: rotate(180deg);
        -moz-transform: rotate(180deg);
        -webkit-transform: rotate(180deg);
        right: auto;
        left: -15px;
    }
    }
    }
    }
    }
    .layout_page_yndynamicform_form_detail{
    .global_form{
    .form-elements{
    .form-wrapper{
    .form-element{
    .yndform_agreement_checkbox{
        float: left;
        text-align: left;
    }
    .form-options-wrapper{
    li{
        margin-right: 0;
        margin-left: 18px;
    }
    }
    }
    }
    }
    }
    .global_form .yndform_button_submit{
        float: left;
    }
    }
    .yndform_form_detail_info{
    .yndform_form_detail_info_parent{
    .yndform_form_detail_info_button{
        float: left;
    a{
    span{
        margin-right: 0;
        margin-left: 6px;
    }
    }
    }
    .yndform_form_detail_info_stast{
        float: right;
    .yndform_form_detail_info_stast_items{
    .yndform_no_border_right{
        border-right: 1px solid $theme_border_medium_color;
    }
    .yndform_no_padding{
        padding-left: 14px;
        padding-right: 0;
        border-right: 0;
    }
    }
    }
    }
    }
    span.yndform_slash{
        display: none;
    }
    span.yndform_backslash{
        display: inline-block;
    }
    .layout_yndynamicform_single_form{
    .yndform_form_center{
    .yndform_form_category_entries{
    span.ynicon{
        margin-left: 4px;
        margin-right: 0;
    }
    }
    }
    }
    .layout_yndynamicform_list_forms{
    ul.yndform_forms_browse{
        margin-bottom: 15px;
    & > li{
    .yndform_form_center{
    span.ynicon{
        margin-right: 0;
        margin-left: 4px;
    }
    .yndform_form_title .yndform_form_entries{
        float: left;
    }
    .yndform_form_category_entries{
        clear: both;
    }
    }
    }
    }
    }
    body[id^=global_page_yndynamicform-]{

    .paginationControl{
        float: right;
    & > li{
          float: right;
      }
    }

    ul.form-errors > li{
        background-position: right center;
        padding-left: .6em;
        padding-right: 32px;

    }

    .form-errors{
        float: right;
    }

    .ui-datepicker-header{
    & > a{
          transform: rotate(180deg);
    &.ui-datepicker-prev{
         left: auto;
         right: 2px;
     }
    &.ui-datepicker-next{
         right: auto;
         left: 2px;
     }
    }
    }
    }

    .layout_yndynamicform_list_forms{
    #yndform_total_item_count{
        float: right;
    }
    }

    .layout_yndynamicform_categories{
    .generic_list_widget{
    .yncategories_item{
        padding-left: 0;
        padding-right: 32px;
    span.yncategories_mainmenu{
        transform: rotate(180deg);
        padding-top: 0;
        padding-bottom: 3px;
    }

    .yncategories_have_child,
    .yncategories_dont_have_child{
        left: auto;
        right: 0;
    }
    }
    .yncategories_item.level_2{
        padding-left: 0;
        padding-right: 66px;
    .yncategories_have_child,
    .yncategories_dont_have_child{
        left: 0;
        right: 20px;
    }
    }
    .yncategories_item.level_3{
        padding-left: 0;
        padding-right: 70px;
    .yncategories_have_child,
    .yncategories_dont_have_child{
        left: 0;
        right: 40px;
    }
    }
    }
    }
    ul.yndform_forms_browse{
    & > li{
          float: right;
      }
    }
    #global_page_yndynamicform-entries-view,
    #global_page_yndynamicform-entries-edit{
    .entry_breadcrum{
    & > span{
          padding-left: 0;
          padding-right: 5px;
      }
    a.yndform_backform{
        float: left;
    span{
        margin-left: 2px;
        margin-right: 0;
        transform: rotate(180deg);
    }
    }
    }
    }
    #yndform_entry-print,
    #yndform_user_entry-print{
    .yndform_form_detail_info_stast_items{
    .yndform_detail_count_item{
    &:first-of-type{
         padding-right: 0;
         padding-left: 14px;
         border-right: 0;
    span.ynicon{
        margin-right: 0;
        margin-left: 4px;
    }
    }
    span.yndform_detail_count_number{
        margin-right: 0 !important;
        margin-left: 2px;
    }
    }
    }
    }
    .layout_page_yndynamicform_entries_list{
    a.yndform_backform{
        float: left;
    span{
        margin-right: 0;
        margin-left: 2px;
        transform: rotate(180deg);
    }
    }
    }
    #global_page_yndynamicform-entries-edit{
    .global_form{
    .form-elements{
    .form-wrapper{
    .form-label{
        text-align: right;
    }
    }
    }
    }
    }
    .global_form_popup{
    #fieldset-buttons{
    a{
        display: inline-block;
    }
    }
    }
    .layout_yndynamicform_categories{
    .generic_list_widget{
    .yncategories_item a:before{
        right: auto;
        left: 0;
    }
    }
    }

    .yn-view-modes-block{
        float: left;
    .yn-view-modes{
        float: left;
    }
    }

    .layout_yndynamicform_list_forms{
    ul.yndform_list-view{
    li{
    .yndform_form_list_mode{
    .yndform_image_parent{
        margin-right: 0;
        margin-left: 12px;
    }
    .yndform_info_parent{
    & > div{
    .yndform_form_title{
    a{
        padding-right: 0;
        padding-left: 10px;
        display: inline-block;
        white-space: pre;
    }
    }
    }
    }
    }
    }
    }
    }

    #global_page_yndynamicform-index-list-forms{
    .yn-view-modes-block{
        right: auto;
        left: 0;
    }
    }

    .layout_yndynamicform_list_forms{
    ul.yndform_list-view{
    li{
    .yndform_form_list_mode{
    .yndform_info_parent{
    .yndform_form_title_parent{
    .yndform_form_category_parent{
    a{
        padding-right: 0;
        padding-left: 10px;
    }
    }
    }
    }
    }
    }
    }
    }

    .layout_yndynamicform_list_forms{
    ul.yndform_forms_browse{
    & > li{
    .yndform_form_center{
    .yndform_image_parent{
        box-shadow: -4px 3px 0px 0px $theme_border_color;
    }
    &:hover{
    .yndform_image_parent{
        box-shadow: -4px 3px 0px 0px $theme_link_color;
    }
    }
    }
    }
    }
    }
    }

    #global_page_yndynamicform-index-my-moderated-forms{
    .yn-view-modes-block{
    .yn-view-modes{
        margin: -45px 0 0;
    }
    }
    .yndform_forms_browse{
        clear: both;
    }
    .layout_yndynamicform_list_forms{
        display: flex;
        flex-direction: column;

    .yndform_total_item_count{
        order: 1;
    }
    .yn-view-modes-block{
        order: 2;
    }
    .yndform_forms_browse {
        order: 3;
    }
    #yndform_total_item_count{
        padding-top: 10px;
        pointer-events: none;
    }
    }
    }










    .clearfix:after{
        visibility: hidden;
        display: block;
        font-size: 0;
        content: " ";
        clear: both;
        height: 0;
    }
    .clearfix{
        display: inline-block;
    }
    * html .clearfix{
        height: 1%;
    }
    .clearfix{
        display: block;
    }
    body[id^=global_page_yndynamicform-admin-]{
    a.smoothbox{
        margin: 20px 0;
    }
    .tip{
        margin-top: 15px;
    }
    .ui-datepicker{
        width: auto !important;
    }
    #global_wrapper{
        min-height: 450px;
    }
    }
    =yndform-box-sizing{
         box-sizing: border-box;
         -moz-box-sizing: border-box;
         -webkit-box-sizing: border-box;
     }
    =yndform-transition{
         -webkit-transition: all 300ms;
         -moz-transition: all 300ms;
         transition: all 300ms;
     }
    #yndform_manage_form_table{
    & > .uiYnfbpp_scroll{
          overflow-y: visible;
      }
    }
    .yndform_manage_form_admin_search,
    #yndform_manage_form_table,
    .yndform_edit_form,
    #global_page_yndynamicform-admin-confirmation-create .form-elements,
    #global_page_yndynamicform-admin-settings-level .form-elements{
    input[type="text"], textarea, select{
        max-height: none;
        max-width: 100%;
        min-height: initial;
        min-width: initial;
    +yndform-box-sizing;
        border-radius: 3px;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        height: 36px;
    }
    a{
        display: inline-block;
        font-weight: normal;
    +yndform-transition;
    &:hover{
         text-decoration: none;
     }
    }
    }
    /*----- manage form admin -----*/
    .yndform_manage_form_admin_search{
    .search{
        padding: 0;
        width: 100%;
    +yndform-box-sizing;
    #filter_form{
    .buttons{
        margin-top: 0;
        margin-bottom: 0;
    }
    & > div:last-of-type{
          margin-left: 5px;
    button{
        height: 35px;
    }
    }
    .form-wrapper{
        width: 20%;
        padding: 0 5px;
    +yndform-box-sizing;
        margin: 0;
    .form-label{
        display: none;
    }
    input[type="text"],
    select{
        padding: 8px;
        margin: 0;
        width: 100%;
    }
    .form-element{
        position: relative;
    span.ui-datepicker-trigger{
        font-size: 16px;
        position: absolute;
        right: 1px;
        top: 1px;
        padding: 9px 9px;
        background-color: #eee;
    +yndform-box-sizing;
        border-left: 1px solid #999;
        -webkit-border-top-right-radius: 3px;
        -moz-border-top-right-radius: 3px;
        border-top-right-radius: 3px;
        border-bottom-right-radius: 3px;
        -moz-border-bottom-right-radius: 3px;
        -webkit-border-bottom-right-radius: 3px;
        cursor: pointer;
    }
    }
    }
    #status-wrapper{
        width: 11%;
    }
    }
    }
    }
    #global_page_yndynamicform-admin-manage-index{
    .search{
    #filter_form{
        display: flex;
        display: -webkit-flex;
        display: -moz-flex;
        border: 1px solid #eee;
    }
    }
    }
    #global_page_yndynamicform-admin-entries-list{
    .yndform_manage_form_admin_search{
        margin-top: 15px;
    .search{
    #filter_form{
        padding: 0;
        border: 0;
        display: block;
    #elements-wrapper{
        display: flex;
        display: -webkit-flex;
        display: -moz-flex;
        width: 100%;
        border: 1px solid #eee;
        padding: 15px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
    .form-wrapper{
        flex-grow: 1;
        -moz-flex-grow: 1;
        -webkit-flex-grow: 1;
        padding: 0 5px;
    +yndform-box-sizing;
        margin: 0;
        width: auto;
    }
    #advsearch-wrapper{
        width: 180px;
        flex-grow: initial;
        -moz-flex-grow: initial;
        -webkit-flex-grow: initial;
    #advsearch-element{
        display: inline-flex;
        display: -moz-inline-flex;
        display: -webkit-inline-flex;
        justify-content: flex-start;
        align-items: center;
        padding: 0px 8px;
        background-color: #f7f7f7;
        height: 36px;
        border: 1px solid $theme_border_medium_color;
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
    +yndform-box-sizing;
        line-height: 36px;
        width: 100%;
        position: relative;
    input{
        margin-top: 0;
    }
    label{
        margin-bottom: 0;
        cursor: pointer;
    }
    }
    }
    button#search{
        height: 36px;
        width: 100%;
    }
    }
    #conditional_logic-wrapper{
        width: 100%;
        margin-left: 0;
    #yndform_conditional_container{
        border-top: 0;
        width: 100%;
        margin: 0;
        margin-bottom: 15px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
    #yndform_conditional_list{
        padding-top: 0;
        margin-bottom: 0;
    .yndform_conditional_logic_item{
    & > select,input{
          margin-top: 0;
      }
    }
    }
    }
    }
    }
    }
    }
    table{
    thead{
    tr{
    th:nth-of-type(2){
        width: 4%;
    }
    }
    }
    tbody{
    tr{
    td{
    &:last-of-type{
         text-align: right;
     }
    span.yndform_span{
        font-size: 9px;
        font-weight: bold;
        color: #ff7800;
    }
    }
    }
    }
    ul{
        margin: 5px 0;
        display: inline-flex;
        display: -moz-inline-flex;
        display: -webkit-inline-flex;
    li{
    a{
        background-color: #fff;
        border: 1px solid $theme_border_medium_color;
        border-left: 0;
        border-right: 0;
        padding: 5px;
        color: $theme_font_color;
    }
    &:first-of-type{
    a{
        border-left: 1px solid $theme_border_medium_color;
        border-top-left-radius: 3px;
        -moz-border-top-left-radius: 3px;
        -webkit-border-top-left-radius: 3px;

        border-bottom-left-radius: 3px;
        -moz-border-bottom-left-radius: 3px;
        -webkit-border-bottom-left-radius: 3px;
    }
    }
    &:last-of-type{
    a{
        border-right: 1px solid $theme_border_medium_color;
        border-top-right-radius: 3px;
        -moz-border-top-right-radius: 3px;
        -webkit-border-top-right-radius: 3px;

        border-bottom-right-radius: 3px;
        -moz-border-bottom-right-radius: 3px;
        -webkit-border-bottom-right-radius: 3px;
    }
    }
    &.yndform_border{
    a{
        border-left: 1px solid $theme_border_color;
        border-right: 1px solid $theme_border_color;
    }
    }
    }
    }
    }
    #multidelete_form{
        margin-bottom: 20px;
    & > a{
          overflow: hidden;
          display: inline-block;
    .ynicon{
        margin-right: 5px;
    }
    }
    .buttons{
        overflow: hidden;
        display: inline-block;
        margin: 0 auto;
    }
    }
    }
    #yndform_manage_form_table{
    button{
        height: 35px;
    }
    button#delete{
        background-color: #d12f2f;
        border-color: #b72c2d;
        text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.3);
        box-shadow: 0 1px 0 #f07676 inset;
    &:hover{
         background-color: #d95252;
     }
    }
    .admin_table{
        padding-top: 10px;
        margin-bottom: 10px;
    thead{
    tr{
    th{
        background-color: #fff;
    a{
        font-weight: bold;
    }
    }
    }
    }
    tbody tr td{
        padding: 15px 10px;
    &:nth-of-type(2){
         max-width: 128px;
     }
    &:last-child{
         padding: 0;
         padding-right: 5px;
         vertical-align: middle;
     +yndform-box-sizing;
    & > div{
          background-color: #fff;
          border: 1px solid $theme_border_medium_color;
      +yndform-box-sizing;
          border-radius: 3px;
          -moz-border-radius: 3px;
          -webkit-border-radius: 3px;
          display: flex;
          display: -moz-flex;
          display: -webkit-flex;
          border-right: 1px solid $theme_border_color;
    .yndform_option_btn{
        display: inline-block;
        padding: 0;
        cursor: pointer;
        position: relative;
    &.yndform_show{
         background-color: $theme_border_color;
     }
    span.ynicon{
        padding: 10px 8px;
        font-size: 8px;
        display: inline-block;
    }
    .yndform_option_items{
        display: none;
        position: absolute;
        background: #fff;
        min-width: 90px;
        right: 0;
        border-radius: 3px;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        z-index: 1;
        top: 28px;
        border: 1px solid $theme_border_medium_color;
        border-top: 0;
    li{
    a{
        margin: 0;
        padding: 0 7px;
        text-align: left;
        height: 32px;
        display: block;
        line-height: 32px;
        font-size: 12px;
        color: $theme_font_color;
        border-top: 1px solid $theme_border_color;
    &:hover{
         background-color: #f8f8f8;
     }
    }
    }
    }
    }
    & > div{
          padding: 4px 10px;
          text-align: center;
          flex-grow: 1;
          -moz-flex-grow: 1;
          -webkit-flex-grow: 1;
          border-left: 1px solid $theme_border_medium_color;
    &:first-of-type{
         border-left: 0;
     }
    a{
        padding: 4px 0;
    +yndform-transition;
        color: $theme_font_color;
        font-size: 11px;
        font-weight: normal;
        text-align: center;
    &:hover{
         text-decoration: none;
         color: $theme_link_color_hover;
     }
    }
    }
    }
    }
    }
    }
    }
    /*----- member level settings -----*/
    #global_page_yndynamicform-admin-settings-level{
    #submit-label{
        display: block !important;
    }
    #credit-wrapper,
    #max_credit-wrapper,
    #period-wrapper{
        padding-top: 0px;
    }
    #first_amount-wrapper,
    #credit-wrapper{
        float: left;
        min-width: 58%;
    }
    #first_credit-wrapper,
    #max_credit-wrapper,
    #period-wrapper{
        float: left;
        clear: none;
    }
    #first_credit-label,
    #max_credit-label,
    #period-label{
        display: none;
    }
    #max_credit-wrapper,
    #period-wrapper{
        width: 120px;
    }
    #max_credit-wrapper input,
    #period-wrapper input{
        width: 100px;
    }
    }
    /*----- logic -----*/
    #yndform_conditional_container{
        background-color: #f8f8f8;
        border-bottom: 1px solid $theme_border_color;
        border-top: 1px solid $theme_border_color;
        margin: 0 -15px;
        padding: 15px;
        margin-bottom: 15px;
    .form-wrapper{
        margin-right: 6px;
        display: inline-flex;
        display: -moz-inline-flex;
        display: -webkit-inline-flex;
    select{
        width: 80px;
    }
    .form-element{
        width: auto;
    }
    .form-label{
        text-align: left;
        line-height: 36px;
        padding-left: 10px;
    }
    }
    #yndform_conditional_list{
        clear: both;
        display: flex;
        display: -moz-flex;
        display: -webkit-flex;
        padding-top: 10px;
    +yndform-box-sizing;
        margin-bottom: 10px;
        flex-direction: column;
        -moz-flex-direction: column;
        -webkit-flex-direction: column;
    select{
        margin-right: 10px;
        min-width: 180px;
        max-width: 180px;
        width: auto;
        flex-grow: 1;
        -moz-flex-grow: 1;
        -webkit-flex-grow: 1;
    }
    input{
        height: 36px;
        flex-grow: 2;
        -moz-flex-grow: 2;
        -webkit-flex-grow: 2;
    }
    .yndform_conditional_logic_item{
        display: -webkit-flex;
        display: flex;
        display: -moz-flex;
        margin-bottom: 10px;
    span.ui-datepicker-trigger{
        font-size: 16px;
        position: absolute;
        right: 1px;
        top: 1px;
        padding: 9px 9px;
        background-color: #eee;
    +yndform-box-sizing;
        border-left: 1px solid #999;
        -webkit-border-top-right-radius: 3px;
        -moz-border-top-right-radius: 3px;
        border-top-right-radius: 3px;
        border-bottom-right-radius: 3px;
        -moz-border-bottom-right-radius: 3px;
        -webkit-border-bottom-right-radius: 3px;
        cursor: pointer;
    }
    .yndform_conditional_logic_options{
        width: auto;
        flex-grow: 1;
        -moz-flex-grow: 1;
        -webkit-flex-grow: 1;
        position: relative;
    input{
        width: 100%;
        flex-grow: initial;
        -moz-flex-grow: initial;
        -webkit-flex-grow: initial;
    }
    }
    .yndform_conditional_logic_buttons{
        margin-left: 10px;
        height: 36px;
    span{
        width: 36px;
        height: 36px;
        text-align: center;
        font-size: 16px;
        padding-top: 10px;
    +yndform-box-sizing;
        color: #888;
    +yndform-transition;
        cursor: pointer;
        display: inline-block;
        position: relative;
        height: 36px;
    +yndform-transition;
    +yndform-box-sizing;
        background-color: #e0e0e0;
        border: 1px solid $theme_border_medium_color;
    &.yndform_conditional_logic_add{
         border-top-left-radius: 3px;
         -moz-border-top-left-radius: 3px;
         -webkit-border-top-left-radius: 3px;
         border-bottom-left-radius: 3px;
         -moz-border-bottom-left-radius: 3px;
         -webkit-border-bottom-left-radius: 3px;
     }
    &.yndform_conditional_logic_remove{
         border-top-right-radius: 3px;
         -moz-border-top-right-radius: 3px;
         -webkit-border-top-right-radius: 3px;
         border-bottom-right-radius: 3px;
         -moz-border-bottom-right-radius: 3px;
         -webkit-border-bottom-right-radius: 3px;
     }
    &:hover{
         background-color: $theme_link_color;
         border-color: $theme_link_color;
         color: #fff;
     }
    }
    &.yndform_nominus{
    .yndform_conditional_logic_add{
        border-top-right-radius: 3px;
        -moz-border-top-right-radius: 3px;
        -webkit-border-top-right-radius: 3px;
        border-bottom-right-radius: 3px;
        -moz-border-bottom-right-radius: 3px;
        -webkit-border-bottom-right-radius: 3px;
    }
    .yndform_conditional_logic_remove{
        display: none;
    }
    }
    }
    }
    }
    }
    #global_content_simple{
    .form-element{
        position: relative;
    span.ui-datepicker-trigger{
        font-size: 16px;
        position: absolute;
        right: 1px;
        top: 1px;
        padding: 9px 9px;
        background-color: #eee;
    +yndform-box-sizing;
        border-left: 1px solid #999;
        -webkit-border-top-right-radius: 3px;
        -moz-border-top-right-radius: 3px;
        border-top-right-radius: 3px;
        border-bottom-right-radius: 3px;
        -moz-border-bottom-right-radius: 3px;
        -webkit-border-bottom-right-radius: 3px;
        cursor: pointer;
    }
    }
    #yndform_conditional_container{
        margin-top: 10px;
        border: 1px solid $theme_border_color;
    .optional{
        font-weight: normal !important;
    }
    .form-element{
        width: auto;
    select{
        width: 100px;
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
    }

    }
    #yndform_conditional_list{
    input{
    +yndform-box-sizing;
        height: 36px;
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
    }
    select{
        height: 36px;
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        width: 100px;
        min-width: auto;
        max-width: none;
    }
    .yndform_conditional_logic_item{
    .yndform_conditional_logic_buttons{
        display: inline-flex;
        display: -moz-inline-flex;
        display: -webkit-inline-flex;
        flex-grow: 1;
        -moz-flex-grow: 1;
        -webkit-flex-grow: 1;
    }
    }
    }
    }
    }

    /*----- edit form -----*/
    body[id^=global_page_yndynamicform-admin-form-],
    body[id^=global_page_yndynamicform-admin-notification],
    body[id^=global_page_yndynamicform-admin-confirmation],
    body[id^=global_page_yndynamicform-admin-import-export-]{
    ul.form-options-wrapper{
        margin-bottom: 5px;
    &:after{
         visibility: hidden;
         display: block;
         font-size: 0;
         content: " ";
         clear: both;
         height: 0;
     }
    li{
        margin-right: 25px;
        float: left;
    }
    }
    /*----- edit form menu -----*/
    #global_content_simple{
        width: 100%;
    +yndform-box-sizing;
    }
    .yndform_color_wrapper,
    #text_color_box-wrapper,
    #background_color_box-wrapper{
    .form-element{
        width: auto !important;
    }
    }
    .form-element{
        display: inline-block;
        width: 100%;
        position: relative;
        font-size: 12px;
    textarea{
        width: 100%;
        height: auto;
    }
    .yndform_color_picker{
        position: absolute;
        border-radius: 100%;
        width: 22px;
        height: 26px;
        top: 50%;
        margin-top: -13px;
        left: 150px;
        cursor: pointer;
    input[type="color"]{
        cursor: pointer;
        width: 100%;
        height: 100%;
        background-color: transparent;
    }
    }
    .description{
        margin-bottom: 0;
    }
    }
    .global_form_popup{
    +yndform-box-sizing;
    input[type="text"], select{
        max-height: none;
        max-width: 100%;
        min-height: initial;
        min-width: initial;
    +yndform-box-sizing;
        border-radius: 3px;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        height: 36px;
    }
    select{
        width: 50%;
    }
    textarea{
        min-height: 80px;
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
    }
    #next_button_text-element,
    #pre_button_text-element{
    .description{
        font-weight: bold;
        margin-bottom: 8px;
    }
    }
    label[for='conditional_enabled']{
        font-weight: bold;
        margin-bottom: 5px;
        display: inline-block;
    }
    .form-label{
        margin-bottom: 8px;
    label{
        font-weight: bold;
    }
    }
    #show_registered-label{
        font-weight: bold;
    }

    #toValues-label,
    #emailValues-label,
    #enable-label,
    #type-label{
        display: none;
    }
    #toValues-wrapper,
    #emailValues-wrapper{
        border: 1px solid $theme_border_medium_color;
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        padding: 4px;
        min-height: 70px;
        max-height: 70px;
        overflow-y: scroll;
    #toValues-element,
    #emailValues-element{
        width: auto;
        float: left;
        position: initial;
    & > input{
          width: 50px;
          display: inline-block;
          border: 0;
          float: left;
          outline: none;
          box-shadow: none;
          background-color: #fff;
          height: 30px;
          padding: 0;
    &:focus{
         outline: none;
     }
    }
    }
    span.tag{
    +yndform-box-sizing;
    +yndform-transition;
        border: 1px solid #b4d2e3;
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        padding: 4px 10px;
        background-color: #f3faff;
        font-size: $theme_font_size;
        line-height: 18px;
        font-weight: bold;
        color: $theme_link_color;
        margin-right: 4px;
        margin-bottom: 4px;
        display: inline-block;
        height: 28px;
        float: left;
    &:hover{
         border-color: $theme_link_color;
         background-color: $theme_link_color;
         color: #fff;
    span.ynicon{
        color: #fff;
    }
    }
    span.ynicon{
    +yndform-transition;
        position: relative;
        margin-left: 4px;
        right: -2px;
        bottom: -1px;
    }
    }
    .message-autosuggest{
        left: 22px;
        position: absolute;
        padding: 0;
        width: 100% !important;
        max-width: 415px;
        list-style: none;
        z-index: 50;
        border: 1px solid $theme_border_dark_color;
        margin: 0;
        list-style: none;
        cursor: pointer;
        white-space: nowrap;
        background: #fff;
        margin-top: 75px;
    li{
        padding: 3px;
        overflow: hidden;
    +yndform-transition;
    img{
        width: 25px;
        height: 25px;
        display: block;
        float: left;
        margin-right: 5px;
    }
    .autocompleter-choice{
        line-height: 25px;
    span{
        font-weight: normal;
    }
    }
    &:hover{
         background-color: $theme_border_color;
     }
    &.autocompleter-selected{
         background-color: $theme_border_color;
     }
    }
    }
    }
    .form-wrapper{
        margin-top: 15px;
    input[type="text"]{
        width: 100%;
        max-width: none;
    }
    textarea{
        width: 100%;
    +yndform-box-sizing;
    }
    }
    #progress_indicator-wrapper{
        margin-bottom: 10px;
    .description{
        margin-bottom: 8px;
    }
    }
    #pre_button_image_heading-label{
        display: none !important;
    }
    }
    .yndform_edit_form{
        margin-top: 15px;
    .yndform_edit_form_menu{
        float: left;
        width: 20%;
    & > li{
          text-align: right;
    a{
        border-top: 1px solid $theme_border_color;
        line-height: 18px;
        padding: 10px 15px;
        display: block;
        font-size: 15px;
        color: $theme_font_color;
        font-weight: normal;
    &:hover{
         background-color: $theme_link_color;
         color: #fff;
     }
    span.ynicon{
        position: relative;
        bottom: -3px;
        opacity: 0;
        margin-left: 8px;
    }
    }
    &.yndform_active{
         background-color: $theme_link_color;
    a{
        color: #fff;
        font-weight: bold;
    span.ynicon{
        opacity: 1
    }
    }
    }
    &:first-of-type{
    a{
        border-top: 0;
    }
    }
    }
    }
    }
    .yndform_action_items{
        border: 1px solid $theme_border_medium_color;
        display: flex;
        display: -moz-flex;
        display: -webkit-flex;
        text-align: center;
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        background-color: #fff;
    .yndform_action_btn{
        flex-grow: 1;
        -moz-flex-grow: 1;
        -webkit-flex-grow: 1;
        border-right: 1px solid $theme_border_color;
    a{
        width: 100%;
        text-transform: capitalize;
        padding: 4px 0;
        font-weight: normal;
        line-height: 18px;
        font-size: $theme_font_size;
        color: $theme_font_color;
    &:hover{
         background-color: $theme_border_color;
     }
    }
    &:last-of-type{
         border-right: 0;
     }
    }
    }
    .global_form{
        float: right;
        width: 80%;
    h3{
        display: none;
    }
    .form-elements{
        border: 1px solid $theme_border_color;
        padding: 15px;
    +yndform-box-sizing;
    & > .form-wrapper{
          display: flex;
          display: -moz-flex;
          display: -webkit-flex;
          margin-bottom: 15px;
    #conditional_enabled-label{
        font-weight: bold;
    }
    & > .form-label{
          display: inline-block;
          max-width: 150px;
          min-width: 150px;
          padding-right: 15px;
      +yndform-box-sizing;
          text-align: left;
    label{
        font-weight: bold;
        font-size: $theme_font_size;
        color: $theme_font_color;
    }
    }
    .form-element{
        width: 100%;
    input[type="text"]{
        width: 100%;
    }
    #entries_max{
        margin-bottom: 10px;
    }
    textarea{
        min-height: 80px;
        width: 100%;
    }
    select{
        width: 50%;
    }
    }
    }
    .yndform_setting_heading{
        margin-bottom: 30px;
        border-bottom: 1px solid $theme_border_color;
    .form-label{
        display: block;
        width: 100%;
        text-align: left;
        min-width: auto;
        max-width: none;
    label{
        color: $theme_font_color;
        font-size: 18px;
        font-weight: bold;
    }
    }
    .form-element{
    .description{
        font-weight: normal;
    }
    }
    }
    #heading_valid_time-wrapper{
        position: relative;
        top: -10px;
    #unlimited_time-element{
        padding: 0px 8px;
        padding-right: 67px;
        background-color: #f7f7f7;
        height: 36px;
        border: 1px solid $theme_border_medium_color;
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
    +yndform-box-sizing;
        line-height: 36px;
    label{
        cursor: pointer;
    }
    }
    }
    .form-element{
    p.description{
        margin-bottom: 5px;
        font-weight: bold;
    }
    ul{
    li{
        float: left;
        margin-bottom: 5px;
        margin-right: 30px;
    &:last-of-type{
         margin-right: 0;
     }
    }
    }
    }
    #valid_from_date-element,
    #valid_to_date-element{
        margin-left: 10px;
        position: relative;
    span.ui-datepicker-trigger{
        font-size: 16px;
        position: absolute;
        right: 1px;
        top: 1px;
        padding: 9px 9px;
        background-color: #eee;
    +yndform-box-sizing;
        border-left: 1px solid #999;
        -webkit-border-top-right-radius: 3px;
        -moz-border-top-right-radius: 3px;
        border-top-right-radius: 3px;
        border-bottom-right-radius: 3px;
        -moz-border-bottom-right-radius: 3px;
        -webkit-border-bottom-right-radius: 3px;
        cursor: pointer;
    }
    }
    #toValues-wrapper{
    .form-label{
        min-width: auto;
    }
    .form-element{
    & > span{
      +yndform-box-sizing;
      +yndform-transition;
          border: 1px solid #b4d2e3;
          border-radius: 3px;
          -moz-border-radius: 3px;
          -webkit-border-radius: 3px;
          padding: 4px 10px;
          background-color: #f3faff;
          font-size: $theme_font_size;
          line-height: 18px;
          font-weight: bold;
          color: $theme_link_color;
          margin-right: 4px;
          display: inline-block;
    span{
        font-weight: bold;
    +yndform-transition;
        color: $theme_link_color;
        position: relative;
        bottom: -1px;
        margin-left: 5px;
        margin-right: -6px;
    }
    &:hover{
         border-color: $theme_link_color_hover;
         background-color: $theme_link_color_hover;
         color: #fff;
    span{
        color: #fff;
    }
    }
    &:last-of-type{
         margin-right: 0;
     }
    }
    }
    }
    #enable_conditional_logic-wrapper{
    .form-options-wrapper{
        margin-bottom: 0;
    }
    }
    #heading_valid_time-element{
    .form-element{
        width: auto;
    }
    }
    #enable-label{
        font-weight: bold;
    }
    }
    }
    .yndform_confirmation_col_right{
        border: 1px solid $theme_border_color;
        padding: 15px;
        min-height: 194px;
    +yndform-box-sizing;
    #toValues-label{
        margin-bottom: 8px;
    }
    .yndform_confirmation_col_right_desc{
        line-height: 18px;
        color: $theme_font_color;
        font-size: $theme_font_size;
        font-weight: normal;
    &.yndform_moderator{
         font-weight: bold;
         margin-bottom: 12px;
     }
    }
    table.yndform_table{
        width: 100%;
        margin-bottom: 15px;
        position: relative;
    a.smoothbox{
        margin: 0;
    }
    th{
        width: 33.33%;
        text-align: center;
    &:first-of-type{
         text-align: left;
     }
    }
    #yndform_options{
    tr{
        position: relative !important;
        cursor: move;
    &.yndform_draging{
         border-color: $theme_link_color;
         background-color: #f3fbfe;
    td{
        color: $theme_link_color;
        font-weight: bold;
        z-index: 1;
    }
    }
    }
    }
    }
    }
    .yndform_moderators_suggest{
        position: relative;
        display: inline-block;
        border: 1px solid #999;
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        padding: 6px;
    .message-autosuggest{
        position: absolute;
        padding: 0;
        width: 300px;
        list-style: none;
        z-index: 50;
        border: 1px solid $theme_border_dark_color;
        margin: 0;
        list-style: none;
        cursor: pointer;
        white-space: nowrap;
        background: #fff;
    li{
        padding: 3px;
        overflow: hidden;
    +yndform-transition;
    img{
        width: 25px;
        height: 25px;
        display: block;
        float: left;
        margin-right: 5px;
    }
    .autocompleter-choice{
        line-height: 25px;
    span{
        font-weight: normal;
    }
    }
    &:hover{
         background-color: $theme_border_color;
     }
    &.autocompleter-selected{
         background-color: $theme_border_color;
     }
    }
    }
    input{
        border: 0;
    &:focus{
         outline: 0;
     }
    }
    }
    }
    #global_page_yndynamicform-admin-form-moderators{
    .form-elements{
    .form-element{
        width: auto;
    }
    #toValues-element{
        margin-bottom: 5px;
    }
    }
    }
    body[id^=global_page_yndynamicform-admin-import-export-]{
    .form-wrapper{
    .form-element{
    ul{
    li{
        float: none;
        clear: both;
        display: block;
        width: 100%;
    input{
        cursor: pointer;
    }
    }
    }
    }
    }
    button#export{
        margin-left: 150px;
    }
    #file_import-element{
    .description{
        margin-bottom: 10px;
    }
    }
    }

    /*----- custom fields -----*/
    .yndform_manage_fields_fields{
        float: left;
        width: 65%;
    +yndform-box-sizing;
        padding-right: 15px;
        position: relative;
    &.yndform_manage_fields_bg{
    div.yndform_manage_fields_fields_bg{
        background-color: #f3faff;
        border: 2px dashed $theme_link_color;
        background-image: none;
    }
    .yndform_manage_fields_desc{
        display: none;
    }
    }
    .item_handle{
        width: 38px !important;
    }
    *{
    +yndform-box-sizing;
    }
    div.yndform_manage_fields_fields_bg{
        background-image: url('~/application/modules/Yndynamicform/externals/images/manga_field_bg.png');
        background-position: center 100px;
        background-repeat: no-repeat;
        height: 100%;
        padding: 15px;
        width: 100%;
        min-height: 1000px;
    }
    div.yndform_manage_fields_desc{
        position: absolute;
        top: 420px;
        padding: 0px 110px;
        box-sizing: border-box;
        text-align: center;
        line-height: 22px;
        font-size: 16px;
        font-weight: normal;
        color: #ccc;
    &:first-line{
         font-weight: bold;
     }
    }
    }
    .yndform_manage_fields_options{
        width: 35%;
        display: inline-block;
        float: right;
    *{
    +yndform-box-sizing;
    }
    .yndform_fields_option_items{
        background-color: #fff;
        border: 1px solid $theme_border_medium_color;
        margin-bottom: 10px;
    &.yndform_show_less{
    & > .yndform_item_name{
          background-color: #eee;
      }
    & > .yndform_item_label_fields,
      .yndform_manage_fields_items,
      .yndform_show{
          display: none !important;
      }
    .yndform_hide{
        display: inline-block !important;
    }
    }
    .yndform_item_name{
        display: flex;
        display: -moz-flex;
        display: -webkit-flex;
        align-items: center;
        -moz-align-items: center;
        -webkit-align-items: center;
        width: 100%;
        height: 40px;
        cursor: pointer;
        padding: 0 15px;
        border-bottom: 1px solid $theme_border_medium_color;
    +yndform-transition;
    &:hover{
         background-color: #eee;
     }
    .yndform_item_name_fields{
        font-weight: bold;
        font-size: 12px;
        color: $theme_font_color;
        text-transform: uppercase;
        float: left;
        display: inline-block;
        width: 100%;
        text-align: left;
    }
    & > span.ynicon{
          font-weight: bold;
          color: $theme_font_color_light;
          font-size: 15px;
          text-align: right;
          display: none;
          float: right;
    &.yndform_show{
         display: inline-block;
     }
    &:before{
         padding: 5px;
         margin-right: -5px;
     }
    }
    }
    }
    .yndform_item_label_fields{
        font-size: 14px;
        text-transform: capitalize;
        color: $theme_font_color;
        height: 40px;
        line-height: 40px;
        padding: 0 15px;
        margin-bottom: -10px;
    }
    ul.yndform_manage_fields_items{
        padding: 0 15px;
        margin: 0 -10px;
        margin-top: 10px;
    .yndform_manage_fields_dragging{
        color: #fff;
        background-color: $theme_link_color;
        height: 40px;
        width: 142px;
        line-height: 40px;
        text-align: center;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        cursor: move;
    }
    li.yndform_manage_fields_item{
        width: 50%;
        float: left;
        cursor: move;
        display: inline-block;
        margin-bottom: 10px;
        padding: 0 10px;
        height: 40px;
    .yndform_draggables{
        height: 40px;
        display: block;
        width: 145px;
        line-height: 40px;
        text-align: center;
        background: #f8f8f8;
        font-size: 12px;
        font-weight: normal;
        color: $theme_font_color;
        overflow: hidden;
        white-space: nowrap;
        word-break: break-word;
        word-wrap: break-word;
        text-overflow: ellipsis;
        padding-left: 10px;
        padding-right: 5px;
    &:before{
         content: "";
         width: 5px;
         height: 40px;
         position: absolute;
         left: 0;
         top: 0;
         background-color: #ddd;
     }
    &.disabled{
         color: #ccc;
         cursor: text;
    &:before{
         display: none;
     }
    }
    }
    }
    }
    }
    .yndform_manage_fields_back{
        margin: 10px 0;
    a{
        color: $theme_font_color;
    +yndform-transition;
    &:hover{
         color: $theme_link_color_hover;
         text-decoration: none;
     }
    span{
        font-size: 11px;
        margin-right: 5px;
    }
    }
    }

    /*----- pick color popup -----*/
    body[id^=global_page_yndynamicform-admin-form-fields-]{
    #next_button_text-label, #pre_button_text-label{
        display: none !important;
    }
    .yndform_color_wrapper{
    .form-label{
        width: auto;
    }
    .form-element{
    .yndform_color_picker{
        left: -30px;
        margin-top: -18px;
    }
    }
    }
    }

    /*----- progress - step - bar -----*/
    div[class^=yndform_progress_indicator_]{
    &, *{
       +yndform-box-sizing;
       }
    }
    .yndform_indicator_progress_bar_parent{
        padding: 20px;
        background-color: #f8f8f8;
        border: 2px dashed $theme_border_medium_color;
        width: 100%;
    ul.yndform_indicator_progress_bar_items{
        display: flex;
        display: -moz-flex;
        display: -webkit-flex;
        border: 1px solid #888;
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        background-color: #fff;
        height: 42px;
    li.yndform_progress_bar_item{
        width: 33.33%;
        position: relative;
        text-align: center;
        padding-top: 10px;
        padding-left: 24px;
        height: 100%;
    .yndform_progree_bar_break{
        display: block;
        position: absolute;
        top: 6px;
        right: -15px;
        background-color: #fff;
        border-top: 1px solid $theme_border_color;
        border-right: 1px solid $theme_border_color;
        width: 28px;
        height: 28px;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }
    .yndform_progree_bar_text_inverse{
        font-weight: bold;
        font-size: 14px;
        background-color: #ddd;
        color: #888;
        border-radius: 100%;
        display: inline-block;
        width: 20px;
        height: 20px;
        line-height: 20px;
        text-align: center;
        margin-right: 8px;
    }
    .yndform_progree_bar_text{
        font-size: $theme_font_size;
        color: #888;
    }
    }
    }
    }
    .yndform_indicator_progress_step_parent{
        padding: 15px 20px 32px 20px;
        background-color: #f8f8f8;
        border: 2px dashed $theme_border_medium_color;
        width: 100%;
    .yndform_indicator_progress_step_items{
        display: flex;
        display: -moz-flex;
        display: -webkit-flex;
    .yndform_progress_step_item{
        width: 33.33%;
        position: relative;
        height: 6px;
        background-color: #ddd;
        display: inline-block;
    .ynform_progress_step_circle{
        display: inline-block;
        width: 17px;
        height: 17px;
        background-color: #ddd;
        position: absolute;
        top: -50%;
        right: 0;
        margin-left: -8.5px;
        margin-top: -3px;
        border-radius: 100%;
        -moz-border-radius: 100%;
        -webkit-border-radius: 100%;
    .ynform_progress_step_circle_child{
        right: -15px;
        bottom: -20px;
        position: absolute;
        white-space: nowrap;
    }
    }
    .ynform_progress_step_bg{
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #ddd;
    }
    &:first-of-type{
    &, .ynform_progress_step_bg{
           border-top-left-radius: 3px;
           -moz-border-top-left-radius: 3px;
           -webkit-border-top-left-radius: 3px;
           border-bottom-left-radius: 3px;
           -moz-border-bottom-left-radius: 3px;
           -webkit-border-bottom-left-radius: 3px;
       }
    }
    &:last-of-type{
    &, .ynform_progress_step_bg{
           border-top-right-radius: 3px;
           -moz-border-top-right-radius: 3px;
           -webkit-border-top-right-radius: 3px;
           border-bottom-right-radius: 3px;
           -moz-border-bottom-right-radius: 3px;
           -webkit-border-bottom-right-radius: 3px;
       }
    }
    }
    }
    }













    =yndform-text-clamp($line){
        word-break: break-word;
        word-wrap: break-word;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        line-height: normal;
        -webkit-line-clamp: $line;
        line-height: 18px;
        height: calc(18*$line)px;
    }

    .ynresponsive1,
    body[class^=ynresponsiveclean-]{
    .layout_page_yndynamicform_entries_view{
    .global_form > div{
        padding: 10px;
    }
    .entry_breadcrum{
        border-bottom: 0;
    }
    #yndform_buttons_group-element{
        width: 100%;
        margin-top: 15px;
        max-width: none;
        margin-left: 0 !important;
        padding-left: 150px;
    }

    #yndform_user_entry-print{
        margin-bottom: 0;
    .yndform_main_content{
        border-bottom: 0;

    }
    }
    }
    }

    body[class*=ynresponsivepurity-]{

    .layout_yndynamicform_categories{
    .generic_list_widget{
    .yncategories_item.level_2{
    a{
        max-width: 128px;
    }
    }
    .yncategories_item.level_3{
    a{
        max-width: 100px;
    }
    }
    }
    }
    &#global_page_yndynamicform-index-list-forms{
    .yn-view-modes-block{
        top: 15px;
        right: 15px;
    }
    }
    }

    body[class=ynresponsive-metro]{
    .layout_middle{
        padding: 8px;
    }
    .entry_breadcrum{
        border-bottom: 0;
    }
    .layout_page_yndynamicform_entries_view{
    .global_form > div{
        padding: 10px;
    }
    #yndform_buttons_group-element{
        width: 100%;
        margin-top: 15px;
        max-width: none;
        margin-left: 0 !important;
        padding-left: 150px;
    }

    #yndform_user_entry-print{
        margin-bottom: 0;
    .yndform_main_content{
        border-bottom: 0;

    }
    }
    }
    }

    .layout_yndynamicform_list_forms{
    ul.yndform_list-view li{
    .yndform_form_list_mode{
    .yndform_info_parent{
    .yndform_form_title_parent{
    .yndform_form_category_parent{
    a{
        vertical-align: middle;
    }
    }
    }
    }
    }
    }
    }

    @media only screen and (max-width: 1024px){

        .layout_page_yndynamicform_entries_manage,
        .layout_page_yndynamicform_entries_list{
        .yndform_my_entries{
        .form-elements,
        #elements-wrapper{
            flex-wrap: wrap;
            -moz-flex-wrap: wrap;
            -webkit-flex-wrap: wrap;
        #keyword-wrapper,
        #search-wrapper,
        #entry_id-wrapper{
            display: block;
            width: 100% !important;
            margin-bottom: 10px;
        }
        #search-wrapper{
            margin-bottom: 0;
            margin-top: 10px;
        }
        #advsearch-wrapper{
            width: auto;
            flex-grow: 1;
            -moz-flex-grow: 1;
            -webkit-flex-grow: 1;
        }
    }
    }
    }
    }

    @media only screen and (max-width: 992px){
        .layout_page_yndynamicform_entries_manage{
        .yndform_my_entries{
        .form-elements{
        #start_date-wrapper,
        #to_date-wrapper,
        #entry_id-wrapper{
            width: 20%;
        }
    }
    }
    }
    }

    @media only screen and (max-width: 768px){

        .layout_middle{
        .layout_yndynamicform_list_related_forms{
        ul{
        li{
            width: 100%;
        }
    }
    }
    }

    .yndform_form_category_entries{
    .yndform_form_category_parent{
        max-width: 65%;
    }
    }

    .layout_page_yndynamicform_entries_list{
    .yndform_title_parent{
        padding-bottom: 5px;
    }
    h1{
        max-width: none;
        margin-bottom: 0;
        padding-bottom: 5px;
    }
    a.yndform_backform{
        float: none;
        display: block;
        width: 100%;
        text-align: right;
    }
    }

    .entry_breadcrum{
    .yndform_title_parent{
        display: block;
        padding-bottom: 10px;
    h1{
        max-width: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    a{
        display: block;
        width: 100%;
        float: none;
        text-align: right;
    }
    }
    }

    #global_page_yndynamicform-entries-edit{
    #yndform_buttons_group-element{
        padding-left: 0;
        padding-right: 0 !important;
        text-align: center;
    #print_button{
        display: none;
    }
    button{
        width: 100%;
        margin-bottom: 5px;
        margin-right: 0;
    }
    a#cancel{
        display: block;
    }
    }
    }

    #yndform_conditional_container{
    #yndform_conditional_list{
    .yndform_conditional_logic_item{
        flex-wrap: wrap;
        -moz-flex-wrap: wrap;
        -webkit-flex-wrap: wrap;
        margin-bottom: 20px;
    .yndform_conditional_logic_buttons{
        margin-left: 0;
        width: 100%;
        text-align: right;
    &.yndform_nominus{
    span{
        width: 100%;
    }
    }
    span{
        width: 50%;
    }
    }
    }
    .yndform_conditional_logic_options,select{
        width: 100% !important;
        max-width: none;
        margin-right: 0;
        margin-bottom: 10px;
    }
    }
    }

    .layout_page_yndynamicform_entries_manage,
    .layout_page_yndynamicform_entries_list{
    .yndform_my_entries{
    .form-elements{
        flex-wrap: wrap;
        -moz-flex-wrap: wrap;
        -webkit-flex-wrap: wrap;
    .form-wrapper{
        display: block;
        margin-bottom: 10px;
        width: 100% !important;
    }
    }
    }
    .yndform_my_entries_table{
    table, thead, tbody, th, td, tr {
        display: block;
    }
    thead tr th{
        display: none;
        text-transform: uppercase;
    }
    td{
        border: none;
        border-bottom: 1px solid $theme_border_dark_color !important;
        position: relative;
        padding-left: 50% !important;
        padding: 10px;
    &.yndform_attached{
         min-height: 39px;
     }
    }
    td:before {
        display: inline-block;
        position: absolute;
        top: 10px;
        left: 6px;
        width: 45%;
        padding-right: 10px;
        white-space: nowrap;
        text-transform: capitalize;
        font-weight: bold;
    }
    }
    }

    .layout_page_yndynamicform_entries_list{
    .yndform_my_entries{
    .form-elements{
    #elements-wrapper{
        flex-wrap: wrap;
        -moz-flex-wrap: wrap;
        -webkit-flex-wrap: wrap;
    }
    #entry_id-wrapper{
        width: 100%;
    }
    #start_date-wrapper,
    #to_date-wrapper{
        width: 33.33%;
    }
    #advsearch-wrapper{
        width: 100%;
    }
    }
    }
    }

    #global_page_yndynamicform-entries-edit{
    .global_form{
    .form-elements{
    .form-wrapper{
        flex-direction: column;
        -moz-flex-direction: column;
        -webkit-flex-direction: column;
    }
    .form-label{
        padding-right: 0 !important;
        margin-bottom: 8px !important;
    }
    }
    }
    #print_button{
        display: none;
    }
    .yndform_page_next_text,
    .yndform_buttons,
    .yndform_page_prev_text,
    .yndform_button_submit{
        width: 100%;
        margin-right: 0 !important;
        margin-bottom: 5px;
    }
    .yndform_button_submit{
        margin-bottom: 5px;
    }
    .yndform_buttons{
        margin-bottom: 5px;
    }
    }

    #global_page_yndynamicform-entries-view{
    #yndform_buttons_group-element{
        margin-left: 0 !important;
        margin-right: 0 !important;
        width: 100%;
        max-width: none;
        padding-left: 10px;
    button{
        magin: 0;
        width: 100%;
    }
    #print_button{
        display: none;
    }
    }
    }

    .layout_page_yndynamicform_form_detail{
    .global_form{
    .form-elements{
    .form-wrapper{
        flex-direction: column;
        -moz-flex-direction: column;
        -webkit-flex-direction: column;
    .form-label{
        padding-right: 0;
        margin-bottom: 5px;
    }
    }
    }
    .global_form{
    .form-elements{
    .form-wrapper{
    .form-element{
        top: 0;
    & > button{
          width: 100%;
      }
    }
    }
    }
    }
    }
    }

    .layout_yndynamicform_list_forms ul.yndform_forms_browse > li{
        width: 100%;
    }

    .ynresponsive1,
    body[class^=ynresponsiveclean-]{
    &#global_page_yndynamicform-entries-edit #yndform_buttons_group-element{
         padding: 15px;
     }
    }

    html[dir="rtl"]{

    #global_page_yndynamicform-entries-view .entry_breadcrum a.yndform_backform,
    #global_page_yndynamicform-entries-edit .entry_breadcrum a.yndform_backform,
    .layout_page_yndynamicform_entries_list a.yndform_backform{
        text-align: left;
        float: none;
    }

    #yndform_conditional_container{
    #yndform_conditional_list{
    select{
        margin-left: 0;
    }
    .yndform_conditional_logic_item{
    .yndform_conditional_logic_buttons{
        margin-right: 0;
    }
    }
    }
    }

    .layout_page_yndynamicform_entries_list{
    table{
    tbody{
    tr{
    td:last-of-type{
        padding-left: 0 !important;
        padding-right: 50%;
    }
    }
    }
    }
    }

    .layout_page_yndynamicform_entries_manage{
    .yndform_my_entries_table{
    td{
        padding-left: 0 !important;
        padding-right: 50% !important;
    &:before{
         left: auto;
         right: 0;
         padding-right: 0;
         padding-left: 10px;
     }
    }
    }
    }

    #global_page_yndynamicform-entries-edit{
    .global_form{
    .form-elements{
    .form-wrapper{
    .form-element{
    .form-options-wrapper{
    li{
        margin-right: 0;
        margin-left: 18px;
    }
    }
    }
    }
    }
    }
    .yndform_page_prev_text,
    .yndform_button_submit{
        float: left;
        margin-right: 0;
        margin-left: 0;
    }
    .yndform_page_prev_text{

    }
    }
    }
    }

    @media only screen and (max-width: 640px){

        .layout_yndynamicform_list_forms{
        ul.yndform_list-view li{
        .yndform_form_list_mode{
            height: auto;
            padding-bottom: 11px;
        .yndform_info_parent{
            flex-direction: column;
            -moz-flex-direction: column;
            -webkit-flex-direction: column;
        .yndform_form_title_parent,
        .yndform_form_content_parent{
            width: 100%;
            height: auto;
        .yndform_form_category_parent{
            display: flex;
            display: -moz-flex;
            display: -webkit-flex;
        a{
            max-width: 80%;
        }
    }
    }
    }
    .yndform_image_parent{
        height: 48px;
    }
    }
    }
    }

    .layout_yndynamicform_list_related_forms{
    ul.yndform_forms_browse{
    & > li{
    .yndform_form_center{
    .yndform_form_category_entries{
    .yn_dots{
        display: none !important;
    }
    }
    }
    }
    }
    }

    .yndform_form_category_entries{
    .yndform_form_category_parent{
        max-width: 100%;
        display: block;
    }
    .yn_dots{
        display: none;
    }
    }

    .layout_yndynamicform_list_related_forms{
    .yndform_form_entries{
        display: block;
    }
    }

    .yndform_form_detail_info{
    .yndform_form_detail_info_parent{
    .yndform_form_detail_info_stast{
        margin-bottom: 10px;
        display: block;
    }
    .yndform_form_detail_info_button{
        clear: both;
        float: none;
        display: block;
        width: 100%;
    }
    }
    }

    .layout_yndynamicform_form_details{
    .global_form{
    .form-elements{
    .form-wrapper{
        flex-direction: column;
        -moz-flex-direction: column;
        -webkit-flex-direction: column;
    .form-label{
        padding: 0;
        margin-bottom: 8px;
        display: block;
        width: 100%;
    }
    }
    }
    }
    }

    html[dir="rtl"]{

    .yndform_form_detail_info{
    .yndform_form_detail_info_parent{
    .yndform_form_detail_info_button{
        float: right;
    }
    }
    }

    .layout_yndynamicform_list_forms{
    ul.yndform_list-view li{
    .yndform_form_list_mode{
    .yndform_info_parent{
    .yndform_form_title_parent{
    .yndform_form_category_parent{
    a{
        vertical-align: bottom;
        padding-right: 0;
        padding-left: 5px;
    }
    }
    }
    }
    }
    }
    }
    }
    }

    @media only screen and (max-width: 480px){

        .layout_yndynamicform_list_forms{
        ul.yndform_forms_browse{
    & > li{
          border-bottom: 1px solid $theme_border_color;
          margin-bottom: 15px;
        .yndform_form_center{
            height: auto;
            padding-top: 10px;
        .yndform_opacity{
            background-color: transparent;
        }
        .yndform_info_parent{
            padding: 0;
    & > div{
        .yndform_form_title{
            margin-bottom: 3px;
        }
    }
    }
    }
    }
    }
    }

    .yndform_form_center{
        text-align: center !important;
        height: auto;
        border: 1px solid;
        border-color: transparent;
        transition: all 0.3s;
        -moz-transition: all 0.3s;
        -webkit-transition: all 0.3s;
        padding: 10px;
    .yndform_info_parent{
        display: block;
        width: 100%;
        float: none;
        clear: both;
        text-align: center;
        padding: 10px;
    .yndform_parent_opacity_border{
        display: none;
    }
    *{
        text-align: center;
    }
    .yndform_form_content_child{
        flex-direction: column;
        -moz-flex-direction: column;
        -webkit-flex-direction: column;
    & > span,div{
          text-align: initial;
      }
    & > span{
          order: 2;
          margin: 5px 0;
      }
    & > div{
          order: 1;
      }
    }
    .yndform_form_description{
        -webkit-line-clamp: 4;
        height: auto;
        max-height: 72px;
    }
    .yndform_form_title{
        margin-bottom: 5px;
    }
    .yndform_parent_opacity{
        display: none;
    }
    }
    .yndform_opacity{
        background-color: $theme_link_color;
    }
    .yndform_form_title{
    a{
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        line-height: 20px;
        height: 36px;
        white-space: normal;
        height: auto;
        max-height: 40px;
    }
    }
    .yndform_image_parent{
        float: none;
        height: 216px;
        width: 150px;
        padding: 0;
    .yndform_parent_opacity{
        background-color: transparent;
    }
    }
    &:hover{
         border: 1px solid;
         border-color: $theme_link_color;
     }
    }

    .layout_page_yndynamicform_entries_list{
    .yndform_my_entries{
    .form-elements{
    #to_date-wrapper,
    #start_date-wrapper{
        width: 100%;
    }
    }
    }
    }

    .layout_page_yndynamicform_entries_manage,
    .layout_page_yndynamicform_entries_list{
    .yndform_my_entries_table{
    td{
        padding-left: 8px !important;
        padding-top: 30px;
    &:last-of-type{
         padding-top: 10px;
         border-bottom: 0 !important;
     }
    }
    }
    }

    .layout_page_yndynamicform_entries_manage{
    .yndform_my_entries{
    .form-elements{
    .form-wrapper{
        width: 100% !important;
        order: initial !important;
    }
    }
    }
    }

    #yndform_user_entry-print,
    #yndform_entry-print{
    .yndform_main_content{
    .form-wrapper{
        flex-direction: column;
        -moz-flex-direction: column;
        -webkit-flex-direction: column;
    }
    }
    .yndform_rating{
        margin-top: 5px;
    }
    }

    .yndform_form_detail_info{
    .yndform_form_category_entries{
    .yn_dots{
        display: none;
    }
    .yndform_form_detail_info_creation_date{
        float: none;
        clear: both;
        display: block;
        width: 100%;
    }
    .yndform_form_category_parent{
        display: block;
        width: 100%;
        margin-bottom: 5px;
    }
    }
    }

    .yndform_form_detail_info{
    .yndform_form_detail_info_parent{
    .yndform_form_detail_info_stast{
        margin-bottom: 10px;
        display: block;
        width: 100%;
    }
    .yndform_form_detail_info_button{
    a{
        width: 100%;
        display: block;
    &.yndform_share_button{
         margin: 5px 0;
     }
    }
    }
    }
    }

    .layout_yndynamicform_single_form{
    .yndform_form_center{
    .yndform_form_category_entries{
    .yndform_form_entries{
        display: block;
        width: 100%;
    }
    }
    }
    }

    .layout_yndynamicform_list_forms ul.yndform_forms_browse > li{
    .yndform_form_center .yndform_form_title{
    .yndform_form_entries{
        display: block;
        width: 100%;
        max-width: none;
        text-align: left;
        margin-bottom: 10px;
    }
    a{
        display: block;
        width: 100%;
        max-width: none;
    }
    }
    }

    html[dir="rtl"]{

    .yndform_form_center{
    .yndform_image_parent{
        float: none;
    }
    }

    .layout_yndynamicform_list_forms{
    ul.yndform_forms_browse{
    & > li{
    .yndform_form_center{
    .yndform_form_title{
    .yndform_form_entries{
        text-align: right;
    }
    }
    }
    }
    }
    }

    .layout_page_yndynamicform_entries_manage,
    .layout_page_yndynamicform_entries_list{
    .yndform_my_entries_table{
    td{
        padding-right: 0 !important;
    &:before{
         left: auto;
         right: 0px;
         padding-right: 0;
         padding-left: 10px;
     }
    }
    }
    }

    .layout_page_yndynamicform_entries_list{
    .yndform_my_entries_table{
    td{
        padding-left: 0 !important;
        padding-right: 10px;
    }
    }
    }


    }
    }

    @media only screen and (max-width: 768px){
        body[class*=insignia]{
        .layout_yndynamicform_list_forms ul.yndform_list-view li .yndform_form_list_mode{
            padding-left: 0;
            padding-right: 0;
        }
        .yndform_form_detail_info .yndform_form_detail_info_parent .yndform_form_detail_info_button{
            display: block;
            float: none;
            clear: both;
            padding-top: 10px;
        }
        .yndform_form_center .yndform_form_entries{
            padding-right: 8px;
            padding-bottom: 5px;
        }
        .layout_yndynamicform_list_forms ul.yndform_list-view li .yndform_form_list_mode .yndform_info_parent{
            height: 135%;
        }
    }

    html[dir='rtl']{
    body[class*=insignia]{
    .yndform_form_center .yndform_form_entries{
        padding-right: 0;
        padding-left: 8px;
    }
    .layout_yndynamicform_list_forms ul.yndform_list-view li .yndform_form_list_mode .yndform_info_parent .yndform_form_title_parent .yndform_form_category_parent a{
        vertical-align: bottom;
    }
    }
    }
    }

    html[dir="rtl"]{

    body[class*=ynresponsivepurity-]{
    &#global_page_yndynamicform-index-list-forms .yn-view-modes-block{
         top: 15px;
         right: auto;
         left: 15px;
     }
    }

    ul.paginationControl > li + li > a{
        border-left-width: 1px;
    }

    body[id^=global_page_yndynamicform-][class*=ynresponsivepurity-]{
    .paginationControl{
        float: left;
    & > li{
          float: right;
      }
    }
    }
    .ynresponsive1,
    body[class^=ynresponsiveclean-]{
    .layout_page_yndynamicform_entries_view{
    #yndform_buttons_group-element{
        margin-right: 0 !important;
        padding-right: 150px;
    }
    }
    }
    }
    ul.admin_fields .field {
        background-color: #f5f5f5;
        border: 1px solid #ddd;
    }
    span.field_extraoptions_explain {
        display: none;
    }
    label.overTxtLabel {
        display: none;
    }
    a {
        color: #0087c3 !important;
    }
    ul.admin_fields .item_handle {
        cursor: move;
        display: inline-block;
        overflow: hidden;
        font-weight: bold;
        -moz-user-select: none;
        -webkit-user-select: none;
        float: left;
        width: 24px;

        padding: 7px;
    }

    ul.admin_fields .field, ul.admin_fields .heading {
        display: inline-block;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        border-radius: 3px;
        overflow: hidden;
        width: 50% ;
        padding: 7px 10px 7px 7px;
        background-repeat: no-repeat;
        background-position: 8px 8px;
    }
    .item_options {
        float: right;
        overflow: hidden;
        text-align: right;
        margin-left: 10px;
        color: #aaa;
    }



</style>
