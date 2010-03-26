//INIT
jQT = new $.jQTouch({
	icon: 'img/istreamdev.png',
	addGlossToIcon: true,
	useFastTouch: false,
	startupScreen: 'img/startup.png',
	statusBar: 'black',
	iconIsGlossy: true,
	fullscreen: true,
	preloadImages: [
	'img/chevron.png',
	'img/back_button.png',
	'img/back_button_clicked.png',
	'img/button_clicked.png',
	'img/button.png',
	'img/button_clicked.png',
	'img/loading.gif',
	'img/toolbar.png',
	'img/on_off.png',
	'img/loading.gif',
	'img/audio.png',
	'img/epg.png',
	'img/media.png',
	'img/record.png',
	'img/timers.png',
	'img/timeron.png',
	'img/timeroff.png',
	'img/timerrec.png',
	'img/tv.png',
	'img/video.png',
	'img/stream.png',
	'img/stream_clicked.png',
	'img/istreamdev.png',
	'img/mask.png',
	'img/nologoTV.png',
	'img/nologoREC.png',
	'img/nologoMEDIA.png',
	'img/rec.png',
	'img/rec_clicked.png',
	'img/sw-alpha.png',
	'img/sw-button-cancel.png',
	'img/sw-button-done.png',
	'img/sw-header.png',
	'img/sw-slot-border.png',
	'img/nologoTV-mini.jpg'
	]
});

// [GENERIC STUFF]
// Global variable
dataString = "action=getGlobals";
$.getJSON("bin/backend.php",
			dataString,
			function(data){	
			streamdev_server = data.streamdev_server;
			rec_path = data.rec_path;
			video_path = data.video_path;
			audio_path = data.audio_path;
			if (streamdev_server != "" && streamdev_server != "null") {
			addVdr();
			gen_formchanlist();
			}
			if ( video_path != "" && video_path != "null") {
			addVideofiles();
			}
			if ( audio_path != "" && audio_path != "null" ) {
			addAudiofiles();
			}
});

function addVdr() {
	vdrmenu = '	<ul class="rounded">\n<li><span class="menutitle">VDR</span></li>\n';
	vdrmenu += '	<li class="arrow"><a id="categories_but" href="#"><img class="menuicon" src="img/tv.png" /><span class="menuname">Watch TV</span></a></li>';
	vdrmenu += '	<li class="arrow"><a id="recording_but" href="#"><img class="menuicon" src="img/record.png" /><span class="menuname">Recordings</span></a></li>';
	vdrmenu += '	<li class="arrow"><a id="timers_but" href="#"><img class="menuicon" src="img/timers.png" /><span class="menuname">Timers</span></a></li>'; 
	vdrmenu += '	<li class="arrow"><a id="epg_but" href="#"><img class="menuicon" src="img/epg.png" /><span class="menuname">Program Guide</span></a></li>\n</ul>';
	$('#home #runningsessions').after(vdrmenu);
}
function addVideofiles() {
	videomenu = '<li class="arrow"><a id="video_but" href="#"><img class="menuicon" src="img/video.png" /><span class="menuname">Video</span></a></li>';
	if ( $('#home #filemenu').length == 0 ) {
		$('#home').append('<ul class="rounded" id="filemenu"><li><span class="menutitle">FILES</span></li></ul>');
	}
	$('#home #filemenu').append(videomenu);
}

function addAudiofiles() {
	audiomenu = '<li class="arrow"><a id="audio_but" href="#"><img class="menuicon" src="img/audio.png" /><span class="menuname">Audio</span></a></li>';
	if ( $('#home #filemenu').length == 0 ) {
		$('#home').append('<ul class="rounded" id="filemenu"><li><span class="menutitle">FILES</span></li></ul>');
	}
	$('#home #filemenu').append(audiomenu);
}

//Goto home
$('#home_but').tap(function(event) {
	event.preventDefault();
	$(this).parents('div').find('a').unbind("tap");
	$('#home').bind('pageAnimationEnd', function(event, info){ 
	if (info.direction == 'in') {
		$('#jqt div[rel="browser"]').remove();
		$('#home').unbind('pageAnimationEnd');
		}
	});
	jQT.goTo('#home','flip');
});

//JSON query loading handler
function json_start(button) {
		$(button).addClass('active');
		$('#loader').addClass("loader");
		$('#loader').css("top", window.pageYOffset);
}
function json_complete(destination,effect) {
		jQT.goTo(destination,effect);
		//$('#loader').removeClass("loader");
		$('a').removeClass('active');
}
function hide_loader() {
	$('#loader').removeClass("loader");
	$('a').removeClass('active');
}

function reinitDivs() {
	$('#categories #cat_menu').html('');
	$('#channels #chan_menu').html('');
	$('#timers ul[rel="timers"]').html('');
}


// Binds
//hide "toggle" elements to lighten animation
$(document).ready(function(e){ 
$('div').bind('pageAnimationEnd', function(event, info){ 
	if (info.direction == 'in') {
		$('#loader').removeClass("loader");
		$('li[rel="toggle"]').show();
		}
		
	})
$('div').bind('pageAnimationStart', function(event, info){ 
	if (info.direction == 'in') {
		$('li[rel="toggle"]').hide();
		}
	})
});
//disable links of page while animation
$('a[class="back"]').tap(function(event) {
	event.preventDefault();
	$(this).parents('div').find('a').unbind("tap");
});


// show active sessions
$(document).ready(function(e){ 
getRunningSessions();
//preloadLogos();
});

//reinit RunningSessions when going to Home:
$(document).ready(function(e){ 
$('#home').bind('pageAnimationStart', function(event, info){ 
	if (info.direction == 'in') {
		getRunningSessions()
		}  
	})
});

