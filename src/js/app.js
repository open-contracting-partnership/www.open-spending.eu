import "./hero-animation.js";

jQuery(($) => {
  /**
   * =================================================
   * Homepage member animation
   * =================================================
   */
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

  /**
   * =================================================
   * Who we are section slider
   * =================================================
   */
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
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 4,
        },
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 3,
        },
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 2,
        },
      },
    ],
  });

  /**
   * =================================================
   * Members accordion
   * =================================================
   */
  $(".members-tab-menu").on("click", ".members-tab-item", function () {
    $(".members-tab-item").removeClass("active");
    $(this).addClass("active");
    if ($(".members-experts-btn").hasClass("active")) {
      $(".members-experts-list").show();
      $(".members-organizations-list").hide();
    }
    if ($(".members-organizations-btn").hasClass("active")) {
      $(".members-experts-list").hide();
      $(".members-organizations-list").show();
    }
  });

  /**
   * =================================================
   * Campaign detail page video popup
   * =================================================
   */
  $(".campaign-vid-thumbnail").on("click", function () {
    var source = $(this).attr("data-src");
    var match = source?.match(
      /(?:youtube(?:-nocookie)?\.com\/(?:watch\?v=|embed\/|v\/|shorts\/)|youtu\.be\/)([\w-]{11})/,
    );
    if (!match) {
      return;
    }
    var embedUrl = `https://www.youtube.com/embed/${match[1]}`;
    var $modal = $(this).siblings(".video-page");
    $("body").css("overflow-y", "hidden");
    $modal.addClass("show");
    $modal.find("iframe").attr("src", embedUrl);
  });
  $(".video-close").on("click", function () {
    var $modal = $(this).closest(".video-page");
    $("body").css("overflow-y", "auto");
    $modal.removeClass("show");
    $modal.find("iframe").attr("src", "");
  });

  /**
   * =================================================
   * Responsive nabvar hide on click outside
   * =================================================
   */
  $(document).mouseup((e) => {
    var container = $(".wp-block-navigation__responsive-container");
    if (!container.is(e.target) && container.has(e.target).length === 0) {
      $(".wp-block-navigation__responsive-container-close").trigger("click");
    }
  });
});
