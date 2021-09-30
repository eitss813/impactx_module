<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: categories.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->tinyMCESEAO()->addJS(); ?>

<?php $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl(); ?>
<iframe id='ajaxframe' name='ajaxframe' style='display: none;' src='javascript:false;'></iframe>

<h2>
  <?php echo $this->translate('Advanced Albums Plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='settings clr'>
  <h3><?php echo $this->translate("Albums Categories") ?></h3>

  <p class="description"><?php echo $this->translate('Below, you can add and manage the various categories, sub-categories for the Albums on your site. Sub-categories are very useful as they allow you to further categorize and organize the Albums on your site beyond the superficial categories. You can also add Icons, Banners, URL Components, Top Content, Bottom Content and Meta Information for categories, sub-categories categories. To do so, click on desired category name, edit it and click on "Save Changes" to save your changes. You can also drag and drop categories to arrange their sequence.<br/><br/>Meta Information, URL Component, Top / Bottom Content, etc added to categories enhance the SEO.<br/><br/><b>Note:</b> Category Banner and Meta Information will be available on "Advanced Albums - Album Browse Page" when users search the associated category.'); ?></p>
</div>

<div class="clr mtop10">
  <div class="sitealbum_categories_left fleft">      

    <?php if(!empty($this->getCategoriesTempInfo)): ?>
      <a class="buttonlink seaocore_icon_add" href="<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'settings', 'action' => 'categories', 'category_id' => 0, 'perform' => 'add'), "admin_default", true); ?>"><?php echo $this->translate("Add Category"); ?></a>
    <?php endif; ?>
    <br />

    <div id='categories' class="sitealbum_cat_list_wrapper clr">
      <?php foreach ($this->categories as $value): ?>
        <div id="cat_<?php echo $value['category_id']; ?>" class="sitealbum_cat_list">
          <input type="hidden" id="cat_<?php echo $value['category_id']; ?>_input_count" value="<?php echo $value["count"] ?>">
          <?php $category_name = $this->translate($value['category_name']); ?>

          <?php $url = $this->url(array('module' => 'sitealbum', 'controller' => 'settings', 'action' => 'categories', 'category_id' => $value['category_id'], 'perform' => 'edit'), "admin_default", true); ?>
          <?php $link = "<a href='$url' title='$category_name' id='cat_" . $value['category_id'] . "_title' >" . $category_name . "</a>"; ?>

          <div class="sitealbum_cat">
            <a href="javascript:void(0);" onclick="showsubcate(1,<?php echo $value['category_id']; ?>, 1);" id="hide_cate_<?php echo $value['category_id']; ?>" title="<?php echo $this->translate('Collapse'); ?>" class="sitealbum_cat_showhide"><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitealbum/externals/images/minus.png' border='0' /></a>
            <a href="javascript:void(0);" onclick="showsubcate(2,<?php echo $value['category_id']; ?>, 1);" style="display:none;" id="show_cate_<?php echo $value['category_id']; ?>" title="<?php echo $this->translate('Expand'); ?>" class="sitealbum_cat_showhide"><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitealbum/externals/images/plus.png' border='0' /></a>
            <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitealbum/externals/images/folder_open_yellow.gif' border='0' class='sitealbum_cat_handle' />
            <div class="sitealbum_cat_det <?php if ($this->category_id == $value['category_id']): ?> sitealbum_cat_selected <?php endif; ?>">
              <span class="sitealbum_cat_det_options">
                [<?php echo $value["count"] ?>] | 

                <a class="smoothbox" href="<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'settings', 'action' => 'mapping-category', 'category_id' => $value['category_id']), "admin_default", true); ?>" title="<?php echo $this->translate("Delete Category"); ?>"><?php echo $this->translate("Delete"); ?></a> 
              </span>
              <?php echo "<span class='sitealbum_cat_det_name' id='cat_" . $value['category_id'] . "_span'>$link</span>" ?> 
            </div>			
            <?php $url = $this->url(array('module' => 'sitealbum', 'controller' => 'settings', 'action' => 'categories', 'category_id' => $value['category_id'], 'perform' => 'add'), "admin_default", true); ?>
            <?php $subcate = $this->translate("Sub Categories") . " - <a href='$url'> " . $this->translate("[Add New]") . "</a>" ?>
            <?php echo "<div class='sitealbum_cat_new' id=subcate_admin_new_" . $value["category_id"] . ">$subcate</div>" ?>
          </div>

          <script type="text/javascript">
              window.addEvent('domready', function() {
                createSortable("subcats_<?php echo $value['category_id'] ?>", "img.handle_subcat_<?php echo $value['category_id'] ?>");
              });
          </script>
          <div id="subcats_<?php echo $value['category_id']; ?>" class="sitealbum_sub_cat_wrapper">
            <?php foreach ($value['sub_categories'] as $subcategory): ?>
              <div id="cat_<?php echo $subcategory['sub_cat_id']; ?>" class="sitealbum_cat_list">
                <input type="hidden" id="cat_<?php echo $subcategory['sub_cat_id']; ?>_input_count" value="<?php echo $subcategory['count'] ?>">
                <?php $subcatname = $this->translate($subcategory['sub_cat_name']); ?>
                <?php $url = $this->url(array('module' => 'sitealbum', 'controller' => 'settings', 'action' => 'categories', 'category_id' => $subcategory["sub_cat_id"], 'perform' => 'edit'), "admin_default", true); ?>
                <?php $subcats = "<a href='$url' title='$subcatname' id='cat_" . $subcategory["sub_cat_id"] . "_title'>$subcatname</a>" ?>
                <div class="sitealbum_cat">
                  <?php echo "<img src='" . $this->layout()->staticBaseUrl . "application/modules/Sitealbum/externals/images/folder_open_green.gif ' border='0' class='sitealbum_cat_handle handle_subcat_" . $value['category_id'] . "'>" ?>
                  <div class="sitealbum_cat_det <?php if ($this->category_id == $subcategory['sub_cat_id']): ?> sitealbum_cat_selected <?php endif; ?>">
                    <span class="sitealbum_cat_det_options">[<?php echo $subcategory["count"] ?>] | 

                      <a class="smoothbox" href="<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'settings', 'action' => 'delete-category', 'category_id' => $subcategory['sub_cat_id']), "admin_default", true); ?>" title="<?php echo $this->translate("Delete Category"); ?>"><?php echo $this->translate("Delete"); ?></a>
                    </span>
                    <?php echo "<span class='sitealbum_cat_det_name' id='cat_" . $subcategory["sub_cat_id"] . "_span'>$subcats</span>" ?>
                  </div>	
                </div>	
              </div>
            <?php endforeach; ?>
          </div>          
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if(!empty($this->form)): ?>
    <div class="sitealbum_categories_right">
      <div class='settings'>
        <?php echo $this->form->render($this); ?>
      </div>
    </div>
  <?php endif; ?>
</div>	

<script type="text/javascript">
  function createSortable(divId, handleClass)
  {
    new Sortables($(divId), {handle: handleClass, onComplete: function() {
        changeorder(this.serialize(), divId);
      }
    });
  }

  Sortables.implement({
    serialize: function() {
      var serial = [];
      this.list.getChildren().each(function(el, i) {
        serial[i] = el.getProperty('id');
      }, this);
      return serial;
    }
  });

  window.addEvent('domready', function() {
    createSortable('categories', 'img.sitealbum_cat_handle');
  });

  //THIS FUNCTION CHANGES THE ORDER OF ELEMENTS
  function changeorder(sitealbumorder, divId)
  {
    $('ajaxframe').src = '<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'settings', 'action' => 'categories'), 'admin_default', true) ?>?task=changeorder&sitealbumorder=' + sitealbumorder + '&divId=' + divId;
  }

  function showsubcate(option, cate_id, level) {

    if (level == 1) {
      if (option == 1) {
        $('subcate_admin_new_' + cate_id).style.display = 'none';
        $('subcats_' + cate_id).style.display = 'none';
        $('hide_cate_' + cate_id).style.display = 'none';
        $('show_cate_' + cate_id).style.display = 'block';
      } else if (option == 2) {
        $('subcate_admin_new_' + cate_id).style.display = 'block';
        $('subcats_' + cate_id).style.display = 'block';
        $('hide_cate_' + cate_id).style.display = 'block';
        $('show_cate_' + cate_id).style.display = 'none';
      }
    }
  }


// strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr


  if ($('top_content')) {
<?php
echo $this->tinyMCESEAO()->render(array('element_id' => '"top_content"',
    'language' => $this->language,
    'directionality' => $this->directionality,
    'upload_url' => $this->upload_url));
?>
  }

  if ($('bottom_content')) {
<?php
echo $this->tinyMCESEAO()->render(array('element_id' => '"bottom_content"',
    'language' => $this->language,
    'directionality' => $this->directionality,
    'upload_url' => $this->upload_url));
?>
  }
</script>
