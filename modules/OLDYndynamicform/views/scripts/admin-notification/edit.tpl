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