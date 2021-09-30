<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Sitemember
* @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<?php if(!$this->viewType) : ?>
 <div class="compliments_icon c_grid">
    <span class="compliment_conut"><i class="fa fa-gift" aria-hidden="true"></i><?php 
    echo $this->translate(array("%s Compliment !", "%s Compliments !", $this->complimentsCount), $this->complimentsCount) ?> </span>

    <div id="layout_sitemember_compliments_icon">
        <ul class="o_hidden">         
            <?php foreach ($this->compliments as $item): ?>
            <li>
                    <?php $category = Engine_Api::_()->getItem("sitemember_compliment_category",$item->complimentcategory_id);?>
                    <?php  echo $this->itemPhoto($category, 'thumb.icon', '', array('title'=>$category->getTitle(),
                    'align' => 'center','onclick' => "tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitemember_profile_compliments'));")); ?>
                    <span class="compliment_count"><?php echo $item->count; ?></span>      
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

</div> 
<?php else : ?>
<div class="compliments_icon c_list">
    <span class="compliment_conut"><i class="fa fa-gift" aria-hidden="true"></i><?php 
    echo $this->translate(array("%s Compliment !", "%s Compliments !", count($this->compliments)), count($this->compliments)) ?></span>
    <div id="layout_sitemember_compliments_icon">
        <ul class="o_hidden">
            <?php foreach ($this->compliments as $item): ?>
            <li>
                    <?php $category = Engine_Api::_()->getItem("sitemember_compliment_category",$item->complimentcategory_id);?>
                    <?php  echo $this->itemPhoto($category, 'thumb.icon', '', array('title'=>$category->getTitle(),
                    'align' => 'center')); ?>
                    <div class="compliment_info">
                     <div class="compliment_name"><?php echo $category->getTitle() ?></div>  
                    <span class="compliment_count"><?php echo $this->complimentTable->getComplimentCount(array('complimentcategory_id' => $category->getIdentity(),'resource_id' => $this->subject->getIdentity(),'resource_type' =>$this->subject->getType())); ?></span> 
                    </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>