//trick to prevent animation bug with object.
$(document).ready(function(e){ 
$('#streaming').bind('pageAnimationEnd', function(event, info){ 
	if (info.direction == 'in') {
		var session = $("#streaming").find('span[rel="session"]').text();
		var name = $("#streaming").find('span[rel="name"]').text();
		playvideo(session,name);
		} 
})
$('#streaming').bind('pageAnimationStart', function(event, info){ 
	var session = $("#streaming").find('span[rel="session"]').text();
	if (info.direction == 'out') {
		$('#player').html('<img class="thumbnail" id="thumbnail" src="ram/session' + session + '/thumb.png"></img>');
		}  
	})
});
//preload logos
function preloadLogos() {
	$.getJSON("bin/genlogolist.php",
	dataString,
	function(data){
    for (var i = data.length - 1; i >= 0; i--) {
		(new Image()).src = data[i];
		};
	});
}

//  [/GENERIC STUFF]

//	[HOME SECTION]
//buttons
$('#categories_but').tap(function(event) {
	event.preventDefault();
	json_start(this);
	gen_categories();	
	return false;
});

$('#recording_but').tap(function(event) {
	event.preventDefault();
	json_start(this);
	browser = 1;
	gen_browser(rec_path,browser,"Recordings","rec");
	return false;
});

$('#timers_but').tap(function(event) {
	event.preventDefault();
	json_start(this);
	gen_timers();
	return false;
});

$('#epg_but').tap(function(event) {
	event.preventDefault();
	json_start(this);
	gen_epgmenu();
	return false;
});

$('#video_but').tap(function(event) {
	event.preventDefault();
	json_start(this);
	browser = 1;
	gen_browser(video_path,browser,"Videos","vid");
	return false;
});

$('#audio_but').tap(function(event) {
	event.preventDefault();
	json_start(this);
	browser = 1;
	gen_browser(audio_path,browser,"Audio","aud");
	return false;
});

$('#runningsessions li a').tap(function(event) {
	event.preventDefault();
	json_start(this);
	var session = $(this).attr('rel');
	if (session=="killsessions") {
		var confirmation = confirm("Delete all active session?");
		if ( confirmation == false ) {
		hide_loader();
		return false;
		}
		var dataString = 'action=stopBroadcast&session=all';
		$.getJSON("bin/backend.php",
		dataString,
		function(data) {
				var status = data.status;
				var message = data.message;
				hide_loader();
				getRunningSessions();
				});
	} else {
	gen_streaming(session);
	}
	return false;
});


// Get Active broadcast & encoding sessions
function getRunningSessions() {
var dataString = "action=getRunningSessions";
	$('#home #runningsessions').html('<li><span class="menutitle">SESSIONS</span></li>\n<li>Checking running session</li>');
	//Json call to get category array
	$.getJSON("bin/backend.php",
	dataString,
	function(data){
		$('#home #runningsessions').html('<li><span class="menutitle">SESSIONS</span></li>');
		if ( data.broadcast.length >= 1 ) {
			$.each(data.broadcast, function(i,broadcast){
			session = broadcast.session;
			name = broadcast.name;
			type = broadcast.type;
			encoding = broadcast.encoding;
			if (encoding == 1) { encstatus = '*'; }
			else { encstatus = ''; }
			if (type == 'tv') { var pic='tv.png'; }
			else if (type == 'rec') { var pic='record.png'; }
			else if (type == 'vid') { var pic='video.png'; }
			$('#home #runningsessions').append('<li class="arrow"><a rel="' + session + '" href="#"><img class="menuicon" src="img/' + pic + '" /><span class="menuname">' + encstatus + name + '</span></a></li>');
			});
			$('#home #runningsessions').append('<li><a rel="killsessions" href="#"><span class="menuname">Stop all sessions</span></a></li>');
		}
		else {
		$('#home #runningsessions').append('<li><span class="menuname">No running session</span></li>');
		}
	});
}
//	[/HOME SECTION]

//	[TV SECTION]
//buttons
$('#categories ul#cat_menu a').tap(function(event) {
	event.preventDefault();
	json_start(this);
	var category = $(this).text();
	gen_channels(category);
	return false;
});

$('#channels ul#chan_menu .chan_but').tap(function(event) {
	event.preventDefault();
	json_start(this);
	var channame = $(this).find('span[class="name"]').text();
	var channumber = $(this).find('small[class="counter"]').text();
	gen_streamchannel(channame,channumber);
	return false;
});

$('#channels ul#chan_menu .toggleLink').tap(function(event) {
	event.preventDefault();
	$('#channels ul#chan_menu li[rel="toggle"]').show();
	$('#channels ul#chan_menu li[rel="showbut"]').remove();
});
//Gen Categories
function gen_categories() {
	$("#cat_menu").html('');
	var dataString = "action=getTvCat";
		//Json call to get category array
		$.getJSON("bin/backend.php",
		dataString,
		function(data){
			$.each(data.categories, function(i,categories){
			if ( i > 10 )	{
			togglestatus = "toggle";
			}
		else {
			togglestatus = "";
			}
			$("#cat_menu").append('<li class="arrow" rel="' + togglestatus + '"><a class="cat_but" href="#">' + categories.name  + '</a><small class="counter">' + categories.channels + '</small></li>');
			});
		$('li[rel="toggle"]').hide();
		json_complete('#categories','cube');
		})
}

