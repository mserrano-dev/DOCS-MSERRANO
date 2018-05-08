describe("!! Request.js !! ", function () {
    "use strict";

    // tearDown
    var obj;
    afterEach(function () {
        helper.test.tearDown();
        obj = null;
    });

    // setUp - constants
    beforeEach(function () {
        helper.test.setUp({});
    });

    // setUp - services
    var $httpBackend, Request, $http, $q;
    beforeEach(function () {
        inject(function (_$httpBackend_, _Request_, _$http_, _$q_) {
            $httpBackend = _$httpBackend_;
            Request = _Request_;
            $http = _$http_;
            $q = _$q_;
        });
    });

    // setUp - test obj (Service)
    function get_test_obj() {
        var _return;
        inject(function (_Request_) {
            _return = _Request_;
        });
        return _return;
    }

    // setUp - test case
    var rnd;
    beforeEach(function () {
        rnd = helper.number.rand();
    });

    // Service
    it("should exist", function () {
        obj = get_test_obj();
        expect(obj).toBeDefined();
    });

    describe("object", function () {
        describe("standard", function () {
            it("should exist", function () {
                obj = new Request.standard(angular.noop);
                expect(obj).toBeDefined();
            });

            it("should invoke an AJAX call", function () {
                var triggered = 0;
                obj = new Request.standard(function () {
                    triggered += 1;

                    var deferred = $q.defer();
                    deferred.resolve(helper.request.pass());

                    return deferred.promise;
                });
                obj.call().then(function () {
                    expect(triggered).toEqual(1);

                    return obj.call();
                }).then(function () {
                    expect(triggered).toEqual(2);
                });
            });
        });

        describe("cached", function () {
            it("should exist", function () {
                obj = new Request.cached(angular.noop);
                expect(obj).toBeDefined();
            });

            it("should make same AJAX call once", function () {
                var triggered = 0;
                obj = new Request.cached(function () {
                    triggered += 1;

                    var deferred = $q.defer();
                    deferred.resolve(helper.request.pass());

                    return deferred.promise;
                });
                obj.call().then(function () {
                    expect(triggered).toEqual(1);

                    return obj.call();
                }).then(function () {
                    expect(triggered).toEqual(1);

                    return obj.call();
                }).then(function () {
                    expect(triggered).toEqual(1);
                });
            });

            it("should be able to clear cache", function () {
                var triggered = 0;
                obj = new Request.cached(function () {
                    triggered += 1;

                    var deferred = $q.defer();
                    deferred.resolve(helper.request.pass());

                    return deferred.promise;
                });
                obj.call().then(function () {
                    expect(triggered).toEqual(1);

                    return obj.call();
                }).then(function () {
                    expect(triggered).toEqual(1);

                    obj.clear_cache();
                    return obj.call();
                }).then(function () {
                    expect(triggered).toEqual(2);
                });
            });
        });
    });

    describe("core functionality:", function () {
        beforeEach(function () {
            obj = new Request.cached(function () {
                var get = {}, post = {};
                var config = {
                    method: "GET",
                    url: "/unittests",
                    params: get,
                    data: post,
                };

                return $http(config);
            });
        });

        describe("is_valid", function () {
            it("should pass", function () {
                $httpBackend.expect("GET", "/unittests")
                        .respond(200, helper.request.pass());

                obj.call().then(function () {
                    var res = obj.is_valid();
                    expect(res).toEqual(true);
                });
                $httpBackend.flush();
            });

            it("should fail", function () {
                $httpBackend.expect("GET", "/unittests")
                        .respond(200, helper.request.fail());

                obj.call().then(function () {
                    var res = obj.is_valid();
                    expect(res).toEqual(false);
                });
                $httpBackend.flush();
            });
        });

        describe("is_loaded", function () {
            it("should be true", function () {
                $httpBackend.expect("GET", "/unittests")
                        .respond(200, helper.request.pass());

                obj.call().then(function () {
                    var res = obj.is_loaded();
                    expect(res).toEqual(true);
                });
                $httpBackend.flush();
            });

            it("should be false", function () {
                var res = obj.is_loaded();
                expect(res).toEqual(false);
            });
        });

        describe("get_data_key", function () {
            it("should return data", function () {
                var data = {};
                data[rnd + 0] = (rnd + 1); // random data
                $httpBackend.expect("GET", "/unittests")
                        .respond(200, helper.request.pass(data));

                obj.call().then(function () {
                    var res = obj.get_data(rnd + 0);
                    expect(res).toEqual(rnd + 1);
                });
                $httpBackend.flush();
            });

            it("should be undefined", function () {
                var data = {};
                // empty data
                $httpBackend.expect("GET", "/unittests")
                        .respond(200, helper.request.pass(data));

                obj.call().then(function () {
                    var res = obj.get_data(rnd + 0);
                    expect(res).toBeUndefined();
                });
                $httpBackend.flush();
            });
        });
    });
});