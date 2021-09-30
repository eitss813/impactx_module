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
if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='seaocore_sub_tabs tabs'>
  <ul class="navigation">
    <li class="active">
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecoretheme', 'controller' => 'subscription', 'action' => 'index'), 'Send Newsletter', array())
      ?>
    </li>
    <li>
      <?php
      echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecoretheme', 'controller' => 'subscription', 'action' => 'subscriber-list'), 'Subscribers List', array())
      ?>
    </li>
  </ul>
</div>
<div class='clear'></div>

<?php if( $this->form ): ?>
  <div class="settings">
    <?php echo $this->form->render($this) ?>
  </div>
<?php else: ?>
  <div class="tip">
    Your message has been queued for sending.
  </div>
<?php endif; ?>