/*
	Question2Answer by Gideon Greenspan and contributors

	http://www.question2answer.org/


	File: qa-content/qa-page.js
	Version: See define()s at top of qa-include/qa-base.php
	Description: Common Javascript including voting, notices and favorites


	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: http://www.question2answer.org/license.php
*/

function qa_reveal(elem, type, callback)
{
	if (elem)
		$(elem).slideDown(400, callback);
}

function qa_conceal(elem, type, callback)
{
	if (elem)
		$(elem).slideUp(400);
}

function qa_set_inner_html(elem, type, html)
{
	if (elem)
		elem.innerHTML=html;
}

function qa_set_outer_html(elem, type, html)
{
	if (elem) {
		var e=document.createElement('div');
		e.innerHTML=html;
		elem.parentNode.replaceChild(e.firstChild, elem);
	}
}

function qa_show_waiting_after(elem, inside)
{
	if (elem && !elem.qa_waiting_shown) {
		var w=document.getElementById('qa-waiting-template');

		if (w) {
			var c=w.cloneNode(true);
			c.id=null;

			if (inside)
				elem.insertBefore(c, null);
			else
				elem.parentNode.insertBefore(c, elem.nextSibling);

			elem.qa_waiting_shown=c;
		}
	}
}

function qa_hide_waiting(elem)
{
	var c=elem.qa_waiting_shown;

	if (c) {
		c.parentNode.removeChild(c);
		elem.qa_waiting_shown=null;
	}
}

function qa_vote_click(elem)
{
	var ens=elem.name.split('_');
	var postid=ens[1];
	var vote=parseInt(ens[2]);
	var code=elem.form.elements.code.value;
	var anchor=ens[3];
	var innerHtml=$(elem).html();
	$(elem).html('<i class="fa fa-spinner fa-spin"></i>');

	qa_ajax_post('vote', {postid:postid, vote:vote, code:code},
		function(lines) {
		     $(elem).html(innerHtml);
			if (lines[0]=='1') {
				qa_set_inner_html(document.getElementById('voting_'+postid), 'voting', lines.slice(1).join("\n"));

			} else if (lines[0]=='0') {
				var mess=document.getElementById('errorbox');

				if (!mess) {
					var mess=document.createElement('div');
					mess.id='errorbox';
					mess.className='qa-error';
					mess.innerHTML=lines[1];
					mess.style.display='none';
				}

				var postelem=document.getElementById(anchor);
				var e=postelem.parentNode.insertBefore(mess, postelem);
				qa_reveal(e);

			} else
				qa_ajax_error();
		}
	);

	return false;
}

function qa_notice_click(elem)
{
	var ens=elem.name.split('_');
	var code=elem.form.elements.code.value;

	qa_ajax_post('notice', {noticeid:ens[1], code:code},
		function(lines) {
			if (lines[0]=='1')
				qa_conceal(document.getElementById('notice_'+ens[1]), 'notice');
			else if (lines[0]=='0')
				alert(lines[1]);
			else
				qa_ajax_error();
		}
	);

	return false;
}

function qa_favorite_click(elem)
{
	var ens=elem.name.split('_');
	var code=elem.form.elements.code.value;

	qa_ajax_post('favorite', {entitytype:ens[1], entityid:ens[2], favorite:parseInt(ens[3]), code:code},
		function (lines) {
			if (lines[0]=='1')
				qa_set_inner_html(document.getElementById('favoriting'), 'favoriting', lines.slice(1).join("\n"));
			else if (lines[0]=='0') {
				alert(lines[1]);
				qa_hide_waiting(elem);
			} else
				qa_ajax_error();
		}
	);

	qa_show_waiting_after(elem, false);

	return false;
}

function qa_ajax_post(operation, params, callback)
{
	jQuery.extend(params, {qa:'ajax', qa_operation:operation, qa_root:qa_root, qa_request:qa_request});

	jQuery.post(qa_root, params, function(response) {
		var header='QA_AJAX_RESPONSE';
		var headerpos=response.indexOf(header);

		if (headerpos>=0)
			callback(response.substr(headerpos+header.length).replace(/^\s+/, '').split("\n"));
		else
			callback([]);

	}, 'text').fail(function(jqXHR) { if (jqXHR.readyState>0) callback([]) });
}

function qa_ajax_error()
{
	alert('Unexpected response from server - please try again or switch off Javascript.');
}

function hybrid_progressHide()
{
	try{
		$laporHybrid.progressHide();
		$(".qa-nav-user-nolink").hide(); 
	}catch(e){}	
}

function hybrid_progressShow()
{
	try{
		$laporHybrid.progressShow();	     
	}catch(e){}	
}

function hybrid_progressShow()
{
	try{
		$laporHybrid.progressShow();	     
	}catch(e){}	
}