//Gen Channels
function gen_channels(category) {
		$("#chan_menu").html('');
		var dataString = "action=getTvChan&cat=" + encodeURIComponent(category);
		//Json call to get category array
		$.getJSON("bin/backend.php",
		dataString,
		function(data){
			$.each(data.channel,function(i,channel){
				//trick to lower cpu while animating. When the page have too much elements, it flickers.
				if ( i <= 10 ) {
					$("#chan_menu").append('<li class="channellist"><a class="chan_but" href="#"><img src="logos/' + channel.name + '.png" onerror="this.src=\'img/nologoTV-mini.jpg\'" /><small class="counter">' + channel.number + '</small><span class="name">' + channel.name + '</span><span class="comment">' + channel.now_title + '</span></a></li>');
					}
				else {
					$("#chan_menu").append('<li class="channellist" rel="toggle"><a class="chan_but" href="#"><img src="logos/' + channel.name + '.png" onerror="this.src=\'img/nologoTV-mini.jpg\'" /><small class="counter">' + channel.number + '</small><span class="name">' + channel.name + '</span><span class="comment">' + channel.now_title + '</span></a></li>');
					}
				});
				$('li[rel="toggle"]').hide();
				json_complete('#channels','cube');
		})
}


//	[/TV SECTION]
//	[STREAM SECTION]
//buttons
$('#streamchannel span.streamButton a').tap(function(event) {
	event.preventDefault();
	json_start(this);
	var type = $("#streamchannel").find('span[rel="type"]').text();
    var url = $("#streamchannel").find('span[rel="url"]').text();
    var mode = $(this).attr('id');
    start_broadcast(type,url,mode);
	return false;
});
$('#streamchannel span.recButton a').tap(function(event) {
	event.preventDefault();
	json_start(this);
	var id = "new";
	var active= 1;
	var name = $("#streamchannel").find('span[class="name_now"]').text();
	name = name.substr(4,name.length);
	var channumber = $("#streamchannel").find('span[rel="number"]').text();
	var channame = $("#streamchannel").find('span[rel="channame"]').text();
	date = new Date();
	var rec_year = date.getFullYear();
	var rec_month = date.getMonth()+1;
	rec_month = str_pad(rec_month,2,'0','STR_PAD_LEFT');
	var rec_day = date.getDate();
	rec_day = str_pad(rec_day,2,'0','STR_PAD_LEFT');
	var rec_date = rec_year + "-" + rec_month + "-" + rec_day;
	var epgtime = $("#streamchannel").find('span[class="epgtime_now"]').text();
	var starttime = epgtime.substr(0,2) + epgtime.substr(3,2);
	var endtime  = epgtime.substr(6,2) + epgtime.substr(9,2);
    gen_edittimer(id,name,active,channumber,channame,rec_date,starttime,endtime);
	return false;
});

$('#streamrec span.streamButton a').tap(function(event) {
	event.preventDefault();
	json_start(this);
	var type = $("#streamrec").find('span[rel="type"]').text();
    var url = $("#streamrec").find('span[rel="url"]').text();
    var mode = $(this).attr('id');
    start_broadcast(type,url,mode);
	return false;
});
$('#streamvid span.streamButton a').tap(function(event) {
	event.preventDefault();
	json_start(this);
	var type = $("#streamvid").find('span[rel="type"]').text();
    var url = $("#streamvid").find('span[rel="url"]').text();
    var mode = $(this).attr('id');
    start_broadcast(type,url,mode);
	return false;
});
$('#streaming span.streamButton a[rel="stopbroadcast"]').tap(function(event) {
	event.preventDefault();
	json_start(this);
	var session = $("#streaming").find('span[rel="session"]').text();
    stop_broadcast(session);
	return false;
});
//Gen tv start stream
function gen_streamchannel(channame,channumber) {
	$('#streamchannel').find('h1').html( '<img class="menuicon" src="img/tv.png" /> ' +channame);
	$('#streamchannel').find('#thumbnail').attr('src','logos/' + channame + ".png");
	var dataString = "action=getChanInfo&chan=" + channumber;
	//Json call to get tv program info
	$.getJSON("bin/backend.php",
			dataString,
			function(data){
			var program = data.program;
			$("#streamchannel").find('span[class="name_now"]').html( 'Now: ' + program.now_title );
			$("#streamchannel").find('span[class="epgtime_now"]').html( program.now_time );
			$("#streamchannel").find('span[class="desc_now"]').html( program.now_desc );
			$("#streamchannel").find('span[class="name_next"]').html( 'Next: ' + program.next_title );
			$("#streamchannel").find('span[class="epgtime_next"]').html( program.next_time );
			$("#streamchannel").find('span[rel="url"]').html(streamdev_server + channumber);
            $("#streamchannel").find('span[rel="type"]').html('tv');
			$("#streamchannel").find('span[rel="number"]').html(channumber);
			$("#streamchannel").find('span[rel="channame"]').html(channame);
			json_complete('#streamchannel','cube');
		});
}

function gen_streamrec(folder,path) {
	var dataString = "action=getRecInfo&rec=" + encodeURIComponent(path) + "/" + encodeURIComponent(folder);
	//Json call to get rec info
	$.getJSON("bin/backend.php",
			dataString,
			function(data){
			var program = data.program;
			$('#streamrec').find('h1').html('<img class="menuicon" src="img/record.png" /> ' + program.name);
			$('#streamrec').find('#thumbnail').attr('src','logos/' + program.channel + ".png");
			$("#streamrec").find('span[class="name_now"]').html( program.name );
			$("#streamrec").find('span[class="epgtime_now"]').html( 'Recorded: ' + program.recorded );
			$("#streamrec").find('span[class="desc_now"]').html( program.desc );
			$("#streamrec").find('span[rel="url"]').html( path + "/" + folder );
            $("#streamrec").find('span[rel="type"]').html('rec');
			json_complete('#streamrec','cube');
		});
}

