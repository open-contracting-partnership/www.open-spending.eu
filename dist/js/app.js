jQuery((function(e){var t=document.getElementById("heroCanvas"),i=t.getContext("2d");t.width=window.innerWidth,t.height=window.innerHeight;for(var a=[],r=45,o=Math.floor(t.width/36),s={x:0,y:0},n=0;n<o;n++)a.push({x:Math.random()*t.width,y:Math.random()*t.height,radius:1*Math.random()+1,vx:Math.floor(50*Math.random())-25,vy:Math.floor(50*Math.random())-25});function l(e,t){var i=0,a=0;return i=t.x-e.x,i*=i,a=t.y-e.y,a*=a,Math.sqrt(i+a)}t.addEventListener("mousemove",(function(e){s.x=e.clientX,s.y=e.clientY})),function e(){!function(){i.clearRect(0,0,t.width,t.height),i.globalCompositeOperation="lighter";for(var e=0,r=a.length;e<r;e++){var o=a[e];i.fillStyle="#fff",i.beginPath(),i.arc(o.x,o.y,o.radius,0,2*Math.PI),i.fill(),i.fillStyle="black",i.stroke()}for(i.beginPath(),e=0,r=a.length;e<r;e++){var n=a[e];i.moveTo(n.x,n.y),l(s,n)<150&&i.lineTo(s.x,s.y);var h=0;for(r=a.length;h<r;h++){var d=a[h];l(n,d)<150&&i.lineTo(d.x,d.y)}}i.lineWidth=.3,i.strokeStyle="white",i.stroke()}(),function(){for(var e=0,i=a.length;e<i;e++){var o=a[e];o.x+=o.vx/r,o.y+=o.vy/r,(o.x<0||o.x>t.width)&&(o.vx=-o.vx),(o.y<0||o.y>t.height)&&(o.vy=-o.vy)}}(),requestAnimationFrame(e)}()})),jQuery((function(e){if(e(".left-side-data, .right-side-data").slick({vertical:!0,verticalSwiping:!0,slidesToShow:3,slidesToScroll:1,autoplay:!0,autoplaySpeed:0,speed:1e4,cssEase:"linear",infinite:!0,arrows:!1,touchMove:!1,swipeToSlide:!0,swipe:!1}),e(document).mouseup((function(t){var i=e(".has-inline-text"),a=e(".highlight-and-share-wrapper");i.is(t.target)||0!==i.has(t.target).length||a.is(t.target)||0!==a.has(t.target).length||(e(".highlight-and-share-wrapper").hide(),2==e(".highlight-and-share-wrapper").length&&i.trigger("click"))})),e(".members-slider").slick({dots:!1,infinite:!0,autoplay:!0,autoplaySpeed:2e3,slidesToShow:5,slidesToScroll:1,prevArrow:e(".prev"),nextArrow:e(".next"),responsive:[{breakpoint:1024,settings:{slidesToShow:4}},{breakpoint:768,settings:{slidesToShow:3}},{breakpoint:480,settings:{slidesToShow:2}}]}),e(".members-tab-menu").on("click",".members-tab-item",(function(){e(".members-tab-item").removeClass("active"),e(this).addClass("active"),e(".members-experts-btn").hasClass("active")&&(e(".members-experts-list").show(),e(".members-organizations-list").hide()),e(".members-organizations-btn").hasClass("active")&&(e(".members-experts-list").hide(),e(".members-organizations-list").show())})),e(".campaign-vid-thumbnail").on("click",(function(){e("body").css("overflow-y","hidden"),e(".video-page").addClass("show");var t=e(this).attr("data-src");e("iframe").attr("src",t)})),e(".video-close").on("click",(function(){e("body").css("overflow-y","auto"),e(".video-page").removeClass("show"),e("iframe").attr("src",e("iframe").attr("src"))})),e(document).mouseup((function(t){var i=e(".wp-block-navigation__responsive-container");i.is(t.target)||0!==i.has(t.target).length||e(".wp-block-navigation__responsive-container-close").trigger("click")})),e("body").hasClass("home")){var t=e(".member-data")[0].scrollHeight;e(".member-data").attr("data-height",t),root=document.documentElement,setTimeout((function(){root.style.setProperty("--member-height",t+"px")}),500)}}));
