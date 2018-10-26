<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Photo Decision</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script src="includes/jaxMe.js" type="text/javascript"></script>
<script>
$(document).ready(function() {

	setInterval(function(){getDBStats("dbStats","yes");}, 30000);
	
	
	$("#undecidedPhoto").html(getUndecidedPhotoFromDB("https://chriscarini.com/organizedPhotos/includes/getUndecidedPhoto.php","undecidedPhoto",600,null));
	
	
	
	$("div.option").click(
	function()
	{
		var accepted = false;
		if( $(this).attr('id') == "yes" )
		{
			// Set Photo Decission
			setPhotoDecision($("img#returnedImage").attr('src').replace("_800px.gif",".JPG"),'1');	

			// Increment Accepted Counter
			$("div#accepted").html( parseInt($("div#accepted").html()) + 1 );
			
			accepted = true;
		}
		else if( $(this).attr('id') == "no" )
		{
			// Set Photo Decission
			setPhotoDecision($("img#returnedImage").attr('src').replace("_800px.gif",".JPG"),'-1');

			// Increment Denied Counter
			$("div#denied").html( parseInt($("div#denied").html()) + 1 );
		}
		else
		{			
			// Increment Undecided Counter
			$("div#undecided").html( parseInt($("div#undecided").html()) + 1 );
		}
		
		// Increment Total Attempted
		$("div#total").html( parseInt($("div#total").html()) + 1 );
		
		// Change Photo to Random One
		setTimeout(function(){$("#undecidedPhoto").html(getUndecidedPhotoFromDB("https://chriscarini.com/organizedPhotos/includes/getUndecidedPhoto.php","undecidedPhoto",600));},500,null);
		
		// Print Results to console for debugging
		console.log($("#undecidedPhoto").html()+': '+$(this).attr('id')+' : '+accepted);
	});
}); 	

</script>
<style>
body {
	font-family:Futura, 'Century Gothic', AppleGothic, sans-serif;
	font-size:32px;
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
    /*top: 50%;*/
    left: 50%;
    width:800px;
    /*margin-top: -112px; /*set to a negative number 1/2 of your height*/
    margin-left: -400px; /*set to a negative number 1/2 of your width*/
    border: 2px solid #666;
    background-color: #f3f3f3;
	display:table;
}
#question {
	background-color:#CCCCCC;
	position:relative;
	width:602px;
	padding:10px;
	text-align:center;
	font-size:16px;
	vertical-align:middle;
	display:table-cell;
	z-index:10;
}
.option {
	position:absolute;
	width:90px;
	height:100%;
	padding:0px 10px 0px 10px;
	text-align:center;
	line-height:100%;
}
#yes {
	background-color:#90FF90;
	left:0;
	float:left;
}
#no {
	background-color:#FF9090;
	right:0;
	float:right;
}
#undecidedPhoto {
	font-weight:bolder;
	line-height:1em;
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
	width:175px;
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

<div id="stats">
	<div id="accepted">0</div>
	<div id="denied">0</div>
	<div id="total">0</div>
	<div id="undecided">0</div>
</div>

<div id="qa">
	<div id="yes" class="option">yes</div>
	<div id="question"><div id="top">Do you want to display this photo in albums?</div><div id="undecidedPhoto">...</div><div id="bottom"><i>Click Yes/No</i></div></div>
	<div id="no" class="option">no</div>
</div>

<div id="madeby">
	<div id="me">&copy; Copyright <?php echo date("Y"); ?> Chris Carini</div>
</div>
<div id="dbStats">
	<b>Album</b><br/>
	For Display: <br/>
	Not Displayed: <br/>
	Not Decided: <br/>
	<b>ALL</b><br/>
	For Display: <br/>
	Not Displayed: <br/>
	Not Decided: 
</div>
<div id="debug"></div>
</body>
</html>