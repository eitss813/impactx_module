<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: gatway.tpl 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
 
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblogpackage/externals/styles/styles.css'); ?>
<div class="generic_layout_container layout_middle">
	<div class="generic_layout_container layout_sesblog_browse_menu">
    <div class="headline">
      <h2>
        <?php echo $this->translate('Blogs');?>
      </h2>
      <?php if( count($this->navigation) > 0 ): ?>
        <div class="tabs">
          <?php
            // Render the menu
            echo $this->navigation()
              ->menu()
              ->setContainer($this->navigation)
              ->render();
          ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <div class="generic_layout_container layout_core_content">
    <div class="sesblogpackage_payment_process">
      <?php $currentCurrency =  Engine_Api::_()->sesblogpackage()->getCurrentCurrency(); ?>
      <form method="get" action="<?php echo $this->escape($this->url(array('action' => 'process'))) ?>" enctype="application/x-www-form-urlencoded">
        <div>
          <div>
            <h3>
              <?php echo $this->translate('Make Payment to subscribe to ').$this->package->title; ?>
            </h3>
            <p class="_des">
              <?php if( $this->package->recurrence ): ?>
                <?php echo $this->translate('Choose the gateway below to continue to make the payment of-') ?>
              <?php else: ?>
                <?php echo $this->translate('Please pay a one-time fee to continue:') ?>
              <?php endif; ?>
              <?php echo $this->package->getPackageDescription(); ?>
            </p>
            <div class="form-elements">
              <div id="buttons-wrapper" class="form-wrapper">
                <?php foreach( $this->gateways as $gatewayInfo ):
                  $gateway = $gatewayInfo['gateway'];
                  $plugin = $gatewayInfo['plugin'];
                  $first = ( !isset($first) ? true : false );
                  $gatewayObject = $gateway->getGateway();
                  $supportedCurrencies = $gatewayObject->getSupportedCurrencies();
                  if(!in_array($currentCurrency,$supportedCurrencies))
                    continue;
                  ?>
                  <?php if( !$first ): ?>
                    <span><?php echo $this->translate('or') ?></span>
                  <?php endif; ?>
                    <button type="submit" name="execute" onclick="$('gateway_id').set('value', '<?php echo $gateway->gateway_id ?>')">
                      <?php echo $this->translate('Pay with %1$s', $this->translate($gateway->title)) ?>
                    </button>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
        <input type="hidden" name="gateway_id" id="gateway_id" value="" />
      </form>
      <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('ecoupon')): ?>
        <?php  echo $this->partial('have_coupon.tpl','ecoupon',array('id'=>$this->package->package_id,'params'=>json_encode(array('resource_type'=>$this->item->getType(),'resource_id'=>$this->item->blog_id,'is_package'=>1,'package_type'=>$this->package->getType(),'package_id'=>$this->package->package_id)))); ?> 
      <?php endif; ?>
      <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sescredit')) { ?>
        <div class="sesblogpackage_payment_process_credit">
        <?php  echo $this->partial('apply_credit.tpl','sescredit',array('id'=>$this->package->package_id,'moduleName'=>'sesblogpackage','item_price'=>$this->itemPrice,'item_id'=>$this->item->blog_id)); ?> 
        </div>
      <?php } ?>
    </div>
  </div>
</div>  
<script type="application/javascript">
  var itemPrice<?php echo $this->package->package_id; ?> = '<?php echo $this->itemPrice; ?>';
</script>
