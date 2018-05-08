"use strict";

(function () {
    window.helper = (window.helper || {});
    window.helper.expect = (window.helper.expect || {});

    window.helper.expect.module_to_exist = expect_module_to_exist;
    window.helper.expect.in_array = expect_in_array;
    window.helper.expect.standard = expect_standard;
    window.helper.expect.cached = expect_cached;
    window.helper.expect.get_request = expect_get_request;
    window.helper.expect.post_request = expect_post_request;

    // -- Functions -- //
    function expect_module_to_exist(name) {
        var obj = angular.module(name);
        expect(obj).toBeDefined();
    }
    function expect_in_array(needle, haystack) {
        var err_msg = "expected " + angular.toJson(needle) + " in " + angular.toJson(haystack);
        expect(haystack.indexOf(needle) === -1).toBe(false, err_msg);
    }
    function expect_standard(obj) {
        var res = Object.keys(obj);
        res.sort();
        if (res.length === 4) {
            expect(res).toEqual(["call", "get_data", "is_loaded", "is_valid"]);
        } else {
            expect("cached").toBe("standard");
        }
    }
    function expect_cached(obj) {
        var res = Object.keys(obj);
        res.sort();
        if (res.length === 5) {
            expect(res).toEqual(["call", "clear_cache", "get_data", "is_loaded", "is_valid"]);
        } else {
            expect("standard").toBe("cached");
        }
    }
    function expect_get_request(obj, get, post) {
        expect_request_args(obj, "GET", get, post);
    }
    function expect_post_request(obj, get, post) {
        expect_request_args(obj, "POST", get, post);

        if (angular.equals({}, post) === true) {
            expect("POST data").toEqual("non-empty");
        }
    }

    // -- Helpers -- //
    function expect_request_args(obj, method, get, data) {
        var $httpBackend;
        inject(function (_$httpBackend_) {
            $httpBackend = _$httpBackend_;
        });

        obj.call();

        var url = object_to_query(get);
        $httpBackend.expect(method, url, data)
                .respond(200);
        $httpBackend.flush();
    }
    function object_to_query(get) {
        var _return = "";
        var query = [];
        for (var key in get) {
            query.push(key + "=" + get[key]);
        }
        if (query.length !== 0) {
            _return = new RegExp(".*\?" + query.join("&"));
        }
        return _return;
    }
})();