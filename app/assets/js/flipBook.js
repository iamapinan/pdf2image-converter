jQuery.fn.startAnimation = function(animationName) {
	var o = this;
	o.css('-webkit-animation-name', '');
	window.setTimeout(function() {
		o.css('-webkit-animation-name', animationName);
	}, 0);
};

(function() {
	var newMethods = {
		flipBook: function(imageUrls, options) {
			var target = this[0];

			if(target.flipBookVars) {
				return target.flipBookVars;
			}

			target.flipBookVars = {};

			var settings = target.flipBookVars.settings = jQuery.extend({
				currentImage: 0,
				effectDuration: 800,
				fastFlipPause: 80
			}, options);

			var addFlippingPage = function(leftImageUrl, rightImageUrl, onLeft) {
				var flippingPage = $('<div>').addClass('flipping').append(
						$('<div>').addClass('left').css('background-image', (rightImageUrl ? 'url(' + rightImageUrl + ')' : '#fff')),
						$('<div>').addClass('right').css('background-image', (leftImageUrl ? 'url(' + leftImageUrl + ')' : '#fff'))
					);

				flippingPage.css('-webkit-transition', '-webkit-transform ' + (settings.effectDuration / 1000) + 's');

				if(onLeft) flippingPage.addClass('onLeft');

				return flippingPage;
			}

			this.addClass('flipBook');

			target.flipBookVars.pages = [];

			for(var i = 0; i <= imageUrls.length; i++) {
				var pageDiv = addFlippingPage(
					(i > 0 ? imageUrls[i-1] : null),
					(i < imageUrls.length ? imageUrls[i] : null),
					settings.currentImage >= i
				);

				pageDiv.addClass('page' + i);

				target.flipBookVars.pages.push(pageDiv);
			}

			for(var i = imageUrls.length; i >= 0; i--) {
				this.append(target.flipBookVars.pages[i]);				
			}

			target.flipBookVars.moveToForderground = function(foregroundPages) {
				$.each(this.pages, function() {
					this.removeClass('foreground');
				});
				$.each(foregroundPages, function() {
					this.addClass('foreground');
				});
			}

			target.flipBookVars.nextImage = function() {
				if(this.settings.currentImage == this.pages.length - 2) {
					this.pages[this.pages.length - 1].startAnimation('lastImage');
					return false;
				}
				
				this.pages[++this.settings.currentImage].addClass('onLeft');

				var t = this;
				window.setTimeout(function() {
					t.moveToForderground([t.pages[t.settings.currentImage + 1], t.pages[t.settings.currentImage]]);
				}, this.settings.effectDuration / 2);

				return true;
			};

			target.flipBookVars.prevImage = function() {
				if(this.settings.currentImage == 0) {
					this.pages[0].startAnimation('firstImage');
					return false;
				}
				this.pages[this.settings.currentImage--].removeClass('onLeft');
				this.moveToForderground([this.pages[this.settings.currentImage], this.pages[this.settings.currentImage + 1]]);
				return true;
			};

			target.flipBookVars.flipTo = function(index) {
				if(index == this.settings.currentImage) return false;

				if(index > this.settings.currentImage) {
					var again = this.nextImage();
				} else {
					var again = this.prevImage();
				}

				if(again) {
					var t = this;
					window.setTimeout(function() {
						t.flipTo(index);
					}, this.settings.fastFlipPause);
				}
			}

			return target.flipBookVars;
		}
	};

	jQuery.each(newMethods, function(i) {
		jQuery.fn[i] = this;
	});
})();