<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<div class="_footer_logo">
  <?php echo $this->content()->renderWidget("core.menu-logo", $this->logoParams); ?>
</div>
<?php if( count($this->navigation) > 0 ): ?>
  <div class="_footer_link">
    <?php
    echo $this->navigation()
      ->menu()
      ->setContainer($this->navigation)
      ->render();
    ?>
  </div>
<?php endif; ?>
<?php if( count($this->socialMenusNavigation) > 0 ): ?>
  <div class="_menu_social_sites">
    <?php
    echo $this->navigation()
      ->menu()
      ->setContainer($this->socialMenusNavigation)
      ->setPartial(array('_navFontIcons.tpl', 'core'))
      ->render()
    ?>
  </div>
<?php endif; ?>
<?php if( 1 !== count($this->languageNameList) ): ?>
  <div class="_footer_language">
    <form method="post" action="<?php echo $this->url(array('controller' => 'utility', 'action' => 'locale'), 'default', true) ?>" class="language_form">
      <?php $selectedLanguage = $this->translate()->getLocale() ?>
      <?php echo $this->formSelect('language', $selectedLanguage, array('onchange' => '$(this).getParent(\'form\').submit();'), $this->languageNameList) ?>
      <?php echo $this->formHidden('return', $this->url()) ?>
    </form>
  </div>
<?php endif; ?>
<?php if( $this->viewer()->getIdentity() ): ?>
  <div class="_footer_signout dnone">
    <a href="<?php echo $this->url(array(), 'user_logout', true); ?>" class="button"><?php echo $this->translate('Sign Out') ?></a>
  </div>
<?php endif; ?>
<div class="copyright _footer_copyright">
  <?php echo $this->translate('Copyright &copy;%s', date('Y')) ?>
</div>