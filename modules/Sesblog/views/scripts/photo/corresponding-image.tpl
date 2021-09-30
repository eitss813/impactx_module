<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: corresposding-image.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php if(count($this->paginator) && !empty($this->paginator)){
          foreach($this->paginator as $item){ ?>
						<a data-url="<?php echo $item->photo_id; ?>" class="sesblog_corresponding_image_album" href="<?php echo $item->getHref(); ?>">
            	<img src="<?php echo $item->getPhotoUrl('thumb.icon'); ?>"/>
            </a>		
   <?php  }
}
 ?>
