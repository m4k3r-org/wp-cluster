
/*
 * HTML5 Audio Player with Playlist by Tean v2.2
 * http://codecanyon.net/item/html5-audio-player-with-playlist/1694831
 */

(function($) {

	$.fn.html5audio = function(settings) {
		
	var componentInited=false;
	
	var _body = $('body');
	var _window = $(window);
	var _doc = $(document);
	
	var isMobile = (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent));
	//isMobile=true;
	
	var isIOS=false;
	var agent = navigator.userAgent;
	var mobile_type;
	if (agent.indexOf('iPhone') > -1 || agent.indexOf('iPod') > -1 || agent.indexOf('iPad') > -1) {
		isIOS=true;
		if(agent.indexOf('iPhone') > -1)mobile_type = 'iPhone';
		else if(agent.indexOf('iPod') > -1)mobile_type = 'iPod';
		else if(agent.indexOf('iPad') > -1)mobile_type = 'iPad';
	}
	
	var isIE = false, ieBelow9 = false;
	var ie_check = getInternetExplorerVersion();
	if (ie_check != -1){
		isIE = true;
		if(ie_check < 9)ieBelow9 = true;
	} 
	
	function getInternetExplorerVersion(){
	 //http://msdn.microsoft.com/en-us/library/ms537509%28v=vs.85%29.aspx
	 //Returns the version of Internet Explorer or a -1 (indicating the use of another browser).
	  var rv = -1; // Return value assumes failure.
	  if (navigator.appName == 'Microsoft Internet Explorer'){
		var ua = navigator.userAgent;
		var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
		if (re.exec(ua) != null){
		  rv = parseFloat( RegExp.$1 );
		}
	  }
	  return rv;
	}
	
	var audio = document.createElement('audio'), mp3Support, oggSupport;
	if (audio.canPlayType) {
		mp3Support = !!audio.canPlayType && "" != audio.canPlayType('audio/mpeg');
	    oggSupport = !!audio.canPlayType && "" != audio.canPlayType('audio/ogg; codecs="vorbis"');
	}else{//flash audio
		mp3Support = true;
		oggSupport = false;
	}
	//console.log('mp3Support = ', mp3Support, ' , oggSupport = ', oggSupport);
	
	//elements
	var componentWrapper = $(this);
	//console.log(componentWrapper);
	var componentPlaylist = componentWrapper.find('.componentPlaylist');
	var playlist_inner = componentWrapper.find('.playlist_inner').css('opacity',0);
	var playlistHolder=componentWrapper.find('.playlistHolder');
	var playerHolder=componentWrapper.find('.playerHolder');
	var player_mediaTime=componentWrapper.find('.player_mediaTime');
	var player_mediaTime_current=componentWrapper.find('.player_mediaTime_current');
	var player_mediaTime_total=componentWrapper.find('.player_mediaTime_total');
	var player_mediaName_Mask=componentWrapper.find('.player_mediaName_Mask');
	var player_mediaName=componentWrapper.find('.player_mediaName');
	var player_controls=componentWrapper.find('.player_controls');
	var preloader = componentWrapper.find('.preloader');
	
	//icons
	var prevBtnUrl = settings.buttonsUrl.prev;
	var prevOnBtnUrl = settings.buttonsUrl.prevOn;
	var nextBtnUrl = settings.buttonsUrl.next;
	var nextOnBtnUrl = settings.buttonsUrl.nextOn;
	var playBtnUrl = settings.buttonsUrl.play;
	var playOnBtnUrl = settings.buttonsUrl.playOn;
	var pauseBtnUrl = settings.buttonsUrl.pause;
	var pauseOnBtnUrl = settings.buttonsUrl.pauseOn;
	var loopBtnUrl = settings.buttonsUrl.loop;
	var loopOnBtnUrl = settings.buttonsUrl.loopOn;
	var volumeBtnUrl = settings.buttonsUrl.volume;
	var volumeOnBtnUrl = settings.buttonsUrl.volumeOn;
	var muteBtnUrl = settings.buttonsUrl.mute;
	var muteOnBtnUrl = settings.buttonsUrl.muteOn;
	var shuffleBtnUrl = settings.buttonsUrl.shuffle;
	var shuffleOnBtnUrl = settings.buttonsUrl.shuffleOn;
	var playlistBtnUrl = settings.buttonsUrl.playlist;
	var playlistOnBtnUrl = settings.buttonsUrl.playlistOn;
	
	//settings
	var playlist_holder = $(settings.playlistHolder);
	var sm2_sound_id = settings.sound_id;
	var autoPlay=settings.autoPlay;
	var autoLoad = settings.autoLoad;
	var initialAutoplay=autoPlay;
	if(isMobile)autoPlay=false;//important!!
	var soundcloudApiKey = settings.soundcloudApiKey;
	var useSongNameScroll=settings.useSongNameScroll;
	var autoSetSongTitle = settings.autoSetSongTitle;
	var loopingOn=settings.loopingOn;
	var randomPlay=settings.randomPlay;
	var mediaTimeSeparator = settings.mediaTimeSeparator;
	var seekTooltipSeparator = settings.seekTooltipSeparator;
	var defaultArtistData = settings.defaultArtistData;
	var useNumbersInPlaylist=settings.useNumbersInPlaylist;
	var activePlaylist=settings.activePlaylist;
	var useAlertMessaging = settings.useAlertMessaging;
	var activatePlaylistScroll=settings.activatePlaylistScroll;
	
	player_mediaName.html(defaultArtistData);
	player_mediaTime_current.html('0:00' + mediaTimeSeparator);
	player_mediaTime_total.html('0:00');
	var songTimeCurr = player_mediaTime_current.html();
	var songTimeTot = player_mediaTime_total.html();
	
	//vars
	var _playlistLoaded = false;//callback boolean
	
	var _currentInsert, playlist_index;
	
	var lastPlaylist = null;
	var playlistTransitionOn=true;
	
	var nullMetaData=false;//iphone sucks
	var sound_started=false;//detect sound start
	var sm_curentSound;//sound manager active sound
	
	var audioInited=false;
	var mp3Path='';	
	var oggPath='';	
	var mediaPlaying=false;
	var useRollovers=true;//we need at least 'on' btn state for the loop and random 'on' btn
	var soundLength;
	
	var dataInterval = 250;//tracking media data
	var dataIntervalID;
	
	if(loopingOn) componentWrapper.find('.player_loop').find('img').attr('src', loopOnBtnUrl);
	if(randomPlay) componentWrapper.find('.player_shuffle').find('img').attr('src', shuffleOnBtnUrl);
	
	var playlistDataArr=[];
	var aArr=[];//a
	var liArr=[];//li
	var _playlistLength;
	
	var scrollPaneApi;
	
	var feedParser = $('<div/>').css('display','none').appendTo(componentWrapper);
	
	var processPlaylistDataArr=[];
	var processPlaylistArr=[];
	

	var textScroller;
	initNameScroll();
	function initNameScroll(){
		if(useSongNameScroll){
			var fontMeasure = $('<div/>').addClass('fontMeasure').appendTo(componentWrapper);
			textScroller = new apTextScroller();
			textScroller.init(fontMeasure, player_mediaName, player_mediaName_Mask, 'left', settings.scrollSeparator, settings.scrollSpeed);
		}
	}
	
	var pm_settings = {'randomPlay': randomPlay, 'loopingOn': loopingOn};
	var playlistManager = $.playlistManager(pm_settings);
	$(playlistManager).bind('ap_PlaylistManager.COUNTER_READY', function(){
		//console.log('ap_PlaylistManager.COUNTER_READY');
		var c = playlistManager.getCounter();
		disableActiveItem();
		if(autoSetSongTitle){
			if(textScroller && useSongNameScroll){
				textScroller.deactivate();
				if(getTitle(c)){
					textScroller.input(getTitle(c));
					textScroller.activate();
				}
			}else{
				if(getTitle(c))player_mediaName.html(getTitle(c));
			}
		}
		mp3Path = playlistDataArr[c].mp3Path;
		oggPath = playlistDataArr[c].oggPath;
		soundLength = playlistDataArr[c].length ? playlistDataArr[c].length : null;
		findMedia();	
		if(typeof itemTriggered !== 'undefined')itemTriggered(sm2_sound_id, c);//callback
		else if(typeof parent.itemTriggered !== 'undefined')parent.itemTriggered(sm2_sound_id, c);//callback
	});
	$(playlistManager).bind('ap_PlaylistManager.PLAYLIST_END', function(){
		disableActiveItem();
		if(typeof audioPlayerPlaylistEnd !== 'undefined')audioPlayerPlaylistEnd(sm2_sound_id);//callback
		else if(typeof parent.audioPlayerPlaylistEnd !== 'undefined')parent.audioPlayerPlaylistEnd(sm2_sound_id);//callback
	});
	$(playlistManager).bind('ap_PlaylistManager.PLAYLIST_END_ALERT', function(){
		if(typeof audioPlayerPlaylistEnd !== 'undefined')audioPlayerPlaylistEnd(sm2_sound_id);//callback
		else if(typeof parent.audioPlayerPlaylistEnd !== 'undefined')parent.audioPlayerPlaylistEnd(sm2_sound_id);//callback
	});
	
	var _downEvent = "";
	var _moveEvent = "";
	var _upEvent = "";
	var hasTouch;
	if("ontouchstart" in window) {
		hasTouch = true;
		_downEvent = "touchstart.ap";
		_moveEvent = "touchmove.ap";
		_upEvent = "touchend.ap";
	}else{
		hasTouch = false;
		_downEvent = "mousedown.ap";
		_moveEvent = "mousemove.ap";
		_upEvent = "mouseup.ap";
	}
	
	
	//********** volume 
	
	var _lastVolume;//for mute/unmute
	var _muteOn=false;
	var _defaultVolume =settings.defaultVolume;
	if(_defaultVolume<0) _defaultVolume=0;
	else if(_defaultVolume>1)_defaultVolume=1;
	
	var volumebarDown=false;
	var volume_bg = componentWrapper.find('.volume_bg');
	var volumeSize=volume_bg.width();
	//console.log(volumeSize);
	var volume_level = componentWrapper.find('.volume_level').css('width', _defaultVolume*volumeSize+'px');
	
	var volume_seekbar = componentWrapper.find('.volume_seekbar').css('cursor', 'pointer')
	.bind(_downEvent,function(e){
		if(isIOS){
			//if(useAlertMessaging) alert('Setting volume on ' + mobile_type + ' is not possible with javascript! Use physical button on your ' + mobile_type + ' to adjust volume.');
			//return false;
		}
		_onDragStartVol(e);
		_muteOn=false;//reset
		return false;		
	});
	if(!isMobile){
		volume_seekbar.bind('mouseover', mouseOverHandlerVolume);
		var player_volume_tooltip = componentWrapper.find('.player_volume_tooltip').css('left', parseInt(volume_seekbar.css('left'), 10) + 'px');
	}
	
	// Start dragging 
	function _onDragStartVol(e) {
		if(!componentInited || playlistTransitionOn) return;
		if(seekBarDown) return;
		if(!volumebarDown){					
			var point;
			if(hasTouch){
				var currTouches = e.originalEvent.touches;
				if(currTouches && currTouches.length > 0) {
					point = currTouches[0];
				}else{	
					return false;						
				}
			}else{
				point = e;								
				e.preventDefault();						
			}
			volumebarDown = true;
			_doc.bind(_moveEvent, function(e) { _onDragMoveVol(e); });
			_doc.bind(_upEvent, function(e) { _onDragReleaseVol(e); });		
		}
		return false;	
	}
				
	function _onDragMoveVol(e) {	
		var point;
		if(hasTouch){
			var touches;
			if(e.originalEvent.touches && e.originalEvent.touches.length) {
				touches = e.originalEvent.touches;
			}else if(e.originalEvent.changedTouches && e.originalEvent.changedTouches.length) {
				touches = e.originalEvent.changedTouches;
			}else{
				return false;
			}
			// If touches more then one, so stop sliding and allow browser do default action
			if(touches.length > 1) {
				return false;
			}
			point = touches[0];	
			e.preventDefault();				
		} else {
			point = e;
			e.preventDefault();		
		}
		volumeTo(point.pageX);
		
		return false;		
	}
	
	function _onDragReleaseVol(e) {
		if(volumebarDown){	
			volumebarDown = false;			
			_doc.unbind(_moveEvent).unbind(_upEvent);	
			
			var point;
			if(hasTouch){
				var touches;
				if(e.originalEvent.touches && e.originalEvent.touches.length) {
					touches = e.originalEvent.touches;
				}else if(e.originalEvent.changedTouches && e.originalEvent.changedTouches.length) {
					touches = e.originalEvent.changedTouches;
				}else{
					return false;
				}
				// If touches more then one, so stop sliding and allow browser do default action
				if(touches.length > 1) {
					return false;
				}
				point = touches[0];	
				e.preventDefault();				
			} else {
				point = e;
				e.preventDefault();		
			}
			
			//console.log(point.pageX);
			volumeTo(point.pageX);
		}
		return false;
	}	
	
	function volumeTo(x) {
		_defaultVolume = Math.max(0, Math.min(1, (x - volume_bg.offset().left) / volumeSize));
	 	setVolume();
	}
	
	function setVolume(){
		//console.log('setVolume');
		volume_level.css('width', _defaultVolume*volumeSize+'px');
		if(audioInited)soundManager.setVolume(sm2_sound_id,_defaultVolume*100);
		if(_defaultVolume > 0){
			componentWrapper.find('.player_volume').find('img').attr('src', volumeBtnUrl);
		}else{
			componentWrapper.find('.player_volume').find('img').attr('src', muteBtnUrl);
		}
	}
	
	//************* volume tooltip
	
	function mouseOverHandlerVolume() {
		player_volume_tooltip.css('display', 'block');
		volume_seekbar.bind('mousemove', mouseMoveHandlerVolumeTooltip).bind('mouseout', mouseOutHandlerVolume);
		_doc.bind('mouseout', mouseOutHandlerVolume);
	}
	
	function mouseOutHandlerVolume() {
		player_volume_tooltip.css('display', 'none');
		volume_seekbar.unbind('mousemove', mouseMoveHandlerVolumeTooltip).unbind('mouseout', mouseOutHandlerVolume);
		_doc.unbind('mouseout', mouseOutHandlerVolume);
	}
	
	function mouseMoveHandlerVolumeTooltip(e){
		var s = e.pageX - volume_bg.offset().left;
		if(isNaN(s))return false;
		if(s<0) s=0;
		else if(s>volumeSize) s=volumeSize;
		
		var center = s + parseInt(volume_seekbar.css('left'), 10) + parseInt(player_controls.css('left'), 10) + parseInt(volume_bg.css('left'), 10) - player_volume_tooltip.width() / 2;
		player_volume_tooltip.css('left', center + 'px');
		
		var newPercent = Math.max(0, Math.min(1, s / volumeSize));
		var value=parseInt(newPercent * 100, 10);
		player_volume_tooltip.find('p').html(value+' %');
	}
	
	//************** end volume
	
	
	
	
	
	//************** seekbar
	
	var seekPercent;
	var seekBarDown=false;
	var progress_bg = componentWrapper.find('.progress_bg');
	var load_progress = componentWrapper.find('.load_progress');
	var play_progress = componentWrapper.find('.play_progress');
	var seekBarSize=progress_bg.width();
	
	var player_progress = componentWrapper.find('.player_progress').css('cursor', 'pointer').bind(_downEvent,function(e){
		_onDragStartSeek(e);
		return false;		
	})
	if(!isMobile){
		player_progress.bind('mouseover', mouseOverHandlerSeek); 
		var player_progress_tooltip = componentWrapper.find('.player_progress_tooltip').css('left', parseInt(player_progress.css('left'), 10) + 'px');
		player_progress_tooltip.find('p').html('0:00' + seekTooltipSeparator + '0:00');
	}
	
	// Start dragging 
	function _onDragStartSeek(e) {
		if(!componentInited) return;
		if(!audioInited) return;
		if(volumebarDown) return;
		if(nullMetaData) return;
		if(!seekBarDown){					
			var point;
			if(hasTouch){
				var currTouches = e.originalEvent.touches;
				if(currTouches && currTouches.length > 0) {
					point = currTouches[0];
				}else{	
					return false;						
				}
			}else{
				point = e;								
				e.preventDefault();						
			}
			seekBarDown = true;
			_doc.bind(_moveEvent, function(e) { _onDragMoveSeek(e); });
			_doc.bind(_upEvent, function(e) { _onDragReleaseSeek(e); });		
		}
		return false;	
	}
				
	function _onDragMoveSeek(e) {	
		var point;
		if(hasTouch){
			var touches;
			if(e.originalEvent.touches && e.originalEvent.touches.length) {
				touches = e.originalEvent.touches;
			}else if(e.originalEvent.changedTouches && e.originalEvent.changedTouches.length) {
				touches = e.originalEvent.changedTouches;
			}else{
				return false;
			}
			// If touches more then one, so stop sliding and allow browser do default action
			if(touches.length > 1) {
				return false;
			}
			point = touches[0];	
			e.preventDefault();				
		} else {
			point = e;
			e.preventDefault();		
		}
		setProgress(point.pageX);
		
		return false;		
	}
	
	function _onDragReleaseSeek(e) {
		if(seekBarDown){	
			seekBarDown = false;			
			_doc.unbind(_moveEvent).unbind(_upEvent);	
			
			var point;
			if(hasTouch){
				var touches;
				if(e.originalEvent.touches && e.originalEvent.touches.length) {
					touches = e.originalEvent.touches;
				}else if(e.originalEvent.changedTouches && e.originalEvent.changedTouches.length) {
					touches = e.originalEvent.changedTouches;
				}else{
					return false;
				}
				// If touches more then one, so stop sliding and allow browser do default action
				if(touches.length > 1) {
					return false;
				}
				point = touches[0];	
				e.preventDefault();				
			} else {
				point = e;
				e.preventDefault();		
			}
			setProgress(point.pageX);
		}
		return false;
	}	
	
	function setProgress(x) {
		seekPercent = x - progress_bg.offset().left;
		if(seekPercent<0) seekPercent=0;
		else if(seekPercent>seekBarSize) seekPercent=seekBarSize;
		play_progress.width(seekPercent);
		var newPercent = Math.max(0, Math.min(1, seekPercent / seekBarSize));
	    soundManager.setPosition(sm2_sound_id,newPercent * durationEstimate2());
	}
	
	//************* seekbar tooltip
	
	function mouseOverHandlerSeek() {
		if(!audioInited) return;
		player_progress_tooltip.css('display', 'block');
		player_progress.bind('mousemove', mouseMoveHandlerSeekTooltip).bind('mouseout', mouseOutHandlerSeek);
		_doc.bind('mouseout', mouseOutHandlerSeek);
	}
	
	function mouseOutHandlerSeek() {
		if(!audioInited) return;
		player_progress_tooltip.css('display', 'none');
		player_progress.unbind('mousemove', mouseMoveHandlerSeekTooltip).unbind('mouseout', mouseOutHandlerSeek);
		_doc.unbind('mouseout', mouseOutHandlerSeek);
	}
	
	function mouseMoveHandlerSeekTooltip(e){
		var s = e.pageX - player_progress.offset().left;
		if(s<0) s=0;
		else if(s>seekBarSize) s=seekBarSize;
		
		var center = s + parseInt(player_progress.css('left'), 10) - player_progress_tooltip.width() / 2;
		player_progress_tooltip.css('left', center + 'px');
		//console.log(pos);
		
		var newPercent = Math.max(0, Math.min(1, s / seekBarSize));
		var value=newPercent * durationEstimate();
		//console.log(newPercent,' , ',  durationEstimate());
		//console.log(sm_curentSound.duration, ' , ', sm_curentSound.position, ' , ', sm_curentSound.bytesTotal, ' , ', sm_curentSound.bytesLoaded);
		if(!isNaN(value)){
			player_progress_tooltip.find('p').html(formatCurrentTime(value)+seekTooltipSeparator+formatDuration(sm_curentSound.duration/1000));
		}
	}
	
	//************** end seekbar
	
	function checkScroll(){
		//console.log('checkScroll');
		
		if(!scrollPaneApi){
			scrollPaneApi = playlist_inner.jScrollPane().data().jsp;
			playlist_inner.bind('jsp-initialised',function(event, isScrollable){
				//console.log('Handle jsp-initialised', this,'isScrollable=', isScrollable);
			}).jScrollPane({
				verticalDragMinHeight: 30,
				verticalDragMaxHeight: 40
			});
		}else{
			scrollPaneApi.reinitialise();
			scrollPaneApi.scrollToY(0);
		}
	}
	
	//************ playlist
	
	function initButtons(){
		
		var buttonArr=[componentWrapper.find('.controls_next'),
		componentWrapper.find('.controls_prev'),
		componentWrapper.find('.controls_toggle'),
		componentWrapper.find('.player_volume'),
		componentWrapper.find('.player_loop'),
		componentWrapper.find('.player_shuffle')];

		var btn,len = buttonArr.length,i=0;
		for(i;i<len;i++){
			btn = $(buttonArr[i]).css('cursor', 'pointer').bind('click', clickControls);
			if(useRollovers && !isMobile){
				btn.bind('mouseover', overControls).bind('mouseout', outControls);
			}
		}
	}
	
	function togglePlayBack(via_button){
		//console.log('togglePlayBack');
		 if(mediaPlaying){
			 soundManager.pause(sm2_sound_id);
			 mediaPlaying=false;
			 if(via_button){
				componentWrapper.find('.controls_toggle').find('img').attr('src', !isMobile ? playOnBtnUrl : playBtnUrl); 
			 }else{
				componentWrapper.find('.controls_toggle').find('img').attr('src', isMobile ? playOnBtnUrl : playBtnUrl);//when we use api
			 }
		 }else{
			 soundManager.play(sm2_sound_id);
			 mediaPlaying=true;
			 if(via_button){
			 	componentWrapper.find('.controls_toggle').find('img').attr('src', !isMobile ? pauseOnBtnUrl : pauseBtnUrl);	
			 }else{
				componentWrapper.find('.controls_toggle').find('img').attr('src', isMobile ? pauseOnBtnUrl : pauseBtnUrl);//when we use api	 
			 }
		 }
		 return false;
	}
	
	function clickPlaylistItem(e){
		if(!componentInited || playlistTransitionOn) return;
		//console.log('clickPlaylistItem');
		if (!e) var e = window.event;
		if(e.cancelBubble) e.cancelBubble = true;
		else if (e.stopPropagation) e.stopPropagation();
		
		var currentTarget = $(e.currentTarget);
		var id = currentTarget.attr('data-id');
		//console.log('id = ', id);
		
		enableActiveItem();
		playlistManager.processPlaylistRequest(id);
		
		return false;
	}
	
	function enableActiveItem(){
		//console.log('enableActiveItem');
		if(playlistManager.getCounter()!=-1){
			var _item = $(aArr[playlistManager.getCounter()]);
			if(_item){
				_item.removeClass('playlistSelected').addClass('playlistNonSelected');
				if(typeof playlistItemEnabled !== 'undefined')playlistItemEnabled(sm2_sound_id, _item);//callback
				else if(typeof parent.playlistItemEnabled !== 'undefined')parent.playlistItemEnabled(sm2_sound_id, _item);//callback
			}
		}
	}
	
	function disableActiveItem(){
		//console.log('disableActiveItem');
		var _item = $(aArr[playlistManager.getCounter()]);
		if(_item){
			_item.removeClass('playlistNonSelected').addClass('playlistSelected');
			if(typeof playlistItemDisabled !== 'undefined')playlistItemDisabled(sm2_sound_id, _item);//callback
			else if(typeof parent.playlistItemDisabled !== 'undefined')parent.playlistItemDisabled(sm2_sound_id, _item);//callback
		}
	}
	
	function clickControls(e){
		if(!componentInited || playlistTransitionOn) return;
		
		if (!e) var e = window.event;
		if(e.cancelBubble) e.cancelBubble = true;
		else if (e.stopPropagation) e.stopPropagation();
		
		var currentTarget = $(e.currentTarget);
		var c=currentTarget.attr('class');
		
		if(c=='controls_prev'){
			if(!audioInited) return;
			enableActiveItem();
			playlistManager.advanceHandler(-1, true);
		}else if(c=='controls_toggle'){
			if(!audioInited) return;
			togglePlayBack(true);
		}else if(c=='controls_next'){
			if(!audioInited) return;
			enableActiveItem();
			playlistManager.advanceHandler(1, true);
		}else if(c=='player_volume'){
			if(isIOS){
				//if(useAlertMessaging) alert('Setting volume on ' + mobile_type + ' is not possible with javascript! Use physical button on your ' + mobile_type + ' to adjust volume.');
				//return false;
			}
			if(!_muteOn){
				_lastVolume = _defaultVolume;//remember last volume
				_defaultVolume = 0;//set mute on (volume to 0)
				_muteOn = true;
			}else{
				_defaultVolume = _lastVolume;//restore last volume
				_muteOn = false;
			}
			setVolume();
		}else if(c=='player_loop'){
			if(loopingOn){
				componentWrapper.find('.player_loop').find('img').attr('src', loopBtnUrl);
				loopingOn=false;
			}else{
				componentWrapper.find('.player_loop').find('img').attr('src', loopOnBtnUrl);
				loopingOn=true;
			}
			playlistManager.setLooping(loopingOn);
		}else if(c=='player_shuffle'){
			if(randomPlay){
				componentWrapper.find('.player_shuffle').find('img').attr('src', shuffleBtnUrl);
				randomPlay=false;
			}else{
				componentWrapper.find('.player_shuffle').find('img').attr('src', shuffleOnBtnUrl);
				randomPlay=true;
			}
			playlistManager.setRandom(randomPlay);
		}
		return false;
	}

	function overControls(e){
		if(!componentInited) return;
		
		if (!e) var e = window.event;
		if(e.cancelBubble) e.cancelBubble = true;
		else if (e.stopPropagation) e.stopPropagation();
		
		var currentTarget = $(e.currentTarget);
		var c=currentTarget.attr('class');
		
		if(c=='controls_prev'){
			componentWrapper.find('.controls_prev').find('img').attr('src', prevOnBtnUrl);
		}else if(c=='controls_toggle'){
			if(mediaPlaying){
				componentWrapper.find('.controls_toggle').find('img').attr('src', pauseOnBtnUrl);	
			}else{
				componentWrapper.find('.controls_toggle').find('img').attr('src', playOnBtnUrl);	
			}
		}else if(c=='controls_next'){
			componentWrapper.find('.controls_next').find('img').attr('src', nextOnBtnUrl);
		}else if(c=='player_volume'){
			if(_defaultVolume > 0){
				componentWrapper.find('.player_volume').find('img').attr('src', volumeOnBtnUrl);
			}else{
				componentWrapper.find('.player_volume').find('img').attr('src', muteOnBtnUrl);
			}
		}else if(c=='player_loop'){
			if(loopingOn){
				componentWrapper.find('.player_loop').find('img').attr('src', loopBtnUrl);
			}else{
				componentWrapper.find('.player_loop').find('img').attr('src', loopOnBtnUrl);
			}
		}else if(c=='player_shuffle'){
			if(randomPlay){
				componentWrapper.find('.player_shuffle').find('img').attr('src', shuffleBtnUrl);
			}else{
				componentWrapper.find('.player_shuffle').find('img').attr('src', shuffleOnBtnUrl);
			}
		}
		return false;
	}
	
	function outControls(e){
		if(!componentInited) return;
		
		if (!e) var e = window.event;
		if(e.cancelBubble) e.cancelBubble = true;
		else if (e.stopPropagation) e.stopPropagation();
		
		var currentTarget = $(e.currentTarget);
		var c=currentTarget.attr('class');
		
		if(c=='controls_prev'){
			componentWrapper.find('.controls_prev').find('img').attr('src', prevBtnUrl);
		}else if(c=='controls_toggle'){
			if(mediaPlaying){
				componentWrapper.find('.controls_toggle').find('img').attr('src', pauseBtnUrl);	
			}else{
				componentWrapper.find('.controls_toggle').find('img').attr('src', playBtnUrl);	
			}
		}else if(c=='controls_next'){
			componentWrapper.find('.controls_next').find('img').attr('src', nextBtnUrl);
		}else if(c=='player_volume'){
			if(_defaultVolume > 0){
				componentWrapper.find('.player_volume').find('img').attr('src', volumeBtnUrl);
			}else{
				componentWrapper.find('.player_volume').find('img').attr('src', muteBtnUrl);
			}
		}else if(c=='player_loop'){
			if(loopingOn){
				componentWrapper.find('.player_loop').find('img').attr('src', loopOnBtnUrl);
			}else{
				componentWrapper.find('.player_loop').find('img').attr('src', loopBtnUrl);
			}
		}else if(c=='player_shuffle'){
			if(randomPlay){
				componentWrapper.find('.player_shuffle').find('img').attr('src', shuffleOnBtnUrl);
			}else{
				componentWrapper.find('.player_shuffle').find('img').attr('src', shuffleBtnUrl);
			}
		}
		return false;
	}
	
	//******************
	
	function destroyAudio() {
		if(!lastPlaylist) return;
		if(playlistManager.getCounter() == -1) return;
		//console.log('destroyAudio');
		cleanAudio();
		resetData();
		componentWrapper.find('.controls_toggle').find('img').attr('src', playBtnUrl);
		enableActiveItem();
	}
	
	function destroyPlaylist(){
		//console.log('destroyPlaylist');
		cleanAudio();
		resetData();
		var i = 0;
		for(i;i<_playlistLength;i++){
			$(aArr[i]).unbind('click', clickPlaylistItem);
		}
		if(lastPlaylist != 'hidden'){
			lastPlaylist.remove();	
		}
		lastPlaylist=null;
		_currentInsert=null;
		
		playlistManager.reSetCounter();
		
		playlistDataArr=[];
		aArr=[];
		liArr=[];
		if(isMobile)autoPlay=false;//reset after first playlist, needed for mobile!
		
		if(scrollPaneApi)scrollPaneApi.reinitialise();//hide scrollbar
	}
	
	function setPlaylist(ap){
		showPreloader();
		playlist_inner.css('opacity',0);
		
		if(lastPlaylist){
			destroyPlaylist();
		}
		_playlistLoaded=false;
		
		//get new playlist
		var playlist = playlist_holder.find(ap).css('display','block').clone();
		if(playlist.length==0){
			if(useAlertMessaging) alert('Failed to select playlist! Make sure that element: "' + ap + '" exist in playlist list! Quitting.');
			return;	
		}
		
		if(scrollPaneApi){
			playlist.appendTo(scrollPaneApi.getContentPane());
		}else{
			playlist.appendTo(playlist_inner);
		}
		lastPlaylist = playlist;
		
		playlistDataArr=[];
		aArr=[];
		liArr=[];
		
		processPlaylistArr = [];
		
		playlist.find("li[class='playlistItem']").each(function(){
			_item = $(this);
			processPlaylistArr.push(_item);
		});
		
		checkPlaylistProcess();
	}

	function checkPlaylistProcess() {
		//console.log('checkPlaylistProcess');
		if(processPlaylistArr.length){
			_processPlaylistItem();
		}else{
			//console.log('finished processing playlist');
			createNewPlaylist();
		}
	}
	
	function _processPlaylistItem(){
		
		var _item = processPlaylistArr[0];
		playlist_index=_item.index();
		var data_type = _item.attr('data-type').toLowerCase();
		var data_path = _item.attr('data-path');
		processPlaylistDataArr=[];
		
		//console.log('_processPlaylistItem, data_type = ', data_type);
		
		if(data_type == 'local'){
			
			liArr.push(_item);//li
			aArr.push(_item.find("a[class='playlistNonSelected']"));//a
			
			_currentInsert=_item;	
			
			processPlaylistArr.shift();
			checkPlaylistProcess();
			
		}else if(data_type == 'soundcloud') {
		
			_item.remove();//remove original li item that holds the data for creating playlist
			
			soundCloudTrackData(data_path);	
			
		}else if(data_type == 'podcast') {
				
			_item.remove();//remove original li item that holds the data for creating playlist	
				
			//https://developers.google.com/feed/v1/devguide
			//http://stackoverflow.com/questions/5971177/google-feed-api-and-grabbing-tags-and-attributes
			//http://stackoverflow.com/questions/11139065/google-feed-loader-api-ignoring-xml-attributes

			//console.log(data_path);
			var url = 'http://ajax.googleapis.com/ajax/services/feed/load?v=1.0&output=xml&num=250&callback=?&q='+ encodeURIComponent(data_path); 
			
			$.ajax({
				type: 'GET',
				url: url,
				dataType: 'jsonp',
				cache: false
			}).done(function( result ) {
				//console.log(result);
				//console.log(result.responseData.xmlString);
				
				if(!ieBelow9){
					
					feedParser.html(result.responseData.xmlString);//store in case of large amount of data
					var tempArr =  feedParser.get(0).getElementsByTagName('item');
					var i = 0, len = tempArr.length, entry, dur, obj;
					
					for(i; i < len; i++){
						entry = tempArr[i];
						obj={};
						obj.type = 'podcast';
						obj.mp3 = entry.getElementsByTagName('enclosure')[0].getAttribute('url');
						obj.ogg='';//dummy path 
						//console.log(entry.getElementsByTagName('itunes:duration')[0]);
						if(entry.getElementsByTagName('itunes:duration')[0] != undefined){//fix for ios!
							dur = hmsToSecondsOnly(entry.getElementsByTagName('itunes:duration')[0].childNodes[0].nodeValue);
							obj.length = parseInt((dur*1000),10);//we want miliseconds
							//console.log(typeof(obj.length), obj.length);
						}
						obj.title = entry.getElementsByTagName('title')[0].childNodes[0].nodeValue;
						//console.log(obj.url ,obj.title);
						processPlaylistDataArr.push(obj);
					}
					
				}else{
					
					var dom = parseXML(result.responseData.xmlString), _item, obj;
					$(dom).find("item").each(function(){
						_item=$(this);
						//console.log(_item.find('enclosure').attr('url'));
						//console.log(_item.find('title').text());
						
						obj={};
						obj.type = 'podcast';
						obj.mp3 = _item.find('enclosure').attr('url');//THIS FAILS IN JQUERY 1.9 IN IE7/8!
						obj.ogg='';//dummy path 
						obj.title = _item.find('title').text();
						processPlaylistDataArr.push(obj);
					});
				}
				
				createNodes();
			
			}).fail(function(jqXHR, textStatus, errorThrown) {
				
				if(useAlertMessaging) alert('Parse feed error: ' + jqXHR.responseText);
				if(useAlertMessaging) alert('Playlist selection failed: ' + data_path);
				
				processPlaylistArr.shift();
				checkPlaylistProcess();
				
			}); 
			
		}else if(data_type == 'folder'){
			
			_item.remove();//remove original li item that holds the data for creating playlist
			
			var url = 'folder_parser.php';	
			var sub_dirs = true;
			var data = {"dir": data_path, 
						"sub_dirs": sub_dirs};

			$.ajax({
				type: 'GET',
				url: url,
				data: data,
				dataType: "json"
			}).done(function(media) {

				//console.log(media);
				//console.log(media.length, media[0], media[1]);

				var i = 0, len = media.length, entry, obj, title;
				//console.log(len);
				for(i; i < len; i++){
					entry = media[i];
					//console.log(entry);
					
					path = entry.path;
					
					if(path.indexOf('\\')){//replace backward slashes
						path = path.replace(/\\/g,"/");
					}
					//console.log(path);
					
					obj={};
					obj.type = 'folder';
					if(path.indexOf('.mp3')){
						obj.mp3 = path;
					}
					obj.ogg = path.substr(0, path.lastIndexOf('.')) + '.ogg';//asssume ogg file exist with the same name!
					//console.log(obj.mp3, obj.ogg);
					
					//get title without extension
					no_ext = path.substr(0, path.lastIndexOf('.'));
					if (/\//i.test(no_ext)) {
						title = no_ext.substr(no_ext.lastIndexOf('/')+1);
					}else{
						title = no_ext;
					}
					//remove underscores from title 
					title = title.split("_").join(" ");
					//console.log('title = ', title);
					obj.title = title;
					
					processPlaylistDataArr.push(obj);
				}
				createNodes();
		  
		    }).fail(function(jqXHR, textStatus, errorThrown) {
				//alert('Folder process error: ' + jqXHR.responseText);
				
				if(useAlertMessaging) alert("Read folder error! Make sure you run this online or on local server!"); 
				if(useAlertMessaging) alert('Playlist selection failed: ' + data_path);
				
				processPlaylistArr.shift();
				checkPlaylistProcess();
			});
		  
		}else if(data_type == 'xml'){
				
			_item.css('display','none');//hide data li
				
			var url = data_path, str, ul, li, a, type, obj;
			url+="?rand=" + (Math.random() * 99999999);
			$.ajax({
				type: "GET",
				url: url,
				dataType: "html",
				cache: false
			}).done(function(xml) {
			
				$(xml).find("li[class='playlistItem']").each(function(){
					obj = $(this);
					//console.log(obj);
					//console.log(typeof obj);
					type = obj.attr('data-type');
					//console.log('xml-type = ', type);
					
					str = $('<div>').append(obj.clone()).html();//convert object to string
					//console.log(typeof str);
					//console.log(str);
					
					//console.log(htmlDecode(str));
					//str = htmlDecode(str);
					str = str.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
					
					//console.log(str);
					
					if(type == 'local'){//copy local from xml directly to playlist
						
						ul = document.createElement('ul');
						ul.innerHTML = str;
						li = ul.firstChild;
						//console.log(li);
						a = getFirstChild(li);
						//console.log(a);
					
						if(_currentInsert){
							_currentInsert.after($(li));
						}else{
							if(!isNaN(playlist_index) && playlist_index == 0){//fix when xml is first in the playlist followed by local
								$(li).prependTo(lastPlaylist);
							}else{
								$(li).appendTo(lastPlaylist);
							}
						}
						_currentInsert=$(li);
					
						liArr.push($(li));//li
						aArr.push($(a));//a
						
					}else if(type == 'xml'){
						if(useAlertMessaging) alert('You cannot load xml over xml, this feature is not supported. Skipping processing of playlist: ' + str);	
					}else{
						processPlaylistArr.push(obj);
					}
				});
				_item.remove();//remove original li item that holds the data for creating playlist	
				
				processPlaylistArr.shift();
				checkPlaylistProcess();
					
			}).fail(function(jqXHR, textStatus, errorThrown) {
				//alert('XML process error: ' + jqXHR.responseText);
				
				if(useAlertMessaging) alert('Parse xml error! Make sure you run this online or on local server!');
				if(useAlertMessaging) alert('Playlist selection failed: ' + data_path);
				
				processPlaylistArr.shift();
				checkPlaylistProcess();
			});		
			
		}else{
			if(useAlertMessaging) alert('Invalid playlist data-type attribute!');
			return;	
		}
	}
	
	function createNodes(){
		//console.log('createNodes');
		if(processPlaylistDataArr.length){
			var li, a, i=0, len = processPlaylistDataArr.length;
			for(i;i<len;i++){
				
				li = $('<li class= "playlistItem" data-type="'+processPlaylistDataArr[i].type+'" data-mp3Path="'+processPlaylistDataArr[i].mp3+'" data-oggPath="'+processPlaylistDataArr[i].ogg+'"></li>').addClass('playlistItem');
				
				if(!_currentInsert){
					li.appendTo(lastPlaylist);
				}else{
					_currentInsert.after(li);
				}
				_currentInsert=li;//on last one	
				
				if(processPlaylistDataArr[i].length) li.attr('data-length',processPlaylistDataArr[i].length);//ios fix
				a = $('<a class="playlistNonSelected" href="#">'+processPlaylistDataArr[i].title+'</a>').addClass('playlistNonSelected').appendTo(li);
				liArr.push(li);
				aArr.push(a);
			}
			
		}
		processPlaylistArr.shift();
		checkPlaylistProcess();
	}
	
	function createNewPlaylist(){
		//console.log('createNewPlaylist');
		_playlistLength = liArr.length;
		
		//console.log(lastPlaylist);
		
		var i=0, pi, title, counter_title, type, length, mp3, ogg;
		for(i;i<_playlistLength;i++){
			pi = $(aArr[i]).bind('click', clickPlaylistItem).attr('data-id', i);
			
			title = pi.html();
			if(useNumbersInPlaylist){
				counter_title = stringCounter(i) + '. ' + title;
				pi.html(counter_title);
			}
			
			pi = $(liArr[i]);
			mp3='',ogg='',type='', length=undefined;
			if(pi.attr('data-mp3Path') != undefined){
				mp3 = pi.attr('data-mp3Path');
			}
			if(pi.attr('data-oggPath') != undefined){
				ogg = pi.attr('data-oggPath');
			}
			
			if(pi.attr('data-type') != undefined){
				type = pi.attr('data-type');
			}
			
			if(pi.attr('data-length') != undefined){
				length = pi.attr('data-length');
			}
			
			playlistDataArr.push({'id': i, 'type': type, 'title': title, 'mp3Path': mp3,'oggPath': ogg, 'length': length});
		}
		
		playlist_inner.css('opacity',1);
		if(activatePlaylistScroll)checkScroll();
		
		if(!componentInited){
			componentInited=true;
			if(typeof audioPlayerSetupDone !== 'undefined')audioPlayerSetupDone(sm2_sound_id);//callback
			else if(typeof parent.audioPlayerSetupDone !== 'undefined')parent.audioPlayerSetupDone(sm2_sound_id);//callback
		}
		
		if(_playlistLength == 0){
			if(useAlertMessaging) alert('Processing playlist failed. No items in playlist! Quitting.');
			playlistTransitionOn = false;
			hidePreloader();
			return;		
		}
		
		playlistTransitionOn = false;
		hidePreloader();
		
		playlistManager.setPlaylistItems(_playlistLength);
		checkActiveItem();
		
		if(typeof audioPlayerPlaylistLoaded !== 'undefined')audioPlayerPlaylistLoaded(sm2_sound_id);//callback
		else if(typeof parent.audioPlayerPlaylistLoaded !== 'undefined')parent.audioPlayerPlaylistLoaded(sm2_sound_id);//callback
		_playlistLoaded = true;
		
	}
	
	function adjustSongData(i){
		var pi, old_title, title, hidden_playlist = aArr.length ? false : true;
		for(i;i<_playlistLength;i++){
			
			if(!hidden_playlist){
				pi = $(aArr[i]).attr('data-id', i);//reapply data-id for playlist click

				if(useNumbersInPlaylist){
					old_title = pi.html();
					//console.log(old_title, old_title.length);
					title = old_title.substr(old_title.indexOf('.')+2);
					//title = old_title;
					playlistDataArr[i].title = title;//without numbers!
					title = stringCounter(i) + '. ' + title;
					pi.html(title);
				}
			}
			playlistDataArr[i].id = i;
		}
	}
	
	function checkActiveItem() {
		//console.log('checkActiveItem');
		var ai = settings.activeItem;
		if(!isNaN(ai) && ai != -1){
			if(ai<0)ai=0;
			else if(ai > _playlistLength-1)ai = _playlistLength-1;
			playlistManager.setCounter(ai, false);
		}else{
			autoPlay = true;//if no active item on start, we would need to click twice to start playback
		}
	}
	
	function soundCloudTrackData(linkUrl) {
		if(isEmpty(soundcloudApiKey)){
			alert('soundcloudApiKey has not been set! Quitting.');
			
			processPlaylistArr.shift();
			checkPlaylistProcess();
			
			return false;	
		}
		//console.log('soundcloudApiKey=',soundcloudApiKey);
		var url = soundCloudApiUrl(linkUrl, soundcloudApiKey);
		
		$.ajax({
			url: url,
			dataType: 'jsonp',
			cache: false
		}).done(function( data ) {
		
			//console.log('data loaded');
			var obj, len, i;
			if(data.tracks) {
				//console.log('DATA.TRACKS');
				//console.log('data.tracks.length = ', data.tracks.length)
				len = data.tracks.length;
				for(i=0; i < len; i++) {
					//console.log(data.tracks[i].title);
					//console.log(data.tracks[i].stream_url + (/\?/.test(data.tracks[i].stream_url) ? '&' : '?') + 'consumer_key=' + soundcloudApiKey);
					//console.log(data.tracks[i].artwork_url);
					
					obj={};
					obj.type = 'soundcloud';
					if(data.tracks[i].duration){
						//console.log(data.tracks[i].duration);
						obj.length = data.tracks[i].duration;
					}
					obj.thumbUrl = data.tracks[i].artwork_url ? data.tracks[i].artwork_url : '';
					obj.mp3 = data.tracks[i].stream_url + (/\?/.test(data.tracks[i].stream_url) ? '&' : '?') + 'consumer_key=' + soundcloudApiKey;
					obj.ogg='';//dummy path 
					obj.title = data.tracks[i].title;
					processPlaylistDataArr.push(obj);
				}
				createNodes();
					 
			}else if(data.duration) {
				//console.log('DATA.DURATION');
				// a secret link fix, till the SC API returns permalink with secret on secret response
				data.permalink_url = linkUrl;
				//console.log(data.artwork_url);
				//console.log(data.stream_url + (/\?/.test(data.stream_url) ? '&' : '?') + 'consumer_key=' + soundcloudApiKey);
				//console.log(data.title);
				
				obj={};
				obj.type = 'soundcloud';
				if(data.duration){
					//console.log(data.duration);
					obj.length = data.duration;
				}
				obj.thumbUrl = data.artwork_url ? data.artwork_url : '';
				obj.mp3 = data.stream_url + (/\?/.test(data.stream_url) ? '&' : '?') + 'consumer_key=' + soundcloudApiKey;
				obj.ogg='';//dummy path 
				obj.title = data.title;
				processPlaylistDataArr.push(obj);
				
				createNodes();
				
			}else if(data.username) {
				// if user, get his tracks or favorites
				if(/favorites/.test(linkUrl)) {
					//console.log('DATA.USERNAME.FAVOURITES');
					soundCloudTrackData(data.uri + '/favorites');
				}else{
					//console.log('DATA.USERNAME.TRACKS');
					soundCloudTrackData(data.uri + '/tracks');
				}
			}else if($.isArray(data)) {
				//console.log('DATA.ISARRAY');
				len = data.length;
				for(i=0; i < len; i++) {
					//console.log(data[i].artwork_url);
					//console.log(data[i].stream_url + (/\?/.test(data[i].stream_url) ? '&' : '?') + 'consumer_key=' + soundcloudApiKey);
					//console.log(data[i].title);
					
					obj={};
					obj.type = 'soundcloud';
					if(data[i].duration){
						//console.log(data[i].duration);
						obj.length = data[i].duration;
					}
					obj.thumbUrl = data[i].artwork_url ? data[i].artwork_url : '';
					obj.mp3 = data[i].stream_url + (/\?/.test(data[i].stream_url) ? '&' : '?') + 'consumer_key=' + soundcloudApiKey;
					obj.ogg='';//dummy path 
					obj.title = data[i].title;
					processPlaylistDataArr.push(obj);
				}
				createNodes();
			}
		
		}).fail(function(jqXHR, textStatus, errorThrown) {
			//alert('Soundcloud process error: ' + jqXHR.responseText);
			if(useAlertMessaging) alert("SoundCloud process error!"); 
			playlistTransitionOn = false;
		});
	};
	
	// convert a SoundCloud resource URL to an API URL
	function soundCloudApiUrl(url, soundcloudApiKey) {
		var useSandBox = false;
		var domain = useSandBox ? 'sandbox-soundcloud.com' : 'soundcloud.com'
		return (/api\./.test(url) ? url + '?' : 'http://api.' + domain +'/resolve?url=' + url + '&') + 'format=json&consumer_key=' + soundcloudApiKey +'&callback=?';
	};	
	
	//***************** audio
		
	function findMedia(){
		//console.log('findMedia');
		cleanAudio();
		sm_createSound();
	}
		
	function cleanAudio(){
		//console.log('cleanAudio');
		if(sm_curentSound){
			soundManager.destroySound(sm2_sound_id);
			sm_curentSound=null;
		}
		resetData2();
		//reset
		nullMetaData=false;
		mediaPlaying=false;
		audioInited=false;
		sound_started=false;
	}
	
	function _onLoading(){
		//console.log(this.bytesLoaded, this.bytesTotal);
		var percent = this.bytesLoaded/this.bytesTotal;
		if(!isNaN(percent)){
			load_progress.width(percent * seekBarSize);	
		}
	}
	
	function _onPlaying(){
		//console.log(this.position, this.duration);
		//console.log(sm_curentSound.duration);
		//console.log(sm_curentSound.position);
		//console.log(sm_curentSound.bytesTotal);
		//console.log(sm_curentSound.bytesLoaded);
		
		var p = this.position/1000, d;
		if(this.duration){
			d = this.duration/1000;
		}else if(soundLength && soundLength != 0){
			d = soundLength/1000;
		}
		
		if(!isNaN(p))player_mediaTime_current.html(formatCurrentTime(p)+mediaTimeSeparator);
		
		//console.log(p, d);
		if(!isNaN(p) && !isNaN(d)){
			//player_mediaTime.html(formatCurrentTime(p)+formatDuration(d));
			player_mediaTime_current.html(formatCurrentTime(p)+mediaTimeSeparator);
			player_mediaTime_total.html(formatDuration(d));
			
			if(!seekBarDown) play_progress.width((p/d)*seekBarSize);
			
			nullMetaData=false;
		}else{
			nullMetaData=true;
		}
	}
	
	function _onSuspend(){}
	
	function durationEstimate(){
		if(sm_curentSound.duration){
			return parseInt((sm_curentSound.bytesLoaded/sm_curentSound.bytesTotal)*(sm_curentSound.duration/1000));
		}else if(soundLength && soundLength != 0){
			return parseInt((sm_curentSound.bytesLoaded/sm_curentSound.bytesTotal)*(soundLength/1000));
		}
	}
	
	function durationEstimate2(){
		if(sm_curentSound.duration){
			return parseInt((sm_curentSound.bytesLoaded/sm_curentSound.bytesTotal)*(sm_curentSound.duration));
		}else if(soundLength && soundLength != 0){
			return parseInt((sm_curentSound.bytesLoaded/sm_curentSound.bytesTotal)*(soundLength));
		}
	}
	
	function _onFinish(){
		if(typeof audioPlayerSoundEnd !== 'undefined')audioPlayerSoundEnd(playlistManager.getCounter());//callback
		else if(typeof parent.audioPlayerSoundEnd !== 'undefined')parent.audioPlayerSoundEnd(playlistManager.getCounter());//callback
		enableActiveItem();
		playlistManager.advanceHandler(1, true);
	}
	
	function _onLoad(state){
		if(!state) {
			var file = mp3Support ? mp3Path : oggPath;
			//if(useAlertMessaging) alert('Audio file could not be loaded! :', file);	
		}
	}
	
	function _onPlay(){//fires only first time sound is played
		//console.log('_onPlay');
		if(!sound_started){
			if(typeof audioPlayerSoundStart !== 'undefined')audioPlayerSoundStart(playlistManager.getCounter());//callback
			else if(typeof parent.audioPlayerSoundStart !== 'undefined')parent.audioPlayerSoundStart(playlistManager.getCounter());//callback
			sound_started=true;	
		}else{
			if(typeof audioPlayerSoundPlay !== 'undefined')audioPlayerSoundPlay(sm2_sound_id);//callback
			else if(typeof parent.audioPlayerSoundPlay !== 'undefined')parent.audioPlayerSoundPlay(sm2_sound_id);//callback
		}
	}
	
	function _onResume(){//fires other times sound is played (resumed)
		//console.log('_onResume');
		if(typeof audioPlayerSoundPlay !== 'undefined')audioPlayerSoundPlay(sm2_sound_id);//callback
		else if(typeof parent.audioPlayerSoundPlay !== 'undefined')parent.audioPlayerSoundPlay(sm2_sound_id);//callback
	}
	
	function _onPause(){
		//console.log('_onPause');
		if(typeof audioPlayerSoundPause !== 'undefined')audioPlayerSoundPause(sm2_sound_id);//callback
		else if(typeof parent.audioPlayerSoundPause !== 'undefined')parent.audioPlayerSoundPause(sm2_sound_id);//callback
	}
	
	function sm_createSound(){
		//console.log('sm_createSound');
		//console.log('mp3Path: ', mp3Path, ' , oggPath: ', oggPath); 
			 
		sm_curentSound = soundManager.createSound({
			  id: sm2_sound_id,
			  url:[
				mp3Path,
				oggPath
			  ],
			  autoLoad: autoLoad,
			  autoPlay: autoPlay,
			  volume: _defaultVolume*100,
			  whileloading: _onLoading,
			  whileplaying: _onPlaying,
			  onfinish: _onFinish,
			  onload: _onLoad,
			  onsuspend: _onSuspend,
			  onplay: _onPlay,
			  onpause: _onPause,
			  onresume: _onResume
		});
		 
	    if(autoPlay){
			mediaPlaying=true;
			componentWrapper.find('.controls_toggle').find('img').attr('src', pauseBtnUrl);	
		}else{
			mediaPlaying=false;
			componentWrapper.find('.controls_toggle').find('img').attr('src', playBtnUrl);
		}
		setVolume();
		audioInited=true;
		autoPlay=true;//set autoplay after first play
		
		//console.log('sm_curentSound.isHTML5()=', sm_curentSound.isHTML5);
		//alert('sm_curentSound.isHTML5 = ' + sm_curentSound.isHTML5); 
	}

	initAudio();
	
	function initAudio(){
		//console.log('initAudio');
		soundManager.onready(function(){
			//console.log('soundManager.onready');
			initButtons();
			if(!isEmpty(activePlaylist)){
				setPlaylist(activePlaylist);
			}else{
				if(!componentInited){
					playlistTransitionOn=false;
					componentInited=true;
					if(typeof audioPlayerSetupDone !== 'undefined')audioPlayerSetupDone(sm2_sound_id);//callback
					else if(typeof parent.audioPlayerSetupDone !== 'undefined')parent.audioPlayerSetupDone(sm2_sound_id);//callback
					hidePreloader();
				}	
			}
		});
		
		soundManager.ontimeout(function(status){
			//console.log('soundManager.ontimeout');
		    //Hrmm, SM2 could not start. Flash blocker involved? Show an error, etc.?
			if(useAlertMessaging){
				if(useAlertMessaging){
					alert('SM2 failed to start. Flash missing, blocked or security error? Status: '+ status.error.type);
					alert('SM2 could be trying to use flash. Make sure you test this ONLINE or ON LOCAL SERVER!');
				}
			} 
		});
		
		//http://www.schillmania.com/projects/soundmanager2/doc/
	}
	
	//***************** helper functions
	
	function hidePreloader(){
		preloader.css('display','none');
	}
	function showPreloader(){
		preloader.css('display','block');
	}
	
	function resetData(){
	  if(textScroller && useSongNameScroll){
		   textScroller.deactivate();
	  }
	  player_mediaName.html(defaultArtistData);
	  player_mediaTime_current.html(songTimeCurr);
	  player_mediaTime_total.html(songTimeTot);
	  play_progress.width(0);
	  load_progress.width(0);
	  componentWrapper.find('.controls_toggle').find('img').attr('src', playBtnUrl);
	  //console.log('resetData');
	}
	
	function resetData2(){
	  if(!autoSetSongTitle){//reset if we manually set song title
		 if(textScroller)textScroller.deactivate();
		 player_mediaName.html(defaultArtistData);
	  }
	  player_mediaTime_current.html(songTimeCurr);
	  player_mediaTime_total.html(songTimeTot);
	  play_progress.width(0);
	  load_progress.width(0);
	  componentWrapper.find('.controls_toggle').find('img').attr('src', playBtnUrl);
	  //console.log('resetData2');
	}	
	
	function formatCurrentTime(seconds) {
		seconds = Math.round(seconds);
		minutes = Math.floor(seconds / 60);
		minutes = (minutes >= 10) ? minutes : "0" + minutes;
		seconds = Math.floor(seconds % 60);
		seconds = (seconds >= 10) ? seconds : "0" + seconds;
		return minutes + ":" + seconds;
	}
	
	function formatDuration(seconds) {
		seconds = Math.round(seconds);
		minutes = Math.floor(seconds / 60);
		minutes = (minutes >= 10) ? minutes : "0" + minutes;
		seconds = Math.floor(seconds % 60);
		seconds = (seconds >= 10) ? seconds : "0" + seconds;
		//return " - " + minutes + ":" + seconds;
		return minutes + ":" + seconds;
	}
	
	function getTitle(c){
		//console.log('getTitle');
		if(isEmpty(playlistDataArr[c].title)){
			player_mediaName.html(defaultArtistData);//restore original
			return false;	
		}
		if(useNumbersInPlaylist){
			return stringCounter(playlistDataArr[c].id) + '. ' + playlistDataArr[c].title;	
		}else{
			return playlistDataArr[c].title;	
		}
	}
	
	function stringCounter(i) {
		var s;
		if(i < 9){
			s = "0" + (i + 1);
		}else{
			s = i + 1;
		}
		return s;
	}
	
	function preventSelect(arr){
		$(arr).each(function() {           
		$(this).attr('unselectable', 'on')
		   .css({
			   '-moz-user-select':'none',
			   '-webkit-user-select':'none',
			   'user-select':'none'
		   })
		   .each(function() {
			   this.onselectstart = function() { return false; };
		   });
		});
	}	
	
	//contains in array
	function contains(arr, obj) {
		var i = arr.length;
		while (i--) {
		   if(RegExp(arr[i]).test(obj)){
			   //console.log(arr[i], obj);
			   return true;
		   }
		}
		return false;
	}
	
	function isEmpty(str) {
	    return str.replace(/^\s+|\s+$/g, '').length == 0;
	}
	
	function timeToSec(time) { // time must be a string type: "HH:mm:ss"
		var a = time.split(':');
		return seconds = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]); 
	}
	
	function hmsToSecondsOnly(str) {//This function handels "HH:MM:SS" as well as "MM:SS" or "SS".
		var p = str.split(':'),
			s = 0, m = 1;
		while (p.length > 0) {
			s += m * parseInt(p.pop());
			m *= 60;
		}
		return s;
	}
	
	function parseXML(xml) {
		if(window.ActiveXObject && window.GetObject) {
			var dom = new ActiveXObject('Microsoft.XMLDOM');
			dom.loadXML(xml);
			return dom;
		}
		if(window.DOMParser){
			return new DOMParser().parseFromString(xml, 'text/xml');
		}else{
			throw new Error('No XML parser available');
		}
	}
	
	function getFirstChild(el){
	    var firstChild = el.firstChild;
	    while(firstChild != null && firstChild.nodeType == 3){ // skip TextNodes
		  firstChild = firstChild.nextSibling;
	    }
	    return firstChild;
	}
	
	function htmlDecode(value){
	    return $('<div/>').html(value).text();
	}
	
	// ******************************** PUBLIC API **************** //

	this.playAudio = function() {
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		if(!lastPlaylist) return false;
		if(mediaPlaying) return false;
		if(!audioInited) return false;
		soundManager.play(sm2_sound_id);
		componentWrapper.find('.controls_toggle').find('img').attr('src', pauseBtnUrl);	
		mediaPlaying=true;
		audioInited=true;
	}
	
	this.pauseAudio = function() {
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		if(!lastPlaylist) return false;
		if(!mediaPlaying) return false;
		if(!audioInited) return false;
		soundManager.pause(sm2_sound_id);
		componentWrapper.find('.controls_toggle').find('img').attr('src', playBtnUrl);	
		mediaPlaying=false;
	}
	
	this.toggleAudio = function() {
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		if(!lastPlaylist) return false;
		if(!audioInited) return false;
		togglePlayBack();
	}
	
	this.stopAudio = function() {
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		if(!lastPlaylist) return false;
		if(!audioInited) return false;
		soundManager.stop(sm2_sound_id);
		resetData2();
		mediaPlaying=false;
	}
	
	this.nextAudio = function() {
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		if(!lastPlaylist) return false;
		enableActiveItem();
		playlistManager.advanceHandler(1, true);
	}
	
	this.previousAudio = function() {
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		if(!lastPlaylist) return false;
		enableActiveItem();
		playlistManager.advanceHandler(-1, true);
	}
	
	this.loadAudio = function(value) {
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		if(!lastPlaylist) return false;
		
		if(typeof(value) === 'string'){
			
			var i = 0, found = false;
			for(i;i<_playlistLength;i++){//find song name and counter
				//console.log(value, value.length, playlistDataArr[i].title, playlistDataArr[i].title.length);
				if(value == playlistDataArr[i].title){
					value = i;
					found=true;
					break;	
				}
			}
			if(!found){
				if(useAlertMessaging) alert('Track with name "' + value + '" doesnt exist. Load audio failed.');
				return false;	
			}
			
		}else if(typeof(value) === 'number'){
			
			if(value<0){
				if(useAlertMessaging) alert('Invalid track number. Track number  "' + value + '" doesnt exist. Load audio failed.');
				return false;
			}
			else if(value > _playlistLength-1){
				if(useAlertMessaging) alert('Invalid track number. Track number  "' + value + '" doesnt exist. Load audio failed.');
				return false;
			}
			
		}else{
			if(useAlertMessaging) alert('Load audio method requires either a track number or a track title to load. Load audio failed.');
			return false;	
		}
		
		enableActiveItem();
		playlistManager.processPlaylistRequest(value);
	}
	
	//************** physical playlist
	
	this.loadPlaylist = function(playlist) {
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		if(typeof(playlist) === 'undefined'){
			if(useAlertMessaging) alert('loadPlaylist method requires playlist parameter. loadPlaylist failed.');
			return false;
		}
		playlistTransitionOn=true;
		setPlaylist(playlist);
	}
	
	this.addTrack = function(track, position) {
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		
		if(typeof(track) === 'undefined'){
			if(useAlertMessaging) alert('addTrack method requires track parameter. AddTrack failed.');
			return false;
		}
		
		var len = 1;	
		
		if(typeof(track) === 'string'){
		}
		else if(track instanceof Array){
			len = track.length;
		}
		else{
			if(useAlertMessaging) alert('addTrack method requires track as string or array parameter. AddTrack failed.');
			return false;
		}
		
		if(lastPlaylist && lastPlaylist == 'hidden'){//needs to be here, above bottom if(lastPlaylist){ statement, so that lastPlaylist becomes null again!
			destroyPlaylist();
		}
		
		var i = 0, mp3, ogg, ul, li, a, _li, _a, title, trackData, end_insert, counter_title, start_pos, num_insert, counter_add=0, failed, _track;
		
		if(lastPlaylist){
			if(typeof(position) !== 'undefined'){
				//console.log(position, _playlistLength);
				if(position<0){
					if(useAlertMessaging) alert('Invalid position to insert track to. Position number "' + position + '" doesnt exist. AddTrack failed.');
					return false;
				}
				else if(position > _playlistLength-1){
					if(useAlertMessaging) alert('Invalid position to insert track to. Position number "' + position + '" doesnt exist. AddTrack failed.');
					return false;
				}
			}else{
				end_insert=true;
				position = _playlistLength-1;	
			}
		}else{//first time create playlist from addTrack method
		
			if(typeof(position) !== 'undefined'){
				if(position!=0){
					if(useAlertMessaging) alert('Invalid position to insert track to. Position number "' + position + '" doesnt exist. AddTrack failed.');
					return false;
				}
			}else{
				position=0;
			}
			end_insert=true;
		
			showPreloader();
			//create playlist node
			var playlist_id = 'playlist' + Math.floor((Math.random()*9999));
			var playlist_ul = $('<ul id = '+playlist_id+'></ul>');
			if(scrollPaneApi){
				playlist_ul.appendTo(scrollPaneApi.getContentPane());
			}else{
				playlist_ul.appendTo(playlist_inner);
			}
		}
		
		//console.log('position= ', position);
		start_pos = position;
			
		for(i; i < len; i++){
			
			//create playlist item node	
			ul = document.createElement('ul');
			_track = len > 1 ? track[i] : track;
			ul.innerHTML = _track;
			li = ul.firstChild;
			//console.log(li);
			a = ul.firstChild.firstChild;
			//console.log(a);
			
			_li = $(li);
			_a = $(a);
			
			if(_li.attr('data-type') != undefined && _li.attr('data-type') != 'local'){//only local track supported in this method (no podcast, soundcloud)
				if(useAlertMessaging)alert('addTrack method supports local type tracks only. addTrack "' + _track + '" failed.');
				continue;
			}
			if(_li.attr('data-mp3Path') != undefined){
				mp3 = _li.attr('data-mp3Path');
			}else{
				if(useAlertMessaging)alert('addTrack method requires "data-mp3Path" attributte. addTrack "' + _track + '" failed.');
				continue;
			}
			
			ogg='';
			if(_li.attr('data-oggPath') != undefined){
				ogg = _li.attr('data-oggPath');
			}
		
			//insert node in playlist
			if(lastPlaylist){
				if(end_insert){
					liArr[position].after(_li);
				}else{
					liArr[position].before(_li);
				}
			}else{
				playlist_ul.append(_li);
			}
			
			num_insert = end_insert ? position+1 : position;
			liArr.splice(num_insert, 0, _li);//li
			aArr.splice(num_insert, 0, _a);//a
			
			//add click to track
			_a.bind('click', clickPlaylistItem);
			
			title = _a.html();
			if(useNumbersInPlaylist){
				counter_title = stringCounter(position) + '. ' + title;
				_a.html(counter_title);
			}
			
			trackData = {'id': position, 'type': 'local', 'title': title, 'mp3Path': mp3,'oggPath': ogg, 'length': undefined};
			playlistDataArr.splice(num_insert, 0, trackData);
			
			if(len > 1)position+=1;
			
			counter_add++;
			
		}
		_playlistLength = playlistDataArr.length;
		//console.log(_playlistLength);
		
		adjustSongData(0);
		
		var current_counter = playlistManager.getCounter();
		playlistManager.setPlaylistItems(_playlistLength, false);
		//console.log(start_pos, current_counter);
		if(start_pos <= current_counter){
			if(!end_insert)	playlistManager.reSetCounter(current_counter+counter_add);//counter_add - some tracks might fail so we dont use 'len'
		}
		
		hidePreloader();
		playlist_inner.css('opacity',1);
		if(activatePlaylistScroll)checkScroll();
		
		//console.log(playlistDataArr);	
		
		if(!lastPlaylist){
			lastPlaylist = playlist_ul;
			checkActiveItem();	
		}
	}
	
	//********** non physical playlist
	
	this.inputAudio = function(track, position) {
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		
		if(typeof(track) === 'undefined'){
			if(useAlertMessaging) alert('inputAudio method requires track parameter. inputAudio failed.');
			return false;
		}
		
		var len = 1;
		
		if(track instanceof Object && !(track instanceof Array)){
			//console.log('object');
		}
		else if(track instanceof Object && track instanceof Array){
			//console.log('array');
			len = track.length;
		}
		else{
			if(useAlertMessaging) alert('inputAudio method requires track as object or array parameter. inputAudio failed.');
			return false;
		}
		
		if(lastPlaylist && lastPlaylist != 'hidden'){//needs to be here, above bottom if(lastPlaylist){ statement, so that lastPlaylist becomes null again!
			destroyPlaylist();
		}
		
		var i = 0, mp3, ogg, ul, li, a, _li, _a, title, trackData, end_insert, counter_title, start_pos, num_insert, counter_add=0, failed, _track;//
		
		if(lastPlaylist){
			if(typeof(position) !== 'undefined'){
				//console.log(position, _playlistLength);
				if(position<0){
					if(useAlertMessaging) alert('Invalid position to insert track to. Position number "' + position + '" doesnt exist. inputAudio failed.');
					return false;
				}
				else if(position > _playlistLength-1){
					if(useAlertMessaging) alert('Invalid position to insert track to. Position number "' + position + '" doesnt exist. inputAudio failed.');
					return false;
				}
			}else{
				end_insert=true;
				position = _playlistLength-1;	
			}
		}else{
			if(typeof(position) !== 'undefined'){
				if(position!=0){
					if(useAlertMessaging) alert('Invalid position to insert track to. Position number "' + position + '" doesnt exist. AddTrack failed.');
					return false;
				}
			}else{
				position=0;
			}
			end_insert=true;
			_playlistLength=0;
		}
		
		//console.log('position= ', position);
		start_pos = position;
			
		for(i;i<len;i++){
				
			_track = len > 1 ? track[i] : track;
			
			if(_track.mp3){
				mp3 = _track.mp3;
			}else{
				if(useAlertMessaging) alert('inputAudio method requires mp3 parameter. inputAudio "' + _track + '" failed.');
				continue;
			}
			ogg='';
			if(_track.ogg){
				ogg = _track.ogg;
			}
			title='';
			if(_track.title){
				title = _track.title;
			}
			
			num_insert = end_insert ? position+1 : position;
			
			trackData = {'id': position, 'type': 'local_hidden', 'title': title, 'mp3Path': mp3,'oggPath': ogg, 'length': undefined};
			playlistDataArr.splice(num_insert, 0, trackData);
			
			if(len>1)position+=1;
			
			counter_add++;
		}
			
		_playlistLength = playlistDataArr.length;
		//console.log(_playlistLength);
		
		adjustSongData(0);
		
		var current_counter = playlistManager.getCounter();
		playlistManager.setPlaylistItems(_playlistLength, false);
		//console.log(start_pos, current_counter);
		if(start_pos <= current_counter){
			if(!end_insert)	playlistManager.reSetCounter(current_counter+counter_add);//counter_add - some tracks might fail so we dont use 'len'
		}
		
		playlistTransitionOn = false;
		
		if(!lastPlaylist){
			lastPlaylist = 'hidden';
			checkActiveItem();	
		}
		
		//console.log(playlistDataArr);
		//console.log('playlistManager.getCounter(); = ', playlistManager.getCounter());
	}
	
	//************** end non physical playlist
	
	this.removeTrack = function(track) {
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		if(!lastPlaylist) return false;
		
		if(typeof(track) === 'string'){
			
			var i = 0, found = false;
			for(i;i<_playlistLength;i++){//find song name and counter
				//console.log(track, track.length, playlistDataArr[i].title, playlistDataArr[i].title.length);
				if(track == playlistDataArr[i].title){
					track = i;
					found=true;
					break;	
				}
			}
			if(!found){
				if(useAlertMessaging) alert('Track with name "' + track + '" doesnt exist. RemoveTrack failed.');
				return false;	
			}
			
		}else if(typeof(track) === 'number'){
			
			if(track<0){
				if(useAlertMessaging) alert('Invalid track number. Track number  "' + track + '" doesnt exist. RemoveTrack failed.');
				return false;
			}
			else if(track > _playlistLength-1){
				if(useAlertMessaging) alert('Invalid track number. Track number  "' + track + '" doesnt exist. RemoveTrack failed.');
				return false;
			}
			
		}else{
			if(useAlertMessaging) alert('RemoveTrack method requires either a track number or a track title to remove. RemoveTrack failed.');
			return false;	
		}
		
		if(lastPlaylist != 'hidden'){
			liArr[track].remove();
			liArr.splice(track,1);
			aArr.splice(track,1);
		}
		playlistDataArr.splice(track,1);
		_playlistLength = playlistDataArr.length;
		//console.log(_playlistLength);
		
		if(_playlistLength > 0){
			
			adjustSongData(0);
			
			var current_counter = playlistManager.getCounter();
			if(track == current_counter){//remove number equal to current counter
				destroyAudio();	
				playlistManager.setPlaylistItems(_playlistLength);
				//counter resets to -1
			}else{
				playlistManager.setPlaylistItems(_playlistLength, false);
				if(track < current_counter){
					//remove number less than current counter
					playlistManager.reSetCounter(playlistManager.getCounter()-1);//if we remove song before current playing media, descrease counter!	
				}else{
					//remove number larger than current counter, current counter doesnt change
				}
			}
			if(lastPlaylist != 'hidden'){
				if(activatePlaylistScroll)checkScroll();
			}
			
		}else{//we removed last track in playlist
			destroyPlaylist();	
		}
		
		//console.log(playlistDataArr);
		//console.log('playlistManager.getCounter(); = ', playlistManager.getCounter());
	}
	
	//*****************
	
	this.destroyAudio = function() {
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		if(!lastPlaylist) return false;
		destroyAudio();
	}
	
	this.destroyPlaylist = function() {
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		if(!lastPlaylist) return false;
		destroyPlaylist();
	}
	
	this.setTitle = function(title) {
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		if(!lastPlaylist) return false;
		
		if(textScroller && useSongNameScroll){
			textScroller.deactivate();
			textScroller.input(title);
			textScroller.activate();
		}else{
			if(player_mediaName)player_mediaName.html(title);
		}
	}
	
	this.getIsMobile = function(){
		return isMobile;
	}
	
	this.getSetupDone = function(){
		return componentInited;
	}
	
	this.getPlaylistLoaded = function(){
		return _playlistLoaded;
	}
	
	this.getPlaylistTransition = function(){
		return playlistTransitionOn;
	}
	
	this.getMediaPlaying = function(){
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		return mediaPlaying;
	}
	
	this.checkAudio = function(action){
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		if(!lastPlaylist) return false;
		//console.log('checkAudio');
		if(mediaPlaying){
			if(action=='pause'){
				soundManager.pause(sm2_sound_id);
			}else if(action=='stop'){
				soundManager.stop(sm2_sound_id);
				resetData2();
			}
			mediaPlaying=false;
			componentWrapper.find('.controls_toggle').find('img').attr('src', playBtnUrl);
		}
	}
	
	this.toggleShuffle = function() {
		if(!componentInited) return false;
		if(randomPlay){
			componentWrapper.find('.player_shuffle').find('img').attr('src', shuffleBtnUrl);
			randomPlay=false;
		}else{
			componentWrapper.find('.player_shuffle').find('img').attr('src', shuffleOnBtnUrl);
			randomPlay=true;
		}
		playlistManager.setRandom(randomPlay);
	}
	
	this.toggleLoop = function() {
		if(!componentInited) return false;
		if(loopingOn){
			componentWrapper.find('.player_loop').find('img').attr('src', loopBtnUrl);
			loopingOn=false;
		}else{
			componentWrapper.find('.player_loop').find('img').attr('src', loopOnBtnUrl);
			loopingOn=true;
		}
		playlistManager.setLooping(loopingOn);
	}
	
	this.setVolume = function(val) {
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		if(val<0) val=0;
		else if(val>1) val=1;
		_defaultVolume = val;
		setVolume();
	}
	
	/*
	Call this when some container (like a div) in which '#componentWrapper' sits is set to 'display:none' in css. 
	Then when you show that container, call reinitScroll(), to reinitialize jScrollPane, 
	because the value in inital initialization was 0 because of display none on parent.
	Also, text scoller needs '.fontMeasure' element visible to get the correct width of the font.
	*/
	this.reinitScroll = function() {
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		if(!lastPlaylist) return false;
		if(scrollPaneApi){
			scrollPaneApi.reinitialise();
			scrollPaneApi.scrollToY(0);
		}
		if(textScroller && useSongNameScroll){
			//set scroll on current active item since counter ready has already been called
			if(playlistManager && playlistManager.getCounter()!=-1){
				var c = playlistManager.getCounter();
				if(getTitle(c)){
					textScroller.input(getTitle(c));
					textScroller.activate();
				}
			}
		}
	}
	
	this.outputPlaylistData = function(){
		if(!componentInited) return false;
		if(playlistTransitionOn) return false;
		if(!lastPlaylist) return false;
		try{ 
			console.log(playlistDataArr);	
		}catch(e){}
	}
	
	return this;

	}
	
})(jQuery);



