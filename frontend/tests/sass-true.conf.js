var path = require('path');
var sassTrue = require('sass-true');
var describe = require('describe');
var it = require('it');

function importer(url) {
    var _return = {file: _return};

    var true_lib_file = /^true$|^true\/.+$/;
    var spec_scss_file = /^\_.+\.spec$/;
    if (true_lib_file.test(url) === true) {
        _return.file = path.join('../../../node_modules/sass-true/sass/', url);
    } else if (spec_scss_file.test(url) === true) {
        _return.file = path.join('../../../frontend/tests/styles/', url);
    } else { // must be src file
        _return.file = path.join('../../../library/styles/', url);
    }
    return _return;
}

var entry_file = path.join(__dirname, 'styles/Const.spec.scss');
sassTrue.runSass({importer, file: entry_file}, describe, it);