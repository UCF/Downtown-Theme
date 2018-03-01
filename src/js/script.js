// =require ./bootstrap-sass/index.js
// =require ./fittext/jquery.fittext.js
// =require ./stellar/src/jquery.stellar.js
// =require ./chart.js/Chart.js
// =require ./@fancyapps/fancybox/dist/jquery.fancybox.js
// =require webcom-base.js
// =require objectFitPolyfill.js


const Generic = {};

Generic.removeExtraGformStyles = function ($) {
  // Since we're re-registering the Gravity Form stylesheet
  // manually and we can't dequeue the stylesheet GF adds
  // by default, we're removing the reference to the script if
  // it exists on the page (if CSS hasn't been turned off in GF settings.)
  $('link#gforms_css-css').remove();
};

Generic.PostTypeSearch = function ($) {
  $('.post-type-search')
    .each((post_type_search_index, post_type_search) => {
      var post_type_search = $(post_type_search),
        form             = post_type_search.find('.post-type-search-form'),
        field            = form.find('input[type="text"]'),
        results          = post_type_search.find('.post-type-search-results'),
        by_term          = post_type_search.find('.post-type-search-term'),
        by_alpha         = post_type_search.find('.post-type-search-alpha'),
        sorting          = post_type_search.find('.post-type-search-sorting'),
        sorting_by_term  = sorting.find('button:eq(0)'),
        sorting_by_alpha = sorting.find('button:eq(1)'),

        post_type_search_data  = null,
        search_data_set        = null,
        column_count           = null,
        column_width           = null,

        typing_timer = null,
        typing_delay = 300, // milliseconds

        prev_post_id_sum = null, // Sum of result post IDs. Used to cache results

        MINIMUM_SEARCH_MATCH_LENGTH = 2;

      // Get the post data for this search
      post_type_search_data = PostTypeSearchDataManager.searches[post_type_search_index];
      if (typeof post_type_search_data === 'undefined') { // Search data missing
        return false;
      }

      search_data_set = post_type_search_data.data;
      column_count    = post_type_search_data.column_count;
      column_width    = post_type_search_data.column_width;

      if (column_count === 0 || column_width === '') { // Invalid dimensions
        return false;
      }

      // Sorting toggle
      sorting_by_term.click(() => {
        by_alpha.fadeOut('fast', () => {
          by_term.fadeIn();
          sorting_by_alpha.removeClass('active');
          sorting_by_term.addClass('active');
        });
      });
      sorting_by_alpha.click(() => {
        by_term.fadeOut('fast', () => {
          by_alpha.fadeIn();
          sorting_by_term.removeClass('active');
          sorting_by_alpha.addClass('active');
        });
      });

      // Search form
      form
        .submit((event) => {
          // Don't allow the form to be submitted
          event.preventDefault();
          perform_search(field.val());
        });
      field
        .keyup(() => {
          // Use a timer to determine when the user is done typing
          if (typing_timer !== null) {
            clearTimeout(typing_timer);
          }
          typing_timer = setTimeout(() => {
            form.trigger('submit');
          }, typing_delay);
        });

      function display_search_message(message) {
        results.empty();
        results.append($(`<p class="post-type-search-message"><big>${message}</big></p>`));
        results.show();
      }

      function perform_search(search_term) {
        let matches             = [],
          elements            = [],
          elements_per_column = null,
          columns             = [],
          post_id_sum         = 0;

        if (search_term.length < MINIMUM_SEARCH_MATCH_LENGTH) {
          results.empty();
          results.hide();
          return;
        }
        // Find the search matches
        $.each(search_data_set, (post_id, search_data) => {
          $.each(search_data, (search_data_index, term) => {
            if (term.toLowerCase().indexOf(search_term.toLowerCase()) != -1) {
              matches.push(post_id);
              return false;
            }
          });
        });
        if (matches.length == 0) {
          display_search_message('No results were found.');
        } else {

          // Copy the associated elements
          $.each(matches, (match_index, post_id) => {

            let element     = by_alpha.find(`li[data-post-id="${post_id}"]:eq(0)`),
              post_id_int = parseInt(post_id, 10);
            post_id_sum += post_id_int;
            if (element.length == 1) {
              elements.push(element.clone());
            }
          });

          if (elements.length == 0) {
            display_search_message('No results were found.');
          } else {

            // Are the results the same as last time?
            if (post_id_sum != prev_post_id_sum) {
              results.empty();
              prev_post_id_sum = post_id_sum;


              // Slice the elements into their respective columns
              elements_per_column = Math.ceil(elements.length / column_count);
              for (let i = 0; i < column_count; i++) {
                let start = i * elements_per_column,
                  end   = start + elements_per_column;
                if (elements.length > start) {
                  columns[i] = elements.slice(start, end);
                }
              }

              // Setup results HTML
              results.append($('<div class="row"></div>'));
              $.each(columns, (column_index, column_elements) => {
                let column_wrap = $(`<div class="${column_width}"><ul></ul></div>`),
                  column_list = column_wrap.find('ul');

                $.each(column_elements, (element_index, element) => {
                  column_list.append($(element));
                });
                results.find('div[class="row"]').append(column_wrap);
              });
              results.show();
            }
          }
        }
      }
    });
};


