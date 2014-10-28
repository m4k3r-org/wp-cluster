define("app",["module","require","exports"],function(module,localRequire,exports){console.debug("loaded",module.id),require.config({baseUrl:exports.baseUrl=document.getElementsByName("festival:url")[0].content+"/static/scripts/src"}),require(["components/require.config"],function(config){components.baseUrl=document.getElementsByName("festival:url")[0].content+"/static/scripts/src",components.paths.jquery="components/jquery/jquery.min",require.config(components),require(["module","require","exports","jquery","lib/developer","lib/equalheights","lib/swipe","lib/countdown","lib/smoothscroll","lib/stickem","lib/dotdotdot","lib/stickynav","lib/buytickets","lib/navigation","lib/masonry","lib/carousel","lib/account","lib/stream-filter","lib/artist-profile","lib/collapse","lib/share","lib/imagelightbox","lib/stream","lib/fancybox","lib/blog-main","lib/tabbed-content","lib/module-video","lib/contact-form","lib/hotel-widget","components/fitvids/fitvids-built"],function(module,require,exports,$,developer,equalheights,swipe,countdown,ss,stickem,dotdotdot,stickynav,buytickets,navigation,masonry,carousel,account,streamFilter,artistProfile,collapse,share,imagelightbox,stream,fancybox,blogMain,tabbedContent,videoModule,contact,hotelWidget,fv){var self=this,resizeTo=null;console.debug("developer",developer),$(window).resize(function(){resizeTo!==null&&clearTimeout(resizeTo),resizeTo=setTimeout(function(){$(this).trigger("resizeEnd")},250)}),$("a.imagelightbox").length>0&&imagelightbox.init(),$("a.fancybox").length>0&&fancybox.init(),$(".hotel-widget").length>0&&hotelWidget.init(),countdown.init(),ss.init(),stickynav.init(),buytickets.init(),navigation.init(),account.init(),collapse.init(),share.init(),$(".video-module-container").length&&videoModule.init(),$(window).on("resizeEnd",function(){$(window).height()>$("#doc > header").height()&&$("#doc > header").height($(window).height())}),$(window).on("resizeEnd",function(){$(window).height()>$(".hero-container").height()&&$(".hero-container").height($(window).height())}),$(window).trigger("resize");var doc=$("#doc");if(doc.hasClass("page-home")){equalheights.equalize($(".main-artists .main-artist"),768),equalheights.equalize($(".artist-lineup .callout, .artist-lineup.tier-one .main-artists"),768),equalheights.equalize($(".location, .accommodations"),992),$(".news-slider-container").length&&swipe.init(".news-slider-container",".news-slider",".card",".news-slider-container .indicator-parent"),$(".accommodations-slider-container").length&&swipe.init(".accommodations-slider-container",".accommodations-slider",".card",".accommodations .indicator-parent"),$(".page-photo-gallery").length&&masonry.init(".photo-grid");var photoVideoScroller=null;$(".photos-videos-strip-container").length&&$(window).on("resizeEnd",function(){document.documentElement.clientWidth>=768?(swipe.destroy(photoVideoScroller),photoVideoScroller=null,$(".photos-videos-strip-container").data("initswipe",!1),$(".photos-videos-strip").removeAttr("style")):$(".photos-videos-strip-container").data("initswipe")||(photoVideoScroller=swipe.init(".photos-videos-strip-container",".photos-videos-strip",".item",".photos-videos-strip-container .indicator-parent"),$(".photos-videos-strip-container").data("initswipe",!0))}),$(window).trigger("resize"),document.documentElement.clientWidth>=768&&stickem.init(),dotdotdot.init()}else if(doc.hasClass("page-artist-lineup")){equalheights.equalize($(".main-artists .main-artist"),768),equalheights.equalize($(".artist-lineup .callout, .artist-lineup.tier-one .main-artists"),768),equalheights.equalize($(".artist-lineup2 .main-artists .main-artist"),768);var tier2ArtistScroller=null;$(window).on("resizeEnd",function(){document.documentElement.clientWidth>=992?(swipe.destroy(tier2ArtistScroller),tier2ArtistScroller=null,$(".artist-slider-container").data("initswipe",!1),$(".artist-slider").removeAttr("style")):$(".artist-slider-container").data("initswipe")||(tier2ArtistScroller=swipe.init(".artist-slider-container",".artist-slider",".tier2-artist",".artist-slider-container .indicator-parent"),$(".artist-slider-container").data("initswipe",!0))}),$(window).trigger("resize"),stickem.init()}else doc.hasClass("page-features")?equalheights.equalize($(".features-content .feature-item"),768):doc.hasClass("page-photo-gallery")?masonry.init(".photo-grid"):doc.hasClass("page-organizers")?equalheights.equalize($(".organizers-content .organizer-item"),768):doc.hasClass("page-sponsors")&&equalheights.equalize($(".sponsors-content .sponsor-item"),768);$("#latest-blog-posts").length&&swipe.init("#latest-blog-posts",".posts",".post","#latest-blog-posts .indicator-parent");if($(".tier3-artists").length){var tier3ArtistScroller=null;$(window).on("resizeEnd",function(){document.documentElement.clientWidth>=992?(swipe.destroy(tier3ArtistScroller),tier3ArtistScroller=null,$(".tier3-artists").data("initswipe",!1),$(".tier3-artists .the-list").removeAttr("style")):$(".tier3-artists").data("initswipe")||(tier3ArtistScroller=swipe.init(".tier3-artists",".the-list",".tier2-artist",".tier3-artists .indicator-parent"),$(".tier3-artists").data("initswipe",!0))})}if($(".tier-two").length){var tier2ArtistScroller=null;$(window).on("resizeEnd",function(){document.documentElement.clientWidth>=992?(swipe.destroy(tier2ArtistScroller),tier2ArtistScroller=null,$(".tier2-artists").data("initswipe",!1),$(".tier2-artists .tier-two").removeAttr("style")):$(".tier-two").data("initswipe")||(tier2ArtistScroller=swipe.init(".tier2-artists",".tier-two",".main-artist",".tier2-artists .indicator-parent"),$(".tier2-artists").data("initswipe",!0))})}$(".single-artist").length&&(carousel.init(),streamFilter.init($(".stream-filters")),artistProfile.initOverlay()),$(".feature-item").length&&equalheights.equalize($(".feature-item"),768),$(".organizer-item").length&&equalheights.equalize($(".organizer-item"),768),$(".posts-list-container").length&&blogMain.init(),$(".single-post").length>0&&tabbedContent.init(),$(".page-contact").length>0&&contact.init();if($(".tpl-panama").length>0){equalheights.equalize($(".equal-height > div"),768);if($(".getting-there").length>0){var gtContainer=$(".getting-there");$(".tab-header a").click(function(e){e.preventDefault();var id=$(this).attr("href");$(".tab-content",gtContainer).hide(),$(id).show(),$(".tab-header a").removeClass("selected"),$(this).addClass("selected")}),$(".tab-header a:first").trigger("click")}}$("article.content").length>0&&$("article.content").fitVids(),equalheights.equalize($(".hotel-item .row > .col-xs-12"),768),$(window).on("resizeEnd",function(){$(".hotel-item").each(function(){var t=$(this),rtHeight=$(".room-types",t).outerHeight(!0),rtpHeight=$(".room-types",t).parents(".col-xs-12").outerHeight(!0);rtpHeight>rtHeight&&$(".room-types",t).outerHeight(rtpHeight)})}),$(window).trigger("resize"),window.app=window.app||module})})});