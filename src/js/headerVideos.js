//
// Handles pause/play buttons for video backgrounds.
//

(function ($) {

  const $btns = $('.header-video-toggle');
  const $videos = $('.page-header-video');

  function btnClickHandler() {
    if ($btns.first().hasClass('play-enabled')) {
      toggleBtnPause();
      bgVideosPause();
    } else {
      toggleBtnPlay();
      bgVideosPlay();
    }
  }

  function toggleBtnPlay() {
    $btns
      .removeClass('play-disabled')
      .addClass('play-enabled')
      .attr('aria-pressed', true);
  }

  function toggleBtnPause() {
    $btns
      .removeClass('play-enabled')
      .addClass('play-disabled')
      .attr('aria-pressed', false);
  }

  function bgVideosPlay() {
    $videos.each(function () {
      if (this.paused || this.ended) {
        this.play();
      }
    });
  }

  function bgVideosPause() {
    $videos.each(function () {
      if (!this.paused || !this.ended) {
        this.pause();
      }
    });
  }

  function init() {
    if ($videos.length && $btns.length) {
      toggleBtnPlay();
      bgVideosPlay();

      // Show the toggle btn and apply event handling
      $btns
        .on('click', btnClickHandler);

      // Reset the btn controls when the first video on the page ends
      $videos.get(0).addEventListener('ended', toggleBtnPause, false);
    }
  }

  $(init);


}(jQuery));
