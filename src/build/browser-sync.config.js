module.exports = {
	proxy: 'pltest.local/',
	host: 'pltest.local',
	open: 'external',
	notify: false,
	files: ['./css/*.min.css', './js/*.min.js', './**/*.php'],
};
