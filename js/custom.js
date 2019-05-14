$(function() {
	function change_star($el) {
		var star = $el.text();
		var result;

		switch(star) {
			case "star_border":
				result = "1";
				break;
			case "star_half":
				result = "2";
				break;
			case "star":
				result = "0";
				break;
			default:
				return;
		}

		change_icon($el, "star" + result);
		return result;
	}

	function change_done($el) {
		var check = $el.text();
		var result;

		if(check == "check_box")
			result = "undone";
		else if(check == "check_box_outline_blank")
			result = "done";
		else
			return;

		change_icon($el, result);
		return result;
	}

	function change_icon($el, icon_name) {
		if(change_icon.icons[icon_name] == undefined)
			return;
		
		$el.attr("class").split(" ").forEach(function(item) {
			if(item.substring(0, 5) == "text-" || item.substring(item.length - 5, item.length) == "-text")
				$el.removeClass(item);
		});
		
		$el.addClass(change_icon.icons[icon_name][0]);
		$el.text(change_icon.icons[icon_name][1]);
	}

	change_icon.icons = new Map;
	change_icon.icons['star0'] = new Array("grey-text text-lighten-1", "star_border");
	change_icon.icons['star1'] = new Array("amber-text text-accent-4", "star_half");
	change_icon.icons['star2'] = new Array("deep-orange-text text-lighten-1", "star");
	change_icon.icons['done'] = new Array("red-text text-accent-2", "check_box");
	change_icon.icons['undone'] = new Array("pink-text text-lighten-2", "check_box_outline_blank");

	M.AutoInit();
	$(".menu-button").click(function() {
		$(".left-menu").toggleClass("active");
		$(".content").toggleClass("left-active");
		$(".notifications").removeClass("active");
		$(".content").removeClass("noti-active");
	});
	$(".left-menu .input-field input").on("focus", function() {
		$(this).parents(".left-menu .input-field").addClass("focus");
		$(this).parents(".left-menu").addClass("focus");
		$(".content").addClass("left-focus");
	});
	$(".left-menu .input-field input").on("blur", function() {
		$(this).parents(".left-menu .input-field").removeClass("focus");
		$(this).parents(".left-menu").removeClass("focus");
		$(".content").removeClass("left-focus");
	});
	$(".left-menu").on("mouseover", function() {
		$(".content").addClass("left-hover");
	});
	$(".left-menu").on("mouseout", function() {
		$(".content").removeClass("left-hover");
	});
	$(".noti-button").click(function() {
		$(".notifications").toggleClass("active");
		$(".content").toggleClass("noti-active");
		$(".left-menu").removeClass("active");
		$(".content").removeClass("left-active");
	});


	$("#write-subject").on("focus", function() {
		$(".write").addClass("focus");
		$("#write-subject").removeAttr("placeholder");
	});
	
	$('.datepicker').datepicker({
		container:'body',
		format:"yyyy-mm-dd",
		i18n:{
			cancel:'취소',
			clear:'초기화',
			done:'확인',
			months:['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			monthsShort:['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			weekdays:['일요일','월요일','화요일','수요일','목요일','금요일','토요일'],
			weekdaysShort:['일','월','화','수','목','금','토'],
			weekdaysAbbrev:['일','월','화','수','목','금','토']
		}
	});

	$("#write-star-icon").click(function() {
		$("#write-star").val(change_star($(this)));
	});

	$("#edit-star-icon").click(function() {
		$("#edit-star").val(change_star($(this)));
	});

	$('.fixed-action-btn').floatingActionButton({
		direction:'left',
		hoverEnabled:false
	});

	$(".list-star").click(function(e) {
		e.stopPropagation();
		change_star($(this));
	});

	$(".list-check").click(function(e) {
		e.stopPropagation();
		change_done($(this));
	});
});