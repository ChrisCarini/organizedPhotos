<div id="containerFoot">
<em>
<div id="copyright" align="center">&copy; <?php echo date("Y"); ?> ChrisCarini.com | All Rights Reserved</div>
</em>
</div>
<script>
function makeScrollable()
{
	$(".cinstallscrollable").scrollable({ vertical: true/*, mousewheel: true */});
	
	$(".cinstallitems img").click(function()
	{
		// see if same thumb is being clicked
		if ($(this).hasClass("active")) { return; }
		// calclulate large image's URL based on the thumbnail URL (flickr specific)
		//var url = $(this).attr("src").replace("_t", "");
		// calclulate large image's URL based on the thumbnail URL (both are same)
		var url = $(this).attr("src");
		var id = $(this).attr("id");
		
		// Convert:
		//		../2012/12-December/2012-12-23-Thrasher_Light_Orbs_in_Park/DSC_0019_400px.gif
		// to...
		// 		../2012/12-December/2012-12-23-Thrasher_Light_Orbs_in_Park/DSC_0019.jpg
		document.getElementById("EXIFDataCell").innerHTML = "Looking up EXIF Data...";
		if(url.slice(-10) == "_800px.gif")
		{
			showPhotoEXIFData(document.URL+url.replace("_800px.gif",".JPG"));
		}
		else
		{
			showPhotoEXIFData(document.URL+url);
		}
		
		// get handle to element that wraps the image and make it semi-transparent
		var wrap = $( ("#image_wrap"+$(this).attr("id"))).fadeTo("medium", 1.0);
		// the large image from www.flickr.com
		var img = new Image();
	
		// call this function after it's loaded
		img.onload = function() {
			// make wrapper fully visible
			wrap.fadeTo("fast", 1);
			// change the image
			wrap.find("img").attr("src", url);
		};
		// begin loading the image from www.flickr.com
		img.src = url;
		// activate item
		$(".cinstallitems img").each(function(i){
			if( $(this).attr('id') === id)
			{
				$(this).removeClass("active");
			}				
		});
		$(this).addClass("active");
	// when page loads simulate a "click" on the first image
	}).filter(":first").click();
	/* For each scroller on the page, set the first image to be shown first */
	$('.cinstallscrollable').each(function (i) {  // loop through scrollers
		var thisCarousel = $('.cinstallscrollable:eq(' + i + ')');  // var this scroller
		$(thisCarousel).scrollable().begin(0.1);
	});
}
$(document).ready(function () {
    doSomething();
});

var resizeTimer;
$(window).resize(function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(doSomething, 100);
	
});

//$(window).resize(function(){
//$(document).ready(function(){
function doSomething()
{
	// remove the slider ability
	$(".cinstallscrollable").removeData();
	// resize slider stuff
	var windowHeight = $(window).height();
	var mySize = Math.floor( (windowHeight-209)/85 );
	$(".cinstallgradient").css({"height":((mySize*85)+64)+'px'});
	$(".cinstallscrollable").css({"height":(mySize*85)+'px'});
	// resize the slider array sizes / make divs in correct size
	var myArray = $.makeArray($('img'));
	$('img').each(function (index, domEle) {
		if( !$(this).is("#2") )
		{
			myArray.splice(index , 1);
		}
	});
	var mainSliderDiv = $('.cinstallitems');
	mainSliderDiv.empty();
	while(myArray.length>0)
	{
		chunk = $(myArray.splice(0, mySize ));
		//console.log(mySize);
		//console.log(chunk);
		var curDiv = $('<div/>');
		$(chunk).each(function() {
			var img = $('<img id="2" src="'+($(this)[0].src).substring(($(this)[0].src).lastIndexOf('/') + 1)+'"/>');
			curDiv.append($(img));
		});
		//kconsole.log(curDiv);
		mainSliderDiv.append(curDiv);
	}
	// resize the image appropriately
	var img = $(mainIMG); // Get my img elem
	var pic_real_width, pic_real_height;
	$("<img/>") // Make in memory copy of image to avoid css issues
		.attr("src", $(img[0]).attr("src"))
		.load(function() {
			pic_real_width = this.width;   // Note: $(this).width() will not
			pic_real_height = this.height; // work for in memory images.
			var size = 170;
			if(pic_real_height > (windowHeight-size))
			{
				// remove currently set dimensions
				img.removeAttr("width"); 
				img.removeAttr("height");
				img.attr("height",(windowHeight-size)+'px');// change img height to (windowHeight-size)
				img.attr("width",(((windowHeight-size)/this.height)*this.width)+'px');// change img width to percentage of original
				$('.cinstallimage_wrap').attr("min-width",(((windowHeight-size)/this.height)*this.width)+'px');
			}
			else
			{
				img.removeAttr("width"); 
				img.removeAttr("height");
				img.attr("height",this.height+'px');
				img.attr("width",this.width+'px');
			}
		});
	// make the thing scrollable again..
	makeScrollable();
}//);

$(document).ready(function(){
	var lastID = 0;
	//set the first element of each gallery to active
	$(".items img").each(function(i)
	{
		if( lastID != $(this).attr('id') )
		{
			lastID = $(this).attr('id');
			$(this).addClass("active");
		}
		else
		{
			$(this).removeClass("active");
		}
	});
});
</script>
</body></html>