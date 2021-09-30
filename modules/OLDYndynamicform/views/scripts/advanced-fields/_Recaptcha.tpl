<script src='https://www.google.com/recaptcha/api.js'></script>
<div id="<?php echo $this -> id ?>-wrapper" class="form-wrapper">
    <div id="<?php echo $this -> id ?>-label" class="form-label">
        <label>

        </label>
    </div>
    <div id="<?php echo $this -> id ?>-element" class="form-element">
        <?php $spamSettings = Engine_Api::_()->getApi('settings', 'core')->core_spam ?>
        <div id="g-recaptcha" class="g-recaptcha" data-sitekey="<?php echo $spamSettings['recaptchapublic'] ?>"></div>
    </div>
</div>