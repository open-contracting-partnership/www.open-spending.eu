/**
 * Slick sliders. Slick is a jQuery plugin, so this is the theme's only
 * remaining jQuery consumer.
 */
jQuery(($) => {
  // Homepage member columns: continuous vertical auto-scroll.
  $(".left-side-data, .right-side-data").slick({
    vertical: true,
    verticalSwiping: true,
    slidesToShow: 3,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 0,
    speed: 10000,
    cssEase: "linear",
    infinite: true,
    arrows: false,
    touchMove: false,
    swipeToSlide: true,
    swipe: false,
  });

  // "Who we are" logo carousel.
  $(".members-slider").slick({
    dots: false,
    infinite: true,
    autoplay: true,
    autoplaySpeed: 2000,
    slidesToShow: 5,
    slidesToScroll: 1,
    prevArrow: $(".prev"),
    nextArrow: $(".next"),
    responsive: [
      { breakpoint: 1024, settings: { slidesToShow: 4 } },
      { breakpoint: 768, settings: { slidesToShow: 3 } },
      { breakpoint: 480, settings: { slidesToShow: 2 } },
    ],
  });
});
