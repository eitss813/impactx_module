<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php
	$id = $this->identity;
?>
<?php if($this->buttonposition == 1){ ?>
  <?php if($this->popuptype){ ?>
  	<a href="javascript:;" data-url="<?php echo $this->layout()->staticBaseUrl.'widget/index/mod/sesmultipleform/name/forms/formid/'.$this->formtype.'/identity/'.rand('1989828','200000').'/hideform/'.$this->hideform.'/closepopup/'.$this->closepopup.'/redirect/'.$this->redirect; ?>" class="sesmultipleform_side_button sesmultipleform_side_button_<?php echo $id; ?> sesmultipleform_right_side_button sessmoothbox sesbasic_animation sesbasic_bxs"><?php echo $this->translate($this->buttontext); ?></a>
  <?php }else{ ?> 
  	<a href="<?php echo $this->redirectOpen; ?>" class="sesmultipleform_side_button sesmultipleform_side_button_<?php echo $id; ?> sesmultipleform_right_side_button sesbasic_animation sesbasic_bxs"><?php echo $this->translate($this->buttontext); ?></a>
  <?php } ?>
<?php }else if($this->buttonposition == 2){ ?>
  <?php if($this->popuptype){ ?>
 	 <a href="javascript:;" data-url="<?php echo $this->layout()->staticBaseUrl.'widget/index/mod/sesmultipleform/name/forms/formid/'.$this->formtype.'/identity/'.rand('1989828','200000').'/hideform/'.$this->hideform.'/closepopup/'.$this->closepopup.'/redirect/'.$this->redirect; ?>" class="sesmultipleform_side_button sesmultipleform_side_button_<?php echo $id; ?> sesmultipleform_left_side_button sessmoothbox sesbasic_animation sesbasic_bxs"><?php echo $this->translate($this->buttontext); ?></a>
  <?php }else{ ?> 
  	<a href="<?php echo $this->redirectOpen; ?>" class="sesmultipleform_side_button sesmultipleform_side_button_<?php echo $id; ?> sesmultipleform_left_side_button sesbasic_animation sesbasic_bxs"><?php echo $this->translate($this->buttontext); ?></a>
  <?php } ?>
<?php }else{ ?>
 <?php if($this->popuptype){ ?>
		<a href="javascript:;" data-url="<?php echo $this->layout()->staticBaseUrl.'widget/index/mod/sesmultipleform/name/forms/formid/'.$this->formtype.'/identity/'.rand('1989828','200000').'/hideform/'.$this->hideform.'/closepopup/'.$this->closepopup.'/redirect/'.$this->redirect; ?>" class="sesmultipleform_button sesmultipleform_side_button_<?php echo $id; ?> sessmoothbox sesbasic_animation sesbasic_bxs"><?php echo $this->translate($this->buttontext); ?></a>
 <?php }else{ ?> 
  	<a href="<?php echo $this->redirectOpen; ?>" class="sesmultipleform_button sesmultipleform_side_button_<?php echo $id; ?> sesbasic_animation sesbasic_bxs"><?php echo $this->translate($this->buttontext); ?></a>
  <?php } ?>
<?php } ?>
<?php if($this->margintype == 'per'){$type = '%';}else{$type = 'px';} ?>
<style type="text/css">
html a.sesmultipleform_side_button_<?php echo $id; ?>{
  background-color: <?php echo '#'.$this->buttoncolor;?> !important;
  color:<?php echo '#'.$this->textcolor;?> !important;
	top:<?php echo $this->margin.$type; ?>
}
html a.sesmultipleform_side_button_<?php echo $id; ?>:hover{
  background-color: <?php echo '#'.$this->texthovercolor;?> !important;
  color:<?php echo '#'.$this->textcolor;?> !important;
  text-decoration:none !important;
}
</style>