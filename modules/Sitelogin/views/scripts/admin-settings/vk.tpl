<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Sitelogin
* @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    vk.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<h2>
    <?php echo $this->translate("Social Login and Sign-up Plugin") ?>
</h2>
<?php if( count($this->navigation) ): ?>
      <div class='tabs seaocore_admin_tabs clr'>
        <?php
    // Render the menu
    //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
      </div>
    <?php endif; ?>
    <?php if( count($this->navigationSubMenu) ): ?>
      <div class='tabs seaocore_admin_tabs clr'>
        <?php
    // Render the menu
    //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigationSubMenu)->render()
        ?>
      </div>

<?php endif; ?>

<?php if ($this->showSubscriptionError){
echo "<div class='sitelogin_tip' style='position:relative;'><span>" . "Note: Please choose default Subscription Plan from <a href='" . $this->url(array('module' => 'payment', 'controller' => 'package', 'action' => 'index'), 'admin_default', true) . "'>here</a> to make your Quick Signup Process via VK more smooth & quick." . "</span></div>";
} ?>

<div class='settings'>
    <?php echo $this->form->render($this) ?>
</div>

<script>
    window.addEvent('domready', function () {
        QuickSignup();
    });

    function QuickSignup() {
        if ($('quickEnable') && $('quickEnable').checked) {
            $('vkProfileType-wrapper').style.display = 'block';
            $('memberLevel-wrapper').style.display = 'block';
        } else
        {
            $('vkProfileType-wrapper').style.display = 'none';
            $('memberLevel-wrapper').style.display = 'none';
        }
    }
</script>
