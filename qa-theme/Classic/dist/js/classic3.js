$(function() {

    $('#side-menu').metisMenu();
    $('#side-menu2').metisMenu();

});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 990) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        
    });
    $(window).scroll(function (event) {
        var scroll = $(window).scrollTop();
        if (scroll<=25){
           $("#logo3").css("height",(85-scroll)+"px");
        }else{
           $("#logo3").css("height","60px");
        }
    });

    
});
