'use strict';

/**
 * Copy the third-party browser libraries out of node_modules into js/vendor/,
 * so they are served from this site, not a CDN. The versions are the ones in
 * package.json; js/vendor/ is committed, like the rest of the compiled output.
 *
 *   npm run vendor
 */

const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '../..');
const files = [
	['lenis/dist/lenis.min.js', 'lenis.min.js'],
	['gsap/dist/gsap.min.js', 'gsap.min.js'],
	['gsap/dist/ScrollTrigger.min.js', 'ScrollTrigger.min.js'],
	['embla-carousel/embla-carousel.umd.js', 'embla-carousel.umd.js'],
];

fs.mkdirSync(path.join(root, 'js/vendor'), { recursive: true });

files.forEach(([from, to]) => {
	const source = path.join(root, 'node_modules', from);
	const pkg = require(path.join(root, 'node_modules', from.split('/')[0], 'package.json'));
	// The source map is not shipped, so drop the comment that points at it.
	const code = fs.readFileSync(source, 'utf8').replace(/\n?\/\/# sourceMappingURL=.*\s*$/, '\n');
	const header = from.startsWith('lenis') || from.startsWith('embla') ? `/*! ${pkg.name} ${pkg.version} (MIT) */\n` : '';

	fs.writeFileSync(path.join(root, 'js/vendor', to), header + code);
	console.log(`js/vendor/${to}  (${pkg.name} ${pkg.version})`);
});
