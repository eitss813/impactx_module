<?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project-create/common.tpl'; ?>
<?php echo $this->form->render(); ?>
<?php
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');
?>
<script type="text/javascript">

    var $j = jQuery.noConflict();
    var validationUrl = '<?php echo $this->url(array("action" => "validation"), "sitevideo_video_general", true) ?>';
    var checkingUrlMessage = '<?php echo $this->string()->escapeJavascript($this->translate("Checking URL...")) ?>';
    var current_code;
    function fetchYoutubeData(url){
        // extract v from url
        var myURI = new URI(url);
        var youtube_code = myURI.get('data')['v'];
        if (youtube_code === undefined) {
            youtube_code = myURI.get('file');
        }
        if (youtube_code) {
            (new Request.HTML({
                'format': 'html',
                'url': validationUrl,
                'data': {
                    'ajax': true,
                    'code': youtube_code,
                    'type': 'youtube'
                },
                'onRequest': function () {
                    $j('.custom_description').remove();
                    $j('#url-element').append("<p class='description custom_description'>Checking the URL</p>")
                    // show checking url
                },
                'onSuccess': function (responseTree, responseElements, responseHTML, responseJavaScript) {

                    try{
                        $j('#video_valid').val(valid)
                        if(valid){
                            let title = informationVideoContent.title;
                            let description = informationVideoContent.description
                            let duration = informationVideoContent.duration
                            $j('#video_title-wrapper').show();
                            $j('#video_description-wrapper').show();
                            $j('#video_title').val(title)
                            $j('#video_description').val(description)
                            $j('#video_duration').val(duration)
                            $j('#video_code').val(youtube_code)
                            $j('.custom_description').remove();
                        }else{
                            $j('.custom_description').html('Invalid URL');
                            $j('#video_title').val('')
                            $j('#video_description').val('')
                            $j('#video_duration').val('')
                            $j('#video_code').val(youtube_code)
                            $j('#video_valid').val(false)
                            // show error message
                        }
                    }catch (e) {
                        $j('.custom_description').html('Invalid URL');
                        $j('#video_title').val('')
                        $j('#video_description').val('')
                        $j('#video_duration').val('')
                        $j('#video_code').val(youtube_code)
                        $j('#video_valid').val(false)
                    }

                }
            })).send();
        }
    }

    $j(document).ready(function() {
        $j('#video_title-wrapper').hide();
        $j('#video_description-wrapper').hide();
        $j("#url").bind("paste", function(e){
            // access the clipboard using the api
            var pastedData = e.originalEvent.clipboardData.getData('text');
            fetchYoutubeData(pastedData)
        });
    })

</script>
<style>
    .custom_description{
        color: red !important;
    }
    form.global_form {
        height: 294px;
        overflow-y: auto;
    }
</style>