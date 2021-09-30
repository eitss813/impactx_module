<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndynamicform
 * @author     YouNet Company
 */
?>
<?php echo $this->edit_form->setAttribs(array('class' => 'global_form_popup', 'style' => 'width: 768px'))->render($this) ?>
<?php if( @$this->closeSmoothbox ): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>
<script type="text/javascript">
    var heightFormURL = '410px';
    var heightFormText = '686px';
    var iframe = $$(parent.document.body).getElements('#TB_iframeContent')[0];

    window.addEvent('domready', function() {
        <?php if (strcmp($this -> confirmation -> type, 'text')): ?>
            confirmationURL();
            // Because mce generate after. So in this statement the mce-container has not yet generated
            setTimeout(function () {
                $$('div.mce-container').hide();
                iframe.setStyle('height', heightFormURL);
            }, 100);
        <?php else: ?>
            confirmationTEXT();
        <?php endif; ?>
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
    }

    function confirmationTEXT() {
        iframe.setStyle('height', heightFormText);
        $('confirmation_url-wrapper').setStyle('display', 'none');
        $$('div.mce-container').show();
    }
</script>