/* Assign browser-specific body classes on page load */
const addBodyClasses = function ($) {
  let bodyClass = '';
  // Old IE:
  if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) { // test for MSIE x.x;
    const ieversion = new Number(RegExp.$1); // capture x.x portion and store as a number
    if (ieversion >= 9) {
      bodyClass = 'ie ie9';
    } else if (ieversion >= 8) {
      bodyClass = 'ie ie8';
    }
  }

  $('body').addClass(bodyClass);
};


const parallaxPhotos = function ($) {
  const isTabletSize = function () {
    if ($(window).width() <= 768) {
      return true;
    }
    return false;
  };
  /* Detect touch-enabled browsers.  (Modernizr check) */
  function isTouchDevice() {
    return 'ontouchstart' in window || window.DocumentTouch && document instanceof DocumentTouch;
  }

  const toggleStellar = function () {
    if (isTabletSize() || isTouchDevice()) {
      if ($(window).data('plugin_stellar')) {
        $(window).data('plugin_stellar').destroy();
      }
      $('.parallax-photo')
        .css({
          'background-position': '50% -50px',
          'background-attachment': 'scroll'
        });
    } else {
      $(window).stellar({
        horizontalScrolling: false,
        responsive: true,
        parallaxElements: false
      });
    }
  };

  toggleStellar();
  $(window).resize(() => {
    toggleStellar();
  });
};


/* Fit subpage title text within the heading's set width */
const subpageTitleSize = function ($) {
  const h1 = $('.parallax-header h1');
  if ($('body').hasClass('ie8')) {
    h1.fitText(0.75, {
      minFontSize: '20px',
      maxFontSize: '120px'
    });
  } else {
    h1.fitText(0.7, {
      minFontSize: '20px',
      maxFontSize: '120px'
    });
  }
};


/* Add Bootstrap button styles for GravityForm submit buttons */
const styleGformButtons = function ($) {
  $('.gform_button').addClass('btn');
  $(document).bind('gform_post_render', () => {
    // Handle buttons generated with ajax
    $('.gform_button').addClass('btn');
  });
};


/* Toggle mobile nav */
const mobileNavToggle = function ($) {
  $('nav.header-nav .mobile-nav-toggle')
    .on('click', function () {
      $(this).parents('.header-nav').toggleClass('mobile-active');
    });
};


const socialButtonTracking = function ($) {
  $('.social a').click(function () {
    let link = $(this),
      target = link.attr('data-button-target'),
      network = '',
      socialAction = '';

    if (link.hasClass('share-facebook')) {
      network = 'Facebook';
      socialAction = 'Like';
    } else if (link.hasClass('share-twitter')) {
      network = 'Twitter';
      socialAction = 'Tweet';
    } else if (link.hasClass('share-linkedin')) {
      network = 'Linkedin';
      socialAction = 'Share';
    } else if (link.hasClass('share-email')) {
      network = 'Email';
      socialAction = 'Share';
    }

    if (typeof ga !== 'undefined' && network !== null && socialAction !== null) {
      ga('send', 'social', network, socialAction, window.location);
    }
  });
};


const customChart = function ($) {
  const $charts = $('.custom-chart');
  if ($charts.length) {
    $.each($charts, function () {
      const $chart = $(this);
      // Update id of chart if it is set to default.
      if ($chart.attr('id') === 'custom-chart') {
        $chart.attr('id', `custom-chart-${idx}`);
      }
      let type = $(this).attr('data-chart-type'),
        jsonPath = $(this).attr('data-chart-data'),
        optionsPath = $(this).attr('data-chart-options'),
        canvas = document.createElement('canvas'),
        ctx = canvas.getContext('2d'),
        data = {};

      // Set default options
      const options = {
        responsive: true,
        scaleShowGridLines: false,
        pointHitDetectionRadius: 5
      };

      $chart.append(canvas);
      $.getJSON(jsonPath, (json) => {
        data = json;
        $.getJSON(optionsPath, (json) => {
          $.extend(options, options, json);
        }).complete(() => {
          switch (type.toLowerCase()) {
            case 'bar':
              var barChart = new Chart(ctx).Bar(data, options);
              break;
            case 'line':
              var lineChart = new Chart(ctx).Line(data, options);
              break;
            case 'radar':
              var radarChart = new Chart(ctx).Radar(data, options);
              break;
            case 'polar-area':
              var polarAreaChart = new Chart(ctx).PolarArea(data, options);
              break;
            case 'pie':
              var pieChart = new Chart(ctx).Pie(data, options);
              break;
            case 'doughnut':
              var doughnutChart = new Chart(ctx).Doughnut(data, options);
              break;
            default:
              break;
          }
        });
      })
        .fail((e) => {
          console.log(e);
        });
    });
  }
};


if (typeof jQuery !== 'undefined') {
  jQuery(document).ready(($) => {
    Webcom.analytics($);
    Webcom.handleExternalLinks($);
    Webcom.loadMoreSearchResults($);

    /* Theme Specific Code Here */
    Generic.removeExtraGformStyles($);
    Generic.PostTypeSearch($);

    addBodyClasses($);
    parallaxPhotos($);
    subpageTitleSize($);
    styleGformButtons($);
    mobileNavToggle($);
    customChart($);
  });
} else {
  console.log('jQuery dependancy failed to load');
}
