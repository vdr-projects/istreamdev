<?php
print "<body class=\"ipodlist\" onorientationchange=\"updateOrientation();\" onload=\"updateOrientation();\">\r\n";
print "<div id=\"topbar\" class=\"transparent\">\r\n";
print "<div id=\"leftnav\">\r\n";
print "	<a href=\"javascript:sendForm('getback')\">Back</a></div>\r\n";
print "<div id=\"rightnav\">\r\n";
print "	<a href=\"index.php\"><img alt=\"home\" src=\"images/home.png\" /></a></div>\r\n";

print "<div id=\"title\">iStreamdev</div>\r\n";
print "</div>\r\n";
print "<div id=\"content\">\r\n";
print "	<ul>";
print "		<li><a class=\"noeffect\" href=\"javascript:document.s1.Play();\">";
print "		<span class=\"number\">1</span><span class=\"auto\"></span><span class=\"name\">Boom Boom Pow</span><span class=\"time\">4:11</span>";
print "		</a></li>";

print "		<li><a class=\"noeffect\" href=\"javascript:document.s2.Play();\">";
print "		<span class=\"number\">2</span><span class=\"auto\"></span><span class=\"name\">Rock That Body</span><span class=\"time\">4:28</span></a></li>";

print "</div>\r\n";

print "<div style=\"position:absolute; left:0; top:0\">";
print "<embed enablejavascript=\"true\" autoplay=\"false\" height=\"0\" name=\"s1\" src=\"http://a1.phobos.apple.com/us/r2000/004/Music/22/97/88/mzm.lzzanxzf.aac.p.m4a\" width=\"0\" loop=\"true\" controller=\"false\" qtnext1=\"<http://a1.phobos.apple.com/us/r2000/012/Music/a9/6e/92/mzm.chcdvuzt.aac.p.m4a>\" />";
print "<embed enablejavascript=\"true\" autoplay=\"false\" height=\"0\" name=\"s2\" src=\"http://a1.phobos.apple.com/us/r2000/012/Music/a9/6e/92/mzm.chcdvuzt.aac.p.m4a\" width=\"0\" />";
print "</div>";
?>

