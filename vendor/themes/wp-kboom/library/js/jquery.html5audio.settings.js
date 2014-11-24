// SETTINGS

var ap_settings = {
	/* playlistHolder: dom elements which holds list of playlists */
	playlistHolder: '#playlist_list',
	/* activePlaylist: set active playlist that will be loaded on beginning. 
	Leave empty for none like this: activePlaylist: '' */
	activePlaylist: '#playlist1',
	/* activeItem: active item to start with when playlist is loaded (0 = first, 1 = second, 2 = third... -1 = none) */
	activeItem: 0,
	/* sound_id: unique string for soundmanager sound id (if multiple player instances were used, then strings need to be different) */
	sound_id: 'sound_id1',
	
	/* soundcloudApiKey: If you want to use SoundCloud music, register you own api key here for free: 
	'http://soundcloud.com/you/apps/new' and enter Client ID */
	soundcloudApiKey: '',

	/*defaultVolume: 0-1 (Irrelevant on ios mobile) */
	defaultVolume:0.5,
	/*autoPlay: true/false (false on mobile by default) */
	autoPlay:false,
	/*autoLoad: true/false (auto start sound load) */
	autoLoad:true,
	/*randomPlay: true/false */
	randomPlay:false,
	/*loopingOn: true/false (loop on the end of the playlist) */
	loopingOn:true,
	
	/* useNumbersInPlaylist: true/false. Prepend numbers in playlist items. */
	useNumbersInPlaylist: true,
	
	/* autoSetSongTitle: true/false. Auto set song title in 'player_mediaName'. */
	autoSetSongTitle: true,
	
	/* useSongNameScroll: true/false. Use song name scrolling. */
	useSongNameScroll: true,
	/* scrollSpeed: speed of the scroll (number higher than zero). */
	scrollSpeed: 1,
	/* scrollSeparator: String to append between scrolling song name. */
	scrollSeparator: '&nbsp;&#42;&#42;&#42;&nbsp;',
	
	/* mediaTimeSeparator: String between current and total song time. */
	mediaTimeSeparator: '&nbsp;-&nbsp;',
	/* seekTooltipSeparator: String between current and total song position, for progress tooltip. */
	seekTooltipSeparator: '&nbsp;/&nbsp;',
	
	/* defaultArtistData: Default text for song media name. */
	defaultArtistData: 'Artist&nbsp;Name&nbsp;-&nbsp;Artist&nbsp;Title',
	
	/* buttonsUrl: url of the buttons for normal and rollover state */
	buttonsUrl: {/*prev: 'media/data/icons/set1/prev.png', prevOn: 'media/data/icons/set1/prev_on.png',
				 next: 'media/data/icons/set1/next.png', nextOn: 'media/data/icons/set1/next_on.png',
				 pause: 'media/data/icons/set1/pause.png', pauseOn: 'media/data/icons/set1/pause_on.png',
				 play: 'media/data/icons/set1/play.png', playOn: 'media/data/icons/set1/play_on.png',
				 volume: 'media/data/icons/set1/volume.png', volumeOn: 'media/data/icons/set1/volume_on.png',
				 mute: 'media/data/icons/set1/mute.png', muteOn: 'media/data/icons/set1/mute_on.png',
				 loop: 'media/data/icons/set1/loop.png', loopOn: 'media/data/icons/set1/loop_on.png',
				 shuffle: 'media/data/icons/set1/shuffle.png', shuffleOn: 'media/data/icons/set1/shuffle_on.png'*/},
	/* useAlertMessaging: Alert error messages to user (true / false). */
	useAlertMessaging: false,
	
	/* activatePlaylistScroll: true/false. activate jScrollPane. */
	activatePlaylistScroll: false
};

