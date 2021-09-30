<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: place-customization-file.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php if (!empty($this->error_message)): ?>
    <div class="tip">
        <span>
            <?php echo $this->error_message; ?>
        </span>
    </div>
<?php endif; ?>

<form method="post" class="global_form_popup">
    <div>
        <h3><?php echo $this->translate("Create customization.css File?") ?></h3>
        <p>
            <?php
            echo $this->translate("You are about to create a new file “customization.css file” over here '/application/themes/sitecoretheme/'. Are you sure you want to create this file?");
            ?>		
        </p>
        <br />
        <p>
            <button type='submit'><?php echo $this->translate("Create File") ?></button>
            <?php echo $this->translate(" or ") ?> 
            <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
                <?php echo $this->translate("cancel") ?></a>
        </p>
    </div>
</form>