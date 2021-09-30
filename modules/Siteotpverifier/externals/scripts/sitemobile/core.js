
(function () { // START NAMESPACE
  var $ = 'id' in document ? document.id : window.$;
  sm4.siteotpverifier = {
    signup: {
      toogleElementsListner: {},
      init: function (params) {
        var form = $('.' + params.elementClass).closest('.global_form');
        form.attr('data-form-key', params.formKey);
        var countryCodeEl = form.find('#country_code').last(), phonenoEl = form.find('#phoneno').parent();
        countryCodeEl.insertBefore(phonenoEl).css({'display' : '', 'width' : '100%'});
        countryCodeEl.addClass('ui-btn ui-icon-carat-d ui-btn-icon-right ui-corner-all ui-shadow');
        // remove extra select
        $('#country_code-button').remove();
        if (params.showBothPhoneAndEmail == 0) {
          var emailEl = form.find('#' + params.emailName);
          var phoneEl = form.find('#phoneno');
          $(form.find('.' + params.elementClass))
                  .insertBefore(emailEl.closest('.form-wrapper').last())
                  .removeClass('dnone');
          phoneEl.on('blur', function () {
            var email = params.autoEmailTemplate;
            email = email.replace('[PHONE_NO]', $(this).attr('value'));
            emailEl.attr('value', email);
          });
          sm4.siteotpverifier.signup.toogleElementsListner[params.formKey] = function (form, reset) {
            emailEl = form.find('#' + params.emailName);
            phoneEl = form.find('#phoneno');
            var target = form.find('#signup_otp_type');
            target = target[0] ? target[0] : target;
            $(form.find('.' + params.elementClass + ' .siteotp_choice')).addClass('dnone');
            if ($(target).attr('value') === 'phone') {
              phoneEl.closest('.form-wrapper').removeClass('dnone');
              emailEl.closest('.form-wrapper').addClass('dnone');
              form.find('.siteotp_phone_choice').addClass('dnone');
              form.find('.siteotp_email_choice').removeClass('dnone');
            } else {
              emailEl.closest('.form-wrapper').removeClass('dnone');
              phoneEl.closest('.form-wrapper').addClass('dnone');
              form.find('.siteotp_email_choice').addClass('dnone');
              form.find('.siteotp_phone_choice').removeClass('dnone');
            }
            if (reset) {
              phoneEl.attr('value', '');
              emailEl.attr('value', '');
            }
          }.bind(this);
          sm4.siteotpverifier.signup.toogleElementsListner[params.formKey](emailEl.closest('.global_form'), false);
        }
      },
      toggoleElementsClickHandler: function (el) {
        var form = $(el).closest('.global_form');
        var target = form.find('#signup_otp_type');
        if ($(target).attr('value') === 'email') {
          $(target).attr('value', 'phone');
        } else {
          $(target).attr('value', 'email');
        }
        var formKey = form.attr('data-form-key');
        sm4.siteotpverifier.signup.toogleElementsListner[formKey](form, true);
      }
    }
  }

})(); // END NAMESPACE