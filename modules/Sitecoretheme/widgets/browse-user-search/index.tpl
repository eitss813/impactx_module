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
 ?>
<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
  'topLevelId' => (int) $this->topLevelId,
  'topLevelValue' => (int) $this->topLevelValue
))
?>

<?php
echo $this->form
  ->setAction($this->url(array(), 'user_general', true))
  ->render($this);
?>

<script type="text/javascript">
  en4.core.runonce.add(function () {
    window.addEvent('onChangeFields', function () {
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
  });
</script>