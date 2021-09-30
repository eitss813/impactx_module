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
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>
<?php
$form_id = $this->id;
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js'); ?>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/odering.js'); ?>
<style>
    .error{
        color:#FF0000;
    }
</style>
<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">
        <?php // include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
            <?php echo $this->
            partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array(
            'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Manage Api', 'sectionDescription' => '')); ?>
            <div class="sitepage_edit_content">
                    <div class="sesbasic_search_reasult">
                        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'forms', 'action' => 'index'), $this->translate("Back to Manage Forms"), array('class'=>'sesbasic_icon_back buttonlink')) ?>
                    </div>
                    <div class='sesbasic-form sesbasic-categories-form'>
                        <div>
                            <div class="sesbasic-form-cont" style="padding-top:15px;">
                                <h3><?php echo $this->translate("Manage Categories") ?> </h3>
                                <p class="description"><?php echo $this->translate('Forms categories can be managed here. To create new categories in any form, first select the form, then use "Add New Category" form below. Below, you can also choose Title, Profile Type to be associated with the category. You can also map Categories with the Profile Types, so that questions belonging to the mapped Profile Type will appear when users choose the associated Category while filling up the forms.<br /></br>To create 2nd-level categories and 3rd-level categories, choose respective 1st-level and 2nd-level category from "Parent Category" dropdown below. Choose this carefully as you will not be able to edit Parent Category later.<br /></br>To reorder the categories, click on their names or row and drag them up or down.<br /></br>Note: If you do not want users to choose category, but you want additional custom fields to be shown in the form, then create only 1 category in the form and map the desired Profile Type. Single category will not be shown in the form.'); ?></p>
                                <?php if(count($this->getForms)){ ?>
                                <div class="admin_fields_type">
                                    <h3><?php echo $this->translate("Select Form:") ?></h3>
                                    <select id="form_id_bn" onchange="refreshParentWithFormId(this.value);">
                                        <?php foreach($this->getForms as $val){ ?>
                                        <option value="<?php echo $val['form_id']; ?>" <?php if($this->id == $val['form_id']){ ?> selected <?php } ?>><?php echo $val['title']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <br />
                                <?php } ?>
                                <?php if(count($this->getForms)){ ?>
                                <div class="sesbasic-categories-add-form">
                                    <h4 class="bold">Add New Category</h4>
                                    <form id="addcategory" method="post" enctype="multipart/form-data">
                                        <div class="sesbasic-form-field" id="tag-title">
                                            <div class="sesbasic-form-field-label">
                                                <label for="title-name">Title</label>
                                            </div>
                                            <div class="sesbasic-form-field-element">
                                                <input name="title" id="title-name" type="text" size="40">
                                            </div>
                                        </div>

                                        <div class="sesbasic-form-field">
                                            <div class="sesbasic-form-field-label">
                                                <label for="parent">Parent Category</label>
                                            </div>
                                            <div class="sesbasic-form-field-element">
                                                <select name="parent" id="parent" class="postform">
                                                    <option value="-1">None</option>
                                                    <?php foreach ($this->categories as $category): ?>
                                                    <?php if($category->category_id == 0) : ?>
                                                    <?php continue; ?>
                                                    <?php endif; ?>
                                                    <option class="level-0" value="<?php echo $category->category_id; ?>"><?php echo $category->title; ?></option>
                                                    <?php
                                      $subcategory = Engine_Api::_()->getDbtable('categories', 'sesmultipleform')->getModuleSubcategory(array('column_name' => "*", 'category_id' => $category->category_id));
                                                    foreach ($subcategory as $sub_category):
                                                    ?>
                                                    <option class="level-1" value="<?php echo $sub_category->category_id; ?>">&nbsp;&nbsp;&nbsp;<?php echo $sub_category->title; ?></option>
                                                    <?php
                                      endforeach;
                                      endforeach;
                                  ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="sesbasic-form-field">
                                            <div class="sesbasic-form-field-label">
                                                <label for="parent">Map Profile Type</label>
                                            </div>
                                            <div class="sesbasic-form-field-element">
                                                <select name="profile_type" id="profile_type" class="postform">
                                                    <?php foreach ($this->profiletypes as $key=>$profiletype): ?>
                                                    <option  value="<?php echo $key ; ?>"><?php echo $profiletype; ?></option>
                                                    <?php
                                      endforeach;
                                  ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="submit sesbasic-form-field">
                                            <button type="button" id="submitaddcategory" class="upload_image_button button">Add New Category</button>
                                        </div>
                                    </form>
                                    <div class="sesbasic-categories-add-form-overlay" id="add-category-overlay" style="display:none"></div>
                                </div>
                                <div class="sesbasic-categories-listing">
                                    <div id="error-message-category-delete"></div>
                                    <form id="multimodify_form" method="post" onsubmit="return multiModify();">
                                        <table class='admin_table' style="width: 100%;">
                                            <thead>
                                            <tr>
                                                <th><input type="checkbox" onclick="selectAll()"  name="checkbox" /></th>
                                                <th><?php echo $this->translate("Title") ?></th>
                                                <th><?php echo $this->translate("Options") ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php //Category Work ?>
                                            <?php foreach ($this->categories as $category): ?>
                                            <?php if($category->category_id == 0) : ?>
                                            <?php continue; ?>
                                            <?php endif; ?>
                                            <tr id="categoryid-<?php echo $category->category_id; ?>" data-article-id="<?php echo $category->category_id; ?>">
                                                <td><input type="checkbox" class="checkbox check-column" name="delete_tag[]" value="<?php echo $category->category_id; ?>" /></td>
                                                <td><?php echo $category->title ?>
                                                    <div class="hidden" style="display:none" id="inline_<?php echo $category->category_id; ?>">
                                                        <div class="parent">0</div>
                                                    </div>
                                                </td>
                                                <td><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'categories', 'action' => 'edit-category', 'id' => $category->category_id,'form_id'=>$form_id), $this->translate('Edit'), array()) ?> | <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Delete'), array('class' => 'deleteCat','data-url'=>$category->category_id)); ?>
                                                </td>
                                            </tr>
                                            <?php //Subcategory Work
                                        $subcategory = Engine_Api::_()->getDbtable('categories', 'sesmultipleform')->getModuleSubcategory(array('column_name' => "*", 'category_id' => $category->category_id,'id'=>$form_id));              foreach ($subcategory as $sub_category):  ?>
                                            <tr id="categoryid-<?php echo $sub_category->category_id; ?>" data-article-id="<?php echo $sub_category->category_id; ?>">
                                                <td><input type="checkbox"  class="checkbox check-column" name="delete_tag[]" value="<?php echo $sub_category->category_id; ?>" /></td>
                                                <td>-&nbsp;<?php echo $sub_category->title ?>
                                                    <div class="hidden" style="display:none" id="inline_<?php echo $sub_category->category_id; ?>">
                                                        <div class="parent"><?php echo $sub_category->subcat_id; ?></div>
                                                    </div>
                                                </td>
                                                <td><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'categories', 'action' => 'edit-category', 'id' => $sub_category->category_id,'form_id'=>$form_id), $this->translate('Edit'), array()) ?> | <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Delete'), array('class' => 'deleteCat','data-url'=>$sub_category->category_id)) ?> 		</td>
                                            </tr>
                                            <?php //SubSubcategory Work
                                        $subsubcategory = Engine_Api::_()->getDbtable('categories', 'sesmultipleform')->getModuleSubsubcategory(array('column_name' => "*", 'category_id' => $sub_category->category_id,'id'=>$form_id));
                                            foreach ($subsubcategory as $subsub_category): ?>
                                            <tr id="categoryid-<?php echo $subsub_category->category_id; ?>" data-article-id="<?php echo $subsub_category->category_id; ?>">
                                                <td><input type="checkbox" class="checkbox check-column" name="delete_tag[]" value="<?php echo $subsub_category->category_id; ?>" /></td>
                                                <td>--&nbsp;<?php echo $subsub_category->title ?>
                                                    <div class="hidden" style="display:none" id="inline_<?php echo $sub_category->category_id; ?>">
                                                        <div class="parent"><?php echo $subsub_category->subsubcat_id; ?></div>
                                                    </div>
                                                </td>
                                                <td><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'categories', 'action' => 'edit-category', 'id' => $subsub_category->category_id,'form_id'=>$form_id), $this->translate('Edit'), array()) ?> | <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Delete'), array('class' => 'deleteCat','data-url'=>$subsub_category->category_id)) ?>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php endforeach; ?>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                        <span class='buttons'>
                               <button type="button" id="deletecategoryselected" class="upload_image_button button"><?php echo $this->translate("Delete Selected") ?></button>
                              </span>
                                    </form>
                                </div>
                                <?php }else{ ?>
                                <div class="tip">
                        <span>
                          <?php echo $this->translate('No form created yet or no active form found.');?>
                            <?php echo $this->translate('%1$sCreate%2$s one!', '<a class="smoothbox" href="'.$this->url(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'forms', 'action' => 'create-form','category'=>true), 'admin_default').'">', '</a>'); ?>
                        </span>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>






