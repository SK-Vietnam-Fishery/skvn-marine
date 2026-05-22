(function () {
	'use strict';

	var prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	if (prefersReduced) {
		document.documentElement.classList.add('skvn-reduced-motion');
		return;
	}

	document.documentElement.classList.add('skvn-motion-ready');

	if (!('IntersectionObserver' in window)) {
		return;
	}

	var revealItems = document.querySelectorAll('[data-skvn-reveal]');

	if (!revealItems.length) {
		return;
	}

	var observer = new IntersectionObserver(
		function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) {
					return;
				}

				entry.target.classList.add('is-skvn-revealed');
				observer.unobserve(entry.target);
			});
		},
		{ threshold: 0.16 }
	);

	revealItems.forEach(function (item) {
		observer.observe(item);
	});
})();
