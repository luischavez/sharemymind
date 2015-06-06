/**
* Cuando el documento este listo agregamos las funciones necesarias.
*/
(function($) {
	$("a.like").click(function() {
		$like = $(this);
		$.ajax({
			type: "POST",
			url: "like.php",
			data: {
				user_id: $(this).data("user"),
				share_id: $(this).data("share")
			}
		}).done(function(liked) {
			var likes = parseInt($like.parent().children(".badge").text(), 10);
			$like.parent().children(".badge").text(liked == 1 ? likes + 1 : likes - 1);

			if (liked == 1) {
				$like.attr("class", "glyphicon glyphicon-heart");
			} else {
				$like.attr("class", "glyphicon glyphicon-heart-empty");
			}
		});
	});
})(jQuery);