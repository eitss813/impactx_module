<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$height1Per = $this->height / 100;
if ($this->isEmbedVideo && $this->isVideo) {
    $list1Height = $this->height - ($height1Per * 25) - 104;
    $list2Height = $height1Per * 25;
} elseif ($this->isVideo) {
    $list1Height = $this->height - 50;
    $list2Height = 0;
} elseif ($this->isEmbedVideo) {
    $list1Height = 0;
    $list2Height = $this->height - 104;
}
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="//api.dmcdn.net/all.js"></script>
<script>
jq = $.noConflict();
</script>
<style type="text/css">
    .sitevideo_playall_container {
        min-height:<?php echo $this->height;
?>px;
    }
    .sitevideo_playlist ul {
        height:<?php echo $list1Height; ?>px;
    }
    ul.embed_video_playlist {
        height:<?php echo $list2Height ?>px !important;
    }
    .sitevideo_playlist {
        <?php echo(($this->playlistOptions == 1) ? 'margin-top:30px;' : '') ?>
    }
	/*For Pintrest videos on play all page*/
	#flvPlayerDiv > span{  height:<?php echo $this->height; ?>px;}

 </style>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/scripts/flowplayer-3.2.13.min.js');
?>
<div class="sitevideo_playall_container">
    <?php if ($this->playlistOptions == 1) : ?>
        <div class="play_list_name"> <?php echo $this->playlist->getTitle(); ?> </div>
    <?php endif; ?>
    <div id="video_loader" class="video_loader" style="background-image:url('<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/images/video_loading.gif' ?>'); height: <?php echo $this->height; ?>px;" >
        <iframe id="player" src=""> </iframe>
        <div id="playerDiv"> </div>
        <div id="flvPlayerDiv"> </div>
        <video  src="" id="videoPlayer" controls > video not supported </video>
    </div>
    <div class="sitevideo_viewer_container">
        <div class="sitevideo_info"></div>
        <div class="site_video_author_name" id="site_video_title"></div>
        <div class="sitevideo_author_section">
            <div class="sitevideo_author" id="site_vedioauther"> </div>
            <div class="video_viewers" >
                <p id="sitevideo_bottom_info_views"></p>
            </div>
            <?php if (count($this->maps) > 0): ?>
            </div>
            <div class="sitevideo_bottom_container">
                <div class="blue_line"></div>
                <div class="icons_left"></div>
                <div class="icons_right"> <a class="sitevideo_bottom_info_likes" id="sitevideo_bottom_info_likes"> </a> </div>
                <div class="widthfull">
                    <p id="postedOn"></p>
                    <span class="site_video_author_name" id="site_video_description"></span> </div>
            </div>
        </div>
    </div>
    <div class="sitevideo_playlist">
        <div class="playlist_heading">
            <div> <?php echo $this->htmlLink($this->playlist->getHref(), $this->string()->truncate($this->playlist->getTitle(), 45)); ?></div>
            <div class="user_play_listname"><?php echo $this->htmlLink($this->playlist->getOwner()->getHref(), $this->string()->truncate($this->playlist->getOwner()->getTitle(), 25)); ?> &nbsp; </div>
            <div id="video_counter"></div>
        </div>
        <?php $c = 0; ?>
        <?php if ($this->isVideo) : ?>
            <ul class="playlist_details">

                <?php foreach ($this->maps as $map): ?>
                    <?php
                    //FIND SITEVIDEO MODEL
                    $item = $map->getVideoDetail();
                    if ($item->status != 1 || in_array($item->type, array('embedcode','instagram','twitter','pinterest')))
                        continue;
                    if ($item->checkType('upload') && empty($item->file_id)) {
                        continue;
                    }
                    ?>
                    <li id="playlist_video_<?php echo $item->video_id; ?>" onclick="player.currentPosition =<?php echo $c; ?>;
                                        player.playVideos();" > <span><?php echo $c + 1; ?></span>
                        <div class="sitevideo_list_thumb">
                            <?php
                            //CHECKING FOR VIDEO THUMBNAIL
                            if ($item->photo_id) {
                                echo $this->itemPhoto($item, 'thumb.normal');
                            } else {
                                echo '<a href=""></a>';
                            }
                            ?>
                        </div>
                        <div class="sitevideo_bottom_info">
                            <p> <?php echo $this->string()->truncate($item->getTitle(), $this->titleTruncation); ?> </p>
                            <p> <?php echo $this->translate("by %s", $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())); ?></p>
                        </div>
                    </li>
                    <?php $c++; ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <?php if ($this->isEmbedVideo) : ?>
            <div>
                <div class="embed_video_playlist_heading">
                    Embedded Videos<span>(Below videos could not be played automatically)</span>
                </div>
                <ul class="embed_video_playlist">
                    <?php foreach ($this->maps as $map): ?>
                        <?php
                        //FIND SITEVIDEO MODEL
                        $item = $map->getVideoDetail();
                        if ($item->status != 1 || !in_array($item->type, array('embedcode','instagram','twitter','pinterest')))
                            continue;
                        ?>
                        <li id="playlist_video_<?php echo $item->video_id; ?>" onclick="player.currentPosition =<?php echo $c; ?>;
                                            player.playVideos();" > <span><?php echo $c + 1; ?></span>
                            <div class="sitevideo_list_thumb">
                                <?php
                                //CHECKING FOR VIDEO THUMBNAIL
                                if ($item->photo_id) {
                                    echo $this->itemPhoto($item, 'thumb.normal');
                                } else {
                                    echo '<a href=""></a>';
                                }
                                ?>
                            </div>
                            <div class="sitevideo_bottom_info">
                                <p> <?php echo $this->string()->truncate($item->getTitle(), $this->titleTruncation); ?> </p>
                                <p> <?php echo $this->translate("by %s", $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())); ?></p>
                            </div>
                        </li>
                        <?php $c++; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    <div style="clear:both;"> </div>
   <script>
        player =
                {
                    "currentPosition": <?php echo $this->currentPosition; ?>,
                    "state": false,
                    "youtubeVideoPlayedCount": 0,
                    "dailymotionVideoPlayedCount": 0,
                    "pinterestVideoPlayedCount": 0,
					"instagramVideoPlayedCount":0,
                    "currentVideo": null,
                    "width": 850,
                    "height": <?php echo $this->height; ?>,
                    "totalVideos":<?php echo count($this->maps); ?>,
                    "backgroundImage": jq("#video_loader").css('background-image'),
                }
        player.initiatePlayer = function ()
        {
            jq("#player").width(player.width);
            jq("#player").height(player.height);
            jq("#player").css("width", "100%")
            jq("#playerDiv").css("width", player.width);
            jq("#playerDiv").css("height", player.height);
            document.getElementById("videoPlayer").setAttribute('height', player.height);
            document.getElementById("videoPlayer").setAttribute('width', player.width);
            player.playVideos();
        }
        player.videos = <?php echo json_encode($this->videosList); ?>

        function playYoutubeVideo()
        {
            window.onYouTubeIframeAPIReady = function () {
                playeraa = new YT.Player('player', {
                    events: {
                        'onReady': onPlayerReady,
                        'onStateChange': onPlayerStateChange
                    }
                });
            }

            // autoplay video
            function onPlayerReady(event) {
                event.target.playVideo();
            }

            function onPlayerStateChange(event) {
                // when video ends

                if (event.data === 0) {
                    player.currentPosition++;
                    player.youtubeVideoPlayedCount++;
                    player.state = true;
                }
            }
            var tag = document.createElement('script');
            tag.src = "//www.youtube.com/player_api";
            var firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

            if (player.youtubeVideoPlayedCount != 0)
                onYouTubeIframeAPIReady();
        }
        player.playVideos = function ()
        {
            jq("#video_loader").css('background-image', player.backgroundImage);
			jq("#video_loader").css('background-color', "#000");
            if (player.currentPosition == player.videos.length)
            {
                console.log("Finish..");
                return false;
            }
            video = player.currentVideo = player.videos[player.currentPosition];
            video.url = video.url.replace("http://", "//"); 
            video.url = video.url.replace("https://", "//"); 
        vText = (player.totalVideos == 1) ? " Video" : " Videos";
            jq("#video_counter").html((player.currentPosition + 1) + "/" + player.totalVideos + vText);
            $$('ul.playlist_details > li').each(function (el) {
                el.removeClass('active');
            });
            viewCount = video.view_count == 1 ? video.view_count + ' view' : video.view_count + ' views';
            likeCount = video.like_count == 1 ? video.like_count + ' like' : video.like_count + ' likes';
            jq('#playlist_video_' + video.vid).addClass('active');
            jq("#site_video_title").html(video.titleLink);
            jq("#site_video_description").html(video.description);
            jq("#sitevideo_bottom_info_likes").html(video.like_count);
            jq("#sitevideo_bottom_info_views").html(viewCount);
            jq("#sitevideo_bottom_info_likes").attr('title', likeCount)
            jq("#sitevideo_bottom_info_views").attr('title', viewCount)
            jq("#postedOn").html(video.posted_date);
            jq("#site_vedioauther").html(video.otherInfo);
            jq("#videoOwner").html(video.videoOwnerLink);
            switch (video.type)
            {
                case "youtube" :
                    jq("#player").show();
                    jq("#playerDiv").hide();
                    jq("#videoPlayer").hide();
                    jq("#flvPlayerDiv").hide();
                    document.getElementById("player").setAttribute("src", video.url);
                    playYoutubeVideo();
                    break;
                case "vimeo" :
                    jq("#player").show();
                    jq("#playerDiv").hide();
                    jq("#videoPlayer").hide();
                    jq("#flvPlayerDiv").hide();
                    document.getElementById("player").setAttribute("src", video.url);
                    break;
                case "dailymotion" :
                    jq("#player").hide();
                    jq("#videoPlayer").hide();
                    jq("#playerDiv").show();
                    jq("#flvPlayerDiv").hide();
                    dmAsyncInit();
                    break;
                case "mycomputer" :
                    jq("#playerDiv").hide();
                    jq("#playerDiv").html("");
                    jq("#player").hide();
                    jq("#videoPlayer").hide();
                    jq("#flvPlayerDiv").hide();
                    if (video.ext != 'flv')
                    {
                        jq("#videoPlayer").show();
                        document.getElementById("videoPlayer").setAttribute("src", video.url);
                        document.getElementById('videoPlayer').play();
                    }
                    else {
                        jq("#flvPlayerDiv").show();
                        playFlvVideo(video);
                    }
                    break;
                case "other" :
                    jq("#player").show();
                    jq("#playerDiv").hide();
                    jq("#videoPlayer").hide();
                    jq("#flvPlayerDiv").hide();
                    document.getElementById("player").setAttribute("src", video.url);
                    break;
				case "instagram" :
                    jq("#flvPlayerDiv").html();
                    jq("#player").hide();
                    jq("#playerDiv").hide();
                    jq("#videoPlayer").hide();
                    jq("#flvPlayerDiv").show();
                    player.instagramVideoPlayedCount++;
                    if (player.instagramVideoPlayedCount > 1) {
                        window.location = video.playallLink;
                    }
                    else {
                        jq("#flvPlayerDiv").html(video.url);
                    }
					jq(".sitevideo_playall_container").css("min-height","");
					jq("#video_loader").css("height","");
					jq("#video_loader").css('background-image', "");
					jq("#video_loader").css('background-color', "#fff");
                    break;
                case "twitter" :
                    jq("#flvPlayerDiv").html();
                    jq("#player").hide();
                    jq("#playerDiv").hide();
                    jq("#videoPlayer").hide();
                    jq("#flvPlayerDiv").show();
                    jq("#flvPlayerDiv").html(video.url);
					jq(".sitevideo_playall_container").css("min-height","");
					jq("#video_loader").css("height","");
					jq("#video_loader").css('background-image', "");
					jq("#video_loader").css('background-color', "#fff");
					break;
                case "pinterest" :
                    jq("#flvPlayerDiv").html();
                    jq("#player").hide();
                    jq("#playerDiv").hide();
                    jq("#videoPlayer").hide();
                    jq("#flvPlayerDiv").show();
                    player.pinterestVideoPlayedCount++;
                    if (player.pinterestVideoPlayedCount > 1) {
                        window.location = video.playallLink;
                    }
                    else {
                        jq("#flvPlayerDiv").html(video.url);
                    }
                        
                    break;
            }
        }
        player.checkStatus = function ()
        {
            if (player.state)
            {
                player.state = false;
                player.playVideos();
            }
            setTimeout(player.checkStatus, 10);
        }
        player.checkStatus();

        jq(function () {
            var playerVD = jq('iframe');
            var playerOrigin = '*';
            var status = jq('.status');

            // Listen for messages from the player
            if (window.addEventListener) {
                window.addEventListener('message', onMessageReceived, false);
            }
            else {
                window.attachEvent('onmessage', onMessageReceived, false);
            }

            // Handle messages received from the player
            function onMessageReceived(event) {
                // Handle messages from the vimeo player only
                if (!(/^https?:\/\/player.vimeo.com/).test(event.origin)) {
                    return false;
                }

                if (playerOrigin === '*') {
                    playerOrigin = event.origin;
                }

                var data = JSON.parse(event.data);

                switch (data.event) {
                    case 'ready':
                        onReady();
                        break;
                    case 'finish':
                        onFinish();
                        break;
                }
            }
            // Helper function for sending a message to the player
            function post(action, value) {
                var data = {
                    method: action
                };

                if (value) {
                    data.value = value;
                }
                var message = JSON.stringify(data);
                playerVD[0].contentWindow.postMessage(data, playerOrigin);
            }
            function onReady() {
                post("play");
                post('addEventListener', 'finish');
            }
            function onFinish() {
                player.currentPosition++;
                player.state = true;
            }
        });

        window.dmAsyncInit = function ()
        {
            var video = player.currentVideo;
            if (!video)
                return false;
            var dailymotionPlayer = DM.player("playerDiv", {video: video.id, width: "420", height: "315"});
            dailymotionPlayer.addEventListener("apiready", function (e) {
                e.target.play();
            });
            dailymotionPlayer.addEventListener("ended", function (e) {
                player.currentPosition++;
                player.state = true;
                player.dailymotionVideoPlayedCount++;
            });
        };
        function playFlvVideo(video) { //alert(video.url);

            $f("flvPlayerDiv", {src: "<?php echo $this->layout()->staticBaseUrl ?>externals/flowplayer/flowplayer-3.2.18.swf", width: player.width,
                height: player.height,
                wmode: 'transparent'}, {
                clip: {
                    url: video.url,
                    autoPlay: true,
                    onFinish: function (clip)
                    {
                        player.currentPosition++;
                        player.state = true;
                    }
                },
                plugins: {
                    controls: {
                        background: '#000000',
                        bufferColor: '#333333',
                        progressColor: '#444444',
                        buttonColor: '#444444',
                        buttonOverColor: '#666666'
                    }
                },
                play: {
                    label: null,
                    replayLabel: "click to play again"
                }
            });
        }

    </script> 
    <script>
        document.getElementById('videoPlayer').addEventListener('ended', myHandler, false);
        function myHandler(e) {
            player.currentPosition++;
            player.state = true;
        }
        player.initiatePlayer();
    </script>
<?php else : ?>
    <div class="tip"> <span> No video added in this playlist. </span> </div>
<?php endif; ?>
