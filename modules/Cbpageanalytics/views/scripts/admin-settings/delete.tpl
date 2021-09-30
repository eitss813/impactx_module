<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    CB Page Analytics
 * @copyright  Copyright Consecutive Bytes
 * @license    https://consecutivebytes.com/agreement
 * @author     Consecutive Bytes
 */
?>

<form method="post" class="global_form_popup">
    <div>
        <h3>Delete Record?</h3>
        <p>
            Are you sure that you want to delete this Page Visit entry? It will not be recoverable after being deleted.
        </p>
        <br />
        <p>
            <input type="hidden" name="confirm" value="<?php echo $this->page_id ?>"/>
            <button type='submit'>Delete</button>
            or
            <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close();'>
                cancel</a>
        </p>
    </div>
</form>

<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>