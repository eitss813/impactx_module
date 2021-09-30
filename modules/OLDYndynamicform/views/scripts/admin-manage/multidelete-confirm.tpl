<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndynamicform
 * @author     YouNet Company
 */
?>

<form method="post" class="global_form_popup" action="<?php echo $this->url(array()) ?>">
    <div>
        <h3><?php echo $this->translate("Delete All Selected Forms?") ?></h3>
        <p>
            <?php echo $this->translate("Are you sure that you want to delete all selected forms? They will not be recoverable after being deleted.") ?>
        </p>
        <br />
        <p>
            <button type='submit'><?php echo $this->translate("Delete") ?></button>
            <?php echo $this->translate("or") ?>
            <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
                <?php echo $this->translate("cancel") ?>
            </a>
        </p>
    </div>
</form>

<?php if( @$this->closeSmoothbox ): ?>
    <script type="text/javascript">
        parent.submitForm();
        parent.Smoothbox.close();
    </script>
<?php endif; ?>
