<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _subCategory.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$request = Zend_Controller_Front::getInstance()->getRequest();
$module = $request->getModuleName();
$controller = $request->getControllerName();
$action = $request->getActionName();
?>

<?php if ($module == 'sitecrowdfunding' && ($action == 'home' || $action == 'manage' || $action == 'index')): ?>
    <li id='subcategory_id_loadingimage' style='display:none;'><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/loading.gif" /></li>
    <li id='subcategory_id-wrapper' style='display:none;' > 
        <span ><?php echo $this->translate('Subcategory') ?></span>
        <select name='subcategory_id' id='subcategory_id' onchange="showFields(this.value, 2);
                addOptions(this.value, 'subcat_dependency', 'subsubcategory_id', 0);" ></select>
    </li>

    <li id='subsubcategory_id_loadingimage' style='display:none;'><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/loading.gif" /></li> 
    <li id='subsubcategory_id-wrapper' style='display:none;'>
        <span ><?php echo $this->translate('3%s Level Category', "<sup>rd</sup>") ?></span>
        <select name='subsubcategory_id' id='subsubcategory_id' onchange='showFields(this.value, 3);
                setSubSubCategorySlug(this.value);'></select>
    </li>
<?php else: ?>
    <div id='subcategory_id_loadingimage' class='form-wrapper' style='display:none;' ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/loading.gif" /></div>
    <div id='subcategory_id-wrapper' class='form-wrapper' style='display:none;'>
        <div class='form-label'><label><?php echo $this->translate('Subcategory') ?></label></div>
        <div class='form-element'>
            <select name='subcategory_id' id='subcategory_id' onchange="showFields(this.value, 2);
                addOptions(this.value, 'subcat_dependency', 'subsubcategory_id', 0);" ></select>
        </div>
    </div>
    <div id='subsubcategory_id_loadingimage' class='form-wrapper' style='display:none;'><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/loading.gif" /></div>
    <div id='subsubcategory_id-wrapper' class='form-wrapper' style='display:none;'>
        <div class='form-label'><label><?php echo $this->translate('3%s Level Category', "<sup>rd</sup>") ?></label></div>
        <div class='form-element'>
            <select name='subsubcategory_id' id='subsubcategory_id' onchange='showFields(this.value, 3);
                setSubSubCategorySlug(this.value);'></select>
        </div>
    </div>
<?php endif; ?>

<script type="text/javascript">
        function setSubSubCategorySlug(value) {
            $('subsubcategoryname').value = sitecrowdfunding_categories_slug[value];
        }
</script>  