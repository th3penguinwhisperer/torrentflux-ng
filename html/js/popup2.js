/***************************/
//@Author: Adrian "yEnS" Mato Gondelle
//@website: www.yensdesign.com
//@email: yensamg@gmail.com
//@license: Feel free to use it, but keep this credits please!					
/***************************/

//SETTING UP OUR POPUP
//0 means disabled; 1 means enabled;
var popupStatus = 0;

popfg = "popup_foreground";
popbg = "popup_background";
popld = "popup_loading";
poptitle = "popup_title";
popbody = "popup_body";

function initPopup(){
	if($("#"+popbg).length==0) {
		$("body").append('<div id="'+popbg+'"></div>');
	}
	if($("#"+popfg).length==0) {
		$("body").append('<div id="'+popfg+'"><div id="popup_windowtitle"><div id="'+poptitle+'">Window</div><div id=popup_close onclick="javascript:disablePopup();" >x</div></div><div id="'+popbody+'">Body</div></div>');
		//$("#"+popfg).html('<img src="images/loading.gif"/>'); // This should stay disabled as it overwrites the contents
		$("#"+popbg).click(function(){
			disablePopup();
		});
	}

	var fgdiv = document.getElementById(popfg);
        fgdiv.style.left = "100px";

	var titlediv = document.getElementById(poptitle);
        titlediv.style.left = "100px";
        titlediv.style.top = "100px";

        // IE doesn't support addEventListener, so check for its presence
        if (titlediv.addEventListener) {
            // firefox, etc.
            titlediv.addEventListener("mousemove", mouseMove, true);
            titlediv.addEventListener("mousedown", mouseDown, true);
            titlediv.addEventListener("mouseup", mouseUp, true);
        }
        else {
            // IE
            titlediv.attachEvent("onmousemove", function(e) { return mouseMove(e) });
            titlediv.attachEvent("onmousedown", function(e) { return mouseDown(e) });
            titlediv.attachEvent("onmouseup", function(e) { return mouseUp(e) });
        }
}

//loading popup with jQuery magic!
function loadPopup(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#" + popbg).css({
			"opacity": "0.7"
		});
		$("#" + popbg).fadeIn("slow");
		$("#" + popfg).fadeIn("slow");
		$("#" + poptitle).fadeIn("slow");
		$("#" + popbody).fadeIn("slow");
		popupStatus = 1;
	}
}

//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){
		$("#" + popbg).fadeOut("slow");
		$("#" + popfg).fadeOut("slow");
		$("#" + poptitle).fadeOut("slow");
		$("#" + popbody).fadeOut("slow");
		popupStatus = 0;
	}
}

//centering popup
function centerPopup(){
	//request data for centering
	var windowWidth = document.documentElement.clientWidth;
	var windowHeight = document.documentElement.clientHeight;
	var popupHeight = $("#" + popfg).height();
	var popupWidth = $("#" + popfg).width();
	
	$("#" + popfg).css({
		"max-height": windowHeight - 200
	});
	popupHeight = $("#" + popfg).height(); // Re-set this to the value that is in use after changing max-height
	
	//centering
	$("#" + popfg).css({
		"position": "absolute",
		"top": windowHeight/2-popupHeight/2,
		"left": windowWidth/2-popupWidth/2
	});
	//only need force for IE6
	
	$("#" + popbg).css({
		"height": windowHeight
	});
	
}

//CONTROLLING EVENTS IN jQuery
$(document).ready(function(){
	initPopup();
	
	//LOADING POPUP
	//Click the button event!
	$("#button").click(function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup();
	});
	
	//CLOSING POPUP
	//Click the x event!
	$("#popup_close").click(function(){
		disablePopup();
	});
	//Click out event!
	$("#backgroundPopup").click(function(){
		disablePopup();
	});
	//Press Escape event!
	$(document).keypress(function(e){
		if(e.keyCode==27 && popupStatus==1){
			disablePopup();
		}
	});

});
