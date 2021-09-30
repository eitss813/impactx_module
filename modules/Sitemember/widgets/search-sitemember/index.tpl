<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
  $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
  $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>

<script type="text/javascript">
  var pageAction = function(page) { 
    $('page').value = page;
    $('filter_form').submit();
  }

  var searchSitemembers = function() { 
    
    var elements = document.getElementById("filter_form").elements;
    var obj ={ };

    for(var i = 0 ; i < elements.length ; i++) {
      var item = elements.item(i);
      if(item.type =="checkbox") {
        if( item.checked ) {
          // If item is singele checkbox the item.name will not have []
          if( item.name.substr( item.name.length - 2,2 ) == '[]' ) {
            var getItemName =item.name.replace('[]',''); 
            if( !obj[getItemName] ) {
              obj[getItemName] = [ ];
            }
            obj[getItemName].push( item.value ); 
          } else {
            obj[item.name] = item.value;
          }
        }
      } else if(item.type =="radio") {
          if(item.checked)
            obj[item.name] = item.value;
      } else {
          obj[item.name] = item.value;
        }
      }
    
    <?php  if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.ajaxify.search.enable',1)): ?>
        if (Browser.Engine.trident) {
             document.getElementById('filter_form').submit();
        } else {
             $('filter_form').submit();
        } 
        return ;
    <?php else: ?>
    $("search_background-image").style.display="inline-block";
    <?php endif; ?>
    var viewType = 2;
          if ($('grid_view')) {
            if ($('grid_view').style.display == 'block')
              viewType = 0;
          }
          if ($('image_view')) {
            if ($('image_view').style.display == 'block')
              viewType = 1;
          }
          if ($('pinboard_view')) {
            if ($('pinboard_view').style.display == 'block')
              viewType = 3;
          }

    
    var params = { 
                    requestParams: <?php echo $this->browse_params ?>,
                    responseContainer: $('dynamic_app_info_sitemember_' +<?php echo $this->content_id ?>)
                 };
    
   var data = $merge(params.requestParams,
                  obj
                );
   var paramsData = $merge(data,{
            format: 'html',
             is_ajax: true,
             loaded_by_ajax: true,
             content_id: '<?php echo $this->content_id ?>',
            });
       new Request.HTML({
            method: 'get',
            url: en4.core.baseUrl + 'widget/index/mod/sitemember/name/browse-members-sitemember',
            data:paramsData,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                var tempResults = Elements.from(responseHTML).getElement('#dynamic_app_info_sitemember_<?php echo $this->content_id ?>');
                $('dynamic_app_info_sitemember_<?php echo $this->content_id ?>').innerHTML = tempResults && tempResults[0] ? tempResults[0].get('html') : responseHTML;
                $("search_background-image").style.display="none";
                <?php if($this->map_view): ?>
                  if(typeof initialize === 'function') {
                    initialize();
                  }
                <?php endif ?>
                en4.core.runonce.trigger();
                Smoothbox.bind($('dynamic_app_info_sitemember_<?php echo $this->content_id ?>'));
           } 
        }).send();
      }  
  

  en4.core.runonce.add(function() {
    $$('#filter_form input[type=text]').each(function(f) {
      if (f.value == '' && f.id.match(/\min$/)) {
        new OverText(f, {'textOverride': 'min', 'element': 'span'});
      }
      if (f.value == '' && f.id.match(/\max$/)) {
        new OverText(f, {'textOverride': 'max', 'element': 'span'});
      }
    });
    
    if($('profile_type') && $('profile_type').length == 1) {
      $('profile_type-label').style.display = "none";
      $('profile_type').style.display = 'none';   
    }
    
  });

  window.addEvent('onChangeFields', function() {
    var firstSep = $$('li.browse-separator-wrapper')[0];
    var lastSep;
    var nextEl = firstSep;
    var allHidden = true;
    do {
      nextEl = nextEl.getNext();
      
      if(!nextEl) {
            break;
        }
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
  <div class="seaocore_searchform_criteria seaocore_search_horizontal <?php
  if ($this->whatWhereWithinmile) {
    echo "seaocore_searchform_criteria_advanced";
  }
  ?>">
         <?php echo $this->form->render($this); ?>

  
<?php else: ?>
  <div class="seaocore_searchform_criteria">
    <?php echo $this->form->render($this); ?>
 
<?php endif; ?>
<span id="search_background-image" style="display: none">
  <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif" />
</span>
      </div>
<script type="text/javascript">

  var viewType = '<?php echo $this->viewType; ?>';
  var whatWhereWithinmile = <?php echo $this->whatWhereWithinmile; ?>;
  var profile_type = '<?php echo isset( $_GET['profile_type'] ) ? $_GET['profile_type'] : '' ?>';
<?php if (isset($_GET['search']) || isset($_GET['location'])): ?>
    var advancedSearch = 0;
<?php else: ?>
    var advancedSearch = <?php echo $this->advancedSearch; ?>;
<?php endif; ?>

  if (viewType == 'horizontal' && whatWhereWithinmile == 1) {

    function advancedSearchLists(showFields, domeReady) {

      if(profile_type && domeReady) {
        showFields = 1;
      }

      if (viewType == 'horizontal' && whatWhereWithinmile == 1) {
        var fieldElements = new Array('profile_type', 'search', 'location', 'locationmiles', 'done');
      } else {
       var fieldElements = new Array('sitemember_street', 'sitemember_city', 'sitemember_state', 'sitemember_country', 'orderby', 'show', 'has_photo', 'is_online', 'network_id', 'level_id', 'profile_type', 'search', 'location', 'locationmiles', 'done');
      }
      
      if (viewType == 'horizontal' && whatWhereWithinmile == 1 && advancedSearch == 1) {
       var fieldElements = new Array('sitemember_street', 'sitemember_city', 'sitemember_state', 'sitemember_country', 'orderby', 'show', 'has_photo', 'is_online', 'network_id', 'level_id', 'profile_type', 'search', 'location', 'locationmiles', 'done');
      }
      
      var fieldsStatus = 'none';
      if (showFields == 1) {
        fieldsStatus = 'block';
      }

      $('filter_form').getElement('ul').getElements('li').each(function(multiEl){ 
      for (i = 0; i < fieldElements.length; i++) {
        if (multiEl.getElementById(fieldElements[i]) != null) {
          return;
        }
      }
      if (showFields == 1) {
          multiEl.setStyle("display", "inline-block");
        } else {
             if(multiEl.style.display == 'none')
                multiEl.setStyle("display", "inline-block");
              else
                multiEl.setStyle("display", "none");
        }
      }); 
      
      var profileType = $('filter_form').getElementById('profile_type');
      if(profileType && profileType.length > 1) {
        //profileType.value = '';
        changeFields(profileType);
      }
//      for (i = 0; i < fieldElements.length; i++) {
//        if ($(fieldElements[i] + '-label')) {
//          if (domeReady == 1) {
//            $(fieldElements[i] + '-label').getParent().style.display = fieldsStatus;
////            setTimeout(function () {
////              $('profile_type').value = '1';
////            changeFields($('profile_type'));
////            }, '500');
//          
//          }
//          else {
//            $(fieldElements[i] + '-label').getParent().toggle();
//            $('profile_type').value = '';
//            changeFields($('profile_type'));
//          }
//        }
//      }

    };
    
    if (viewType == 'horizontal' && whatWhereWithinmile == 1) {
      en4.core.runonce.add(function() {
        advancedSearchLists(advancedSearch, 1);
      });
    } else {
      advancedSearchLists(advancedSearch, 1);
    }
  }

  var module = '<?php echo Zend_Controller_Front::getInstance()->getRequest()->getModuleName()?>';
  
  var globalContentElement = en4.seaocore.getDomElements('content');
  if (module != 'siteadvsearch' && $(globalContentElement) && $(globalContentElement).getElement('.browsesitemembers_criteria')) {
    $(globalContentElement).getElement('.browsesitemembers_criteria').addEvent('keypress', function(e) {
      if (e.key != 'enter')
        return;
      searchSitemembers();
    });
  }

  en4.core.runonce.add(function()
  {
    var contentAutocomplete = new Autocompleter.Request.JSON('search', '<?php echo $this->url(array('module' => 'sitemember', 'controller' => 'location', 'action' => 'getmember'), 'default', true) ?>', {
      'postVar': 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'seaocore-autosuggest tag-autosuggest',
      'customChoices': true,
      'filterSubset': true,
      'multiple': false,
      'injectChoice': function(token) {
        var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id': token.label});
        new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice1'}).inject(choice);
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      },
    });

    contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
      document.getElementById('user_id').value = selected.retrieve('autocompleteChoice').id;
    });

  });

  en4.core.runonce.add(function() {
    if ($('location')) {
      var params = {
        'detactLocation': <?php echo $this->locationDetection; ?>,
        'fieldName': 'location',
        'noSendReq': 1,
        'locationmilesFieldName': 'locationmiles',
        'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
        'reloadPage': 1,
      };
      en4.seaocore.locationBased.startReq(params);
    }
      locationAutoSuggest('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities'); ?>', 'location', 'sitemember_city');
      if($("done")) {
             $("done").getParent().getParent().appendChild($("search_background-image"));
             $("done").getParent().setStyle('clear','both');
      }
  });

  



</script>
<div id="userlocation_location_map_none" style="display: none;"></div>