jQuery(function() {
    $ = jQuery;
    if($('.banner-logo').length){
        var html = '<div id="sidebar-header">';
        html += '<table class="cnss-social-icon" style="width:240px" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="width:60px"><a target="_blank" title="twitter" href="https://twitter.com/MasjaSlootweg"><img src="http://socialmet.kominski.net/wp-content/uploads//1416871002_icon_twitter.png" border="0" width="40" height="40" alt="twitter" style="opacity: 1;"></a></td><td style="width:60px"><a target="_blank" title="google+" href="http://plus.google.com"><img src="http://socialmet.kominski.net/wp-content/uploads//1416871051_icon_google.png" border="0" width="40" height="40" alt="google+"></a></td><td style="width:60px"><a target="_blank" title="linkedin" href="http://linkedin.com"><img src="http://socialmet.kominski.net/wp-content/uploads//1416871017_icon_linkedin.png" border="0" width="40" height="40" alt="linkedin"></a></td><td style="width:60px"><a target="_blank" title="facebook" href="https://www.facebook.com/socialmet.nl"><img src="http://socialmet.kominski.net/wp-content/uploads//1416871033_icon_facebook.png" border="0" width="40" height="40" alt="facebook"></a></td></tr></tbody></table>';
        html+='</div><br style="clear:both;" />';
        $('.banner-logo').append(html);
        var html='<div id="footer-row" class="site-footer"><div class="col-md-3 footer-left"><div id="text-5" class="widget widget_text"><h3 class="widget-title">Algemeen</h3><div class="textwidget"><a href="#">Over ons</a><br><a href="#">Disclaimer</a><br><a href="#">Algemene voorwaarden</a><br></div></div></div><div class="col-md-3 footer-middle"><div id="text-6" class="widget widget_text"><h3 class="widget-title">Trainingen</h3><div class="textwidget"><ul class="sm_training_list"><li class="linkedin">Vrijdag 28 November</li><li class="facebook">Zaterdag 29 November</li></ul><br style="clear:both;"></div></div></div><div class="col-md-3 footer-right"><div id="text-7" class="widget widget_text"><h3 class="widget-title">Contact</h3><div class="textwidget">E: <a href="mailto:masja.slootweg@socialmet.nl">masja.slootweg@socialmet.nl</a><br>T: 06 14 13 44 88<br>Theemsstraat 26<br>2014 RX Haarlem<br></div></div></div></div>';
        $('.footer .row .fixed-width').append(html);
    }
    
    if($('body.blog').length){
      var queryString = 'h1.entry-title';
      $('.row').each(function(i){
          
          var highest = 0;
          $(this).find(queryString).each(function(j){
              if($(this).height()>highest)highest=$(this).height();
          });
          $(this).find(queryString).css('height',highest+'px');
      });
  }
});