function gen_streamvid(filename,path) {
	var dataString = "action=getVidInfo&file=" + encodeURIComponent(path) + "/" + encodeURIComponent(filename);
	//Json call to get rec info
	$.getJSON("bin/backend.php",
			dataString,
			function(data){
			var program = data.program;
			var time = new Date();

			$('#streamvid').find('h1').html('<img class="menuicon" src="img/video.png" /> ' + program.name);
			$('#streamvid').find('#thumbnail').attr('src','ram/temp-logo.png?'+time);
			$("#streamvid").find('span[class="name_now"]').html( program.name );
			$("#streamvid").find('span[class="epgtime_now"]').html( 'Duration: ' + program.duration );
			desc='<b>format: </b>' + program.format + '<br><b>video: </b>' + program.video + '<br><b>audio: </b>' + program.audio + '<br><b>resolution: </b>' + program.resolution;
			$("#streamvid").find('span[class="desc_now"]').html( desc );
			$("#streamvid").find('span[rel="url"]').html( path + "/" + filename );
            $("#streamvid").find('span[rel="type"]').html('vid');
			json_complete('#streamvid','cube');
			});
}
//Gen streaming page
function gen_streaming(session) {
	$("#streaming").find('span[rel="session"]').html(session);
	var dataString = "action=getStreamInfo&session=" + session;
	//Json call to start streaming 
	$.getJSON("bin/backend.php",
			dataString,
			function(data){	
			var stream = data.stream;
			$('#streaming').find('#thumbnail').attr('src','ram/session' + stream.session + '/thumb.png');
			$("#streaming").find('span[rel="thumbwidth"]').html(stream.thumbwidth);
			$("#streaming").find('span[rel="thumbheight"]').html(stream.thumbheight);
			if (stream.type == "tv") 
				{
				$('#streaming').find('h1').html('<img class="menuicon" src="img/tv.png" /> ' + stream.name );
				$('#streaming').find('#player').css('width', '90px');
				var streaminfo = '<li><span class="name_now">Now: ' + stream.now_title + '</span>';
				streaminfo += '<span class="epgtime_now">' + stream.now_time + '</span>';
				streaminfo += '<span class="desc_now">' + stream.now_desc + '</span></li>';
				streaminfo += '<li><span class="name_next">Next: ' + stream.next_title + '</span>';
				streaminfo += '<span class="epgtime_next">' + stream.next_time + '</span></li>';
				$("#streaming").find('ul[class="streaminfo"]').html(streaminfo);
				}
			else if (stream.type == "rec") 
				{
				$('#streaming').find('h1').html('<img class="menuicon" src="img/record.png" /> ' + stream.name );
				$('#streaming').find('#player').css('width', '90px');
				var streaminfo = '<li><span class="name_now">' + stream.name + '</span>';
				streaminfo += '<span class="epgtime_now">Recorded: ' + stream.recorded + '</span>';
				streaminfo += '<span class="desc_now">' + stream.desc + '</span></li>';
				$("#streaming").find('ul[class="streaminfo"]').html(streaminfo);
				}
			else if (stream.type == "vid") 
				{
				$('#streaming').find('h1').html('<img class="menuicon" src="img/video.png" /> ' + stream.name );
				$('#streaming').find('#player').css('width', '190px');
				var streaminfo = '<li><span class="name_now">' + stream.name + '</span>';
				streaminfo += '<span class="epgtime_now">Duration: ' + stream.duration + '</span>';
				desc='<b>format: </b>' + stream.format + '<br><b>video: </b>' + stream.video + '<br><b>audio: </b>' + stream.audio + '<br><b>resolution: </b>' + stream.resolution;
				streaminfo += '<span class="desc_now">' + desc + '</span></li>';
				$("#streaming").find('ul[class="streaminfo"]').html(streaminfo);
				}
			$('ul[class="streamstatus"]').find('span[class="mode"]').html('Please wait.'); 
			$("#streaming").find('span[rel="name"]').html(stream.name);
			json_complete('#streaming','cube');
		});
}

//Start broadcast
function start_broadcast(type,url,mode) {
     var dataString = 'action=startBroadcast&type='+type+'&url='+encodeURIComponent(url)+'&mode=' + mode;
	 $.getJSON("bin/backend.php",
	 dataString,
	 function(data){
			var session = data.session;
			gen_streaming(session);	
		});

}
//Stop broadcast

function stop_broadcast(session) {
	var dataString = 'action=stopBroadcast&session='+session;
	$.getJSON("bin/backend.php",
	dataString,
	function(data) {
			var status = data.status;
			var message = data.message;
			hide_loader();
			jQT.goBack();
    });
}


//Get server status & Play video
function playvideo(session,name) {
	var prevmsg="";
	var status_OnComplete = function(data) {
		var status = data.status;
		var message = data.message;
		var url = data.url;
		var thumbwidth = $('#streaming span[rel="thumbwidth"]').text();
		var thumbheight = $('#streaming span[rel="thumbheight"]').text();
		$('#streaming ul[class="streamstatus"]').find('span[class="mode"]').html(message);
		if ( status == "ready" || status == "error" ) {
			$('#player').html('<video id="videofeed" width="' + thumbwidth + '" height="' + thumbheight + '" poster="ram/session' + session + '/thumb.png" src="' + url + '" ></video><span rel="ready"></span>');
			return false;
			}
		prevmsg = message;
		status_Start(session,prevmsg);
	}
	
	var status_Start = function(session,prevmsg) {
		dataString = "action=getStreamStatus&session=" + session + "&msg=" + encodeURIComponent(prevmsg);
		$.getJSON("bin/backend.php",
		dataString,
		function(data){	
			status_OnComplete(data)
		});
	}
	status_Start(session,prevmsg);
}
//	[/STREAM SECTION]

