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

<script type="text/javascript">
    var pageData = <?php echo json_encode($this->pageData, true); ?>;

    if (pageData.page_title == '') {
        pageData.page_title = document.title;
    }

    var request = new Request({
        url: "<?php echo $this->url(array(), 'cbpageanalytics_general', true); ?>",
        method: "post",
        data: pageData,
        onSuccess: function (responseText) {
            console.log(responseText);
        }
    });

    request.send();
</script>