<?php
if (file_exists('config.php'))
        include ('config.php');
else
        include ('config_default.php');
include ('bin/auth.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" manifest="istreamdev.manifest">
    <head>
        <meta charset="UTF-8" />
        <title>iStreamdev</title>
        <link rel="stylesheet" href="min/?b=css&f=jqtouch.css,theme.css,istreamdev.css,spinningwheel.css" type="text/css" media="screen" />
        <script src="min/?b=js&f=jquery-1.4.2.min.js,jqtouch.js,jqtouch.transitions.js,istreamdev.js,functions.js,spinningwheel.js" type="text/javascript" charset="utf-8"></script>
    </head>
    <body>
	<div id="loader"></div>
	<div id="status_box"></div>
	<div id="jqt">
	<!---------------------------------- HOME SCREEN (always present) ---------------------------------->
        	
		<div id="home" class="current">
            <div class="toolbar">
                <h1>HOME</h1>
            </div>
			<ul class="rounded" id="runningsessions">
				<li><span class="menutitle">SESSIONS</span></li>
				<li>Checking running session</li>
			</ul>
        </div>
	 <!----------------------------------/ HOME SCREEN ---------------------------------->
	 <!---------------------------------- CHAN CATEGORY SCREEN ---------------------------------->
        <!----------------------------------categories------------------------------>
		<div id="categories">
            <div class="toolbar">
                <a href="#" class="back">Home</a>
                <h1><img class="menuicon" src="img/tv.png" /> CATEGORIES</h1>
            </div>
            <ul id="cat_menu" class="rounded">
			</ul>
        </div>
	<!----------------------------------channels------------------------------>
		<div id="channels">
			<div class="toolbar">
			<a href="#" class="back">Back</a>
			<a href="#home" id="home_but" class="button">Home</a>
                <h1><img class="menuicon" src="img/tv.png" /> CHANNELS</h1>
			</div>
			<ul id="chan_menu" class="rounded">
			</ul>
		</div>
	<!---------------------------------- /CHAN CATEGORY SCREEN ---------------------------------->
	<!---------------------------------- STREAM SCREEN ---------------------------------->
		<!------------------------ tv start stream ----------------------------------------->
		<div id="streamchannel">
			<div class="toolbar">
			<a href="#" class="back">Back</a>
			<a href="#home" id="home_but" class="button">Home</a>
                <h1><img class="menuicon" src="img/tv.png" />Channel</h1>
			</div>
			<center><ul class="thumb" style="width:90px"><img class="thumbnail" id="thumbnail" src="" onerror="this.src='img/nologoTV.png'" /></ul></center>
			<ul class="streaminfo">
			<li><span class="name_now"></span>
			<span class="epgtime_now"></span>
			<span class="desc_now"></span></li>
			<li>
			<span class="name_next"></span>
			<span class="epgtime_next"></span></li>
			</ul>
			<center>
			<br>
			<span class="streamButton"><a id="edge" href="#">Edge</a></span><span class="streamButton"><a id="3g" href="#" class="cube"> 3G </a></span><span class="streamButton"><a id="wifi" href="#" class="cube">Wifi</a></span><span class="recButton"><a id="rec" href="#" class="cube">Rec.</a></span>
			<br><br>
			</center>
			<div rel="dataholder" style="visibility:hidden">
                <span rel="type"></span>
                <span rel="url"></span>
				<span rel="number"></span>
				<span rel="channame"></span>
            </div>			
		</div>
		
		<div id="streamrec">
			<div class="toolbar">
			<a href="#" class="back">Back</a>
			<a href="#home" id="home_but" class="button">Home</a>
                <h1>Recordings</h1>
			</div>
			<center><ul class="thumb" style="width:90px"><img class="thumbnail" id="thumbnail" src="" onerror="this.src='img/nologoTV.png'" /></ul></center>
			<ul class="streaminfo">
				<li>
					<span class="name_now"></span>
					<span class="epgtime_now"></span>
					<span class="desc_now"></span>
				</li>
			</ul>
			<center><br>
			<span class="streamButton"><a id="edge" href="#">Edge</a></span><span class="streamButton"><a id="3g" href="#" class="cube"> 3G </a></span><span class="streamButton"><a id="wifi" href="#" class="cube">Wifi</a></span>
			<br><br>
			</center>
			<div rel="dataholder" style="visibility:hidden">
                <span rel="type"></span>
                <span rel="url"></span>
            </div>	
		</div>
		<div id="streamvid">
			<div class="toolbar">
			<a href="#" class="back">Back</a>
			<a href="#home" id="home_but" class="button">Home</a>
                <h1>Video</h1>
			</div>
			<center><ul class="thumb" style="width:190px;"><img class="thumbnail" id="thumbnail" src="" /></ul></center>
			<ul class="streaminfo">
				<li>
					<span class="name_now"></span>
					<span class="epgtime_now"></span>
					<span class="desc_now"></span>
				</li>
			</ul>
			<center><br>
			<span class="streamButton"><a id="edge" href="#">Edge</a></span><span class="streamButton"><a id="3g" href="#" class="cube"> 3G </a></span><span class="streamButton"><a id="wifi" href="#" class="cube">Wifi</a></span>
			<br><br>
			</center>
			<div rel="dataholder" style="visibility:hidden">
                <span rel="type"></span>
                <span rel="url"></span>
            </div>	
		</div>

		<div id="streaming">
			<div class="toolbar">
			<a href="#" class="back">Back</a>
			<a href="#home" id="home_but" class="button">Home</a>
                <h1></h1>
			</div>
			<center><ul class="thumb" id="player"><img class="thumbnail" id="thumbnail" src="" /></ul></center>
			<ul class="streamstatus">
				<span class="title">Status</span>
				<span class="mode"></span>
			</ul>
			<ul class="streaminfo">
			</ul>
			<center>
				<span class="streamButton"><a rel="stopbroadcast" href="#">Stop stream</a></span>
				<br><br>
			</center>
			<div rel="dataholder" style="visibility:hidden">
                <span rel="session"></span>
				<span rel="name"></span>
				<span rel="thumbwidth"></span>
				<span rel="thumbheight"></span>
				<span rel="number"></span>
            </div>						
		</div>		
		
		<!---------------------------------- /STREAM SCREEN ---------------------------------->
		<!---------------------------------- TIMERS SCREEN ---------------------------------->
		
		<div id="timers">
			<div class="toolbar">
			<a href="#" class="back">Home</a>
				<h1><img class="menuicon" src="img/timers.png" /> TIMERS</h1>
			</div>
			
			<ul class="rounded" rel="timers">
			</ul>
			<ul class="rounded">
			<li class="arrow"><a href="#" rel="new"><span class="menuname">New Timer</span></a></li>
			</ul>
		</div>
		
		<div id="edittimer">
			<div class="toolbar">
			<a href="#" class="back">Back</a>
			<a href="#home" id="home_but" class="button slideup">Home</a>
                <h1></h1>
			</div>
			<form name="timer" id="timer" onsubmit="checktimerform();">
			<ul class="rounded">
				<li><span class="timertitle">Active</span><span class="toggle"><input id="timer_active" name="timer_active" type="checkbox" /></span></li>
			</ul>
			<ul class="rounded" rel="name">
				<li><span class="timertitle">Name</span></li>
				<li class="formerror" id="timer_name_error"><span class="formerrormsg">Recording name is missing</span></li>
				<li><input type="text" name="name" placeholder="Enter recording name" id="timer_name" style="color: #FFFFFF" /></li>
			</ul>
			<ul class="rounded" rel="channel">
				<li>
					<span class="timertitle">Channel</span>
				</li>
				<li>
					<select id="timer_chan">
					</select>
				</li>
			</ul>
			<ul class="rounded" rel="date">
				<li><span class="timertitle">Date</span></li>
				<li class="formerror" id="timer_date_error"><span class="formerrormsg">Date is missing</span></li>
				<li class="arrow"><a id="a_date" class="abutton" href="#" onClick="$('#timer_date_error').hide();"><span class="menuname" id="layer_date">Select date</span></a></li>
				
			</ul>
			<ul class="rounded" rel="stime">
				<li><span class="timertitle">Start time</span></li>
				<li class="formerror" id="timer_starttime_error"><span class="formerrormsg">Starting time is missing</span></li>
				<li class="arrow"><a id="a_starttime" class="abutton" href="#" onClick="$('#timer_starttime_error').hide();"><span class="menuname" id="layer_starttime">Select start time</span></a></li>
				
			</ul>
			<ul class="rounded" rel="etime">
				<li><span class="timertitle">End time</span></li>
				<li class="formerror" id="timer_endtime_error"><span class="formerrormsg">Ending time is missing</span></li>
				<li class="arrow"><a id="a_endtime" class="abutton" href="#" onClick="$('#timer_endtime_error').hide();"><span class="menuname" id="layer_endtime">Select end time</span></a></li>
			</ul>
			<input name="timer_id" type="hidden" id="timer_id" value="" />
			<input name="timer_date" type="hidden" id="timer_date" value="" />
			<input name="timer_starttime" type="hidden" id="timer_starttime" value="" />
			<input name="timer_endtime" type="hidden" id="timer_endtime" value="" />
			</form>
		</div>
		<!---------------------------------- /TIMERS SCREEN ---------------------------------->
		<!------------------------------------ EPG ------------------------------------------->
		<div id="epg">
			<div class="toolbar">
					<a href="#" class="back">Home</a>
						<h1>EPG</h1>
				</div>
			<ul class="rounded">
				<li class="arrow"><a href="#now"><span class="menuname">What's Now</span></a></li>
				<li class="arrow"><a href="#at"><span class="menuname">What's At</span></a></li>
				<li class="arrow"><a href="#on"><span class="menuname">What's On</span></a></li>
			</ul>
		</div>
		
		<div id="on">
			<div class="toolbar">
				<a href="#" class="back">Back</a>
				<a href="#home" id="home_but" class="button slideup">Home</a>
                <h1>WHAT'S ON</h1>
			</div>
			<form name="epg_chanselector" id="epg_chanselector" action="">
				<ul class="rounded">
					<li><span class="timertitle">Select a Channel</span></li>
					<li><select id="epg_chan" onchange="epg_selectchan()">
						<optgroup label="NATIONALES & TNT">
							<option value="1">TF1</option>
							<option value="2">FRANCE 2</option>
							<option value="3">FRANCE 3</option>
							<option value="4">FRANCE 4</option>
							<option value="5">TV5</option>
							<option value="6">M6</option>
						</optgroup>
						<optgroup label="DIVERTISSEMENT">
							<option value="11">COMEDIE!</option>
							<option value="12">13EME RUE</option>
							<option value="13">FOX LIFE</option>
						</optgroup>
					</select>
					</li>
				</ul>
			</form>	
				
		</div>
		<!------------------------------------ /EPG ------------------------------------------->
	</div>
    </body>
</html>