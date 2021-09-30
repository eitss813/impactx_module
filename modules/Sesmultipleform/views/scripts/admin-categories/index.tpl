<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */ 
?>
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
<?php include APPLICATION_PATH .  '/application/modules/Sesmultipleform/views/scripts/dismiss_message.tpl';?>
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
<script type="application/javascript">

function refreshParentWithFormId(value){
	window.location.href = en4.core.baseUrl+"admin/sesmultipleform/categories/index/id/"+value;
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