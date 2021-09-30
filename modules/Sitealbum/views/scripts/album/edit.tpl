<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<?php $cateDependencyArray = Engine_Api::_()->getDbTable('categories', 'sitealbum')->getCatDependancyArray(); ?>
<?php
echo $this->form->render();
?>

<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array()); ?>

<script>
  var category_id = '<?php echo $this->category_id ?>';
  var cateDependencyArray = '<?php echo json_encode($cateDependencyArray); ?>';
  if (category_id != '') {
    var subcategory_id = '<?php echo $this->subcategory_id; ?>';
    subcategories(category_id, subcategory_id);
  }

  var subcategories = function(category_id, subcategory_id)
  {
    if (cateDependencyArray.indexOf(category_id) == -1 || category_id == 0) {
      if ($('subcategory_id-wrapper'))
        $('subcategory_id-wrapper').style.display = 'none';
      if ($('subcategory_id-label'))
        $('subcategory_id-label').style.display = 'none';
      return;
    }

    if ($('subcategory_backgroundimage'))
      $('subcategory_backgroundimage').style.display = 'inline-block';
    if ($('subcategory_id'))
      $('subcategory_id').style.display = 'none';
    if ($('subcategory_id-label'))
      $('subcategory_id-label').style.display = 'none';

    var url = '<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'index', 'action' => 'sub-category'), "default", true); ?>';

    if ($('subcategory_backgroundimage'))
      $('subcategory_backgroundimage').innerHTML = '<div class="form-label"></div><div class="form-element"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif" alt="" /></div>';

    en4.core.request.send(new Request.JSON({
      url: url,
      data: {
        format: 'json',
        category_id_temp: category_id
      },
      onSuccess: function(responseJSON) {
        $('subcategory_backgroundimage').style.display = 'none';
        clear('subcategory_id');
        var subcatss = responseJSON.subcats;

        addOption($('subcategory_id'), " ", '0');
        for (i = 0; i < subcatss.length; i++) {
          addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
          $('subcategory_id').value = subcategory_id;
        }

        if (category_id == 0) {
          clear('subcategory_id');
          $('subcategory_id').style.display = 'none';
          if ($('subcategory_id-label'))
            $('subcategory_id-label').style.display = 'none';
        }
      }
    }));
  };

  function clear(ddName)
  {
    for (var i = (document.getElementById(ddName).options.length - 1); i >= 0; i--)
    {
      document.getElementById(ddName).options[ i ] = null;
    }
  }

  function addOption(selectbox, text, value)
  {
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;

    if (optn.text != '' && optn.value != '') {
      $('subcategory_id').style.display = 'inline-block';
      if ($('subcategory_id-wrapper'))
        $('subcategory_id-wrapper').style.display = 'inline-block';
      if ($('subcategory_id-label'))
        $('subcategory_id-label').style.display = 'inline-block';
      selectbox.options.add(optn);
    } else {
      $('subcategory_id').style.display = 'none';
      if ($('subcategory_id-wrapper'))
        $('subcategory_id-wrapper').style.display = 'none';
      if ($('subcategory_id-label'))
        $('subcategory_id-label').style.display = 'none';
      selectbox.options.add(optn);
    }
  }
