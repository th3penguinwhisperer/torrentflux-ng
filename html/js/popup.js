/**
old school popup window using plain javascript
*/
function popup_window(szUrl) {
	popupwin = window.open(szUrl,'Window106','width=750, height=600, left=200, top=200, location=no, menubar=no, status=no, toolbar=no, scrollbars=no, resizable=no');
	popupwin.focus();
	return false;
}

/**
new style popup window
*/
function popup_jquery(szUrl) {
	pp = new Popup();
	pp.url(szUrl);
}

/**
generic popup function
*/
function popup(szUrl) {
		popup_jquery(szUrl);
}

/** 2010-12-16
*/
jQuery.fn.center = function () {
	var padding = 10;
	var top =  ($(window).height() - this.height()) / 2+$(window).scrollTop();
	var left = ($(window).width() - this.width()) / 2+$(window).scrollLeft();
	this.css("position","absolute");
	this.css("top",  Math.max(padding,top) + "px");
	this.css("left", Math.max(padding,left) + "px");
	this.css("max-height", $(window).height()-2*padding + "px");
	return this;
}

/** 2010-12-16
*/
jQuery.fn.maximize = function () {
	this.css("position","absolute");
	this.css("top","0px");
	this.css("left","0px");
	this.css("top", $(window).height());
	this.css("left", $(window).width());
	return this;
}



/** 2010-12-29
@brief center foreground div
and adjust background div to cover whole browser window

CSS alternative
display:block
position:relative;
width:400px;
margin-left:auto;
margin-right:auto;
*/
function reposition() {
	//alert('reposition');
	popfg = "popup_foreground";
	popbg = "popup_background";
	popld = "popup_loading";
	domfg = $('#'+popfg);
	dombg = $('#'+popbg);
	domld = $('#'+popld);

	//get window and dom object width/hight  
	var windowWidth = $(window).width();
	var windowHeight = $(window).height();
	var documentWidth = $(document).width();
	var documentHeight = $(document).height();
	var bgWidth = Math.max(windowWidth,documentWidth);
	var bgHeight = Math.max(windowHeight,documentHeight);
	var popupHeight = domfg.height();
	var popupWidth = domfg.width();
	var uPadding = 15; //minimum distance between foreground div and window border
	
	//background occupies whole window
	dombg.css({
		"top": "0px",
		"left": "0px",
		"height": bgHeight,  
		"width": bgWidth  
	});
	
	//center foreground with padding at borders 
	var padding = 10;
	var top  = ($(window).height()-domfg.height()) / 2+$(window).scrollTop();
	var left = ($(window).width()-domfg.width()) / 2+$(window).scrollLeft();
	domfg.css("position","absolute");
	domfg.css("top",  Math.max(padding,top) + "px");
	domfg.css("left", Math.max(padding,left) + "px");
	domfg.css("max-height", $(window).height()-2*padding + "px");
	
	//center loading div
	var top  = ($(window).height()-domfg.height()) / 2+$(window).scrollTop();
	var left = ($(window).width()-domfg.width()) / 2+$(window).scrollLeft();
	domld.css("position","absolute");
	domld.css("top",  Math.max(padding,top) + "px");
	domld.css("left", Math.max(padding,left) + "px");

	//reposition popup divs when window is resized
	$(window).unbind('resize', reposition);
	$(window).resize(reposition);

	return 0;
}



