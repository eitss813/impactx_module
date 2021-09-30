<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding'); ?>
<div class="category_navigation category_menu_v">
    <ul class="category_menu category_menu_v" id="nav_cat_<?php echo $this->identity ?>">
        <?php $level = 0 ?>
        <?php if (!empty($this->categories)): ?>   
            <?php foreach ($this->categories as $category): ?>
                <?php $level = $this->productTypesCount > 1 ? 1 : 0; ?>
                <?php $subcategories = $category['sub_categories']; ?>
                <li class="level<?php echo $level . " " . (!empty($subcategories) ? 'parent' : '') ?> ">
                    <div class="level li_text close">
                        <a class="<?php echo $this->productTypesCount <= 1 ? "level-top" : '' ?> <?php
                            if (isset($this->requestAllParams['category']) && $this->requestAllParams['category'] == $category['category_id']): echo $this->translate("selected");
                            endif;
                            ?>" href="<?php echo $this->url(array('category_id' => $category['category_id'], 'categoryname' => $tableCategory->getCategory($category['category_id'])->getCategorySlug()), 'sitecrowdfunding_general_category'); ?>">
                            <span><?php echo $this->translate($category["category_name"]) ?></span> 
                        </a>
                        <?php if (!empty($subcategories)): ?>
                            <span class="menu_toggle_button">+</span>
                        <?php endif ?>
                    </div>
                    <?php if (!empty($subcategories)): ?>
                        <ul class="level<?php echo $level ?>">
                            <?php foreach ($subcategories as $subcategory): ?>
                                <?php $level = $this->productTypesCount > 1 ? 2 : 1; ?>
                                <?php $subsubcategories = $subcategory['tree_sub_cat']; ?>
                                <li class="level<?php echo $level . " " . (!empty($subsubcategories) ? 'parent' : '') ?> ">
                                    <div class="level li_text close">
                                        <a class="<?php
                                        if (isset($this->requestAllParams['subcategory']) && $this->requestAllParams['subcategory'] == $subcategory['sub_cat_id']): echo "selected";
                                        endif;
                                        ?>" href="<?php echo $this->url(array('category_id' => $category['category_id'], 'categoryname' => $tableCategory->getCategory($category['category_id'])->getCategorySlug(), 'subcategory_id' => $subcategory['sub_cat_id'], 'subcategoryname' => $tableCategory->getCategory($subcategory['sub_cat_id'])->getCategorySlug()), 'sitecrowdfunding_general_subcategory') ?>">
                                        <span><?php echo $this->translate($subcategory['sub_cat_name']) ?></span>
                                    </a>
                                    <?php if (!empty($subsubcategories)): ?>
                                        <span class="menu_toggle_button">+</span>
                                    <?php endif ?>
                                </div>
                                <?php if (!empty($subsubcategories)): ?>
                                    <ul class="level<?php echo $level ?>">
                                        <?php foreach ($subsubcategories as $subsubcategory): ?>
                                            <?php $level = $this->productTypesCount > 1 ? 3 : 2; ?>
                                            <li class="level<?php echo $level . "  " ?> ">
                                                <a class="<?php
                                                if (isset($this->requestAllParams['subcategory']) && $this->requestAllParams['subcategory'] == $subcategory['sub_cat_id']): echo $this->translate("selected");
                                                endif;
                                                ?>" href="<?php
                                                echo $this->url(
                                                array('category_id' => $category['category_id'],
                                                'categoryname' => $tableCategory->getCategory($category['category_id'])->getCategorySlug(), 'subcategory_id' => $subcategory['sub_cat_id'], 'subcategoryname' => $tableCategory->getCategory($subcategory['sub_cat_id'])->getCategorySlug(), 'subsubcategory_id' => $subsubcategory['tree_sub_cat_id'],
                                                'subsubcategoryname' => $tableCategory->getCategory($subsubcategory['tree_sub_cat_id'])->getCategorySlug()), 'sitecrowdfunding_general_subsubcategory')
                                                ?>">
                                                <span><?php echo $this->translate($subsubcategory['tree_sub_cat_name']) ?></span>
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
<?php endif; ?>
</ul>
</div>
<script type="text/javascript">
    en4.core.runonce.add(function () {

        li_text = $$('ul.category_menu_v .parent>div.li_text');
        li_text.store('toggled', true);
        li_text.addEvent('click', function (event) {
            if (this.retrieve('toggled')) {
                var ul = this.parentElement.getElement('ul');
                this.getElement('span.menu_toggle_button').set('html', '-');
                this.removeClass('close');
                this.addClass('open');
                hidelevel(ul.get('class').substr(0, 6));
                var slide = new Fx.Reveal(ul, {duration: 300, mode: 'vertical'});
                slide.reveal();
            } else {
                var ul = this.parentElement.getElement('ul');
                this.getElement('span.menu_toggle_button').set('html', '+');
                this.removeClass('open');
                this.addClass('close');
                var slide = new Fx.Reveal(ul, {duration: 300, mode: 'vertical'});
                slide.dissolve();
            }
            this.store('toggled', !(this.retrieve('toggled')));
        });
    });

    function hidelevel(level) {
        var ul = $$('.category_menu_v ul.' + level);
        for (var i = ul.length - 1; i >= 0; i--) {
            li_text = ul[i].parentElement.getElement('div.li_text');
            if (!li_text.retrieve('toggled')) {
                var slide = new Fx.Reveal(ul[i], {duration: 300, mode: 'vertical'});
                slide.dissolve();
                li_text.store('toggled', true);
                li_text.removeClass('open');
                li_text.addClass('close');
                li_text.getElement('span.menu_toggle_button').set('html', '+');
            }
        }
    }
</script>

<style>
    ul.level0{
        display: none;
    }
    ul.level1{
        display: none;
    }
</style>