<script type="application/javascript">

    function refreshParentWithFormId(value){
        window.location.href = en4.core.baseUrl+"manageforms/categories/index/id/"+value;
        return;
    }
    ajaxurl = en4.core.baseUrl+"admin/sesmultipleform/categories/change-order";

    jqueryObjectOfSes (document).ready(function (e) {
        jqueryObjectOfSes ('#addcategory').on('submit',(function(e) {
            var error = false;
            var nameFieldRequired = jqueryObjectOfSes('#title-name').val();
            if(!nameFieldRequired){
                jqueryObjectOfSes('#title-name').css('background-color','#ffebe8');
                jqueryObjectOfSes('#tag-title').css('border','1px solid red');
                error = true;
            }else{
                jqueryObjectOfSes('#title-required').css('background-color','');
                jqueryObjectOfSes('#tag-title').css('border','');
            }

            if(error){
                jqueryObjectOfSes('html, body').animate({
                        scrollTop: jqueryObjectOfSes('#addcategory').position().top },
                    1000
                );
                return false;
            }
            jqueryObjectOfSes('#add-category-overlay').css('display','block');
            e.preventDefault();
            var form = jqueryObjectOfSes('#addcategory');
            var formData = new FormData(this);
            formData.append('is_ajax', 1);
            formData.append('form_id', <?php echo $form_id; ?>);
            jqueryObjectOfSes .ajax({
                type:'POST',
                url: jqueryObjectOfSes(this).attr('action'),
                data:formData,
                cache:false,
                contentType: false,
                processData: false,
                success:function(data){
                    jqueryObjectOfSes('#cover_photo_preview-wrapper').css('display','none');
                    jqueryObjectOfSes('#thumbnail_photo_preview-wrapper').css('display','none');
                    jqueryObjectOfSes('#add-category-overlay').css('display','none');
                    data = jqueryObjectOfSes.parseJSON(data);
                    parent = jqueryObjectOfSes('#parent').val();
                    if ( parent > 0 && jqueryObjectOfSes('#categoryid-' + parent ).length > 0 ){ // If the parent exists on this page, insert it below. Else insert it at the top of the list.
                        var scrollUpTo= '#categoryid-' + parent;
                        jqueryObjectOfSes( '.admin_table #categoryid-' + parent ).after( data.tableData ); // As the parent exists, Insert the version with - - - prefixed
                    }else{
                        var scrollUpTo = '#multimodify_form';
                        jqueryObjectOfSes( '.admin_table' ).prepend( data.tableData ); // As the parent is not visible, Insert the version with Parent - Child - ThisTerm
                    }
                    if ( jqueryObjectOfSes('#parent') ) {
                        // Create an indent for the Parent field
                        indent = data.seprator;
                        if(indent != 3)
                            form.find( 'select#parent option:selected' ).after( '<option value="' + data.id + '">' + indent + data.name + '</option>' );
                    }
                    jqueryObjectOfSes('html, body').animate({
                            scrollTop: jqueryObjectOfSes(scrollUpTo).position().top },
                        1000
                    );
                    jqueryObjectOfSes('#addcategory')[0].reset();
                },
                error: function(data){
                    //silence
                }
            });
        }));
        jqueryObjectOfSes("#submitaddcategory").on("click", function() {
            jqueryObjectOfSes("#addcategory").submit();
        });
    });
    function selectAll()
    {
        var i;
        var multimodify_form = $('multimodify_form');
        var inputs = multimodify_form.elements;
        for (i = 1; i < inputs.length - 1; i++) {
            if (!inputs[i].disabled) {
                inputs[i].checked = inputs[0].checked;
            }
        }
    }
    jqueryObjectOfSes("#deletecategoryselected").click(function(){
        var n = jqueryObjectOfSes(".checkbox:checked").length;
        if(n>0){
            var confirmDelete = confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected categories?")) ?>');
            if(confirmDelete){
                var selectedCategory = new Array();
                if (n > 0){
                    jqueryObjectOfSes(".checkbox:checked").each(function(){
                        jqueryObjectOfSes('#categoryid-'+jqueryObjectOfSes(this).val()).css('background-color','#ffebe8');
                        selectedCategory.push(jqueryObjectOfSes(this).val());
                    });
                    var scrollToError = false;
                    jqueryObjectOfSes.post(window.location.href,{data:selectedCategory,selectDeleted:'true'},function(response){
                        response = jqueryObjectOfSes.parseJSON(response);
                        var ids = response.ids;
                        if(response.diff_ids.length>0){
                            jqueryObjectOfSes('#error-message-category-delete').html("Red mark category can't delete.You need to delete lower category of that category first.<br></br>");
                            jqueryObjectOfSes('#error-message-category-delete').css('color','red');
                            scrollToError = true;
                        }else{
                            jqueryObjectOfSes('#error-message-category-delete').html("");
                            jqueryObjectOfSes('#error-message-category-delete').css('color','');
                        }
                        jqueryObjectOfSes('#multimodify_form')[0].reset();
                        if(response.ids){
                            //error-message-category-delete;
                            for(var i =0;i<=ids.length;i++){
                                jqueryObjectOfSes('select#parent option[value="' + ids[i] + '"]').remove();
                                jqueryObjectOfSes('#categoryid-'+ids[i]).fadeOut("normal", function() {
                                    jqueryObjectOfSes(this).remove();
                                });
                            }
                        }
                        if(scrollToError){
                            jqueryObjectOfSes('html, body').animate({
                                    scrollTop: jqueryObjectOfSes('#addcategory').position().top },
                                1000
                            );
                        }
                    });
                    return false;
                }
            }
        }
    });
    jqueryObjectOfSes(document).on('click','.deleteCat',function(){
        var id = jqueryObjectOfSes(this).attr('data-url');
        var confirmDelete = confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected category?")) ?>');
        if(confirmDelete){
            jqueryObjectOfSes('#categoryid-'+id).css('background-color','#ffebe8');
            var selectedCategory=[id];
            var scrollToError = false;
            jqueryObjectOfSes.post(window.location.href,{data:selectedCategory,selectDeleted:'true'},function(response){
                response = jqueryObjectOfSes.parseJSON(response);
                console.log(response);
                if(response.ids){
                    var ids = response.ids;
                    if(response.diff_ids.length>0){
                        jqueryObjectOfSes('#error-message-category-delete').html("Red mark category can't delete.You need to delete lower category of that category first.<br></br>");
                        jqueryObjectOfSes('#error-message-category-delete').css('color','red');
                    }else{
                        jqueryObjectOfSes('#error-message-category-delete').html("");
                        jqueryObjectOfSes('#error-message-category-delete').css('color','');
                    }
                    for(var i =0;i<=ids.length;i++){
                        jqueryObjectOfSes('select#parent option[value="' + ids[i] + '"]').remove();
                        jqueryObjectOfSes('#categoryid-'+ids[i]).fadeOut("normal", function() {
                            jqueryObjectOfSes(this).remove();
                        });
                    }
                    if(scrollToError){
                        jqueryObjectOfSes('html, body').animate({
                                scrollTop: jqueryObjectOfSes('#addcategory').position().top },
                            1000
                        );
                    }
                }
            });
        }
    });
