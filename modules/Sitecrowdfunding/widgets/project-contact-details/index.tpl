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

<div>
    <?php if($this->email):?><b>Email:</b> <?php echo $this->email;?><br/><?php endif;?>
    <?php if($this->phone):?><b>Phone:</b> <?php echo $this->phone;?><br/><?php endif;?>
    <?php if($this->address):?><b>Address:</b> <?php echo $this->address;?><br/><?php endif;?>
</div>

