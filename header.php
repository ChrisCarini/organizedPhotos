<?php
/*

Linux Commands to Resize images...
for i in $(ls | grep JPG);  do j=`echo $i | cut -d . -f 1`; j=$j"_400px.gif"; convert $i -resize 400 $j ; done

*/
?>
<html>
	<head>
		<?php
		$str = $_SERVER['REQUEST_URI']; // /organizedPhotos/2012/08-August/2012-08-18-Donna%20Duathlon/
		$secondLastSlash = strrpos($str,'/',-2);
		$str = substr($str,$secondLastSlash+1,strlen($str)-$secondLastSlash-2); // 2012-08-18-Donna%20Duathlon
		
		switch(substr_count($str,"-"))
		{
			case 1:
			{
				// Must be a month or year folder...
				$str = "Folder: ".substr($str,3,strlen($str)-3);
				break;
			}
			case 3:
			{
				// Must be a photo album...
				//01234567890
				//2012-09-28-
				$year  = substr($str,0,4);
				$month = substr($str,5,2);
				$day   = substr($str,8,2);
				$album = str_replace("_"," ",substr($str,11,strlen($str)-11));
				$date  = date("F j, Y",mktime(0,0,0,$month,$day,$year));
				$str   = "Album: ".$album . " taken on ".$date; 
				break;
			}
			default:
			{
				// Not sure what it is...maybe the year folder...leave alone..
			}
		};
		echo "<title>".$str."</title>";
		?>
	<script src="/includes/jaxMe.js" type="text/javascript"></script>
	<!-- include the Tools -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
	<script src="/includes/jquery.tools.min.js"></script>
		<script type="text/javascript">
		<?php //Show my photos! ?>
		var enabled = false;
		
		$(document).ready(function(){
			
			var photos = $('a[href^="DSC"],a[href^="IMG"],a[href^="HDR"]');
			// $('a[href^="DSC"]').replaceWith('<img width="20%" src="'+$(this).attr('src')+'" />');
			//$(photos).replaceWith("<img src='"+$(this).href+"/>");
			//alert($(photos).length != 0)
			if($(photos).length != 0)
			{
				var list = $('<div id="list"/>');
				var flag = false;
				var dia = "";
				$("pre").contents().each(function(index,val){
					//alert($(val).html());
					if($(val).html() == "")
					{
						flag = true;//!flag;
					}
					if(($(val).html() == "Parent Directory") )//|| (!flag) || ($(val).html() == null && flag))
					{
						list.append($(this));
						list.append($(document.createElement('br')));
						//return false;
						dia += " |=| "+$(this).html();
						//alert($(this).html());
					}
					if(!flag)
					{
						list.append($(this));
						list.append($(document.createElement('br')));
						dia += " |=| "+$(this).html();
						//alert($(this).html());
					}
					if($(val).html() == null && flag)
					{
						list.append($(this));
						list.append($(document.createElement('br')));
						dia += " |=| "+$(this).html();
						//alert($(this).html());
					}
				});
				var count = 0;
				//Modify Photos here...
				/*$(photos).each(function() {
					var p = $('<a href="'+$(this).attr('href')+'"><img class="image" width="400px" src="'+$(this).attr('href').replace(".JPG","_400px.gif")+'"/></a>'+((count++%3==2)?"<br/>":""));
					$(this).replaceWith(p);
				});*/
				
				var albumTitle = "<? echo $album;?>";
				var albumComment = "";//"Comment on the photo album.";
				var albumDate = "<? echo $date;?>";
				var albumLocation = "";//"Milwaukee, WI";
				
				
				
				var wrapper = $('<div id="wrapper">');
				var containerMain = $('<div id="containerMain"/>');
				var mainContent = $('<div id="mainContent"/>');
				var table = $('<table cellpadding="5" id="1" align="center"/>').append($('<tbody/>'));
				
				// Make the main table footer comment section...
				var tableFooterRow = $('<tr/>').append($('<td colspan="3" width="100%" valign="top"/>').append($('<p align = "center">').html(albumComment)));
				
				// Make the main table header...
				var tableHeaderRow = $('<tr/>');
				var tableHeader = $('<td colspan="3"/>');
				var subheader;
				tableHeader.append($('<div class="cinstalltitle" align="center"/>').append($('<h1 style="margin:2px;"/>').html(albumTitle)).append(
				subheader = $('<h2 style="margin:2px;" class="space"/>').append(
					($('<span style="float:left;padding-left:20px;"/>').html("Taken on:"+albumDate))
				)));
				if(albumLocation.length != 0)
				{
					subheader.append(
						($('<span style="float:right;padding-right:20px;"/>').html("Location:"+albumLocation))
					);
				}
				tableHeaderRow.append(tableHeader);
				
				// Make the main table body...
				var tableBodyRow = $('<tr/>');
				
				var imageEXIFDataTableCell = $('<td valign="top" id="EXIFDataCell"/>');
				
				var largeImageTableCell = $('<td valign="top"/>');
				var imageDiv = $('<div class="cinstallimage_wrap" id="image_wrap2" style="opacity: 1;"/>');
				var image = $('<img id="mainIMG" width="800"/>');
				image.attr("src",$(photos).first().attr('href').replace(".JPG","_800px.gif")); // Set first large image to be first in array..
				largeImageTableCell.append(imageDiv.append(image));
				
				var up,down,slider;
				var imageScrollerTableCell = $('<td valign="top"/>').append(
				$('<div class="cinstallgradient" align="center"/>').append(
					$('<table/>').append(
						$('<tbody/>').append(
							up=$('<tr/>'),slider=$('<tr/>'),down=$('<tr/>')
						)
					)
				)
				);
				up.append($('<td/>').append($('<a class="prev cinstallbrowse cinstallup disabled"/>')));
				down.append($('<td valign="bottom"/>').append($('<a class="next cinstallbrowse cinstalldown"></a>')));
				
				var mainSliderDiv;
				slider.append( $('<td/>').append(
					$('<div id="scrollable2" class="cinstallscrollable"/>').append(
						mainSliderDiv=$('<div class="cinstallitems" style="left:0px;top:0px;"/>')
					)
				));
				//console.log(jQuery.makeArray($(photos)));
				var tmpArray = $.makeArray($(photos));
				var mySize = Math.floor( ($(window).height()-209)/85 );
				while(tmpArray.length>0)
				{
					chunk = $(tmpArray.splice(0, mySize ));
					//console.log(mySize);
					//console.log(chunk);
					var curDiv = $('<div/>');
					$(chunk).each(function() {
						var img = $('<img id="2" src="'+$(this).attr('href').replace(".JPG","_800px.gif")+'"/>');
						curDiv.append($(img));
					});
					mainSliderDiv.append(curDiv);
				}
				/*$(photos).each(function() {
					var p = $('<a href="'+$(this).attr('href')+'"><img class="image" width="400px" src="'+$(this).attr('href').replace(".JPG","_400px.gif")+'"/></a>'+((count++%3==2)?"<br/>":""));
					$(this).replaceWith(p);
				});*/
				
				tableBodyRow.append(imageScrollerTableCell,largeImageTableCell,imageEXIFDataTableCell);				
				table.append(tableHeaderRow,tableBodyRow,tableFooterRow);
				wrapper.append(containerMain.append(mainContent.append(table)));
				wrapper.append($("#containerFoot"));
				$("h1").hide(); // Hide the Album H1 Title
				$('pre').replaceWith(wrapper);
			}
			else
			{
				var hrefs = [];
				var count = 5;
				
				$('pre').find('a[href]')  // only target <a>s which have a href attribute
						.each(function() {
							if(count--<=0)
							{
								hrefs.push(this.href);
							}
						}).end();
				//console.log(hrefs);
				// hrefs = ['https://link1', 'https://link2', 'https://link3']
				var newList = $('<div id="newList"/>');
				var orderedList = $(document.createElement('ol'));
				
				$(hrefs).each(function(index,domEle){
					$(orderedList).append( $(document.createElement('li')).append(
						$(document.createElement('a'))
							.attr( {href : this} )
							.text( function()
							{
								var url = this.href;
								var rel	= (url.substring(((url.substr(0,url.length-2)).lastIndexOf('/')+1),url.length-1));
								
								switch(rel.split("-").length - 1)
								{
									case 1:
									{
										// Must be a month or year folder...
										n = new Date()
										var year = "<? echo $str;?>";
										if((n.getFullYear() > year) || (1+(n.getMonth()) >= parseInt(rel.substr(0,2),10)) )
											return "Folder: " + rel.substr(3,rel.length-3);
										else
											return;
										break;
									}
									case 6:case 5:case 4:case 3:
									{
										// Must be a photo album...
										var date = new Date(rel.substr(0,4),rel.substr(5,2)-1,rel.substr(8,2),0,0,0,0);
										if(rel.charAt(10) == "_") // must be date range...
										{
											//2011-06-07_09-Snowdon
											//0123456789012345567
											var album = (rel.substr(14,rel.length-14)).replace(/\_/g, ' ');
											var date2 = new Date(rel.substr(0,4),rel.substr(5,2)-1,rel.substr(11,2),0,0,0,0);
											return album + " taken between " + date.toLocaleDateString() + " and " + date2.toLocaleDateString();
										}
										else
										{
											var album = (rel.substr(11,rel.length-11)).replace(/\_/g, ' ');
											return album + " taken on " + date.toLocaleDateString();
										}
										break;
									}
									default:
									{
										return rel;
										break;// Not sure what it is...maybe the year folder...leave alone..
									}
								}//end switch
							})
					));
				});
				
				$(newList).append(orderedList);
				$('#list').append(newList);
				$("pre").hide();
			}
		});
		</script>
		<link rel="stylesheet" type="text/css" href="/scrollable-horizontal.css">
		<link rel="stylesheet" type="text/css" href="/scrollable-buttons.css">
		<style type="text/css">
		#wrapper {
			/* centered */
