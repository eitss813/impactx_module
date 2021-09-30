<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: compose.tpl 10224 2014-05-15 18:45:45Z lucas $
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

<?php
    $this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
?>

<script type="text/javascript">

    var toValueIndex = "";

    function removeFromToValue(id) {
        // code to change the values in the hidden field to have updated values
        // when recipients are removed.
        var toValues = $('toValues').value;
        var toValueArray = toValues.split(",");

        var checkMulti = id.search(/,/);

        // check if we are removing multiple recipients
        if (checkMulti!=-1){
            var recipientsArray = id.split(",");
            for (var i = 0; i < recipientsArray.length; i++){
                removeToValue(recipientsArray[i], toValueArray);
            }
        }
        else{
            removeToValue(id, toValueArray);
        }

        // hide the wrapper for usernames if it is empty
        if ($('toValues').value==""){
            $('toValues-wrapper').setStyle('height', '0');
        }

        $('to').disabled = false;
    }

    function removeToValue(id, toValueArray){
        for (var i = 0; i < toValueArray.length; i++){
            if (toValueArray[i]==id) toValueIndex =i;
        }

        toValueArray.splice(toValueIndex, 1);
        toValueIndex = "";
        $('toValues').value = toValueArray.join();
    }

    en4.core.runonce.add(function() {
    <?php  if (count($this->selectedRecipients) > 0) : ?>
    <?php foreach ($this->selectedRecipients as $item) : ?>
        var id = "<?php echo $item['id']; ?>";
        var type = "<?php echo $item['type']; ?>";
        var title = "<?php echo $item['label']; ?>";

        var guid = "<?php echo $item['guid']; ?>"

        // POPULATED
        var myElement = new Element("span", {
            'id' : 'tospan' + id,
            'class' : 'tag tag_' + type,
            'html' :  `${title} <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue("${id}");'>x</a>`
        });

        $('to-element').appendChild(myElement);
        $('to-wrapper').setStyle('height', 'auto');
        $('to').setStyle('display', 'none');
        $('toValues-wrapper').setStyle('display', 'none');

    <?php endforeach; ?>
    <?php endif; ?>
    });
</script>


<script type="text/javascript">
    var composeInstance;
    en4.core.runonce.add(function() {
        var tel = new Element('div', {
            'id' : 'compose-tray',
            'styles' : {
                'display' : 'none'
            }
        }).inject($('submit'), 'before');

        var mel = new Element('div', {
            'id' : 'compose-menu'
        }).inject($('submit'), 'after');

        // @todo integrate this into the composer
        if ( '<?php
            $id = Engine_Api::_()->user()->getViewer()->level_id;
        echo Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('messages', $id, 'editor');
            ?>' == 'plaintext' ) {
        if( !Browser.Engine.trident && !DetectMobileQuick() && !DetectIpad() ) {
            composeInstance = new Composer('body', {
                overText : false,
                menuElement : mel,
                trayElement: tel,
                baseHref : '<?php echo $this->baseUrl() ?>',
                hideSubmitOnBlur : false,
                allowEmptyWithAttachment : false,
                submitElement: 'submit',
                type: 'message'
            });
        }
    }
    });
</script>

<?php foreach( $this->composePartials as $partial ): ?>
<?php echo $this->partial($partial[0], $partial[1]) ?>
<?php endforeach; ?>

<?php echo $this->form->render($this) ?>

