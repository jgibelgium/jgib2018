jQuery(document).ready(function ($) {
    
     /*red colors*/
     /*red color of the menu item who we are, its submenu is styled by css*/ 
     /*
     $('.menu-item-87 > a, .menu-item-87 > ul').mouseenter(function() {
        $('.menu-item-87 > a').css('background-color','#E07126');
        $('.menu-item-87 > a > span').css('background-color','#E07126');
     });
     
     $('.menu-item-87 > a, .menu-item-87 > ul').mouseleave(function() {
         $('.menu-item-87 > a').css('background-color','#FFF');
         $('.menu-item-87 > a > span').css('background-color','#FFF');
     }); 
     */
    
     /*red color of the menu item wie we zijn, its submenu is styled by css*/ 
     /*
     $('.menu-item-225 > a, .menu-item-225 > ul').mouseenter(function() {
        $('.menu-item-225 > a').css('background-color','#E07126');
        $('.menu-item-225 > a > span').css('background-color','#E07126');
     });
     
     $('.menu-item-225 > a, .menu-item-225 > ul').mouseleave(function() {
         $('.menu-item-225 > a').css('background-color','#FFF');
         $('.menu-item-225 > a > span').css('background-color','#FFF');
     }); 
     */
    
     /*red color of the menu item qui sommes nous, its submenu is styled by css*/ 
     /*
     $('.menu-item-256 > a, .menu-item-256 > ul').mouseenter(function() {
        $('.menu-item-256 > a').css('background-color','#E07126');
        $('.menu-item-256 > a > span').css('background-color','#E07126');
     });
     
     $('.menu-item-256 > a, .menu-item-256 > ul').mouseleave(function() {
         $('.menu-item-256 > a').css('background-color','#FFF');
         $('.menu-item-256 > a > span').css('background-color','#FFF');
     }); 
     */
     
     /*nog generieker*/
    
     $('#main-navigation > div > ul > li:nth-child(2) > a, #main-navigation > div > ul > li:nth-child(2) > ul').mouseenter(function() {
        $('#main-navigation > div > ul > li:nth-child(2) > a').css('background-color','#E07126');
        $('#main-navigation > div > ul > li:nth-child(2) > a > span').css('background-color','#E07126');
     });
     
     $('#main-navigation > div > ul > li:nth-child(2) > a, #main-navigation > div > ul > li:nth-child(2) > ul').mouseleave(function() {
         $('#main-navigation > div > ul > li:nth-child(2) > a').css('background-color','#FFF');
         $('#main-navigation > div > ul > li:nth-child(2) > a > span').css('background-color','#FFF');
     }); 
     
     
     /*yellow colors*/
     $('#main-navigation > div > ul > li:nth-child(3) > a, #main-navigation > div > ul > li:nth-child(3) > ul').mouseenter(function() {
        $('#main-navigation > div > ul > li:nth-child(3) > a').css('background-color','#FFD147');
        $('#main-navigation > div > ul > li:nth-child(3) > a > span').css('background-color','#FFD147');
     });
     
     $('#main-navigation > div > ul > li:nth-child(3) > a, #main-navigation > div > ul > li:nth-child(3) > ul').mouseleave(function() {
         $('#main-navigation > div > ul > li:nth-child(3) > a').css('background-color','#FFF');
         $('#main-navigation > div > ul > li:nth-child(3) > a > span').css('background-color','#FFF');
     }); 
    
    
      /*gray colors*/
      $('#main-navigation > div > ul > li:nth-child(4) > a, #main-navigation > div > ul > li:nth-child(4) > ul').mouseenter(function() {
        $('#main-navigation > div > ul > li:nth-child(4) > a').css('background-color','#97D0DB');
        $('#main-navigation > div > ul > li:nth-child(4) > a > span').css('background-color','#97D0DB');
      });
     
      $('#main-navigation > div > ul > li:nth-child(4) > a, #main-navigation > div > ul > li:nth-child(4) > ul').mouseleave(function() {
         $('#main-navigation > div > ul > li:nth-child(4) > a').css('background-color','#FFF');
         $('#main-navigation > div > ul > li:nth-child(4) > a > span').css('background-color','#FFF');
      }); 
    
      /*simulate a hover on touch enabled browsers*/
       $('.hover').bind('touchstart touchend', function(e) {
        e.preventDefault();
        $(this).toggleClass('hover_effect');
       });
       
       /*make a video fit to its parent container*/
       $(".spb-asset-content").fitVids({ customSelector: "iframe[src*='wordpress.tv'], iframe[src*='www.dailymotion.com'], iframe[src*='blip.tv'], iframe[src*='www.viddler.com']"});
       $(".wc-tab").fitVids({ customSelector: "iframe[src*='www.youtube.com']"});
       
});     