/**
@brief new style popup window using a javasript object
*/
function Popup() {
	this.popfg = "popup_foreground";
	this.popbg = "popup_background";
	this.popld = "popup_loading";

	/** 2010-12-15
	@brief example function
	*/
	this.example = function(szText) {
		alert(szText + this.popfg + this.popg);
	}

	/** 2010-12-15
	*/
	this.close = function() {
		popfg = "popup_foreground";
		popbg = "popup_background";
		popld = "popup_loading";
	
		//hide popup divs
		$("#"+popfg).fadeOut('fast');
		$("#"+popbg).fadeOut('fast', function(){
			//remove popup divs
			$("#"+popfg).hide();
			$("#"+popfg).remove();
			$("#"+popbg).remove();
			$("#"+popld).remove();
		});
		
	}
	
	/** 2010-12-15
	@param szTxt string constant of text (or HTML code) to display in pop-up window
	*/
	this.txt = function(szTxt) {
		popfg = "popup_foreground";
		popbg = "popup_background";
		popld = "popup_loading";
	
		//create div's if needed
		var windowWidth = $(window).width();
		var windowHeight = $(window).height();
		if($("#"+popbg).length==0) {
			$("body").append('<div id="'+popbg+'"></div>');
			$("#"+popbg).attr('style', 'background-color:black; opacity:0.7; position:absolute; top:0px; left:0px; height:'+windowHeight+'px; width:'+windowWidth+'px;');
			$("#"+popbg).dblclick(this.close);
		}
		else {
			//alert(popbg + " already exists " + $("#"+popbg).length);	
		}
		if($("#"+popfg).length==0) {
			$("body").append('<div id="'+popfg+'">...</div>');
			$("#"+popfg).attr('style','background-color:white; border:1px solid #000000; display:block; overflow:auto;'); 
			$("#"+popfg).hide();
		}
		else {
			//alert(popfg + " already exists " + $("#"+popfg).length);	
		}
		
		// center foreground div after setting content
		document.getElementById(popfg).innerHTML = szTxt;
		reposition();
		
		// fade in
		$("#"+popbg).fadeIn(200);
		$("#"+popld).fadeIn(250);
	}
	
	/** 2010-12-15
	@param szUrl string constant of URL to open in popup window
	*/
	this.url = function(szUrl, data) {
		popfg = "popup_foreground";
		popbg = "popup_background";
		popld = "popup_loading";	
		//create back- and foreground div's if needed
		var windowWidth = $(window).width();
		var windowHeight = $(window).height();
		var documentWidth = $(document).width();
		var documentHeight = $(document).height();
		var bgWidth = Math.max(windowWidth,documentWidth);
		var bgHeight = Math.max(windowHeight,documentHeight);
		if($("#"+popbg).length==0) {
			$("body").append('<div id="'+popbg+'"></div>');
			$("#"+popbg).attr('style', 'display:none; background-color:black; opacity:0.7;'+
			' position:absolute; top:0px; left:0px; height:'+bgHeight+'px; width:'+bgWidth+'px;');
		}
		else {
			//alert(popbg + " already exists " + $("#"+popbg).length);	
		}
		if($("#"+popfg).length==0) {
			$("body").append('<div id="'+popfg+'"></div>');
			//for debugging
			$("#"+popfg).attr('style','display:none; background-color:white; border:1px solid #000000; overflow:auto; min-width: 100px; min-height: 50px; max-width:95%; max-height:95%;');
			//non-debugging 
			//$("#"+popfg).attr('style','display:none; background-color:white; border:1px solid #000000; overflow:auto;');
			$("#"+popfg).html('<img src="images/loading.gif"/>');
			$("#"+popbg).dblclick(this.close);
		}
		else {
			//alert(popfg + " already exists " + $("#"+popfg).length);
			//$("#"+popfg).text('already exists');
			$("#"+popfg).hide();
		}
		if($("#"+popld).length==0) {
			$("body").append('<div id="'+popld+'"><img src="images/loading.gif"/></div>');
			$("#"+popld).attr('style','background-color:white; border:1px solid #000000; display:block; overflow:auto;'); 
			$("#"+popld).hide();
		}
		else {
			//alert(popfg + " already exists " + $("#"+popfg).length);	
		}
	
		// fade in
		reposition();
		$("#"+popbg).fadeIn('fast');
		$("#"+popld).fadeIn('fast');
		$("#"+popfg).load(szUrl, data, this.complete);
	}
	
	/** 2010-12-16
	@brief call this function when url is loaded in the foreground div
	*/
	this.complete = function(obj) {
		//alert('complete');
		reposition();
		//alert('complete');
		popfg = "popup_foreground";
		popbg = "popup_background";
		popld = "popup_loading";
		$("#"+popfg).fadeIn(200);
		$("#"+popld).fadeOut(250);
	}
	
	/** 2010-12-16
	@param szUrl string constant of URL to open in popup window
	*/
	this.post = function(event) {
		if(event) event.preventDefault();

		szUrl = $(event.target).attr('action');
		data = $(event.target).serializeArray();
		this.url(szUrl, data);
	}
	
}
