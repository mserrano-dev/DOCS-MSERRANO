angular.module("docs.blocks")
        .config(function ($translateProvider) {
            $translateProvider.useSanitizeValueStrategy('sanitize');
            $translateProvider
                    .preferredLanguage("en");
        });