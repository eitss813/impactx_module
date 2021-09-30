<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/core.js');
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>

<script type="text/javascript">
  var pageAction = function(page) {
    $('page').value = page;
    $('filter_form').submit();
  };

  var searchSitealbums = function() {

    var formElements = $('filter_form').getElements('li');
    formElements.each(function(el) {
      var field_style = el.style.display;
      if (field_style == 'none') {
        el.destroy();
      }
    });

    if (Browser.Engine.trident) {
      document.getElementById('filter_form').submit();
    } else {
      $('filter_form').submit();
    }
  };

  en4.core.runonce.add(function() {
    $$('#filter_form input[type=text]').each(function(f) {
      if (f.value == '' && f.id.match(/\min$/)) {
        new OverText(f, {'textOverride': 'min', 'element': 'span'});
        //f.set('class', 'integer_field_unselected');
      }
      if (f.value == '' && f.id.match(/\max$/)) {
        new OverText(f, {'textOverride': 'max', 'element': 'span'});
        //f.set('class', 'integer_field_unselected');
      }
    });
  });
  window.addEvent('onChangeFields', function() {
    var firstSep = $$('li.browse-separator-wrapper')[0];
    var lastSep;
    var nextEl = firstSep;
    var allHidden = true;
    do {
      nextEl = nextEl.getNext();
      if (nextEl.get('class') == 'browse-separator-wrapper') {
        lastSep = nextEl;
        nextEl = false;
      } else {
        allHidden = allHidden && (nextEl.getStyle('display') == 'none');
      }
    } while (nextEl);
    if (lastSep) {
      lastSep.setStyle('display', (allHidden ? 'none' : ''));
    }
  });

</script>

<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array()); ?>

<?php if ($this->viewType == 'horizontal'): ?>
  <div class="seaocore_searchform_criteria <?php
  if ($this->whatWhereWithinmile): echo "seaocore_searchform_criteria_advanced";
  endif;
  if ($this->viewType == 'horizontal'): echo " seaocore_search_horizontal";
  endif;
  ?>">
         <?php echo $this->form->render($this); ?>
  </div>
<?php else: ?>
  <div class="seaocore_searchform_criteria">
    <?php echo $this->form->render($this); ?>
  </div>
<?php endif; ?>

<script>
  window.addEvent('domready', function() {
    var search_category_id = '<?php echo $this->category_id ?>';
    if (search_category_id != 0) {
      var search_subcategory_id = '<?php echo $this->subcategory_id ?>';
      subcategories(search_category_id, search_subcategory_id, 1);
    }
  });

</script>

<script type="text/javascript">

  var viewType = '<?php echo $this->viewType; ?>';
  var whatWhereWithinmile = <?php echo $this->whatWhereWithinmile; ?>;

<?php if (isset($_GET['search']) || isset($_GET['location'])): ?>
    var advancedSearch = 1;
<?php else: ?>
    var advancedSearch = <?php echo $this->advancedSearch; ?>;
