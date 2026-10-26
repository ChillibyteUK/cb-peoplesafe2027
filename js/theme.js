/*!
 * cb-peoplesafe2027 v1.0.0 (https://github.com/ChillibyteUK/cb-peoplesafe2027)
 * Copyright 2026 Chillibyte - DS
 * Licensed under GPL-3.0
 */
(function () {
	'use strict';

	/**
	 * Mobile nav toggle. Wires any button with aria-controls pointing at a
	 * .navbar-collapse to show/hide it and keep aria-expanded in sync — this is
	 * the entire replacement for Bootstrap's Collapse component for this use case.
	 */
	function initNavToggle() {
	  document.querySelectorAll('.navbar-toggler[aria-controls]').forEach(toggler => {
	    const target = document.getElementById(toggler.getAttribute('aria-controls'));
	    if (!target) return;
	    toggler.addEventListener('click', () => {
	      const isOpen = target.classList.toggle('is-open');
	      toggler.setAttribute('aria-expanded', String(isOpen));
	    });

	    // Close after choosing a link — expected mobile nav behaviour.
	    target.querySelectorAll('a').forEach(link => {
	      link.addEventListener('click', () => {
	        target.classList.remove('is-open');
	        toggler.setAttribute('aria-expanded', 'false');
	      });
	    });
	  });
	}

	/**
	 * Click-to-open nav dropdowns. Each dropdown-toggle button shows/hides its
	 * linked .dropdown-menu and keeps aria-expanded in sync. Clicking elsewhere,
	 * or pressing Escape, closes whatever is open — this is the entire
	 * replacement for hover-based submenus.
	 */
	function initNavDropdowns() {
	  const toggles = document.querySelectorAll('.dropdown-toggle[aria-controls]');
	  function close(toggle) {
	    const menu = document.getElementById(toggle.getAttribute('aria-controls'));
	    if (!menu) return;
	    menu.classList.remove('is-open');
	    toggle.setAttribute('aria-expanded', 'false');
	  }
	  function closeAllExcept(except) {
	    toggles.forEach(toggle => {
	      if (toggle !== except) close(toggle);
	    });
	  }
	  toggles.forEach(toggle => {
	    const menu = document.getElementById(toggle.getAttribute('aria-controls'));
	    if (!menu) return;
	    toggle.addEventListener('click', event => {
	      event.stopPropagation();
	      const isOpen = menu.classList.toggle('is-open');
	      toggle.setAttribute('aria-expanded', String(isOpen));
	      closeAllExcept(toggle);
	    });
	  });
	  document.addEventListener('click', event => {
	    if (event.target.closest('.dropdown-menu')) return;
	    closeAllExcept();
	  });
	  document.addEventListener('keydown', event => {
	    if (event.key !== 'Escape') return;
	    const openToggle = Array.from(toggles).find(toggle => toggle.getAttribute('aria-expanded') === 'true');
	    closeAllExcept();
	    if (openToggle) openToggle.focus();
	  });
	}

	/**
	 * Header navigation (markup: inc/nav-render.php, styles: src/css/site-nav.css).
	 *
	 * Phones and small screens: the burger opens a full-screen list of plain links.
	 * Larger screens: a link that has a panel opens it, and a panel with a list on
	 * its left shows the content for the list item chosen. The links stay real
	 * links, so without JavaScript they go to their page. Escape or a click
	 * elsewhere closes the open panel.
	 */
	function initSiteNav() {
	  const header = document.querySelector('.site-header');
	  const nav = document.querySelector('.site-nav');
	  if (!header || !nav) return;

	  // Keep in sync with the max-width in src/css/site-nav.css.
	  const desktop = window.matchMedia('(min-width: 1200px)');
	  const burger = document.getElementById('site-nav-burger');
	  const items = document.getElementById('site-nav-items');
	  const toggles = Array.from(nav.querySelectorAll('.site-nav__toggle[data-panel]'));
	  const menus = document.getElementById('site-nav-menus');
	  function closePanels() {
	    toggles.forEach(toggle => {
	      toggle.setAttribute('aria-expanded', 'false');
	      document.getElementById(toggle.dataset.panel)?.classList.remove('active');
	    });
	  }
	  function showSubpanel(panel, button) {
	    panel.querySelectorAll('.left button').forEach(other => {
	      const on = other === button;
	      other.setAttribute('aria-expanded', String(on));
	      other.parentElement.classList.toggle('active', on);
	      document.getElementById(other.getAttribute('aria-controls'))?.classList.toggle('active', on);
	    });
	  }

	  // Line a panel up under its link, without running off the right of the page.
	  function place(panel, toggle) {
	    const base = menus.getBoundingClientRect();
	    const left = toggle.getBoundingClientRect().left - base.left;
	    const room = base.width - panel.offsetWidth;
	    panel.style.setProperty('--panel-left', Math.max(0, Math.min(left, room)) + 'px');
	  }
	  toggles.forEach(toggle => {
	    const panel = document.getElementById(toggle.dataset.panel);
	    if (!panel) return;
	    function open(event) {
	      if (!desktop.matches) return;
	      event.preventDefault();
	      const wasOpen = toggle.getAttribute('aria-expanded') === 'true';
	      closePanels();
	      if (wasOpen) return;
	      toggle.setAttribute('aria-expanded', 'true');
	      panel.classList.add('active');
	      place(panel, toggle);
	      const first = panel.querySelector('.left button');
	      if (first) showSubpanel(panel, first);
	    }
	    toggle.addEventListener('click', open);
	    toggle.addEventListener('keydown', event => {
	      if (event.key === ' ' || event.key === 'Enter' && !toggle.getAttribute('href')) open(event);
	    });
	    panel.querySelectorAll('.left button').forEach(button => {
	      button.addEventListener('click', () => showSubpanel(panel, button));
	      button.addEventListener('mouseenter', () => showSubpanel(panel, button));
	    });
	  });
	  if (burger && items) {
	    burger.addEventListener('click', () => {
	      const open = burger.getAttribute('aria-expanded') !== 'true';
	      burger.setAttribute('aria-expanded', String(open));
	      items.classList.toggle('active', open);
	    });
	  }
	  desktop.addEventListener('change', () => {
	    closePanels();
	    items?.classList.remove('active');
	    burger?.setAttribute('aria-expanded', 'false');
	  });
	  document.addEventListener('click', event => {
	    if (!event.target.closest('.site-nav__toggle, .site-nav__panel')) closePanels();
	  });
	  document.addEventListener('keydown', event => {
	    if (event.key !== 'Escape') return;
	    const open = toggles.find(toggle => toggle.getAttribute('aria-expanded') === 'true');
	    closePanels();
	    open?.focus();
	  });

	  // White bar once the page has moved.
	  function onScroll() {
	    header.classList.toggle('scrolled', window.scrollY > 100);
	  }
	  onScroll();
	  window.addEventListener('scroll', onScroll, {
	    passive: true
	  });
	}

	/**
	 * Form iframes (Pardot). The address sits in data-form-src so nothing loads
	 * until it is wanted: a form on the page loads as it nears the screen, and the
	 * Book a Demo dialog loads its form when it first opens. The page's campaign
	 * parameters (utm_*, gclid, msclkid, fbclid) are passed on to the form so a
	 * lead keeps its source.
	 */
	const FORWARDED = /^(utm_|gclid|msclkid|fbclid|gad_)/;
	function loadFormFrame(frame) {
	  const src = frame.getAttribute('data-form-src');
	  if (!src || frame.getAttribute('src')) {
	    return;
	  }
	  const url = new URL(src, window.location.href);
	  new URLSearchParams(window.location.search).forEach((value, key) => {
	    if (FORWARDED.test(key) && !url.searchParams.has(key)) {
	      url.searchParams.set(key, value);
	    }
	  });
	  frame.setAttribute('src', url.toString());
	}
	function initFormFrames() {
	  const frames = Array.from(document.querySelectorAll('iframe[data-form-src]:not(.is-in-dialog)'));
	  if (!('IntersectionObserver' in window)) {
	    frames.forEach(loadFormFrame);
	    return;
	  }
	  const observer = new IntersectionObserver(entries => {
	    entries.forEach(entry => {
	      if (entry.isIntersecting) {
	        loadFormFrame(entry.target);
	        observer.unobserve(entry.target);
	      }
	    });
	  }, {
	    rootMargin: '400px'
	  });
	  frames.forEach(frame => observer.observe(frame));
	}

	/**
	 * Native <dialog> wiring — replaces Bootstrap's Modal component entirely.
	 * showModal()/close() do the heavy lifting (focus trap, Escape-to-close,
	 * ::backdrop); this just connects trigger/close buttons to a target dialog.
	 *
	 * Markup:
	 *   <button data-dialog-target="my-dialog">Open</button>
	 *   <dialog id="my-dialog">
	 *     <button data-dialog-close>Close</button>
	 *     ...
	 *   </dialog>
	 */
	function initDialogs() {
	  document.querySelectorAll('[data-dialog-target]').forEach(trigger => {
	    const dialog = document.getElementById(trigger.getAttribute('data-dialog-target'));
	    if (!(dialog instanceof HTMLDialogElement)) return;
	    trigger.addEventListener('click', () => {
	      dialog.showModal();
	      dialog.querySelectorAll('iframe[data-form-src]').forEach(loadFormFrame);

	      // Lenis would keep scrolling the page behind the dialog.
	      if (window.cbLenis) {
	        window.cbLenis.stop();
	      }
	    });
	    dialog.addEventListener('close', () => {
	      if (window.cbLenis) {
	        window.cbLenis.start();
	      }
	    });
	    dialog.querySelectorAll('[data-dialog-close]').forEach(closeBtn => {
	      closeBtn.addEventListener('click', () => dialog.close());
	    });

	    // Click on the backdrop (the dialog element itself, outside its content) closes it.
	    dialog.addEventListener('click', event => {
	      if (event.target === dialog) dialog.close();
	    });
	  });
	}

	/**
	 * Video behind a play button. The block prints a button holding a poster
	 * image; the real player is only created when it is clicked, so a page with a
	 * video loads no third-party iframe until the visitor asks for it.
	 *
	 * Markup: <button class="video-facade" data-play="modal|inline" data-embed="https://…" data-title="…">
	 *
	 * data-play="modal" (the default) opens the video in a pop-out player on
	 * screens from md up; below md, and with data-play="inline", the video plays
	 * in place of the poster. The pop-out is one shared <dialog> built on first
	 * use; its player is removed on close, which stops the video.
	 */
	const DESKTOP = window.matchMedia('(min-width: 768px)');
	let modal = null;
	function buildModal() {
	  const dialog = document.createElement('dialog');
	  dialog.className = 'video-modal';
	  const close = document.createElement('button');
	  close.type = 'button';
	  close.className = 'video-modal__close';
	  close.setAttribute('aria-label', 'Close video');
	  close.innerHTML = '<span aria-hidden="true">&times;</span>';
	  close.addEventListener('click', () => dialog.close());
	  const inner = document.createElement('div');
	  inner.className = 'video-modal__inner';
	  dialog.append(close, inner);

	  // A click on the backdrop lands on the dialog itself; the player fills the dialog, so a click on it never does.
	  dialog.addEventListener('click', event => {
	    if (event.target === dialog) {
	      dialog.close();
	    }
	  });
	  dialog.addEventListener('close', () => {
	    inner.innerHTML = '';

	    // Lenis was stopped while the pop-out was open.
	    if (window.cbLenis) {
	      window.cbLenis.start();
	    }
	    if (dialog.opener && document.contains(dialog.opener)) {
	      dialog.opener.focus();
	    }
	  });
	  document.body.appendChild(dialog);
	  return dialog;
	}
	function createFrame(facade) {
	  const frame = document.createElement('iframe');
	  frame.className = 'video-facade__frame';
	  frame.src = facade.getAttribute('data-embed');
	  frame.title = facade.getAttribute('data-title') || 'Video';
	  frame.allow = 'autoplay; fullscreen; picture-in-picture';
	  frame.allowFullscreen = true;
	  return frame;
	}
	const warmed = new Set();

	/**
	 * Open the connection to the video provider as soon as a visitor points at
	 * (or tabs to) a poster, so the player is ready sooner when they click.
	 */
	function warmUp(facade) {
	  let origin = '';
	  try {
	    origin = new URL(facade.getAttribute('data-embed')).origin;
	  } catch (e) {
	    return;
	  }
	  if (warmed.has(origin)) {
	    return;
	  }
	  warmed.add(origin);
	  const link = document.createElement('link');
	  link.rel = 'preconnect';
	  link.href = origin;
	  document.head.appendChild(link);
	}
	function initVideoFacades() {
	  ['pointerover', 'focusin'].forEach(type => {
	    document.addEventListener(type, event => {
	      const facade = event.target.closest && event.target.closest('.video-facade');
	      if (facade) {
	        warmUp(facade);
	      }
	    });
	  });
	  document.addEventListener('click', event => {
	    const facade = event.target.closest('.video-facade');
	    if (!facade) {
	      return;
	    }
	    if ('inline' !== facade.getAttribute('data-play') && DESKTOP.matches && 'function' === typeof HTMLDialogElement) {
	      modal = modal || buildModal();
	      modal.querySelector('.video-modal__inner').replaceChildren(createFrame(facade));
	      modal.setAttribute('aria-label', facade.getAttribute('data-title') || 'Video');
	      modal.opener = facade;
	      modal.showModal();

	      // Lenis would keep scrolling the page behind the pop-out.
	      if (window.cbLenis) {
	        window.cbLenis.stop();
	      }
	      return;
	    }
	    const frame = createFrame(facade);
	    facade.replaceWith(frame);
	    frame.focus();
	  });
	}

	/**
	 * Entrance animations. Anything with data-animate="fade-up|fade-left|fade-right"
	 * (see src/css/animate.css) fades in once, the first time it scrolls into view.
	 * Without IntersectionObserver, or with reduced motion, it is just shown.
	 */
	function initScrollAnimate() {
	  const items = document.querySelectorAll('[data-animate]');
	  if (!items.length) {
	    return;
	  }
	  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	  if (reduce || !('IntersectionObserver' in window)) {
	    items.forEach(item => item.classList.add('is-visible'));
	    return;
	  }

	  // Anything already on screen at load is shown at once: a fade-in on the first view reads as a glitch, not an entrance.
	  items.forEach(item => {
	    const box = item.getBoundingClientRect();
	    if (box.top < window.innerHeight * 0.9 && box.bottom > 0) {
	      item.classList.add('is-instant', 'is-visible');
	    }
	  });
	  const observer = new IntersectionObserver(entries => {
	    entries.forEach(entry => {
	      if (entry.isIntersecting) {
	        entry.target.classList.add('is-visible');
	        observer.unobserve(entry.target);
	      }
	    });
	  }, {
	    threshold: 0.15,
	    rootMargin: '0px 0px -5% 0px'
	  });
	  items.forEach(item => {
	    if (!item.classList.contains('is-visible')) {
	      observer.observe(item);
	    }
	  });
	}

	/**
	 * Smooth scrolling with Lenis (js/vendor/lenis.min.js, loaded before this).
	 * The instance is window.cbLenis, so other code can stop() and start() it.
	 *
	 * Left off for visitors who ask for reduced motion, and on touch devices Lenis
	 * does not take over (syncTouch is off), so phones and tablets scroll natively.
	 *
	 * What it changes, and what to do about it:
	 *  - An element with its own scrollbar (a long menu, a dialog) needs
	 *    data-lenis-prevent, or the page scrolls instead of the element.
	 *  - Anything that locks the page (a dialog, a consent banner, a mobile menu)
	 *    should call window.cbLenis.stop() and start() again. dialog.js does.
	 *  - In-page links (#section, the skip link) are handled here so the page
	 *    glides and keyboard focus still moves to the target.
	 */
	function initSmoothScroll() {
	  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	  if (typeof window.Lenis !== 'function' || reduce) {
	    return;
	  }

	  // autoRaf: Lenis only moves the page when something calls its raf(); this
	  // lets it run its own animation loop. (Without it the wheel is intercepted
	  // but nothing scrolls.)
	  const lenis = new window.Lenis({
	    autoRaf: true,
	    anchors: false
	  });
	  window.cbLenis = lenis;
	  document.addEventListener('click', event => {
	    const link = event.target.closest('a[href^="#"]');
	    if (!link || event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey) {
	      return;
	    }
	    const id = decodeURIComponent(link.getAttribute('href').slice(1));
	    const target = id ? document.getElementById(id) : document.body;
	    if (!target) {
	      return;
	    }
	    event.preventDefault();
	    lenis.scrollTo(target, {
	      onComplete: () => {
	        if (id) {
	          if (!target.hasAttribute('tabindex') && !/^(A|BUTTON|INPUT|SELECT|TEXTAREA)$/.test(target.tagName)) {
	            target.setAttribute('tabindex', '-1');
	          }
	          target.focus({
	            preventScroll: true
	          });
	          window.history.pushState(null, '', `#${id}`);
	        }
	      }
	    });
	  });
	}

	/**
	 * Side tabs: the tabs are native <details> elements sharing a name (one open at
	 * a time); this shows the image that goes with the open one, and keeps one tab
	 * open (clicking the open tab does not close it).
	 *
	 * Markup: [data-side-tabs] > .side-tabs__images > .side-tabs__image (one per tab)
	 *                          > .side-tabs__tabs   > details.side-tabs__tab (one per tab)
	 */
	function initSideTabs() {
	  document.querySelectorAll('[data-side-tabs]').forEach(root => {
	    const images = Array.from(root.querySelectorAll('.side-tabs__image'));
	    const tabs = Array.from(root.querySelectorAll('.side-tabs__tab'));
	    function show() {
	      const open = tabs.findIndex(tab => tab.open);
	      images.forEach((image, index) => {
	        image.classList.toggle('is-active', index === open);
	      });
	    }
	    tabs.forEach(tab => {
	      tab.addEventListener('toggle', show);
	      tab.querySelector('summary').addEventListener('click', event => {
	        if (tab.open) {
	          event.preventDefault();
	        }
	      });
	    });
	    show();
	  });
	}

	/**
	 * Top tabs: the pills are buttons, each controlling one panel. Showing a tab
	 * hides the others; clicking the open tab leaves it open.
	 *
	 * Markup: [data-top-tabs] > button.top-tabs__pill[aria-controls] + .top-tabs__panel (per tab)
	 */
	function initTopTabs() {
	  document.querySelectorAll('[data-top-tabs]').forEach(root => {
	    const pills = Array.from(root.querySelectorAll('.top-tabs__pill'));
	    function show(selected) {
	      pills.forEach(pill => {
	        const open = pill === selected;
	        const panel = document.getElementById(pill.getAttribute('aria-controls'));
	        pill.classList.toggle('is-active', open);
	        pill.setAttribute('aria-expanded', open ? 'true' : 'false');
	        if (panel) {
	          panel.classList.toggle('is-active', open);
	          panel.hidden = !open;
	        }
	      });
	    }
	    pills.forEach(pill => {
	      pill.addEventListener('click', () => show(pill));
	    });
	  });
	}

	/**
	 * Stats counter: each .stats-counter__number is printed at its final value; as
	 * its block scrolls into view it counts up from 0 to that value and stops on
	 * it. Nothing happens for visitors who prefer reduced motion, so they see the
	 * final numbers. Markup: <span class="stats-counter__number" data-count="246" data-decimals="0">
	 */
	const DURATION = 2000;
	function ease(t) {
	  return 1 - Math.pow(1 - t, 3);
	}
	function run(el) {
	  const target = parseFloat(el.getAttribute('data-count'));
	  const decimals = parseInt(el.getAttribute('data-decimals'), 10) || 0;
	  const start = performance.now();
	  function frame(now) {
	    const t = Math.min((now - start) / DURATION, 1);
	    el.textContent = (target * ease(t)).toFixed(decimals);
	    if (t < 1) {
	      requestAnimationFrame(frame);
	    } else {
	      el.textContent = target.toFixed(decimals);
	    }
	  }
	  requestAnimationFrame(frame);
	}
	function initStatsCounters() {
	  const numbers = document.querySelectorAll('.stats-counter__number');
	  if (!numbers.length || window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
	    return;
	  }
	  const observer = new IntersectionObserver(entries => {
	    entries.forEach(entry => {
	      if (entry.isIntersecting) {
	        run(entry.target);
	        observer.unobserve(entry.target);
	      }
	    });
	  }, {
	    threshold: 0.6
	  });
	  numbers.forEach(el => {
	    const decimals = parseInt(el.getAttribute('data-decimals'), 10) || 0;
	    el.textContent = 0 .toFixed(decimals);
	    observer.observe(el);
	  });
	}

	/**
	 * Sector cards: the All / Private sector / Public sector buttons show only the
	 * cards whose data-groups include the chosen group. The buttons are hidden
	 * until this script runs (without it every card simply shows).
	 */
	function initSectorFilters() {
	  document.querySelectorAll('.sector-cards').forEach(root => {
	    const bar = root.querySelector('.sector-cards__filters');
	    const buttons = Array.from(root.querySelectorAll('.sector-cards__filter'));
	    const cards = Array.from(root.querySelectorAll('.sector-cards__card'));
	    if (!bar || !buttons.length) {
	      return;
	    }
	    bar.hidden = false;
	    buttons.forEach(button => {
	      button.addEventListener('click', () => {
	        const filter = button.getAttribute('data-filter');
	        buttons.forEach(other => {
	          const on = other === button;
	          other.classList.toggle('is-active', on);
	          other.setAttribute('aria-pressed', on ? 'true' : 'false');
	        });
	        cards.forEach(card => {
	          const groups = (card.getAttribute('data-groups') || '').split(' ');
	          card.hidden = 'all' !== filter && !groups.includes(filter);
	        });
	      });
	    });
	  });
	}

	/**
	 * Product cards: the type buttons show only the cards whose data-types include
	 * the chosen type. The buttons are hidden until this script runs (without it
	 * every card simply shows). ?q=type-slug opens with that type chosen.
	 */
	function initProductFilters() {
	  document.querySelectorAll('.product-cards').forEach(root => {
	    const bar = root.querySelector('.product-cards__filters');
	    const buttons = Array.from(root.querySelectorAll('.product-cards__filter'));
	    const cards = Array.from(root.querySelectorAll('.product-cards__card'));
	    if (!bar || !buttons.length) {
	      return;
	    }
	    function choose(filter) {
	      buttons.forEach(button => {
	        const on = button.getAttribute('data-filter') === filter;
	        button.classList.toggle('is-active', on);
	        button.setAttribute('aria-pressed', on ? 'true' : 'false');
	      });
	      cards.forEach(card => {
	        card.hidden = 'all' !== filter && !(card.getAttribute('data-types') || '').split(' ').includes(filter);
	      });
	    }
	    bar.hidden = false;
	    buttons.forEach(button => button.addEventListener('click', () => choose(button.getAttribute('data-filter'))));
	    const wanted = new URLSearchParams(window.location.search).get('q');
	    if (wanted && buttons.some(button => button.getAttribute('data-filter') === wanted)) {
	      choose(wanted);
	    }
	  });
	}

	document.addEventListener('DOMContentLoaded', () => {
	  initSmoothScroll();
	  initNavToggle();
	  initNavDropdowns();
	  initSiteNav();
	  initDialogs();
	  initVideoFacades();
	  initScrollAnimate();
	  initSideTabs();
	  initTopTabs();
	  initStatsCounters();
	  initSectorFilters();
	  initProductFilters();
	  initFormFrames();
	});

})();
//# sourceMappingURL=theme.js.map
