<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: save-blog-admin.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php foreach($this->paginator as $blogAdmin):?>
	<div class="admin_manage" id="admin_manage_<?php echo $blogAdmin->role_id;?>">
		<?php $user = Engine_Api::_()->getItem('user', $blogAdmin->user_id);?>
		<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle())) ?>
		<?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
		<?php if($blogAdmin->user_id != $this->owner_id):?>
			<a class="remove_blog" href="javascript:void(0);" onclick="removeUser('<?php echo $blogAdmin->blog_id;?>','<?php echo $blogAdmin->role_id;?>');"><i class="fa fa-times"></i></a>
		<?php endif;?>
		<br />
	</div>
<?php endforeach;?>
