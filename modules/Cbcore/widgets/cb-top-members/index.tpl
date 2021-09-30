<?php
/**
 * SocialEngine
 *
 * @category   Module
 * @package    Consecutive Bytes Core
 * @copyright  Copyright 2015 - 2017 Consecutive Bytes
 * @license    http://www.consecutivebytes.com/license/
 * @version    4.9.0
 * @author     Consecutive Bytes
 */
?>
 <link type="text/css" href="application/themes/blossomcommunity/carousel/animate.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="application/themes/blossomcommunity/carousel/styles2.css">
 <div class="animated bounceInLeft title">
<?php if(!empty($this->title)): ?>
<h3><?php echo $this->translate($this->title); ?></h3>
<?php else: ?>
<h3><?php echo $this->translate("Our Top Members"); ?></h3>
<?php endif; ?>
</div>
      <div class="flexslider carousel">
        
      <ul class="slides">
    <?php foreach( $this->paginator as $user ): ?>
      <li><div class="member_photo">
        <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.profile', $user->getTitle()), array('class' => 'members_thumb' , 'title' =>$user->getTitle() )) ?>
        </div>
        <p class="title"><?php echo $user->getTitle();?></p>
      </li>
    <?php endforeach; ?>
    </ul>

</div>
<script src="application/themes/blossomcommunity/carousel/jquery-1.11.0.min.js"></script>
<script src="application/themes/blossomcommunity/carousel/jquery.flexslider-min.js"></script>
<script src="application/themes/blossomcommunity/carousel/jquery.flexslider-min.js"></script>
<script src="application/themes/blossomcommunity/carousel/jquery.parallax-1.1.3.js"></script>

<script type="text/javascript">
var qcjq = $.noConflict();
qcjq (document).ready(function() {
	  qcjq ('.flexslider').flexslider({
		animation: "slide",
		controlNav: false,
		directionNav: true,
		animationLoop: true,
		itemWidth: 155,
		itemMargin: 0,
		minItems: 2,
		maxItems: 7,
		move: 1
	  });
  });

</script>