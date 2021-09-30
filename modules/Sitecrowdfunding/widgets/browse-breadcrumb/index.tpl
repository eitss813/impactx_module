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
<?php if (!empty($this->category_id) || (isset($this->formValues['tag']) && !empty($this->formValues['tag']) && isset($this->formValues['tag_id']) && !empty($this->formValues['tag_id']))): ?>
    <div class="sitecrowdfunding_breadcrumb">
        <?php if (!empty($this->category_id)): ?>
            <?php echo $this->htmlLink($this->url(array('action' => 'browse'), "sitecrowdfunding_general"), $this->translate("Browse Projects")) ?>
            <?php
            $this->category_name = $this->translate($this->category_name);
            $this->subcategory_name = $this->translate($this->subcategory_name);
            ?>
            <?php if ($this->category_name != '') : ?>
                <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
                <?php if ($this->subcategory_name != ''): ?> 
                    <?php echo $this->htmlLink($this->url(array('category_id' => $this->category_id, 'categoryname' => Engine_Api::_()->getItem('sitecrowdfunding_category', $this->category_id)->getCategorySlug()), 'sitecrowdfunding_general_category'), $this->translate($this->category_name)) ?>
                <?php else: ?>
                    <?php echo $this->translate($this->category_name) ?>   
                <?php endif; ?>
                <?php if ($this->subcategory_name != ''): ?> 
                    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
                    <?php echo $this->translate($this->subcategory_name) ?>       
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (((isset($this->formValues['tag']) && !empty($this->formValues['tag']) && isset($this->formValues['tag_id']) && !empty($this->formValues['tag_id'])))): ?>
            <?php
            $tag_value = $this->formValues['tag'];
            $tag_value_id = $this->formValues['tag_id'];
            $browse_url = $this->url(array('action' => 'browse'), "sitecrowdfunding_general", true) . "?tag=$tag_value&tag_id=$tag_value_id";
            ?>
            <?php if ($this->category_name): ?><br /><?php endif; ?>
            <?php echo $this->translate("Showing Projects tagged with: "); ?>
            <b><a href='<?php echo $browse_url; ?>'>#<?php echo $this->formValues['tag'] ?></a>
                <?php if ($this->current_url2): ?>  
                    <a href="<?php echo $this->url(array('action' => 'browse'), "sitecrowdfunding_general", true) . "?" . $this->current_url2; ?>"><?php echo $this->translate('(x)'); ?></a></b>
            <?php else: ?>
                <a href="<?php echo $this->url(array('action' => 'browse'), "sitecrowdfunding_general", true); ?>"><?php echo $this->translate('(x)'); ?></a></b>        
        <?php endif; ?>
    <?php endif; ?>
    </div>
<?php endif; ?>

