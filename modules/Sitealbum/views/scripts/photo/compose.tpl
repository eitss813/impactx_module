<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Sitealbum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: compose.tpl 10110 2013-10-31 02:04:11Z andres $
 * @author     John
 */
?>

<?php
if (APPLICATION_ENV == 'production')
  $this->headScript()
          ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.min.js');
else
  $this->headScript()
          ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
          ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
          ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
          ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">

  // Populate data
  var maxRecipients = <?php echo sprintf("%d", $this->maxRecipients) ?> || 10;
  var to = {
    id: false,
    type: false,
    guid: false,
    title: false
  };
  var isPopulated = false;

  en4.core.runonce.add(function() {
    if (!isPopulated) { // NOT POPULATED
      new Autocompleter.Request.JSON('to', '<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest'), 'default', true) ?>', {
        'minLength': 1,
        'delay': 250,
        'selectMode': 'pick',
        'autocompleteType': 'message',
        'multiple': false,
        'className': 'message-autosuggest',
        'filterSubset': true,
        'tokenFormat': 'object',
        'tokenValueKey': 'label',
        'injectChoice': function(token) {
          if (token.type == 'user') {
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
          }
          else {
            var choice = new Element('li', {
              'class': 'autocompleter-choices friendlist',
              'id': token.label
            });
            new Element('div', {
              'html': this.markQueryValue(token.label),
              'class': 'autocompleter-choice'
            }).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }

        },
        onPush: function() {
          if ($('toValues').value.split(',').length >= maxRecipients) {
            $('to').disabled = true;
          }
        }
      });

      new Composer.OverText($('to'), {
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

    }
  });
</script>

<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
?>

<script type="text/javascript">
  var composeInstance;
  en4.core.runonce.add(function() {
    if ('<?php
$id = Engine_Api::_()->user()->getViewer()->level_id;
echo Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('messages', $id, 'editor');
?>' == 'plaintext') {
      if (!Browser.Engine.trident && !DetectMobileQuick() && !DetectIpad()) {
        composeInstance = new Composer('body', {
          overText: false,
          baseHref: '<?php echo $this->baseUrl() ?>',
          hideSubmitOnBlur: false,
          allowEmptyWithAttachment: false,
          submitElement: 'submit',
          type: 'message'
        });
      }
    }
  });
</script>
<?php echo $this->form->render($this) ?>
<a href="javascript:void(0);" onclick="javascript:parent.Smoothbox.close();" class="popup_close fright"></a>

