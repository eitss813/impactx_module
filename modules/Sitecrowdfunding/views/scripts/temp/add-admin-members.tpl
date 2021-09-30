<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: invite-members.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepagemember/externals/styles/style_sitepagemember.css'); ?>
<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">
    en4.core.runonce.add(function () {
        new Autocompleter.Request.JSON('user_ids', '<?php echo $this->url(array('controller' => 'temp', 'action' => 'get-admin-members', 'project_id' => $this->project_id), 'sitecrowdfunding_create_temp', true) ?>', {
            'postVar': 'text',
            'postData': false,
            'minLength': 1,
            'delay': 250,
            'selectMode': 'pick',
            'element': 'toValues',
            'autocompleteType': 'message',
            'multiple': false,
            'className': 'seaocore-autosuggest tag-autosuggest',
            'filterSubset': true,
            'tokenFormat': 'object',
            'tokenValueKey': 'label',
            'injectChoice': function (token) {
                //if(token.type == 'sitepage'){
                var choice = new Element('li', {
                    'class': 'autocompleter-choices',
                    'html': token.photo,
                    'id': token.label
                });
                new Element('div', {
                    'html': this.markQueryValue(token.label),
                    'class': 'autocompleter-choice'
                }).inject(choice);
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            },
            onCommand: function (e) {
                //this.options.postData = { 'couponId' : $('coupon_id').value };
            },
            onPush: function () {
                if ($('toValues-wrapper')) {
                    $('toValues-wrapper').style.display = 'block';
                }
            }
        });
        new Composer.OverText($('user_ids'), {
            'textOverride': '<?php echo $this->translate('Start typing...') ?>',
            'element': 'label',
            'isPlainText': true,
            'positionOptions': {
                position: (en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft'),
                edge: (en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft'),
                offset: {
                    x: (en4.orientation == 'rtl' ? -4 : 4),
                    y: 2
                }
            }
        });
    });


</script>
<div class="global_form_popup sitepage_add_members_popup">
    <div class="align-center">
        <?php echo $this->form->setAttrib('class', ' ')->render($this) ?>
    </div>
</div>