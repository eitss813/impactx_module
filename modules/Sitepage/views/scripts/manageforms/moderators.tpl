<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndynamicform
 * @author     YouNet Company
 */
?>
<?php
$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Yndynamicform/externals/scripts/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Yndynamicform/externals/scripts/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Yndynamicform/externals/scripts/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Yndynamicform/externals/scripts/autocompleter/Autocompleter.Request.js');
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>
<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">
        <?php // include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
            <?php echo $this->
            partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array(
            'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Moderators', 'sectionDescription' => '')); ?>
            <div class="sitepage_edit_content">

                <!--
                                   <div class="headline">
                           <h2>
                               <?php echo $this->translate('Dynamic Form Plugin');?>
                           </h2>
                           <div class="tabs">
                               <?php
                               // Render the menu
                               echo $this->navigation()->menu()->setContainer($this->navigation)->render();
                               ?>
                           </div>
                       </div>
                              <h3><?php echo $this->form->getTitle() ?> &#47; Edit Form</h3> -->
                        <?php if (!empty($this -> message)): ?>
                            <ul class="form-notices"><li><?php echo $this -> message ?></li></ul>
                        <?php endif; ?>
                        <div class="yndform_edit_form clearfix">
                            <?php // echo $this->partial('_menuSettings.tpl', 'yndynamicform', array('form' => $this->form, 'moderators' => 'yndform_active')); ?>
                            <form enctype="application/x-www-form-urlencoded" action="<?php echo ($this->url()) ?>" class="global_form" method="post">
                            <div class="yndform_confirmation_col_right">
                                <div class="yndform_confirmation_col_right_desc yndform_moderator"><?php echo $this->translate("Please identify the moderators who can view submitted entries of this form on your behalf.")?></div>
                                <div class="form-elements">
                                    <div id="toValues-wrapper" class="form-wrapper" style="display: block;">
                                     <!--
                                        <div id="toValues-label" class="form-label">
                                            <label for="toValues" class="optional">Moderators:</label>

                                        </div>
                                        -->
                                        <div class="yndform_moderators_suggest">
                                            <div id="toValues-element" class="form-element">
                                                <input type="hidden" name="toValues" value="" id="toValues">
                                            </div>
                                            <input type="text" name="moderator" placeholder="Start typing here..." id="moderator" autocomplete="off">
                                         </div>

                                    </div>
                                    <br>
                                    <div>
                                        <button type='submit'><?php echo $this->translate("Save") ?></button>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>


            </div>

        </div>
    </div>
</div>


<script type="text/javascript">
    // Populate data
    var maxRecipients = <?php echo sprintf("%d", $this->maxRecipients) ?> || 10;
    var objData = new Object();
    <?php $count = 0 ?>
    <?php if(!empty($this->toObjects)): ?>
        // Pass all data to toValues
        <?php foreach ($this->toObjects as $toObject): ?>
            objData[<?php echo $count ?>] = {
                id : <?php echo sprintf("%d", $toObject->getIdentity()) ?>,
                type : '<?php echo $toObject->getType() ?>',
                guid : '<?php echo $toObject->getGuid() ?>',
                title : '<?php echo $this->string()->escapeJavascript($toObject->getTitle()) ?>'
            };
            <?php $count++ ?>
        <?php endforeach; ?>
    <?php endif; ?>


    function removeFromToValue(id) {
        // code to change the values in the hidden field to have updated values
        // when recipients are removed.
        var toValues = $('toValues').value;
        var toValueArray = toValues.split(",");
        var toValueIndex = "";

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

        

        $('moderator').disabled = false;
    }

    function removeToValue(id, toValueArray){
        var toValueIndex = 0;
        for (var i = 0; i < toValueArray.length; i++){
            if (toValueArray[i]==id) toValueIndex =i;
        }

        toValueArray.splice(toValueIndex, 1);
        $('toValues').value = toValueArray.join();
    }


    en4.core.runonce.add(function() {

        new Autocompleter.Request.JSON('moderator', '<?php echo $this->url(array('module' => 'yndynamicform', 'controller' => 'notification', 'action' => 'suggest','message' => true), 'admin_default', true) ?>', {
            'minLength': 1,
            'delay' : 250,
            'selectMode': 'pick',
            'autocompleteType': 'message',
            'multiple': false,
            'className': 'message-autosuggest',
            'filterSubset' : true,
            'tokenFormat' : 'object',
            'tokenValueKey' : 'label',
            'injectChoice': function(token){
                if(token.type == 'user'){
                    var choice = new Element('li', {
                        'class': 'autocompleter-choices',
                        'html': token.photo,
                        'id':token.label
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
                        'id':token.label
                    });
                    new Element('div', {
                        'html': this.markQueryValue(token.label),
                        'class': 'autocompleter-choice'
                    }).inject(choice);
                    this.addChoiceEvents(choice).inject(this.choices);
                    choice.store('autocompleteChoice', token);
                }

            },
            onPush : function(){
                if( $('toValues').value.split(',').length >= maxRecipients ){
                    $('moderator').disabled = true;
                }
            }
        });

        <?php if (!empty($this -> toObjects)): ?>
        Object.each(objData, function (ele) {
            var hideLoc = 'toValues';
            var myElement = new Element("span");
            myElement.id = "tospan_"+ele.title+"_"+ele.id;
            myElement.innerHTML = ele.title+" <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+ele.id+"\", \""+hideLoc+"\");'><span class='ynicon yn-del cal'></span></a>";
            $('toValues-wrapper').setStyle('height', 'auto');

            document.getElementById('toValues-element').appendChild(myElement);
        });
        $('toValues').value = <?php echo "'".$this -> toValues."'" ?>;
        <?php endif; ?>
    });
</script>