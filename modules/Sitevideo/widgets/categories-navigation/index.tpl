<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>
<div class="b_medium seaocore_navigation <?php echo $this->viewDisplayHR ? "seaocore_navigation_h" : "seaocore_navigation_v"; ?>" >
    <?php $level = 0 ?>
    <ul class="seaocore_menu <?php echo $this->viewDisplayHR ? "seaocore_menu_h" : "seaocore_menu_v" ?>" id="nav_cat_<?php echo $this->identity ?>">
        <?php if (!empty($this->categoriesArray)): ?>
            <?php foreach ($this->categoriesArray as $categorylist): ?>
                <?php $level = $this->productTypesCount > 1 ? 1 : 0; ?>
                <?php $category = $categorylist['category']; ?>
                <?php $subcategories = $categorylist['subcategories']; ?>
                <li class="level<?php echo $level . " " . (!empty($subcategories) ? 'parent' : '') ?> ">
                    <a class="<?php echo $this->productTypesCount <= 1 ? "level-top" : '' ?> <?php
                    if (isset($this->requestAllParams['category']) && $this->requestAllParams['category'] == $category->getIdentity()): echo "selected";
                    endif;
                    ?>" href="<?php echo $this->url(array('category_id' => $category->getIdentity(), 'categoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $category->getIdentity())->getCategorySlug()), "sitevideo_general_category"); ?>">

                        <span><?php echo $this->translate($category->getTitle()) ?></span>
                    </a> 
                    <?php if (!empty($subcategories)): ?>
                        <ul class="level<?php echo $level ?>">
                            <?php foreach ($subcategories as $subcategorieslist): ?>
                                <?php $level = $this->productTypesCount > 1 ? 2 : 1; ?>
                                <?php $subcategory = $subcategorieslist['subcategory'] ?>
                                <li class="level<?php echo $level . "  " ?> ">
                                    <a class="<?php
                                    if (isset($this->requestAllParams['subcategory']) && $this->requestAllParams['subcategory'] == $subcategory->getIdentity()): echo "selected";
                                    endif;
                                    ?>" href="<?php echo $this->url(array('category_id' => $category->getIdentity(), 'categoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $category->getIdentity())->getCategorySlug(), 'subcategory_id' => $subcategory->getIdentity(), 'subcategoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $subcategory->getIdentity())->getCategorySlug()), "sitevideo_general_subcategory"); ?>">
                                        <span><?php echo $this->translate($subcategory->getTitle()) ?></span>
                                    </a>                       
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

<script type="text/javascript">
    en4.core.runonce.add(function () {
        if (!(typeof NavigationSitevideo == 'function')) {
            new Asset.javascript(en4.core.staticBaseUrl + 'application/modules/Sitevideo/externals/scripts/core.js', {
                onLoad: addDropdownMenu
            });
        } else {
            addDropdownMenu();
        }

        function addDropdownMenu() {
            NavigationSitevideo("nav_cat_<?php echo $this->identity ?>", {"show_delay": "100", "hide_delay": "100"});
        }
    });
</script>