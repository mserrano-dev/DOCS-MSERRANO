describe("!! Midtier.js !! ", function () {
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
    var $httpBackend;
    beforeEach(function () {
        inject(function (_$httpBackend_) {
            $httpBackend = _$httpBackend_;
        });
    });

    // setUp - test obj (Service)
    function get_test_obj() {
        var _return;
        inject(function (_Midtier_) {
            _return = _Midtier_;
        });
        return _return;
    }

    // setUp - test case
    var rnd;
    beforeEach(function () {
        rnd = helper.number.rand();
    });

    it("should exist", function () {
        obj = get_test_obj();
        expect(obj).toBeDefined();
    });

    describe("Request", function () {
        beforeEach(function () {
            obj = get_test_obj();
        });

        describe("docs_constants", function () {
            var res;
            beforeEach(function () {
                res = obj.docs_constants;
            });

            it("should be cached", function () {
                helper.expect.cached(res);
            });

            it("should have no get or post data", function () {
                var exp_get = {};
                var exp_post = {};
                helper.expect.get_request(res, exp_get, exp_post);
            });
        });

        describe("checker", function () {
            var res;
            beforeEach(function () {
                res = obj.checker;
            });

            it("should be standard", function () {
                helper.expect.standard(res);
            });

            it("should have no get or post data", function () {
                var exp_get = {};
                var exp_post = {};
                helper.expect.get_request(res, exp_get, exp_post);
            });
        });
    });
});