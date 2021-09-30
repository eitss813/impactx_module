
/* $Id: composer_music.js 9572 2011-12-27 23:41:06Z john $ */



(function() { // START NAMESPACE
var $ = 'id' in document ? document.id : window.$;



Composer.Plugin.Music = new Class({

  Extends : Composer.Plugin.Interface,

  name : 'music',

  options : {
    title : 'Add Music',
    lang : {},
    requestOptions : false,
    fancyUploadEnabled : true,
    fancyUploadOptions : {},
    debug : ('en4' in window && en4.core.environment == 'production' ? false : true )
  },

  initialize : function(options) {
    this.elements = new Hash(this.elements);
    this.params = new Hash(this.params);
    this.parent(options);
  },

  attach : function() {
    this.parent();
    this.makeActivator();
    return this;
  },

  detach : function() {
    this.parent();
    return this;
  },

  activate : function() {
    if( this.active ) return;
    this.parent();

    this.makeMenu();
    this.makeBody();

    // Generate form
    var fullUrl = this.options.requestOptions.url;
    this.elements.form = new Element('form', {
      'id' : 'compose-music-form',
      'class' : 'compose-form',
      'method' : 'post',
      'action' : fullUrl,
      'enctype' : 'multipart/form-data'
    }).inject(this.elements.body);

    this.elements.formInput = new Element('input', {
      'id' : 'compose-music-form-input',
      'class' : 'compose-form-input',
      'type' : 'file',
      'name' : 'file',
      'accept' : 'audio/*',
      'events' : {
        'change' : this.doRequest.bind(this)
      }
    }).inject(this.elements.form);

    // Try to init fancyupload
  },

  deactivate : function() {
    if (this.params.song_id)
      new Request.JSON({
        url: en4.core.basePath + '/music/remove-song',
        data: {
          format: 'json',
          song_id: this.params.song_id
        }
      });
    if( !this.active ) return;
    this.parent();
  },

  doRequest : function(that) {

    if (this.elements.formInput.files.length > 0) {
      var FileSize = this.elements.formInput.files[0].size / 1024 / 1024; // in MB
      if(FileSize > post_max_size) {
        alert("The size of the file exceeds the limits set on the server.");
        scriptJquery(this.elements.formInput).val('');
        return;
      }
    }
    
    var submittedForm = false;
    this.elements.iframe = new IFrame({
      'name' : 'composeMusicFrame',
      'src' : 'javascript:false;',
      'styles' : {
        'display' : 'none'
      },
      'events' : {
        'load' : function() {
          if( !submittedForm ) {
            return;
          }
          this.doProcessResponse(window._composeMusicResponse);
          window._composeMusicResponse = false;
        }.bind(this)
      }
    }).inject(this.elements.body);

    window._composeMusicResponse = false;
    this.elements.form.set('target', 'composeMusicFrame');

    // Submit and then destroy form
    this.elements.form.submit();
    submittedForm = true;
    this.elements.form.destroy();

    // Start loading screen
    this.makeLoading();
  },

  makeLoading : function(action) {
    if( !this.elements.loading ) {
      if( action == 'empty' ) {
        this.elements.body.empty();
      } else if( action == 'hide' ) {
        this.elements.body.getChildren().each(function(element){ element.setStyle('display', 'none')});
      } else if( action == 'invisible' ) {
        this.elements.body.getChildren().each(function(element){ element.setStyle('height', '0px').setStyle('visibility', 'hidden')});
      }

      this.elements.loading = new Element('div', {
        'id' : 'compose-' + this.getName() + '-loading',
        'class' : 'compose-loading'
      }).inject(this.elements.body);

      var image = this.elements.loadingImage || (new Element('img', {
        'id' : 'compose-' + this.getName() + '-loading-image',
        'class' : 'compose-loading-image'
      }));

      image.inject(this.elements.loading);

      new Element('span', {
        'html' : this._lang('Loading song, please wait...')
      }).inject(this.elements.loading);
    }
  },

  doProcessResponse : function(responseJSON) {
	  if( typeof responseJSON == 'object' && typeof responseJSON.error != 'undefined' ) {
		  if( this.elements.loading ) {
			  this.elements.loading.destroy();
		  }
		  return this.makeError(responseJSON.error, 'empty');
	  }

    // An error occurred
    if ( ($type(responseJSON) != 'object' && $type(responseJSON) != 'hash' )) {
      if( this.elements.loading )
          this.elements.loading.destroy();
      this.makeError(this._lang('Unable to upload music. Please click cancel and try again'), 'empty');
      return;
    }

    if (  $type(parseInt(responseJSON.id)) != 'number' ) {
      if( this.elements.loading )
          this.elements.loading.destroy();
      //if ($type(console))
      //  console.log('responseJSON: %o', responseJSON);
      this.makeError(this._lang('Song got lost in the mail. Please click cancel and try again'), 'empty');
      return;
    }
    // Success
    this.params.set('rawParams',  responseJSON);
    this.params.set('song_id',    responseJSON.id);
    this.params.set('song_title', responseJSON.fileName);
    this.params.set('song_url',   responseJSON.song_url);
    this.elements.preview = new Element('a', {
      'href': responseJSON.song_url,
      'text': responseJSON.fileName,
      'class': 'compose-music-link',
      'events' : {
        'click' : function(event) {
          event.stop();
          $(this).toggleClass('compose-music-link-playing');
          $(this).toggleClass('compose-music-link');
          var song = (responseJSON.song_url.match(/\.mp3$/)
            ? soundManager.createSound({id:'s'+responseJSON.id, url:responseJSON.song_url})
          : soundManager.createVideo({id:'s'+responseJSON.id, url:responseJSON.song_url}));
          song.togglePause();
          this.blur();
        }
      }
    });
    this.elements.preview.set('text', responseJSON.fileName);
    this.doSongLoaded();
  },

  doSongLoaded : function() {
    if( this.elements.loading )
        this.elements.loading.destroy();
    if( this.elements.formFancyContainer )
        this.elements.formFancyContainer.destroy();
    if( this.elements.error ) {
      this.elements.error.destroy();
    }
    this.elements.preview.inject(this.elements.body);
    this.makeFormInputs();
  },

  makeFormInputs : function() {
    this.ready();
    this.parent({
      'song_id' : this.params.song_id
    });
  }

});



})(); // END NAMESPACE
