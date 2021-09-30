<div class="siteotp_sms_cards">
  <div class="siteotp_sms_cards_content">
  <h2><?php echo $this->translate('Inbox') ?></h2>
  <div class="siteotp_sms">
    <?php if( empty($this->messages) ): ?>
      <div class="tip">
        <span><?php echo $this->translate('No received any messages.') ?></span>
      </div>
    <?php else: ?>

      <?php foreach( $this->messages as $message ): ?>
        <div>
          <div>
            <span class="siteotp_phone"><?php echo $message['phone'] ?></span>
            <span class="siteotp_time"><?php echo $message['time'] ?></span>
          </div>
          <p><?php echo nl2br($message['message'])?></p>
        </div>
      <?php endforeach; ?>

    <?php endif; ?>
  </div>
</div>
</div>
<!--<script type="text/javascript">
  setTimeout(function () {
    window.location.reload(false);
  }, 36000);
</script>-->