$(document).ready(function(){
	$('.contact-action').click(function(){
		$(this).parents('.contact').toggleClass('front-facing').toggleClass('back-facing');
	});

	$('.contact-action-delete').click(function(){
		return window.confirm("Willst du diesen Kontakt wirklich wirklich richtig echt löschen? Dann ist er nämlich weg, für immer!")
	});
});