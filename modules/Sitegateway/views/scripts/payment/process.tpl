<?php $product = $this->product; ?>
<?php $allParams = $this->allParams; 
$currentCurrency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
$priceStr = Zend_Registry::get('Zend_View')->locale()->toCurrency($this->productPrice,$currentCurrency);

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.3/jquery.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/3.0.0/jquery.payment.js"></script>
 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/3.0.0/jquery.payment.min.js"></script>
 <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
  <link rel="stylesheet" href="application/modules/Sitegateway/externals/styles/style.css">
  <style type="text/css" media="screen">
    .has-error input {
      border-width: 2px;
    }

    .validation.text-danger:after {
      content: 'Validation failed';
    }

    .validation.text-success:after {
      content: 'Validation of data is successful';
    }
input#cc-number {
  background: url('application/modules/Sitegateway/externals/images/card.png') no-repeat 95% center;
  background-size: 25px 19px;
}

input#email {
 background: url('application/modules/Sitegateway/externals/images/email.png') no-repeat scroll 3% 50%;
padding-left:40px;
 background-size: 25px 19px;
  }
    input#cc-exp {
 background: url('application/modules/Sitegateway/externals/images/calender.png') no-repeat scroll 9% 50%;
padding-left:40px;
 background-size: 25px 19px;
  }
    input#cc-cvc {
 background: url('application/modules/Sitegateway/externals/images/lock.png') no-repeat scroll 9% 50%;
padding-left:40px;
 background-size: 25px 19px;
  }

.checkout-cvc {
  width: 100px;
}

input#cc-number.visa {
  background-image: url('application/modules/Sitegateway/externals/images/visa.png');
}

input#cc-number.mastercard {
  background-image: url('application/modules/Sitegateway/externals/images/mastercard.png');
}

