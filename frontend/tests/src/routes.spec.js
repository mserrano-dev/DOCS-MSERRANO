describe("!! routes.js !! ", function () {
    "use strict";

    // setUp - constants
    beforeEach(function () {
        helper.test.setUp({});
    });

    // setUp - services
    var $rootScope, $route, $controller, $templateCache, $location;
    beforeEach(function () {
        inject(function (_$rootScope_, _$route_, _$controller_, _$templateCache_, _$location_) {
            $rootScope = _$rootScope_;
            $route = _$route_;
            $controller = _$controller_;
            $templateCache = _$templateCache_;
            $location = _$location_;
        });
    });

    // setUp - test case
    var rnd;
    beforeEach(function () {
        rnd = helper.number.rand();
    });

    // tearDown
    var obj;
    afterEach(function () {
        obj = null;

        helper.test.tearDown();
    });

    it("should exist", function () {
        expect($route).toBeDefined();
    });

    describe("route", function () {
        describe("sitedown", function () {
            var path = "/";
            beforeEach(function () {
                obj = $route.routes[path];
            });

            it("should be defined", function () {
                var err_msg = "Expected route \"" + path + "\" to be defined";
                expect(obj).toBeDefined(err_msg);

                if (angular.isDefined(obj) === true) {
                    $location.path(path);
                    expect($route.current).toBeUndefined();
                    $rootScope.$digest();
                    expect($route.current.controller).toEqual("SiteDownCtrl");
                }
            });

            it("should have valid templateUrl", function () {
                var res = $templateCache.get(obj.templateUrl);
                expect(res).toEqual(jasmine.any(String));
            });

            it("should have valid controller", function () {
                expect(obj.controller).toEqual(jasmine.any(String));
            });
        });

        describe("otherwise", function () {
            it("should redirect to Landing page", function () {
                $location.path("/unrecognized-page-DNE");
                expect($route.current).toBeUndefined();
                $rootScope.$digest();
                expect($route.current.controller).toEqual("SiteDownCtrl");
            });
        });
    });
});