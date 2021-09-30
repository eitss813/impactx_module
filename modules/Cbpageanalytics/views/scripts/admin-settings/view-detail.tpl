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

<?php
$item = $this->page;

if (isset($item->user_id) && !empty($item->user_id) && $item->user_id != 0) {
    $user = Engine_Api::_()->getItem('user', $item->user_id);
    $viewer = $this->htmlLink($user->getHref(), $user->getTitle());
} else {
    $viewer = 'Not logged in';
}

if (isset($item->page_original_id) && !empty($item->page_original_id)) {
    $pageData = Engine_Api::_()->getItem('core_page', $item->page_original_id);

    $page = $this->htmlLink($item->page_url, $pageData->getTitle(), array('target' => '_blank'));
} else {
    $page = $this->htmlLink($item->page_url, $title, array('target' => '_blank'));
}
?>

<div class="code_popup global_form_popup ">
    <table>
        <tr><td colspan="2" style="text-align: right; border-bottom: 1px solid #ddd;"><a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close();'><strong>Close X</strong></a></td></tr>
        <?php if (isset($page) && !empty($page)) { ?>
            <tr>
                <td>
                    <div>
                        <strong>Page:</strong> <?php echo $page; ?>
                    </div>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td> 
                <div>
                    <strong>Viewer :</strong> <?php echo $viewer; ?>
                </div>
            </td>
        </tr>
        <tr>
            <td> 
                <div>
                    <strong>Link :</strong> <?php echo $this->htmlLink($item->page_url, $item->page_url, array('target' => '_blank')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td> 
                <div>
                    <strong>Visited at:</strong> <?php echo date('dS F Y h:i:s A', strtotime($item->creation_date)); ?>
                </div>
            </td>
        </tr>
    </table>

    <input type="hidden" name="confirm" value="<?php echo $this->id ?>"/>
</div>

<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>