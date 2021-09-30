<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndynamicform
 * @author     YouNet Company
 */
?>
<div style="width: 768px">
    <?php echo $this->new_form->setAttribs(array('class' => 'global_form_popup', 'style' => 'width: 768px'))->render($this) ?>
</div>
<?php if( @$this->closeSmoothbox ): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>
<script type="text/javascript">
    var heightFormURL = '410px';
    var heightFormText = '686px';
    var iframe = $$(parent.document.body).getElements('#TB_iframeContent')[0];
    window.addEvent('domready', function () {
        switchConfirmationType($('type-text'));
        setTimeout(function () {
            iframe.setStyle('height', heightFormText);
        }, 100);
    });
    function switchConfirmationType(ele) {
        switch (ele.id) {
            case 'type-url':
                confirmationURL();
                break;
            case 'type-text':
            default:
                confirmationTEXT();
                break;
        }
    }

    function confirmationURL() {
        iframe.setStyle('height', heightFormURL);
        $('confirmation_url-wrapper').setStyle('display', 'block');
        $$('div.mce-container').hide();
        $('type-url').set('checked', 'checked');
    }

    function confirmationTEXT() {
        iframe.setStyle('height', heightFormText);
        $('confirmation_url-wrapper').setStyle('display', 'none');
        $$('div.mce-container').show();
        $('type-text').set('checked', 'checked');
    }
</script>