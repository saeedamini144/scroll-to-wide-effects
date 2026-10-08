/**
 * SEI – Scroll Expand Image
 * Writes the scroll progress (0 → 1) of each widget into the --sei-p custom property.
 * All sizing lives in CSS. Vanilla JS, no dependencies.
 */
( function () {
	'use strict';

	var SELECTOR = '[data-sei]';

	var easings = {
		'linear': function ( p ) {
			return p;
		},
		'ease-in': function ( p ) {
			return p * p;
		},
		'ease-out': function ( p ) {
			return 1 - ( 1 - p ) * ( 1 - p );
		},
		'ease-in-out': function ( p ) {
			return p * p * ( 3 - 2 * p );
		}
	};

	var instances = new Set();
	var visible = new Set();
	var ready = new WeakSet();
	var observer = null;
	var ticking = false;

	function drop( inst ) {
		instances.delete( inst );
		visible.delete( inst );
		if ( observer ) {
			observer.unobserve( inst.root );
		}
	}

	function update( inst ) {
		var root = inst.root;

		if ( ! root.isConnected ) {
			drop( inst );
			return;
		}

		var distance = root.offsetHeight - inst.stage.offsetHeight;
		var top = root.getBoundingClientRect().top;
		var p = distance > 0 ? -top / distance : ( top <= 0 ? 1 : 0 );

		p = Math.min( Math.max( p, 0 ), 1 );
		root.style.setProperty( '--sei-p', inst.ease( p ).toFixed( 4 ) );
	}

	function updateVisible() {
		ticking = false;
		visible.forEach( update );
	}

	function requestUpdate() {
		if ( ! ticking ) {
			ticking = true;
			window.requestAnimationFrame( updateVisible );
		}
	}

	function onIntersect( entries ) {
		entries.forEach( function ( entry ) {
			var inst = entry.target.__sei;

			if ( ! inst ) {
				return;
			}

			if ( entry.isIntersecting ) {
				visible.add( inst );
			} else {
				visible.delete( inst );
			}

			// Always settle on the exact value when entering/leaving the viewport.
			update( inst );
		} );
	}

	function init( root ) {
		if ( ready.has( root ) ) {
			return;
		}

		var stage = root.querySelector( '.sei__stage' );

		if ( ! stage ) {
			return;
		}

		ready.add( root );

		var inst = {
			root: root,
			stage: stage,
			ease: easings[ root.getAttribute( 'data-sei-easing' ) ] || easings.linear
		};

		root.__sei = inst;
		instances.add( inst );

		if ( observer ) {
			observer.observe( root );
		} else {
			visible.add( inst );
		}

		update( inst );
	}

	function initAll() {
		document.querySelectorAll( SELECTOR ).forEach( init );
	}

	if ( 'IntersectionObserver' in window ) {
		observer = new IntersectionObserver( onIntersect, { rootMargin: '100px 0px' } );
	}

	window.addEventListener( 'scroll', requestUpdate, { passive: true } );
	window.addEventListener( 'resize', function () {
		instances.forEach( update );
	} );
	window.addEventListener( 'load', function () {
		instances.forEach( update );
	} );

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', initAll );
	} else {
		initAll();
	}

	// Elementor editor: the widget DOM is replaced on every control change, so re-scan.
	function watchEditor() {
		if ( ! document.body || ! document.body.classList.contains( 'elementor-editor-active' ) ) {
			return;
		}

		var scheduled = false;

		new MutationObserver( function () {
			if ( scheduled ) {
				return;
			}
			scheduled = true;
			window.requestAnimationFrame( function () {
				scheduled = false;
				initAll();
			} );
		} ).observe( document.body, { childList: true, subtree: true } );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', watchEditor );
	} else {
		watchEditor();
	}
}() );
