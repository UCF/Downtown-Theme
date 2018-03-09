// Adds filter method to array objects
// https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/filter

if (!Array.prototype.filter) {
  Array.prototype.filter = function (a) {
    if (this === void 0 || this === null) {
      throw new TypeError();
    }
    const b = Object(this);
    const c = b.length >>> 0;
    if (typeof a !== 'function') {
      throw new TypeError();
    }
    const d = [];
    const e = arguments[1];
    for (let f = 0; f < c; f++) {
      if (f in b) {
        const g = b[f];
        if (a.call(e, g, f, b)) {
          d.push(g);
        }
      }
    }
    return d;
  };
}

const WebcomAdmin = {};


WebcomAdmin.__init__ = function ($) {
  // Allows forms with input fields of type file to upload files
  $('input[type="file"]').parents('form').attr('enctype', 'multipart/form-data');
  $('input[type="file"]').parents('form').attr('encoding', 'multipart/form-data');
};


WebcomAdmin.shortcodeTool = function ($) {
  const cls   = this;
  cls.metabox = $('#shortcodes-metabox');
  if (cls.metabox.length < 1) {
    console.log('no meta');
    return;
  }

  cls.form     = cls.metabox.find('form');
  cls.search   = cls.metabox.find('#shortcode-search');
  cls.button   = cls.metabox.find('button');
  cls.results  = cls.metabox.find('#shortcode-results');
  cls.select   = cls.metabox.find('#shortcode-select');
  cls.formURL  = cls.metabox.find('#shortcode-form').val();
  cls.textURL  = cls.metabox.find('#shortcode-text').val();

  cls.shortcodes = (function () {
    const shortcodes = [];
    cls.select.children('.shortcode').each(function () {
      shortcodes.push($(this).val());
    });
    return shortcodes;
  }());

  cls.shortcodeAction = function (shortcode) {
    const text = `[${shortcode}]`;
    send_to_editor(text);
  };

  cls.searchAction = function () {
    cls.results.children().remove();

    const value = cls.search.val();

    if (value.length < 1) {
      return;
    }

    const found = cls.shortcodes.filter((e, i, a) => {
      return e.match(value);
    });

    if (found.length > 1) {
      cls.results.removeClass('empty');
    }

    $(found).each(function () {
      const item      = $('<li />');
      const link      = $('<a />');
      link.attr('href', '#');
      link.addClass('shortcode');
      link.text(this.valueOf());
      item.append(link);
      cls.results.append(item);
    });


    if (found.length > 1) {
      cls.results.removeClass('empty');
    } else {
      cls.results.addClass('empty');
    }

  };

  cls.buttonAction = function () {
    cls.searchAction();
  };

  cls.itemAction = function () {
    const shortcode = $(this).text();
    cls.shortcodeAction(shortcode);
    return false;
  };

  cls.selectAction = function () {
    const selected = $(this).find('.shortcode:selected');
    if (selected.length < 1) {
      return;
    }

    const value = selected.val();
    cls.shortcodeAction(value);
  };

  // Resize results list to match size of input
  cls.results.width(cls.search.outerWidth());

  // Disable enter key causing form submit on shortcode search field
  cls.search.keyup((e) => {
    cls.searchAction();

    if (e.keyCode == 13) {
      return false;
    }
  });

  // Search button click action, cause search
  cls.button.click(cls.buttonAction);

  // Option change for select, cause action
  cls.select.change(cls.selectAction);

  // Results click actions
  cls.results.find('li a.shortcode').live('click', cls.itemAction);
};


WebcomAdmin.themeOptions = function ($) {
  const cls    = this;
  cls.active   = null;
  cls.parent   = $('.i-am-a-fancy-admin');
  cls.sections = $('.i-am-a-fancy-admin .fields .section');
  cls.buttons  = $('.i-am-a-fancy-admin .sections .section a');

  this.showSection = function (e) {
    const button  = $(this);
    const href    = button.attr('href');
    const section = $(href);

    // Switch active styles
    cls.buttons.removeClass('active');
    button.addClass('active');

    cls.active.hide();
    cls.active = section;
    cls.active.show();

    history.pushState({}, '', button.attr('href'));
    const http_referrer = cls.parent.find('input[name="_wp_http_referer"]');
    http_referrer.val(window.location);
    return false;
  };

  this.__init__ = function () {
    cls.active = cls.sections.first();
    cls.sections.not(cls.active).hide();
    cls.buttons.first().addClass('active');
    cls.buttons.click(this.showSection);

    if (window.location.hash) {
      cls.buttons.filter(`[href="${window.location.hash}"]`).click();
    }

    const fadeTimer = setInterval(() => {
      $('.updated').fadeOut(1000);
      clearInterval(fadeTimer);
    }, 2000);
  };

  if (cls.parent.length > 0) {
    cls.__init__();
  }
};


(function ($) {
  WebcomAdmin.__init__($);
  WebcomAdmin.themeOptions($);
  WebcomAdmin.shortcodeTool($);
}(jQuery));
