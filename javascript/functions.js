function fullscreen() {
	var a = document.getElementsByTagName("a");
	for (var i = 0; i < a.length;i++) {
		if (a[i].className.match("noeffect")) {
		}
		else {
			a[i].onclick = function () {
				window.location = this.getAttribute("href");
				return false;
			};
		}
	}
}

function hideURLbar() {
	window.scrollTo(0, 0.9);
}


window.onload = function () {
	fullscreen();
	hideURLbar();
}
function updateOrientation() {
     switch(window.orientation) {
     case 0:
         orient = "portrait";
         break;
     case -90:
         orient = "landscape";
         break;
     case 90:
         orient = "landscape";
         break;
     case 180:
         orient = "portrait";
         break;
     }
     document.body.setAttribute("orient", orient);
     window.scrollTo(0, 1);

}
function sendForm(formid) {
	var frm;
	frm = document.getElementById(formid);
	frm.submit();
}

function swapPic(session) {
        document.getElementById('videofeed').src = "ram/" +session +"/stream.m3u8";
}

function openSelectDate(timer_year,timer_month,timer_day) {
	var now = new Date();
	if ( timer_year == null ) {
	var now_year = now.getFullYear();
	var now_month = now.getMonth()+1;
	var now_day = now.getDate();
	}
	else
	{
	var now_year = timer_year;
	var now_month = timer_month;
	var now_day = timer_day;
	}
	var layer = 'layer_date';
	var days = { };
	var years = { };
	var months = { '01': '01', '02': '02', '03': '03', '04': '04', '05': '05','06': '06', '07': '07', '08': '08', '09': '09', '10': '10', '11': '11', '12': '12' };
	
	for( var i = 1; i < 32; i += 1 ) {
		days[i] = str_pad(i, 2, '0', 'STR_PAD_LEFT');
	}

	for( i = now.getFullYear(); i < now.getFullYear()+5; i += 1 ) {
		years[i] = i;
	}

	SpinningWheel.addSlot(years, 'right', now_year );
	SpinningWheel.addSlot(months, '', now_month);
	SpinningWheel.addSlot(days, 'right', now_day);	
	SpinningWheel.setCancelAction(cancel_date);
	SpinningWheel.setDoneAction(done_date);
	
	SpinningWheel.open();
}

function done_date() {
	var results = SpinningWheel.getSelectedValues();
	
	document.getElementById('layer_date').innerHTML = results.keys.join('-');
	document.timer.timer_date.value = results.keys.join('-');
	
}

function cancel_date() {
}
function openSelectTime(layer,timer_hour,timer_minute) {
	if ( timer_hour == null ) {
	var now = new Date();
	var now_hour = now.getHours();
	var now_minute = now.getMinutes()+1;
	}
	else
	{
	var now_hour = timer_hour;
	var now_minute = timer_minute;
	}
	var hours = { };
	var minutes = { };
	
	for( var i = 0; i < 24; i += 1 ) {
		hours[i] = str_pad(i,2,'0','STR_PAD_LEFT');
	}

	for( var i = 0; i < 60; i += 1 ) {
		minutes[i] = str_pad(i,2,'0','STR_PAD_LEFT');
	}

	SpinningWheel.addSlot(hours, 'right', now_hour);
	//SpinningWheel.addSlot({ separator: 'h' }, 'readonly shrink');
	SpinningWheel.addSlot(minutes, '', now_minute);
	
	SpinningWheel.setCancelAction( function() {} );
	SpinningWheel.setDoneAction(function () {var results = SpinningWheel.getSelectedValues(); document.getElementById(layer).innerHTML = results.values.join('h');if ( layer == 'layer_starttime' ) { var forminput = 'timer_starttime'; } else { var forminput = 'timer_endtime'; }; eval ("document.timer." + forminput + ".value = results.values.join('')");  });
	SpinningWheel.open();
}

function str_pad (input, pad_length, pad_string, pad_type) {
    // Returns input string padded on the left or right to specified length with pad_string  
    // 
    // version: 909.322
    // discuss at: http://phpjs.org/functions/str_pad    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // + namespaced by: Michael White (http://getsprink.com)
    // +      input by: Marco van Oort
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: str_pad('Kevin van Zonneveld', 30, '-=', 'STR_PAD_LEFT');    // *     returns 1: '-=-=-=-=-=-Kevin van Zonneveld'
    // *     example 2: str_pad('Kevin van Zonneveld', 30, '-', 'STR_PAD_BOTH');
    // *     returns 2: '------Kevin van Zonneveld-----'
    var half = '', pad_to_go;
     var str_pad_repeater = function (s, len) {
        var collect = '', i;
 
        while (collect.length < len) {collect += s;}
        collect = collect.substr(0,len); 
        return collect;
    };
 
    input += '';    pad_string = pad_string !== undefined ? pad_string : ' ';
    
    if (pad_type != 'STR_PAD_LEFT' && pad_type != 'STR_PAD_RIGHT' && pad_type != 'STR_PAD_BOTH') { pad_type = 'STR_PAD_RIGHT'; }
    if ((pad_to_go = pad_length - input.length) > 0) {
        if (pad_type == 'STR_PAD_LEFT') { input = str_pad_repeater(pad_string, pad_to_go) + input; }        else if (pad_type == 'STR_PAD_RIGHT') { input = input + str_pad_repeater(pad_string, pad_to_go); }
        else if (pad_type == 'STR_PAD_BOTH') {
            half = str_pad_repeater(pad_string, Math.ceil(pad_to_go/2));
            input = half + input + half;
            input = input.substr(0, pad_length);        }
    }
 
    return input;
}

function checkform()
{
	
	if ( document.timer.timer_name.value=="" )
	{
	alert('Recording name is missing');
	return false;
	}
	else if ( document.timer.timer_chan.value=="" )
	{
	alert('Select a channel to record');
	return false;
t
	}
	else if ( document.timer.timer_date.value=="" )
	{
	alert('Select recording date');
	return false;
	}
	else if ( document.timer.timer_starttime.value=="" )
	{
	alert('Select recording start time');
	return false;
	}
	else if ( document.timer.timer_endtime.value=="" )
	{
	alert('Select recording end time');
	return false;
	}
	
	return true;

}

function addplayer(path,name,track) {


	playercode= "<embed src='" + escape(track[0]) + "' width='0' height='0' autoplay='false' name='player' type='audio/mp3' loop='true' controller='false' "; 
	for ( var i=1; i<track.length; i+=1 ){
		playercode += "qtnext" + i + "='<" + escape(track[i]) + ">' ";

	}
	playercode+= "></embed>"; 
	playmusic(path,name);
	document.getElementById('div_player').innerHTML = playercode;

}
function errorMsg(errmessage) {
	htmlmsg="<span class='graytitle'>Error</span><ul class='pageitem'><li class='textbox'><span class='header'>Error</span><p><strong>" + errmessage + "</strong></li></ul>";
	document.getElementById('content').innerHTML = htmlmsg;
}

