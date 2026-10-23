'use strict';

const path = require('path');
const { babel } = require('@rollup/plugin-babel');
const banner = require('./banner.js');

/**
 * Three bundles: theme.js (loads on every page); motion.js (the small glue
 * that connects GSAP and ScrollTrigger to Lenis, enqueued only on pages that
 * use them: see cb_peoplesafe2027_enqueue_motion() in inc/enqueue.php); and
 * slider.js (the shared carousel wrapper around Embla, enqueued only on pages
 * with a slider: see cb_peoplesafe2027_enqueue_slider()).
 */
function bundle(name) {
	return {
		input: path.resolve(__dirname, `../js/${name}.js`),
		output: [
			{
				file: path.resolve(__dirname, `../../js/${name}.js`),
				format: 'iife',
				banner: banner(),
			},
			{
				file: path.resolve(__dirname, `../../js/${name}.min.js`),
				format: 'iife',
				banner: banner(),
			},
		],
		plugins: [
			babel({
				babelHelpers: 'bundled',
				configFile: path.resolve(__dirname, 'babel.config.js'),
			}),
		],
	};
}

module.exports = [bundle('theme'), bundle('motion'), bundle('slider')];
