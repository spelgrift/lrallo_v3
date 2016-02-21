$(function() {


$('#test1').slideMan({
	'autoSlideshow' : false,
	'animationType' : 'slide'
});

$('#test2').slideMan();

// var test1 = $('#test2').data('imSlider');

$('#goToSlide').click(function(e) {
	e.preventDefault();
	var slide = $('#getSlideID').val();
	$('#test1').slideMan().changeSlide(slide, false);
});



});