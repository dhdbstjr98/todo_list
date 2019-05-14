$(function() {
	M.AutoInit();
	$(".menu-button").click(function() {
		$(".left-menu").toggleClass("active");
	});
	$(".left-menu .input-field input").on("focus", function() {
		$(this).parents(".left-menu .input-field").addClass("focus");
		$(this).parents(".left-menu").addClass("focus");
	});
	$(".left-menu .input-field input").on("blur", function() {
		$(this).parents(".left-menu .input-field").removeClass("focus");
		$(this).parents(".left-menu").removeClass("focus");
	});

	$(".noti-button").click(function() {
		$(".notifications").toggleClass("active");
	});
});