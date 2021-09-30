<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: request-member.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  var pageWidgetRequestSend = function(action, group_id, notification_id)
  {
    var url;
    if( action == 'accept' )
    {
      url = '<?php echo $this->url(array('controller' => 'member', 'action' => 'accept'), 'sitepage_profilepagemember', true) ?>';
    }
    else if( action == 'reject' )
    {
      url = '<?php echo $this->url(array('controller' => 'member', 'action' => 'reject'), 'sitepage_profilepagemember', true) ?>';
    }
    else
    {
      return false;
    }

    (new Request.JSON({
      'url' : url,
      'data' : {
        'page_id' : group_id,
        'format' : 'json'
        //'token' : '<?php //echo $this->token() ?>'
      },
      'onSuccess' : function(responseJSON)
      {
        if( !responseJSON.status )
        {
          $('sitepagemember-widget-request-' + notification_id).innerHTML = responseJSON.error;
        }
        else
        {
          $('sitepagemember-widget-request-' + notification_id).innerHTML = responseJSON.message;
        }
      }
    })).send();
  }
</script>

<li id="sitepagemember-widget-request-<?php echo $this->notification->notification_id ?>">
  <?php echo $this->itemPhoto($this->notification->getObject(), 'thumb.icon') ?>
  <div>
    <div>
      <?php echo $this->translate('%1$s has invited you to the page %2$s', $this->htmlLink($this->notification->getSubject()->getHref(), $this->notification->getSubject()->getTitle()), $this->htmlLink($this->notification->getObject()->getHref(), $this->notification->getObject()->getTitle())); ?>
    </div>
    <div>
      <button type="submit" onclick='pageWidgetRequestSend("accept", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>)'>
        <?php echo $this->translate('Join Page');?>
      </button>
      <?php echo $this->translate('or');?>
      <a href="javascript:void(0);" onclick='pageWidgetRequestSend("reject", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>)'>
        <?php echo $this->translate('ignore request');?>
      </a>
    </div>
  </div>
</li>