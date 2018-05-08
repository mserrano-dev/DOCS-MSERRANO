angular.module("docs.routing")
        .config(function ($routeProvider) {
            $routeProvider.when('/', {
                templateUrl: 'frontend/src/pages/sitedown/sitedown.html',
                controller: 'SiteDownCtrl',
                controllerAs: 'page',
            });

            $routeProvider.otherwise({redirectTo: '/'});
        });