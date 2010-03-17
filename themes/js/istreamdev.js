//INIT
jQT = new $.jQTouch({
	icon: 'img/istreamdev.png',
	addGlossToIcon: true,
	useFastTouch: true,
	startupScreen: 'img/startup.png',
	statusBar: 'black',
	iconIsGlossy: true,
	fullscreen: true,
	preloadImages: [
	'themes/jqt/img/chevron.png',
	'themes/jqt/img/back_button.png',
	'themes/jqt/img/back_button_clicked.png',
	'themes/jqt/img/button_clicked.png',
	'themes/jqt/img/button.png',
	'themes/jqt/img/button_clicked.png',
	'themes/jqt/img/loading.gif',
	'themes/jqt/img/toolbar.png',
	'themes/jqt/img/on_off.png',
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
	'img/sw-slot-border.png'
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
			}
		);
//streamdev_server = "http://127.0.0.1:3000/TS/";
//rec_path = "/video/";
//video_path = "/mnt/media/Video/";
//audio_path = "/mnt/media/Music/";



//Goto home
$('#home_but').tap(function(event) {
	event.preventDefault();
	jQT.goTo('#home','flip');
});

//JSON query loading handler
function json_start(button) {
		$(button).addClass('active');
		$('#loader').addClass("loader");

}
function json_complete(destination,effect) {
		$('#loader').removeClass("loader");
		$('a').removeClass('active');
		jQT.goTo(destination,effect);
}
function hide_loader() {
	$('#loader').removeClass("loader");
	$('a').removeClass('active');
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

$('#video_but').tap(function(event) {
	event.preventDefault();
	json_start(this);
	browser = 1;
	gen_browser(video_path,browser,"Videos","vid");
	return false;
});
//	[/HOME SECTION]

//	[TV SECTION]
//buttons
$('#categories ul#cat_menu a').tap(function(event) {
	event.preventDefault();
	json_start(this);
	var category = $(this).html();
	gen_channels(category);
	return false;
});

$('#channels ul#chan_menu a').tap(function(event) {
	event.preventDefault();
	json_start(this);
	var channame = $(this).find('span[class="name"]').html();
	var channumber = $(this).find('small[class="counter"]').html();
	gen_streamchannel(channame,channumber);
	return false;
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
			$("#cat_menu").append('<li class="arrow"><a class="cat_but" href="#">' + categories.name  + '</a><small class="counter">' + categories.channels + '</small></li>');
			});
		json_complete('#categories','cube');
		})
}

//Gen Channels
function gen_channels(category) {
		$("#chan_menu").html('');
		var dataString = "action=getTvChan&cat=" + category;
		//Json call to get category array
		$.getJSON("bin/backend.php",
		dataString,
		function(data){
			$.each(data.channel,function(i,channel){
				$("#chan_menu").append('<li class="channellist"><a class="chan_but" href="#"><img src="logos/' + channel.name + '.png"/><small class="counter">' + channel.number + '</small><span class="name">' + channel.name + '</span><span class="comment">' + channel.now_title + '</span></a></li>');
				});
			json_complete('#channels','cube');
		})
}


//	[/TV SECTION]
//	[STREAM SECTION]
//buttons
$('#streamchannel span.streamButton a').tap(function(event) {
	event.preventDefault();
	json_start(this);
	var type = $("#streamchannel").find('span[rel="type"]').html();
    var url = $("#streamchannel").find('span[rel="url"]').html();
    var mode = $(this).attr('rel');
    start_broadcast(type,url,mode);
	return false;
});
$('#streamrec span.streamButton a').tap(function(event) {
	event.preventDefault();
	json_start(this);
	var type = $("#streamrec").find('span[rel="type"]').html();
    var url = $("#streamrec").find('span[rel="url"]').html();
    var mode = $(this).attr('rel');
    start_broadcast(type,url,mode);
	return false;
});
$('#streamvid span.streamButton a').tap(function(event) {
	event.preventDefault();
	json_start(this);
	var type = $("#streamvid").find('span[rel="type"]').html();
    var url = $("#streamvid").find('span[rel="url"]').html();
    var mode = $(this).attr('rel');
    start_broadcast(type,url,mode);
	return false;
});
$('#streaming span.streamButton a[rel="stopbroadcast"]').tap(function(event) {
	event.preventDefault();
	json_start(this);
	var session = $("#streaming").find('span[rel="session"]').html();
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
			json_complete('#streamchannel','cube');
		});
}

