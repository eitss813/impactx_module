<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: create.tpl 10110 2013-10-31 02:04:11Z andres $
 * @author     Jung
 */
?>

<?php
  if (APPLICATION_ENV == 'production')
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.min.js');
  else
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    var tagsUrl = '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>';
    var validationErrorMessage = "<?php echo $this->translate("We could not find a video there - please check the URL and try again."); ?>";
    var checkingUrlMessage = '<?php echo $this->string()->escapeJavascript($this->translate('Checking URL...')) ?>';

    var updateTextFields = window.updateTextFields = function() {
      var video_element = document.getElementById("type");
      var url_element = document.getElementById("url-wrapper");
      var file_element = document.getElementById("Filedata-wrapper");
      var submit_element = document.getElementById("upload-wrapper");
      var rotation_element = document.getElementById("rotation-wrapper");

      // clear url if input field on change
      $('upload-wrapper').style.display = "none";

      // If video source is empty
      if( video_element.value == 0 ) {
        $('url').value = "";
        file_element.style.display = "none";
        url_element.style.display = "none";
        rotation_element.style.display = "none";
        return;
      } else if( $('code').value && $('url').value ) {
        $('type-wrapper').style.display = "none";
        file_element.style.display = "none";
        rotation_element.style.display = "none";
        submit_element.style.display = "block";
        return;
      } else if( video_element.value == 'iframely' ) {
        // If video source is youtube or vimeo
        $('url').value = '';
        $('code').value = '';
        $('id').value = '';
        file_element.style.display = "none";
        rotation_element.style.display = "none";
        url_element.style.display = "block";
        return;
      } else if( $('id').value ) {
        // if there is video_id that means this form is returned from uploading
        // because some other required field
        $('url-wrapper').style.display = "none";
        $('type-wrapper').style.display = "none";
        rotation_element.style.display = "block";
        file_element.style.display = "none";
        submit_element.style.display = "block";
        return;
      } else if( video_element.value == 'upload' ) {
        // If video source is from computer
        $('url').value = '';
        $('code').value = '';
        file_element.style.display = "block";
        rotation_element.style.display = "block";
        url_element.style.display = "none";
        $('upload_file').style.display = "block";
        $('upload-wrapper').style.display = "";
        return;
      }
    }
    
    var video = window.video = {
      active : false,

      debug : false,

      currentUrl : null,

      currentTitle : null,

      currentDescription : null,

      currentImage : 0,

      currentImageSrc : null,

      imagesLoading : 0,

      images : [],

      maxAspect : (10 / 3), //(5 / 2), //3.1,

      minAspect : (3 / 10), //(2 / 5), //(1 / 3.1),

      minSize : 50,

      maxPixels : 500000,

      monitorInterval: null,

      monitorLastActivity : false,

      monitorDelay : 500,

      maxImageLoading : 5000,

      attach : function() {
        var bind = this;
        $('url').addEvent('keyup', function() {
          bind.monitorLastActivity = (new Date).valueOf();
        });

        var url_element = document.getElementById("url-element");
        var myElement = new Element("p");
        myElement.innerHTML = "test";
        myElement.addClass("description");
        myElement.id = "validation";
        myElement.style.display = "none";
        url_element.appendChild(myElement);

        var body = $('url');
        var lastBody = '';
        var video_element = $('type');
        (function() {
          // Ignore if no change or url matches
          if( body.value == lastBody || bind.currentUrl || video_element.value != 'iframely') {
            return;
          }

          // Ignore if delay not met yet
          if( (new Date).valueOf() < bind.monitorLastActivity + bind.monitorDelay ) {
            return;
          }
          video.iframely(body.value);
          lastBody = body.value;
        }).periodical(250);
      },
      iframely : function(url) {
          (new Request.JSON({
            'url' : '<?php echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'get-iframely-information'), 'default', true) ?>',
            'data' : {
              'format': 'json',
              'uri' : url,
            },
            'onRequest' : function() {
              $('validation').style.display = "block";
              $('validation').innerHTML = checkingUrlMessage;
              $('upload-wrapper').style.display = "none";
            },
            'onSuccess' : function(response) {
              if( response.valid ) {
                $('upload-wrapper').style.display = "block";
                $('validation').style.display = "none";
                $('code').value = response.iframely.code;
                $('title').value = response.iframely.title;
                $('description').value = response.iframely.description;
                $('validation').value = "none";
              } else {
                $('upload-wrapper').style.display = "none";
                $('validation').innerHTML = validationErrorMessage;
                $('code').value = '';
              }
            }
          })).send();
      }
    }
    
    
    // Run stuff
    updateTextFields();
    video.attach();
    
    var autocompleter = new Autocompleter.Request.JSON('tags', tagsUrl, {
      'postVar' : 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'customChoices' : true,
      'filterSubset' : true,
      'multiple' : true,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
    
  });
</script>

<?php if (($this->current_count >= $this->quota) && !empty($this->quota)):?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You have already uploaded the maximum number of videos allowed.');?>
      <?php echo $this->translate('If you would like to upload a new video, please <a href="%1$s">delete</a> an old one first.', $this->url(array('action' => 'manage'), 'video_general'));?>
    </span>
  </div>
  <br/>
<?php else:?>
  <?php echo $this->form->render($this);?>
<?php endif; ?>


<script type="text/javascript">
  $$('.core_main_video').getParent().addClass('active');
</script>
