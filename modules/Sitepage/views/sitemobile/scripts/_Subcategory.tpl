<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formSubcategory.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<?php
$tabel = Engine_Api::_()->getDbTable('categories', 'sitepage');
$subCategories = $tabel->getCategoriesByLevel('subcategory');

if (count($subCategories) == 0)
  return;

$request = Zend_Controller_Front::getInstance()->getRequest();
$module = $request->getModuleName();
$action = $request->getActionName();
$params = $request->getParams();
$catParams = array();
if ((isset($params['subcategory_id']) && $cat = $params['subcategory_id']) || (isset($params['subcategory']) && $cat = $params['subcategory'])) {
  $catParams[] = array('type' => 'subcategory', 'value' => $cat, 'isChildSet' => 1);
  if ((isset($params['subsubcategory_id']) && $cat = $params['subsubcategory_id']) || (isset($params['subsubcategory']) && $cat = $params['subsubcategory'])) {
    $catParams[] = array('type' => 'subsubcategory', 'value' => $cat);
  }
}


$subsubCategories = $tabel->getCategoriesByLevel('subsubcategory');
?>
<?php if ($module == 'sitepage' && ($action == 'home' || $action == 'manage' || $action == 'index')): ?>
  <li id='subcategory_id-wrapper' class="dnone"> 
    <span> <?php echo $this->translate('Subcategory'); ?></span>
    <select name='subcategory_id' id='subcategory_id' onchange="sm4.core.category.set(this.value, 'subsubcategory');">
      <option value="0" ></option>
      <?php foreach ($subCategories as $category): ?>
        <option class="subcategory_option" value="<?php echo $category->getIdentity() ?>" data-parent_category="<?php echo "sp_cat_" . $category->cat_dependency; ?>" ><?php echo $this->translate($category->getTitle(true)); ?></option>
      <?php endforeach; ?>
    </select>
  </li>
  <?php if (count($subsubCategories) > 0): ?>
    <li id='subsubcategory_id-wrapper' class="dnone">
      <span ><?php echo $this->translate('3%s Level Category', "<sup>rd</sup>") ?></span>
      <select name='subsubcategory_id' id='subsubcategory_id' onchange="sm4.core.category.onChange('subsubcategory',this.value);">
        <option value="0" ></option>
        <?php foreach ($subsubCategories as $category): ?>
          <option class="subsubcategory_option dnone" value="<?php echo $category->getIdentity() ?>" data-parent_category="<?php echo "sp_cat_" . $category->cat_dependency; ?>" ><?php echo $this->translate($category->getTitle(true)); ?></option>
        <?php endforeach; ?>
      </select>
    </li>
  <?php endif; ?>
<?php else: ?>
  <div id='subcategory_id-wrapper' class='form-wrapper dnone'>
    <div class='form-label'><label><?php echo $this->translate('Subcategory', "<sup>rd</sup>") ?></label></div>
    <div class='form-element'>
      <select name='subcategory_id' id='subcategory_id' onchange="sm4.core.category.set(this.value,'subsubcategory');">
        <option value="0" ></option>
        <?php foreach ($subCategories as $category): ?>
          <option class="subcategory_option dnone" value="<?php echo $category->getIdentity() ?>" data-parent_category="<?php echo "sp_cat_" . $category->cat_dependency; ?>" ><?php echo $this->translate($category->getTitle(true)); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <?php if (count($subsubCategories) > 0): ?>
    <div id='subsubcategory_id-wrapper' class='form-wrapper dnone'>
      <div class='form-label'><label><?php echo $this->translate('3%s Level Category', "<sup>rd</sup>") ?> </label></div>
      <div class='form-element'>
        <select name='subsubcategory_id' id='subsubcategory_id' onchange="sm4.core.category.onChange('subsubcategory',this.value);">
          <option value="0" ></option>
          <?php foreach ($subsubCategories as $category): ?>
            <option class="subsubcategory_option dnone" value="<?php echo $category->getIdentity() ?>" data-parent_category="<?php echo "sp_cat_" . $category->cat_dependency; ?>" ><?php echo $this->translate($category->getTitle(true)); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  <?php endif; ?>
<?php endif; ?>
<script type="text/javascript">
  sm4.core.runonce.add(function(){
    sm4.core.category.setDefault(<?php echo $this->jsonInline($catParams) ?>);
  });
</script>