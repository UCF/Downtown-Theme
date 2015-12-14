(function() {

  var inviewElemsArray,
      $inviewElems;

  function getInviewElemsArray() {
    var elems = [];

    for (var i = 0; i < $inviewElems.length; i++) {
      elems[i] = $inviewElems.eq(i);
    }

    return elems;
  }

  // Animates increment of a number (no decimals). Handles commas.
  function animateNumber(e) {
    var $num = $(e.target),
        numText = parseInt($num.text().replace(/\D/g, ''), 10);

    $num.css('width', $num.width()); // force fixed width to reduce flicker

    $({number: 0}).animate({number: numText}, {
      duration: 1500,
      easing: 'swing',
      step: function() {
        $num.text(commaSeparateNumber(Math.ceil(this.number)));
      },
      done: function() {
        $num.css('width', ''); // removed forced width
      }
    });
  }

  // Returns a comma-separated numerical string.
  // http://stackoverflow.com/a/16228123
  function commaSeparateNumber(val){
    while (/(\d+)(\d{3})/.test(val.toString())){
      val = val.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    }
    return val;
  }

  // Animates "dollar box" growth.
  function animateDollarBox(e) {
    var $boxInner = $(e.target),
        $box = $boxInner.parent(),
        boxHeight = $box.height(),
        delay = parseInt($boxInner.attr('data-delay'), 10) || 0;

    $box.css('height', boxHeight);

    $boxInner
      .css({
        'height': 0,
        'position': 'absolute'
      })
      .delay(delay)
      .animate({height: boxHeight}, {
        duration: 800,
        easing: 'swing',
        done: function() {
          $boxInner.css({
            'height': '',
            'position': ''
          });
          $box.css('height', '');
        }
      });
  }

  // Fires the 'inview' event when any element in inviewElemsArray
  // (has class .inview) is visible in the viewport.
  //
  // Removes itself as a binded event on the window object when all
  // elements in inviewElemsArray have had an 'inview' event fire once.
  function inviewWatcher() {
    var newInviewElemsArray = [];
    if (inviewElemsArray.length) {
      for (var i = 0; i < inviewElemsArray.length; i++) {
        var $elem = inviewElemsArray[i];
        if ($elem.offset().top < $(window).scrollTop() + $(window).outerHeight()) {
          $elem.trigger('inview');
        }
        else {
          newInviewElemsArray.push(i);
        }
      }

      inviewElemsArray = newInviewElemsArray;
    }
    else {
      // Only listen on window load/scroll for as long as necessary (until all
      // inview events have fired)
      $(window).off('load scroll', inviewWatcher);
    }
  }

  function init() {
    $inviewElems = $('.inview');
    inviewElemsArray = getInviewElemsArray();

    $(window).on('load scroll', inviewWatcher);
    $('.number-animated').on('inview', animateNumber);
    $('.dollar-box-inner').on('inview', animateDollarBox);
  }

  $(init);

}());
