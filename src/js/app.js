jQuery(function ($) {
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
   * Twitter sharable link hide on click on body
   * =================================================
   */

  $(document).mouseup(function (e) {
    var container = $(".has-inline-text");
    var tweetPopup = $(".highlight-and-share-wrapper");
    // var container = $(".highlight-and-share-wrapper");

    // if the target of the click isn't the container nor a descendant of the container
    if (!container.is(e.target) && container.has(e.target).length === 0) {
      if (!tweetPopup.is(e.target) && tweetPopup.has(e.target).length === 0) {
        // $(".highlight-and-share-wrapper").css("opacity", "0");
        $(".highlight-and-share-wrapper").hide();
        if ($(".highlight-and-share-wrapper").length == 2) {
          container.trigger("click");
        }
      }
    }
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
   * Archive page accordion card height
   * =================================================
   */
  // if (window.innerWidth > 640) {
  //   var cardItems = document.querySelectorAll(".accordion-card-inside");
  //   var maxHeight = 0;
  //   cardItems.forEach((cardItem) => {
  //     var cardHeight = $(cardItem).height();
  //     if (cardHeight > maxHeight) {
  //       maxHeight = cardHeight;
  //     }
  //   });
  //   cardItems.forEach((cardItem) => {
  //     $(cardItem).css("min-height", maxHeight + 40);
  //   });
  // }

  /**
   * =================================================
   * Archive page accordion
   * =================================================
   */
  // var $box = $(".archive-accordion").isotope({
  //   itemSelector: ".archive-accordion-items",
  //   layoutMode: "fitRows",
  // });
  // var iso = $box.data("isotope");

  // $(".archive-accordion-category").on("click", "p", function (e) {
  //   console.log("Helloww");
  //   e.preventDefault();
  //   var blockToShow = $(".no-filter-items");
  //   $(blockToShow).hide();
  //   var filterValue = $(this).attr("data-filter");
  //   $(".category-item").removeClass("active");
  //   $(this).addClass("active");
  //   $box.isotope({ filter: filterValue });
  //   var filteredItems = iso.filteredItems.length;
  //   if (filteredItems == 0) {
  //     $(blockToShow).show();
  //   }
  // });

  /**
   * =================================================
   * News filter by country accordion
   * =================================================
   */
  // $("#news-country-filter").change(function (e) {
  //   e.preventDefault();
  //   // var filterValue = $(this).attr("data-filter");
  //   // $box.isotope({ filter: filterValue });
  //   var filterValue = $(this).children(":selected").attr("data-filter");
  //   $box.isotope({ filter: filterValue });
  // });

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
    if (!/^https:\/\/(www\.)?youtube(-nocookie)?\.com\/embed\//.test(source)) {
      return;
    }
    $("body").css("overflow-y", "hidden");
    $(".video-page").addClass("show");
    $("iframe").attr("src", source);
  });
  $(".video-close").on("click", function () {
    $("body").css("overflow-y", "auto");
    $(".video-page").removeClass("show");
    $("iframe").attr("src", $("iframe").attr("src"));
  });

  /**
   * =================================================
   * Member quote animation
   * =================================================
   */
  // $(".member-card").on("mouseenter", function () {
  //   $(this).addClass("animateUp");
  //   $(this).removeClass("animateDown");
  // });

  // $(".member-card").on("mouseleave", function () {
  //   $(this).removeClass("animateUp");
  //   $(this).addClass("animateDown");
  // });

  /**
   * =================================================
   * Responsive nabvar hide on click outside
   * =================================================
   */
  $(document).mouseup(function (e) {
    var container = $(".wp-block-navigation__responsive-container");
    if (!container.is(e.target) && container.has(e.target).length === 0) {
      // $(".highlight-and-share-wrapper").css("opacity", "0");
      $(".wp-block-navigation__responsive-container-close").trigger("click");
    }
  });

  /**
   * =================================================
   * place this placeholder ath bottom of the code
   *
   * @if no bugs this code will appear in console
   * =================================================
   */
  console.log("App Loaded Successfully.");

  /**
   * =================================================
   * Who we are section animation
   * =================================================
   */
  if ($("body").hasClass("home")) {
    var membersHeight = $(".member-data")[0].scrollHeight;
    $(".member-data").attr("data-height", membersHeight);
    root = document.documentElement;
    setTimeout(function () {
      root.style.setProperty("--member-height", membersHeight + "px");
    }, 500);
  }
});
