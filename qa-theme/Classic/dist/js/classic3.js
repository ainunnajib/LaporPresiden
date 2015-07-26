$(function() {

    $('#side-menu').metisMenu();
    $('#side-menu2').metisMenu();

});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
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
         
        $(".fb-page").children().css( "width", "100%" );
        $(".fb-page").children().children().css( "width", "100%" );
        
    });
    $(window).scroll(function (event) {
        var scroll = $(window).scrollTop();
        if (scroll<=30){
           $("#logo3").css("height",(90-scroll)+"px");
        }else{
           $("#logo3").css("height","60px");
        }
    });

    
});
