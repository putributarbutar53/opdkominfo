/*-----------------------------------------------------------------------------------
    Template Name: Safem Cleaning Service HTML Template
    Template URI: https://webtend.biz/safem
    Description: A perfect Cleaning template for build beautiful and unique Cleaning Service websites. It comes with nice and clean design.
    Author: WebTend
    Author URI: https://www.webtend.com
	Version: 1.0

	Note: This is Main js File For custom and jQuery plugins activation Code..
-----------------------------------------------------------------------------------

/*---------------------------
	INDEX
	===================
	01. Preloader
	02. Sticky Header
	03. Mobile Menu
	04. Hero slider active One
	05. Hero slider active Two
	06. Feature area hover class
	07. Magnific Popup
	08. Service area hover class
	09. Team Area Hover
	10. Team Slider Active
	11. Project Area Hover
	12. Project Slider Active
	13. Isotope Project
	14. counter up
	15. Easy PieChart
	16. niceSelect js
	17. Wow Js
	18. Brand Slider Active
	19. testimonialSlide Active
	20. Pricing area hover class
	21. Bootstrap Accordion
	22. Scroll and Click Event For Bak to top
	-----------------------------*/

	$(function() {
		'use strict';

	//===== 01. Preloader
	$(window).on('load', function(event) {
		$('#preloader')
		.delay(500)
		.fadeOut(500);
	});

	//===== 02. Sticky Header
	$(window).on('scroll', function(event) {
		var scroll = $(window).scrollTop();
		if (scroll < 110) {
			$('.header-nav').removeClass('sticky');
		} else {
			$('.header-nav').addClass('sticky');
		}
	});

	//===== 03. Mobile Menu
	$('.navbar-toggler').on('click', function() {
		$(this).toggleClass('active');
	});

	$('.navbar-nav a').on('click', function() {
		$('.navbar-toggler').removeClass('active');
	});

	var subMenu = $('.sub-menu-bar .navbar-nav .sub-menu');
	if (subMenu.length) {
		subMenu
		.parent('li')
		.children('a')
		.append(function() {
			return '<button class="sub-nav-toggler"> <i class="fal fa-plus"></i> <i class="fal fa-minus"></i> </button>';
		});

		var subMenuToggler = $('.sub-menu-bar .navbar-nav .sub-nav-toggler');

		subMenuToggler.on('click', function() {
			$(this)
			.parent()
			.parent()
			.children('.sub-menu')
			.slideToggle();
			$(this).toggleClass('clicked');
			return false;
		});
	}

	//===== 04. Hero slider active One
	var heroSlide = $('#heroSlideActive');
	heroSlide.slick({
		autoplay: true,
		arrows: true,
		adaptiveHeight: true,
		dots: false,
		slidesToShow: 1,
		slidesToScroll: 1,
		prevArrow:
		'<span class="slick-arrow prev-arrow"><i class="fal fa-angle-double-left"></i></span>',
		nextArrow:
		'<span class="slick-arrow next-arrow"><i class="fal fa-angle-double-right"></i></span>',
		responsive: [
		{
			breakpoint: 767,
			settings: {
				arrows: false
			}
		}
		]
	});

	//===== 05. Hero slider active Two
	var herosliderTwo = $('#herosliderTwo');
	var sliderArrows = $('#arrows');
	herosliderTwo.slick({
		arrows: true,
		adaptiveHeight: true,
		dots: false,
		autoplay: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		fade: true,
		appendArrows: sliderArrows,
		prevArrow:
		'<div class="slick-arrow prev-arrow"><i class="fal fa-angle-double-left"></i></div>',
		nextArrow:
		'<div class="slick-arrow next-arrow"><i class="fal fa-angle-double-right"></i></div>',
		responsive: [
		{
			breakpoint: 767,
			settings: {
				arrows: false
			}
		}
		]
	});

	//===== 06. Feature area hover class
	$('.feature-loop').on('mouseover', '.single-feature-box', function() {
		$('.single-feature-box.active').removeClass('active');
		$(this).addClass('active');
	});

	//====== 07. Magnific Popup
	$('.popup-video').magnificPopup({
		type: 'iframe'
		// other options
	});

	//===== 08. Service area hover class
	$('.service-loop').on('mouseover', '.single-service', function() {
		$('.single-service').removeClass('active');
		$(this).addClass('active');
	});

	//===== 09. Team Area Hover
	$('.team-loop .single-member-box').on('mouseenter', function() {
		$(this)
		.find('.social-icons')
		.slideDown(300);
	});
	$('.team-loop .single-member-box').on('mouseleave', function() {
		$(this)
		.find('.social-icons')
		.slideUp(300);
	});

	//===== 10. Team Slider Active
	var teamSlider = $('#teamSlider');
	teamSlider.slick({
		slidesToShow: 6,
		slidesToScroll: 1,
		fade: false,
		infinite: true,
		autoplay: true,
		autoplaySpeed: 4000,
		arrows: false,
		dots: false,
		responsive: [
		{
			breakpoint: 992,
			settings: {
				slidesToShow: 2
			}
		},
		{
			breakpoint: 575,
			settings: {
				slidesToShow: 1
			}
		}
		]
	});

	//===== 11. Project Area Hover
	$('.project-loop .single-project-box').on('mouseenter', function() {
		$(this)
		.find('.read-more-btn, .hide-text')
		.slideDown(300);
	});
	$('.project-loop .single-project-box').on('mouseleave', function() {
		$(this)
		.find('.read-more-btn, .hide-text')
		.slideUp(300);
	});

	//===== 12. Project Slider Active
	var projectSlider = $('#projectSlider');
	projectSlider.slick({
		slidesToShow: 2,
		slidesToScroll: 1,
		fade: false,
		infinite: true,
		autoplay: true,
		autoplaySpeed: 4000,
		arrows: false,
		dots: false,
		responsive: [
		{
			breakpoint: 1200,
			settings: {
				slidesToShow: 3
			}
		},
		{
			breakpoint: 992,
			settings: {
				slidesToShow: 2
			}
		},
		{
			breakpoint: 575,
			settings: {
				slidesToShow: 1
			}
		}
		]
	});

	//===== 13. Isotope Project
	var $grid = $('.gird').isotope({
		// options
		transitionDuration: '1s'
	});
	// items on button click
	$('.project-filter ul').on('click', 'li', function() {
		var filterValue = $(this).attr('data-filter');
		$grid.isotope({ filter: filterValue });
	});
	// menu active class
	$('.project-filter ul li').on('click', function(event) {
		$(this)
		.siblings('.active')
		.removeClass('active');
		$(this).addClass('active');
		event.preventDefault();
	});

	//===== 14. counter up
	$('.counter-num').counterUp({
		delay: 10,
		time: 2000
	});

	//===== 15. Easy PieChart
	$('.about-text').bind('inview', function(
		event,
		visible,
		visiblePartX,
		visiblePartY
		) {
		if (visible) {
			$('.chart').easyPieChart({
				//your configuration goes here
				easing: 'easeOut',
				delay: 3000,
				barColor: '#279e64',
				trackColor: '#ffc600',
				scaleColor: false,
				lineWidth: 20,
				size: 110,
				rotate: 280,
				animate: 2000,
				onStep: function(from, to, percent) {
					this.el.children[0].innerHTML = Math.round(percent) + '%';
				}
			});
			$(this).unbind('inview');
		}
	});

	//===== 16. niceSelect js
	$('select').niceSelect();

	//===== 17. Wow Js
	new WOW().init();

	//===== 18. Brand Slider Active
	var brandSlide = $('#brandSlide');
	brandSlide.slick({
		slidesToShow: 4,
		slidesToScroll: 1,
		fade: false,
		infinite: true,
		autoplay: true,
		autoplaySpeed: 5000,
		arrows: false,
		dots: false,
		responsive: [
		{
			breakpoint: 992,
			settings: {
				slidesToShow: 3
			}
		},
		{
			breakpoint: 575,
			settings: {
				slidesToShow: 1
			}
		}
		]
	});

	//===== 19. testimonialSlide Active
	var testimonialSlide = $('#testimonialSlide');
	testimonialSlide.slick({
		slidesToShow: 2,
		slidesToScroll: 1,
		fade: false,
		infinite: true,
		autoplay: true,
		autoplaySpeed: 5000,
		arrows: false,
		dots: true,
		responsive: [
		{
			breakpoint: 992,
			settings: {
				slidesToShow: 1
			}
		}
		]
	});

	//===== 20. Pricing area hover class
	$('.pricing-loop').on('mouseover', '.pricing-box', function() {
		$('.pricing-box.active').removeClass('active');
		$(this).addClass('active');
	});

	//===== 21. Bootstrap Accordion
	$('.faq-accordion .card-header button').on('click', function(e) {
		$('.faq-accordion .card-header button').removeClass('active-accordion');
		$(this).addClass('active-accordion');
	});

	//===== 22. Scroll and Click Event For Bak to top
	$(window).on('scroll', function() {
		var scrolled = $(window).scrollTop();
		if (scrolled > 300) $('.go-top').addClass('active');
		if (scrolled < 300) $('.go-top').removeClass('active');
	});
	$('.go-top').on('click', function() {
		$('html, body').animate(
		{
			scrollTop: '0'
		},
		1200
		);
	});
});
