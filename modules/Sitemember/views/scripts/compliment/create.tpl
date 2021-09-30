<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php if($this->error) : ?>
<div class="description"> <?php echo $this->translate("It's not right place to compliment.") ?></div>
<?php return; endif; ?>
<div class="compliments_wrapper">
<h3 class="sitemember_compliment_icon_select"><?php echo $this->translate('Click to select an icon for compliment') ?></h3>
<div class="compliment_grid">
    <?php  foreach ($this->complimentIcons as $item): ?>
        <span class="compliment_item" onclick="setCompliment(this,<?php echo $item->getIdentity(); ?>)"> 
            
            <div><?php  echo $this->itemPhoto($item, 'thumb.icon', '', array(
            'align' => 'center')); ?></div>
            <div><?php echo $this->translate($item->getTitle()) ?></div>
        </span>
    <?php  endforeach; ?> 
</div>
 <div class="sm_form_popup">
            <?php echo $this->form->render($this); ?>
        </div>
</div>
<script type="text/javascript">
        function setCompliment(element,compliment_id){
                $("complimentcategory_id").value = compliment_id; 
                var activeElement = document.querySelector(".compliment_item_highlight");
                activeElement.removeClass('compliment_item_highlight');
                element.addClass('compliment_item_highlight');
                
        }
        en4.core.runonce.add(function(){ 
            document.querySelector(".compliment_item").addClass('compliment_item_highlight');
            $("complimentcategory_id").value = <?php echo $this->complimentIcons[0]->getIdentity() ?>;
        });
</script>