//	[BROWSER SECTION]
//buttons
$('ul[rel="filelist"] li[class="arrow"] a').tap(function(event) {
	event.preventDefault();
	json_start(this);
	var type = $(this).attr('rel');
	if ( type == 'audio' ) {
	var name = $(this).find('span[class="tracktitle"]').text();
	}
	else {
	var name = $(this).find('span[class="menuname"]').text();
	}
	var browser = $(this).parents('div').find('span[rel="currentbrowser"]').html();
	var foldertype = $('#browser'+browser+' span[rel="foldertype"]').html();
	var path = $('#browser'+browser+' span[rel="path"]').text();
	browser = parseInt(browser);
	browser++;
	if ( type == "folder" ) 
		{
		newpath=path+name+'/';
		gen_browser(newpath,browser,name,foldertype);
		}
	else if ( type == "rec" )
		{
		gen_streamrec(name,path);
		}
	else if ( type == "video" )
		{
		gen_streamvid(name,path);
		}
	return false;
});




$('div[rel="browser"] a[class="back"]').tap(function(event) {
	event.preventDefault();
	$(this).parents('div[rel="browser"]').remove();
});

//Generate browser div according to type
function gen_browser(path,browser,name,foldertype) {
	browser_template = '<div class="toolbar"></div>';
	browser_template += '<ul rel="filelist" class="rounded"></ul>';
	browser_template += '<div rel="dataholder" style="visibility:hidden">'
	browser_template += '<span rel="path"></span>';
	browser_template += '<span rel="currentbrowser"></span>';
	browser_template += '<span rel="foldertype">' + foldertype + '</span>';
	browser_template += '</div>';
	$('#jqt').append('<div id="browser' + browser + '" rel="browser"></div>'),
	$('#browser'+browser).html(browser_template);
	if ( path == rec_path || path == video_path || path == audio_path ) {
		toolbar = '<a href="#" class="back">Home</a>';
		if ( foldertype == 'rec' ){
		toolbar += '<h1><img class="menuicon" src="img/record.png" /> ' + name + '</h1>';
		} 
		else if ( foldertype == 'vid' ){
			toolbar += '<h1><img class="menuicon" src="img/video.png" /> ' + name + '</h1>';
		}
		else if ( foldertype == 'aud' ){
			toolbar += '<h1><img class="menuicon" src="img/audio.png" /> ' + name + '</h1>';
		}
		$('#browser' + browser + ' div[class="toolbar"]').html(toolbar);
	}
	else {
		toolbar = '<a href="#" class="back">Back</a>';
		toolbar += '<a href="#home" id="home_but" class="button">Home</a>';
		if ( foldertype == 'rec' ){
			toolbar += '<h1><img class="menuicon" src="img/record.png" /> ' + name + '</h1>';
		} 
		else if ( foldertype == 'vid' ){
			toolbar += '<h1><img class="menuicon" src="img/video.png" /> ' + name + '</h1>';
		}
		else if ( foldertype == 'aud' ){
			toolbar += '<h1><img class="menuicon" src="img/audio.png" /> ' + name + '</h1>';
		}
		$('#browser' + browser + ' div[class="toolbar"]').html(toolbar);
	}
	var dataString = 'action=browseFolder&path='+encodeURIComponent(path);
	$.getJSON("bin/backend.php",
	dataString,
	function(data) {
			$("#browser" + browser).find('ul').html('');
			$("#browser" + browser).find('span[rel="path"]').html(path);
			$("#browser" + browser).find('span[rel="currentbrowser"]').html(browser);
			$.each(data.list, function(i,list){
				if ( i > 10 ) {
				hidetoggle = 'toggle';
				}
				else
				{
				hidetoggle = '';
				}
				if (list.type == "folder") {
				$("#browser" + browser).find('ul').append('<li class="arrow" rel="' + hidetoggle + '"><a href="#" rel="folder"><span class="menuname">' + list.name + '</span></a></li>');			
				}
				else if (list.type == "rec") {
				$("#browser" + browser).find('ul').append('<li class="arrow" rel="' + hidetoggle + '"><a href="#" rel="rec"><img class="menuicon" src="img/record.png" /><span class="menuname">' + list.name + '</span></a></li>');	
				}
				else if ( list.type == "video" ) {
				$("#browser" + browser).find('ul').append('<li class="arrow" rel="' + hidetoggle + '"><a href="#" rel="video"><img class="menuicon" src="img/video.png" /><span class="menuname">' + list.name + '</span></a></li>');	
				}
				else if ( list.type == "audio" ) {
					if ( list.trackname != "" ) {
					name = list.trackname;
					} else {
					name = list.name; }
				$("#browser" + browser).find('ul').append('<li class="track" rel="' + hidetoggle + '"><a href="javascript:document.player.Play();" onclick="addplayer(this);" rel="audio"><div class="numberbox"><span class="number">' + list.number + '</span></div><span class="tracktitle" rel="'+ list.name + '">' + name + '</span><div class="timebox"><span class="time">' + list.length +'</span></div></a></li>');
				}
			});
			$('li[rel="toggle"]').hide();
			json_complete('#browser' + browser,'cube');
    });
}

