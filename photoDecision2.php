<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Photo Decision 2</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="includes/jaxMe.js" type="text/javascript"></script>
<script>
var NUM_PHOTOS = 5;
var inPhoto = false;
var lastLarge = "";

function getFoto(id)
{
	if(id=="all")
	{
		$.ajax({
		  url: "https://chriscarini.com/organizedPhotos/includes/getUndecidedPhoto.php?num="+NUM_PHOTOS+"&width=300"
		}).done(function ( data ) {
			split = (data).split("|");
			for(var x=1;x<split.length;x++)
			{
				$("#undecidedPhoto"+x).html(split[x-1]);
			}
		});
	}
	else
	{	
		$.ajax({
		  url: "https://chriscarini.com/organizedPhotos/includes/getUndecidedPhoto.php?width=300&index="+id
		}).done(function ( data ) {
		  $("#undecidedPhoto"+id).html(data);
		});
	}
}

$(document).ready(function() {

	setInterval(function(){getDBStats("dbStats","no",parseInt($("div#total").html()));}, 30000);
	//setInterval(function(){console.log("In Photo: "+inPhoto);}, 1000);
	
	(function(){getDBStats("dbStats","no",-1);})();
	$('div#zoom').hide(200);
	getFoto("all");
	
	$("div.photo").mouseover(
	function()
	{
		id = ($(this).attr('id'));
		id = id.charAt(id.length-1);
		$("img#largePhoto").attr('src',$("img#returnedImage"+id).attr('src'));
		$("img#largePhoto").attr('height',$(window).height()-200);
		var newWidth = (($(window).height()-200)*($("img#returnedImage"+id).width()/$("img#returnedImage"+id).height()));
		$("img#largePhoto").attr('width', newWidth );
		$("img#largePhoto").css('margin-left', -newWidth/2 );
		
		/*$("img#largePhoto").attr('width', 800 );
		$("img#largePhoto").attr('height',$(window).height()-120);
		var newHeight = ((800)*($("img#returnedImage"+id).height()/$("img#returnedImage"+id).width()));
		$("img#largePhoto").attr('width', newWidth );
		$("img#largePhoto").css('margin-left', -newWidth/2 );*/
		
		$('div#zoom').show(100);
	}).mouseout(
	function()
	{
		//console.log("Try to shrink with: "+inPhoto+" ["+!inPhoto+"]");
		setTimeout(function()
		{
			if(!inPhoto)
			{
				$('div#zoom').hide(100);
			}
		},200);
	});
	
	$("div#zoom").hover(
	function()
	{
		inPhoto = true;
	},
	function()
	{
		inPhoto = false;
		$('div#zoom').hide(100);
		setTimeout(function()
		{
			inPhoto = false;
			$('div#zoom').hide(100);
		},100);
	});
	
	$("div.option").click(
	function()
	{
		id = ($(this).attr('id')).substring(1);
		var accepted = false;
		
		if( ($(this).attr('id')).substring(0,1) == "y" )
		{
			accepted = true;
			if( id == "Master")
			{
				for(var x = 1; x<=NUM_PHOTOS; x++)
				{
					setPhotoDecision($("img#returnedImage"+x).attr('src').replace("_800px.gif",".JPG"),'1');
					console.log($("img#returnedImage"+x).attr('src').replace("_800px.gif",".JPG")+': '+$(this).attr('id')+' : '+accepted);
				}
				
				// Increment Accepted Counter & Total Attempted
				$("div#accepted").html( parseInt($("div#accepted").html()) + 5 );
				$("div#total").html( parseInt($("div#total").html()) + 5 );
			}
			else
			{
				// Set Photo Decision
				setPhotoDecision($("img#returnedImage"+id).attr('src').replace("_800px.gif",".JPG"),'1');
				console.log($("img#returnedImage"+id).attr('src').replace("_800px.gif",".JPG")+': '+$(this).attr('id')+' : '+accepted);

				// Increment Accepted Counter & Total Attempted
				$("div#accepted").html( parseInt($("div#accepted").html()) + 1 );
				$("div#total").html( parseInt($("div#total").html()) + 1 );
			}
		}
		else if( ($(this).attr('id')).substring(0,1) == "n" )
		{
			accepted = false;
			if( id == "Master")
			{
				for(var x = 1; x<=NUM_PHOTOS; x++)
				{
					setPhotoDecision($("img#returnedImage"+x).attr('src').replace("_800px.gif",".JPG"),'-1');
					console.log($("img#returnedImage"+x).attr('src').replace("_800px.gif",".JPG")+': '+$(this).attr('id')+' : '+accepted);
				}
				
				// Increment Denied Counter & Total Attempted
				$("div#denied").html( parseInt($("div#denied").html()) + 5 );
				$("div#total").html( parseInt($("div#total").html()) + 5 );
			}
			else
			{
				// Set Photo Decision
				setPhotoDecision($("img#returnedImage"+id).attr('src').replace("_800px.gif",".JPG"),'-1');
				console.log($("img#returnedImage"+id).attr('src').replace("_800px.gif",".JPG")+': '+$(this).attr('id')+' : '+accepted);

				// Increment Denied Counter & Total Attempted
				$("div#denied").html( parseInt($("div#denied").html()) + 1 );
				$("div#total").html( parseInt($("div#total").html()) + 1 );
			}
		}
		else
		{			
			// Increment Undecided Counter & Total Attempted
			$("div#undecided").html( parseInt($("div#undecided").html()) + 1 );
			$("div#total").html( parseInt($("div#total").html()) + 1 );
		}
		
		if(id != "Master")
		{
			getFoto(id);
		}
		else if(id == "Master")
		{
			getFoto("all");
		}
		else
		{
			alert("WTF?!");
		}
	});
}); 	

