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

        // Debounced resize
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

        custom_inputs($('.form'));

        // apply material inputs on ajax forms
        $(document).bind('gform_post_render', function(event, form_id, cur_page) {
          var form = $('#gform_' + form_id);
          custom_inputs(form);
        });


        // Dropdown align plugin
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

              if(($(this).offset().left + $dropdownEL.width()) < $navbarEl.width()){
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


        // wait until users finishes resizing the browser
        var debouncedResize = debounce(function() {

          // fixed navbar offsets

          $('.navbar-fixed-top').each(function() {
            var $self = $(this),
                adminbar = $('#wpadminbar');

            if (adminbar.length) {
              $self.css('margin-top', adminbar.height());
            }

            $('.wrap').css('margin-top', $self.height());
          });

          $('.navbar-fixed-bottom').each(function() {
            var $self = $(this);
            $('.content-info').css('padding-bottom', $self.height());
          });

          // fire dropdown align plugin
          $('.navbar-nav .nav-item-has-children').dropdownAlign();

        }, 100);


        //window load callback
        $(window).load(function() {
          // needed by preloaded
          $('body').addClass('loaded');

          // when the window resizes, redraw the grid
          $(window).resize(debouncedResize).trigger('resize');

        });

        // Disable 300ms click delay on mobile
        FastClick.attach(document.body);

        // Responsive video
        $('.main').fitVids();

        // Video lightbox
        $('.video-lightbox').magnificPopup({
          type: 'iframe'
        });

        // Image gallery lightbox
        $('.gallery-wrapper .gallery').each(function() {
          var $thumb = $(this).find('a.gallery-thumb');

          $thumb.each(function() {

            $(this).magnificPopup({
              type: 'image',
              enableEscapeKey: true,
              gallery: {
                enabled: false,
                tPrev: '',
                tNext: '',
                tCounter: '%curr% | %total%'
              },
              image: {
                verticalFit: true,
                markup: '<div class="mfp-figure gallery-lightbox">' +
                          '<div class="mfp-close"></div>' +
                          '<a class="mfp-pin-it"' +
                          'href="http://pinterest.com/pin/create/bookmarklet/' +
                          '?media=' + window.location.protocol +
                          '//' + window.location.host + $(this).data('media') +
                          '&url=' + $(this).data('url') +
                          '&is_video=false' +
                          '&description=' + $(this).data('description') +
                          '"><span class="fa fa-pinterest"></span></a>' +
                          '<div class="mfp-img"></div>' +
                          '<div class="mfp-bottom-bar">' +
                            '<div class="mfp-title"></div>' +
                            '<div class="mfp-counter"></div>' +
                          '</div>' +
                        '</div>',
                titleSrc: function(item) {
                  return item.el[0].nextSibling.innerHTML;
                }
              },
              mainClass: 'mfp-fade'
            });
          });
        });

        /**
         * ripples
         */
        $([
          ".navbar-toggle",
          ".btn:not(.btn-link)",
          ".card-image",
          ".navbar a:not(.withoutripple)",
          ".dropdown-menu a:not(.withoutripple)",
          ".withripple"
        ].join(",")).ripples();

        // Handle hash anchors
        $('.scroll-link').on('click', function(e) {
          e.preventDefault();
          var target = $($(this).attr('href'));

          if (target.length) {
            var offset = Math.round(target.offset().top - $('.navbar-fixed-top').outerHeight() - $('#wpadminbar').outerHeight());
            $('html,body').animate({
              scrollTop: offset
            }, 1000, 'easeInOutCubic');
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
