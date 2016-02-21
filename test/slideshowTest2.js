$(function() {


$('#test1').imSlider({
	'autoSlideshow' : true,
	'animationType' : 'slide'
});

$('#test2').imSlider({
	'autoSlideshow' : true,
	'animationType' : 'fade'
});

var test1 = $('#test2').data('imSlider');

$('#goToSlide').click(function(e) {
	e.preventDefault();
	var slide = $('#getSlideID').val();
	test1.changeSlide(slide);
});



});