</script>
<style>
body {
	font-family:Futura, 'Century Gothic', AppleGothic, sans-serif;
	font-size:16px;
}
#stats {
	position:absolute;
	bottom:10px;
	right:10px;
	width:75px;
	text-align:center;
	color:#FFF;
}
#accepted {
	background-color:#90DD90;
}
#denied {
	background-color:#DD9090;
}
#total {
	background-color:#9099DD;
}
#undecided {
	background-color:#DDDD90;
}
#qa {
    position:relative;
    border: 2px solid #666;
    background-color: #f3f3f3;
	display:table;
}
/*
.option {
	position:inherit;
	min-width:90px;
	height:40px;
	vertical-align:middle;
	padding:0px 10px 0px 10px;
	text-align:center;
	line-height:100%;
	display:table-cell;
}
*/
.option {
	position:inherit;
	min-width:90px;
	vertical-align:middle;
}
.photo {
	min-width:200px;
	max-width:400px;
}
.yes {
	background-color:#90FF90;
	left:0;
	float:left;
}
.no {
	background-color:#FF9090;
	right:0;
	float:right;
}
#undecidedPhoto {
	font-weight:bolder;
	line-height:1em;
}
#zoom {
	position: fixed;
	top: 0%;
	left: 50%;
	max-height:90%;
	margin-top: 10px;
	z-index: 1000;
}
#madeby {
	position:absolute;
	bottom:10px;
	left:10px;
	width:auto;
	text-align:center;
	color:#000;
	font-size:18px;
}
#dbStats {
	position:absolute;
	bottom:10px;
	right:95px;
	width:225px;
	text-align:left;
	font-size:14px;
	color:#BDBDBD;
}
#debug {
	font-size:14px;
}
</style>
</head>

<body>
<div align="center">
<table width="95%" border="0">
  <tr>
    <th align="center"><div>1</div></th>
    <th align="center"><div>2</div></th>
    <th align="center"><div>3</div></th>
    <th align="center"><div>4</div></th>
    <th align="center"><div>5</div></th>
  </tr>
  <tr height="455px">
    <td align="center"><div class="photo" id="undecidedPhoto1">1</div></td>
    <td align="center"><div class="photo" id="undecidedPhoto2">2</div></td>
    <td align="center"><div class="photo" id="undecidedPhoto3">3</div></td>
    <td align="center"><div class="photo" id="undecidedPhoto4">4</div></td>
    <td align="center"><div class="photo" id="undecidedPhoto5">5</div></td>
  </tr>
  <tr>
    <td align="center"><div id="qa"><div id="y1" class="yes option">yes</div><div id="n1" class="no option">no</div></div></td>
    <td align="center"><div id="qa"><div id="y2" class="yes option">yes</div><div id="n2" class="no option">no</div></div></td>
    <td align="center"><div id="qa"><div id="y3" class="yes option">yes</div><div id="n3" class="no option">no</div></div></td>
    <td align="center"><div id="qa"><div id="y4" class="yes option">yes</div><div id="n4" class="no option">no</div></div></td>
    <td align="center"><div id="qa"><div id="y5" class="yes option">yes</div><div id="n5" class="no option">no</div></div></td>
  </tr>
  <tr>
    <td align="center" colspan="5"><hr/></td>
  </tr>
  <tr>
    <td align="center" colspan="5"><div id="qa" style="width:300px"><div id="yMaster" class="yes option">yes</div><div id="nMaster" class="no option">no</div></div></td>
  </tr>
</table>
</div>
<div id="zoom">
	<img id='largePhoto' src='' />
</div>

<div id="stats">
	<div id="accepted">0</div>
	<div id="denied">0</div>
	<div id="total">0</div>
	<div id="undecided">0</div>
</div>
<div id="dbStats">
	<b>ALL</b><br/>
	For Display: <br/>
	Not Displayed: <br/>
	Not Decided: 
</div>
<div id="debug"></div>
<div id="madeby">
	<div id="me">&copy; Copyright <?php echo date("Y"); ?> Chris Carini</div>
</div>
</body>
</html>
