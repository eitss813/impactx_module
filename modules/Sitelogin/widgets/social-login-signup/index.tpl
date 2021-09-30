<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Sitelogin
* @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    index.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<?php if(!empty($this->position)) {
echo $this->partial('_layout'.$this->layout.'.tpl', 'sitelogin',$this->data);
}
?>

<?php if(!empty($this->showForm)): ?>
<?php if( !$this->noForm ): ?>

<h3>
    <?php echo $this->translate('Sign In or %1$sJoin%2$s', '<a href="'.$this->url(array(), "user_signup").'" class="user_signup_link">', '</a>'); ?>
</h3>

<?php echo $this->form->setAttrib('class', 'global_form_box')->render($this) ?>

<?php if( !empty($this->fbUrl) ): ?>

<script type="text/javascript">
    var openFbLogin = function () {
        Smoothbox.open('<?php echo $this->fbUrl ?>');
    }
    var redirectPostFbLogin = function () {
        window.location.href = window.location;
        Smoothbox.close();
    }
    
</script>

<?php // <button class="user_facebook_connect" onclick="openFbLogin();"></button> ?>

<?php endif; ?>

<?php else: ?>

<h3 style="margin-bottom: 0px;">
    <?php echo $this->htmlLink(array('route' => 'user_login'), $this->translate('Sign In')) ?>
    <?php echo $this->translate('or') ?>
    <?php echo $this->htmlLink(array('route' => 'user_signup'), $this->translate('Join')) ?>
</h3>

<?php echo $this->form->setAttrib('class', 'global_form_box no_form')->render($this) ?>

<?php endif; ?>
<?php endif; ?>
<?php if(empty($this->position)) {
echo $this->partial('_layout'.$this->layout.'.tpl', 'sitelogin',$this->data);
}
?>

<script type="text/javascript">
    window.onload = function () {
        var parentDiv = document.querySelectorAll("[id='facebook-wrapper']");
        if (parentDiv.length > 0) {
            for (i = 0; i < parentDiv.length; i++) {
                parentDiv[i].style.display = "none";
            }
        }
        var parentDiv = document.querySelectorAll("[id='twitter-wrapper']");
        if (parentDiv.length > 0) {
            for (i = 0; i < parentDiv.length; i++) {
                parentDiv[i].style.display = "none";
            }
        }
    }
</script>    