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
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->prependStylesheet($baseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css');
?>
<div class="sitemember_form_quick_search">
  <?php echo $this->form->setAttrib('class', 'sitemember-search-box')->render($this) ?>
</div>	

<?php
$this->headScript()
        ->appendFile($baseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<?php
  $language = $_COOKIE['en4_language'];
  $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
  $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<script type="text/javascript">

  var doSearching = function(searchboxcategory_id) {
    $('searchBox').submit();
  }

  en4.core.runonce.add(function()
  {
    var item_count = 0;
    var contentAutocomplete = new Autocompleter.Request.JSON('sitemember_searchbox', '<?php echo $this->url(array('module' => 'sitemember', 'controller' => 'location', 'action' => 'get-search-members'), 'default', true) ?>', {
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
          if (token.sitemember_url != 'seeMoreLink') {
            var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id': token.label, 'sitemember_url': token.sitemember_url, onclick: 'javascript:getPageResults("' + token.sitemember_url + '")'});
            new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
          if (token.sitemember_url == 'seeMoreLink') {
            var search = $('sitemember_searchbox').value;
            var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': '', 'id': 'stopevent', 'sitemember_url': ''});
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
            var url = selected.retrieve('autocompleteChoice').sitemember_url;
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

  if ($('locationSearch')) {
    var locationSearchField = <?php echo isset($_GET['locationSearch']) ? 1 : 0; ?>;
    locationAutoSuggest('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities'); ?>', 'locationSearch', '');

    if (!locationSearchField) {

      var params = {
        'detactLocation': <?php echo $this->locationDetection; ?>,
        'fieldName': 'locationSearch',
        'noSendReq': 1,
        'locationmilesFieldName': 'locationmilesSearch',
        'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
        'reloadPage': 1,
      };

      en4.seaocore.locationBased.startReq(params);
    }
  }

  function Seemore() {
    $('stopevent').removeEvents('click');
    var url = '<?php echo $this->url(array('action' => 'userby-locations'), "sitemember_userbylocation", true); ?>';
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
</script>