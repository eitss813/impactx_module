<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: view.tpl 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */ 
?>
<div class="sesmultipleform_message_view">
  <h3>Entry Details</h3>
  <ul>
    <li>
      <span>
        <span><?php echo $this->translate('Name:') ?></span>
        <span><?php echo $this->translate($this->entry->first_name) ?></span>
      </span>
    </li>
    <li>
      <span>
        <span><?php echo $this->translate('Email:') ?></span>
        <span><?php echo $this->translate($this->entry->email) ?></span>
      </span>
    </li>
    <?php if($this->entry->category_id): ?>
    <li>
      <span>
        <span><?php echo $this->translate('Category:') ?></span>
       <?php $category = Engine_Api::_()->getItem('sesmultipleform_category', $this->entry->category_id);  ?>
        <?php if(!empty($category)): ?>
        <span><?php echo $category->title ?></span>
        <?php else: ?>
        <span><?php echo "---" ?></span>
        <?php endif; ?>
      </span>
    </li>
    <?php endif; ?>
    <?php if($this->entry->subcat_id): ?>
    <li>
      <span>
        <span><?php echo $this->translate('Sub Category:') ?></span>
       <?php $subcategory = Engine_Api::_()->getItem('sesmultipleform_category', $this->entry->subcat_id);  ?>
        <?php if(!empty($subcategory)): ?>
        <span><?php echo $subcategory->title ?></span>
        <?php else: ?>
        <span><?php echo "---" ?></span>
        <?php endif; ?>
      </span>
    </li>
    <?php endif; ?>
     <?php if($this->entry->subsubcat_id): ?>
    <li>
      <span>
        <span><?php echo $this->translate('Sub Sub Category:') ?></span>
       <?php $subsubcategory = Engine_Api::_()->getItem('sesmultipleform_category', $this->entry->subsubcat_id);  ?>
        <?php if(!empty($subsubcategory)): ?>
        <span><?php echo $subsubcategory->title ?></span>
        <?php else: ?>
        <span><?php echo "---" ?></span>
        <?php endif; ?>
      </span>
    </li>
    <?php endif; ?>
    
    <?php if(count($this->profilefields)){ 
      foreach($this->profilefields as $valProfileField){ ?>
      <li>
        <span>  
          <span><?php echo $valProfileField['label'] ?>:</span>
          <span><?php echo $valProfileField['value']; ?></span>
        </span>
      </li>
     <?php }
    }?>
    
    <li>
      <span>
        <span><?php echo $this->translate('Message:') ?></span>
        <span><?php echo $this->entry->description ?></span>
      </span>
    </li>
    <li>
      <span>
        <span><?php echo $this->translate('Date:'); ?></span>
        <span><?php echo $this->locale()->toDateTime($this->entry->creation_date) ?></span>
      </span>
    </li>
    <li>
      <span>
        <span><?php echo $this->translate('IP Address:'); ?></span>
        <span><?php echo $this->entry->ip_address ?></span>
      </span>
    </li>
  </ul>
  <button type="submit" onclick="parent.Smoothbox.close();
      return false;" name="close_button" value="Close">Close</button>
</div>