//Add audio player code when needed
function addplayer(button) {
	json_start(button);
	var name = $(button).find('span[class="tracktitle"]').attr('rel');
	var browser = $(button).parents('div').find('span[rel="currentbrowser"]').html();
	var path = $('#browser'+browser+' div[rel="dataholder"] span[rel="path"]').text();
	browser = parseInt(browser);
	$('#browser'+browser+' #div_player').remove();
	$('#browser'+browser).append('<div style="position:absolute; left:0; top:0" name="div_player" id="div_player"></div>');
	//get playlist data
	dataString = 'action=streamAudio&path=' + encodeURIComponent(path) + '&file=' + encodeURIComponent(name);
	$.ajax({
	url: "bin/backend.php",
	dataType: 'json',
	data: dataString,
	async: false,
	success: function(json) {
		var track = json.track;
		playercode = "<embed id='musicplayer' enablejavascript='true' id='musicplayer' src='" + track[0].file + "' width='0' height='0' autoplay='false' name='player' type='audio/mp3' loop='true' controller='false'"; 
		for ( var i=1; i<track.length; i+=1 ) {
			qtattr = "'<" + track[i].file + ">'";
			playercode += "qtnext" + i + "=" + qtattr;
			}
		playercode += "></embed>";
		$('#div_player').html(playercode);
		hide_loader();
		return true;
		}

	});
}
//	[/BROWSER SECTION]

//  [TIMER SECTION]

// buttons
$('#timers li[class="arrow"] a').tap(function(event) {
	event.preventDefault();
	$(this).addClass('active');
	if ( $(this).attr('rel') == "new" ) {
	gen_edittimer();
	} else {
	timerid = $(this).attr('rel');
	timerdata = $('#timers ul[rel="timers"] li a[rel="' + timerid + '"]').data("timerdata");
	id = timerdata.id;
	name = timerdata.name;
	active = timerdata.active;
	channumber = timerdata.channumber;
	channame = timerdata.channame;
	date = timerdata.date;
	starttime = timerdata.starttime;
	endtime = timerdata.endtime;
	gen_edittimer(id,name,active,channumber,channame,date,starttime,endtime);
	}
});	

$('#edittimer a[rel="deletetimer"]').tap(function(event) {
	event.preventDefault();
	json_start(this);
	var timer_id = $("input#timer_id").val();
	dataString = 'action=delTimer&id=' + timer_id;
	$.getJSON("bin/backend.php",
	dataString,
	function(data) {
		message = data.status + ": " + data.message;
		gen_timers("true");
		showStatus( 0,message );
		return false;
	});
});
	
// gen Timers
function gen_timers(edit) {
	$('#timers ul[rel="timers"]').html('');
	var dataString = 'action=getTimers';
	$.getJSON("bin/backend.php",
	dataString,
	function(data) {
		$('#timers ul[rel="timers"]').append('<li><span class="menutitle">Current timers</span></li>');
		$.each(data.timer, function(i,timer){
		if ( i > 10 )	{
			togglestatus = "toggle";
			}
		else {
			togglestatus = "";
			}
		if ( timer.running == "1" ) {
			timerli = '<li class="arrow" rel="' + togglestatus + '"><a rel="' + timer.id + '" href="#"><img class="menuicon" src="img/timerrec.png" /><span class="menuname">' + timer.date + ' ' + timer.name + '</span></a></li>';
			}
			else
			{
				if ( timer.active == "1" ) {
					timerli = '<li class="arrow" rel="' + togglestatus + '"><a rel="' + timer.id + '" href="#"><img class="menuicon" src="img/timeron.png" /><span class="menuname">' + timer.date + ' ' + timer.name + '</span></a></li>';
				} else {
					timerli = '<li class="arrow" rel="' + togglestatus + '"><a rel="' + timer.id + '" href="#"><img class="menuicon" src="img/timeroff.png" /><span class="menuname">' + timer.date + ' ' + timer.name + '</span></a></li>';
				}
			}
		$('#timers ul[rel="timers"]').append(timerli);
		$('#timers ul[rel="timers"] li a[rel="' + timer.id + '"]').data("timerdata", timer);
		});
		if ( edit ) {
		hide_loader();
		jQT.goBack();
		}
		else {
		json_complete('#timers','cube');
		}
	});
}

