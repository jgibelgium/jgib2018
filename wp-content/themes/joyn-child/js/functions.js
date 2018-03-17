/*global jQuery,google */

var SWIFT = SWIFT || {};

(function(){
	
	// USE STRICT
	"use strict";
	
	/////////////////////////////////////////////
	// PAGE FUNCTIONS
	/////////////////////////////////////////////
	
 	SWIFT.page = {
		init: function () {
			
			// BROWSER CHECK
			SWIFT.page.browserCheck();
										
			// FITTEXT
			SWIFT.page.resizeHeadings();
					
			// HEADER SLIDER
			if (jQuery('body').hasClass('header-below-slider') && jQuery('.home-slider-wrap').length > 0) {
			SWIFT.page.headerSlider();
			}
			
			// ONE PAGE NAV
			if (jQuery('#one-page-nav').length > 0) {
				SWIFT.page.onePageNav();
			}
			
			// BACK TO TOP
			if (jQuery('#back-to-top').length > 0) {
				$window.scroll(function() {
					SWIFT.page.backToTop();
				});
			}
						
			// EXPANDING ASSETS
			SWIFT.page.expandingAssets();

			// RECENT POSTS
			if (jQuery('.recent-posts').length > 0) {
				SWIFT.recentPosts.init();
			}
			
			// MOVE MODALS TO BOTTOM OF PAGE
			SWIFT.page.moveModals();
			
			// LOAD MAP ASSET ON MODAL CLICK
			jQuery('a[data-toggle="modal"]').on('click', function() {
				setTimeout(function() {
					SWIFT.map.init();						
				}, 300);
				
				return true;
			});
			
			// REFRESH MODAL IFRAME ON CLOSE (FOR VIDEOS)
			SWIFT.page.modalClose();
			
			// REPLACE COMMENTS REPLY TITLE HTML
			if (body.hasClass('single-post')) {
				var replyTitle = jQuery('#respond').find('h3');
				var originalText = jQuery('#respond').find('h3').html();
				
				replyTitle.addClass('spb-heading');
				replyTitle.html('<span>'+originalText+'</span>');
			}
			
			// SMOOTH SCROLL LINKS
			SWIFT.page.smoothScrollLinks();
			
			// STICKY SIDEBAR
			if (!isMobileAlt && sfIncluded.hasClass('stickysidebars') && jQuery('.sidebar').length > 0) {
				SWIFT.page.stickySidebars();
			}
			
			// PORTFOLIO STICKY SIDEBAR	
			if (!isMobileAlt && jQuery('article.type-portfolio').hasClass('single-portfolio-split')) {
				//SWIFT.portfolio.stickyDetails();
			}
			
			// BUDDYPRESS ACTIVITY LINK CLICK
			jQuery('.activity-time-since,.bp-secondary-action').on('click', function(e) {
				e.preventDefault();
				jQuery('.viewer').css('display', 'none');
				window.location = jQuery(this).attr('href');
			});
			
			// LOVE IT CLICK
			jQuery('.love-it').on('click', function() {
				SWIFT.page.loveIt(jQuery(this));
				return false;
			});
			
			// ARTICLE SHARE
			SWIFT.page.articleShare();
			
			// ARTICLE NAVIGATION
			if (body.hasClass('article-swipe') && body.hasClass('single-post') && jQuery('.post-pagination-wrap').length > 0) {
				//SWIFT.page.articleNavigation();
			}
			
			// ARTICLE WITH FULL WIDTH MEDIA TITLE
			if (jQuery('article').hasClass('single-post-fw-media-title')) {
				SWIFT.page.postMediaTitle();
			}
			
			// RELATED POSTS
			if (jQuery('.related-items').length > 0) {
				SWIFT.relatedPosts.init();
			}
			
			// LIGHTBOX
			SWIFT.page.lightbox();
			
			// MOBILE 2 CLICK IMAGES
			if (isMobileAlt && body.hasClass('mobile-two-click')) {
				SWIFT.page.mobileThumbLinkClick();
			}
			
			// PAGE FADE OUT
			if (body.hasClass('page-transitions') && !isMobileAlt) {
				SWIFT.page.pageTransitions();
			}
			
			// DIRECTORY SUBMIT
			SWIFT.page.directorySubmit();
		},
		homePreloader: function() {
			// Site loaded, fade out preloader
			body.addClass('sf-preloader-done');
			setTimeout(function() {
				jQuery('#sf-home-preloader').fadeOut(300);
			}, 300);
		},
		load: function() {
			
			// Hero HEADING
			if (jQuery('.fancy-heading').length > 0) {
				SWIFT.page.fancyHeading();
			}
			
			// FS NEXT/PREV 
			if (jQuery('#prev-article-pagination') || jQuery('#next-article-pagination')) {
								
				if ((body.hasClass('single-portfolio') && jQuery('article.portfolio').hasClass('single-portfolio-fw-media')) || (body.hasClass('single-post') && (jQuery('article.type-post').hasClass('single-post-fw-media-title') || jQuery('article.type-post').hasClass('single-post-fw-media') ))) {
					
					setTimeout(function() {
						SWIFT.page.fwMediaNextPrevAdjust();
					}, 500);
					$window.smartresize( function() {
						SWIFT.page.fwMediaNextPrevAdjust();
					});
				}
				
				SWIFT.page.fsNextPrev();
				$window.scroll(function() {
					SWIFT.page.fsNextPrev();
				});
			}
			
			// HASH CHECK
			setTimeout(function() {
		        var urlHash = document.location.toString();
				if (urlHash.match('#')) {
				    var hash = urlHash.split('#')[1];
				    if ( jQuery('#' + hash).length > 0 ) {
				        SWIFT.page.onePageNavGoTo('#' + hash);
				    }
				}
		   }, 200);
		},
		browserCheck: function() {
			jQuery.browser = {};
			jQuery.browser.mozilla = /mozilla/.test(navigator.userAgent.toLowerCase()) && !/webkit/.test(navigator.userAgent.toLowerCase());
			jQuery.browser.webkit = /webkit/.test(navigator.userAgent.toLowerCase());
			jQuery.browser.opera = /opera/.test(navigator.userAgent.toLowerCase());
			jQuery.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());
			jQuery.browser.msieMobile10 = /iemobile\/10\.0/.test(navigator.userAgent.toLowerCase());
					
			// BODY CLASSES
			if (isMobileAlt) {
				body.addClass("mobile-browser");
			} else {
				body.addClass("standard-browser");
			}
			if (isIEMobile) {
				body.addClass("ie-mobile");
			}
			if (isAppleDevice) {
				body.addClass("apple-mobile-browser");
			}
			if (body.hasClass("woocommerce-page") && !body.hasClass("woocommerce")) {
				body.addClass("woocommerce");
			}
			
			// ADD IE CLASS
			if (IEVersion && IEVersion < 9) {
				body.addClass('browser-ie');
			}
			
			// ADD IE10 CLASS
			var pattern = /MSIE\s([\d]+)/,
				ua = navigator.userAgent,
				matched = ua.match(pattern);
			if (matched) {
				body.addClass('browser-ie10');
			}
			
			// ADD IE11 CLASS
			if (!!navigator.userAgent.match(/Trident.*rv\:11\./)) {
				body.addClass('browser-ie11');
			}
			
			// ADD MOZILLA CLASS
			if (jQuery.browser.mozilla) {
				body.addClass('browser-ff');
			}
			
			// ADD SAFARI CLASS
			if (navigator.userAgent.search("Safari") >= 0 && navigator.userAgent.search("Chrome") < 0) {
				body.addClass('browser-safari');
			}
		}, 
		resizeHeadings: function() {	
			var h1FontSize = jQuery('h1:not(.logo-h1)').css('font-size'),
				h2FontSize = jQuery('h2:not(.caption-title)').css('font-size');
						
			SWIFT.page.resizeHeadingsResize(h1FontSize, h2FontSize);
			$window.smartresize( function() {
				SWIFT.page.resizeHeadingsResize(h1FontSize, h2FontSize);
			});
		},
		resizeHeadingsResize: function(h1FontSize, h2FontSize) {
			if ($window.width() <= 768) {		
				if (h1FontSize) {
					h1FontSize = h1FontSize.replace("px", "");
					var h1FontSizeMin = Math.floor(h1FontSize * 0.6);
					jQuery('h1:not(.logo-h1)').fitText(1, { minFontSize: h1FontSizeMin + 'px', maxFontSize: h1FontSize +'px' }).css('line-height', '120%');
				}
				
				if (h2FontSize) {
					h2FontSize = h2FontSize.replace("px", "");
					var h2FontSizeMin = Math.floor(h2FontSize * 0.6);
					jQuery('h2:not(.caption-title)').fitText(1, { minFontSize: h2FontSizeMin + 'px', maxFontSize: h2FontSize +'px' }).css('line-height', '120%');
				}
			} else {
				jQuery('h1:not(.logo-h1)').css('font-size', '').css('line-height', '');
				jQuery('h2:not(.caption-title)').css('font-size', '').css('line-height', '');
			}
		},
		loveIt: function($this) {	
			var locale = jQuery('#loveit-locale'),
				post_id = $this.data('post-id'),
				user_id = $this.data('user-id'),
				action = 'love_it';
			
			if ($this.hasClass('loved')) {
				action = 'unlove_it';
			}	
			if (locale.data('loggedin') == 'false' && jQuery.cookie('loved-' + post_id)) {
				action = 'unlove_it';
			}
			
			var data = {
				action: action,
				item_id: post_id,
				user_id: user_id,
				love_it_nonce: locale.data('nonce')
			};
			
			jQuery.post(locale.data('ajaxurl'), data, function(response) {
				var ajaxResponse = jQuery.trim(response),
					count_wrap,
					count;
					
				if (ajaxResponse == 'loved') {
					$this.addClass('loved');
					count_wrap = $this.find('data.count');
					count = count_wrap.text();
					count_wrap.text(parseInt(count) + 1);
					if(locale.data('loggedin') == 'false') {
						jQuery.cookie('loved-' + post_id, 'yes', { expires: 1 });
					}
				} else if (ajaxResponse == 'unloved') {
					$this.removeClass('loved');
					count_wrap = $this.find('data.count');
					count = count_wrap.text();
					count_wrap.text(parseInt(count) - 1);
					if (locale.data('loggedin') == 'false') {
						jQuery.cookie('loved-' + post_id, 'no', { expires: 1 });
					}
				} else {
					alert(locale.data('error'));
				}
			});
		},
		expandingAssets: function() {	
			jQuery('.spb-row-expand-text').on('click', '', function(e) {
				e.preventDefault();
				var expand = jQuery(this),
					expandRow = expand.next();
				
				if (expandRow.hasClass('spb-row-expanding-open') && !expandRow.hasClass('spb-row-expanding-active')) {
					expandRow.addClass('spb-row-expanding-open').addClass('spb-row-expanding-active').slideUp(800);
					setTimeout(function() {
						expand.removeClass('row-open').find('span').text(expand.data('closed-text'));
						expandRow.css('display', 'block').removeClass('spb-row-expanding-open').removeClass('spb-row-expanding-active');
					}, 800);
				} else if (!expandRow.hasClass('spb-row-expanding-active')) {
					expand.addClass('row-open').find('span').text(expand.data('open-text'));
					expandRow.css('display', 'none').addClass('spb-row-expanding-open').addClass('spb-row-expanding-active').slideDown(800);
					setTimeout(function() {
						expandRow.removeClass('spb-row-expanding-active');
					}, 800);
				}
				
			});
		},
		headerSlider: function() {
			
			// SET LOADING INDICATOR
			jQuery('#site-loading').css('display', 'block');		
		
			// SET SLIDER POSITION & HEIGHT
			jQuery('#container').css('position', 'relative');
			jQuery('.home-slider-wrap').css('position', 'fixed');
			jQuery('#container').css('top', jQuery('.home-slider-wrap').height());
			setTimeout(function() {
				jQuery('#site-loading').fadeOut(1000);		
			}, 250);
			
			// RESIZE SLIDER HEIGHT
			$window.smartresize( function() {
				jQuery('#container').css('top', jQuery('.home-slider-wrap').height());
			});
			
			// CONTINUE LINK
			jQuery('a#slider-continue').on('click', function(e) {
				
				// Prevent default anchor action
				e.preventDefault();
				
				// Animate scroll to main content
				jQuery('html, body').stop().animate({
					scrollTop: jQuery('#container').css('top')
				}, 1500, 'easeInOutExpo');
				
			});
		},
		fancyHeading: function() {
			var fancyHeading = jQuery('.fancy-heading'),
				fancyHeadingText = fancyHeading.find('.heading-text'),
				fancyHeadingTextHeight = fancyHeadingText.height(),
				fancyHeadingHeight = parseInt(fancyHeading.data('height'), 10),
				header = jQuery('.header-wrap'),
				headerHeight = 0;
			
			if (body.hasClass('header-naked-light') || body.hasClass('header-naked-dark')) {
				headerHeight = header.height();
			}	
			
			if (!fancyHeadingHeight) {
				fancyHeadingHeight = 400;
			}
			
			if (fancyHeadingTextHeight > fancyHeadingHeight) {
				fancyHeadingHeight = fancyHeadingTextHeight + 80;
			}
			
			fancyHeadingHeight = fancyHeadingHeight + headerHeight;
			
			// Vertically center the heading text
			fancyHeadingText.vCenterTop();
			
			// Animate in the heading text and title
			setTimeout(function() {
				fancyHeading.css({
					'height': fancyHeadingHeight + 'px',
					'opacity': 1
				});
			}, 0);
			setTimeout(function() {
				fancyHeadingText.css('opacity', 1);
			}, 0);
			setTimeout(function() {
				fancyHeading.addClass('animated');
			}, 0);
			
			// Check if parallax scroll is possible
			if (parallaxScroll && !isMobileAlt) {
				$window.scroll(function(){
					
					var scrollTop = $window.scrollTop(),
						headingOffset = 0;
	
					if (jQuery('.sticky-header').length > 0) {
						headingOffset = jQuery('.sticky-header').height() > 0 ? jQuery('.sticky-header').height() : jQuery('#header-section').height();
					}
					if (jQuery('#top-bar').length > 0) {
						headingOffset = headingOffset + jQuery('#top-bar').height();
					}
					if (jQuery('#wpadminbar').length > 0) {
						headingOffset = headingOffset + 32;
					}
					
					scrollTop = scrollTop - headingOffset;
					
					// Only scroll if the heading is makes sense to do so
					if (scrollTop < jQuery(document).height() - $window.height()) {
						
						if (scrollTop < 0) {
							scrollTop = 0;
						}
						
						// Reduce the heading height
						fancyHeading.stop(true,true).transition({
							top: scrollTop / 1.8
						}, 0);
						
						// Move & fade the heading content
						fancyHeadingText.stop(true,true).transition({
							opacity: 1 - scrollTop / 420
						}, 0);
					}
					
				}); 
			}
		},
		moveModals: function() {
			jQuery(".modal").each(function(){
				jQuery(this).appendTo("body");
			});
		},
		modalClose: function() {
			jQuery(".modal-backdrop, .modal .close, .modal .btn").on("click", function() {
				jQuery(".modal iframe").each(function() {
					var thisModal = jQuery(this);
					thisModal.attr("src", thisModal.attr("src"));
				});
			});
		},
		smoothScrollLinks: function() {
			jQuery('a.smooth-scroll-link').on('click', function(e) {

				var linkHref = jQuery(this).attr('href');

				if (linkHref && linkHref.indexOf('#') === 0) {
					var headerHeight = 0;

					if (jQuery('.sticky-header').length > 0) {
						headerHeight = jQuery('.sticky-header').height() > 0 ? jQuery('.sticky-header').height() : jQuery('#header-section').height();
					}
					if (jQuery('#wpadminbar').length > 0) {
						headerHeight = headerHeight + 32;
					}
					if (jQuery('.sticky-top-bar').length > 0) {
						headerHeight = headerHeight + jQuery('.sticky-top-bar').height() > 0 ? jQuery('.sticky-top-bar').height() : jQuery('#top-bar').height();
					}

					SWIFT.isScrolling = true;

					jQuery('html, body').stop().animate({
						scrollTop: jQuery(linkHref).offset().top - headerHeight
					}, 1000, 'easeInOutExpo', function() {
						SWIFT.isScrolling = false;
					});

					e.preventDefault();

				} else {
					return e;
				}

			});			
		},
		onePageNav: function() {
				
			var onePageNav = jQuery('#one-page-nav'),
				onePageNavType = onePageNav.hasClass('opn-arrows') ? "arrows" : "standard",
				onePageNavItems = "",
				pageSectionCount = 0,
				mainContent = jQuery('.page-content');
			
			mainContent.find('section.row').each(function() {
				var linkID = jQuery(this).attr('id'),
					linkName = jQuery(this).data('rowname');
				
				if (linkID && linkName.length > 0 && jQuery(this).height() > 0) {
					onePageNavItems += '<li><a href="#'+linkID+'" data-title="'+linkName+'"><i></i></a><div class="hover-caption">'+linkName+'</div></li>';
					pageSectionCount++;
				}
			});
			
			if (pageSectionCount > 0) {
				onePageNav.append('<ul>'+onePageNavItems+'</ul>');
				
				if (onePageNavType === "arrows") {
					onePageNav.find('ul').css('display', 'none');
					onePageNav.append('<a href="#" class="opn-up"><i class="ss-up"></i></a>');
					onePageNav.append('<div class="opn-status"><span class="current">1</span>/<span class="total">'+pageSectionCount+'</span></div>');
					onePageNav.append('<a href="#" class="opn-down"><i class="ss-down"></i></a>');
				}
					
				onePageNav.vCenter();
				setTimeout(function() {
					
					SWIFT.page.onePageNavScroll(onePageNav);
					
					onePageNav.css('display', 'block').stop().animate({
						'right': '0',
						'opacity': 1
					}, 1000, "easeOutQuart");
					
					// Nav Dots
					jQuery('#one-page-nav ul li a').bind('click', function(e) {
						SWIFT.page.onePageNavGoTo(jQuery(this).attr('href'));
						e.preventDefault();
					});
					
					// Up Arrow
					jQuery('#one-page-nav a.opn-up').bind('click', function(e) {
						var currentSection = parseInt(jQuery('.opn-status .current').text(), 10),
							prevSection = currentSection - 1,
							prevSectionHref = jQuery('#one-page-nav ul li:nth-child('+ prevSection +') > a').attr('href');
						
						if (prevSection > 0) { 
							SWIFT.page.onePageNavGoTo(prevSectionHref);
						}
						e.preventDefault();
					});
					
					// Down Arrow
					jQuery('#one-page-nav a.opn-down').bind('click', function(e) {
						var currentSection = parseInt(jQuery('.opn-status .current').text(), 10),
							nextSection = currentSection + 1,
							nextSectionHref = jQuery('#one-page-nav ul li:nth-child('+ nextSection +') > a').attr('href');
						
						if (nextSection <= pageSectionCount) { 
							SWIFT.page.onePageNavGoTo(nextSectionHref);
						}
						e.preventDefault();
					});
					
					// Assign current on init and scroll
					SWIFT.page.onePageNavScroll(onePageNav);
					$window.on('scroll', function() {
						SWIFT.page.onePageNavScroll(onePageNav);
					});
					
				}, 1000);
			}
		},
		onePageNavGoTo: function(anchor) {
			
			var adjustment = 0;
			
			if (jQuery('#wpadminbar').length > 0) {
				adjustment = jQuery('#wpadminbar').height();
			}
			
			if (body.hasClass('sticky-header-enabled')) {
				adjustment = jQuery('.sticky-header').outerHeight() > 0 ? adjustment + jQuery('.sticky-header').outerHeight() : adjustment + jQuery('#header-section').outerHeight();
			}
			
			if (jQuery('.sticky-top-bar').length > 0) {
				adjustment += jQuery('.sticky-top-bar').height() > 0 ? adjustment + jQuery('.sticky-top-bar').height() : adjustment + jQuery('#top-bar').height();
			}
			
			SWIFT.isScrolling = true;
			
			jQuery('html, body').stop().animate({
				scrollTop: jQuery(anchor).offset().top - adjustment + 1
			}, 1000, 'easeInOutExpo', function() {
				SWIFT.isScrolling = false;
			});
		},
		onePageNavScroll: function(onePageNav) {
			
			var adjustment = 0;
			
			if (body.hasClass('sticky-header-enabled')) {
				adjustment = jQuery('.sticky-header').height() > 0 ? adjustment + jQuery('.sticky-header').height() : adjustment + jQuery('#header-section').height();
			}
			
			if (jQuery('#wpadminbar').length > 0) {
				adjustment = adjustment + jQuery('#wpadminbar').height();
			}
			
			if (jQuery('.sticky-top-bar').length > 0) {
				adjustment += jQuery('.sticky-top-bar').height() > 0 ? adjustment + jQuery('.sticky-top-bar').height() : adjustment + jQuery('#top-bar').height();
			}
			
			var currentSection = jQuery('section.row:in-viewport('+adjustment+')').data('rowname'),
				currentSectionIndex = 0;
			
			if (!currentSection) {
				onePageNav.find('li').removeClass('selected');	
			}
				
			if (onePageNav.is(':visible') && currentSection) {
				onePageNav.find('li').removeClass('selected');				
				onePageNav.find('li a[data-title="'+currentSection+'"]').parent().addClass('selected');
				currentSectionIndex = onePageNav.find('li a[data-title="'+currentSection+'"]').parent().index() + 1;
			}
			
			if (currentSectionIndex > 0) {
				jQuery('.opn-status .current').text(currentSectionIndex);
			}
			
			if (onePageNav.hasClass('opn-arrows')) {
				var current = onePageNav.find('.current').text(),
					total = onePageNav.find('.total').text();
				
				if (current === "1") {
					onePageNav.find('.opn-up').addClass('disabled');
				} else {
					onePageNav.find('.opn-up').removeClass('disabled');
				}
				
				if (current === total) {
					onePageNav.find('.opn-down').addClass('disabled');
				} else {
					onePageNav.find('.opn-down').removeClass('disabled');
				}
			}
		},
		articleShare: function() {
			jQuery('.article-share').each(function() {
				var articleShare = jQuery(this),
					image = articleShare.data('image'),
					permalink = window.location.href,
					title = encodeURIComponent(jQuery(document).find("title").text()),
					links = '';
					
				links += '<li class="pinterest enabled" data-network="pinterest"><a href="http://pinterest.com/pin/create/button/?url=' + permalink + '&media=' + image + '&description=' + title + '" onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=690,width=750\');return false;"></a></li>';
				links += '<li class="twitter enabled" data-network="twitter"><a href="http://twitter.com/share?text=' + title + '&url=' + permalink + '" onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=440,width=660\');return false;"></a></li>';
				links += '<li class="facebook enabled" data-network="facebook"><a href="https://www.facebook.com/sharer/sharer.php?u=' + permalink + '" onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=440,width=660\');return false;"></a></li>';
				if ( isMobile ) {
				links += '<li class="whatsapp enabled" data-network="whatsapp"><a href="whatsapp://send?text=' + title + ' ' + permalink + '"></a></li>';
				}
				links += '<li class="googlePlus enabled" data-network="googlePlus"><a href="https://plus.google.com/share?url=' + permalink + '" onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=440,width=660\');return false;"></a></li>';
				//links += '<li class="reddit enabled" data-network="reddit"><a onclick="return false"></a></li>';
				links += '<li class="linkedin enabled" data-network="linkedin"><a href="https://www.linkedin.com/shareArticle?mini=true&url=' + permalink + '&title=' + title + '" onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=690,width=750\');return false;"></a></li>';
				links += '<li class="email enabled" data-network="email"><a href="mailto:?subject=left%20sidebar%20test%20-%20Cardinal&amp;body=' + permalink + '"></a></li>';
				
				articleShare.find('share-button').append('<span>' + articleShare.data('buttontext') + '</span>');
				articleShare.find('share-button').append('<div class="sb-social sb-top sb-center"><ul>' + links + '</ul></div>');
				
				articleShare.find('share-button').on('click', function() {
					jQuery(this).find('.sb-social').toggleClass('active');
				});
			});
		},
		articleNavigation: function() {
			var postPagination = jQuery('.post-pagination-wrap'),
				postNext = postPagination.find('.next-article > h2 > a').attr('href'),
				postPrev = postPagination.find('.prev-article > h2 > a').attr('href');
			
			jQuery('article.type-post').hammer().on("swipeleft", function(event) {
			    if (postPrev) {
			    	window.location = postPrev;
			    }
			});
			
			jQuery('article.type-post').hammer().on("swiperight", function(event) {
			    if (postNext) {
			    	window.location = postNext;
			    }
			});
			
		},
		postMediaTitle: function() {
			var detailsOverlay = jQuery('.details-overlay'),
				detailFeature = jQuery('.detail-feature'),
				featureHeight = detailsOverlay.height() + 80;
			
			if (body.hasClass('header-naked-light') || body.hasClass('header-naked-dark')) {
				detailFeature.css('padding-top', jQuery('.header-wrap').height() * 2);
			} 
				
			detailFeature.css('height', featureHeight);
			setTimeout(function() {
				jQuery('.details-overlay').vCenter().stop().animate({
					'bottom': '50%',
					'opacity': 1
				}, 1500, "easeOutExpo");	
			}, 500);
			
			$window.smartresize( function() {	
				detailFeature.css('height', detailsOverlay.height() + 80);
				jQuery('.details-overlay').vCenter();
			});
			
		},
		lightbox: function() {	
		
			// Lightbox Social
			var lightboxSocial = {};
			if (lightboxSharing) {
				lightboxSocial = {
					facebook: {
						source: 'https://www.facebook.com/sharer/sharer.php?u={URL}',
						text: 'Share on Facebook'
					},
					twitter: true,
					googleplus: true,
					pinterest: {
						source: "https://pinterest.com/pin/create/bookmarklet/?url={URL}",
						text: "Share on Pinterest"
					}
				};
			}
					
			// Lightbox Galleries
			var galleryArr = [];
			jQuery('[data-rel^="ilightbox["]').each(function () {
				var attr = this.getAttribute("data-rel");
				if (jQuery.inArray(attr, galleryArr) == -1 ) {
					galleryArr.push(attr);
				}
			});
			jQuery.each(galleryArr, function (b, c) {
				jQuery('[data-rel="' + c + '"]').iLightBox({
					skin: lightboxSkin,
					social: {
						buttons: lightboxSocial
					},
					path: 'horizontal',
					thumbnails: {
						maxWidth: 120,
						maxHeight: 120
					},
					controls: {
						arrows: lightboxControlArrows,
						thumbnail: lightboxThumbs
					}
				});
			});
		},
		backToTop: function() {
			var scrollPosition = $window.scrollTop();
						
			if (scrollPosition > 300) {
				jQuery('#back-to-top').stop().animate({
					'bottom': '10px',
					'opacity': 1
				}, 300, "easeOutQuart");
			} else if (scrollPosition < 300) {
				jQuery('#back-to-top').stop().animate({
					'bottom': '-60px',
					'opacity': 0
				}, 300, "easeInQuart");
			}
		},
		fwMediaNextPrevAdjust: function() {
			
			var navTop = 0;
			
			if (body.hasClass('single-portfolio')) {
				navTop = jQuery('article.portfolio').offset().top + jQuery('figure.fw-media-wrap').height() + 80;
			} else if (body.hasClass('single-post')) {
				if (jQuery('.detail-feature').length > 0) {
					navTop = jQuery('article.post').offset().top + jQuery('.detail-feature').height() + 80;
				} else {
					navTop = jQuery('article.post').offset().top + jQuery('figure.fw-media-wrap').height() + 80;
				}
			}
			
			jQuery('#prev-article-pagination').addClass('has-fw-media').css('top', navTop);
			jQuery('#next-article-pagination').addClass('has-fw-media').css('top', navTop);
		},
		fsNextPrev: function() {
			var scrollPosition = $window.scrollTop(),
				absTop = 0,
				showArrowX = body.hasClass('single-product') ? 60 : 100;
				
			if ((body.hasClass('single-portfolio') && jQuery('article.portfolio').hasClass('single-portfolio-fw-media')) || (body.hasClass('single-post') && (jQuery('article.type-post').hasClass('single-post-fw-media-title') || jQuery('article.type-post').hasClass('single-post-fw-media') ))) {
				
				if (body.hasClass('single-portfolio')) {
					absTop = jQuery('article.portfolio').offset().top + jQuery('figure.fw-media-wrap').height() + 200;
				} else if (body.hasClass('single-post')) {
					if (jQuery('.detail-feature').length > 0) {
						absTop = jQuery('article.post').offset().top + jQuery('.detail-feature').height() + 200;
					} else {
						absTop = jQuery('article.post').offset().top + jQuery('figure.fw-media-wrap').height() + 200;
					}
				}
										
				if ((scrollPosition + $window.height() / 2) > absTop) {
					jQuery('#prev-article-pagination').addClass('fs-nav-fixed');
					jQuery('#next-article-pagination').addClass('fs-nav-fixed');
				} else if (scrollPosition < absTop) {
					jQuery('#prev-article-pagination').removeClass('fs-nav-fixed');
					jQuery('#next-article-pagination').removeClass('fs-nav-fixed');
				}
			} else {	
				if (scrollPosition > showArrowX) {
					jQuery('#prev-article-pagination').stop().animate({
						'left': '0',
						'opacity': 1
					}, 300, "easeOutQuart");
					jQuery('#next-article-pagination').stop().animate({
						'right': '0',
						'opacity': 1
					}, 300, "easeOutQuart");
				} else if (scrollPosition < showArrowX) {
					jQuery('#prev-article-pagination').stop().animate({
						'left': '-80px',
						'opacity': 1
					}, 300, "easeOutQuart");
					jQuery('#next-article-pagination').stop().animate({
						'right': '-80px',
						'opacity': 1
					}, 300, "easeOutQuart");
				}
			}
		},
		stickySidebars: function() {
			
			var topMargin = 10;
			
			if (jQuery('.sticky-header').length > 0) {
				topMargin += jQuery('.sticky-header').height();
			}

			if (jQuery('#breadcrumbs').length > 0) {
				topMargin += jQuery('#breadcrumbs').height();
			}
			
			jQuery('.sidebar > .sidebar-widget-wrap').stickySidebar({
				headerSelector: '.header-wrap',
				navSelector: '.page-heading',
				contentSelector: '.hentry',
				footerSelector: '#footer-wrap',
				sidebarTopMargin: topMargin,
				footerThreshold: 160,
			});
			
			SWIFT.page.stickySidebarsResize();
			$window.smartresize( function() {
				SWIFT.page.stickySidebarsResize();
			});
		},
		stickySidebarsResize: function() {
			jQuery('.sidebar-widget-wrap').each(function() {
				var sidebar = jQuery(this);
					sidebar.css('width', sidebar.parent().width());
				});
		},
		getViewportHeight: function() {
			var height = "innerHeight" in window ? window.innerHeight: document.documentElement.offsetHeight; 
			return height;		
		},
		checkIE: function() {
			var undef,
				v = 3,
				div = document.createElement('div'),
				all = div.getElementsByTagName('i');
				
			while (
				div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',
				all[0]
			);
			
			return v > 4 ? v : undef;
		},
		pageTransitions: function() {
			jQuery('a').on('click', function(e) {
				var linkElement = jQuery(this),
					link = linkElement.attr('href'),
					linkTarget = linkElement.attr('target');
					
				if (linkTarget === '_blank' || link.indexOf('?') >= 0 || link.indexOf('#') >= 0 || link.indexOf('.jpg') >= 0 || link.indexOf('.png') >= 0 || link.indexOf('mailto') >= 0 || e.ctrlKey || e.metaKey || link.indexOf('javascript') === 0 ) {
					return e;
				} else {					
					if (body.hasClass('mobile-menu-open')) {
						return;
					} else {
						SWIFT.page.fadePageOut(link);
						e.preventDefault();
					}
				}
			});
		},
		fadePageIn: function() {
			var preloadTime = 400;
			
			if (jQuery('.parallax-window-height').length > 0) {
				preloadTime = 800;
			}
			
			body.addClass('page-fading-in');
			jQuery('#site-loading').animate({
				'opacity': 0,
			}, preloadTime, 'easeInExpo', function() {
				jQuery(this).css('display', 'none');
			});
			setTimeout(function() {
				body.removeClass('page-fading-in');
			}, 500);
		},
		fadePageOut: function(link) {
			jQuery('#site-loading').css('display', 'block').animate({
				'opacity': 1,
			}, 400, 'easeInExpo');
			jQuery('#main-container').animate({
				'opacity': 0,
			}, 500, 'easeInExpo', function() {
				window.location = link;
			});
		},
		mobileThumbLinkClick: function() {
			jQuery(document).on('click', '.animated-overlay > a', function(e) {
				var thisLink = jQuery(this);

				if (thisLink.hasClass('hovered')) {
					return e;
				} else {
					e.preventDefault();
					thisLink.addClass('hovered');
				}
			});
		},
		directorySubmit: function() {
			jQuery('#sf_directory_calculate_coordinates').live("click", function(e) {
				e.preventDefault();
				var geocoder = new google.maps.Geocoder();	
						geocoder.geocode( { 'address': jQuery('#sf_directory_address').val()}, function(results, status) {
									jQuery('#sf_directory_lat_coord').val(results[0].geometry.location.lat());
									jQuery('#sf_directory_lng_coord').val(results[0].geometry.location.lng());	
					});
								
			});
			
			jQuery('#directory-submit').click(function(e) {
			
			if (jQuery('#sf_directory_address').val() === '' || jQuery('#sf_directory_lat_coord').val() === '' || jQuery('sf_directory_lng_coord').val() === '' || jQuery('#directory_title').val() === '' || jQuery('#directory_description').val() === '' || jQuery('#directory-cat').val() <= 0 || jQuery('#directory-loc').val() <= 0) {
					e.preventDefault();
					jQuery('.directory-error').show();
					jQuery('html, body').animate({ scrollTop: jQuery('.directory-error').offset().top-100}, 700);
					return false;
				}
				jQuery('#add-directory-entry').submit();
			});
		}
	};

	
	/////////////////////////////////////////////
	// SUPER SEARCH
	/////////////////////////////////////////////
		
	SWIFT.superSearch = {
		init: function() {
						
			jQuery('.search-options .ss-dropdown').on('click', function(e) {
				e.preventDefault();
				
				var option = jQuery(this),
					dropdown = option.find( 'ul' );
				
				jQuery('.ss-dropdown ul').removeClass('show-dropdown');
					
				if (isMobileAlt) {
					if (dropdown.hasClass('show-dropdown')) {
						dropdown.removeClass('show-dropdown');
					} else {
						dropdown.addClass('show-dropdown');							
					}
				} else {
					if (dropdown.hasClass('show-dropdown')) {
						dropdown.css('top', 30).removeClass('show-dropdown');
					} else {
						dropdown.css('top', -10).addClass('show-dropdown');							
					}
				}
			});
						
			jQuery('.ss-option').on('click', function(e) {
				e.preventDefault();
				
				var thisOption = jQuery(this),
					selectedOption = thisOption.attr('data-attr_value'),
					parentOption = thisOption.parent().parent().parent();
								
				parentOption.find('li').removeClass('selected');
				thisOption.parent().addClass('selected');
				
				parentOption.attr('data-attr_value', selectedOption);
				parentOption.find('span').text(thisOption.text());
				
				setTimeout(function() {
					thisOption.parents('ul').first().css('top', 30).removeClass('show-dropdown');
				}, 100);
			});
			
			jQuery('.super-search-go').on('click', function(e) {
				e.preventDefault();
				var parentSearch = jQuery(this).parents('.sf-super-search'),
					filterURL = SWIFT.superSearch.urlBuilder(parentSearch),
					homeURL = jQuery(this).attr('data-home_url'),
					shopURL = jQuery(this).attr('data-shop_url');
				
				if (filterURL.indexOf("product_cat") >= 0) {
				location.href = homeURL + filterURL;
				} else {
				location.href = shopURL + filterURL;
				}
				
			});
		},
		urlBuilder: function(searchInstance) {
			
			var queryString = "";
			
			jQuery(searchInstance).find('.search-options .ss-dropdown').each(function() {
				
				var attr = jQuery(this).attr('id');
				var attrValue = jQuery(this).attr('data-attr_value');
				if (attrValue !== "") {
					if (attr === "product_cat") {
						if (queryString === "") {
							queryString += "?product_cat=" + attrValue;
						} else {
							queryString += "&product_cat=" + attrValue;
						}
					} else {
						if (queryString === "") {
						queryString += "?filter_" + attr + "=" + attrValue;				
						} else {
						queryString += "&filter_" + attr + "=" + attrValue;									
						}
					}
				}
			});
			
			jQuery('.search-options input').each(function() {
				var attr = jQuery(this).attr('name');
				var attrValue = jQuery(this).attr('value');
				if (queryString === "") {
					queryString += "?"+ attr + "=" + attrValue;				
				} else {
					queryString += "&" + attr + "=" + attrValue;									
				}
			});
			
			return queryString;
		}
	};
	
	
	/////////////////////////////////////////////
	// HEADER
	/////////////////////////////////////////////
		
	SWIFT.header = {
		init: function() {
			
			// INIT VARIABLES
			var lastAjaxSearchValue = "",
				searchTimer = false;
			
								
			// STICKY HEADER		
			if (body.hasClass('sticky-header-enabled') && !body.hasClass('vertical-header')) {
				SWIFT.header.stickyHeaderInit();
			}
			
			// STICKY TOP BAR
			if ( jQuery('.sticky-top-bar').length > 0 ) {
				SWIFT.header.stickyTopBarInit();
			}
			
			// FULLSCREEN SEARCH
			if (jQuery('#fullscreen-search').length > 0) {
				SWIFT.header.fsSearch();
			}
			
			jQuery('a.fs-header-search-link,a.fs-overlay-close').on('click', function(e) {
				e.preventDefault();
				
				if (body.hasClass('overlay-menu-open')) {
					SWIFT.header.overlayMenuToggle();
				}
				if (body.hasClass('fs-supersearch-open')) {
					SWIFT.header.fsSuperSearchToggle();
				}
				
				SWIFT.header.fsSearchToggle();
			});
			
			
			// FULLSCREEN SUPERSEARCH
			jQuery('a.fs-supersearch-link').on('click', function(e) {
				e.preventDefault();
				
				if (body.hasClass('overlay-menu-open')) {
					SWIFT.header.overlayMenuToggle();
				}
				if (body.hasClass('fs-search-open')) {
					SWIFT.header.fsSearchToggle();
				}
				
				SWIFT.header.fsSuperSearchToggle();
			});
			
			
			// OVERLAY MENU
			jQuery('a.overlay-menu-link').on('click', function(e) {
				e.preventDefault();
				SWIFT.header.overlayMenuToggle();
				
				if (body.hasClass('fs-search-open')) {
					SWIFT.header.fsSearchToggle();
				}
				if (body.hasClass('fs-supersearch-open')) {
					SWIFT.header.fsSuperSearchToggle();
				}
			});
			
			jQuery('#overlay-menu li.menu-item a').on('click', function() {
				SWIFT.header.overlayMenuToggle();
			});
			
			
			// AJAX SEARCH
			jQuery('.header-search-link-alt').on('click', function(e) {
				e.preventDefault();
				
				var ajaxSearchWrap = jQuery('.ajax-search-wrap');
				
				if (ajaxSearchWrap.is(':visible')) {
					ajaxSearchWrap.fadeOut(300);
					setTimeout(function() {
						jQuery('.ajax-search-results').slideUp(100).empty();
						jQuery('.ajax-search-form input[name=s]').val('');
					}, 300);
				} else {
					ajaxSearchWrap.fadeIn(300);
					setTimeout(function() {
						jQuery('.ajax-search-form input[name=s]').focus();
						jQuery("#container").bind("click", function(e) {
							var ajaxSearchWrap = jQuery('.ajax-search-wrap');
							if (!jQuery(e.target).closest('.ajax-search-wrap').length) {
								ajaxSearchWrap.fadeOut(300);
								setTimeout(function() {
									jQuery('.ajax-search-results').slideUp(100).empty();
									jQuery('.ajax-search-form input[name=s]').val('');
								}, 300);
								jQuery("#container").unbind("click");
							}
						});
					}, 300);
				}
				
			});
			
			
			// AJAX SEARCH INPUT FUNCTION
			jQuery('.ajax-search-form input[name=s],#fs-search-input').on('keyup', function(e) {
				var searchvalue = e.currentTarget.value,
					homeURL = jQuery('.ajax-search-form').attr('action');

				clearTimeout(searchTimer);						
				if (lastAjaxSearchValue != jQuery.trim(searchvalue) && searchvalue.length >= 3) {
					searchTimer = setTimeout( function() {
						if (jQuery('#fullscreen-search').length > 0 && history.pushState) {
							jQuery('#fullscreen-search').find('.search-wrap').animate({
								'margin-top': '10%'
							}, 200);
						    var urlWithSearchParam = homeURL + '?s=' + searchvalue;
						    window.history.pushState({path:urlWithSearchParam}, '', urlWithSearchParam);
						}
						SWIFT.header.ajaxSearch(e);
					}, 400);
				}
			});
			
			
			// CONTACT SLIDEOUT
			jQuery('a.contact-menu-link').on('click', function(e) {
				e.preventDefault();
				var headerTop = jQuery('.header-wrap').scrollTop();
				if (headerTop !== 0) {
					jQuery('body,html').animate({scrollTop: headerTop}, 600, 'easeOutCubic');
					setTimeout(function() {
						jQuery('#contact-slideout').slideToggle(800, 'easeInOutExpo');
						if (jQuery(this).hasClass('slide-open')) {
							jQuery(this).removeClass('slide-open');
						} else {
							SWIFT.map.init();
							jQuery(this).addClass('slide-open');
						}
					}, 800);
				} else {
					jQuery('#contact-slideout').slideToggle(800, 'easeInOutExpo');
					if (jQuery(this).hasClass('slide-open')) {
						jQuery(this).removeClass('slide-open');
					} else {
						SWIFT.map.init();
						jQuery(this).addClass('slide-open');
					}
				}
			});	
			
			// Mobile Sticky Header
			if (body.hasClass('mh-sticky')) {
				SWIFT.header.mobileStickyHeader();
			}
			
			// VERTICAL HEADER RIGHT POSITIONING
			if (body.hasClass('layout-boxed') && body.hasClass('vertical-header-right')) {
				
				var rightOffset = ($window.width() - jQuery('#container').width()) / 2;
				jQuery('.header-wrap').css('right', rightOffset);
				
				$window.smartresize( function() {
					var rightOffset = ($window.width() - jQuery('#container').width()) / 2;
					jQuery('.header-wrap').css('right', rightOffset);
				});
			}
		},
		mobileStickyHeader: function() {
			// MOBILE STICKY HEADER		
			var mobileHeader = jQuery('#mobile-header');
			
			mobileHeader.sticky({
				topSpacing: 0
			});
			
			jQuery('#mobile-header-sticky-wrapper').css('height', mobileHeader.outerHeight(true));
			
			$window.smartresize( function() {
				mobileHeader.sticky('update');
				jQuery('#mobile-header-sticky-wrapper').css('height', mobileHeader.outerHeight(true));
			});
		},
		stickyHeaderInit: function() {
			var spacing = 0,
				stickyHeader = jQuery('.sticky-header'),
				headerWrap = jQuery('.header-wrap');
			
			if (jQuery('#wpadminbar').length > 0) {
				spacing = 32;
			}
			
			if ( jQuery('.sticky-top-bar').length > 0 ) {
				spacing += jQuery('.sticky-top-bar').outerHeight();
			}
			
			stickyHeader.sticky({
				topSpacing: spacing
			});
			
			$window.smartresize( function() {
				stickyHeader.sticky('update');
			});
			
			if (body.hasClass('layout-boxed')) {
				jQuery('.sticky-header').css('max-width', headerWrap.width());
				$window.smartresize( function() {
					jQuery('.sticky-header').css('max-width', headerWrap.width());
				});
			}
			
			// Sticky Header Resizing
			if (body.hasClass('sh-dynamic')) {
				var defaultHeaderPos = headerWrap.offset().top;
				$window.scroll(function () {
					defaultHeaderPos = headerWrap.offset().top - $window.scrollTop();
					if (jQuery('.sticky-wrapper').hasClass('is-sticky') && defaultHeaderPos < -160) {
						headerWrap.addClass('resized-header');
					} else if (headerWrap.hasClass('resized-header')) {
						headerWrap.removeClass('resized-header');
					}
					
				});
			}
		},
		stickyTopBarInit: function() {
			var spacing = 0,
				stickyTB = jQuery('.sticky-top-bar'),
				headerWrap = jQuery('.header-wrap');

			if (jQuery('#wpadminbar').length > 0) {
				spacing = 32;
			}

			stickyTB.sticky({
				topSpacing: spacing
			});

			$window.smartresize( function() {
				stickyTB.sticky('update');
			});

			if (body.hasClass('layout-boxed')) {
				stickyTB.css('max-width', headerWrap.width());
				$window.smartresize( function() {
					stickyTB.css('max-width', headerWrap.width());
				});
			}
		},
		ajaxSearch: function(e) {			
			var searchInput = jQuery(e.currentTarget),
				searchValues = searchInput.parents('form').serialize() + '&action=sf_ajaxsearch',
				results = jQuery('.ajax-search-results'),
				loadingIndicator = jQuery('.search-wrap .ajax-loading'),
				ajaxurl = jQuery('.search-wrap').data('ajaxurl');
						
			jQuery.ajax({
				url: ajaxurl,
				type: "POST",
				data: searchValues,
				beforeSend: function() {
					jQuery('.ajax-search-results').slideUp(200);
					setTimeout(function() {
						loadingIndicator.fadeIn(50);					
					}, 150);				
				},
				success: function(response) {
					if (response === 0) {
						response = "";
					} else {
						results.html(response);
					}
				},
				complete: function() {
					loadingIndicator.fadeOut(200);
					setTimeout(function() {
						results.slideDown(400);
						if (jQuery('#fullscreen-search').length > 0) {
							results.find('.search-result').each(function(i) {
								var result = jQuery(this);
								setTimeout(function() {
									result.addClass('load-in');
								}, 200*i);
							});
						}
					}, 200);			
				}
			});
		},
		fsSearch: function() {
			var fsSearch = jQuery('#fullscreen-search'),
				searchInput = fsSearch.find('#fs-search-input');
				
			searchInput.autoGrowInput();
		},
		fsSearchToggle: function() {
			var fsSearch = jQuery('#fullscreen-search'),
				searchInput = fsSearch.find('#fs-search-input');

			if (body.hasClass('fs-search-open')) {
				body.removeClass('fs-aux-open');
				body.removeClass('fs-search-open');
				body.addClass('fs-search-closing');
				setTimeout(function() {
					if (!body.hasClass('overlay-menu-open')) {
						jQuery('#main-nav,#main-navigation').fadeIn(400);
					}
				}, 200);
			} else {
				setTimeout(function() {
					body.removeClass('fs-search-closing');
					body.addClass('fs-search-open');
					body.addClass('fs-aux-open');
					searchInput.focus();
					jQuery('#main-nav,#main-navigation').fadeOut(300);
				}, 30);
			}
		},
		fsSuperSearchToggle: function() {
			var fsSuperSearch = jQuery('#fullscreen-supersearch');

			if (body.hasClass('fs-supersearch-open')) {
				body.removeClass('fs-supersearch-open');
				body.removeClass('fs-aux-open');
				body.addClass('fs-supersearch-closing');
				setTimeout(function() {
					jQuery('#main-nav,#main-navigation').fadeIn(400);
				}, 200);
			} else {
				setTimeout(function() {
					body.removeClass('fs-supersearch-closing');
					body.addClass('fs-supersearch-open');
					body.addClass('fs-aux-open');
					jQuery('#main-nav,#main-navigation').fadeOut(300);
				}, 30);
			}
		},
		overlayMenuToggle: function() {
			var overlayMenu = jQuery('#overlay-menu'),
				overlayMenuItemHeight = Math.floor(overlayMenu.find('nav ul').height() / overlayMenu.find('ul.menu > li').length) - 5,
				overlayMenuItemFS = Math.floor(overlayMenuItemHeight * 0.5);
			
			if (overlayMenuItemFS > 60) {
				overlayMenuItemFS = 60;
			}
			
			overlayMenu.find('li').css('height', overlayMenuItemHeight + 'px').css('font-size', overlayMenuItemFS + 'px').css('line-height', overlayMenuItemHeight - 20 + 'px');
			
			if (body.hasClass('overlay-menu-open')) {
				body.removeClass('overlay-menu-open');
				body.removeClass('fs-aux-open');
				body.addClass('overlay-menu-closing');
				setTimeout(function() {
					if (!body.hasClass('fs-search-open')) {
						jQuery('#main-nav,#main-navigation').fadeIn(400);
					}
					body.removeClass('overlay-menu-closing');
				}, 200);
			} else {
				setTimeout(function() {
					body.removeClass('overlay-menu-closing');
					body.addClass('overlay-menu-open');
					body.addClass('fs-aux-open');
					jQuery('#main-nav,#main-navigation').fadeOut(300);
				}, 30);
			}
		}
	};
	
	
	/////////////////////////////////////////////
	// NAVIGATION
	/////////////////////////////////////////////
	
	SWIFT.nav = {
		init: function() {
			
			// Set up Mega Menu
			if (!isMobile || $window.width() > 768) {
				SWIFT.nav.mainMenu();
			}
			
			// Set up Mobile Menu
			SWIFT.nav.mobileMenuInit();
			
			// Add main menu actions
			SWIFT.nav.mainMenuActions();

		},
		mainMenu: function() {
						
			jQuery("#main-navigation").find(".menu li.menu-item").hoverIntent({
				over: function() {
					if (jQuery('#container').width() > 767 || body.hasClass('responsive-fixed')) {
						jQuery(this).find('ul.sub-menu,.mega-menu-sub').first().fadeIn(200);
					}
				},
				out:function() {
					if (jQuery('#container').width() > 767 || body.hasClass('responsive-fixed')) {
						jQuery(this).find('ul.sub-menu,.mega-menu-sub').first().fadeOut(150);
					}
				},
				timeout: 0
			});
			
			// Set sub-menu position based on menu height
			var mainNav = jQuery('#main-navigation'),
				mainNavHeight = mainNav.height(),
				subMenu = mainNav.find('.sub-menu');
			
			subMenu.each(function() {
				jQuery(this).css('top', mainNavHeight);
			});
		},
		mainMenuActions: function() {
			// Add parent class to items with sub-menus
			jQuery("ul.sub-menu").parents('li').addClass('parent');
			
			// Menu parent click function
			jQuery('.menu li.parent > a').on('click', function(e) {
			
				if (jQuery('#container').width() < 1024 || body.hasClass('standard-browser')) {
					return e;
				}
				
				var directDropdown = jQuery(this).parent().find('ul.sub-menu').first();
				
				if (directDropdown.css('opacity') === '1' || directDropdown.css('opacity') === 1) {
					return e;
				} else {
					e.preventDefault();
				}
			});
			
			// Set Standard Sub Menu Top Position
			jQuery("nav.std-menu").find(".menu li").each(function() {
				var top = jQuery(this).outerHeight();
				jQuery(this).find('ul.sub-menu').first().css('top', top);
			});
			
			// Enable hover dropdowns for window size above tablet width
			jQuery("nav.std-menu").find(".menu li.parent").hoverIntent({
				over: function() {
					if (jQuery('#container').width() > 767 || body.hasClass('responsive-fixed')) {
						jQuery(this).find('ul.sub-menu').first().stop( true, true ).fadeIn(200);
					}
				},
				out:function() {
					if (jQuery('#container').width() > 767 || body.hasClass('responsive-fixed')) {
						jQuery(this).find('.sub-menu').first().stop( true, true ).fadeOut(150);
					}
				},
				timeout: 100
			});
			
			
			// Shopping bag hover function
			jQuery(document).on("mouseenter", "li.shopping-bag-item", function() {
				if (jQuery('#container').width() > 767 || body.hasClass('responsive-fixed')) {
					jQuery(this).find('ul.sub-menu').first().stop( true, true ).fadeIn(200);
				}
			}).on("mouseleave", "li.shopping-bag-item", function() {
				if (jQuery('#container').width() > 767 || body.hasClass('responsive-fixed')) {
					jQuery(this).find('ul.sub-menu').first().stop( true, true ).fadeOut(150);
				}
			});
			
			
			// Menu item click function
			jQuery('.menu-item').on('click', 'a', function(e) {
				
				var menuItem = jQuery(this),
					linkHref = menuItem.attr('href'),
					isMobileMenuItem = false,
					youtubeURL = linkHref.match(/watch\?v=([a-zA-Z0-9\-_]+)/),
					vimeoURL = linkHref.match(/^http:\/\/(www\.)?vimeo\.com\/(clip\:)?(\d+).*$/);
				
				
				if (jQuery(this).parents('nav').attr('id') === "mobile-menu") {
					isMobileMenuItem = true;
				}
				
				// Link to full width video overlay if link is YouTube/Vimeo
				if (youtubeURL || vimeoURL) {
					
					var videoURL = "";
					
					if (youtubeURL) {
						videoURL = 'http://www.youtube.com/embed/'+ youtubeURL[1] +'?autoplay=1&amp;wmode=transparent';
					} else if (vimeoURL) {
						videoURL = 'https://player.vimeo.com/video/'+ vimeoURL[3] +'?title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1&amp;wmode=transparent';
					}
					
					if (videoURL !== "") {
						jQuery(this).data('video', videoURL);
						SWIFT.widgets.openFullWidthVideo(jQuery(this));
					}
					e.preventDefault();		
				// Smooth Scroll
				} else if (linkHref.indexOf('#') === 0 && linkHref.length > 1) {
					var headerHeight = 0;
					
					if (isMobileMenuItem) {
						SWIFT.nav.mobileMenuHideTrigger();
						setTimeout(function() {
							if (body.hasClass('mh-sticky')) {
								headerHeight = jQuery('#mobile-header').height();
							}
							if (jQuery('#mobile-top-text-sticky-wrapper').length > 0 ) {
								headerHeight += jQuery('#mobile-top-text-sticky-wrapper').height();
							}
							if (jQuery('#wpadminbar').length > 0) {
								headerHeight += jQuery('#wpadminbar').height();
							}
							if ( jQuery('.sticky-top-bar').length > 0 ) {
								headerHeight += jQuery('.sticky-top-bar').outerHeight();
							}
												
							if (jQuery(linkHref).length > 0) {
								jQuery('html, body').stop().animate({
									scrollTop: jQuery(linkHref).offset().top - headerHeight + 2
								}, 1000, 'easeInOutExpo');
							}
						}, 400);
					} else {											
						if (jQuery(linkHref).length > 0) {
							SWIFT.isScrolling = true;
							SWIFT.page.onePageNavGoTo(linkHref);
							setTimeout(function() {
								SWIFT.isScrolling = false;
							}, 1000);
						}
					}
					e.preventDefault();
				} else {
					if (isMobileMenuItem) {
						return;
					} else {
						return e;
					}
				}
				
			});
			
			
			// Set current language to top bar item
			var currentLanguage = jQuery('li.aux-languages').find('.current-language').html();
			if (currentLanguage !== "") {
				jQuery('li.aux-languages > a').html(currentLanguage);
			}
			
			// Set menu state on resize
			$window.smartresize( function() {  
				if (jQuery('#container').width() > 767 || body.hasClass('responsive-fixed')) {
					var menus = jQuery('nav').find('ul.menu');
					menus.each(function() {
						jQuery(this).css("display", "");
					});
				}
			});
			
			// Change menu active when scroll through sections
			SWIFT.nav.currentScrollIndication();
			$window.scroll(function () {
				SWIFT.nav.currentScrollIndication();
			});
		},
		currentScrollIndication: function() {
			var adjustment = 0;
	
			if (body.hasClass('sticky-header-enabled')) {
				adjustment = jQuery('.header-wrap').height();
			}
	
			if ( jQuery('.sticky-top-bar').length > 0 ) {
				adjustment += jQuery('.sticky-top-bar').outerHeight();
			}
	
			var inview = jQuery('section.row:in-viewport('+adjustment+')').attr('id'),
				menuItems = jQuery('#main-navigation .menu li a'),
				link;
				
			if ( inview !== "" && typeof inview != 'undefined' ) {
				link = menuItems.filter('[href="#' + inview + '"]');
			}
			
			menuItems.parent().removeClass('current-scroll-item');
	
			if (typeof inview != 'undefined' && link.length > 0 && !link.hasClass('.current-scroll-item')) {
				menuItems.parent().removeClass('current-scroll-item');
				link.parent().addClass('current-scroll-item');
			}
			
		},
		mobileMenuInit: function() {
			
			// Check if admin bar present
			if (jQuery('#wpadminbar').length > 0) {
				var topPadding = 54;
				
				if (body.hasClass('mh-overlay')) {
					jQuery('.mobile-overlay-close').css('top', topPadding + 'px');
					topPadding = topPadding + 40;
				}
				
				jQuery('#mobile-menu-wrap').css('padding-top', topPadding + 'px');
				jQuery('#mobile-cart-wrap').css('padding-top', topPadding + 'px');
			}
			
			// Mobile Logo click	
			jQuery('#mobile-logo > a').on('click touchstart', function(e) {
				if (body.hasClass('mobile-menu-open') || body.hasClass('mobile-cart-open') || body.hasClass('mobile-menu-closing')) {
					return false;
				} else {
					return e;
				}
			});
			
			// Mobile Nav show		
			jQuery('.mobile-menu-link').on('click', function(e) {
				e.preventDefault();
				
				if (body.hasClass('mh-overlay')) {
					SWIFT.nav.mobileHeaderOverlay('menu');
				} else {
					if (body.hasClass('mobile-menu-open')) {
						if (isAndroid) {
							return;
						}
						SWIFT.nav.mobileMenuHideTrigger();
					} else {
						SWIFT.nav.showMobileMenu();
					}
				}
			});
			
			// Mobile Cart show	
			jQuery('.mobile-cart-link').on('click', function(e) {
				e.preventDefault();
				
				if (body.hasClass('mh-overlay')) {
					SWIFT.nav.mobileHeaderOverlay('cart');
				} else {
					if (body.hasClass('mobile-menu-open')) {
						if (isAndroid) {
							return;
						}
						SWIFT.nav.mobileMenuHideTrigger();
					} else {
						SWIFT.nav.showMobileCart();
					}
				}
			});
			
			// Mobile Overlay Close
			jQuery('.mobile-overlay-close').on('click', function(e) {
				e.preventDefault();
				if (body.hasClass('mh-cart-show')) {
					jQuery('#mobile-cart-wrap').animate({
						'opacity': 0
					}, 500, 'easeOutQuart');
					setTimeout(function() {
						body.removeClass('mh-overlay-show');
						body.removeClass('mh-cart-show');
					}, 500);
				} else if (body.hasClass('mh-menu-show')) {
					jQuery('#mobile-menu-wrap').animate({
						'opacity': 0
					}, 500, 'easeOutQuart');
					setTimeout(function() {
						body.removeClass('mh-overlay-show');
						body.removeClass('mh-menu-show');
					}, 500);
				}
			});
			
			// Mobile Menu parent click
			jQuery('#mobile-menu li > a').on('click', function(e) {
				var parentMenuItem = jQuery(this).parent(),
					linkHref = jQuery(this).attr('href'),
					subMenu = parentMenuItem.find('ul.sub-menu'); 
				
				if (linkHref.indexOf('#') === 0 && linkHref.length > 1) {
					var headerHeight = 0;
					
					SWIFT.nav.mobileMenuHideTrigger();
					setTimeout(function() {
						if (body.hasClass('mh-sticky')) {
							headerHeight = jQuery('#mobile-header').height();
						}
						if (jQuery('#mobile-top-text-sticky-wrapper').length > 0 ) {
							headerHeight += jQuery('#mobile-top-text-sticky-wrapper').height();
						}
						if (jQuery('#wpadminbar').length > 0) {
							headerHeight += jQuery('#wpadminbar').height();
						}
											
						if (jQuery(linkHref).length > 0) {
							jQuery('html, body').stop().animate({
								scrollTop: jQuery(linkHref).offset().top - headerHeight + 2
							}, 1000, 'easeInOutExpo');
						}
					}, 400);
					
					e.preventDefault();
					
				} else {
					
					if (!parentMenuItem.hasClass('parent')) {
						SWIFT.nav.mobileMenuHideTrigger();
						
						if (body.hasClass('page-transitions')) {
							SWIFT.page.fadePageOut(linkHref);
						}
						
						return e;
					}
					
					if (subMenu.hasClass('sub-menu-open')) {
						if (linkHref.indexOf('http') === 0) {
							return e;
						} else {
							subMenu.removeClass('sub-menu-open');
							e.preventDefault();
						}
					} else {
						subMenu.addClass('sub-menu-open');
						e.preventDefault();
					}
				}
			});
			
			
			// Swipe to hide mobile menu
			jQuery("#mobile-menu-wrap").swipe({
				swipeLeft:function() {
					if (!body.hasClass('mobile-header-center-logo-alt') && !body.hasClass('mobile-header-left-logo')) {
						SWIFT.nav.mobileMenuHideTrigger();
					}
				},
				swipeRight:function() {
					if (body.hasClass('mobile-header-center-logo-alt') || body.hasClass('mobile-header-left-logo')) {
						SWIFT.nav.mobileMenuHideTrigger();
					}
				},
			});
			
			// Swipe to hide mobile cart
			jQuery("#mobile-cart-wrap").swipe( {
				swipeLeft:function() {
					if (body.hasClass('mobile-header-center-logo-alt') || body.hasClass('mobile-header-right-logo') || body.hasClass('mobile-header-center-logo')) {
						SWIFT.nav.mobileMenuHideTrigger();  
					}
				},
				swipeRight:function() {
					if (!body.hasClass('mobile-header-center-logo-alt') && !body.hasClass('mobile-header-right-logo')) {
						SWIFT.nav.mobileMenuHideTrigger();  
					}
				},
			});
			
			// Hide on resize
			$window.smartresize( function() {
				
				var windowWidth = $window.width();
				
				if (windowWidth > 1024 && body.hasClass('mhs-tablet-land')) {
					SWIFT.nav.mobileMenuHideTrigger();
				} else if (windowWidth > 991 && body.hasClass('mhs-tablet-port')) {
					SWIFT.nav.mobileMenuHideTrigger();
				} else if (windowWidth > 767 && body.hasClass('mhs-mobile')) {
					SWIFT.nav.mobileMenuHideTrigger();
				}
				
			});
			
		},
		showMobileMenu: function() {
			
			var windowTop = $window.scrollTop();			
			if (jQuery('#wpadminbar').length > 0) {
				windowTop = windowTop - jQuery('#wpadminbar').height();
			}
			if (windowTop > 0 && body.hasClass('mh-sticky')) {
				jQuery('#mobile-header').css('position', 'absolute').css('top', windowTop);
			}
			
			if (body.hasClass('header-below-slider')) {
				jQuery('#mobile-menu-wrap').css('position', 'absolute').css('top', jQuery('#container').offset().top);	
			}
			
			jQuery('#mobile-menu-wrap').css('display', 'block');
			body.addClass('mobile-menu-open');
			setTimeout(function() {
				jQuery('#container').on('click touchstart', SWIFT.nav.mobileMenuHideTrigger);
			}, 400);
		},
		hideMobileMenu: function() {
			var windowTop = $window.scrollTop();
			body.removeClass('mobile-menu-open');
			setTimeout(function() {
				jQuery('#mobile-menu-wrap').css('display', 'none');
				if (windowTop > 0 && body.hasClass('mh-sticky')) {
					jQuery('#mobile-header').css('position', 'fixed').css('top', '0');
				} else {
					jQuery('#mobile-header').css('position', '').css('top', '');
				}
				jQuery('#container').off('click touchstart', SWIFT.nav.mobileMenuHideTrigger);
			}, 300);
			setTimeout(function() {
				body.removeClass('mobile-menu-closing');
			}, 1000);
		},
		showMobileCart: function() {
			var windowTop = $window.scrollTop();
			if (jQuery('#wpadminbar').length > 0) {
				windowTop = windowTop - jQuery('#wpadminbar').height();
			}
			if (windowTop > 0 && body.hasClass('mh-sticky')) {
				jQuery('#mobile-header').css('position', 'absolute').css('top', windowTop);
			}
			
			if (body.hasClass('header-below-slider')) {
				jQuery('#mobile-cartdon-wrap').css('position', 'absolute').css('top', jQuery('#container').offset().top);	
			}
			
			jQuery('#mobile-cart-wrap').css('display', 'block');
			body.addClass('mobile-menu-open');
			body.addClass('mobile-cart-open');
			setTimeout(function() {
				jQuery('#container').on('click touchstart', SWIFT.nav.mobileMenuHideTrigger);
			}, 400);
		},
		hideMobileCart: function() {
			var windowTop = $window.scrollTop();
			body.removeClass('mobile-cart-open');
			setTimeout(function() {
				jQuery('#mobile-cart-wrap').css('display', 'none');
				if (windowTop > 0 && body.hasClass('mh-sticky')) {
					jQuery('#mobile-header').css('position', 'fixed').css('top', '0');
				} else {
					jQuery('#mobile-header').css('position', '').css('top', '');
				}
				jQuery('#container').off('click touchstart', SWIFT.nav.mobileMenuHideTrigger);
			}, 400);
			setTimeout(function() {
				body.removeClass('mobile-menu-closing');
			}, 1000);
		},
		mobileMenuHideTrigger: function(e) {
            if (e != null) {
                e.preventDefault();	
            }
			body.addClass('mobile-menu-closing');
			SWIFT.nav.hideMobileMenu();
			SWIFT.nav.hideMobileCart();
		},
		mobileHeaderOverlay: function(type) {
			
			if (type === "menu") {
				jQuery('#mobile-menu-wrap').css('display', 'block');
				body.addClass('mh-overlay-show');
				body.addClass('mh-menu-show');
				jQuery('#mobile-menu-wrap').animate({
					'opacity': 1
				}, 500, 'easeOutQuart');
			} else if (type === "cart") {
				jQuery('#mobile-cart-wrap').css('display', 'block');
				body.addClass('mh-overlay-show');
				body.addClass('mh-cart-show');
				jQuery('#mobile-cart-wrap').animate({
					'opacity': 1
				}, 500, 'easeOutQuart');
			}
			
		}
	};
	
	
	/////////////////////////////////////////////
	// WOOCOMMERCE FUNCTIONS
	/////////////////////////////////////////////
	
	SWIFT.woocommerce = {
		init: function() {
			
			// QUANTITY FUNCTIONS
			SWIFT.woocommerce.productQuantityAdjust();
		
			jQuery('.add_to_cart_button').on('click', function() {
				var button = jQuery(this);
				var added_text = button.attr("data-added_text");
				button.addClass("product-added");
				button.find('span').text(added_text);
				button.find('i').attr('class', 'fa-check');
			});
			
				// Cart remove product
			jQuery(document).on('click', '.shopping-bag .remove-product', function(e) {

				e.preventDefault();
				e.stopPropagation();
				var prod_id = jQuery(this).attr('data-product-id'),
				 	prod_quantity = jQuery(this).attr('data-product-qty'),
					empty_bag_txt = jQuery('.shopping-bag').attr('data-empty-bag-txt'),
					singular_item_txt = jQuery('.shopping-bag').attr('data-singular-item-txt'),
					multiple_item_txt = jQuery('.shopping-bag').attr('data-multiple-item-txt'),
					data = {action: 'sf_cart_product_remove', product_id: prod_id},
					ajaxURL = jQuery(this).attr('data-ajaxurl');

				jQuery.post(ajaxURL, data, function(response) {

					var cartTotal = response;
					var cartcounter = 0;

					cartcounter = parseInt(jQuery('.cart-contents .num-items').first().text()) - prod_quantity;
					jQuery('.cart-contents .amount').replaceWith(cartTotal);
					jQuery('.cart-contents .num-items').text(cartcounter);

					jQuery('.cart-contents .num-items').each(function( index ) {
						jQuery(this).text(cartcounter);
					});

					jQuery('.product-id-'+prod_id).remove();

					if ( cartcounter <= 0 ) {
						jQuery('.sub-menu .shopping-bag').prepend('<div class="bag-empty">' + empty_bag_txt + '</div>');
						jQuery('.sub-menu .shopping-bag .bag-header').remove();
						jQuery('.sub-menu .shopping-bag .bag-buttons').remove();
					} else {
						if ( cartcounter == 1 ) {
							jQuery('.sub-menu .shopping-bag .bag-header').text('1 ' + singular_item_txt);
						} else {
							jQuery('.sub-menu .shopping-bag .bag-header').text(cartcounter + ' ' + multiple_item_txt);
						}
					}

				});

				return false;

			});
			
//			jQuery("body").bind("added_to_cart", function() {
//				console.log('added to cart');
//			});
						
			jQuery('.show-products-link').on('click', function(e) {
				e.preventDefault();
				var linkHref = jQuery(this).attr('href').replace('?', ''),
					currentURL = document.location.href.replace(/\/page\/\d+/, ''),
					currentQuery = document.location.search;
				
				if (currentQuery.indexOf('?show') >= 0) {				
					window.location = jQuery(this).attr('href');
				} else if (currentQuery.indexOf('?') >= 0) {
					window.location = currentURL + '&' + linkHref;
				} else {
					window.location = currentURL + '?' + linkHref;
				}
			});
			
			jQuery('.shipping-calculator-form input').keypress(function(e) {
				if(e.which == 10 || e.which == 13) {
					jQuery(".update-totals-button button").click();
				}
			}); 
			
			if (jQuery('.product-grid').length > 0) {
				
				if (!jQuery('.inner-page-wrap').hasClass('full-width-shop')) {
					var productGrid = jQuery('.product-grid');
					productGrid.imagesLoaded(function () {
						productGrid.equalHeights();
					});
					
					$window.smartresize( function() {
						jQuery('.product-grid').children().css('min-height','0');
						jQuery('.product-grid').equalHeights();
					});
				}
			}
		},
		load: function() {
			
			if (jQuery('.woocommerce-shop-page').hasClass('full-width-shop') && !(IEVersion && IEVersion < 9)) {
				SWIFT.woocommerce.fullWidthShop();
			}
			
		},
		variations: function() {
			jQuery('.variations_form select, .variations select').each( function() {
				var variationSelect = jQuery(this);
				variationSelect.on("change", function(){
					if (jQuery('#sf-included').hasClass('has-productzoom')) {
						jQuery('.zoomContainer').remove();
						setTimeout(function() {
							jQuery('.product-slider-image').each(function() {
								jQuery(this).data('zoom-image', jQuery(this).parent().find('a.zoom').attr('href'));
							});
							var firstImage = jQuery('#product-img-slider li:first').find('.product-slider-image');
							SWIFT.woocommerce.productZoom(firstImage);
							jQuery('#product-img-slider').flexslider(0);
						}, 500);
					} else {
						setTimeout(function() {
							jQuery('#product-img-slider').flexslider(0);
							var flexViewport = jQuery('#product-img-slider').find('.flex-viewport'),
							flexsliderHeight = flexViewport.find('ul.slides').css('height');
							
							flexViewport.animate({
								'height': flexsliderHeight
							}, 300);
						}, 500);
					}
				});
			});
		},
		productZoom: function(zoomObject) {
			jQuery('#product-img-slider li a.zoom').css('display', 'none');
			zoomObject.elevateZoom({
				zoomType: productZoomType,
				cursor: "crosshair",
				zoomParent: '#product-img-slider',
				responsive: true,
				zoomWindowFadeIn: 400,
				zoomWindowFadeOut: 500,
				lensSize: 250
			});
			$window.smartresize( function() {
				var currentImage = jQuery('#product-img-slider li.flex-active-slide').find('.product-slider-image');	
				if (!currentImage) {
					jQuery('#product-img-slider li:first').find('.product-slider-image');
				}
				SWIFT.woocommerce.productZoom(currentImage);
			});
		},
		fullWidthShop: function() {
			var shopItems = jQuery('.full-width-shop').find('.products'),
				itemWidth = shopItems.find('.product').first().data('width'),
				shopSidebar = shopItems.find('.sidebar');
			
			if (shopSidebar.length > 0) {
				
				// Full Width Shop Sidebar
				SWIFT.woocommerce.fullWidthShopSetSidebarHeight();
				$window.smartresize( function() {  
					SWIFT.woocommerce.fullWidthShopSetSidebarHeight();
				});
				
				shopItems.isotope({
					itemSelector: '.product',
					layoutMode: 'masonry',
					masonry: {
						columnWidth: '.'+itemWidth
					},
					isOriginLeft: !isRTL
				});
				
				SWIFT.woocommerce.animateItems(shopItems);
				
				shopSidebar.stop().animate({
					'opacity': 1
				}, 500);
				shopItems.isotope( 'stamp', shopSidebar );
				shopItems.isotope('layout');
					
			} else {
				
				shopItems.isotope({
					itemSelector: '.product',
					layoutMode: 'fitRows',
					isOriginLeft: !isRTL
				});
				
				SWIFT.woocommerce.animateItems(shopItems);
				
			}
		},
		fullWidthShopSetSidebarHeight: function() {
			var shopItems = jQuery('.full-width-shop').find('.products'),
				shopSidebar = shopItems.find('div.sidebar'),
				defaultSidebarHeight = shopSidebar.css('height', '').outerHeight(),
				newSidebarHeight = 0,
				sidebarHeightMultiply = 2,
				firstProductHeight = shopItems.find('.product').first().outerHeight(true);
			
			sidebarHeightMultiply = Math.ceil(defaultSidebarHeight / firstProductHeight);
			newSidebarHeight = firstProductHeight * sidebarHeightMultiply;
			shopSidebar.css('height', newSidebarHeight);
		},
		animateItems: function(shopItems) {
			shopItems.find('.product').each(function(i) {
				jQuery(this).delay(i*200).animate({
					'opacity' : 1
				}, 800, 'easeOutExpo', function() {
					jQuery(this).addClass('item-animated');
				});
			});
		},
		productQuantityAdjust: function() {

			// Increase
			jQuery(document).on('click', '.qty-plus', function(e) {
				e.preventDefault();
				var quantityInput = jQuery(this).parents('.quantity').find('input.qty'),
					newValue = parseInt(quantityInput.val(), 10) + 1,
					maxValue = parseInt(quantityInput.attr('max'), 10);

				if (!maxValue) {
					maxValue = 9999999999;
				}

				if ( newValue <= maxValue ) {
					quantityInput.val(newValue);
					quantityInput.change();
				}
			});

			// Decrease
			jQuery(document).on('click', '.qty-minus', function(e) {
				e.preventDefault();
				var quantityInput = jQuery(this).parents('.quantity').find('input.qty'),
					newValue = parseInt(quantityInput.val(), 10) - 1,
					minValue = parseInt(quantityInput.attr('min'), 10);
				
				if (!minValue) {
					minValue = 1;
				}

				if ( newValue >= minValue ) {
					quantityInput.val(newValue);
					quantityInput.change();
				}
			});

		}
	};

	/////////////////////////////////////////////
	// FLEXSLIDER FUNCTION
	/////////////////////////////////////////////
	
	SWIFT.flexSlider = {
		init: function() {
			
			if (jQuery('#product-img-slider').length > 0) {
				SWIFT.flexSlider.productSlider();
			}
					
			jQuery('.item-slider').flexslider({
				useCSS: flexUseCSS,
				animation: "slide",              //String: Select your animation type, "fade" or "slide"
				slideDirection: "horizontal",   //String: Select the sliding direction, "horizontal" or "vertical"
				slideshow: sliderAuto,	//Boolean: Animate slider automatically
				slideshowSpeed: sliderSlideSpeed,           //Integer: Set the speed of the slideshow cycling, in milliseconds
				animationDuration: sliderAnimSpeed,			//Integer: Set the speed of animations, in milliseconds
				rtl: isRTL,
				smoothHeight: true,         
				directionNav: true,             //Boolean: Create navigation for previous/next navigation? (true/false)
				controlNav: true,               //Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
				keyboardNav: false,              //Boolean: Allow slider navigating via keyboard left/right keys
				mousewheel: false,              //Boolean: Allow slider navigating via mousewheel
				prevText: "Prev",           //String: Set the text for the "previous" directionNav item
				nextText: "Next",               //String: Set the text for the "next" directionNav item
				pausePlay: true,               //Boolean: Create pause/play dynamic element
				pauseText: '',             //String: Set the text for the "pause" pausePlay item
				playText: '',               //String: Set the text for the "play" pausePlay item
				randomize: false,               //Boolean: Randomize slide order
				slideToStart: 0,                //Integer: The slide that the slider should start on. Array notation (0 = first slide)
				animationLoop: true,            //Boolean: Should the animation loop? If false, directionNav will received "disable" classes at either end
				pauseOnAction: true,            //Boolean: Pause the slideshow when interacting with control elements, highly recommended.
				pauseOnHover: false,            //Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
				controlsContainer: "",          //Selector: Declare which container the navigation elements should be appended too. Default container is the flexSlider element. Example use would be ".flexslider-container", "#container", etc. If the given element is not found, the default action will be taken.
				manualControls: "",             //Selector: Declare custom control navigation. Example would be ".flex-control-nav li" or "#tabs-nav li img", etc. The number of elements in your controlNav should match the number of slides/tabs.
				start: function(){},            //Callback: function(slider) - Fires when the slider loads the first slide
				before: function(){},           //Callback: function(slider) - Fires asynchronously with each slider animation
				after: function(){},      //Callback: function(slider) - Fires after each slider animation completes
				end: function(){}               //Callback: function(slider) - Fires when the slider reaches the last slide (asynchronous)
			});
			jQuery('.content-slider').each(function() {
				var slider = jQuery(this),
					autoplay = ((slider.attr('data-autoplay') === "yes") ? true : false);
				
				slider.flexslider({
					useCSS: flexUseCSS,
					animation: "fade",              //String: Select your animation type, "fade" or "slide"
					slideshow: autoplay,	//Boolean: Animate slider automatically
					slideshowSpeed: 6000,           //Integer: Set the speed of the slideshow cycling, in milliseconds
					animationDuration: 1000,			//Integer: Set the speed of animations, in milliseconds
					smoothHeight: false,         
					directionNav: true,             //Boolean: Create navigation for previous/next navigation? (true/false)
					rtl: isRTL,
					controlNav: false,               //Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
					prevText: "",           //String: Set the text for the "previous" directionNav item
					nextText: "",               //String: Set the text for the "next" directionNav item
					pauseOnHover: true,
					start: function() {
						SWIFT.flexSlider.slideEqualHeights();
						$window.smartresize( function() {  
							SWIFT.flexSlider.slideEqualHeights();
						});
					}
				});
			});
		},
		productSlider: function() {
			jQuery('#product-img-slider').flexslider({
				useCSS: flexUseCSS,
				animation: "slide",
				controlNav: true,
				smoothHeight: true,
				animationLoop: false,
				slideshow: sliderAuto,
				slideshowSpeed: sliderSlideSpeed,           //Integer: Set the speed of the slideshow cycling, in milliseconds
				animationDuration: sliderAnimSpeed,			//Integer: Set the speed of animations, in milliseconds
				rtl: isRTL,
				sync: '#product-img-nav',
				start: function(productSlider) {
					if (hasProductZoom) {
						if (productSlider.slides) {
							var currentImageHasSlides = productSlider.slides.eq(productSlider.currentSlide).find('.product-slider-image');
							SWIFT.woocommerce.productZoom(currentImageHasSlides);
						} else {
							var currentImageNoSlides = jQuery('#product-img-slider').find('.product-slider-image');
							SWIFT.woocommerce.productZoom(currentImageNoSlides);
						}
					}
				},
				before: function() {
					if (hasProductZoom) {
						jQuery('.zoomContainer').remove();
					}
				},
				after: function(productSlider) {
					if (hasProductZoom) {
						var currentSlideImage = productSlider.slides.eq(productSlider.currentSlide).find('.product-slider-image');
						SWIFT.woocommerce.productZoom(currentSlideImage);
					}
				}
			});
			
			if (jQuery('#product-img-nav ul.slides li').length > 1) {
				jQuery('#product-img-nav').flexslider({
					animation: "slide",
					rtl: isRTL,
					useCSS: flexUseCSS,
					directionNav: false,
					controlNav: false,
					animationLoop: false,
					slideshow: false,
					itemWidth: 100,
					itemMargin: 0,
					asNavFor: '#product-img-slider'
				});
			} else {
				jQuery('#product-img-nav').css('display', 'none');
			}
		},
		thumb: function() {			
			jQuery('.thumb-slider').flexslider({
				animation: "fade",              //String: Select your animation type, "fade" or "slide"
				slideDirection: "horizontal",   //String: Select the sliding direction, "horizontal" or "vertical"
				slideshow: sliderAuto,	//Boolean: Animate slider automatically
				slideshowSpeed: sliderSlideSpeed,           //Integer: Set the speed of the slideshow cycling, in milliseconds
				animationDuration: sliderAnimSpeed,         //Integer: Set the speed of animations, in milliseconds
				directionNav: true,             //Boolean: Create navigation for previous/next navigation? (true/false)
				controlNav: false,               //Boolean: Create navigation for paging control of each slide? Note: Leave true for manualControls usage
				keyboardNav: false,              //Boolean: Allow slider navigating via keyboard left/right keys
				smoothHeight: false,
				rtl: isRTL
			});
		},
		slideEqualHeights: function() {
			setTimeout(function() {
				jQuery('.content-slider').find('.slide-content-wrap').css('position', 'relative');
				jQuery('.content-slider > ul').equalHeights();
				jQuery('.content-slider').find('.slide-content-wrap').vCenterTop();
				jQuery('.content-slider').find('.slide-content-wrap').css('position', 'absolute');
			}, 200);
		}
	};
	
	/////////////////////////////////////////////
	// PORTFOLIO
	/////////////////////////////////////////////
	
	var portfolioContainer = jQuery('.portfolio-wrap').find('.filterable-items');
	
	SWIFT.portfolio = {
		init: function() {
			portfolioContainer.each(function() {
				var portfolioInstance = jQuery(this);
				if (portfolioInstance.hasClass('masonry-items') && !(IEVersion && IEVersion < 9)) {
					SWIFT.portfolio.masonrySetup(portfolioInstance);
				} else if (portfolioInstance.hasClass('multi-masonry-items') && !(IEVersion && IEVersion < 9)) {
					SWIFT.portfolio.multiMasonrySetup(portfolioInstance);
				} else {
					SWIFT.portfolio.standardSetup(portfolioInstance);
				}
			});
						
			// PORTFOLIO WINDOW RESIZE
			$window.smartresize( function() {  
				SWIFT.portfolio.windowResized();
			});
			
			// Enable filter options on when there are items from that skill
			jQuery('.filtering li').each( function() {
				var filter = jQuery(this),
					filterName = jQuery(this).find('a').attr('class'),
					portfolioItems = jQuery(this).parents('.portfolio-wrap').find('.filterable-items');
				
				portfolioItems.find('.portfolio-item').each( function() {
					if ( jQuery(this).hasClass(filterName) ) {
						filter.addClass('has-items');
					}
				});
			}).parents('.filtering').animate({
				opacity: 1
			}, 400);
	
			// filter items when filter link is clicked
			jQuery('.filtering li').on('click', 'a', function(e) {
				e.preventDefault();
				jQuery(this).parent().parent().find('li').removeClass('selected');
				jQuery(this).parent().addClass('selected');
				var selector = jQuery(this).data('filter');
				var portfolioItems = jQuery(this).parents('.portfolio-wrap').find('.filterable-items');
				portfolioItems.isotope({ filter: selector });				
			});  
			
			jQuery('.filter-wrap > a').on('click', function(e) {
				e.preventDefault();
				jQuery(this).parent().find('.filter-slide-wrap').slideToggle();
			});
		},
		standardSetup: function(portfolioInstance) {
			portfolioInstance.imagesLoaded(function () {
				SWIFT.portfolio.setItemHeight();
				SWIFT.flexSlider.thumb();
				portfolioInstance.animate({opacity: 1}, 800);
				portfolioInstance.isotope({
					resizable: true,
					layoutMode: 'fitRows',
					isOriginLeft: !isRTL
				});
			});
			portfolioInstance.appear(function() {
				SWIFT.portfolio.animateItems(portfolioInstance);
			});
		},
		masonrySetup: function(portfolioInstance) {
			portfolioInstance.imagesLoaded(function () {
				SWIFT.flexSlider.thumb();
				portfolioInstance.isotope({
					resizable: false, 
					itemSelector : '.portfolio-item',
					layoutMode: 'masonry',
					isOriginLeft: !isRTL
				});
			});
			portfolioInstance.appear(function() {
				SWIFT.portfolio.animateItems(portfolioInstance);
			});
		},
		multiMasonrySetup: function(portfolioInstance) {
			portfolioInstance.imagesLoaded(function () {
				SWIFT.flexSlider.thumb();
				SWIFT.portfolio.multiMasonrySizeFix(false);
				portfolioInstance.isotope({
					resizable: false, 
					itemSelector : '.portfolio-item',
					layoutMode: 'packery',
					packery: {
						columnWidth: '.grid-sizer'
					},
					isOriginLeft: !isRTL
				});
			});
			portfolioInstance.appear(function() {
				SWIFT.portfolio.animateItems(portfolioInstance);
			});
		},
		animateItems: function(portfolioInstance) {
			portfolioInstance.find('.portfolio-item').each(function(i) {
				jQuery(this).delay(i*200).animate({
					'opacity' : 1
				}, 800, 'easeOutExpo', function() {
					jQuery(this).addClass('item-animated');
				});
			});
		},
		setItemHeight: function() {
			if (!portfolioContainer.hasClass('col-1') && !portfolioContainer.hasClass('masonry-items') && !portfolioContainer.hasClass('multi-masonry-items')) {
				portfolioContainer.children().css('min-height','0');
				portfolioContainer.equalHeights();
			}
		},
		multiMasonrySizeFix: function($init) {
			var baseItem = portfolioContainer.find('.portfolio-item.size-standard').first(),
				standardHeight = baseItem.height(),
				standardWidth = baseItem.width(),
				wideTallHeight = (standardHeight * 2) + parseInt(baseItem.css('margin-bottom'), 10),
				wideWidth = (standardWidth * 2) + parseInt(baseItem.css('margin-bottom'), 10);
			
			if (standardHeight > 0) {
				//portfolioContainer.find('.portfolio-item.size-wide .multi-masonry-img-wrap').css('height', standardHeight).css('width', wideWidth + 2);
				//portfolioContainer.find('.portfolio-item.size-wide-tall .multi-masonry-img-wrap').css('height', wideTallHeight).css('width', wideWidth + 2);
				//portfolioContainer.find('.portfolio-item.size-tall .multi-masonry-img-wrap').css('height', wideTallHeight).css('width', standardWidth + 2);
				portfolioContainer.find('.portfolio-item.size-wide .multi-masonry-img-wrap').css('height', standardHeight);
				portfolioContainer.find('.portfolio-item.size-wide-tall .multi-masonry-img-wrap').css('height', wideTallHeight);
				portfolioContainer.find('.portfolio-item.size-tall .multi-masonry-img-wrap').css('height', wideTallHeight);
			}
			if ($init) {
				portfolioContainer.isotope('layout');
			}
		},
		windowResized: function() {
			if (!portfolioContainer.hasClass('col-1') && !portfolioContainer.hasClass('masonry-items') && !portfolioContainer.hasClass('multi-masonry-items')) {
				SWIFT.portfolio.setItemHeight();
			}
			
			if (portfolioContainer.hasClass('multi-masonry-items')) {
				SWIFT.portfolio.multiMasonrySizeFix(true);
			}
		},
		portfolioShowcaseInit: function() {
			SWIFT.flexSlider.thumb();
			SWIFT.portfolio.portfolioShowcaseWrap();
			SWIFT.portfolio.portfolioShowcaseItems();
			$window.smartresize( function() {
				SWIFT.portfolio.portfolioShowcaseWrap();
				SWIFT.portfolio.portfolioShowcaseItems();
			});
		},
		portfolioShowcaseWrap: function() {
			var portfolioShowcaseWrap = jQuery('.portfolio-showcase-wrap');						
			portfolioShowcaseWrap.animate({opacity: 1}, 600);
		},
		portfolioShowcaseItems: function() {
			jQuery('.portfolio-showcase-wrap').each(function() {
				
				var contWidth = jQuery('#main-container').width();
				
				if (jQuery('#container').hasClass('boxed-layout')) {
					contWidth = jQuery('#container').width();
				}
				
				var thisShowcase = jQuery(this),
					columns = thisShowcase.find('.portfolio-showcase-items').data('columns'),
					windowWidth = contWidth + 2,
					itemWidth = Math.floor(windowWidth / columns),
					maximisedWidth = Math.floor(windowWidth * 40 / 100),
					reducedWidth = Math.floor(windowWidth / 5),
					deselectedLeft = (itemWidth / 2 - maximisedWidth / 2) / 0.75,
					resetLeft = (reducedWidth / 2 - maximisedWidth / 2) / 1.3,
					isAnimating = !1,
					speed = 300;

				var showcaseItem = thisShowcase.find('li.portfolio-item');
				
				if (columns === 5) {
					maximisedWidth = Math.floor(windowWidth * 25 / 100);
					reducedWidth = Math.floor(windowWidth / 5.33);
					deselectedLeft = (itemWidth / 2 - maximisedWidth / 2) / 0.75;
					resetLeft = (reducedWidth / 2 - maximisedWidth / 2) / 1.3;
					showcaseItem.css("width", itemWidth);
					showcaseItem.css("height", maximisedWidth / 1.5);
					showcaseItem.find('.main-image').css("width", maximisedWidth);
					showcaseItem.find('.main-image').css("left", resetLeft);
					showcaseItem.find('.main-image').css("top", - maximisedWidth / 6);
					speed = 200;
				} else {
					showcaseItem.css("width", itemWidth);
					showcaseItem.css("height", maximisedWidth / 2);
					showcaseItem.find('.main-image').css("width", maximisedWidth);
					showcaseItem.find('.main-image').css("left", resetLeft);
				}
							
				showcaseItem.each(function () {
					if (windowWidth > 768) {
						jQuery(this).mouseenter(function () {
							if (!isAnimating) {
								isAnimating = !0;
								jQuery(this).removeClass("deselected-item");
								thisShowcase.find(".deselected-item").stop().animate({
									width: reducedWidth
								}, speed);
								thisShowcase.find(".deselected-item").find(".main-image").stop().animate({
									left: deselectedLeft
								}, speed);
								jQuery(this).find(".main-image").stop().animate({
									left: 0
								}, speed);
								jQuery(this).stop().animate({
									width: maximisedWidth
								}, speed + 1, function () {
									jQuery(this).find(".item-info").stop().show();
									jQuery(this).find(".item-info").stop().animate({
										bottom: 0
									}, speed, "easeInOutQuart");
								});
							}
						});
						jQuery(this).mouseleave(function () {
							if (isAnimating) {
								isAnimating = !1;
								jQuery(this).addClass("deselected-item");
								thisShowcase.find(".portfolio-item").stop().animate({
									width: itemWidth
								}, speed);
								thisShowcase.find(".portfolio-item .main-image").stop().animate({
									left: resetLeft
								}, speed);
								jQuery(this).find(".item-info").stop().animate({
									bottom: -85
								}, speed, function () {
									jQuery(this).find(".item-info").stop().hide();
								});
							}
						});
					}
				});
			});			
		},
		stickyDetails: function() {
			
			var offset = 0;
			
			if (jQuery('.sticky-header').length > 0) {
				offset = jQuery('.sticky-header').height() > 0 ? jQuery('.sticky-header').height() : jQuery('#header-section').height();
			}
			if (jQuery('#wpadminbar').length > 0) {
				offset = offset + jQuery('#wpadminbar').height();
			}
			if (jQuery('.sticky-top-bar').length > 0) {
				offset = offset + jQuery('.sticky-top-bar').height() > 0 ? jQuery('.sticky-top-bar').height() : jQuery('#top-bar').height();
			}
			
			jQuery(".page-content").stick_in_parent({
				offset_top: offset + 30
			});
			
		}
	};
	
	
	/////////////////////////////////////////////
	// BLOG
	/////////////////////////////////////////////
	
	var blogItems = jQuery('.blog-wrap').find('.blog-items');
	
	SWIFT.blog = {
		init: function() {
		
			// BLOG ITEM SETUP
			blogItems.each(function() {
				var blogInstance = jQuery(this);
				if (blogInstance.hasClass('blog-grid-items')) {
					SWIFT.blog.blogGrid(blogInstance.find('.grid-items'));
				} else {
					SWIFT.blog.blogLayout(blogInstance);
				}
			});
			
			// Blog Filtering
			SWIFT.blog.blogFiltersInit();
			
			// BLOG AUX SLIDEOUT
			jQuery('.blog-slideout-trigger').on('click', function(e) {
				e.preventDefault();
				
				// VARIABLES
				var blogWrap = jQuery(this).parent().parent().parent().parent();
				var filterPanel = blogWrap.find('.blog-filter-wrap .filter-slide-wrap');
				var auxType = jQuery(this).attr('data-aux');
								
				// ADD COLUMN SIZE AND REMOVE BRACKETS FROM COUNT
				blogWrap.find('.aux-list li').addClass('col-sm-2');
				blogWrap.find('.aux-list li a span').each(function() {
					jQuery(this).html(jQuery(this).html().replace("(","").replace(")",""));
				});
				
				// IF SELECTING AN OPTION THAT IS OPEN, CLOSE THE PANEL
				if (jQuery(this).parent().hasClass('selected') && !filterPanel.is(':animated')) {
					blogWrap.find('.blog-aux-options li').removeClass('selected');
					filterPanel.slideUp(400);
					return;
				}
				
				// AUX BUTTON SELECTED STATE
				blogWrap.find('.blog-aux-options li').removeClass('selected');	
				jQuery(this).parent().addClass('selected');
				
				// IF SLIDEOUT IS OPEN
				if (filterPanel.is(':visible')) {
					
					filterPanel.slideUp(400);
					setTimeout(function() {
						blogWrap.find('.aux-list').css('display', 'none');
						blogWrap.find('.aux-'+auxType).css('display', 'block');
						filterPanel.slideDown();
					}, 600);
					
				// IF SLIDEOUT IS CLOSED
				} else {
					
					blogWrap.find('.aux-list').css('display', 'none');
					blogWrap.find('.aux-'+auxType).css('display', 'block');
					filterPanel.slideDown();
					
				}
			});
			
		},
		blogFiltersInit: function() {
			
			SWIFT.blog.blogShowFilters();
			jQuery('.filtering').animate({
				opacity: 1
			}, 400);
	
			// filter items when filter link is clicked
			jQuery('.filtering li').on('click', 'a', function(e) {
				e.preventDefault();
				jQuery(this).parent().parent().find('li').removeClass('selected');
				jQuery(this).parent().addClass('selected');
				var selector = jQuery(this).data('filter');
				var blogItems = jQuery(this).parents('.blog-wrap').find('.blog-items');
				blogItems.isotope({ filter: selector });				
			});  
			
			jQuery('.filter-wrap > a').on('click', function(e) {
				e.preventDefault();
				jQuery(this).parent().find('.filter-slide-wrap').slideToggle();
			});					
		},
		blogShowFilters: function() {
			// Enable filter options on when there are items from that skill
			jQuery('.filtering li').each( function() {
				var filter = jQuery(this),
					filterName = jQuery(this).find('a').attr('class'),
					blogItems = jQuery(this).parents('.blog-wrap').find('.blog-items');
				
				blogItems.find('.blog-item').each( function() {
					if ( jQuery(this).hasClass(filterName) ) {
						filter.addClass('has-items');
					}
				});
			});
		},
		blogLayout: function(blogInstance) {
			
			var blogType = blogInstance.data('blog-type'),
				layoutMode = 'fitRows';
			
			if (blogType === "masonry") {
				layoutMode = 'masonry';
			}
			
			if (blogType === "masonry" && blogInstance.hasClass('social-blog')) {
				var tweets = blogInstance.parent().find('.blog-tweets').html(),
					instagrams = blogInstance.parent().find('.blog-instagrams');
					
				blogInstance.imagesLoaded(function () {
					SWIFT.flexSlider.thumb();
					blogInstance.isotope({
						resizable: false, 
						itemSelector : '.blog-item',
						layoutMode: 'masonry',
						getSortData : {
							date : function ( elem ) {
								return jQuery(elem).data('date');
							}
						},
						sortBy: 'date',
						sortAscending: false,
						isOriginLeft: !isRTL
					});
					if (tweets !== "") {
					blogInstance.isotope('insert', jQuery(tweets));
					}
					if (instagrams.length > 0) {
					SWIFT.blog.masonryInstagram(instagrams, blogInstance);
					}
					blogInstance.isotope('updateSortData').isotope();
				});
				
			} else {
				blogInstance.imagesLoaded(function () {
					SWIFT.flexSlider.thumb();
					blogInstance.isotope({
						resizable: true,
						layoutMode: layoutMode,
						isOriginLeft: !isRTL
					});
				});
				blogInstance.appear(function() {
					SWIFT.blog.animateItems(blogInstance);
				});
			}
		},
		masonryInstagram: function(instagrams, blogItems) {
			var userID = instagrams.data('userid'),
				token = instagrams.data('token'),
				count = instagrams.data('count'),
				itemClass = instagrams.data('itemclass'),
				clientid = '756db9880cc84c3dab85118df38f9b91';
				
			jQuery.ajax({
				url: 'https://api.instagram.com/v1/users/' + userID + '/media/recent?access_token=' + token, // specify the ID of the first found user
				dataType: 'jsonp',
				type: 'GET',
				data: {client_id: clientid, count: count},
				success: function(data) {
					for (var i = 0; i < count; i++) {
						if (data.data[i]) {
							var caption = "";
							if (data.data[i].caption) {
								caption = data.data[i].caption.text;
							}
							instagrams.append("<li class='blog-item instagram-item "+itemClass+"' data-date='"+data.data[i].created_time+"'><a class='timestamp inst-icon' target='_blank' href='" + data.data[i].link +"'><i class='fa-instagram'></i></a><div class='inst-overlay'><a target='_blank' href='" + data.data[i].link +"'></a><h6>"+instagrams.data('title')+"</h6><h2>"+caption+"</h2><div class='name-divide'></div><data class='date timeago' title='"+data.data[i].created_time+"' value=''>"+data.data[i].created_time+"</data></div><img class='instagram-image' src='" + data.data[i].images.low_resolution.url +"' width='306px' height='306px' /></li>");  
						} 
					}
					jQuery("data.timeago").timeago();
					instagrams.imagesLoaded(function(){
						blogItems.isotope('insert', jQuery(instagrams.html()));
					});
					blogItems.isotope('updateSortData').isotope();
				}
			});
		},
		animateItems: function(blogInstance) {
			blogInstance.find('.blog-item').each(function(i) {
				jQuery(this).delay(i*200).animate({
					'opacity' : 1,
				}, 800, 'easeOutExpo', function() {
					jQuery(this).addClass('item-animated');
				});
			});
		},
		blogGrid: function(gridItems) {
			var tweets = gridItems.parent().find('.blog-tweets').html(),
				instagrams = gridItems.parent().find('.blog-instagrams');
						
			gridItems.imagesLoaded(function () {
				SWIFT.flexSlider.thumb();
				gridItems.isotope({
					resizable: false, 
					itemSelector : '.blog-item',
					layoutMode: 'fitRows',
					getSortData : {
						id : function ( elem ) {
							return jQuery(elem).data('sortid');
						}
					},
					sortBy: 'id',
					sortAscending: true,
					isOriginLeft: !isRTL
				});
				if (tweets !== "") {
				gridItems.isotope('insert', jQuery(tweets));
				}
				if (instagrams.length > 0) {
				SWIFT.blog.blogGridInstagram(instagrams, gridItems);
				}
				gridItems.isotope('updateSortData').isotope();
				SWIFT.blog.blogGridResize();
			}).animate({
				'opacity' : 1
			}, 800, 'easeOutExpo');
			
			$window.smartresize( function() {  
				SWIFT.blog.blogGridResize();
			});
			
		},
		blogGridResize: function() {
			blogItems.find('.grid-items').each(function() {
				var gridItem = jQuery(this).find('.blog-item'),
					itemWidth = gridItem.first().width();
				if (gridItem.first().hasClass('col-sm-sf-25')) {
					itemWidth = itemWidth / 2;
				}
				gridItem.css('height', itemWidth);
			});
			setTimeout(function() {
				jQuery(".tweet-text,.quote-excerpt").dotdotdot();
				blogItems.find('.grid-items').isotope('layout');			
			}, 500);
		},
		blogGridInstagram: function(instagrams, gridItems) {
			var userID = instagrams.data('userid'),
				token = instagrams.data('token'),
				count = instagrams.data('count'),
				clientid = '756db9880cc84c3dab85118df38f9b91';
				
			jQuery.ajax({
				url: 'https://api.instagram.com/v1/users/' + userID + '/media/recent?access_token=' + token, // specify the ID of the first found user
				dataType: 'jsonp',
				type: 'GET',
				data: {client_id: clientid, count: count},
				success: function(data) {
					for (var i = 0; i < count; i++) {
						if (data.data[i]) {
							var caption = "";
							if (data.data[i].caption) {
								caption = data.data[i].caption.text;
							}
							instagrams.append("<li class='blog-item col-sm-sf-5 instagram-item' data-date='"+data.data[i].created_time+"' data-sortid='"+i*2+"'><a class='timestamp inst-icon' target='_blank' href='" + data.data[i].link +"'><i class='fa-instagram'></i></a><div class='inst-img-wrap'><div class='inst-overlay'><a target='_blank' href='" + data.data[i].link +"'></a><h6>"+instagrams.data('title')+"</h6><h2>"+caption+"</h2><div class='name-divide'></div><data class='date timeago' title='"+data.data[i].created_time+"' value=''>"+data.data[i].created_time+"</data></div><img class='instagram-image' src='" + data.data[i].images.low_resolution.url +"' /></div></li>");   
						}
					}
					jQuery("data.timeago").timeago();
					SWIFT.blog.blogGridResize();
					instagrams.imagesLoaded(function(){
						gridItems.isotope('insert', jQuery(instagrams.html()));
						SWIFT.blog.blogGridResize();
					});
					gridItems.isotope('updateSortData').isotope();
				}
			});
		},
		infiniteScroll: function() {
			if (!(IEVersion && IEVersion < 9)) {
				var infScrollData = jQuery('#inf-scroll-params');
				var infiniteScroll = {
					loading: {
						img: infScrollData.data('loadingimage'),
						msgText: infScrollData.data('msgtext'),
						finishedMsg: infScrollData.data('finishedmsg')
					},
					"nextSelector":".pagenavi li.next a",
					"navSelector":".pagenavi",
					"itemSelector":".blog-item",
					"contentSelector":".blog-items"
				};
				jQuery( infiniteScroll.contentSelector ).infinitescroll(
					infiniteScroll, function(elements) {
						SWIFT.flexSlider.thumb();
						//blogItems.fitVids();
						blogItems.imagesLoaded(function () {
							blogItems.isotope( 'appended', elements );
							jQuery.each(elements, function(i, element) {
								jQuery(element).addClass('item-animated');
							});
						});
						SWIFT.blog.blogShowFilters();
						if (blogItems.parent().find('.pagination-wrap').hasClass('load-more')) {
							jQuery('.load-more-btn').animate({
								'opacity': 1
							}, 400);
						}
					}
				);
				if (blogItems.parent().find('.pagination-wrap').hasClass('load-more')) {
					$window.unbind('.infscr');
					jQuery('.load-more-btn').on('click', function(e) {
						e.preventDefault();					
						jQuery( infiniteScroll.contentSelector ).infinitescroll('retrieve');
						jQuery('.load-more-btn').animate({
							'opacity': 0
						}, 400);
					});
				}
			} else {
				jQuery('.pagination-wrap').removeClass('hidden');
			}
		}
	};
	
	/////////////////////////////////////////////
	// GALLERIES
	/////////////////////////////////////////////
	
	var galleriesContainer = jQuery('.galleries-wrap').find('.filterable-items');
	
	SWIFT.galleries = {
		init: function() {
			galleriesContainer.each(function() {
				var galleriesInstance = jQuery(this);
				if (galleriesInstance.hasClass('masonry-items')) {
					SWIFT.galleries.masonrySetup(galleriesInstance);
				} else {
					SWIFT.galleries.standardSetup(galleriesInstance);
				}
			});
						
			// PORTFOLIO WINDOW RESIZE
			$window.smartresize( function() {  
				SWIFT.galleries.windowResized();
			});
			
			// Enable filter options on when there are items from that skill
			jQuery('.filtering li').each( function() {
				var filter = jQuery(this),
					filterName = jQuery(this).find('a').attr('class'),
					galleryItems = jQuery(this).parents('.galleries-wrap').find('.filterable-items');
				
				galleryItems.find('.gallery-item').each( function() {
					if ( jQuery(this).hasClass(filterName) ) {
						filter.addClass('has-items');
					}
				});
			}).parents('.filtering').animate({
				opacity: 1
			}, 400);
	
			// filter items when filter link is clicked
			jQuery('.filtering li').on('click', 'a', function(e) {
				e.preventDefault();
				jQuery(this).parent().parent().find('li').removeClass('selected');
				jQuery(this).parent().addClass('selected');
				var selector = jQuery(this).data('filter');
				var galleryItems = jQuery(this).parents('.galleries-wrap').find('.filterable-items');
				galleryItems.isotope({ filter: selector });				
			});  
		},
		standardSetup: function(galleryInstance) {
			galleryInstance.imagesLoaded(function () {
				SWIFT.galleries.setItemHeight();
				galleryInstance.animate({opacity: 1}, 800);
				galleryInstance.isotope({
					resizable: true,
					layoutMode: 'fitRows',
					isOriginLeft: !isRTL
				});
			});
			galleryInstance.appear(function() {
				SWIFT.galleries.animateItems(galleryInstance);
			});
		},
		masonrySetup: function(galleryInstance) {
			galleryInstance.imagesLoaded(function () {
				galleryInstance.isotope({
					resizable: false, 
					itemSelector : '.gallery-item',
					layoutMode: 'masonry',
					isOriginLeft: !isRTL
				});
			});
			galleryInstance.appear(function() {
				SWIFT.galleries.animateItems(galleryInstance);
			});
		},
		animateItems: function(galleryInstance) {
			galleryInstance.find('.gallery-item').each(function(i) {
				jQuery(this).delay(i*200).animate({
					'opacity' : 1
				}, 800, 'easeOutExpo', function() {
					jQuery(this).addClass('item-animated');
				});
			});
		},
		setItemHeight: function() {
			if (!galleriesContainer.hasClass('col-1') && !galleriesContainer.hasClass('masonry-items')) {
				galleriesContainer.children().css('min-height','0');
				galleriesContainer.equalHeights();
			}
		},
		windowResized: function() {
			if (!galleriesContainer.hasClass('col-1') && !galleriesContainer.hasClass('masonry-items')) {
				SWIFT.galleries.setItemHeight();
			}
		},
	};
	
	
	/////////////////////////////////////////////
	// GALLERY
	/////////////////////////////////////////////
	
	SWIFT.gallery = {
		init: function() {
			
			jQuery('.spb_gallery_widget').each(function() {
				if (jQuery(this).hasClass('gallery-masonry')) {
					SWIFT.gallery.galleryMasonry(jQuery(this).find('.filterable-items'));
				} else if (jQuery(this).hasClass('gallery-slider')) {
					SWIFT.gallery.gallerySlider(jQuery(this));
				}
			});
		},
		galleryMasonry: function(element) {
			element.imagesLoaded(function () {
				element.isotope({
					resizable: false, 
					itemSelector : '.gallery-image',
					layoutMode: 'masonry',
					isOriginLeft: !isRTL
				});
			});
			element.appear(function() {
				SWIFT.gallery.animateItems(element);
			});
		},
		animateItems: function(element) {
			element.find('.gallery-image').each(function(i) {
				jQuery(this).delay(i*200).animate({
					'opacity' : 1
				}, 800, 'easeOutExpo', function() {
					jQuery(this).addClass('item-animated');
				});
			});
		},
		gallerySlider: function(element) {
				
			var gallerySlider = element.find('.gallery-slider'),
				galleryNav = element.find('.gallery-nav'),
				galleryAuto = gallerySlider.data('autoplay');
			
			if (galleryAuto === "yes") {
				galleryAuto = true;
			} else {
				galleryAuto = false;
			}
				
			galleryNav.flexslider({
				animation: "slide",
				directionNav: true,
				controlNav: false,
				animationLoop: false,
				slideshow: false,
				itemWidth: 100,
				itemMargin: 30, 
				rtl: isRTL,
				asNavFor: gallerySlider
			});
				
			gallerySlider.flexslider({
				animation: gallerySlider.data('transition'),
				slideshow: galleryAuto,
				slideshowSpeed: sliderSlideSpeed,           //Integer: Set the speed of the slideshow cycling, in milliseconds
				animationDuration: sliderAnimSpeed,         //Integer: Set the speed of animations, in milliseconds
				controlNav: false,
				animationLoop: false,
				rtl: isRTL,
				sync: galleryNav,
				start: function() {
					if ( galleryAuto ) {
						gallerySlider.appear(function() {
							gallerySlider.flexslider('play');			
						});	
					}
				}
			});
		
		}
	};
	
	
	/////////////////////////////////////////////
	// RECENT POSTS
	/////////////////////////////////////////////
	
	SWIFT.recentPosts = {
		init: function() {
			
			// TEAM EQUAL HEIGHTS
			var recentPostAsset = jQuery('.recent-posts:not(.carousel-items,.posts-type-list)');
			recentPostAsset.imagesLoaded(function () {
				SWIFT.flexSlider.thumb();
				recentPostAsset.equalHeights();
			});
			
			// TEAM ASSETS
			$window.smartresize( function() {
				jQuery('.recent-posts:not(.carousel-items,.posts-type-list)').children().css('min-height','0');
				jQuery('.recent-posts:not(.carousel-items,.posts-type-list)').equalHeights();
			});
		}
	};
	
	
	/////////////////////////////////////////////
	// CAROUSEL FUNCTIONS
	/////////////////////////////////////////////
	
	SWIFT.carouselWidgets = {
		init: function() {
	
			// CAROUSELS
			var carousel = jQuery('.carousel-items'),
				carouselAuto = sfOptionParams.data('carousel-autoplay'),
				carouselPSpeed = sfOptionParams.data('carousel-pagespeed'),
				carouselSSpeed = sfOptionParams.data('carousel-slidespeed'),
				carouselPagination = sfOptionParams.data('carousel-pagination'),
				carouselPDirection = 'ltr',
				desktopWidth = 1199;
			
			if (body.hasClass('vertical-header')) {
				desktopWidth = desktopWidth + jQuery('#header-section').width();
			}
			
			if (carouselAuto) {
				carouselAuto = true;
			} else {
				carouselAuto = false;
			}	
			if (carouselPagination) {
				carouselPagination = true;
			} else {
				carouselPagination = false;
			}
			if (isRTL) {
				carouselPDirection = 'rtl';
			}
			
			carousel.each(function() {
				var carouselInstance = jQuery('#'+jQuery(this).attr('id')),
					carouselColumns = parseInt(carouselInstance.attr("data-columns"), 10),
					desktopCarouselItems = 4 > carouselColumns ? carouselColumns : 4,
					desktopSmallCarouselItems = 3 > carouselColumns ? carouselColumns : 3,
					mobileCarouselItems = 1;
				
				if (carouselInstance.hasClass('clients-items')) {
					mobileCarouselItems = 2;
				}
				if (carouselInstance.hasClass('testimonials')) {
					desktopCarouselItems = 1;
					desktopSmallCarouselItems = 1;
					mobileCarouselItems = 1;
				}
				
				carouselInstance.imagesLoaded(function () {
					
					if (!carouselInstance.hasClass('no-gutters')) {
						var carouselWidth = carouselInstance.width();
						if (isRTL) {
						carouselInstance.css('margin-right', '-15px').css('width', carouselWidth + 30);
						} else {
						carouselInstance.css('margin-left', '-15px').css('width', carouselWidth + 30);
						}
					}
										
					carouselInstance.owlCarousel({
						items : carouselColumns,
						itemsDesktop: [desktopWidth,desktopCarouselItems],
						itemsDesktopSmall: [desktopWidth-220,desktopSmallCarouselItems],
						itemsMobile: [479,mobileCarouselItems],
						paginationSpeed: carouselPSpeed,
						slideSpeed: carouselSSpeed,
						autoPlay: carouselAuto,
						autoPlayDirection : carouselPDirection,
						pagination: carouselPagination,
						autoHeight : true,
						beforeUpdate: function() {
							if (!carouselInstance.hasClass('no-gutters')) {
								carouselInstance.css('width', '');
								var carouselWidth = carouselInstance.width();
								carouselInstance.css('width', carouselWidth + 30);
							}
						},
						afterUpdate: function () {
							carouselInstance.find('.flex-active-slide').resize();
						},
						afterInit: function() {
							SWIFT.flexSlider.thumb();
							carouselInstance.find('.flex-active-slide').resize();
							carouselInstance.find('.owl-wrapper-outer').css('height', carouselInstance.find('.owl-wrapper').height());
						}
					}).animate({
						'opacity': 1
					},800);
				});
			});		
			
			// Previous
			jQuery('.carousel-next').click( function(e) {
				e.preventDefault();
				var carousel = jQuery(this).closest('.spb_content_element').find('.owl-carousel');
				if (isRTL) {
				carousel.data( 'owlCarousel' ).prev();
				} else {
				carousel.data( 'owlCarousel' ).next();
				}
			});
	
			// Next
			jQuery('.carousel-prev').click( function(e) {
				e.preventDefault();
				var carousel = jQuery(this).closest('.spb_content_element').find('.owl-carousel');
				if (isRTL) {
				carousel.data( 'owlCarousel' ).next();
				} else {
				carousel.data( 'owlCarousel' ).prev();
				}
			});	
		},
		carouselSwipeIndicator: function(carousel) {
			carousel.appear(function() {
				var swipeIndicator = jQuery(this).parents('.carousel-wrap').find('.sf-swipe-indicator');
				setTimeout(function() {
					swipeIndicator.fadeIn(500);
				}, 400);
				setTimeout(function() {
					swipeIndicator.addClass('animate');
				}, 1000);
				setTimeout(function() {
					swipeIndicator.fadeOut(400);	
				}, 3000);
			});
		},
		carouselMinHeight: function(carousel) {
			var minHeight = parseInt(carousel.find('.carousel-item:not(.no-thumb)').eq(0).css('height'));
			carousel.find('.owl-item').each(function () {
				jQuery(this).css('min-height',minHeight+'px');
			});
		}
	};

	
	/////////////////////////////////////////////
	// WIDGET FUNCTIONS
	/////////////////////////////////////////////
	
	SWIFT.widgets = {
		init: function() {
			
			// LOAD WIDGETS
			SWIFT.widgets.accordion();
			SWIFT.widgets.toggle();
			SWIFT.widgets.fullWidthVideo();
			SWIFT.widgets.introAnimations();
			SWIFT.widgets.iconBoxes();	

			// SF TOOLTIPS
			if (jQuery('[rel=tooltip]').length > 0) {
			jQuery('[rel=tooltip]').tooltip();
			}
			
			// DYNAMIC TABS
			jQuery('.tabs-type-dynamic .menu-icon').hover(function() {
				jQuery(this).parents('.nav-tabs').addClass('show-tabs');
			});
			
			jQuery('.tabs-type-dynamic').mouseleave(function() {
				jQuery(this).find('.nav-tabs').removeClass('show-tabs');
			});
			
			jQuery('ul.nav-tabs li a, .spb_accordion_section > h3 a').on('click', '', function(){
				var thisTab = jQuery(this),
					asset = thisTab.parents('.spb_content_element').first();
					
				setTimeout(function() {
					$window.trigger('resize');
					if (asset.find('.map-canvas').length > 0) {
						var map = asset.find('.map-canvas');
						map.each(function() {
							google.maps.event.trigger(jQuery(this), "resize");		
						});
					}
				}, 300);
			});
			
		},
		accordion: function() {
			jQuery('.spb_accordion').each(function() {
				var spb_tabs,				
					active_tab = false,
					active_attr = parseInt(jQuery(this).attr('data-active'), 10);
							
				if (jQuery.type( active_attr ) === "number") { active_tab = active_attr; }
							
				spb_tabs = jQuery(this).find('.spb_accordion_wrapper').accordion({
					header: "> div > h4",
					autoHeight: true,
					collapsible: true,
					active: active_tab,
					heightStyle: "content"
				});
			}).css('opacity', 1);
		},
		tabs: function() {
			// SET ACTIVE TABS PANE
			setTimeout(function() {
				jQuery('.spb_tabs').each(function() {
					jQuery(this).find('.tab-pane').first().addClass('active');
					jQuery(this).find('.tab-pane').removeClass('load');
				});
			}, 200);
			
			// SET ACTIVE TOUR PANE
			setTimeout(function() {
				jQuery('.spb_tour').each(function() {
					jQuery(this).find('.tab-pane').first().addClass('active');
					jQuery(this).find('.tab-pane').removeClass('load');
				});
			}, 200);
			
			// RESET MAPS ON TAB CLICK
			jQuery('ul.nav-tabs li a, .spb_accordion_section > h4 a').on('click', '', function(){
				var thisTab = jQuery(this),
					asset = thisTab.parents('.spb_content_element').first();
				
				if (asset.find('.map-canvas').length > 0) {     
					setTimeout(function() {
						jQuery(window).trigger('resize');
						var map = asset.find('.map-canvas');
						SWIFT.map.init();
					}, 100);
				}
			});
			
			// SET DESIRED TAB ON LOAD
			setTimeout(function() {
				if (jQuery('.spb_tabs').length > 0) {

					// Show correct tab on page load
					var tabUrl = document.location.toString();
					if (tabUrl.match('#') && jQuery('.nav-tabs a[href="#'+tabUrl.split('#')[1]+'"]').length > 0) {
					    var thisTab = jQuery('.nav-tabs a[href="#'+tabUrl.split('#')[1]+']"'),
					    	tabHash = tabUrl.split('#')[1];

					    jQuery('.nav-tabs a[href="#'+tabHash+'"]').tab('show');
					}

					// Change hash on tab click
					jQuery('.nav-tabs a').click(function (e) {
						var hash = e.target.hash;

						if (history.pushState) {
						    history.pushState(null, null, hash);
						} else {
						    location.hash = hash;
						}
					});
				}
				if (jQuery('.spb_accordion').length > 0) {

					// Show correct accordion section on page load
					var accordionUrl = document.location.toString();
					if (accordionUrl.match('#') && jQuery('.spb_accordion a[href="#'+accordionUrl.split('#')[1]+'"]').length > 0) {
					    var thisAccordion = jQuery('.spb_accordion a[href="#'+accordionUrl.split('#')[1]+'"]'),
					    	accordionHash = accordionUrl.split('#')[1];

					    jQuery('.spb_accordion a[href="#'+accordionHash+'"]').click();
					}

					// Change hash on tab click
					jQuery('.spb_accordion a').click(function (e) {
						var hash = e.target.hash;

						if (history.pushState) {
						    history.pushState(null, null, hash);
						} else {
						    location.hash = hash;
						}
					});
				}

				if (jQuery('.spb_tour').length > 0) {

					// Show correct accordion section on page load
					var tourUrl = document.location.toString();
					if (tourUrl.match('#') && jQuery('.spb_tour a[href="#'+tourUrl.split('#')[1]+'"]').length > 0) {
					    var thisTour = jQuery('.spb_tour a[href="#'+tourUrl.split('#')[1]+'"]'),
					    	tourHash = tourUrl.split('#')[1];

					    jQuery('.spb_tour a[href="#'+tourHash+'"]').click();
					}

					// Change hash on tab click
					jQuery('.spb_tour a').click(function (e) {
						var hash = e.target.hash;

						if ( history.pushState ) {
						    history.pushState(null, null, hash);
						} else {
						    location.hash = hash;
						}
					});
				}

			}, 220);
		},
		toggle: function() {
			jQuery('.spb_toggle').click(function() {
				if ( jQuery(this).hasClass('spb_toggle_title_active') ) {
					jQuery(this).removeClass('spb_toggle_title_active').next().slideUp(500);
				} else {
					jQuery(this).addClass('spb_toggle_title_active').next().slideDown(500);
				}
			});
			jQuery('.spb_toggle_content').each(function() {
				if ( jQuery(this).next().is('h4.spb_toggle') === false ) {
					jQuery('<div class="last_toggle_el_margin"></div>').insertAfter(this);
				}
			});
		},
		initSkillBars: function() {		
			// SKILL BARS
			SWIFT.widgets.animateSkillBars();
			jQuery('a.ui-tabs-anchor').click(function(){
				SWIFT.widgets.animateSkillBars();
			});
		},
		animateSkillBars: function() {
			jQuery('.progress:not(.animated)').each(function(){
				var progress = jQuery(this);
				
				progress.appear(function() {
					var progressBar = jQuery(this),
					progressValue = progressBar.find('.bar').data('value');
					progressBar.addClass('animated');
					progressBar.find('.bar').animate({
						width: progressValue + "%"
					}, 800, function() {
						progressBar.parent().find('.bar-text').css('width', progressValue + "%");
						progressBar.parent().find('.bar-text .progress-value').fadeIn(600);
					});
				});
			});
		},
		fullWidthVideo: function() {			
			body.on('click', '.fw-video-link', function(){
				if (jQuery(this).data('video') !== "") {
				SWIFT.widgets.openFullWidthVideo(jQuery(this));
				}
				return false;
			});
			
			body.on('click', '.fw-video-close', function(){
				SWIFT.widgets.closeFullWidthVideo();
			});
		},
		openFullWidthVideo: function(element) {
			jQuery('.fw-video-close').addClass('is-open');
			jQuery('.fw-video-spacer').animate({
				height: windowheight
			}, 1000, 'easeInOutExpo');
			
			jQuery('.fw-video-area').css('display', 'block').animate({
				top: 0,
				height: '100%'
			}, 1000, 'easeInOutExpo', function() {
				// load video here
				jQuery('.fw-video-area > .fw-video-wrap').append('<iframe class="fw-video" src="'+element.data('video')+'" width="100%" height="100%" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen=""></iframe>');
			});
		},
		closeFullWidthVideo: function() {
			jQuery('.fw-video-close').removeClass('is-open');
			jQuery('.fw-video-spacer').animate({
				height: 0
			}, 1000, 'easeInOutExpo', function(){
			});
			jQuery('.fw-video-area').animate({
				top:'-100%'
			}, 1000, 'easeInOutExpo', function() {
				jQuery('.fw-video-area').css('display', 'none');
				jQuery('.fw-video-area .fw-video').remove();
			});
			
			// pause videos
			jQuery('.fw-video-area video').each(function(){
				this.pause();
			});
			setTimeout(function() {
				jQuery('.fw-video-area').find('iframe').remove();
			}, 1500);
			
			return false;
		},
		introAnimations: function() {
			if ( !(isMobileAlt && body.hasClass('disable-mobile-animations')) ) {
				jQuery('.sf-animation').each(function() {
					var animatedItem = jQuery(this);
					animatedItem.waypoint(function(direction) {
						var itemAnimation = animatedItem.data('animation'),
						itemDelay = animatedItem.data('delay');
						
						setTimeout(function() {
							window.requestAnimationFrame( function() {
								animatedItem.addClass(itemAnimation);
								setTimeout(function() {
									animatedItem.addClass('sf-animate');
								}, 100);
							});
						}, itemDelay);
					}, {
					    offset: '90%',
					    triggerOnce: true
					});
				});
			}
		},
		animateItem: function(animatedItem) {
			var itemAnimation = animatedItem.data('animation'),
			itemDelay = animatedItem.data('delay');
			
			setTimeout(function() {
				animatedItem.addClass(itemAnimation + ' sf-animate');
			}, itemDelay);
		},
		iconBoxes: function() {
			if (isMobileAlt) {
				jQuery('.sf-icon-box').on('click', function() {
					jQuery(this).addClass('sf-mobile-hover');
				});
			} else {
				jQuery('.sf-icon-box').hover(
					function() {
						jQuery(this).addClass('sf-hover');
					}, function() {
						jQuery(this).removeClass('sf-hover');
					}
				);
			}
		}
	};
	
	
	/////////////////////////////////////////////
	// TEAM MEMBERS FUNCTION
	/////////////////////////////////////////////
	
	SWIFT.teamMembers = {
		init: function() {
			// TEAM EQUAL HEIGHTS
			var team = jQuery('.team-members:not(.carousel-items)');
			team.imagesLoaded(function () {
				if ($window.width() > 767) {
					team.equalHeights();
				}
			});
			
			// TEAM ASSETS
			$window.smartresize( function() {
				jQuery('.team-members:not(.carousel-items)').children().css('min-height','0');
				if ($window.width() > 767) {
					jQuery('.team-members:not(.carousel-items)').equalHeights();
				}
			});
		}
	};
	
	/////////////////////////////////////////////
	// RELATED POSTS FUNCTION
	/////////////////////////////////////////////
	
	SWIFT.relatedPosts = {
		init: function() {
			// TEAM EQUAL HEIGHTS
			var relatedItems = jQuery('.related-items');
			relatedItems.imagesLoaded(function () {
				if ($window.width() > 767) {
					relatedItems.equalHeights();
				}
			});
			
			// TEAM ASSETS
			$window.smartresize( function() {
				jQuery('.related-items').children().css('min-height','0');
				if ($window.width() > 767) {
					jQuery('.related-items').equalHeights();
				}
			});
		}
	};
		
	
	/////////////////////////////////////////////
	// MAP FUNCTIONS
	/////////////////////////////////////////////
	
	var infowindow, bounds, directory_bounds, mapTypeIdentifier = "", mapType, mapColor, mapSaturation, mapCenterLat, mapCenterLng, companyPos = "", isDraggable = true, latitude, longitude, mapCoordinates, markersArray = [];
	
	
	SWIFT.map = {
		init:function() {
			
			var maps = jQuery('.map-canvas');
			var mapContainer;
			
			if (typeof google !== 'undefined') {
				bounds = new google.maps.LatLngBounds();
			}
			
			maps.each(function(i, element) {
				mapContainer = element;
				var mapZoom = mapContainer.getAttribute('data-zoom');
				
				mapType = mapContainer.getAttribute('data-maptype');
				mapColor = mapContainer.getAttribute('data-mapcolor');
				mapCenterLat = mapContainer.getAttribute('data-center-lat');
				mapCenterLng = mapContainer.getAttribute('data-center-lng');
				mapSaturation = mapContainer.getAttribute('data-mapsaturation');
				
				SWIFT.map.createMap( mapContainer, mapZoom, mapType, mapColor, mapSaturation, jQuery(this), mapCenterLat, mapCenterLng );
			});
			 
		},
		getLatLong: function(address, fn) {
			var geocoder;
			geocoder = new google.maps.Geocoder();	
			geocoder.geocode( { 'address': address}, function(results, status) {
				fn(results[0].geometry.location, results[0].geometry.location.lat(), results[0].geometry.location.lng()); 
			});
		},
		addWinContent: function(marker, html, map) {
			google.maps.event.addListener(marker, 'click', function() {
				infowindow.setContent(html);
				infowindow.open(map, marker);
			});	
		},
		addMarker: function(mapInstance, pinLogoURL, pinTitle, pinContent, pinLink, address, pinButtonText) {
			var companyMarker;

			mapCoordinates = new google.maps.LatLng(latitude, longitude);	

			if (pinLogoURL) {					
				companyPos = new google.maps.LatLng(latitude, longitude);
				companyMarker = new google.maps.Marker({
					position: mapCoordinates,
					map: mapInstance,
					icon: pinLogoURL,
					animation: google.maps.Animation.DROP });
			} else {
				companyPos = new google.maps.LatLng(latitude, longitude);
				companyMarker = new google.maps.Marker({
					position: mapCoordinates,
					map: mapInstance,
					animation: google.maps.Animation.DROP });  
			}
			
			var html = '<div class="pinmarker">';
			if (pinTitle !== "") {
			html += '<h3>'+pinTitle+'</h3>';
			}
			if (pinContent !== "") {
			html += '<p>'+pinContent+' </p>';
			}
			if (pinLink !== "" && pinButtonText !== "") {
			html += '<div><a href="http://' + pinLink + '" target="_blank">'+pinButtonText+'</a></div>';
			}
			html += '</div>';
											
			infowindow = new google.maps.InfoWindow();
			SWIFT.map.addWinContent(companyMarker, html, mapInstance);
			bounds.extend(mapCoordinates);
		},
		createMap: function(mapContainer, mapZoom, mapType, mapColor, mapSaturation, targetMap, mapCenterLat, mapCenterLng) {
			
			if (google) {
				directory_bounds = new google.maps.LatLngBounds();
			}	
			
			var pinLogoURL = "", mapLightness = false;
								
			if (mapSaturation == "mono-light") {
				if (mapColor === "") {
					mapColor = "#ffffff";
				}
				mapSaturation = -100;
			} else if (mapSaturation == "mono-dark") {
				if (mapColor === "") {
					mapColor = "#222222";
				}
				mapSaturation = -100;
				mapLightness = true;
			} else {
				mapSaturation = -20;
			}
			
			var styles = [   
				{
					stylers: [
						{hue: mapColor},
						{"invert_lightness": mapLightness},
						{saturation: mapSaturation}
					]
				}
			];
			
			var styledMap = new google.maps.StyledMapType(styles,{name: "Styled Map"});  
									
			if (isMobileAlt) {
			isDraggable = false;
			}
			
			if (mapType === "satellite") {
			mapTypeIdentifier = google.maps.MapTypeId.SATELLITE;
			} else if (mapType === "terrain") {
			mapTypeIdentifier = google.maps.MapTypeId.TERRAIN;
			} else if (mapType === "hybrid") {
			mapTypeIdentifier = google.maps.MapTypeId.HYBRID;
			} else {
			mapTypeIdentifier = google.maps.MapTypeId.ROADMAP;
			}
			
			var settings = {
				scrollwheel: false,
				draggable: isDraggable,
				mapTypeControl: true,
				mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
				navigationControl: true,
				navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
				mapTypeId: mapTypeIdentifier
			};
			
			var mapInstance = new google.maps.Map(mapContainer, settings);
			var pinTitle, pinContent, pinLink, address, pinButtonText;
			var pincount = targetMap.parent().find('.pin_location').length;
			
			//Resize Window Responsiveness
			google.maps.event.addDomListener(window, 'resize', function() {
				//mapInstance.fitBounds(bounds);
				mapInstance.setZoom(mapInstance.getZoom());
			});
			
			bounds = new google.maps.LatLngBounds();
			targetMap.parent().find('.pin_location').each(function(i, element) {
		
				pinLogoURL = element.getAttribute('data-pinimage');
				pinTitle = element.getAttribute('data-title');
				pinContent = element.getAttribute('data-content');
				address = element.getAttribute('data-address');
				pinLink = element.getAttribute('data-pinlink');			
				latitude = element.getAttribute('data-lat');
				longitude = element.getAttribute('data-lng');
				pinButtonText = element.getAttribute('data-button-text');
						
	
				if (latitude === '' && longitude === '') {

					SWIFT.map.getLatLong(address, function(location, lati,longi ) {
							
						latitude = lati; 
						longitude = longi;
						SWIFT.map.addMarker(mapInstance, pinLogoURL, pinTitle, pinContent, pinLink, address, pinButtonText);
						
						if ( pincount > 1 ) {
							if(mapCenterLat !== '' && mapCenterLng !== ''){
								mapInstance.setZoom(parseInt(mapZoom, 10));
								mapInstance.setCenter(new google.maps.LatLng(mapCenterLat, mapCenterLng));	
							}else{
								mapInstance.fitBounds(bounds);	
							}	
						} else {
							mapInstance.setZoom(parseInt(mapZoom, 10));
							mapInstance.setCenter(new google.maps.LatLng(latitude, longitude));
						}
								
					});								

				} else {
								
					SWIFT.map.addMarker(mapInstance, pinLogoURL, pinTitle, pinContent, pinLink, address, pinButtonText);
				
					if ( pincount > 1 ) {
						if(mapCenterLat !== '' && mapCenterLng !== ''){
								mapInstance.setZoom(parseInt(mapZoom, 10));
								mapInstance.setCenter(new google.maps.LatLng(mapCenterLat, mapCenterLng));	
							}else{
								mapInstance.fitBounds(bounds);		
						}	
					} else {
						mapInstance.setZoom(parseInt(mapZoom, 10));
						mapInstance.setCenter(new google.maps.LatLng(latitude, longitude));
					}
					
				}
									
			});   
					
								
			// MAP COLOURIZE
			if (mapColor !== "") {
				mapInstance.mapTypes.set('map_style', styledMap);
				mapInstance.setMapTypeId('map_style');
			}					
			
			var listener = google.maps.event.addListener(mapInstance, "idle", function() { 
                    mapInstance.setZoom(parseInt( mapInstance.getZoom(), 10)); 
                    google.maps.event.removeListener(listener); 
                });

		}
	};
	
	
	
	/////////////////////////////////////////////
	// MAP DIRECTORY FUNCTIONS
	/////////////////////////////////////////////
	
	SWIFT.mapDirectory = {
		init : function(searchTerm, locationFilter, categoryFilter) {

			searchTerm = jQuery('#dir-search-value').val();
			locationFilter = jQuery('.directory-location-option').val();
			categoryFilter = jQuery('.directory-category-option').val();
			
			var mapsDirectory = jQuery('.map-directory-canvas');
			var mapDirContainer, mapZoom;

			mapsDirectory.each(function(i, element) {

				var mapDirContainer = element,
					mapZoom = mapDirContainer.getAttribute('data-zoom'),
					mapType = mapDirContainer.getAttribute('data-maptype'),
					mapColor = mapDirContainer.getAttribute('data-mapcolor'),
					mapSaturation = mapDirContainer.getAttribute('data-mapsaturation'),
					mapExcerpt = mapDirContainer.getAttribute('data-excerpt');
					
					if ( categoryFilter == null || categoryFilter == 'All' ){
						categoryFilter = mapDirContainer.getAttribute('data-directory-category');	
					}

				SWIFT.mapDirectory.directoryMap( mapDirContainer, mapZoom, mapType, mapColor, mapSaturation, jQuery(this), searchTerm, locationFilter, categoryFilter);

			});
		},
		searchDirectory: function(){
			SWIFT.mapDirectory.init(jQuery('#dir-search-value').val(), jQuery('.directory-location-option').val(), jQuery('.directory-category-option').val());
		},
		getLatLong: function(address, fn){
			var geocoder;
			geocoder = new google.maps.Geocoder();
			geocoder.geocode( { 'address': address}, function(results, status) {
				fn(results[0].geometry.location, results[0].geometry.location.lat(), results[0].geometry.location.lng());
			});
		},
		addWinContent: function(marker, html, map){
				markersArray.push(marker);
				google.maps.event.addListener(marker, 'click', function() {
				infowindow.setContent(html);
				infowindow.open(map, marker);
			});
		},
		addMarkerDir: function(mapInstance, pinLogoURL, pinTitle, pinContent, pinLink, address, pinButtonText,pinFeaturedImage, lat, lng){
			var companyMarker, pinImgContainer;

			mapCoordinates = new google.maps.LatLng(lat, lng);

			if (pinLogoURL) {
				companyPos = new google.maps.LatLng(lat, lng);
				companyMarker = new google.maps.Marker({
					position: mapCoordinates,
					map: mapInstance,
					icon: pinLogoURL,
					optimized: false,
					animation: google.maps.Animation.DROP });
			} else {
				companyPos = new google.maps.LatLng(lat, lng);
				companyMarker = new google.maps.Marker({
					position: mapCoordinates,
					map: mapInstance,
					animation: google.maps.Animation.DROP });
			}
			if (pinFeaturedImage === null) {
				pinImgContainer = '';
			} else {
				pinImgContainer = '<img class="info-window-img" alt="" src="'+pinFeaturedImage+'" height="140" width="140"/>';
			}

			var html = '';
			if (pinLink === '' || pinButtonText === '') {
				html = '<div class="pinmarker">'+pinImgContainer+'<div class="pinmarker-container"><h3>'+pinTitle+'</h3><div class="excerpt">'+pinContent+'</div></div></div>';
			} else {
				html = '<div class="pinmarker">'+pinImgContainer+'<div class="pinmarker-container"><h3>'+pinTitle+'</h3><div class="excerpt">'+pinContent+'</div><a class="pin-button" href="' + pinLink + '" target="_blank">'+pinButtonText+'</a></div></div>';
			}


			infowindow = new google.maps.InfoWindow();
			SWIFT.mapDirectory.addWinContent(companyMarker, html, mapInstance);
			directory_bounds.extend(mapCoordinates);
		},
		directoryMap: function(mapContainer, mapZoom, mapType, mapColor, mapSaturation, targetMap, searchTerm, locationFilter, categoryFilter, excerpt) {

			var pinLogoURL = "", mapLightness = false;
			var mapFilter = mapContainer.getAttribute('data-directory-map-filter');
			var mapFilterPos =  mapContainer.getAttribute('data-directory-map-filter-pos');
			var mapResults =  mapContainer.getAttribute('data-directory-map-results');
			var excerpt =  mapContainer.getAttribute('data-excerpt');
			var directoryItemHtml = "";

			//Only if we want to display the map
			if (mapResults !== 'list') {

				if (mapSaturation == "mono-light") {
					if (mapColor === "") {
						mapColor = "#ffffff";
					}
					mapSaturation = -100;
				} else if (mapSaturation == "mono-dark") {
					if (mapColor === "") {
						mapColor = "#222222";
					}
					mapSaturation = -100;
					mapLightness = true;
				} else {
					mapSaturation = -20;
				}

				var styles = [
					{
						stylers: [
							{hue: mapColor},
							{"invert_lightness": mapLightness},
							{saturation: mapSaturation}
						]
					}
				];

				var styledMap = new google.maps.StyledMapType(styles,{name: "Styled Map"});

				if (isMobileAlt) {
					isDraggable = false;
				}

				if (mapType === "satellite") {
				mapTypeIdentifier = google.maps.MapTypeId.SATELLITE;
				} else if (mapType === "terrain") {
				mapTypeIdentifier = google.maps.MapTypeId.TERRAIN;
				} else if (mapType === "hybrid") {
				mapTypeIdentifier = google.maps.MapTypeImapDirContainerd.HYBRID;
				} else {
				mapTypeIdentifier = google.maps.MapTypeId.ROADMAP;
				}


			} else {
				jQuery(mapContainer).hide();
			}

			var pinTitle, pinContent, pinLink, address, pinButtonText, pinShortContent, pinThumbnail;
			var pincount = targetMap.parent().find('.pin_location').length;

			directory_bounds = new google.maps.LatLngBounds();

			if (categoryFilter === "") {
				categoryFilter = mapContainer.getAttribute('data-directory-category');
			}

			//Ajax call to get the Directory Itens
			var data = {
				action: 'sf_directory',
				search_term: searchTerm,
				location_term: locationFilter,
				category_term: categoryFilter,
				item_excerpt: excerpt

			};

			jQuery.post(mapContainer.getAttribute('data-ajaxurl'), data, function(response) {
				var json = jQuery.parseJSON(response);
				var mapFilterHtml;
				var pincount = json.results;

				jQuery('.directory-results').hide();

				var settings = {
					scrollwheel: false,
					draggable: isDraggable,
					mapTypeControl: true,
					mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
					navigationControl: true,
					navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
					mapTypeId: mapTypeIdentifier
				};

				if (pincount > 0) {

					var mapInstance = new google.maps.Map(mapContainer, settings);
					markersArray = [];

					// MAP COLOURIZE
					if (mapColor !== "" && mapResults != 'list') {
						mapInstance.mapTypes.set('map_style', styledMap);
						mapInstance.setMapTypeId('map_style');
					}

					//Resize Window Responsiveness
					google.maps.event.addDomListener(window, 'resize', function() {
						mapInstance.fitBounds(directory_bounds);
						mapInstance.setZoom(parseInt(mapZoom, 10));
					});
					
					var resultText = "", thumbnail_image = '';
										
					if ( pincount > 1 ) {
					resultText = json.results_text_1 + ' '+pincount+' '+json.results_text_2plural;
					} else {
					resultText = json.results_text_1 + ' '+pincount+' '+json.results_text_2;
					}
					jQuery(mapContainer).parent().parent().parent().find('.directory-results').prepend('<div class="results-title"><h2>'+resultText+'</h2>');

				} else {

					for (var i=0; i < markersArray.length; i++) {
						markersArray[i].setMap(null);
					}

					jQuery(mapContainer).parent().parent().parent().find('.directory-results').append('<div class="results-title"><h2>'+json.errormsg+'</h2>');

				}

				if (pincount > 0 && mapResults != 'list') {

					jQuery(mapContainer).show();
					jQuery('.directory-results').append(json.pagination);

					jQuery.each(json.items, function(i, item) {

						//Get the directory itens details
						var pinTitle = item.pin_title,
							pinLogoURL = item.pin_logo_url,
							pinContent = item.pin_content,
							pinShortContent = item.pin_short_content,
							address = item.pin_address,
							pinLink = item.pin_link,
							pinButtonText = item.pin_button_text,
							latitude = item.pin_lat,
							longitude = item.pin_lng,
							pinThumbnail = item.pin_thumbnail,
							categories = item.categories,
							location = item.location,
							thumbnail_image	= '',
							result_header = true,
							hasThumb = false,
							extra_class = '';
							if ( excerpt != '' ){
								pinContent = pinShortContent;
							}

						if (pinThumbnail && pinLink) {
							hasThumb = true;
							thumbnail_image = '<figure class="animated-overlay overlay-alt"><img itemprop="image" src="'+pinThumbnail+'" alt="'+pinTitle+'"><a href="'+pinLink+'" target="_blank" class="link-to-post"></a><div class="figcaption-wrap"></div><figcaption><div class="thumb-info"><h4>'+pinTitle+'</h4></div></figcaption></figure>';
						} else if (pinThumbnail) {
							hasThumb = true;
							thumbnail_image = '<figure><img itemprop="image" src="'+pinThumbnail+'" alt="'+pinTitle+'"></figure>';
						}

						//Add the Directory List Results
						if (!hasThumb) {
							extra_class = 'no-thumb';
						}

						if ( mapResults != 'list') {

							//Add directory item to the map
							SWIFT.mapDirectory.addMarkerDir(mapInstance, pinLogoURL, pinTitle, pinContent, pinLink, address, pinButtonText,pinThumbnail, latitude, longitude);

							if ( pincount > 1 ) {
								mapInstance.fitBounds(directory_bounds);
							} else {
								mapInstance.setZoom(parseInt(mapZoom, 10));
								mapInstance.setCenter(new google.maps.LatLng(latitude, longitude));
							}
						}
					});
				}else{
					jQuery(mapContainer).hide();
				}
				jQuery('.directory-results').show();
			});
		}
	};

				
	/////////////////////////////////////////////
	// CROWDFUNDING FUNCTIONS
	/////////////////////////////////////////////
	
	SWIFT.crowdfunding = {
		init:function() {
			
			if (jQuery('#back-this-project').find('.edd_price_options').length <= 0) {
				jQuery('#back-this-project > h2').css('display', 'none');
			}
			
			if (jQuery('#back-this-project').hasClass('project-donate-only')) {
				jQuery('.edd_price_options').find('li').first().addClass('atcf-selected');
				jQuery('.edd_price_options').find('li').first().find('input[type="radio"]').prop('checked', true);
			}
		
			jQuery('.atcf-price-option').on('click', function() {
				
				var selectedOption = jQuery(this),
					selectedPrice = selectedOption.data('price').substr(0, selectedOption.data('price').indexOf('-'));
				
				if (selectedOption.hasClass('inactive')) {
					return false;
				}
				
				if (selectedPrice && jQuery('#atcf_custom_price').val().length <= 0) {
				jQuery('#atcf_custom_price').val(selectedPrice);
				}
				jQuery('.atcf-price-option').find('input[type="radio"]').prop('checked', false);
				jQuery('.atcf-price-option').removeClass('atcf-selected');
				selectedOption.find('input[type="radio"]').prop('checked', true);
				selectedOption.addClass('atcf-selected');
			});
			
			var campaignItems = jQuery('.campaign-items:not(.carousel-items)');
			campaignItems.imagesLoaded(function () {
				campaignItems.equalHeights();
			});
			
			$window.smartresize( function() {
				jQuery('.campaign-items:not(.carousel-items)').children().css('min-height','0');
				jQuery('.campaign-items:not(.carousel-items)').equalHeights();
			});
			
		}
	};
	
	/////////////////////////////////////////////
	// RELOAD FUNCTIONS
	/////////////////////////////////////////////
	
	SWIFT.reloadFunctions = {
		init:function() {	
	
			// Remove title attributes from images to avoid showing on hover 
			jQuery('img[title]').each(function() {
				jQuery(this).removeAttr('title');
			});
			
			if (!isAppleDevice) {
				jQuery('embed').show();
			}
						
			// Animate Top Links
			jQuery('.animate-top').on('click', function(e) {
				e.preventDefault();
				jQuery('body,html').animate({scrollTop: 0}, 800, 'easeOutCubic');           
			});
		},
		load:function() {
			if (!isMobile) {
			
				// Button hover tooltips
				jQuery('.tooltip').each( function() {
					jQuery(this).css( 'marginLeft', '-' + Math.round( (jQuery(this).outerWidth(true) / 2) ) + 'px' );
				});
				
				jQuery('.comment-avatar').hover( function() {
					jQuery(this).find('.tooltip' ).stop().animate({
						bottom: '44px',
						opacity: 1
					}, 500, 'easeInOutExpo');
				}, function() {
						jQuery(this).find('.tooltip').stop().animate({
							bottom: '25px',
							opacity: 0
						}, 400, 'easeInOutExpo');
				});
				
				jQuery('.grid-image').hover( function() {
					jQuery(this).find('.tooltip' ).stop().animate({
						bottom: '85px',
						opacity: 1
					}, 500, 'easeInOutExpo');
				}, function() {
						jQuery(this).find('.tooltip').stop().animate({
							bottom: '65px',
							opacity: 0
						}, 400, 'easeInOutExpo');
				});
			
			}	
		}
	};
	
	
	/////////////////////////////////////////////
	// GLOBAL VARIABLES
	/////////////////////////////////////////////
	
	var $window = jQuery(window),
		body = jQuery('body'),
		sfIncluded = jQuery('#sf-included'),
		sfOptionParams = jQuery('#sf-option-params'),
		windowheight = SWIFT.page.getViewportHeight(),
		deviceAgent = navigator.userAgent.toLowerCase(),
		isMobile = deviceAgent.match(/(iphone|ipod|android|iemobile)/),
		isMobileAlt = deviceAgent.match(/(iphone|ipod|ipad|android|iemobile)/),
		isAppleDevice = deviceAgent.match(/(iphone|ipod|ipad)/),
		isAndroid = deviceAgent.match(/(android)/),
		isIEMobile = deviceAgent.match(/(iemobile)/),
		isSafari = navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1 &&  navigator.userAgent.indexOf('Android') == -1,
		parallaxScroll = navigator.userAgent.indexOf('Safari') != -1 || navigator.userAgent.indexOf('Chrome') == -1,
		IEVersion = SWIFT.page.checkIE(),
		isRTL = body.hasClass('rtl') ? true : false,
		flexUseCSS = isMobileAlt ? false : true,
		sliderAuto = sfOptionParams.data('slider-autoplay') ? true : false,
		sliderSlideSpeed = sfOptionParams.data('slider-slidespeed'),
		sliderAnimSpeed = sfOptionParams.data('slider-animspeed'),
		lightboxControlArrows = sfOptionParams.data('lightbox-nav') === "arrows" ? true : false,
		lightboxThumbs = sfOptionParams.data('lightbox-thumbs') ? true : false,
		lightboxSkin = sfOptionParams.data('lightbox-skin') === "dark" ? "metro-black" : "metro-white",
		lightboxSharing = sfOptionParams.data('lightbox-sharing') ? true : false,
		hasProductZoom = jQuery('#sf-included').hasClass('has-productzoom') && !body.hasClass('mobile-browser') ? true : false,
		productZoomType = sfOptionParams.data('product-zoom-type') === "lens" ? "lens" : "inner";
	
	SWIFT.isScrolling = false;

	/////////////////////////////////////////////
	// LOAD + READY FUNCTION
	/////////////////////////////////////////////
		
	var onReady = {
		init: function(){
			SWIFT.page.init();
			if (jQuery('.sf-super-search').length > 0) {
			SWIFT.superSearch.init();
			}
			if (jQuery('#header-section').length > 0) {
			SWIFT.header.init();
			}
			SWIFT.nav.init();
			//if (sfIncluded.hasClass('has-products') || body.hasClass('woocommerce-cart') || body.hasClass('woocommerce-account')) {
			SWIFT.woocommerce.init();
			//}
			if (sfIncluded.hasClass('has-portfolio')) {
			SWIFT.portfolio.init();
			}
			if (sfIncluded.hasClass('has-portfolio-showcase')) {
			SWIFT.portfolio.portfolioShowcaseInit();
			}
			if (sfIncluded.hasClass('has-blog')) {
			SWIFT.blog.init();
			}
			if (sfIncluded.hasClass('has-infscroll') && !isMobile) {
			SWIFT.blog.infiniteScroll();
			}
			if (sfIncluded.hasClass('has-gallery')) {
			SWIFT.gallery.init();
			}
			if (sfIncluded.hasClass('has-galleries')) {
			SWIFT.galleries.init();
			}
			SWIFT.widgets.init();
			if (sfIncluded.hasClass('has-team')) {
			SWIFT.teamMembers.init();
			}
			if (sfIncluded.hasClass('has-carousel')) {
			SWIFT.carouselWidgets.init();
			}
			SWIFT.crowdfunding.init();
			SWIFT.reloadFunctions.init();
		}
	};
	var onLoad = {
		init: function(){
			if (body.hasClass('sf-preloader') && !isMobileAlt) {
			SWIFT.page.homePreloader();
			}
			SWIFT.flexSlider.init();
			if (sfIncluded.hasClass('has-products') || body.hasClass('woocommerce-cart') || body.hasClass('woocommerce-account')) {
			SWIFT.woocommerce.load();
			}
			SWIFT.page.load();
			if (body.hasClass('page-transitions')) {
			SWIFT.page.fadePageIn();
			}
			SWIFT.widgets.tabs();
			if (sfIncluded.hasClass('has-progress-bar')) {
			SWIFT.widgets.initSkillBars();
			}
			if (sfIncluded.hasClass('has-map')) {
				SWIFT.map.init();
				SWIFT.mapDirectory.init('', '', '');
			
				jQuery('#directory-search-button').live( "click", function() {
					jQuery('.directory-search-form').submit();
				});
				jQuery('ul.nav-tabs li a, .spb_accordion_section > h3 a').click(function(){
					var thisTabHref = jQuery(this).attr('href');
					if (jQuery(thisTabHref).find('.spb_gmaps_widget').length > 0) {
						SWIFT.map.init();
						SWIFT.mapDirectory.init('', '', '');
					}
				});
			}
			SWIFT.reloadFunctions.load();
			SWIFT.woocommerce.variations();
		}
	};
	
	jQuery(document).ready(onReady.init);
	jQuery(window).load(onLoad.init);
	
})(jQuery);

window.onpageshow = function(event) {
    if ( event.persisted && jQuery('body').hasClass("sf-preloader") ) {
        window.location.reload();
    }
};
