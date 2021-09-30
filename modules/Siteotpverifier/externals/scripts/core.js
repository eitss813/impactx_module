
/* $Id: core.js 10182 2014-04-29 23:52:40Z andres $ */

(function () { // START NAMESPACE
  var $ = 'id' in document ? document.id : window.$;
  en4.siteotpverifier = {
    signup: {
      toogleElementsListner: {},
      init: function (params) {
        var form = $$('.' + params.elementClass).getParent('form');
        form.set('data-form-key', params.formKey);
        var countryCodeEl = form.getElementById('country_code').getLast(), phonenoEl = form.getElementById('phoneno').getLast();
        countryCodeEl.inject(phonenoEl, 'before').setStyle('display', '');
        if (params.showBothPhoneAndEmail == 0) {
          var emailEl = form.getElementById(params.emailName);
          var phoneEl = form.getElementById('phoneno');
          $$(form.getElements('.' + params.elementClass))
                  .inject(emailEl.getParent('.form-wrapper').getLast(), 'before')
                  .removeClass('dnone');
          phoneEl.addEvent('blur', function () {
            var email = params.autoEmailTemplate;
            email = email.replace('[PHONE_NO]', this.get('value'));
            emailEl.set('value', email);
          });
          en4.siteotpverifier.signup.toogleElementsListner[params.formKey] = function (form, reset) {
            emailEl = form.getElementById(params.emailName);
            phoneEl = form.getElementById('phoneno');
            console.log(emailEl);
            console.log(phoneEl);
            var target = form.getElementById('signup_otp_type');
            target = target[0] ? target[0] : target;
            $$(form.getElements('.' + params.elementClass + ' .siteotp_choice')).addClass('dnone');
            if (target.get('value') === 'phone') {
              phoneEl.getParent('.form-wrapper').removeClass('dnone');
              emailEl.getParent('.form-wrapper').addClass('dnone');
              form.getElement('.siteotp_phone_choice').addClass('dnone');
              form.getElement('.siteotp_email_choice').removeClass('dnone');
            } else {
              emailEl.getParent('.form-wrapper').removeClass('dnone');
              phoneEl.getParent('.form-wrapper').addClass('dnone');
              form.getElement('.siteotp_email_choice').addClass('dnone');
              form.getElement('.siteotp_phone_choice').removeClass('dnone');
            }
            if (reset) {
              phoneEl.set('value', '');
              emailEl.set('value', '');
            }
          }.bind(this);
          en4.siteotpverifier.signup.toogleElementsListner[params.formKey](emailEl.getParent('form'), false);
        }
      },
      toggoleElementsClickHandler: function (el) {
        var form = $(el).getParent('form');
        var target = form.getElementById('signup_otp_type');
        if (target.get('value') === 'email') {
          $(target).set('value', 'phone');
        } else {
          $(target).set('value', 'email');
        }
        var formKey = form.get('data-form-key');
        en4.siteotpverifier.signup.toogleElementsListner[formKey](form, true);
      }
    }
  }

})(); // END NAMESPACE