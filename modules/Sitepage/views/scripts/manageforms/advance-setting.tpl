<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    manage.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
	$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jscolor/jscolor.js');
$id = $this->form_id;
?>


<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">
        <?php // include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
            <?php echo $this->
            partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array(
            'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Edit', 'sectionDescription' => '')); ?>
            <div class="sitepage_edit_content">

                <div class="sesbasic_search_reasult">
                    <?php echo $this->htmlLink(array( 'module' => 'sitepage', 'controller' => 'manageforms', 'action' => 'manage'), $this->translate("Back to Manage Forms"), array('class'=>'sesbasic_icon_back buttonlink')) ?>
                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'categories', 'action' => 'index', 'id' => $id), $this->translate("Manage Categories"), array('class'=>'sesbasic_icon_add buttonlink')) ?>
                </div>
                <div class='clear sesbasic_admin_form'>
                    <div class='settings'>
                        <?php echo $this->form->render($this); ?>
                    </div>
                </div>



            </div>

        </div>
    </div>
</div>




<script type="application/javascript">
    function showDescriptionSetting(value){
        if (value==0)
        {
            if(document.getElementById('description_required-wrapper'))
                document.getElementById('description_required-wrapper').style.display = 'none';
        }else{
            if(document.getElementById('description_required-wrapper'))
                document.getElementById('description_required-wrapper').style.display = 'block';
        }
    }
    function showFileUpload(value)
    {
        if (value==0)
        {
            if(document.getElementById('file_upload-wrapper'))
                document.getElementById('file_upload-wrapper').style.display = 'none';
            if(document.getElementById('display_file_upload_required-wrapper'))
                document.getElementById('display_file_upload_required-wrapper').style.display = 'none';
            if(document.getElementById('label_file_upload-wrapper'))
                document.getElementById('label_file_upload-wrapper').style.display = 'none';
        }
        else
        {
            if(document.getElementById('file_upload-wrapper'))
                document.getElementById('file_upload-wrapper').style.display = 'block';
            if(document.getElementById('display_file_upload_required-wrapper'))
                document.getElementById('display_file_upload_required-wrapper').style.display = 'block';
            if(document.getElementById('label_file_upload-wrapper'))
                document.getElementById('label_file_upload-wrapper').style.display = 'block';
        }
    }
    window.addEvent('domready', function() {
        showDescriptionSetting('<?php  echo $this->formObj->description; ?>');
        showFileUpload(document.getElementById('display_file_upload').value);
    });
</script>
<style type="text/css">
    #sesmultipleformheading-wrapper{border-bottom-width:1px;padding-bottom:5px;}
    #sesmultipleformheading-label{font-size:17px;}
</style>