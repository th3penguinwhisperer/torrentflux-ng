/***************************/
//@Author: Adrian "yEnS" Mato Gondelle
//@website: www.yensdesign.com
//@email: yensamg@gmail.com
//@license: Feel free to use it, but keep this credits please!					
/***************************/

//SETTING UP OUR POPUP
//0 means disabled; 1 means enabled;
var popupStatus = 0;

function initPopup(){
	popfg = "popup_foreground";
	popbg = "popup_background";
	popld = "popup_loading";

	if($("#"+popbg).length==0) {
		$("body").append('<div id="'+popbg+'"></div>');
	}
	if($("#"+popfg).length==0) {
		$("body").append('<div id="'+popfg+'"></div>');
		$("#"+popfg).html('<img src="images/loading.gif"/>');
		$("#"+popbg).click(function(){
			disablePopup();
		});
	}
}

//loading popup with jQuery magic!
function loadPopup(){
	popfg = "popup_foreground";
	popbg = "popup_background";
	popld = "popup_loading";

	var fgdiv = document.getElementById(popfg);
        fgdiv.style.left = "100px";
        fgdiv.style.top = "100px";

        // IE doesn't support addEventListener, so check for its presence
        if (fgdiv.addEventListener) {
            // firefox, etc.
            fgdiv.addEventListener("mousemove", function(e) { return mouseMove(e) }, true);
            fgdiv.addEventListener("mousedown", function(e) { return mouseDown(e) }, true);
            fgdiv.addEventListener("mouseup", function(e) { return mouseUp(e) }, true);
        }
        else {
            // IE
            fgdiv.attachEvent("onmousemove", function(e) { return mouseMove(e) });
            fgdiv.attachEvent("onmousedown", function(e) { return mouseDown(e) });
            fgdiv.attachEvent("onmouseup", function(e) { return mouseUp(e) });
        }
	
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#" + popbg).css({
			"opacity": "0.7"
		});
		$("#" + popbg).fadeIn("slow");
		$("#" + popfg).fadeIn("slow");
		popupStatus = 1;
	}
}

//disabling popup with jQuery magic!
function disablePopup(){
	popfg = "popup_foreground";
	popbg = "popup_background";
	popld = "popup_loading";
	
	//disables popup only if it is enabled
	if(popupStatus==1){
		$("#" + popbg).fadeOut("slow");
		$("#" + popfg).fadeOut("slow");
		popupStatus = 0;
	}
}

//centering popup
function centerPopup(){
	popfg = "popup_foreground";
	popbg = "popup_background";
	popld = "popup_loading";
	
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
