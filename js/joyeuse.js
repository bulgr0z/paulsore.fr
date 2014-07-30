$(function() {

	$('.karou-container').karou({
		hasNav: false,
		hasPrevNext: true,
		onInit: function() { // Fixer le carounav
			$('.karou-container .carounav .navprev').html('prev')
			$('.karou-container .carounav .navnext').html('next')
			$('.karou-container .carounav').appendTo('#content article')
		},
		onMove: function() { // légende
			var caption = $('.karou-container .karouel').eq(this.settings.current).attr('data-caption');
			$('#karou-caption').css("opacity", 1);

			if (caption) {
				$('#karou-caption').html(caption)
			} else {
				$('#karou-caption').css("opacity", 0);
			}
		}
	});

	// cas du caroussel fluide
	if ($('#content>article').hasClass('fluid')) {

		function getKarouHeight() {
			return Math.floor($(window).width() * 0.50);
		}

		$('#content>article').css('height', 540)
		$(window).on('resize', function() {
			var freespace = $(window).height() - 265; // espace dispo pour le caroussel
			if (freespace > 540) $('#content>article').css('height', freespace)
		});
		$(window).trigger('resize');
	}

	// régler la taille de entry-content en fonction du texte présent dans .overview
	// - Si le texte est plus grand que la taille max (300px) de .entry-content alors fixé à 300px
	// - Si moins grand, alors fixé à la taille du texte (donc pas de scroll)
	var contentSize = $('article .entry-content .viewport .overview').height();
	if (contentSize >= 320) {
		$('article .entry-content').css('height', 320);
	} else {
		$('article .entry-content').css('height', contentSize);
	}
	// offset entry-content maitenant qu'il a une taille (inutile de le centrer avant)
	$('#content article .entry-content').css(offset_background());

	$('article .entry-content').tinyscrollbar();


});

function offset_background() {

	return {
		'margin-left': ($('#content article .entry-content').outerWidth(true) / 2) * -1,
		'margin-top': ($('#content article .entry-content').outerHeight(true) / 2) * -1
	}

}