function gen_streamrec(folder,path) {
	var dataString = "action=getRecInfo&rec=" + path + folder;
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
			$("#streamrec").find('span[rel="url"]').html( path + folder );
            $("#streamrec").find('span[rel="type"]').html('rec');
			json_complete('#streamrec','cube');
		});
}

function gen_streamvid(filename,path) {
	var dataString = "action=getVidInfo&file=" + path + filename;
	//Json call to get rec info
	$.getJSON("bin/backend.php",
			dataString,
			function(data){
			var program = data.program;
			$('#streamvid').find('h1').html('<img class="menuicon" src="img/video.png" /> ' + program.name);
			$('#streamvid').find('#thumbnail').attr('src','ram/temp-logo.png');
			$("#streamvid").find('span[class="name_now"]').html( program.name );
			$("#streamvid").find('span[class="epgtime_now"]').html( 'Duration: ' + program.duration );
			desc='<b>format: </b>' + program.format + '<br><b>video: </b>' + program.video + '<br><b>audio: </b>' + program.audio + '<br><b>resolution: </b>' + program.resolution;
			$("#streamvid").find('span[class="desc_now"]').html( desc );
			$("#streamvid").find('span[rel="url"]').html( path + filename );
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
     var dataString = 'action=startBroadcast&type='+type+'&url='+url;
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

//trick to prevent animation bug with object.
$(document).ready(function(e){ 
$('#streaming').bind('pageAnimationEnd', function(event, info){ 
	if (info.direction == 'in') {
		var session = $("#streaming").find('span[rel="session"]').html();
		var name = $("#streaming").find('span[rel="name"]').html();
		playvideo(session,name);
		} 
})

$('#streaming').bind('pageAnimationStart', function(event, info){ 
	var session = $("#streaming").find('span[rel="session"]').html();
	if (info.direction == 'out') {
		$('#player').html('<img class="thumbnail" id="thumbnail" src="ram/session' + session + '/thumb.png"></img>');
		}  
	})
});

//Get server status & Play video
function playvideo(session,name) {
	var prevmsg="";
	var status_OnComplete = function(data) {
		var status = data.status;
		var message = data.message;
		var url = data.url;
		var thumbwidth = $('#streaming span[rel="thumbwidth"]').html();
		var thumbheight = $('#streaming span[rel="thumbheight"]').html();
		$('#streaming ul[class="streamstatus"]').find('span[class="mode"]').html(message);
		if ( status == "ready" ) {
			$('#player').html('<video id="videofeed" width="' + thumbwidth + '" height="' + thumbheight + '" poster="ram/session' + session + '/thumb.png" src="' + url + '" ></video><span rel="ready"></span>');
			return false;
			}
		prevmsg = message;
		status_Start(session,prevmsg);
	}
	
	var status_Start = function(session,prevmsg) {
		dataString = "action=getStreamStatus&session=" + session + "&msg=" + prevmsg;
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
$('ul[rel="filelist"] li.arrow a').tap(function(event) {
	event.preventDefault();
	json_start(this);
	var type = $(this).attr('rel');
	var name = $(this).find('span[class="menuname"]').html();
	var path = $(this).parents('div').find('span[rel="path"]').html();
	var browser = $(this).parents('div').find('span[rel="currentbrowser"]').html();
	var foldertype = $(this).parents('div').find('span[rel="foldertype"]').html();
	browser = parseInt(browser);
	browser++;
	if ( type == "folder" ) 
		{
		newpath=path+name;
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

$('div[rel="browser"] #home_but').tap(function(event) {
	event.preventDefault();
	$('#home').bind('pageAnimationEnd', function(event, info){ 
			$('#jqt div[rel="browser"]').remove();
			$('#home').unbind('pageAnimationEnd');
			
		});
});

//functions
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
	if ( path == rec_path || path == video_path ) {
	toolbar = '<a href="#" class="back">Home</a>';
	if ( foldertype == 'rec' ){
		toolbar += '<h1><img class="menuicon" src="img/record.png" /> ' + name + '</h1>';
	} 
	else {
		toolbar += '<h1><img class="menuicon" src="img/video.png" /> ' + name + '</h1>';
	}
	$('#browser' + browser + ' div[class="toolbar"]').html(toolbar);
	}
	else {
	toolbar = '<a href="#" class="back">Back</a>';
	toolbar += '<a href="#home" id="home_but" class="button">Home</a>';
	if ( foldertype == 'rec' ){
		toolbar += '<h1><img class="menuicon" src="img/record.png" /> ' + name + '</h1>';
	} 
	else {
		toolbar += '<h1><img class="menuicon" src="img/video.png" /> ' + name + '</h1>';
	}
	$('#browser' + browser + ' div[class="toolbar"]').html(toolbar);
	}
	var dataString = 'action=browseFolder&path='+path+'&browser=' + browser;
	$.getJSON("bin/backend.php",
	dataString,
	function(data) {
			$("#browser" + browser).find('ul').html('');
			$("#browser" + browser).find('span[rel="path"]').html(path);
			$("#browser" + browser).find('span[rel="currentbrowser"]').html(browser);
			$.each(data.list, function(i,list){
				if (list.type == "folder") {
				$("#browser" + browser).find('ul').append('<li class="arrow"><a href="#" rel="folder"><span class="menuname">' + list.name + '</span></a></li>');			
				}
				else if (list.type == "rec") {
				$("#browser" + browser).find('ul').append('<li class="arrow"><a href="#" rel="rec"><img class="menuicon" src="img/record.png" /><span class="menuname">' + list.name + '</span></a></li>');	
				}
				else if ( list.type == "video" ) {
				$("#browser" + browser).find('ul').append('<li class="arrow"><a href="#" rel="video"><img class="menuicon" src="img/video.png" /><span class="menuname">' + list.name + '</span></a></li>');	
				}
			});
			json_complete('#browser' + browser,'cube');
    });
}


//	[/BROWSER SECTION]

//  [TIMER SECTION]
//get fullchannel list onload

$(document).ready(function(e){ 
gen_formchanlist();
});

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

// gen Timers
function gen_timers(edit) {
	$('#timers ul[rel="timers"]').html('');
	var dataString = 'action=getTimers';
	$.getJSON("bin/backend.php",
	dataString,
	function(data) {
		$('#timers ul[rel="timers"]').append('<li><span class="menutitle">Current timers</span></li>');
		$.each(data.timer, function(i,timer){
		if ( timer.running == "1" ) {
			timerli = '<li class="arrow"><a rel="' + timer.id + '" href="#"><img class="menuicon" src="img/timerrec.png" /><span class="menuname">' + timer.date + ' ' + timer.name + '</span></a></li>';
			}
			else
			{
				if ( timer.active == "1" ) {
					timerli = '<li class="arrow"><a rel="' + timer.id + '" href="#"><img class="menuicon" src="img/timeron.png" /><span class="menuname">' + timer.date + ' ' + timer.name + '</span></a></li>';
				} else {
					timerli = '<li class="arrow"><a rel="' + timer.id + '" href="#"><img class="menuicon" src="img/timeroff.png" /><span class="menuname">' + timer.date + ' ' + timer.name + '</span></a></li>';
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
		$('#edittimer h1').html('<img class="menuicon" src="img/timers.png" / > EDIT TIMER');
		if (active = 1) 
		{
				$('#timer_active').attr("checked", "checked");
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
		submitbuttons = '<ul ref="submitbut" class="individual">';
		submitbuttons += '<li><a class="submit_form" href="#">Edit</a></li>';
		submitbuttons += '<li><a class="abutton" href="javascript:deletetimer(\'id\');">Delete</a></li></ul>';
		$('#timer').append(submitbuttons);
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
		$('.formerror').hide();  
		$(this).removeClass('active');
		var timer_name = $("input#timer_name").val();   
		if (timer_name == "") {   
			$("li#timer_name_error").show();   
			$.scrollTo('#edittimer #ul[rel="name"]');
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
		var timer_active = $("input#timer_active").val();
		var dataString = 'action=editTimer&id=' + timer_id + '&active=' + timer_active + '&channumber=' + timer_chan + '&date=' + timer_date + '&starttime=' + timer_starttime + '&endtime=' + timer_endtime; 
		$.getJSON("bin/backend.php",
		dataString,
		function(data) {
				message = data.status + ": " + data.message;
				gen_timers("true");
				json_start(this);
				showStatus( 0,message );
				return false;
				});
		return false;
}); 

function showStatus( timeout, message ) { 
    if( timeout == 0 ) { 
		$('#timer_status').html(message);
		$('#timer_status').show();
        setTimeout( function() { showStatus( 1, message ); }, 4000 ); 
    } else if( timeout == 1 ) { 
	$('#timer_status').hide();
    } 
}
//  [/TIMER SECTION]