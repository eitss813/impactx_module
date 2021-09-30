<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2018 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/jquery.min.js');
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/wow.js')
?>

 <script>
	  new WOW().init();
</script>

<?php if( trim($this->description != '') ): ?>
    <div class="widgets_title_border">
	   <span></span>
	   <i></i>
	   <span></span>
   </div>
  <div class="widgets_title_description">
    <?php echo $this->translate($this->description);?>
  </div>
<?php endif; ?>

<div class="sitemember_featured_sponsored">
         <div class="member_seeall"><?php echo $this->htmlLink(array('route' => 'sitemember_userbylocation', 'module' => 'sitemember',), $this->translate('See All'), array( 'class' => 'buttonlink')); ?><i style="margin-left: 2px;" class="fa fa-angle-double-right"></i></div>
      <?php foreach( $this->members as $user ): ?>
        <div class="sitemember_f_s_inner wow animated zoomIn">
         <div class="sitemember_f_s_inner_wrapper">
         <div class="sitemember_f_s_inner_wrapper_container">
         <div class="sitemember_f_s_inner_member test">
         	<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.main', $user->getTitle()), array('class' => 'popularmembers_thumb')) ?>
         	      <?php
        $aboutMe = $this->profileFieldsValue($user,'about_me');
        $facebookLink = $this->profileFieldsValue($user,'twitter');
        $twitterLink = $this->profileFieldsValue($user,'facebook');
        ?>
			 <div class="sitemember_f_s_inner_member_hover">
			 <div class="overlay">
				 <div class="center-bar">
				 <?php if( !empty($facebookLink) ):?>
				 <a href="<?php echo $facebookLink; ?>" title="Facebook"><i class="fa fa-facebook"></i></a>
				 <?php endif; ?>
				 <?php if( !empty($twitterLink) ):?>
				 <a href="<?php echo $twitterLink; ?>" title="Twitter"><i class="fa fa-twitter"></i></a>
				 <?php endif; ?>
				 </div>
			 </div>
			 </div>
         </div>
         
         <div class="sitemember_f_s_inner_member_info">
			 <div class="username"><?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?></div>
			 <?php $location = $this->memberInfo($user, array("location"), array('customParams' => 10, 'custom_field_title' => 1, 'custom_field_heading' => 1)); ?>
			 
			 <?php if( !empty($aboutMe) ):?>
			 	<p class="user_description"><?php echo $aboutMe; ?></p>
        	<?php endif; ?>
         </div>
         
        </div>
         </div>
        </div>
      <?php endforeach; ?>
    </div>