input#cc-number.discover {
  background-image: url('application/modules/Sitegateway/externals/images/discover.png');
}
input#cc-number.amex {
  background-image: url('application/modules/Sitegateway/externals/images/express.png');
}
input#cc-number.jcb {
  background-image: url('application/modules/Sitegateway/externals/images/jcb.png');
}
input#cc-number.diners {
  background-image: url('application/modules/Sitegateway/externals/images/diners.png');
}
</style>

  <script>
  function validateEmail(emailField){
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

        if (reg.test(emailField.value) == false) 
        {
           document.getElementById('email-error').innerHTML = 'Invalid email';
           document.getElementById('email-error').style.color = "red";
            return false;
        }
document.getElementById('email-error').innerHTML = '';
        return true;

}
    //To remove the jquery conflicit at the time of payment using stripe    
    jQuery.noConflict();

   jQuery(function() {   
      jQuery('.cc-number').payment('formatCardNumber');
      jQuery('.cc-exp').payment('formatCardExpiry');
      jQuery('.cc-cvc').payment('formatCardCVC');
      jQuery.fn.toggleInputError = function(erred) {
        this.toggleClass('has-error', erred);
        return this;
      };
      jQuery('form').submit(function(e) {       
        e.preventDefault();
        var cardType = jQuery.payment.cardType(jQuery('.cc-number').val());

        if(cardType=='visa')
          jQuery('#cc-number').addClass('visa');
        if(cardType=='mastercard')
          jQuery('#cc-number').addClass('mastercard');
        if(cardType=='american express')
          jQuery('#cc-number').addClass('amex');
        if(cardType=='discover')
        jQuery('#cc-number').addClass('discover');
        if(cardType=='jcb')
        jQuery('#cc-number').addClass('jcb');
        if(cardType=='dinersclub')
        jQuery('#cc-number').addClass('diners');

       
        jQuery('.cc-number').toggleInputError(!jQuery.payment.validateCardNumber(jQuery('.cc-number').val()));
        jQuery('.cc-exp').toggleInputError(!jQuery.payment.validateCardExpiry(jQuery('.cc-exp').payment('cardExpiryVal')));
        jQuery('.cc-cvc').toggleInputError(!jQuery.payment.validateCardCVC(jQuery('.cc-cvc').val(), cardType));
        jQuery('.cc-brand').text(cardType);
        jQuery('.validation').removeClass('text-danger text-success');
        jQuery('.validation').addClass(jQuery('.has-error').length ? 'text-danger' : 'text-success');
        if(jQuery('.validation').hasClass('text-success'))
        {
          jQuery('input[type="submit"]').prop('disabled', true);
          var card_exp_year =jQuery('.cc-exp').val().split('/')[1];
          var card_exp_year=card_exp_year.replace(" ","");
          var card_exp_month =jQuery('.cc-exp').val().split('/')[0];
          var card_exp_month=card_exp_month.replace(" ","");
         
           Stripe.card.createToken({
                        number: jQuery('.cc-number').val(),
                        cvc: jQuery('.cc-cvc').val(),
                        exp_month: card_exp_month,
                        exp_year: card_exp_year,
       
                    }, stripeResponseHandler);
              }
      });

    });

  </script>
  <script type="text/javascript">
            // this identifies your website in the createToken call below
            var key='<?php echo $this->publishable; ?>';
            Stripe.setPublishableKey(key);
            var secretkey ='<?php echo $this->secret; ?>';
            function stripeResponseHandler(status, response) {
                if (response.error) {
                    // re-enable the submit button
                    jQuery('input[type="submit"]').prop('disabled', false);
          // show hidden div
                     document.getElementById('a_x200').style.display = 'block';

                    // show the errors on the form
                    jQuery(".payment-errors").html(response.error.message);

                } 
                else {
                
                   jQuery('input[type="submit"]').prop('disabled', true);
                  document.getElementById('processing-payment-stripe').style.display = 'block';
                  
                    var form$ = jQuery("#payment-form");
                    // token contains id, last4, and card type
                    var token = response['id'];

                    var request = new Request.JSON({
                url: '<?php echo $this->url(array('module' => 'sitegateway', 'controller' => 'payment', 'action' => 'payment'), "default"); ?>',
                method: 'post',
                data: {
                    format: 'json',
                    secret : secretkey,
                    stripeToken: token,
                    product_id: '<?php echo $product->getIdentity(); ?>',
                    product_type: '<?php echo $product->getType(); ?>',
                    product_price: <?php echo $this->productPrice; ?>,
                    product_desc: '<?php echo json_encode($this->productDesc); ?>',
                    product_qty: '<?php echo $this->productQty; ?>',
                    productParentId: '<?php echo $this->productParentId; ?>',
                    allParams: <?php echo json_encode($allParams); ?>
                },
                //responseTree, responseElements, responseHTML, responseJavaScript
                onSuccess: function (responseJSON) {
                    window.location.href = '<?php echo $allParams['RETURNURL']; ?>' + '&customer_id=' + responseJSON.customer_id + '&subscription_id=' + responseJSON.subscription_id + '&charge_id=' + responseJSON.charge_id;
                }
            });
            request.send();
                    
                  
                }
            }
 
</script>

</head>
<body>
     <form novalidate autocomplete="on" method="POST" id="payment-form" class="checkout payment-form" >
        <div class="checkout-header">
      <h1 class="checkout-title">
      <b><?php
       echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE')); ?></b>
  <!--       <span class="checkout-price">&nbsp;</span> -->
      </h1>
    </div>
    <div class="checkout-body">
       <p>
      <input id="email" type="text" class="checkout-input checkout-card email" autocomplete="email" placeholder="Email" onblur="validateEmail(this);" required> <span id="email-error"></span>
    </p>
    <p>
      <input id="cc-number" type="tel" class="checkout-input checkout-card cc-number" autocomplete="cc-number" placeholder="Card number" required>
    </p>
    <p>
      <input id="cc-exp" type="tel" class="checkout-input checkout-exp cc-exp" autocomplete="cc-exp" placeholder="MM/YY" required>
     
      <input id="cc-cvc"  type="tel" class="checkout-input checkout-cvc cc-cvc" autocomplete="off" placeholder="CVC" required>
    </p>
    <p>
      <input type="submit" value="Pay <?php echo $priceStr ?>" class="checkout-btn">
    </p>
    </div>
     <h6 class="validation"></h6>     
    </form>
    <div id="a_x200">
     <center><h2 class="payment-errors" style="color : red;"> </h2></center>
    </div>
    <div id="processing-payment-stripe" style="display:none;">
    <div>
        <center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/loading.gif" /></center>
    </div>
    <div id="LoadingImage" style="text-align:center;margin-top:15px;font-size:17px;">  
        <?php echo $this->translate("Processing Request. Please wait .....") ?>
       
    </div>
</div>
</body>
</html>