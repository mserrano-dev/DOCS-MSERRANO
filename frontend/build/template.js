// ======================================
// # Necessary for JS coverage tests 
// ======================================
helper = require('./_helper');

// load all html in src directory
var require_all_template = require.context('../src/', true, /.+\.(html)$/);
helper.load_all(require_all_template);