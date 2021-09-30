<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: _blogRatingStat.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php if(Engine_Api::_()->sesbasic()->getViewerPrivacy('sesblog_review', 'view')):?>
	<?php if(isset($this->allParams['show_criteria']) && in_array('rating', $this->allParams['show_criteria']) && isset($item->rating)): ?>
		<span title="<?php echo $this->translate(array('%s rating', '%s ratings', round($item->rating,1)), $this->locale()->toNumber(round($item->rating,1)))?>"><i class="far fa-star"></i><?php echo round($item->rating,1).'/5';?></span>
	<?php endif; ?>
<?php endif;?>
