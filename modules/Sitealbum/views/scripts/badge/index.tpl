<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php echo $this->doctype()->__toString() ?>
<?php $locale = $this->locale()->getLocale()->__toString(); $orientation = ( $this->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr' ); ?>
<html id="smoothbox_window" xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $locale ?>" lang="<?php echo $locale ?>" dir="<?php echo $orientation ?>">
<head>
	<base href="<?php echo rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->baseUrl(), '/'). '/' ?>" />
	<title></title>
	<?php	 $this->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
			->appendHttpEquiv('Content-Language', 'en-US');  ?>
	<?php echo $this->headMeta()->toString()."\n" ?>
	<?php $this->headScript()->prependScript($this->headTranslate()->toString()) ?>
</head>
<body>
<div style="background-color: <?php echo $this->background_color; ?>; border:1px solid <?php echo $this->border_color; ?>; height: <?php echo ($this->height-2)."px;"?>" id="sitealbum_badge">
	<div class="badge_header">
		<?php 
		 // Prepare host info
		    $schema = 'http://';
		    if (isset($_ENV["HTTPS"]) &&!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
		      $schema = 'https://';
		    }
		    ?>
		<a href="<?php echo $schema . $_SERVER['HTTP_HOST'].$this->baseUrl() ?>" target="_blank;">
			<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title'); ?>
		</a>
	</div>

<?php if (  $this->badgeEnable &&$this->paginator->getTotalItemCount() > 0): ?>
  <ul class="sitealbum_badge_thumbs" style="width: <?php echo $this->inOneRowWidth."px;"?>">
  <?php foreach ($this->paginator as $item): ?>
    <li>
      <a class="sitealbum_badge_thumbs_photo" href="<?php echo $item->getHref(); ?>" target="_blank;"  title="<?php echo $item->getTitle(); ?>">
        
      <?php echo $this->itemBackgroundPhoto($item, 'thumb.normal', $item->getTitle(), array('tag' => 'span', 'nolazy' => true));
        ?>
      </a>
    </li>
  <?php endforeach; ?>
     <li class="sitealbum_badge_owner">
      <?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->owner, 'thumb.icon', $this->owner->getTitle(), array('nolazy' => true)), array( 'title' => $this->owner->getTitle(), 'target' => '_blank', 'class'=>'thumbs_photo')); ?><br />
      <?php  echo $this->translate("%s's photos.",$this->htmlLink($this->owner->getHref(), $this->owner->getTitle(), array( 'title' => $this->owner->getTitle(), 'target' => '_blank'))); ?>
    </li>
  </ul>
<?php elseif(!$this->badgeEnable): ?>
    <br />
   <div class="tip">
      <span>
    <?php echo $this->translate('Photos Badge has been disabled.'); ?>
      </span>
    </div>
<?php else:?>
     <br />
 <div class="tip">
      <span>
    <?php echo $this->translate('No photo yet.'); ?>
      </span>
    </div>
<?php endif; ?>
</div>
</body>
</html>  
<style type="text/css">
#sitealbum_badge a {
	color:<?php echo $this->text_color ?>;
	text-decoration:none;
}
#sitealbum_badge a:hover {
	color:<?php echo $this->link_color ?>;
	text-decoration:underline;
}
html, body {
	margin:0px;
	padding:0px;
	overflow: hidden;
	font-size: 10pt;
}
* {
	font-family: tahoma, arial, verdana, sans-serif;
}
#sitealbum_badge {
	overflow:hidden;
}
#sitealbum_badge .badge_header {
	padding:0 5px;
	font-size:17px;
	font-weight:bold;
	line-height:20px;
}
ul.sitealbum_badge_thumbs {
	overflow: hidden;
	padding:3px;
	margin:0 auto;
	list-style:none;
}
ul.sitealbum_badge_thumbs > li {
	float: left;
	margin:3px;
	height:90px;
	width:110px;
	text-align:center;
}
ul.sitealbum_badge_thumbs > li.sitealbum_badge_owner {
	padding-top:7px;
	height:80px;
}
ul.sitealbum_badge_thumbs .sitealbum_badge_thumbs_photo {
	display: inline-block;
	border: 1px solid #DDDDDD;
	padding: 4px;
	vertical-align: bottom;
}
ul.sitealbum_badge_thumbs .sitealbum_badge_thumbs_photo:hover {
	border:1px solid #AAAAAA;
	cursor: pointer;
}
ul.sitealbum_badge_thumbs .sitealbum_badge_thumbs_photo > span {
	display: block;
	width: 100px;
	height: 80px;
	background-position: center 50%;
	background-repeat: no-repeat;
	background-size:cover;
}
ul.sitealbum_badge_thumbs .thumb_icon{
	border: 1px solid #DDDDDD;
}
.tip {
	overflow: hidden;
	clear: both;
	margin:10px;
}
.tip > span {
	display: inline-block;
	background-repeat: no-repeat;
	background-position: 6px 6px;
	padding: 6px 6px 6px 27px;
	background-color: #faf6e4;
	float: left;
	margin-bottom: 15px;
	background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/tip.png);
	border: 1px solid #e4dfc6;
}
</style>