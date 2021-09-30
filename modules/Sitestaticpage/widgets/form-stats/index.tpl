<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php echo $this->content; ?>

<script type="text/javascript">
  function editData(form_id, item_id) {

    var member_id = '<?php echo Engine_Api::_()->user()->getViewer()->getIdentity(); ?>';
    var url = en4.core.baseUrl + 'sitestaticpage/index/edit/item_id/' + item_id + '/member_id/' + member_id + '/form_id/' + form_id;
    Smoothbox.open(url);
  }

  function deleteData(form_id, item_id) {
    var member_id = '<?php echo Engine_Api::_()->user()->getViewer()->getIdentity(); ?>';
    var url = en4.core.baseUrl + 'sitestaticpage/index/delete/item_id/' + item_id + '/member_id/' + member_id + '/form_id/' + form_id;
    Smoothbox.open(url);
  }
</script>