</script>
<style>

    /*Fonts*/
    @font-face {
        font-family:'Font Awesome 5 Free';
        src:url('~/application/modules/Sesbasic/externals/styles/fonts/fontawesome-webfont.eot');
        src:url('~/application/modules/Sesbasic/externals/styles/fonts/fontawesome-webfont.eot') format('embedded-opentype'),
        url('~/application/modules/Sesbasic/externals/styles/fonts/fontawesome-webfont.woff2') format('woff2'),
        url('~/application/modules/Sesbasic/externals/styles/fonts/fontawesome-webfont.woff') format('woff'),
        url('~/application/modules/Sesbasic/externals/styles/fonts/fontawesome-webfont.ttf') format('truetype'),
        url('~/application/modules/Sesbasic/externals/styles/fonts/fontawesome-webfont.svg') format('svg');
        font-weight:normal;
        font-style:normal;
    }
    .fa:before, .fa:after{
        font-family:'Font Awesome 5 Free';
        font-style:normal;
        font-weight:normal;
        line-height:1;
        -webkit-font-smoothing:antialiased;
        -moz-osx-font-smoothing:grayscale;
    }
    .fa-star:before {
        content:"\\f005";
    }
    .fa-star-o:before {
        content:"\\f006";
    }
    .fa-star-half:before {
        content:"\\f089";
    }
    .fa-plus:before {
        content:"\\f067";
    }
    .fa-edit:before {
        content:"\\f040";
    }
    .fa-trash:before {
        content:"\\f1f8";
    }
    .fa-long-arrow-left:before {
        content:"\\f177";
    }
    .fa-facebook:before{
        content:"\\f09a";
    }
    .fa-twitter:before{
        content:"\\f099";
    }
    .fa-youtube:before{
        content:"\\f167";
    }
    .fa-skype:before{
        content:"\\f17e";
    }
    .fa-whatsapp:before{
        content:"\\f232";
    }
    .fa-call:before{
        content:"\\f098";
    }
    .fa-notice:before{
        content:"\\f071";
    }
    /*Global CSS*/
    .bold {
        font-weight:bold;
    }
    .clear {
        clear:both;
    }
    .sesbasic_icon_add{
        background-image:url(~/application/modules/Sesbasic/externals/images/icons/add.png);
    }
    .sesbasic_icon_edit{
        background-image:url(~/application/modules/Sesbasic/externals/images/icons/edit.png);
    }
    .sesbasic_icon_back{
        background-image:url(~/application/modules/Sesbasic/externals/images/icons/back.png);
    }
    .sesbasic_icon_delete{
        background-image:url(~/application/modules/Sesbasic/externals/images/icons/delete.png);
    }
    .sesbasic_icon_sink{
        background-image:url(~/application/modules/Sesbasic/externals/images/icons/link-globe.png);
    }
    .sesbasic_bxs, .sesbasic_bxs *{
        -webkit-box-sizing:border-box;
        -moz-box-sizing:border-box;
        box-sizing:border-box;
    }
    .sesbasic_clearfix:after{
        content:"";
        clear:both;
        display:block;
    }
    /*Important Message Tip*/
    .sesbasic_info_tip{
        box-shadow:0 4px 5px 0 rgba(0, 0, 0, .14);
        margin-bottom:20px;
        background-color:#00cae3;
        color:#fff;
        border-radius:3px;
        position:relative;
        padding:15px 20px;
        display:flex;
        align-items:center;
    }
    .sesbasic_info_tip i{
        font-size:22px;
        margin-right:10px;
    }
    .sesbasic_info_tip span{
        font-size:15px;
    }
    .sesbasic_info_tip span a{
        color:#fff;
        text-decoration:underline;
        font-weight:bold;
    }
    /*Calendar Button*/
    div.event_calendar_container button.event_calendar{
        padding:0;
    }
    /*Contact SocialEngineSolution Form*/
    .sesbasic_site_view {
        border:5px solid #f5f5f5;
        margin-top:15px;
    }
    /*Tabs*/
    .sesbasic-admin-navgation{
        display: block;
        margin: 10px 0 0;
        border-top: 1px solid #ddd;
        overflow: hidden;
        padding: 10px 0;
    }
    .sesbasic-admin-navgation *{
        -webkit-box-sizing:border-box;
        -moz-box-sizing:border-box;
        box-sizing:border-box;
    }
    .sesbasic-admin-navgation ul{
        margin:0 -5px;
    }
    .sesbasic-admin-navgation ul li{
        float:left;
        margin:5px;
    }
    .sesbasic-admin-navgation ul li a{
        color: #0dc7f1;
        display: block;
        font-weight: bold;
        padding: 10px 20px;
        border-radius: 20px;
        border: 1px solid #0dc7f1;
    }
    .sesbasic-admin-navgation ul li a:hover,
    .sesbasic-admin-navgation ul li.active a{
        background-color:#0dc7f1;
        text-decoration:none;
        color:#fff;
    }
    /*Sub Tabs*/
    .sesbasic-admin-sub-tabs{
        background-color:#fcfcfc;
        border-bottom:1px solid #e9e9e9;
        margin-bottom:15px;
        padding:10px 15px 0;
    }
    .sesbasic-admin-sub-tabs:after{
        clear:both;
        content:'';
        display:block;
    }
    .sesbasic-admin-sub-tabs li{
        float:left;
        margin-right:5px;
    }
    .sesbasic-admin-sub-tabs a{
        border:1px solid #e9e9e9;
        border-bottom-width:0;
        border-radius:3px 3px 0 0;
        box-shadow:0px 1px 3px #fff inset;
        color:#555;
        display:block;
        font-weight:bold;
        margin:0 5px 0 0;
        outline:none;
        padding:8px 10px;
        text-shadow:1px 1px 1px #fff;
        background:#f5f5f5;
        background:-moz-linear-gradient(top, #fafafa 0%, #f5f5f5 100%);
        background:-webkit-gradient(linear, left top, left bottom, color-stop(0%, #fafafa), color-stop(100%, #f5f5f5));
        background:-webkit-linear-gradient(top, #fafafa 0%, #f5f5f5 100%);
        background:-webkit-gradient(linear, left top, left bottom, from(#fafafa), to(#f5f5f5));
        background:linear-gradient(to bottom, #fafafa 0%,#f5f5f5 100%);
    }
    .sesbasic-admin-sub-tabs a:hover{
        text-decoration:none;
        background:#fafafa;
    }
    .sesbasic-admin-sub-tabs li.active a{
        background:#fff;
        margin-bottom:-1px;
        padding-top:9px;
        position:relative;
    }
    .sesbasic-admin-sub-inner-tabs{
        background-color:transparent;
        margin:0 15px 20px;
        padding:0;
    }
    /*Faq*/
    .sesbasic_faqs {
        background-color:#f5f5f5;
        overflow:hidden;
        padding:10px;
    }
    .sesbasic_faqs > ul {
        background:#fff;
        border:1px solid #ccc;
        overflow:hidden;
        padding:20px;
    }
    .sesbasic_faqs > ul li {
        clear:both;
    }
    .sesbasic_faqs > ul .faq_ques{
        font-size:17px;
        font-weight:bold;
        margin-bottom:10px;
    }
    .sesbasic_faqs > ul li p{
        margin-bottom:10px;
    }
    .sesbasic_faqs > ul li .codebox{
        margin-bottom:11px;
        padding:10px;
        display:block;
        border-width:1px;
        background-color:#f1f1f1;
        line-height:20px;
    }
    /*Autosuggest*/
    .sesbasic-autosuggest {
        position:absolute;
        padding:0px;
        width:300px;
        list-style:none;
        z-index:50;
        border:1px solid #d0d1d5;
        margin:0px;
        list-style:none;
        cursor:pointer;
        white-space:nowrap;
        background:#fff;
    }
    .sesbasic-autosuggest > li {
        padding:3px;
        margin:0 !important;
        overflow:hidden;
    }
    .sesbasic-autosuggest > li + li {
        border-top:1px solid #d0d1d5;
    }
    .sesbasic-autosuggest > li img {
        max-width:25px;
        max-height:25px;
        display:block;
        float:left;
        margin-right:5px;
    }
    .sesbasic-autosuggest > li.autocompleter-selected {
        background:#eee;
        color:#555;
    }
    .sesbasic-autosuggest > li.autocompleter-choices {
        font-size:.8em;
    }
    .sesbasic-autosuggest > li.autocompleter-choices .autocompleter-choice {
        line-height:25px;
    }
    .sesbasic-autosuggest > li:hover {
        background:#eee;
        color:#555;
    }
    .sesbasic-autosuggest > li span.autocompleter-queried {
        font-weight:bold;
    }
    ul.sesbasic-autosuggest .search-working {
        background-image:none;
    }
    .autocompleter-choice {
        cursor:pointer;
    }
    .autocompleter-choice:hover {
        color:#5ba1cd;
    }
    /*Search Form*/
    div.sesbasic_search_form form{
        padding:10px;
    }
    div.sesbasic_search_form form > div {
        display:inline-block;
        float:none;
        margin:5px 10px 5px 0;
    }
    div.sesbasic_search_form form > div label{
        font-weight:normal;
    }
    div.sesbasic_search_form form > div input[type="text"],
    div.sesbasic_search_form form > div select{
        min-width:100px;
        padding:5px;
        max-width:200px;
    }
    .sesbasic_search_result,
    .sesbasic_search_reasult{
        font-weight:bold;
        margin-bottom:10px;
    }
    /*Global Form*/
    div .sesbasic_admin_form form > div{
        padding:15px;
    }
    div .sesbasic_admin_form .form-description {
        max-width:inherit;
    }
    div .sesbasic_admin_form .form-element .description,
    div .sesbasic_admin_form .form-element .hint{
        max-width:650px;
    }
    div .sesbasic_admin_form label.required:after{
        content:" *";
        color:#f00;
    }
    .sesbasic_form_help_icon{
        display:inline-block;
        margin:0 0 5px 5px;
    }
    .sesbasic-form{
    +foreground;
        padding:10px;
        overflow:hidden;
    }
    .sesbasic-form > div{
        background:#fff;
        border:1px solid #ccc;
        overflow:hidden;
        padding:0px;
    }
    .sesbasic-form-cont{
        padding:15px;
    }
    .sesbasic-admin-sub-tabs + .sesbasic-form-cont{
        padding-top:0;
    }
    .sesbasic-form-cont:after{
        clear:both;
        content:'';
        display:block;
    }
    .sesbasic-form-cont h3{
        margin-left:0 !important;
        margin-bottom:10px;
    }
    .sesbasic-form .settings form,
    .sesbasic-form .settings form > div{
        border-width:0;
        background-color:transparent;
        padding:0;
    }
    /*Category Form*/
    .sesbasic-categories-form *{
        -webkit-box-sizing:border-box;
        -moz-box-sizing:border-box;
        box-sizing:border-box;
    }
    .sesbasic-categories-add-form{
        float:left;
        padding-right:20px;
        position:relative;
        width:35%;
    }
    .sesbasic-categories-add-form h4{
        font-size:15px;
    }
    .sesbasic-categories-add-form .sesbasic-form-field{
        margin-top:15px;
    }
    .sesbasic-categories-add-form .sesbasic-form-field-label{
        margin-bottom:5px;
    }
    .sesbasic-categories-add-form .sesbasic-form-field label{
        font-weight:bold;
    }
    .sesbasic-categories-add-form .sesbasic-form-field-element p{
        margin-top:5px;
        font-size:11px;
    }
    .sesbasic-categories-add-form input[type="text"], .sesbasic-categories-add-form textarea{width:100%;}
    .sesbasic-categories-add-form textarea{min-height:100px;}
    .sesbasic-categories-add-form select{max-width:100%;}
    .sesbasic-categories-add-form-overlay{
        background:url(~/application/modules/Sesbasic/externals/images/loading.gif) no-repeat center rgba(255, 255, 255, .5);
        position:absolute;
        bottom:0;
        top:0;
        left:0;
        right:0;
    }
    .sesbasic-categories-listing{
        background-color:#f5f5f5;
        float:right;
        position:relative;
        width:65%
    }
    .sesbasic-categories-listing .sesbasic-category-icon{
        max-width:20px;
    }
    .sesbasic-categories-listing span.buttons{
        margin:5px;
        float:left;
    }
    /*Category Mapping Popup*/
    .sesbasic_catmaping_form.settings form > div{
        width:450px;
    }
    /*Feature Request Button*/
    .sesbasic_admin_plugin_title h2{
        overflow:hidden;
    }
    .sesbasic_nav_btns{
        float:right;
        margin-top:-40px;
    }
    .sesbasic_admin_plugin_title .sesbasic_nav_btns{
        margin:0 0 0 20px;
    }
    .sesbasic_nav_btns a{
        background-color:#f36a33;
        border-radius:3px;
        background-position:10px center;
        background-repeat:no-repeat;
        color:#fff;
        float:left;
        font-weight:bold;
        padding:7px 15px 7px 30px;
        margin-left:10px;
        position:relative;
    }
    .sesbasic_nav_btns a:before{
        font-family:'Font Awesome 5 Free';
        left:10px;
        position:absolute;
        font-size:17px;
        font-weight:normal;
        top:5px;
    }
    .sesbasic_nav_btns a:hover{
        text-decoration:none;
        opacity:.8;
    }
    .sesbasic_nav_btns a.request-btn{
        background-image:url(~/application/modules/Sesbasic/externals/images/request.png);
    }
    .sesbasic_nav_btns a.help-btn:before{
        content:"\\f059"
    }
    .sesbasic_nav_btns a.back-btn{
        background-image:url(~/application/modules/Sesbasic/externals/images/back.png);
    }
    /*View Ststics Popup*/
    .sesbasic_view_stats_popup{
        margin:10px 0 0 10px;
        width:500px;
    }
    .sesbasic_view_stats_popup h3{
        margin-bottom:10px;
    }
    .sesbasic_view_stats_popup > table{
        width:100%;
    }
    .sesbasic_view_stats_popup > table > tbody > tr:nth-child(2n) {
        background-color:#f8f8f8;
    }
    .sesbasic_view_stats_popup > table > tbody > tr > td{
        border-top:1px solid #ccc;
        padding:5px;
        font-weight:bold;
        vertical-align:top;
        width:30%;
    }
    .sesbasic_view_stats_popup > table > tbody > tr > td + td{
        font-weight:normal;
        width:70%;
    }
    .sesbasic_view_stats_popup > table > tbody > tr > td img{
        max-width:60px;
        max-height:60px;
    }
    /*Rating Star*/
    .sesbasic_rating_star{
        overflow:hidden;
    }
    .sesbasic_rating_star > span{
        cursor:pointer;
        display:inline-block;
        margin:0 1px;
        vertical-align:middle;
        /*  text-shadow:0px 0px 1px rgba(0, 0, 0, .3);*/
    }
    .sesbasic_rating_star > span.fa:before{
        color:#ff4500;
        font-size:24px;
    }
    .sesbasic_rating_star > span.star-disable:before{
        color:#ddd;
    }
    .sesbasic_rating_star_small.fa:before{
        color:#ff4500;
        font-size:12px;
    }
    [dir="rtl"] .sesbasic_rating_star > span,
    [dir="rtl"] .sesbasic_rating_star_small{
        filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=0, mirror=1);
        -webkit-transform:scale(-1, 1);
        -ms-transform:scale(-1, 1);
        transform:scale(-1, 1);
    }
    .sesbasic_rating_parameter{
        overflow:hidden;
    }
    .sesbasic_rating_parameter *{
        -webkit-box-sizing:border-box;
        -moz-box-sizing:border-box;
        box-sizing:border-box;
    }
    .sesbasic-rating-parameter-unit{
        background-color:#ff4500;
        cursor:pointer;
        display:inline-block;
        margin:0 1px;
        vertical-align:middle;
        position:relative;
        height:10px;
        width:20px;
        /*  text-shadow:0px 0px 1px rgba(0, 0, 0, .3);*/
    }
    .sesbasic-rating-parameter-unit-disable{
        background-color:transparent;
        border:2px solid #ddd;
    }
    .sesbasic_rating_parameter_small .sesbasic-rating-parameter-unit{
        width:12px;
        height:6px;
    }
    /*Add Item of the day Popup*/
    .sesbasic_add_itemoftheday_popup .form-label{
        float:none !important;
    }
    .sesbasic_add_itemoftheday_popup .form-elements > div + div{
        margin-top:15px;
    }
    .sesbasic_add_itemoftheday_popup .event_calendar_container{
        float:none;
    }
    .sesbasic_add_itemoftheday_popup div.event_calendar_container button.event_calendar{
        border-radius:0;
        margin-top:0;
    }
    /*Loading Message*/
    .sesbasic_waiting_msg_box{
        background-color:rgba(255, 255, 255, 0.8);
        height:100%;
        left:0;
        position:fixed;
        top:0;
        width:100%;
    }
    .sesbasic_waiting_msg_box_cont{
        background-color:rgba(255, 255, 255, 0.8);
        border:5px solid #43bbef;
        box-shadow:0 0 5px rgba(0, 0, 0, 0.5);
        font-size:20px;
        font-weight:bold;
        height:100px;
        left:50%;
        margin:-50px 0 0 -300px;
        padding:20px;
        position:fixed;
        text-align:center;
        top:50%;
        width:600px;
        z-index:24;
    }
    .sesbasic_waiting_msg_box_cont i{
        background-image:url(~/application/modules/Sesbasic/externals/images/loading.gif);
        background-position:center center;
        background-repeat:no-repeat;
        display:block;
        height:30px;
        margin-top:20px;
        width:100%;
    }
    /*Manage Table*/
    .sesbasic_manage_table{
        width:100%;
    }
    .sesbasic_manage_table *{
        -webkit-box-sizing:border-box;
        -moz-box-sizing:border-box;
        box-sizing:border-box;
    }
    .sesbasic_manage_table_head{
        background-color:#f5f5f5;
        border-bottom:1px solid #aaa;
        display:block;
        overflow:hidden;
    }
    .sesbasic_manage_table_head > div{
        padding:10px;
        float:left;
        font-weight:bold;
        padding-top:7px;
        padding-bottom:7px;
        white-space:nowrap;
    }
    .sesbasic_manage_table_list{
        margin-bottom:10px;
        width:100% !important;
    }
    .sesbasic_manage_table_list li{
        border-bottom:1px solid #eee;
        cursor:move;
        clear:both;
        overflow:hidden;
    }
    .sesbasic_manage_table_list li:nth-child(odd){
        background-color:#fff;
    }
    .sesbasic_manage_table_list li:nth-child(even) {
        background-color:#f8f8f8;
    }
    .sesbasic_manage_table_list li > div{
        padding:10px;
        font-size:.9em;
        float:left;
        padding-top:7px;
        padding-bottom:7px;
        vertical-align:top;
        white-space:normal;
    }
    .sesbasic_loading_cont_overlay{
        background-color:rgba(255, 255, 255, .5);
        background-image:url(~/application/modules/Sesbasic/externals/images/loading.gif);
        background-repeat:no-repeat;
        background-position:center center;
        position:absolute;
        bottom:0;
        display:none;
        top:0;
        left:0;
        right:0;
        z-index:1;
    }
    /*Import*/
    button.sesbasic_import_button{
        background-color:#83b64e;
        background-image:url(~/application/modules/Sesbasic/externals/images/import/import.png);
        background-position:5px center;
        background-repeat:no-repeat;
        border:medium none;
        border-radius:0;
        font-size:17px;
        font-weight:normal;
        padding:10px 10px 10px 35px;
        text-shadow:inherit;
    }
    button.sesbasic_import_button:hover{
        background-color:#78a946;
    }
    .sesbasic_import_msg span{
        background-position:5px center;
        background-repeat:no-repeat;
        border-radius:0;
        color:#fff;
        display:inline-block;
        font-size:17px;
        padding:10px 10px 10px 35px;
    }
    .sesbasic_import_loading span{
        background-color:#6fbbde;
        background-image:url(~/application/modules/Sesbasic/externals/images/import/loading.GIF);
    }
    .sesbasic_import_success span{
        background-color:#83b64e;
        background-image:url(~/application/modules/Sesbasic/externals/images/import/success.png);
    }
    .sesbasic_import_error span{
        background-color:#c12e2a;
        background-image:url(~/application/modules/Sesbasic/externals/images/import/error.png);
    }
    /*Popup Form*/
    .sesbasic_popup_form{
        margin:15px 0 0 15px;
    }
    .sesbasic_popup_form form > div > div{
        width:600px;
    }
    .sesbasic_popup_form form > div > div .form-element input[type="text"],
    .sesbasic_popup_form form > div > div .form-element textarea{
        min-width:0;
        max-width:100%;
        width:100%;
        box-sizing:border-box;
    }
    /*Buttons*/
    .sesbasic_button{
        border-radius:3px;
        display:inline-block;
        padding:.5em 1em;
        font-weight:bold;
        border:none;
        background-color:#619dbe;
        border:1px solid #50809b;
        color:#fff !important;
        font-family:inherit !important;
        text-shadow:0px -1px 0px rgba(0, 0, 0, .3);
        font-family:arial, sans-serif;
    }
    .sesbasic_button:hover{
        background-color:#7eb6d5;
        text-decoration:none;
    }
    .sesbasic_button:before{
        margin-right:5px;
        font-family:'Font Awesome 5 Free' !important;
    }
    .sesbasic_notice_btn{
        -moz-border-radius:3px;
        -webkit-border-radius:3px;
        border-radius:3px;
        font-weight:bold;
        border:none;
        background-color:$theme_button_background_color;
        color:$theme_button_font_color;
        text-shadow:0px -1px 0px rgba(0, 0, 0, .3);
        padding:5px 8px;
        margin-top:2px;
        display:inline-block;
    }
    /* Help setting tab in admin panel */
    .sesatoz_support_links {
        display:flex;
        flex-wrap:wrap;
    }
    .sesatoz_support_links a {
        padding:40px 10px;
        width:30%;
        margin:5px;
        text-align:center;
        text-decoration:none !important;
        font-family:monospace;
        transition:all .5s ease;
    }
    .sesatoz_support_links a h4 {
        font-size:20px;
        font-weight:bold;
        color:#706e6e;
    }
    .sesatoz_support_links a p {
        font-size:15px;
        padding:0 20px;
        margin-top:10px;
        color:#818181;
    }
    .sesatoz_support_links a:nth-child(4n+1){
        background:#e1f9ed;
    }
    .sesatoz_support_links a:nth-child(4n+2){
        background:#daf5ce;
    }
    .sesatoz_support_links a:nth-child(4n+3){
        background:#fbf9d6;
    }
    .sesatoz_support_links a:nth-child(4n+4){
        background:#ffe4ed;
    }
    .sesatoz_support_links a:nth-child(4n+5) {
        background:#ebe0ff;
    }
    .sesatoz_support_links a:nth-child(4n+6) {
        background:#e3e7ff;
    }
    .sesatoz_support_links a:hover {
        box-shadow:0 20px 20px -15px #cacaca;
        transform:scale(1.01);
        transition:all .5s ease;
    }
    .sesatoz_support_links a img {
        margin:0 auto;
        display:block;
        margin-bottom:10px;
        filter:invert(60%);
    }
    .sesatoz_support_links b{
        font-weight:bold;
    }
    /*Help setting tab in admin panel*/
    .sesbasic_support_links{
        display:flex;
        flex-wrap:wrap;
    }
    .sesbasic_support_links a{
        padding:20px 10px 15px;
        width:30%;
        margin:5px;
        text-decoration:none !important;
        transition:all .5s ease;
    }
    .sesbasic_support_links a i{
        float: left;
        margin: 0 10px 10px 0;
        width: 25px;
        font-size: 28px;
        color: #706e6e;
    }
    .sesbasic_support_links a h4{
        font-size:17px;
        font-weight:bold;
        color:#706e6e;
        margin-top:5px;
    }
    .sesbasic_support_links a p{
        clear:both;
        font-size:13px;
        margin-top:10px;
        color:#818181;
        text-align:justify;
    }
    .sesbasic_support_links a p span._btn{
        background-color:#0DC7F1;
        color:#fff;
        float:right;
        padding:3px 10px;
        margin-top:10px;
        text-transform:uppercase;
        font-size:90%;
    }
    .sesbasic_support_links a:nth-child(1){
        background:#e1f9ed;
    }
    .sesbasic_support_links a:nth-child(2){
        background:#daf5ce;
    }
    .sesbasic_support_links a:nth-child(3){
        background:#fbf9d6;
    }
    .sesbasic_support_links a:nth-child(4){
        background:#ffe4ed;
    }
    .sesbasic_support_links a:nth-child(5){
        background:#ebe0ff;
    }
    .sesbasic_support_links a:nth-child(6){
        background:#eeffd8;
    }
    .sesbasic_support_links a:nth-child(7){
        background:#e3fffa;
    }
    .sesbasic_support_links a:nth-child(8){
        background:#e3e7ff;
    }
    .sesbasic_support_links a:nth-child(9){
        background:#ffecee;
    }
    /*.sesbasic_support_links a:hover{
        box-shadow:0 20px 20px -15px #cacaca;
        transform:scale(1.01);
        transition:all .5s ease;
    }*/
    .sesbasic_support_links a img{
        float:left;
        margin:0 10px 10px 0;
        width:30px;
    }
    .sesbasic_support_links b{
        font-weight:bold;
    }
    .sesbasic_support_social_buttons{
        margin-top:30px;
    }
    .sesbasic_support_social_buttons ul{
        display:flex;
        justify-content:center;
    }
    .sesbasic_support_social_buttons ul li{
        padding:0 5px;
        width:25%;
    }
    .sesbasic_support_social_buttons ul li._facebook a{
        background-color:#3a589b;
    }
    .sesbasic_support_social_buttons ul li._twitter a{
        background-color:#1da1f2;
    }
    .sesbasic_support_social_buttons ul li._youtube a{
        background-color:#cf3427;
    }
    .sesbasic_support_social_buttons ul li._se a{
        background-color:#000000;
    }
    .sesbasic_support_social_buttons ul a{
        justify-content:center;
        align-items:center;
        display:flex;
        text-align:center;
        color:#fff;
        height:50px;
        box-shadow:0 5px 5px rgba(0, 0, 0, .2);
        position:relative;
        -webkit-transition:all 200ms ease 0s;
        -moz-transition:all 200ms ease 0s;
        -o-transition:all 200ms ease 0s;
        transition:all 200ms ease 0s;
        top:0;
    }
    .sesbasic_support_social_buttons ul a:hover{
        text-decoration:none;
        top:-5px;
    }
    .sesbasic_support_social_buttons ul a i{
        margin-right:8px;
        font-size:17px;
        vertical-align:middle;
    }
    .sesbasic_support_social_buttons ul li._se img{
        width:17px;
        vertical-align:middle;
    }
    .sesbasic_support_social_buttons ul a span{
        font-weight:bold;
    }
    .sesbasic_support_contact_links{
        margin-top:30px;
    }
    .sesbasic_support_contact_links ul{
        display:flex;
        justify-content:center;
        background-color:#f8f8f8;
        padding:20px;
    }
    .sesbasic_support_contact_links ul li{
        margin:0 10px;
        font-size:15px;
        display:flex;
        align-items:center;
    }
    .sesbasic_support_contact_links ul li i{
        font-size:20px;
        margin-right:5px;
        vertical-align:middle;
    }
    .sesbasic_support_contact_links ul li span,
    .sesbasic_support_contact_links ul li span a{
        font-weight:bold;
    }
    .sesbasic_support_contact_links ul li i.fa-skype{
        color:#00aaf2;
    }
    .sesbasic_support_contact_links ul li i.fa-whatsapp{
        color:#4caf50;
    }
    .sesbasic_support_contact_links ul li i.fa-call{
        color:#fc7a51;
    }
    .sesbasic_support_contact_links ul li i img{
        width:16px;
        vertical-align:top;
    }
    /*DATE CHOOSER CSS START HERE*/
    .datepicker{
        background-color:#fff;
    }
    .datepicker .days .title{
        color:#999;
    }
    .datepicker .selected,
    .datepicker .days .week .day:hover,
    .datepicker .months .month:hover,
    .datepicker .years .year:hover,
    .datepicker .days .week .day:hover,
    .datepicker .months .month:hover,
    .datepicker .years .year:hover{
        background:#619dbe !important;
        color:#fff !important;
    }
    /*DATE CHOOSER CSS END HERE*/
    .sesbasic_tip {
        text-align:center !important;
        padding:20px 0;
        clear:both;
        width:100%;
    }
    .sesbasic_tip img {
        margin-bottom:20px;
        max-width:128px;
    }
    .sesbasic_tip span {
        display:block;
        font-size:17px;
    }
    .sesbasic_tip ._btn{
        margin-top:15px;
    }
    .sesbasic_tip ._btn a{
        display:inline-block;
        padding:8px 20px;
        font-weight:bold;
    }
    /*No Content Tip CSS End Here*/
    /* Tabs */
    .tabs_alt > ul {
        height: auto;
        line-height: inherit;
    }
</style>