function gen_edittimer(id,name,active,channumber,channame,date,starttime,endtime) {
	$('ul[ref="submitbut"]').remove();
	if (id) {
		if (id=="new") {
		$('#edittimer h1').html('<img class="menuicon" src="img/timers.png" / > NEW TIMER');
		id="";
		submitbutton = '<ul ref="submitbut" class="rounded">';
		submitbutton +=	'<li><center><a class="submit_form" href="#">Create</a></center></li></ul>';
		$('#timer').append(submitbutton);
		}
		else 
		{
		$('#edittimer h1').html('<img class="menuicon" src="img/timers.png" / > EDIT TIMER');
		submitbuttons = '<ul ref="submitbut" class="individual">';
		submitbuttons += '<li><a class="submit_form" href="#">Edit</a></li>';
		submitbuttons += '<li><a class="abutton" rel="deletetimer" href="#">Delete</a></li></ul>';
		$('#timer').append(submitbuttons);
		}
		if (active == 1) 
		{
				$('#timer_active').attr('checked', true);

		}
		else {
		$('#timer_active').attr('checked', false);
		}
		
		$('#timer_id').val(id);
		$('#timer_name').val(name);
		$('#timer_chan option[value="' + channumber + '"]').attr("selected", "selected");
		$('#timer_date').val(date);
		var wheeldate = date;
		while (wheeldate.indexOf("-") > -1)
		wheeldate = wheeldate.replace("-", ",");
		$('#a_date').attr('href', "javascript:openSelectDate(" + wheeldate + ");");
		$('#layer_date').html(date);
		$('#timer_starttime').val(starttime);
		$('#timer_endtime').val(endtime);
		wheelstart_h = starttime.substring(0,2);
		wheelstart_m = starttime.substring(2,4);
		$('#layer_starttime').html(wheelstart_h + 'h' + wheelstart_m);
		$('#a_starttime').attr('href', "javascript:openSelectTime('layer_starttime','" + wheelstart_h + "','" + wheelstart_m + "')");
		wheelend_h = endtime.substring(0,2);
		wheelend_m = endtime.substring(2,4);
		$('#layer_endtime').html(wheelend_h + 'h' + wheelend_m);
		$('#a_endtime').attr('href', "javascript:openSelectTime('layer_endtime','" + wheelend_h + "','" + wheelend_m + "')");
		
	}
	else {
	$('#edittimer h1').html('<img class="menuicon" src="img/timers.png" / > NEW TIMER');
	$('#timer_active').attr("checked", "checked");
	$('#timer_id').val(null);
	$('#timer_name').val(null);
	$('#timer_chan option').removeAttr("selected");
	$('#timer_chan option[value="1"]').attr("selected", "selected");
	$('#a_date').attr('href', "javascript:openSelectDate();");
	$('#layer_date').html("Select date");
	$('#timer_date').val(null);
	$('#timer_starttime').val(null);
	$('#timer_endtime').val(null);
	$('#a_starttime').attr('href', "javascript:openSelectTime('layer_starttime')");
	$('#layer_starttime').html('Select start time');
	$('#a_endtime').attr('href', "javascript:openSelectTime('layer_endtime')");
	$('#layer_endtime').html('select end time');
	submitbutton = '<ul ref="submitbut" class="rounded">';
	submitbutton +=	'<li><center><a class="submit_form" href="#">Create</a></center></li></ul>';
	$('#timer').append(submitbutton);
	}
	$('.formerror').hide(); 
	json_complete('#edittimer','cube');
}
//get full chanlist for timer page ( doing it one time on document load ).
function gen_formchanlist() {
	var dataString = 'action=getFullChanList';
	$.getJSON("bin/backend.php",
	dataString,
	function(data) {
	$('#jqt').data('channellist',data);
	$.each(data.category, function(i,category){
		$('#timer_chan').append('<optgroup label="' + category.name + '">');
			var catname = category.name;
			$.each(category.channel, function(j, channel){
				$('#timer_chan optgroup[label="' + catname +'"]').append('<option value="' + channel.number + '">' + channel.name +'</option>');
			});
		$('#timer_chan').append('</optgroup>');
		});
	});
}

// TIMER FORM VALIDATION & SUBMIT
$('.submit_form').tap(function(event) {  
		event.preventDefault();
		checktimerform();
		$(this).removeClass('active');
		});

function checktimerform() {
		$('.formerror').hide();  
		var timer_name = $("input#timer_name").val();   
		if (timer_name == "") {  
		$.scrollTo('#edittimer .toolbar', 400, {easing:'swing'});		
		$("li#timer_name_error").show();   
		return false;   
		}   
		var timer_date = $("input#timer_date").val();   
		if (timer_date == "") {   
			$("li#timer_date_error").show();
			$.scrollTo('#edittimer #timer_date_error');
		return false;   
		}
		var timer_starttime = $("input#timer_starttime").val();   
		if (timer_starttime == "") {   
			$("li#timer_starttime_error").show();
			$.scrollTo('#edittimer #timer_starttime_error');
		return false;   
		}   
		var timer_endtime = $("input#timer_endtime").val();   
		if (timer_endtime == "") {   
			$("li#timer_endtime_error").show();   
			$.scrollTo('#edittimer #timer_endtime_error');
		return false;   
		}
		var timer_id = $("input#timer_id").val();
		var timer_chan = $("select#timer_chan").val();
		var timer_active = $("input#timer_active").attr('checked')?1:0;
		var dataString = 'action=editTimer&id=' + timer_id + '&active=' + timer_active + '&name=' + encodeURIComponent(timer_name) + '&channumber=' + timer_chan + '&date=' + timer_date + '&starttime=' + timer_starttime + '&endtime=' + timer_endtime; 
		$.getJSON("bin/backend.php",
		dataString,
		function(data) {
				message = data.status + ": " + data.message;
				gen_timers("true");
				json_start(this);
				showStatus( 0,message );
				});
		return false;
}; 

function showStatus( timeout, message ) { 
    if( timeout == 0 ) { 
		$('#status_box').html(message);
		$('#status_box').show();
        setTimeout( function() { showStatus( 1, message ); }, 5000 ); 
    } else if( timeout == 1 ) { 
	$('#status_box').hide();
    } 
}
//  [/TIMER SECTION]

//   [EPG SECTION]
//buttons & events
$('.submit_epg').tap(function(event) {  
	event.preventDefault();
	channel = $('#epgform #epg_chan').val();
	time = $('#epgform #epg_time').val();
	day = $('#epgform #epg_day').val();
	if ( channel == "all" && time == "") {
			alert("You have to select a time for All channels listing");
			$(this).removeClass('active');
			return false;
	}
	if ( time == "" ) {
	programs = "day";
	}
	else if ( channel == "all" ) {
	programs = 2;
	} else {
	programs = "day";
	}
	json_start(this);
	get_epg(channel,time,day,programs);
	$(this).removeClass('active');
});


$('#epg ul li a[rel="whatsnow"]').tap(function(event) {
event.preventDefault();
json_start(this);
get_epg("all","now","0","2");
});



//functions
function gen_epgmenu() {
	gen_epgchanlist();
	gen_epgdatelist();
	genepg_timelist()
	json_complete('#epg','cube');
}

function gen_epgchanlist() {
	$('#epg #epg_chan').html('<option value="all">All channels</option>');
	datachanlist = $('#jqt').data('channellist');
	$.each(datachanlist.category, function(i,category){
		$('#epg #epg_chan').append('<optgroup label="' + category.name + '">');
			var catname = category.name;
			$.each(category.channel, function(j, channel){
				$('#epg #epg_chan optgroup[label="' + catname +'"]').append('<option value="' + channel.number + '">' + channel.name +'</option>');
			});
		$('#epg_chan').append('</optgroup>');
		});
}

