function ajax(session)
{
    var xhr=null;

    xhr = new XMLHttpRequest();
    //on définit l'appel de la fonction au retour serveur
    xhr.onreadystatechange = function() { alert_ajax(xhr, session); };

    xhr.open("GET", "ram/" + session + "/streamstatus.php", true);
    xhr.send(null);
}

function alert_ajax(xhr, session)
{
	if (xhr.readyState==4)
	{
		var docXML= xhr.responseXML;
		var streamstatus = null;
		var items = docXML.getElementsByTagName("streamstatus");
		streamstatus = items.item(0).firstChild.data;
		var items2 =  docXML.getElementsByTagName("message");
		errmessage = items2.item(0).firstChild.data;
		if ( streamstatus == 'error' )
			//this.location.href = 'error.php';
			errorMsg(errmessage);
		else
			swapPic(session);
	}
}

function playmusic(path,name)
{
    var xhr=null;

    xhr = new XMLHttpRequest();
    //on définit l'appel de la fonction au retour serveur
    xhr.onreadystatechange = function() { openpls(xhr); };
    encpath=escape(path);
    encname=escape(name);
    xhr.open("GET", "genplaylist.php?path=" + encpath + "&name=" + encname, false);
    xhr.send(null);

}

function openpls(xhr)
{
        if (xhr.readyState==4)
        {
                var docXML= xhr.responseXML;
                var streamstatus = null;
                var items = docXML.getElementsByTagName("m3u")

                streamstatus = items.item(0).firstChild.data;
                if ( streamstatus == 'error' ) {
                        this.location.href = 'error.php';
               		}
                
		//this.location.href = 'playlist/playlist.m3u';        
		//document.s1.Play();
	}		
}
