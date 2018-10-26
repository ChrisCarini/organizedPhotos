var gStr, t, delay = 500;
var lastAlbum = "";
var lastCount = 0;

function getDBStats(divToUpdate,detail,count)
{
	if(count != lastCount || count == -1 )
	{
		var url="https://chriscarini.com/organizedPhotos/includes/getDBStats.php";
		if(typeof detail !== 'undefined')
		{
			url=url+"?detail="+detail;
		}
		else
		{
			url=url+"?detail=no";
		}
		url=url+"&album="+lastAlbum;
		ajaxQuery(url,divToUpdate);
	}
	lastCount = count;
}

function setPhotoDecision(photo,decision)
{
	var url="https://chriscarini.com/organizedPhotos/includes/setPhotoDecision.php";
	url=url+"?photo="+photo+"&decision="+decision;
	ajaxQuery(url,null);
	//ajaxQuery(url,"debug");
}

function getUndecidedPhotoFromDB(url,divToUpdate,width,index)
{
	url=url+"?width="+width;
	if(typeof index !== 'undefined')
	{
		url=url+"&index="+index;
	}
	ajaxQuery(url,divToUpdate);
	setTimeout(function()
	{
		split = ($(("img#returnedImage")).attr('src')).split("/");
		lastAlbum = split[split.length-2];
		//getDBStats("dbStats");
	},5000);
}

function showPhotoEXIFData(str)
{
	if (str.length == 0)
	{
		return;
	}
	else
	{
		gStr = str;
		clearTimeout(t);
		t=setTimeout("showPhotoEXIFData2()",delay);
	}
}

function showPhotoEXIFData2()
{
	var str = encodeURIComponent(gStr);
	var url="https://chriscarini.com/organizedPhotos/includes/getEXIF.php";
	url=url+"?q="+str;
	ajaxQuery(url,"EXIFDataCell");
}

function ajaxQuery(myUrl,divToUpdate)
{
	$.ajax({
	  url: myUrl+"&sid="+Math.random(),
      success:function ( data, textStatus, jqXHR ) {
                if(divToUpdate != null)
                {
                    $("#"+divToUpdate+"").html(data);
                }
            }
    });
}
/*

var xmlHttp=GetXmlHttpObject();

function ajaxQuery(url,divToUpdate)
{
	if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
		return;
	}
	url=url+"&sid="+Math.random();
	xmlHttp.onreadystatechange=function()
	{ 
		if (xmlHttp.readyState==0)
		{
			document.getElementById(divToUpdate).innerHTML="The request is not initialized...";
		}
		if (xmlHttp.readyState==1)
		{
			document.getElementById(divToUpdate).innerHTML="The request has been set up...";
		}
		if (xmlHttp.readyState==2)
		{
			document.getElementById(divToUpdate).innerHTML="The request has been sent...";
		}
		if (xmlHttp.readyState==3)
		{
			if(divToUpdate != null)
				document.getElementById(divToUpdate).innerHTML="Running query...";
		}		
		if (xmlHttp.readyState==4)
		{
			if(divToUpdate != null)
				document.getElementById(divToUpdate).innerHTML=xmlHttp.responseText;
		}
	}
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null);
}

function GetXmlHttpObject()
{
	var xmlHttp=null;
	try// Firefox, Opera 8.0+, Safari
	{xmlHttp=new XMLHttpRequest();}
	catch (e)// Internet Explorer
	{
		try{xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");}
		catch (e){xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");}
	}
	return xmlHttp;
}*/
