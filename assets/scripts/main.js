/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can
 * always reference jQuery with $, even when in .noConflict() mode.
 * ======================================================================== */
(function($) {

  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var Sage = {
    // All pages
    'common': {
      init: function() {

        /*
         *_Debounced resize
         */
        function debounce(func, wait, immediate) {
          var timeout;
          return function() {
            var context = this,
              args = arguments;
            var later = function() {
              timeout = null;
              if (!immediate) {
                func.apply(context, args);
              }
            };
            if (immediate && !timeout) {
              func.apply(context, args);
            }
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
          };
        }


        /*
         *_custom inputs
         */
        function custom_inputs(parent) {
          // Material choices
          $(parent).find('.checkbox input[type=checkbox]').after("<span class=checkbox-material><span class=check></span></span>");
          $(parent).find('.radio input[type=radio]').after("<span class=radio-material><span class=circle></span><span class=check></span></span>");
          $(parent).find('.togglebutton input[type=checkbox]').after("<span class=toggle></span>");

          // Gravity Forms render tweak
          $(parent).find('.gfield_checkbox > li').addClass('checkbox');
          $(parent).find('.gfield_radio > li').addClass('radio');
          $(parent).find('select.gfield_select').addClass('form-control');
        }


        /*
         * Smooth scroll
         */
        function smoothScrollTo(target) {
          target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
          if (target.length) {
            $('html, body').animate({
              scrollTop: target.offset().top - $('.navbar-sticky').outerHeight(true)
            }, 1000);
          }
        }


        /*
         * Dropdown align plugin
         */
        jQuery.fn.dropdownAlign = function(options){

          options = $.extend({
            itemClass: 'dropdown-rtl',
            dropdownClass: 'dropdown-menu'
          }, options);

          var make = function(){
            var $dropdownEL = $(this).find('>.' + options.dropdownClass),
                $navbarEl = $(this).parent();

            if($dropdownEL.length && $navbarEl.length){

              $navbarEl.css('oveflow', 'hidden');

              if(($(this).offset().left - $navbarEl.offset().left + $dropdownEL.width()) < $navbarEl.width()){
                $(this).removeClass(options.itemClass);
              } else{
                $(this).addClass(options.itemClass);
              }

              $navbarEl.css('oveflow', 'visible');

            }

          };

          $(this).on('show.bs.dropdown', function () {
            $(this).each(make);
          });

          return this.each(make);
        };


        // wait untila page loads
        var onloadCallback = function() {

          setTimeout(function(){
            // needed by preloaded
            $('body').addClass('loaded');

            // when the window resizes, redraw the grid
            $(window).trigger('resize scroll');

            if (location.hash) {
              var target = location.hash.split('#');
              smoothScrollTo($('#'+target[1]));
            }

          }, 1);

        };


        // wait until users finishes resizing the browser
        var debouncedResize = debounce(function() {

          var $navbarSticky = $('.navbar-sticky');

          // navbar height helper
          $navbarSticky.data('height', $navbarSticky.outerHeight());

          //respect wpadminbar
          $navbarSticky.css('top', $('#wpadminbar').outerHeight());


          // dropdown align plugin
          $('.navbar-nav .nav-item-has-children').dropdownAlign();


          // carousel min-height
          $('.carousel-inner').each(function() {
              var $items = $(this).children(),
                  $coll = [];

              $items.css('height', 'auto').each(function(i){
                $coll[i] = $(this).height();
              });

              $items.css('height', Math.max.apply(Math, $coll));
          });

          // reset article list height
          $('.article-list').height('auto');

        }, 100);


        // wait until users finishes scrolling the browser
        var debouncedScroll = debounce(function() {

          var $navbarSticky = $('.navbar-sticky');

          // navbar height helper
          $navbarSticky.data('height', $navbarSticky.outerHeight());

          if ($(window).scrollTop() > $navbarSticky.data('height')) {
            $navbarSticky.addClass('narrow');
          } else {
            $navbarSticky.removeClass('narrow');
          }

        }, 10);



        //window handlers
        $(window)
          .load(onloadCallback)
          .scroll(debouncedScroll)
          .resize(debouncedResize);



        /*
         * Prevent page jump to hash
         */
        if (location.hash) {
          window.scrollTo(0, 0);
        }


        // Disable 300ms click delay on mobile
        FastClick.attach(document.body);


        // Responsive video
        $('.main').fitVids();


        //Object fit images polyfill
        objectFitImages();


        // position sticky polifill
        $('.sticky').Stickyfill();


        // init form custom inputs
        custom_inputs($('.form'));


        //ripples
        $([ ".navbar-toggler", ".nav-link", ".btn" ].join(",")).ripples();


        // apply material inputs on ajax forms
        $(document).bind('gform_post_render', function(event, form_id, cur_page) {
          var form = $('#gform_' + form_id);
          custom_inputs(form);
        });


        // smooth scrolling
        $('a[href*="#"]:not([href="#"])').click(function() {
          if (location.pathname.replace(/^\//,'') === this.pathname.replace(/^\//,'') && location.hostname === this.hostname) {
            smoothScrollTo($(this.hash));
            return false;
          }
        });



      },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired
      }
    },
    // Home page
    'home': {
      init: function() {
        // JavaScript to be fired on the home page
      },
      finalize: function() {
        // JavaScript to be fired on the home page, after the init JS
      }
    },
  };

  // The routing fires all common scripts, followed by the page specific scripts.
  // Add additional events for more control over timing e.g. a finalize event
  var UTIL = {
    fire: function(func, funcname, args) {
      var fire;
      var namespace = Sage;
      funcname = (funcname === undefined) ? 'init' : funcname;
      fire = func !== '';
      fire = fire && namespace[func];
      fire = fire && typeof namespace[func][funcname] === 'function';

      if (fire) {
        namespace[func][funcname](args);
      }
    },
    loadEvents: function() {
      // Fire common init JS
      UTIL.fire('common');

      // Fire page-specific init JS, and then finalize JS
      $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function(i, classnm) {
        UTIL.fire(classnm);
        UTIL.fire(classnm, 'finalize');
      });

      // Fire common finalize JS
      UTIL.fire('common', 'finalize');
    }
  };

  // Load Events
  $(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.
