(function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		// ハンバーガーメニューの開閉
		var toggle = document.querySelector( '.cl-hamburger' );
		var mobileNav = document.querySelector( '.cl-mobile-nav' );

		if ( toggle && mobileNav ) {
			toggle.addEventListener( 'click', function () {
				var isHidden = mobileNav.hasAttribute( 'hidden' );
				if ( isHidden ) {
					mobileNav.removeAttribute( 'hidden' );
					toggle.setAttribute( 'aria-expanded', 'true' );
				} else {
					mobileNav.setAttribute( 'hidden', '' );
					toggle.setAttribute( 'aria-expanded', 'false' );
				}
			} );

			// Escapeキーでモバイルメニューを閉じ、開閉ボタンにフォーカスを戻す
			document.addEventListener( 'keydown', function ( e ) {
				if ( e.key === 'Escape' && ! mobileNav.hasAttribute( 'hidden' ) ) {
					mobileNav.setAttribute( 'hidden', '' );
					toggle.setAttribute( 'aria-expanded', 'false' );
					toggle.focus();
				}
			} );
		}

		// ヘッダーのスクロール変化
		var header = document.querySelector( '.cl-header' );
		if ( header ) {
			var onScroll = function () {
				if ( window.scrollY > 20 ) {
					header.classList.add( 'is-scrolled' );
				} else {
					header.classList.remove( 'is-scrolled' );
				}
			};
			onScroll();
			window.addEventListener( 'scroll', onScroll, { passive: true } );
		}

		// スクロールフェードイン
		var revealEls = document.querySelectorAll( '.cl-reveal' );
		if ( 'IntersectionObserver' in window && revealEls.length ) {
			var observer = new IntersectionObserver(
				function ( entries ) {
					entries.forEach( function ( entry ) {
						if ( entry.isIntersecting ) {
							entry.target.classList.add( 'is-visible' );
							observer.unobserve( entry.target );
						}
					} );
				},
				{ threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
			);
			revealEls.forEach( function ( el ) {
				observer.observe( el );
			} );
		} else {
			revealEls.forEach( function ( el ) {
				el.classList.add( 'is-visible' );
			} );
		}
	} );
})();
