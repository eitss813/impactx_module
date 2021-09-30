<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
  	              . 'application/modules/Seaocore/externals/styles/styles.css');

include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';

  $temp_menu_route = "sitepage_general";
?>
<script type="text/javascript">
//     if($("tag"))
//       $("tag").value='';
    var form;
  var categoryAction =function(category,sub,categoryname, url)
  {

	  if($('filter_form')) {
	    form=document.getElementById('filter_form');
      if($('category')){
      form.elements['category'].value = category;
      if(form.elements['category_id'])
      form.elements['category_id'].value = category;
      }
      if($('categoryname')){
        form.elements['categoryname'].value = categoryname;
      }
      if($('subcategory')){
        form.elements['subcategory'].value = 0;
      }
      if($('subcategoryname')){
       form.elements['subcategoryname'].value = 0;
      }
      if($('subsubcategory')){
        form.elements['subsubcategory'].value = 0;
      }
      if($('subsubcategoryname')){
       form.elements['subsubcategoryname'].value = 0;
      }
			form.submit();
      } 
      else if($('filter_form_category')){
				form=$('filter_form_category');
					if(url == '') {
					window.location.href='<?php echo $this->url(array('action' => 'index'), $temp_menu_route, true)?>';
				} else {
					window.location.href= url;
				}
	    }

  }

 	var subcategoryAction = function(category,subcategory,categoryname,subcategoryname, url, subsubcategory, subsubcategoryname)
 	{

    if($('filter_form')) {
      form=document.getElementById('filter_form');
      if($('category')){
      form.elements['category'].value = category;
      if(form.elements['category_id'])
      form.elements['category_id'].value = category;
    	}

	    if($('categoryname')){
	     form.elements['categoryname'].value = categoryname;
	    }

  	  if($('subcategory')){
	     form.elements['subcategory'].value = subcategory;
       if(form.elements['subcategory_id'])
       form.elements['subcategory_id'].value = subcategory;
	    }
	    if($('subcategoryname')){
	     form.elements['subcategoryname'].value = subcategoryname;
	    }
      
	    if($('subsubcategory')){
	     form.elements['subsubcategory'].value = subsubcategory;
       if(form.elements['subsubcategory_id'])
       form.elements['subsubcategory_id'].value = subsubcategory;
	    }
	    if($('subsubcategoryname')){
	     form.elements['subsubcategoryname'].value = subsubcategoryname;
	    }
      form.submit();
    } else if($('filter_form_category')){
			form=$('filter_form_category');
      if(url == '') {
        window.location.href='<?php echo $this->url(array('action' => 'index'), $temp_menu_route, true)?>';
      } else {
        window.location.href= url;
      }
		}   
  }

  function show_subcat(cat_id)
  {
    if(document.getElementById('subcat_' + cat_id)) {
      if(document.getElementById('subcat_' + cat_id).style.display == 'block') {
        document.getElementById('subcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/plus16.gif';
      }
      else if(document.getElementById('subcat_' + cat_id).style.display == '') {
        document.getElementById('subcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/plus16.gif';
      }
      else {
        document.getElementById('subcat_' + cat_id).style.display = 'block';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/minus16.gif';
      }
    }
  }

  function show_subsubcat(cat_id)
  {
    if(document.getElementById('subsubcat_' + cat_id)) {
      if(document.getElementById('subsubcat_' + cat_id).style.display == 'block') {
        document.getElementById('subsubcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/plus16.gif';
      }
      else if(document.getElementById('subsubcat_' + cat_id).style.display == '') {
        document.getElementById('subsubcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/plus16.gif';
      }
      else {
        document.getElementById('subsubcat_' + cat_id).style.display = 'block';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/minus16.gif';
      }
    }
  }

window.addEvent('domready', function() {
	var subcategory_default = '<?php echo $this->subcategorys; ?>';
	var subsubcategory_default = '<?php echo $this->subsubcategorys;?>';
	if(subcategory_default == 0)
	show_subcat('<?php echo $this->category; ?>');
	if(subsubcategory_default == 0)
	show_subsubcat('<?php echo $this->subcategorys; ?>');
});
</script>

<?php $sitepage_subcategory = Zend_Registry::isRegistered('sitepage_subcategory') ? Zend_Registry::get('sitepage_subcategory') : null; ?>
<?php if (count($this->categories)): ?>
		<form id='filter_form_category' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), $temp_menu_route, true) ?>' style='display: none;'>
			<input type="hidden" id="category" name="category"  value=""/>
			<input type="hidden" id="categoryname" name="categoryname"  value=""/>
      <input type="hidden" id="subsubcategory" name="subsubcategory" value=""/>
			<input type="hidden" id="subcategory" name="subcategory"  value=""/>
			<input type="hidden" id="subcategoryname" name="subcategoryname"  value=""/>
      <input type="hidden" id="subsubcategoryname" name="subsubcategoryname" value=""/>
      <input type="hidden" id="tag" name="tag" value=""/>
		</form>
		</form>
  <ul class="seaocore_browse_category">
    <li>
      <a href="javascript:subcategoryAction(0,0,0,0,0,0,0)" <?php if ($this->category == 0): ?>class="bold"<?php endif; ?>><?php echo $this->translate("All Categories") ?></a>
    </li>
    <?php foreach ($this->categories as $category) : ?>
      <?php $total_subcat = count($category['sub_categories']); ?>
      <?php if (empty($this->sitepage_categories)) {
        exit();
      } ?>
      <?php if ($total_subcat > 0): ?>
        <li>
          <div class="cat" >
            <a href="javascript:show_subcat('<?php echo $category['category_id'] ?>')" id='button_<?php echo $category['category_id'] ?>'>
              <?php if ($this->category != $category['category_id']): ?>
                <img alt=""  src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/plus16.gif' class='icon' border='0' id='img_<?php echo $category['category_id'] ?>'/>
              <?php elseif ($this->subcategorys != 0 && $this->category == $category['category_id']): ?>
                <img alt="" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/minus16.gif' class='icon' border='0' id='img_<?php echo $category['category_id'] ?>'/>
              <?php elseif ($this->category != 0 && $this->category == $category['category_id']): ?>
                <img alt="" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/plus16.gif' class='icon' border='0' id='img_<?php echo $category['category_id'] ?>'/>
              <?php endif; ?>
           </a>
            <a <?php if ($this->category == $category['category_id']): ?> class="bold"<?php endif; ?> href='javascript:void(0);' onclick="javascript:subcategoryAction('<?php echo $category['category_id'] ?>',0, '<?php echo Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->translate($category['category_name'])) ?>',0, '<?php echo $this->url(array('action' => 'index', 'category' => $category["category_id"], 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->translate($category['category_name']))), $temp_menu_route, true);?>', 0, 0);">
              <?php echo $this->translate($category['category_name']) ?>
            </a>
          </div>
          <?php if (empty($sitepage_subcategory)) {
            return;
          } ?>      
          
            <div class="subcat" id="subcat_<?php echo $category['category_id'] ?>" <?php if ($this->category != $category['category_id'] || $this->subcategorys == 0): ?>style="display:none;"<?php endif; ?> >
              <?php foreach ($category['sub_categories'] as $subcategory) : ?>
                <?php $total_subsubcat = count($subcategory['tree_sub_cat']); ?>
                <?php if ($total_subsubcat > 0): ?>
                	<div class="subcat_second">
	                  <a href="javascript:show_subsubcat('<?php echo $subcategory['sub_cat_id'] ?>')" id='button_<?php echo $subcategory['sub_cat_id'] ?>'>
	                    <?php if ($this->subcategorys != $subcategory['sub_cat_id']): ?>
	                    <img alt=""  src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/plus16.gif' class='icon' border='0' id='img_<?php echo $subcategory['sub_cat_id'] ?>'/>
	                  <?php elseif ($this->subsubcategorys != 0 && $this->subcategorys == $subcategory['sub_cat_id']): ?>
	                    <img alt="" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/minus16.gif' class='icon' border='0' id='img_<?php echo $subcategory['sub_cat_id'] ?>'/>
	                  <?php elseif ($this->subcategorys != 0 && $this->subcategorys == $subcategory['sub_cat_id']): ?>
	                    <img alt="" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/plus16.gif' class='icon' border='0' id='img_<?php echo $subcategory['sub_cat_id'] ?>'/>
	                  <?php endif; ?>
	                  </a>
	                  <a <?php if ($this->subcategorys == $subcategory['sub_cat_id']): ?>class="bold"<?php endif; ?>  href='javascript:void(0);' onclick="javascript:subcategoryAction('<?php echo $category['category_id'] ?>','<?php echo $subcategory['sub_cat_id'] ?>','<?php echo Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->translate($category['category_name'])) ?>','<?php echo Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->translate($subcategory['sub_cat_name'])) ?>','<?php echo $this->url(array('action' => 'index', 'category' => $category["category_id"], 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->translate($category['category_name'])), 'subcategory' => $subcategory["sub_cat_id"],'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->translate($subcategory['sub_cat_name']))), $temp_menu_route, true);?>', 0, 0);">
	                    <?php echo $this->translate($subcategory['sub_cat_name']) ?>
	                  </a>
	                </div>  
                  <div class="subcat_third" id="subsubcat_<?php echo $subcategory['sub_cat_id'] ?>" <?php if ($this->subcategorys != $subcategory['sub_cat_id'] || $this->subsubcategorys == 0): ?>style="display:none;"<?php endif; ?> >
                      <?php if(isset($subcategory['tree_sub_cat'])):?>
                      <?php foreach ($subcategory['tree_sub_cat'] as $subsubcategory) : ?>
                        <a <?php if ($this->subsubcategorys == $subsubcategory['tree_sub_cat_id']): ?>class="bold"<?php endif; ?>  href='javascript:void(0);' onclick="javascript:subcategoryAction('<?php echo $category['category_id'] ?>','<?php echo $subcategory['sub_cat_id'] ?>','<?php echo Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->translate($category['category_name'])) ?>','<?php echo Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($subcategory['sub_cat_name']) ?>','<?php echo $this->url(array('action' => 'index', 'category' => $category["category_id"], 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->translate($category['category_name'])),'subcategory' => $subcategory["sub_cat_id"],'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->translate($subcategory['sub_cat_name'])),'subsubcategory' => $subsubcategory["tree_sub_cat_id"],'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->translate($subsubcategory['tree_sub_cat_name'])),), $temp_menu_route, true);?>', '<?php echo $subsubcategory['tree_sub_cat_id'] ?>', '<?php echo $this->translate($subsubcategory['tree_sub_cat_name'])?>');">
                        	<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/gray_arrow.png" alt="">
                          <?php echo $this->translate($subsubcategory['tree_sub_cat_name']); ?>
                        </a>
                      <?php endforeach; ?>
                      <?php endif;?>
                  </div>
                <?php else:?>
                	<div class="subcat_second">
	                  <img  src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/minus16_disabled.gif" class='icon' border="0" />
	                  <a <?php if ($this->subcategorys == $subcategory['sub_cat_id']): ?>class="bold"<?php endif; ?>  href='javascript:void(0);' onclick="javascript:subcategoryAction('<?php echo $category['category_id'] ?>','<?php echo $subcategory['sub_cat_id'] ?>','<?php echo Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->translate($category['category_name'])) ?>','<?php echo Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->translate($subcategory['sub_cat_name'])) ?>','<?php echo $this->url(array('action' => 'index', 'category' => $category["category_id"], 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->translate($category['category_name'])),'subcategory' => $subcategory["sub_cat_id"],'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->translate($subcategory['sub_cat_name']))), $temp_menu_route, true);?>',0, 0);">
	                   <?php echo $this->translate($subcategory['sub_cat_name']) ?>
	                  </a>
	                </div>  
                <?php endif;?>
               <?php endforeach; ?>
            </div>
        </li>
      <?php else: ?>
        <li>
          <div class="cat">
            <?php if (empty($sitepage_subcategory)) {
              return;
            } ?>
            <img  src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/minus16_disabled.gif" class='icon' border="0" />
            <?php if (empty($this->sitepage_categories)) {
              exit();
            } ?>
            <a <?php if ($this->category == $category['category_id']): ?>class="bold"<?php endif; ?>  href='javascript:void(0);' onclick="javascript:categoryAction('<?php echo $category["category_id"] ?>',0, '<?php echo Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->translate($category['category_name'])) ?>','<?php echo $this->url(array('action' => 'index', 'category' => $category["category_id"], 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->translate($category['category_name']))), $temp_menu_route, true);?>');"><?php echo $this->translate($category['category_name']) ?>
            </a>
          </div>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
