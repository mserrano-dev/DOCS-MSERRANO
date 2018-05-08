// ======================================
// # Javascript/Template Source Files
// ======================================
helper = require('./_helper');

// load src javascript and templates
var require_src_file = require.context('../src/', true, /.+\.(js|html)$/);
helper.load_all(require_src_file);