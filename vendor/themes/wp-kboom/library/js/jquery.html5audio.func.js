/* AUDIO SETUP */

(function($){
    //sound manager settings (http://www.schillmania.com/projects/soundmanager2/)

    var audio = document.createElement('audio'), mp3Support, oggSupport;
    if (audio.canPlayType) {
        mp3Support = !!audio.canPlayType && "" != audio.canPlayType('audio/mpeg');//setting this will use html5 audio on all html5 audio capable browsers ('modern browsers'), flash on the rest ('older browsers')
        mp3Support=true;//setting this will use html5 audio on modern browsers that support 'mp3', flash on the rest of modern browsers that support 'ogv' like firefox and opera, and of course flash on the rest ('older browsers') (USE THIS SETTING WHEN USING PODCAST AND SOUNDCLOUD!)
        oggSupport = !!audio.canPlayType && "" != audio.canPlayType('audio/ogg; codecs="vorbis"');
    }else{
        //for IE<9
        mp3Support = true;
        oggSupport = false;
    }
//console.log('mp3Support = ', mp3Support, ' , oggSupport = ', oggSupport);
    setAudioFormats();

    function setAudioFormats(){
        soundManager.audioFormats = {
            'mp3': {
                'type': ['audio/mpeg; codecs="mp3"', 'audio/mpeg', 'audio/mp3', 'audio/MPA', 'audio/mpa-robust'],
                'required': mp3Support
            },
            'mp4': {
                'related': ['aac','m4a'],
                'type': ['audio/mp4; codecs="mp4a.40.2"', 'audio/aac', 'audio/x-m4a', 'audio/MP4A-LATM', 'audio/mpeg4-generic'],
                'required': false
            },
            'ogg': {
                'type': ['audio/ogg; codecs=vorbis'],
                'required': oggSupport
            },
            'wav': {
                'type': ['audio/wav; codecs="1"', 'audio/wav', 'audio/wave', 'audio/x-wav'],
                'required': false
            }
        };
    }



    /* HELPERS */

    function stringToBoolean(string){
        switch(string.toLowerCase()){
            case "true": case "yes": case "1": return true;
            case "false": case "no": case "0": case null: return false;
            default: return Boolean(string);
        }
    }

    function isEmpty(str) {
        return str.replace(/^\s+|\s+$/g, '').length == 0;
    }



    /* API CALLS */

    function api_checkAudio(action){
        /* pause/play multiple players call */
        if(player1)player1.checkAudio(action);
    }

    function api_playAudio(){
        /* play active media */
        if(player1)player1.playAudio();
    }
    function api_pauseAudio(){
        /* pause active media */
        if(player1)player1.pauseAudio();
    }
    function api_toggleAudio(){
        /* toggle active media */
        if(player1)player1.toggleAudio();
    }
    function api_stopAudio(){
        /* stop active media */
        if(player1)player1.stopAudio();
    }
    function api_nextAudio(){
        /* play next media */
        if(player1)player1.nextAudio();
    }
    function api_previousAudio(){
        /* play previous media */
        if(player1)player1.previousAudio();
    }
    function api_loadAudio(id){
        /*
         play media (pass media number, counting starts from zero, or media title as string to load).

         Examples:
         player1.loadAudio(0); //Play media number 0
         player1.loadAudio(1); //Play media number 1
         player1.loadAudio(2); //Play media number 2
         player1.loadAudio('Tim McMorris - A Bright And Hopeful Future') //Play media with name 1
         player1.loadAudio('Tim McMorris - Health Happiness Success'); //Play media with name 2
         */
        if(player1)player1.loadAudio(id);
    }

    /* METHODS RELATED TO PHYSICAL PLAYLIST */
    function api_loadPlaylist(id){
        /*
         pass element 'id' from the dom
         player1.loadPlaylist('#playlist1');//Load local playlist 1
         player1.loadPlaylist('#playlist2');//Load local playlist 2
         */
        if(player1)player1.loadPlaylist(id);
    }
    function api_addTrack(tracks, pos){
        /*add track (
         param1: pass track or array of tracks as string, in the same format as you would in html or xml, ONLY type local supported!
         param2: position to insert track(s) (number, counting starts from 0), leave out parameter for the end).

         Examples:
         player1.addTrack(trackList[0], 0); //Add track 0, position 0
         player1.addTrack(trackList[1], 1); //Add track 1, position 1
         player1.addTrack(trackList[2], 2); //Add track 2, position 2
         player1.addTrack(trackList[3], 3); //Add track 3, position 3
         player1.addTrack(trackList[4]); //Add track 4, at pos end

         player1.addTrack(trackList_1[0], 0); //Add track 0, position 0
         player1.addTrack(trackList_1[1], 1); //Add track 1, position 1
         player1.addTrack(trackList_1[2], 2); //Add track 2, position 2
         player1.addTrack(trackList_1[0]); //Add track 0, at pos end

         player1.addTrack(trackList, 0); //Add tracks, position 0
         player1.addTrack(trackList, 1); //Add tracks, position 1
         player1.addTrack(trackList, 2); //Add tracks, position 2
         player1.addTrack(trackList); //Add tracks, position end

         player1.addTrack(trackList_1, 0); //Add tracks 2, position 0
         player1.addTrack(trackList_1, 1); //Add tracks 2, position 1
         player1.addTrack(trackList_1, 2); //Add tracks 2, position 2
         player1.addTrack(trackList_1); //Add tracks 2, position end
         */
        if(player1)player1.addTrack(tracks, pos);
    }
    /* END METHODS RELATED TO PHYSICAL PLAYLIST */

    /* METHODS RELATED TO NON-PHYSICAL PLAYLIST */
    function api_inputAudio(tracks, pos){
        /* play audio without creating any kind of physical playlist(
         param1 (object or array of objects containing following properties):
         mp3 path (required),
         ogg path (optional),
         title (optional)
         param2: position to insert track(s) (number, counting starts from 0), leave out parameter for the end).

         Examples:
         player1.inputAudio(trackList2[0], 0); //Input media number 0, position 0
         player1.inputAudio(trackList2[0], 1); //Input media number 0, position 1
         player1.inputAudio(trackList2[0], 2); //Input media number 0, position 2
         player1.inputAudio(trackList2[0]); //Input media number 0, position end

         player1.inputAudio(trackList2[1], 0); //Input media number 1, position 0
         player1.inputAudio(trackList2[1], 1); //Input media number 1, position 1
         player1.inputAudio(trackList2[1], 2); //Input media number 1, position 2
         player1.inputAudio(trackList2[1]); //Input media number 1, position end

         player1.inputAudio(trackList2[2], 0); //Input media number 2, position 0
         player1.inputAudio(trackList2[2], 1); //Input media number 2, position 1
         player1.inputAudio(trackList2[2], 2); //Input media number 2, position 2
         player1.inputAudio(trackList2[2]); //Input media number 2, position end

         player1.inputAudio(trackList2, 0); //Input media array 1, position 0
         player1.inputAudio(trackList2, 1); //Input media array 1, position 1
         player1.inputAudio(trackList2, 2); //Input media array 1, position 2
         player1.inputAudio(trackList2); //Input media array 1, position end

         player1.inputAudio(trackList3, 0); //Input media array 2, position 0
         player1.inputAudio(trackList3, 1); //Input media array 2, position 1
         player1.inputAudio(trackList3, 2); //Input media array 2, position 2
         player1.inputAudio(trackList3); //Input media array 2, position end
         */

        if(player1)player1.inputAudio(tracks, pos);
    }
    /* END METHODS RELATED TO NON-PHYSICAL PLAYLIST */

    function api_removeTrack(id){
        /* remove track (pass track number, counting starts from zero, or track title as string to remove).constructor

         Examples:
         player1.removeTrack(0); //Remove track 0
         player1.removeTrack(1); //Remove track 1
         player1.removeTrack(2); //Remove track 2
         player1.removeTrack(3); //Remove track 3

         player1.removeTrack('Tim McMorris - A Bright And Hopeful Future'); //Remove track with name 1
         player1.removeTrack('Tim McMorris - Be My Valentine'); //Remove track with name 2
         player1.removeTrack('Tim McMorris - Give Our Dreams Their Wings To Fly'); //Remove track with name 3
         player1.removeTrack('Tim McMorris - Happy Days Are Here To Stay'); //Remove track with name 4
         */
        if(player1)player1.removeTrack(id);
    }
    function api_destroyAudio(){
        /* destroy active audio */
        if(player1)player1.destroyAudio();
    }
    function api_destroyPlaylist(){
        /* destroy active playlist */
        if(player1)player1.destroyPlaylist();
    }
    function api_setTitle(){
        /* set song title in 'player_mediaName'.

         Examples:
         player1.setTitle(titleList[0]); //Set song title 1
         player1.setTitle(titleList[1]); //Set song title 2
         player1.setTitle(titleList[2]); //Set song title 3
         */
        if(player1)player1.setTitle();
    }

    /* GENERAL METHODS */
    function api_toggleShuffle(){
        /* toggle shuffle */
        if(player1)player1.toggleShuffle();
    }
    function api_toggleLoop(){
        /* toggle loop */
        if(player1)player1.toggleLoop();
    }
    function api_setVolume(val){
        /* set volume (0-1) */
        if(player1)player1.setVolume(val);
    }

    /* OTHER METHODS */
    function api_reinitScroll(){
        /* reinitialize scroll (check help for more info when to use this) */
        if(player1)player1.reinitScroll();
    }
    function api_outputPlaylistData(){
        /* output playlist data in format: {id, type, title, mp3 path, ogg path} */
        if(player1)player1.outputPlaylistData();
    }
    function api_getSetupDone(){
        /* check if component setup is done */
        if(player1)return player1.getSetupDone();
    }
    function api_getPlaylistTransition(){
        /* check if playlist is loading */
        if(player1)return player1.getPlaylistTransition();
    }
    function api_getPlaylistLoaded(){
        /* check if playlist is loaded */
        if(player1)return player1.getPlaylistLoaded();
    }
    function api_getMediaPlaying(){
        /* get media playing */
        if(player1)return player1.getMediaPlaying();
    }




    /* CALLBACKS */

    function audioPlayerSetupDone(sound_id){
        /* called when component is ready to receive public function calls. Returns current player instance sound id */
        //console.log('audioPlayerSetupDone: ', sound_id);
        if(sound_id == 'sound_id10'){
            slideHolder= $('#slideHolder');
            loadImage();
        }
    }
    function audioPlayerPlaylistLoaded(sound_id){
        /* called when playlist is loaded. Returns current player instance sound id */
        //console.log('audioPlayerPlaylistLoaded: ', sound_id);
    }
    function audioPlayerPlaylistEnd(sound_id){
        /* called when current playlists reaches end. Returns current player instance sound id. */
        //console.log('audioPlayerPlaylistEnd: ', sound_id);
    }
    function audioPlayerSoundEnd(counter){
        /* called when current playing sound ends. Returns current audio counter starting from zero */
        //console.log('audioPlayerSoundEnd: ', counter);
    }
    function audioPlayerSoundStart(counter){
        /* called when current playing sound starts. Returns current audio counter starting from zero */
        //console.log('audioPlayerSoundStart: ', counter);
    }
    function audioPlayerSoundPlay(sound_id){
        //console.log('audioPlayerSoundPlay: ', sound_id);
        /* called when sound is played. Returns current playing sound id */
        if($('#componentWrapper').length || $('#componentWrapper2').length) return;
        var soundArr = [{player_id: player1, sound_id: 'sound_id1'},{player_id: player2, sound_id: 'sound_id2'}], i = 0, len = soundArr.length, el;//list of players and related sound_ids
        for(i;i<len;i++){
            //console.log('audioPlayerSoundPlay: ', sound_id, soundArr[i].sound_id);
            if(sound_id != soundArr[i].sound_id){
                el = document.getElementById(soundArr[i].sound_id);
                if(el && typeof el.contentWindow.api_checkAudio !== 'undefined'){
                    el.contentWindow.api_checkAudio('pause');
                }else{
                    if(sound_id != soundArr[i].sound_id)soundArr[i].player_id.checkAudio('pause');
                }
            }
        }
    }
    function audioPlayerSoundPause(sound_id){
        /* called when sound is paused. Returns current playing sound id */
        //console.log('audioPlayerSoundPause: ', sound_id);
    }
    function itemTriggered(sound_id, counter){
        /* called when new sound is triggered. Returns current audio counter starting from zero */
        //console.log('itemTriggered: ', counter);
        if(sound_id == 'sound_id7')player1.setTitle(titleList[counter]);
    }
    function playlistItemEnabled(sound_id, target){
        /* called on playlist item enable. Returns playlist item. */
        //console.log('playlistItemEnabled: ', target);
        if(sound_id == 'sound_id7'){
            var src = $(target).find('img').attr('src');
            var s1 = src.substr(0, src.lastIndexOf('/')-2);
            var s2 = src.substr(src.lastIndexOf('/')+1);
            //console.log(s1, s2);
            var new_src = s1 + s2;
            //console.log(new_src);
            $(target).css('cursor','pointer').find('img').attr('src', new_src);
        }

    }
    function playlistItemDisabled(sound_id, target){
        /* called on playlist item disable. Returns playlist item. */
        //console.log('playlistItemDisabled: ', target);
        if(sound_id == 'sound_id7'){
            var src = $(target).find('img').attr('src');
            var s1 = src.substr(0, src.lastIndexOf('/')+1);
            var s2 = src.substr(src.lastIndexOf('/'));
            //console.log(s1,s2);
            var new_src = s1 + 'on' + s2;
            //console.log(new_src);
            $(target).css('cursor','default').find('img').attr('src', new_src);
        }

    }

//list of tracks to insert into playlist (just for demo purposes)

    var trackList = ["<li class='playlistItem' data-type='local' data-mp3Path='media/audio/2/Soundroll_-_Funky_Boom.mp3' data-oggPath='media/audio/2/Soundroll_-_Funky_Boom.ogg'><a class='playlistNonSelected' href='#'>Soundroll - Funky Boom",

        "<li class= 'playlistItem' data-type='local' data-mp3Path='media/audio/2/Soundroll_-_Fight_No_More.mp3' data-oggPath='media/audio/2/Soundroll_-_Fight_No_More.ogg'><a class='playlistNonSelected' href='#'>Soundroll - Fight No More",

        "<li class= 'playlistItem' data-type='local' data-mp3Path='media/audio/2/Soundroll_-_Rush.mp3' data-oggPath='media/audio/2/Soundroll_-_Rush.ogg'><a class='playlistNonSelected' href='#'>Soundroll - Rush",

        "<li class= 'playlistItem' data-type='local' data-mp3Path='media/audio/2/Soundroll_-_Pump_The_Club.mp3' 	data-oggPath='media/audio/2/Soundroll_-_Pump_The_Club.ogg'><a class='playlistNonSelected' href='#'>Soundroll - Pump The Club",

        "<li class= 'playlistItem' data-type='local' data-mp3Path='media/audio/2/Soundroll_-_A_Way_To_The_Top.mp3'  data-oggPath='media/audio/2/Soundroll_-_A_Way_To_The_Top.ogg'><a class='playlistNonSelected' href='#'>Soundroll - A Way To The Top",

        "<li class= 'playlistItem' data-type='local' data-mp3Path='media/audio/2/Soundroll_-_Sun_Is_So_Bright.mp3'  data-oggPath='media/audio/2/Soundroll_-_Sun_Is_So_Bright.ogg'><a class='playlistNonSelected' href='#'>Soundroll - Sun Is So Bright"];

    var trackList_1 = ["<li class= 'playlistItem' data-type='local' data-mp3Path='media/audio/1/Tim_McMorris_-_A_Bright_And_Hopeful_Future.mp3' 		 data-oggPath='media/audio/1/Tim_McMorris_-_A_Bright_And_Hopeful_Future.ogg'><a class='playlistNonSelected' href='#'>Tim McMorris - A Bright And Hopeful Future",
        "<li class= 'playlistItem' data-type='local' data-mp3Path='media/audio/1/Tim_McMorris_-_Be_My_Valentine.mp3' 					 data-oggPath='media/audio/1/Tim_McMorris_-_Be_My_Valentine.ogg'><a class='playlistNonSelected' href='#'>Tim McMorris - Be My Valentine</a><a class='hlink' href='http://www.google.com' target='_blank'><img src='media/data/link.png' width='10' height='10' alt = ''/>",

        "<li class= 'playlistItem' data-type='local' data-mp3Path='media/audio/1/Tim_McMorris_-_Give_Our_Dreams_Their_Wings_To_Fly.mp3'  data-oggPath='media/audio/1/Tim_McMorris_-_Give_Our_Dreams_Their_Wings_To_Fly.ogg'><a class='playlistNonSelected' href='#'>Tim McMorris - Give Our Dreams Their Wings To Fly"];

//list of tracks 2
    var trackList2 = [{mp3: 'media/audio/2/Soundroll_-_Funky_Boom.mp3', ogg: 'media/audio/2/Soundroll_-_Funky_Boom.ogg', title: 'Soundroll - Funky Boom'}, {mp3: 'media/audio/2/Soundroll_-_Fight_No_More.mp3', ogg: 'media/audio/2/Soundroll_-_Fight_No_More.ogg', title: 'Soundroll - Fight No More'}, {mp3: 'media/audio/2/Soundroll_-_Rush.mp3', ogg:'media/audio/2/Soundroll_-_Rush.ogg', title: 'Soundroll - Rush'}];

//list of tracks 3
    var trackList3 = [{mp3: 'media/audio/1/Tim_McMorris_-_A_Bright_And_Hopeful_Future.mp3', ogg: 'media/audio/1/Tim_McMorris_-_A_Bright_And_Hopeful_Future.ogg', title: 'Tim McMorris - A Bright And Hopeful Future'}, {mp3: 'media/audio/1/Tim_McMorris_-_Be_My_Valentine.mp3', ogg: 'media/audio/1/Tim_McMorris_-_Be_My_Valentine.ogg', title: 'Tim McMorris - Be My Valentine'}];

//list of tracks 3
    var trackList4 = [{mp3: 'media/audio/3/Bluegestalt_-_Becoming.mp3', ogg: 'media/audio/3/Bluegestalt_-_Becoming.ogg'}];

//list of titles to insert into player_mediaName
    var titleList = ['Tim McMorris - A Bright And Hopeful Future', 'Tim McMorris - Be My Valentine', 'Tim McMorris - Give Our Dreams Their Wings To Fly', 'Tim McMorris - Happy Days Are Here To Stay', 'Tim McMorris - Health Happiness Success', 'Tim McMorris - Marketing Advertising Music', 'Tim McMorris - Successful Business Venture'];





    /* PLAYER INIT */

    var player1, player2;
    jQuery(document).ready(function($) {
        //init component
        player1 = $('#componentWrapper').html5audio(ap_settings);
        ap_settings = null;
        if($('#componentWrapper2').length){
            player2 = $('#componentWrapper2').html5audio(ap_settings2);
            ap_settings2 = null;
        }
    });





    /* EXAMPLE 10 CODE */

    var imgArr=['media/slideshow/01.jpg','media/slideshow/02.jpg','media/slideshow/03.jpg','media/slideshow/04.jpg','media/slideshow/05.jpg','media/slideshow/06.jpg','media/slideshow/07.jpg'], counter=0, slideHolder, prevImage, currentImage, imageOnOffSpeed=500, imageOnOffEase="easeOutSine", imageLoaded=false, slideshowTimeoutID, slideshowTimeout = 3000, ap, counter=0, slideHolder= $('#slideHolder'), prevImage, currentImage, imageOnOffSpeed=1500, imageOnOffEase="easeOutSine", slideshowTimeoutID, slideshowTimeout = 3000, originalWidth, originalHeight, autop;

    function loadImage(){
        imageLoaded=false;//reset

        var url = imgArr[counter];

        var img = $(new Image()).appendTo(slideHolder).css({
            display: 'block',
            position: 'absolute',
            opacity: 0
        }).load(function() {
                originalWidth=this.width;
                originalHeight=this.height;
                nextImage = $(this);
                setImage();
            }).error(function(e) {
                //console.log("error " + e);
            }).attr('src', url);
    }

    function setImage(){

        if(currentImage) prevImage = currentImage;
        currentImage = nextImage;

        var w1 = getComponentSize('w');
        var h1 = getComponentSize('h');
        var w = originalWidth;
        var h = originalHeight;

        var obj = retrieveObjectRatio(slideHolder, w, h, false);
        w = obj.width;
        h = obj.height;

        currentImage.css({
            width: w+'px',
            height: h+'px',
            left: w1/2-w/2 + 'px',
            top: h1/2-h/2 + 'px'
        });

        if(prevImage)prevImage.stop().animate({opacity: 0}, imageOnOffSpeed, imageOnOffEase);
        currentImage.stop().animate({opacity: 1}, imageOnOffSpeed, imageOnOffEase, imageOn);
    }

    function imageOn(){
        if(prevImage){
            prevImage.remove();//remove previous image
            prevImage=null;
        }
        if(slideshowTimeoutID) clearTimeout(slideshowTimeoutID);

        var interval = setInterval(function(){
            if(player1.getSetupDone()){
                if(interval) clearInterval(interval);
                player1.inputAudio(trackList4[0]); //add one looping track

                if(player1.getIsMobile() || autop == false){
                    //show play btn
                    var big_play = $('#big_play').css({opacity:0, display:'block', cursor: 'pointer'}).stop().animate({opacity: 1}, 500, 'easeOutSine').bind('click', function(){
                        player1.playAudio();
                        startSlideshow();
                        $(this).stop().animate({opacity: 0}, 300, 'easeOutExpo', function(){
                            $(this).remove();//remove play btn
                        });
                        return false;
                    });
                }else{
                    $('#big_play').remove();//remove play btn
                    startSlideshow();
                }
            }
        }, 100);
    }

    function nextSlide(){
        counter++;
        if(counter>imgArr.length-1)counter=0;
        loadImage();
    }

    function retrieveObjectRatio( obj, w, h, _fitScreen ) {

        var o ={};

        var _paddingX = 0;
        var _paddingY = 0;

        var objWidth = getComponentSize('w');
        var objHeight = getComponentSize('h');

        var targetWidth = w;
        var targetHeight = h;

        var destinationRatio = (objWidth - _paddingX) / (objHeight - _paddingY);///destination ratio of an object
        var targetRatio = targetWidth / targetHeight;///target ratio of an object

        if (targetRatio < destinationRatio) {

            //console.log('targetRatio < destinationRatio 1');

            if (!_fitScreen) {//fullscreen
                o.height = ((objWidth - _paddingX) / targetWidth) * targetHeight;
                o.width = (objWidth - _paddingX);
            } else {//fitscreen
                o.width = ((objHeight - _paddingY) / targetHeight) * targetWidth;
                o.height = (objHeight - _paddingY);
            }
        } else if (targetRatio > destinationRatio) {

            //console.log('targetRatio > destinationRatio 2');

            if (_fitScreen) {//fitscreen
                o.height = ((objWidth - _paddingX) / targetWidth) * targetHeight;
                o.width = (objWidth - _paddingX);
            } else {//fullscreen
                o.width = ((objHeight - _paddingY) / targetHeight) * targetWidth;
                o.height = (objHeight - _paddingY);
            }
        } else {//fitscreen & fullscreen
            o.width = (objWidth - _paddingX);
            o.height = (objHeight - _paddingY);
        }

        return o;
    }

    function getComponentSize(side){
        if(side=='w'){
            return slideHolder.width();
        }else{
            return slideHolder.height();
        }
    };

    function startSlideshow(){
        //continue from first slide
        if(slideshowTimeoutID) clearTimeout(slideshowTimeoutID);
        slideshowTimeoutID = setTimeout(nextSlide, slideshowTimeout);
    };


})(jQuery);