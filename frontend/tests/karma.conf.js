module.exports = function (config) {
    config.set({
        autoWatch: true,
        basePath: '../..',
        browsers: ['PhantomJS'],
        files: [
            'public/vendor.mserrano.js',
            'library/javascript/angular-mocks.js',
            'public/docs.mserrano.js',
            'frontend/tests/helpers/helpers.*.js',
            'frontend/tests/src/**/*.spec.js',
        ],
        frameworks: ['jasmine'],
        reporters: ['dots']
    });
};