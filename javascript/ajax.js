function ajax()
{
    var xhr=null;
    
    xhr = new XMLHttpRequest();
    //on définit l'appel de la fonction au retour serveur
    xhr.onreadystatechange = function() { alert_ajax(xhr); };
        
    xhr.open("GET", "streamstatus.php", true);
    xhr.send(null);
}

function alert_ajax(xhr)
{
	if (xhr.readyState==4)
	{
		var docXML= xhr.responseXML;
		var streamstatus = null;
		var items = docXML.getElementsByTagName("streamstatus")
	
		streamstatus = items.item(0).firstChild.data;
		if ( streamstatus == 'error' )
			this.location.href = 'error.php';
		else
			swapPic();
	}
}
