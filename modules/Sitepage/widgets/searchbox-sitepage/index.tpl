<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<ul class="seaocore_sidebar_list">
	<li>
		<?php echo $this->form->setAttrib('class', 'sitepage-search-box')->render($this) ?>
	</li>
</ul>	

<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl .  'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    var item_count = 0;
    var contentAutocomplete = new Autocompleter.Request.JSON('title', '<?php echo $this->url(array('action' => 'get-search-pages','category_id' => $this->category_id), 'sitepage_general', true) ?>', {
      'postVar' : 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'searchbox_autosuggest',
      'customChoices' : true,
      'filterSubset' : true,
      'multiple' : false,
      'injectChoice': function(token) {      				
	      if(typeof token.label != 'undefined' ) {
          if (token.page_url != 'seeMoreLink') {
            var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id':token.label, 'page_url':token.page_url, onclick:'javascript:getPageResults("'+token.page_url+'")'});
            new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
          if(token.page_url == 'seeMoreLink') {
            var title = $('title').value;
            var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': '', 'id':'stopevent', 'page_url':''});
            new Element('div', {'html': 'See More Results for '+title ,'class': 'autocompleter-choicess', onclick:'javascript:Seemore()'}).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
         }
       }
    });

    contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
      window.addEvent('keyup', function(e) {
        if(e.key == 'enter') {
          if(selected.retrieve('autocompleteChoice') != 'null' ) {
            var url = selected.retrieve('autocompleteChoice').page_url;
            if (url == 'seeMoreLink') {
              Seemore();
            }
            else {
              window.location.href=url;
            }
          }
        }
      });      
    });
  });
  
  function Seemore() {
    $('stopevent').removeEvents('click');
    var url = '<?php echo $this->url(array('action' => 'index'), 'sitepage_general', true); ?>';
  	window.location.href= url + "?search=" + encodeURIComponent($('title').value);
  }

  function getPageResults(url) {
    if(url != 'null' ) {
      if (url == 'seeMoreLink') {
        Seemore();
      }
      else {
        window.location.href=url;
      }
    }
  }
</script>