$(document).ready(function(){
   $('.ux-vote-buttons').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    });
    $('.ux-item-avatar-meta').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    })
    $('.ux-view-avatar-meta').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    })
    $('.ux-c-item-meta').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    })
    $('.qa-a-item-meta').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    })
    $('.ux-favoriting').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    })
    $('.ux-a-selection').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    })
    $('.qa-top-users-table').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    })
    var globalheight=20; 
    if (window.location.href.toString().split(window.location.host)[1]!=="/" && 
        (window.location.href.toString().split(window.location.host)[1]).indexOf("/question")<0){
    	$(".jumbotron").remove();
    }
    if ($(".jumbotron").length>0){
    	globalheight=420;
    	$(".jumbotron").show();
    }else{
        $("#wrapper").css("margin-top","0px");
    }
    var setScroll=function(){
        var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        var scroll = $(window).scrollTop();
        
        if (width>992){
			if (scroll<=globalheight){
				$("#leftPanel").children().css("position","static");
				if ($(".jumbotron").length>0){
					var y=40;
					var yy=35+(y*(scroll/globalheight));
					if (yy>(y+35)){
						yy=y+35;
					}
					$(".jumbotron").css("background-position","0% "+yy+"%");
				}
			}else{
			   if (scroll<=($(document).height()-680)){
				  $("#leftPanel").children().css("position","fixed");
				  $("#leftPanel").children().css("top","52px");
			   }else{
				  $("#leftPanel").children().css("position","static");
			   }
			}
			if (scroll>=1560){
			    if (scroll<=($(document).height()-830)){
					$("#facebookpage").css("position","fixed");
					$("#facebookpage").css("top","52px");
				}else{
					$("#facebookpage").css("position","static");
				}
			}else{
				$("#facebookpage").css("position","static");
			}
        }else {
           $("#leftPanel").children().css("position","static");
           $("#facebookpage").css("position","static");
        }
        
    }
   $(window).bind("load resize", function() {
        topOffset = 10;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 992) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }
        
        height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#navbar").css("max-height",(height) + "px");
        }
         
        //$(".fb-page").children().css( "width", "100%" );
        //$(".fb-page").children().children().css( "width", "100%" );
        setScroll();
    });
    $(window).scroll(function (event) {
        setScroll();
    });
    if ( ($(window).height() + 500) < $(document).height() ) {
		$('#top-link-block').removeClass('hidden').affix({
			// how far to scroll down before link "slides" into view
			offset: {top:500}
		});
	}
	$('#top-link-block').click(function(){
	    var to=20;
	    if ($(".jumbotron").length>0){
	        to=150;
	    }
		$('html,body').animate({scrollTop:to},'slow');
		$("#q").focus();
		return false;
	});
    
    
   hybrid_progressHide();	
		var redirectToNewApps = {
			waitFor: 5,
			itemNumber: null,
			timerReadyState: null,
			run:false,
			init: function () {
				if (this.run){
					return;
				}
				var userFB=0; 
				try {
					userFB=$laporHybrid.getUserFB().length;
				} catch (e) {
					userFB=0; 
				}
				var version='1';
				try {
					version=$laporHybrid.getAppsVersion();
				} catch (e) {
					version='1';
				}
				try {
					$laporHybrid.progressHide();
					if (version === "1" && userFB===0) {
						$('body').html("");
						var divPopUp = $("<div>").attr("style", "font-size:20px;background-color:white;z-index:9999999;position: absolute;top:0;left:0px;display:table-cell; vertical-align:middle;text-align:center;width:100%;height:100%;overflow:hidden;").appendTo("body");
						var div = $("<div>").attr("style","padding:20px;").html("Aplikasi <b><span style='color:#c10808;'>Lapor Presiden</span></b> sudah bisa auto login dg Facebook account.<br>Silahkan mengunduh Aplikasi terbaru <b><span style='color:#c10808;'>Lapor Presiden</span></b>.<br>Terimakasih atas kerjasamanya.<br/>").appendTo(divPopUp);
						this.itemNumber = $("<span>").attr("style", "font-size:44px;color:#c10808;").html(this.waitFor).appendTo(divPopUp);
						this.timerReadyState = setInterval(function () {
							try {
								redirectToNewApps.waitFor--;
								redirectToNewApps.itemNumber.html(redirectToNewApps.waitFor);
								if (redirectToNewApps.waitFor <= 0) {
									window.location.href = "https://play.google.com/store/apps/details?id=org.laporpresiden.android";
									clearInterval(redirectToNewApps.timerReadyState);
								}
							} catch (e) {
							}
						}, 1000);
					}
				} catch (e) {
				}
			}
		};
		redirectToNewApps.init();	

		/*$( window ).resize( function(){
			if ($('.navbar-toggle').is(':hidden')){
				$(".qa-body-wrapper").hide();
				$(".qa-nav-main").show();
				window.setTimeout('$(".qa-body-wrapper").fadeIn()', 100);
			}
		});*/
});
