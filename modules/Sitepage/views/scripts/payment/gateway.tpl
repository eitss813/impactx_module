<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: gateway.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>

<?php if ($this->status == 'pending'): // Check for pending status ?>
   <?php echo $this->translate('Your page is pending payment. You will receive an email when the
  payment completes.'); ?>
<?php else: ?>

  <form method="get" action="<?php echo $this->escape($this->url(array(), "sitepage_process_payment", true)) ?>"
        class="global_form" enctype="application/x-www-form-urlencoded">
    <div>
      <div>
                <?php if ($this->creditAllow) : ?>
                <a id="credit_addTextBox" onclick ="switchCreditBox('credit_redeem_box');" class="code_link sitecredit_form_link" href="javascript:void(0);">
                <img id="credit_lab_png" src="application/modules/Sitecredit/externals/images/lab.png" style="display: inline-block;">
                <img id="credit_lar_png" src="application/modules/Sitecredit/externals/images/lar.png" style="display: none;">Redeem Credits</a>
                <br/>
                <div class="bold mtop10" id="credit_redeem_box">
                                   <!-- Use credits -->
                    <div class="credits_box">
                      <div class="mbot5">
                        <label><?php echo $this->translate("Enter credits to avail discount."); ?></label>
                      </div>
                      <div class="mbot5">
                      <input type='text' id='credit_code_value' name='credit_code' onkeypress="return isNumberKey(event)" value = "" onchange="return validateCreditCode()" />
                      </div>
                      <div class='buttons clr mtop5'>
                        <button type='button' onclick="applyCreditcode();" id="apply_credit_package" style="float:left;">
                          <?php echo $this->translate("Use Credits") ?>
                        </button>

                        <button type='button' onclick="cancelCreditcode();" id="apply_credit_package_cancel" style="display:none;float:left;">
                          <?php echo $this->translate("Remove Credits") ?>
                        </button>
                        <div id="apply_credit_spinner" style="display: inline-block;clear:right;padding-left: 15px;"></div>
                      </div>
                    </div>
                    <br/>
                    <div class="credits_msg"><span id='max_credit_msg' class="form-notices">You can redeem upto <?php echo $this->maxcredit?> credits. </span></div>                    
                    <div class="credits_msg " id='credit_error_msg_div' style="display:none;"><ul class="form-errors" style="margin:5px 0px 0px;"><li style="margin:0px;"><span id='credit_error_msg' ></span></li></ul></div>
                    <div class="credits_msg" id='credit_success_msg_div' style="display:none;"><ul class="form-notices" style="margin:5px 0px 0px;"><li style="margin:0px;"><span id='credit_success_msg' ></span></li></ul>
                    </div>
                </div>
            <?php endif; ?>
            <br/><br/>


        <h3>
          <?php echo $this->translate('Order your Page') ?>
        </h3>
        <p class="form-description">
          <?php echo $this->translate('You have created a page that requires payment. You will be taken to a secure checkout area where you can pay for your page.') ?>
        </p>
        <p style="font-weight: bold; padding-top: 15px; padding-bottom: 15px;max-width:none;">
          <?php if ($this->package->recurrence): ?>
            <?php echo $this->translate('Your Page requires payment:') ?>
          <?php else: ?>
            <?php echo $this->translate('Please pay a one-time fee to continue:') ?>
          <?php endif; ?>
          <?php echo $this->package->getPackageDescription() . "." ?>
        </p>
        <div class="form-elements">
          <div id="buttons-wrapper" class="form-wrapper">
            <?php foreach ($this->gateways as $gatewayInfo):
              $gateway = $gatewayInfo['gateway'];
              $plugin = $gatewayInfo['plugin'];
              $first = (!isset($first) ? true : false );
              ?>
              <?php if (!$first): ?>
                <?php echo $this->translate('or') ?>
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
<?php endif; ?>

<script type="text/javascript">
        function switchCreditBox (id) {
            if($(id).style.display == 'block') {
                $(id).style.display = 'none';
                $('credit_lar_png').style.display = 'inline-block';
                $('credit_lab_png').style.display = 'none';
            }else {
                $('credit_lab_png').style.display = 'inline-block';
                $('credit_lar_png').style.display = 'none';
                $(id).style.display = 'block';
            }
        }


        function applyCreditcode() {
            if( document.getElementById("credit_code_value").value == '' ) {
              document.getElementById('credit_error_msg_div').style.display="block";
              document.getElementById('credit_error_msg').innerHTML = '<?php echo $this->translate("Please Enter valid credits."); ?>';
              return;
            }
            if(!validateCreditCode())
            {
              return;
            }
            en4.core.request.send(new Request.JSON({
              url: "<?php echo $this->url(array('module' => 'sitecredit', 'controller' => 'redeem', 'action' => 'package-purchase'), 'default', true); ?>",
              method: 'post',
              data: {
                format: 'json',
                credit_code: document.getElementById("credit_code_value").value,
                package_type: 'sitepage_package',
                package_id:<?php echo $this->page->package_id ?>,
              },
              onRequest: function(){
                document.getElementById('credit_error_msg').innerHTML = '';
                document.getElementById('credit_error_msg_div').style.display="none";
                document.getElementById("apply_credit_spinner").innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitecredit/externals/images/loading.gif" />';
              },
              onSuccess: function(responseJSON) {
                document.getElementById("apply_credit_spinner").innerHTML = '';
                if (responseJSON.credit_error_msg) {
                  document.getElementById('credit_error_msg_div').style.display="block";
                  document.getElementById('credit_error_msg').innerHTML = responseJSON.credit_error_msg;
                } else if (responseJSON.credit_applied) {
                    document.getElementById('apply_credit_package_cancel').style.display="block";
                    document.getElementById('apply_credit_package').style.display="none";
                    document.getElementById('credit_success_msg').innerHTML = responseJSON.credit_success_msg;
                    document.getElementById('credit_success_msg_div').style.display="block";                     
                }
              }
            }));
        }
        function cancelCreditcode() {
           
            en4.core.request.send(new Request.JSON({
              url: "<?php echo $this->url(array('module' => 'sitecredit', 'controller' => 'redeem', 'action' => 'cancel-package-purchase'), 'default', true); ?>",
              method: 'post',
              data: {
                format: 'json',
                package_type: 'sitepage_package',
                package_id:<?php echo $this->page->package_id ?>,
              },
              onRequest: function(){
                document.getElementById('credit_error_msg').innerHTML = '';
                document.getElementById('credit_error_msg_div').style.display="none";
                document.getElementById("apply_credit_spinner").innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" />';
              },
              onSuccess: function(responseJSON) {
                document.getElementById("apply_credit_spinner").innerHTML = '';
                document.getElementById("credit_code_value").value = '';
                document.getElementById('credit_success_msg').innerHTML = '';
                document.getElementById('credit_success_msg_div').style.display="none";
                document.getElementById('apply_credit_package_cancel').style.display="none";
                document.getElementById('apply_credit_package').style.display="block";
              }
            }));
        }
        function isNumberKey(evt) { 
            var charCode = (evt.charCode) ? evt.which : event.keyCode

            if (charCode > 31 && (charCode < 48 || charCode > 57) || charCode == 46) 
              return false; 
             
            return true; 
        } 
        function validateCreditCode() {
            if($('credit_code_value').value < 1) {
                $('credit_error_msg').innerHTML="Please enter valid credit points"; 
                $('credit_error_msg_div').style.display="block";
                return false;
            }else {
                $('credit_error_msg').innerHTML=""; 
                $('credit_error_msg_div').style.display="none";
                return true;
            }
        }
</script>          