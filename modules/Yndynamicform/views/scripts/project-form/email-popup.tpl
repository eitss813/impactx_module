<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndynamicform
 * @author     YouNet Company
 */
?>

<?php $this->form_email->setAction($this->url()); ?>
<?php echo $this->form_email->render($this) ?>

<?php if($this->closeSmoothbox ): ?>
    <script type="text/javascript">
        parent.doGuestSubmit('<?php echo $this->email ?>');
        parent.Smoothbox.close();
    </script>
<?php endif; ?>
