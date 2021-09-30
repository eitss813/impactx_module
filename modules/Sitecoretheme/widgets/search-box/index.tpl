<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
  ?>
<?php if($this->isSiteadvsearchEnable): ?>
 <?php echo $this->content()->renderWidget("siteadvsearch.search-box", array("widgetName" => "advmenu_mini_menu",)); ?>
<i class="fa fa-close" id="close_search_icon" onclick="hideSearchBox()"></i>
<?php else: ?>
<form id="global_search_form" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="get">
    <input <?php if ($this->viewer()->getIdentity()): ?> style="width:<?php echo $this->searchbox_width; ?>px;" <?php endif; ?> type="text" class="text suggested" name="query" id="global_lanndingpage_search_field" placeholder="<?php echo $this->translate("Search here...") ?>" alt='<?php echo $this->translate('Search') ?>'> 
    <i class="fa fa-close" id="close_search_icon" onclick="hideSearchBox()"></i>
   <!-- <button type="button" onclick="this.form.submit();"></button> -->
</form>
<script>
	(function() {
		var requestURL = '<?php echo $this->url(array('module' => 'sitecoretheme', 'controller' => 'general', 'action' => 'get-search-content'), "default", true) ?>';
    if($('global_lanndingpage_search_field')) {
      contentAutocomplete = new Autocompleter.Request.JSON('global_lanndingpage_search_field', requestURL, {
        'postVar': 'text',
        'cache': false,
        'minLength': 1,
        'selectFirst': false,
        'selectMode': 'pick',
        'autocompleteType': 'tag',
        'className': 'tag-autosuggest adsearch-autosuggest adsearch-stoprequest',
        'maxChoices': 8,
        'indicatorClass': 'vertical-search-loading',
        'customChoices': true,
        'filterSubset': true,
        'multiple': false,
        'injectChoice': function (token) {
          if (typeof token.label != 'undefined') {
            var seeMoreText = '<?php echo $this->string()->escapeJavascript($this->translate('See more results for') . ' '); ?>';
            if (token.type == 'no_resuld_found') {
              var choice = new Element('li', {'class': 'autocompleter-choices', 'id': 'sitecoretheme_search_' + token.type});
              new Element('div', {'html': token.label, 'class': 'autocompleter-choicess'}).inject(choice);
              choice.inject(this.choices);
              choice.store('autocompleteChoice', token);
              return;
            }
            if (token.item_url != 'seeMoreLink') {
              var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'item_url': token.item_url, onclick: 'javascript: showSearchResultPage("' + token.item_url + '")'});
              var divEl = new Element('div', {
                'html': token.type ? this.options.markQueryValueCustom.call(this, (token.label)) : token.label,
                'class': 'autocompleter-choice'
              });

              new Element('div', {
                'html': token.type, //this.markQueryValue(token.type)  
                'class': 'seaocore_txt_light f_small'
              }).inject(divEl);

              divEl.inject(choice);
              new Element('input', {
                'type': 'hidden',
                'value': JSON.encode(token)
              }).inject(choice);
              this.addChoiceEvents(choice).inject(this.choices);
              choice.store('autocompleteChoice', token);
            }
            if (token.item_url == 'seeMoreLink') {
              var titleAjax1 = encodeURIComponent($('global_lanndingpage_search_field').value);
              var choice = new Element('li', {'class': 'autocompleter-choices', 'html': '', 'id': 'stopevent', 'item_url': ''});
              new Element('div', {'html': seeMoreText + '"' + titleAjax1 + '"', 'class': 'autocompleter-choicess', onclick: 'javascript:seeMoreSearchResults()'}).inject(choice);
              this.addChoiceEvents(choice).inject(this.choices);
              choice.store('autocompleteChoice', token);
            }
          }
        },
        markQueryValueCustom: function (str) {
          return (!this.options.markQuery || !this.queryValue) ? str
            : str.replace(new RegExp('(' + ((this.options.filterSubset) ? '' : '^') + this.queryValue.escapeRegExp() + ')', (this.options.filterCase) ? '' : 'i'), '<b>$1</b>');
        },
      });
    }
    function showSearchResultPage(url) {
      window.location.href = url;
    }
    function seeMoreSearchResults() {

      $('stopevent').removeEvents('click');
      var url = '<?php echo $this->url(array('controller' => 'search'), 'default', true); ?>' + '?query=' + encodeURIComponent($('global_lanndingpage_search_field').value) + '&type=' + 'all';
      window.location.href = url;
    }
    $('global_lanndingpage_search_field').addEvent('keydown', function (event) {
      if (event.key == 'enter') {
        $('sitecoretheme_fullsite_search').submit();
      }
    });
	})();
	</script>
<?php endif; ?>