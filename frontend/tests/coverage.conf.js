module.exports = function (config) {
    config.set({
        singleRun: true,
        basePath: '../..',
        frameworks: ['jasmine'],
        browsers: ['PhantomJS'],
        files: [
            'public/vendor.mserrano.js',
            'library/javascript/angular-mocks.js',
            'frontend/src/**/*.js',
            'tmp/trash/template.scratch',
            'frontend/tests/helpers/helpers.*.js',
            'frontend/tests/src/**/*.spec.js',
        ],
        reporters: ['progress', 'coverage'],
        preprocessors: {
            'frontend/src/**/*.js': ['coverage'],
        },
        coverageReporter: {
            type: 'html',
            dir: 'tmp/js-coverage-report',
            subdir: '.',
        },
    });
};