// ======================================
// # Javascript Vendor Files
// ======================================
helper = require('./_helper');

var require_vendor_js = require.context("../../library/javascript", true, /.+\.js$/);
var list_vendor = helper.load_and_remove(require_vendor_js, './angular.js'); // load AngularJS first
list_vendor.splice(list_vendor.indexOf("./angular-mocks.js"), 1); // remove angular-mocks
helper.load_selected(require_vendor_js, list_vendor); // load all other vendor javascript