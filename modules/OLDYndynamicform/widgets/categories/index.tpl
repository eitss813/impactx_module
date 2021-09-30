<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndynamicform
 * @author     YouNet Company
 */
$session = new Zend_Session_Namespace('mobile');
?>

<?php $request = Zend_Controller_Front::getInstance()->getRequest(); ?>
<div class="generic_list_widget">
    <ul class="ymb_menuRight_wapper yncategories_items">
        <?php foreach ($this->categories as $category) : ?>
            <li value ='<?php echo $category->getIdentity() ?>' class="yncategories_item <?php if ($category->parent_id > 1) echo 'yncategories_sub_item child_'.$category->parent_id.' level_'.$category->level?>">
                <div class="<?php if($request-> getParam('category') == $category -> category_id) echo 'active';?>">
                    <?php if(count($category->getChildList()) > 0 && !$session-> mobile) : ?>
                        <div class="yncategories_have_child yncategories_collapsed">
                            <span class="ynicon yn-caret-down yncategories_submenu"></span>
                            <span class="ynicon yn-caret-right yncategories_mainmenu"></span>
                        </div>
                        <?php
                        echo $this->htmlLink(
                            $category->getHref(),
                            $this->string()->truncate($this->translate($category->title), 20),
                            array('title' => $this->translate($category->title)));
                        ?>
                    <?php else : ?>
                        <div class="yncategories_dont_have_child yncategories_last_sub_item">
                            <span class="ynicon yn-caret-right yncategories_mainmenu"></span>
                        </div>
                        <?php
                        echo $this->htmlLink(
                            $category->getHref(),
                            $this->string()->truncate($this->translate($category->title), 20),
                            array('title' => $this->translate($category->title)));
                        ?>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>