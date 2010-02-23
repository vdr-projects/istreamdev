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

function swapPic() {
        document.getElementById('videofeed').src = "ram/stream.m3u8";
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
	var months = { 1: 'Jan', 2: 'Feb', 3: 'Mar', 4: 'Apr', 5: 'May', 6: 'Jun', 7: 'Jul', 8: 'Aug', 9: 'Sep', 10: 'Oct', 11: 'Nov', 12: 'Dec' };
	
	for( var i = 1; i < 32; i += 1 ) {
		days[i] = i;
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
	document.getElementById('layer_date').innerHTML = results.values.join('-');
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
	
	for( var i = 00; i < 24; i += 1 ) {
		hours[i] = i;
	}

	for( var i = 00; i < 60; i += 1 ) {
		minutes[i] = i;
	}

	SpinningWheel.addSlot(hours, 'right', now_hour);
	//SpinningWheel.addSlot({ separator: 'h' }, 'readonly shrink');
	SpinningWheel.addSlot(minutes, '', now_minute);
	
	SpinningWheel.setCancelAction( function() {} );
	SpinningWheel.setDoneAction(function () {var results = SpinningWheel.getSelectedValues(); document.getElementById(layer).innerHTML = results.values.join('');if ( layer == 'layer_starttime' ) { var forminput = 'timer_starttime'; } else { var forminput = 'timer_endtime'; }; eval ("document.timer." + forminput + ".value = results.keys.join('')");  });
	SpinningWheel.open();
}

