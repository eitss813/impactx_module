<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: refund.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
    <div>
        <h3>Refund Project ?</h3>
        <p>
           Are you sure that you want to refund money to backer of this Project ?
        </p>
        <br />
        <p>
            <input type="hidden" name="confirm" value="<?php echo $this->project_id ?>"/>
            <button type='submit'>Refund</button>
            or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>cancel</a>
        </p>
    </div>
</form>

<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
                    TB_close();
    </script>
<?php endif; ?>
