<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: widget.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div style="padding: 10px;">

  <?php if( $this->form ): ?>

    <script type="text/javascript">
      window.addEvent('domready', function() {
        var params = parent.pullWidgetParams();
        var info = parent.pullWidgetTypeInfo();
        $H(params).each(function(value, key) {
          if( $type(value) == 'array' ) {
            value.each(function(svalue){
              if( $(key + '-' + svalue) ) {
                $(key + '-' + svalue).set('checked', true);
              }
            });
          } else if( $(key) ) {
            $(key).value = value;
          } else if( $(key + '-' + value) ) {
            $(key + '-' + value).set('checked', true);
          }
        });
        $$('.form-description').set('html', info.description);
      })
    </script>

    <?php echo $this->form->render($this) ?>

  <?php elseif( $this->values ): ?>

    <script type="text/javascript">
      parent.setWidgetParams(<?php echo Zend_Json::encode($this->values) ?>);
      parent.Smoothbox.close();
    </script>
  <?php else: ?>
    <?php echo $this->translate("Error: no values") ?>
  <?php endif; ?>
</div>