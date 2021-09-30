<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */
?>

<?php if ($this->viewer_id): ?>

  <script type="text/javascript">
    en4.core.runonce.add(function(){
      $$('#rsvp_options input[type=radio]').addEvent('click', function(){
        var option_id = this.get('value');
        $('event_radio_' + option_id).className = 'event_radio_loading';
        new Request.JSON({
            url: '<?php echo $this->url(array('module' => 'event', 'controller' => 'widget', 'action'=>'profile-rsvp', 'subject' => $this->subject()->getGuid()), 'default', true); ?>',
            method: 'post',
            data : {
              'format' : 'json',
              'option_id' : option_id,
              'subject' : en4.core.subject.guid
            },
            onComplete: function(responseJSON, responseText)
            {
            refreshEventStats();
            $('event_radio_' + option_id).className = 'event_radio';
              $$('#rsvp_options input').each(function(radio){
                if (radio.type == 'radio') {
                  radio.style.display = null;
                  radio.blur();
                }
              });
              if (responseJSON.error) {
                alert(responseJSON.error);
              } else {
                <?php if (!$this->canChangeVote): ?>
                  $$('.poll_radio input').set('disabled', true);
                <?php endif ?>
              }
            }
        }).send();
      });
    });

    var refreshEventStats = function() {
      en4.core.request.send(
        new Request.HTML({
          url : en4.core.baseUrl + 'widget/index/content_id/' +
            <?php echo sprintf('%d', $this->profileInfoContentId) ?>,
          data : {
            format : 'html',
            subject : en4.core.subject.guid,
          }
        }),
        { 'element' : $('event_stats').getParent() }
      );
    }
  </script>

  <h3>
    <?php echo $this->translate('Your RSVP');?>
  </h3>
  <form class="event_rsvp_form" action="<?php echo $this->url() ?>" method="post" onsubmit="return false;">
    <div class="events_rsvp" id="rsvp_options">
      <div class="event_radio" id="event_radio_2">
        <input id="rsvp_option_2" type="radio" class="rsvp_option" name="rsvp_options" <?php if ($this->rsvp == 2): ?>checked="true"<?php endif; ?> value="2" /><?php echo $this->translate('Attending');?>
      </div>
      <div class="event_radio" id="event_radio_1">
        <input id="rsvp_option_1" type="radio" class="rsvp_option" name="rsvp_options" <?php if ($this->rsvp == 1): ?>checked="true"<?php endif; ?> value="1" /><?php echo $this->translate('Maybe Attending');?>
      </div>
      <div class="event_radio" id="event_radio_0">
        <input id="rsvp_option_0" type="radio" class="rsvp_option" name="rsvp_options" <?php if ($this->rsvp == 0): ?>checked="true"<?php endif; ?> value="0" /><?php echo $this->translate('Not Attending');?>
      </div>
    </div>
  </form>

<?php endif; ?>