align:center;margin:0 auto;		}
		/* styling for the image wrapper  */
		#image_wrap {
			/* dimensions */
			width:545px;
			margin:0px 0px 0px 0px;
			padding:5px 0;

			/* centered */
			text-align:center;

			/* some "skinning" */
			background-color:#efefef;
			border:2px solid #fff;
			outline:1px solid #ddd;
			-moz-ouline-radius:4px;
		}
		/*#####################*/
		/* ADDED FOR C-INSTALL */
		/*#####################*/

		div.cinstalltitle {	
			/*width: 804px;
			width: 84px;*/
			height: 75px;
			border: 2px solid #999;
		}
		
		div.cinstallgradient {
			width: 120px;
			height: 660px;
			position:relative;
			z-index:1;
		}

		/* this makes it possible to add next button beside scrollable */
		.cinstallscrollable {
			float:left;	
		}
		/* prev, next, prevPage and nextPage buttons */
		a.cinstallbrowse {
			background:url(/vert_large.png) no-repeat;
			display:block;
			width:30px;
			height:30px;
			float:left;
			margin:30px 9px;
			cursor:pointer;
			font-size:1px;
		}
		
		/* up and down */
		a.cinstallup, a.cinstalldown  {
			background:url(/vert_large.png) no-repeat;
			float: none;
			margin: 0px 46px;
		}

		/* up */
		a.cinstallup:hover { background-position:-30px 0; }
		a.cinstallup:active { background-position:-60px 0; }

		/* down */
		a.cinstalldown { background-position: 0 -30px; }
		a.cinstalldown:hover { background-position:-30px -30px; }
		a.cinstalldown:active { background-position:-60px -30px; }
		
		/* right */
		a.cinstallright 		{ background-position: 0 -30px; clear:right; margin-right: 0px;}
		a.cinstallright:hover 	{ background-position:-30px -30px; }
		a.cinstallright:active 	{ background-position:-60px -30px; } 

		/* left */
		a.cinstallleft			{ margin-left: 0px; } 
		a.cinstallleft:hover  	{ background-position:-30px 0; }
		a.cinstallleft:active  	{ background-position:-60px 0; }

		/* disabled navigational button */
		a.cinstalldisabled {
			visibility:hidden !important;		
		}
		/*
			root element for the scrollable.
			when scrolling occurs this element stays still.
		*/
		.cinstallscrollable {

			/* required settings */
			position:relative;
			overflow:hidden;
			height: 597px;
			width:	120px;

			/* custom decorations */
			border:1px solid #ccc;
			/*background:url(https://static.flowplayer.org/tools/img/global/gradient/h300.png) repeat-x;*/
		}

		/*
			root element for scrollable items. Must be absolutely positioned
			and it should have a extremely large width to accomodate scrollable items.
			it's enough that you set the width and height for the root element and
			not for this element.
		*/
		.cinstallscrollable .cinstallitems {
			/* this cannot be too large */
			height:20000em;
			position:absolute;
			clear:both;
		}

		.cinstallitems div {
			float:left;
			width:122px;
		}

		/* single scrollable item */
		.cinstallscrollable img {
			/*float:left;*/
			margin:2px 2px 2px 2px;
			background-color:#fff;
			padding:2px;
			border:1px solid #ccc;
			width:110px;
			height:75px;
			
			-moz-border-radius:4px;
			-webkit-border-radius:4px;
		}

		/* active item */
		.cinstallscrollable .active {
			border:2px solid #000;
			padding:1px;
			position:relative;
			cursor:default;
		}

		/* active image */
		.cinstallimage_wrap{
			max-height:670px;
			//min-width:820px;
			vertical-align:top;
			overflow:auto;
		}
		
		#EXIFDataCell{
			min-width:333px;
			padding:2px;
		}
		#EXIFDataCell table th {
		  text-align: right;
		  font-weight: bold;
		}
		/*###########################*/
		/* END - ADDED FOR C-INSTALL */
		/*###########################*/
		</style>
	</head>
	<body>

	<?php
	echo "<h1>".$str."</h1>";
	?>
	<div id="list"></div>
