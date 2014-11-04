jQuery(function() {
    $ = jQuery;
    if($('.cDivHeaderSlider > div').length>1){
        $('.cDivHeaderSlider').bxSlider({
            mode:'fade',
            auto: true,
            pause: 8000,
            adaptiveHeight: true
        });
        
    }
    $('a[href="start-film"]').click(function(){
           $('#movieModal').modal();
            return false;
        });
    $('.cDivFaqTitle').click(function(e){
        $('.cDivFaqItem').removeClass('open');
        $(this).parent('.cDivFaqItem').toggleClass('open');
    });
    $('.modal').on('hidden.bs.modal', function (e) {
                    
            $("video").each(function () { this.pause() });
            
        //api.stop();
      });
});