<?php endif; ?>

  if (viewType == 'horizontal' && whatWhereWithinmile == 1) {

    function advancedSearchLists(showFields, domeReady) {

      var fieldElements = new Array('album_street', 'album_city', 'album_state', 'album_country', 'orderby', 'category_id', 'view_view');
      var fieldsStatus = 'none';

      if (showFields == 1) {
        var fieldsStatus = 'block';
      }

      for (i = 0; i < fieldElements.length; i++) {
        if ($(fieldElements[i] + '-label')) {
          if (domeReady == 1) {
            $(fieldElements[i] + '-label').getParent().style.display = fieldsStatus;
          }
          else {
            $(fieldElements[i] + '-label').getParent().toggle();
          }
        }
        
        if((fieldElements[i] == 'subcategory_id') &&  ($('subcategory_id-wrapper')) && domeReady != 1 && $('category_id').value != 0) {
            $(fieldElements[i] + '-wrapper').toggle();
        }

        if((fieldElements[i] == 'subsubcategory_id') &&  ($('subsubcategory_id-wrapper')) && domeReady != 1 && $('subcategory_id').value != 0) {
            $(fieldElements[i] + '-wrapper').toggle();
        }        
        
      }
    }
    
            if (showFields == 1) {
                $("filter_form").getElements(".field_toggle").each(function(el){
                    if(el.getParent('li')) {
                         el.getParent('li').removeClass('dnone');
                    }
                 });
            }else{
                $("filter_form").getElements(".field_toggle").each(function(el){
                    if(el.getParent('li')) {
                        el.getParent('li').removeClass('dnone').addClass('dnone');
                    }
                 });
            }     
    
    advancedSearchLists(advancedSearch, 1);
  }

  var module = '<?php echo Zend_Controller_Front::getInstance()->getRequest()->getModuleName()?>';
  if (module != 'siteadvsearch') {
    var globalContentElement = en4.seaocore.getDomElements('content');
    $(globalContentElement).getElement('.browsesitealbums_criteria').addEvent('keypress', function(e) {
    if (e.key != 'enter')
    return;
    searchSitealbums();
    });
 }

  en4.core.runonce.add(function()
  {
    var item_count = 0;
    var contentAutocomplete = new Autocompleter.Request.JSON('search', '<?php echo $this->url(array('action' => 'get-search-albums'), "sitealbum_general", true) ?>', {
      'postVar': 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest seaocore-autosuggest',
      'customChoices': true,
      'filterSubset': true,
      'multiple': false,
      'injectChoice': function(token) {
        if (typeof token.label != 'undefined') {
          if (token.sitealbum_url != 'seeMoreLink') {
            var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id': token.label, 'sitealbum_url': token.sitealbum_url, onclick: 'javascript:getPageResults("' + token.sitealbum_url + '")'});
            new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
          if (token.sitealbum_url == 'seeMoreLink') {
            var search = $('search').value;
            var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': '', 'id': 'stopevent', 'sitealbum_url': ''});
            new Element('div', {'html': 'See More Results for ' + search, 'class': 'autocompleter-choicess', onclick: 'javascript:Seemore()'}).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
        }
      }
    });

    contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
      window.addEvent('keyup', function(e) {
        if (e.key == 'enter') {
          if (selected.retrieve('autocompleteChoice') != 'null') {
            var url = selected.retrieve('autocompleteChoice').sitealbum_url;
            if (url == 'seeMoreLink') {
              Seemore();
            }
            else {
              window.location.href = url;
            }
          }
        }
      });
    });
  });

  function Seemore() {
    $('stopevent').removeEvents('click');
    var url = '<?php echo $this->url(array('action' => 'browse'), "sitealbum_general", true); ?>';
    window.location.href = url + "?search=" + encodeURIComponent($('search').value);
  }

  function getPageResults(url) {
    if (url != 'null') {
      if (url == 'seeMoreLink') {
        Seemore();
      }
      else {
        window.location.href = url;
      }
    }
  }

  en4.core.runonce.add(function(){
    if ($('location')) {
      var params = {
        'detactLocation': <?php echo $this->locationDetection; ?>,
        'fieldName': 'location',
        'noSendReq': 1,
        'locationmilesFieldName': 'locationmiles',
        'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
        'reloadPage': 1
      };
      en4.seaocore.locationBased.startReq(params);
    }
    
    locationAutoSuggest('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities'); ?>', 'location', 'album_city');
  });

  var profile_type = 0;
  var previous_mapped_level = 0;
  var sitealbum_categories_slug = <?php echo json_encode($this->categories_slug); ?>;

  function showFields(cat_value, cat_level) {

    if (cat_level == 1 || (previous_mapped_level >= cat_level && previous_mapped_level != 1) || (profile_type == null || profile_type == '' || profile_type == 0)) {
      profile_type = getProfileType(cat_value);
      if (profile_type == 0) {
        profile_type = '';
      } else {
        previous_mapped_level = cat_level;
      }
      $('profile_type').value = profile_type;
      changeFields($('profile_type'));
    }
  }

  var getProfileType = function(category_id) {
    var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sitealbum')->getMapping(array('category_id', 'profile_type'))); ?>;
    for (i = 0; i < mapping.length; i++) {
      if (mapping[i].category_id == category_id)
        return mapping[i].profile_type;
    }
    return 0;
  }

  var subcategories = function(category_id, subcategory_id, domready)
  {
    if (domready == 0) {
      $('subcategory_id' + '-wrapper').style.display = 'none';
      clear('subcategory_id');
      $('subcategory_id').value = 0;
      $('categoryname').value = sitealbum_categories_slug[category_id];
    }

    if (category_id <= 0)
      return;

    var url = '<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'index', 'action' => 'sub-category', 'showAllCategories' => $this->showAllCategories), "default", true); ?>';

    en4.core.request.send(new Request.JSON({
      url: url,
      data: {
        format: 'json',
        category_id_temp: category_id,
      },
      onSuccess: function(responseJSON) {
        clear('subcategory_id');
        var subcatss = responseJSON.subcats;

        addOption($('subcategory_id'), " ", '0');
        for (i = 0; i < subcatss.length; i++) {
          addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
          $('subcategory_id').value = subcategory_id;
          sitealbum_categories_slug[subcatss[i]['category_id']] = subcatss[i]['category_slug'];
        }

        if (category_id == 0) {
          clear('subcategory_id');
          $('subcategory_id').style.display = 'none';
          if ($('subcategory_id-label'))
            $('subcategory_id-label').style.display = 'none';
        }

      }
    }), {'force': true});
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