function gen_epgdatelist() {
var daymax = 8;
var date = new Date();
var date_year = date.getFullYear();
var date_month = date.getMonth()+1;
var date_day = date.getDate();
//var date_hour = str_pad(date.getHours(),2,'0','STR_PAD_LEFT');
//var date_min = str_pad(date.getMinutes(),2,'0','STR_PAD_LEFT');
//$('#epg #epg_time').val(date_hour + '' + date_min);
//var date_fulldate = date_year + '-' + str_pad(date_month,2,'0','STR_PAD_LEFT') + '-' + str_pad(date_day,2,'0','STR_PAD_LEFT');
$('#epg #epg_day').html('<option value="0">Today</option>');
var dayname = new Array( "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat" );
	for ( i=1;i<daymax;i++ ) {
		date_milli=date.getTime();
		date.setTime(date_milli+86400000);
		$('#epg #epg_day').append('<option value="' + i +  '">' + dayname[date.getDay()] + ' ' + date.getDate() + '/' + (date.getMonth()+1) + '</option>');
		}
}

function genepg_timelist() {
starth = 0;
startm = 0;
$('#epg #epg_time').append('<option value="">All</option>');
	for ( i=0;i<24;i++ ) {
	curh = str_pad(starth+i,2,'0','STR_PAD_LEFT');
		for ( j=0;j<4;j++) {
		curm = str_pad(startm+(j*15),2,'0','STR_PAD_LEFT');
		$('#epg #epg_time').append('<option value="' + curh + curm +'">' + curh + 'h' + curm + '</option>');
		}
	
	}
}

function get_epg(channel,time,day,programs) {
var dataString = 'action=getEpg&channel=' + channel + '&time=' + time + '&day=' + day + '&programs=' + programs; 
	$.getJSON("bin/backend.php",
	dataString,
	function(data) {
		$('#jqt').data("epg",data);
		if ( data.category.length > 1 ) {
		type = 'cat';
		parse_epg(data,0,type,day);
		} else {
		type = 'chan';
		parse_epg(data,channel,type,day);
		}
		$('#epglist li[rel="toggle"]').hide();
	});
}

function parse_epg(data,selectedvalue,type,day){
	$('#epglist #epg_selector').html('');
	$('#epglist #ul_epglist').html('');
	date = new Date();
	var date_milli=date.getTime();
	date.setTime(date_milli+(86400000*day));
	var date_year = date.getFullYear();
	var date_month = date.getMonth()+1;
	var date_day = date.getDate();
	var epgdate = date_year + "-" + date_month + "-" + date_day;
	$('#epglist #ul_epglist').append('<li rel="epgdate" class="sep">' + epgdate + '</li>');
	if ( data.category.length > 1 )
	{
		$('#epglist #epg_selector').append('<select id="epglist_cat"></select>');
		$.each(data.category, function(i,category){
			$('#epglist #epg_selector #epglist_cat').append('<option value="' + i + '">' + category.name + '</option>');
		});
		$('#epglist #epg_selector select option[value=' + selectedvalue + ']').attr("selected", "selected");
	}
	else {
		$('#epglist #epg_selector').append('<select id="epglist_chan"></select>');
		datalistchan = $('#jqt').data('channellist');
		$.each(datalistchan.category, function(i,category){
		$('#epglist #epg_selector #epglist_chan').append('<optgroup label="' + category.name + '">');
		var catname = category.name;
		$.each(category.channel, function(j, channel){
			$('#epglist #epg_selector #epglist_chan optgroup[label="' + catname +'"]').append('<option value="' + channel.number + '">' + channel.name +'</option>');
		});
	$('#epg_chan').append('</optgroup>');
	});
	$('#epglist #epg_selector select option[value=' + selectedvalue + ']').attr("selected", "selected");
	}
	var k=1;
	if ( type == "cat" ) {
	arrayvalue = selectedvalue;
	} else {
	arrayvalue = 0;
	}
	$.each(data.category[arrayvalue].channel, function(i,channel){
	if ( k > 10 ) {
				togglestatus = 'toggle';
			}
			else
			{
				togglestatus = '';
			}
	k++;
	$('#epglist #ul_epglist').append('<li rel="' + togglestatus + '" class="sep">' + channel.name + '</li>');
		$.each(channel.epg, function(j,epg){
			if ( k > 10 ) {
				togglestatus = 'toggle';
			}
			else
			{
				togglestatus = '';
			}
		$('#epglist #ul_epglist').append('<li rel="' + togglestatus + '"><a href="#"><span class="epgtime">' + epg.time + '</span><span class="epgname">' + epg.title + '</span></a></li>');
		
		k++;
		});
	});
	$("#epglist #epg_selector select").change(function () {
	epgdata = $('#jqt').data("epg");
	selectedvalue = $("#epglist #epg_selector select option:selected").val();
	if ($("#epglist #epg_selector select").attr("id") == 'epglist_cat') {
		parse_epg(epgdata,selectedvalue,'cat',day);
	} else {
		time = $('#epgform #epg_time').val();
		day = $('#epgform select##epg_day').val();
		if ( time == "" ) {
			programs = "day";
		}
		else if ( channel == "all" ) {
			programs = 2;
		} else {
			programs = "day";
		}
		json_start('null');
		get_epg(selectedvalue,time,day,programs);
	}

	});
		if ( $('div[class="current"]').attr('id') == "epg" || $('div[class="current reverse"]').attr('id') == "epg") {
		json_complete('#epglist','cube');
		}
		else {
		$('li[rel="toggle"]').show();
		hide_loader();
		}
}

//   [/EPG SECTION]
