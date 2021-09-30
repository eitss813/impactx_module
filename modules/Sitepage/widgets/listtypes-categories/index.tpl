<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>

<div class="b_medium seaocore_navigation <?php echo $this->viewDisplayHR ? "seaocore_navigation_h" : "seaocore_navigation_v"; ?>" >
    <?php $level = 0 ?>

    <ul class="seaocore_menu <?php echo $this->viewDisplayHR ? "seaocore_menu_h" : "seaocore_menu_v b_dark" ?>" id="nav_cat_<?php echo $this->identity ?>">
        <?php if (!empty($this->categoriesArray)): ?>
            <?php if (empty($this->category_id)): ?>
                <?php foreach ($this->categoriesArray as $categorylist): ?>
                    <?php $level = $this->productTypesCount > 1 ? 1 : 0; ?>
                    <?php $category = $categorylist['category']; ?>
                    <?php $subcategories = $categorylist['subcategories']; ?>
                    <li class="level<?php echo $level . " " . (!empty($subcategories) ? 'parent' : '') ?> ">
                        <a class="<?php echo $this->productTypesCount <= 1 ? "level-top" : '' ?> <?php
                        if (isset($this->requestAllParams['category']) && $this->requestAllParams['category'] == $category->getIdentity()): echo "selected";
                        endif;
                        ?>" href="<?php echo $category->getHref() ?>">
                            <span><?php echo $this->translate($category->getTitle()) ?></span>
                        </a> 
                            <?php if (!empty($subcategories)): ?>
                            <ul class="level<?php echo $level ?>">
                                <?php foreach ($subcategories as $subcategorieslist): ?>
                                    <?php $level = $this->productTypesCount > 1 ? 2 : 1; ?>
                                    <?php $subcategory = $subcategorieslist['subcategory'] ?>
                    <?php $subsubcategories = $subcategorieslist['subsubcatgories'] ?>
                                    <li class="level<?php echo $level . " " . (!empty($subsubcategories) ? 'parent' : '') ?> ">
                                        <a class="<?php
                                           if (isset($this->requestAllParams['subcategory']) && $this->requestAllParams['subcategory'] == $subcategory->getIdentity()): echo "selected";
                                           endif;
                                           ?>" href="<?php echo $subcategory->getHref() ?>">
                                            <span><?php echo $this->translate($subcategory->getTitle()) ?></span>
                                        </a>                       
                                            <?php if (!empty($subsubcategories)): ?>
                                            <ul class="level<?php echo $level ?>">
                                                <?php foreach ($subsubcategories as $subsubcategory): ?>
                                                    <?php $level = $this->productTypesCount > 1 ? 3 : 2; ?>
                                                    <li class="level<?php echo $level ?> ">
                                                        <a class="<?php
                            if (isset($this->requestAllParams['subsubcategory']) && $this->requestAllParams['subsubcategory'] == $subsubcategory->getIdentity()): echo "selected";
                            endif;
                            ?>" href="<?php echo $subsubcategory->getHref() ?>">
                                                            <span><?php echo $this->translate($subsubcategory->getTitle()) ?></span>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <?php
                $level = $this->productTypesCount > 1 ? 1 : 0;
                $subcategories = $this->categoriesArray;
                ?>    
                <?php if (!empty($subcategories)): ?>
                    <?php foreach ($subcategories as $subcategorieslist): ?>
                        <?php $level = $this->productTypesCount > 1 ? 2 : 1; ?>
                        <?php $subcategory = $subcategorieslist['subcategory'] ?>
                        <?php $subsubcategories = $subcategorieslist['subsubcatgories'] ?>
                        <li class="level<?php echo $level . " " . (!empty($subsubcategories) ? 'parent' : '') ?> ">
                            <a class="<?php
                if (isset($this->requestAllParams['subcategory']) && $this->requestAllParams['subcategory'] == $subcategory->getIdentity()): echo "selected";
                endif;
                ?>" href="<?php echo $subcategory->getHref() ?>">
                                <span><?php echo $this->translate($subcategory->getTitle()) ?></span>
                            </a>                       
                            <?php if (!empty($subsubcategories)): ?>
                                <ul class="level<?php echo $level ?>">
                                        <?php foreach ($subsubcategories as $subsubcategory): ?>
                                               <?php $level = $this->productTypesCount > 1 ? 3 : 2; ?>
                                        <li class="level<?php echo $level ?> ">
                                            <a class="<?php
                        if (isset($this->requestAllParams['subsubcategory']) && $this->requestAllParams['subsubcategory'] == $subsubcategory->getIdentity()): echo "selected";
                        endif;
                        ?>" href="<?php echo $subsubcategory->getHref() ?>">
                                                <span><?php echo $this->translate($subsubcategory->getTitle()) ?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
</div>

<script type="text/javascript">
    en4.core.runonce.add(function() {
        if (!(typeof NavigationSiteevent == 'function')) {
            new Asset.javascript(en4.core.staticBaseUrl + 'application/modules/Siteevent/externals/scripts/core.js', {
                onLoad: addDropdownMenu
            });
        } else {
            addDropdownMenu();
        }

        function addDropdownMenu() {
            NavigationSiteevent("nav_cat_<?php echo $this->identity ?>", {"show_delay": "100", "hide_delay": "100"});
        }
    })
</script>