</script>
<script>

  window.addEvent('domready', function() {
<?php if ($this->profileType): ?>
      $('<?php echo '0_0_' . $this->defaultProfileId ?>').value = <?php echo $this->profileType ?>;
      changeFields($('<?php echo '0_0_' . $this->defaultProfileId ?>'));
<?php endif; ?>
  });

  var prefieldForm = function() {
<?php
$defaultProfileId = "0_0_" . $this->defaultProfileId;
foreach ($this->form->getSubForms() as $subForm) {
  foreach ($subForm->getElements() as $element) {

    $elementGetName = $element->getName();
    $elementGetValue = $element->getValue();
    $elementGetType = $element->getType();

    if ($elementGetName != $defaultProfileId && $elementGetName != '' && $elementGetName != null && $elementGetValue != '' && $elementGetValue != null) {

      if (!is_array($elementGetValue) && $elementGetType == 'Engine_Form_Element_Radio') {
        ?>
            $('<?php echo $elementGetName . "-" . $elementGetValue ?>').checked = 1;
      <?php } elseif (!is_array($elementGetValue) && $elementGetType == 'Engine_Form_Element_Checkbox') { ?>
            $('<?php echo $elementGetName ?>').checked = <?php echo $elementGetValue ?>;
        <?php
      } elseif (is_array($elementGetValue) && ($elementGetType == 'Engine_Form_Element_MultiCheckbox' || $elementGetType == 'Fields_Form_Element_Ethnicity' || $elementGetType == 'Fields_Form_Element_LookingFor' || $elementGetType == 'Fields_Form_Element_PartnerGender')) {
        foreach ($elementGetValue as $key => $value) {
          ?>
              $('<?php echo $elementGetName . "-" . $value ?>').checked = 1;
          <?php
        }
      } elseif (is_array($elementGetValue) && $elementGetType == 'Engine_Form_Element_Multiselect') {
        foreach ($elementGetValue as $key => $value) {
          $key_temp = array_search($value, array_keys($element->options));
          if ($key !== FALSE) {
            ?>
                $('<?php echo $elementGetName ?>').options['<?php echo $key_temp ?>'].selected = 1;
            <?php
          }
        }
      } elseif (!is_array($elementGetValue) && ($elementGetType == 'Engine_Form_Element_Text' || $elementGetType == 'Engine_Form_Element_Textarea' || $elementGetType == 'Fields_Form_Element_AboutMe' || $elementGetType == 'Fields_Form_Element_Aim' || $elementGetType == 'Fields_Form_Element_City' || $elementGetType == 'Fields_Form_Element_Facebook' || $elementGetType == 'Fields_Form_Element_FirstName' || $elementGetType == 'Fields_Form_Element_Interests' || $elementGetType == 'Fields_Form_Element_LastName' || $elementGetType == 'Fields_Form_Element_Location' || $elementGetType == 'Fields_Form_Element_Twitter' || $elementGetType == 'Fields_Form_Element_Website' || $elementGetType == 'Fields_Form_Element_ZipCode')) {
        ?>
            $('<?php echo $elementGetName ?>').value = "<?php echo $this->string()->escapeJavascript($elementGetValue, false) ?>";
      <?php } elseif (!is_array($elementGetValue) && $elementGetType != 'Engine_Form_Element_Date' && $elementGetType != 'Fields_Form_Element_Birthdate' && $elementGetType != 'Engine_Form_Element_Heading') { ?>
            $('<?php echo $elementGetName ?>').value = "<?php echo $this->string()->escapeJavascript($elementGetValue, false) ?>";
        <?php
      }
    }
  }
}
?>
  };
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.tags.enabled', 1)): ?>
  en4.core.runonce.add(function()
  {
    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'index', 'action' => 'tag-suggest', 'resourceType' => 'album'), 'default', true) ?>', {
      'postVar': 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'customChoices': true,
      'filterSubset': true, 'multiple': true,
      'injectChoice': function(token) {
        var choice = new Element('li', {'class': 'autocompleter-choices', 'value': token.label, 'id': token.id});
        new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
  });
<?php endif; ?>

  var getProfileType = function(category_id) {
    var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sitealbum')->getMapping(array('category_id', 'profile_type'))); ?>;
    for (i = 0; i < mapping.length; i++) {
      if (mapping[i].category_id == category_id)
        return mapping[i].profile_type;
    }
    return 0;
  }
  en4.core.runonce.add(function() {
    var defaultProfileId = '<?php echo '0_0_' . $this->defaultProfileId ?>' + '-wrapper';
    if ($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') {
      $(defaultProfileId).setStyle('display', 'none');
    }
  });
</script>
