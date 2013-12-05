$(document).ready(function(){
	$('.contact-action').click(function(){
		$(this).parents('.contact').toggleClass('front-facing').toggleClass('back-facing');
	});
});