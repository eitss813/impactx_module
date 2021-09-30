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
<?php include_once APPLICATION_PATH . '/application/modules/Sitemember/views/scripts/_browseUsersSM.tpl'; ?>

<?php if(!$this->autoContentLoad) :?>
<?php if (empty($this->is_ajax_load)): ?>
  <script type="text/javascript">
    var requestParams = $.extend(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'});
    var params = {  
      'detactLocation': <?php echo $this->detactLocation; ?>,
      'responseContainer': 'layout_sitemember_browse_members_sitemember',
      'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
      requestParams: requestParams
    };

    sm4.core.locationBased.startReq(params);
  </script> 
<?php endif; ?>

  
<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
    'topLevelId' => (int) @$this->topLevelId,
    'topLevelValue' => (int) @$this->topLevelValue
))
?>
<script type="text/javascript">
  var url = '<?php echo $this->url() ?>';
  var requestActive = false;
  var browseContainer, formElement, page, totalUsers, userCount, currentSearchParams;

  sm4.core.runonce.add(function() {

    $(window).bind('onChangeFields', function() {
      var firstSep = $('li.browse-separator-wrapper');
      var lastSep;
      var nextEl = firstSep;
      var allHidden = true;
      do {
        nextEl = nextEl.next();
        if( nextEl.attr('class') == 'browse-separator-wrapper' ) {
          lastSep = nextEl;
          nextEl = false;
        } else {
          allHidden = allHidden && ( nextEl.css('display') == 'none' );
        }
      } while( nextEl );
      if( lastSep ) {
        lastSep.css('display', (allHidden ? 'none' : ''));
      }
    });

  });
</script>
<?php endif;?>