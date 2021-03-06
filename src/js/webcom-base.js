/** ****************************************************************************\
 Global Namespace
 the only variable exposed to the window should be Webcom
\******************************************************************************/
const Webcom = {};

if (!window.console) {
  window.console = {
    log() {
      return;
    }
  };
}

// for jslint validation
/* global window, document, Image, $, jQuery */

Webcom.handleExternalLinks = function ($) {
  $('a:not(.ignore-external)').each(function () {
    const url  = $(this).attr('href');
    const host = window.location.host.toLowerCase();

    if (url && url.search(host) < 0 && url.search('http') > -1) {
      $(this).attr('target', '_blank');
      $(this).addClass('external');
    }
  });
};

Webcom.loadMoreSearchResults = function ($) {
  const more        = '#search-results .more';
  const items       = '#search-results .result-list .item';
  const list        = '#search-results .result-list';
  const start_class = 'new-start';

  let next = null;
  let sema = null;

  var load = function () {
    if (sema) {
      setTimeout(() => {
        load();
      }, 100);
      return;
    }

    if (next == null) {
      return;
    }

    // Grab results content and append to current results
    const results = $(next).find(items);

    // Add navigation class for scroll
    $(`.${start_class}`).removeClass(start_class);
    $(results[0]).addClass(start_class);

    $(list).append(results);

    // Grab new more link and replace current with new
    const anchor = $(next).find(more);
    if (anchor.length < 1) {
      $(more).remove();
    }
    $(more).attr('href', anchor.attr('href'));

    next = null;
  };

  const prefetch = function () {
    sema = true;
    // Fetch url for href via ajax
    const url = $(more).attr('href');
    if (url) {
      $.ajax({
        url,
        success(data) {
          next = data;
        },
        complete() {
          sema = false;
        }
      });
    }
  };

  const load_and_prefetch = function () {
    load();
    prefetch();
  };

  if ($(more).length > 0) {
    load_and_prefetch();

    $(more).click(() => {
      load_and_prefetch();
      const scroll_to = $(`.${start_class}`).offset().top - 10;

      let element = 'body';

      if ($.browser.mozilla || $.browser.msie) {
        element = 'html';
      }

      $(element).animate({
        scrollTop : scroll_to
      }, 1000);
      return false;
    });
  }
};
