$(function() {

	// Mail system
	var mailbloc = new Mailbloc();

	$('form#contact').on('submit', function(e) {

		e.preventDefault();
		// afficher le loader sur le formulaire
		mailbloc.setLoading();

		var $post = $.ajax({
			url: ajaxurl,
			type: 'post',
			dataType: 'json',
			data: {
				'action': 'sendmail',
				'email': $(this).find('#email').val(),
				'name': $(this).find('#name').val(),
				'message': $(this).find('#message').val()
			}
		});

		$post.done(function(data) {
			if (data && data.result) return mailbloc.success();
			return mailbloc.error();
		});

	});

	// Skills radial animations
	$('.radial-progress[data-item="code"]').attr('data-progress', 75);
	$('.radial-progress[data-item="integration"]').attr('data-progress', 90);
	$('.radial-progress[data-item="design"]').attr('data-progress', 55);

	// Dirty fix lien blog
	$('#menu-item-30 a').attr('target', '_blank');

});

// Gere les animations du bloc sendmail
//
// TODO : Gérer les vieux navigateurs ne supportant pas les transitions avec un test
// 	 -> var support = Modernizr.cssanimations;
//
var Mailbloc = function() {

	// trouver le bon endEvent pour les animations
	this.endEventName = this._getAnimationEventName();
	// binder le bouton retour
	$('#footer #contact .bloc-error #retour-contact').on('click', $.proxy(function(e) {
		e.preventDefault(); // ne pas remonter en haut de la page
		this._flip('contact'); // afficher le bon bloc
	}, this ));
};

Mailbloc.prototype = {

	_blocs: '#footer #contact .bloc',
	_blocLoading: '#footer #contact #contact-loading',

	_getAnimationEventName: function() {

		var endEventNames = {
			'WebkitAnimation' : 'webkitAnimationEnd',
			'OAnimation' : 'oAnimationEnd',
			'msAnimation' : 'MSAnimationEnd',
			'animation' : 'animationend'
		};

		return endEventNames[ Modernizr.prefixed( 'animation' ) ];

	},

	_flip: function(result) {

		var outBloc = $(this._blocs).siblings('.current');
		var inBloc = $(this._blocs+ '-' +result);

		inBloc.one(this.endEventName, function() {
			inBloc.removeClass('rotate-in');
			outBloc.removeClass('rotate-out current bloc-ontop');
		});

		outBloc.addClass('rotate-out current bloc-ontop');
		inBloc.addClass('rotate-in current');

	},

	success: function() {
		this._flip('success');
		$(this._blocLoading).removeClass('visible');
	},

	setLoading: function() {
		$(this._blocLoading).addClass('visible');
	},

	error: function() {
		this._flip('error');
		$(this._blocLoading).removeClass('visible');
	}

};
