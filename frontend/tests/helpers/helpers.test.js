"use strict";

(function () {
    var app_name = "docs"; //APP_NAME

    window.helper = (window.helper || {});
    window.helper.Const = null;
    window.helper.test = (window.helper.test || {});

    window.helper.test.setUp = test_setUp;
    window.helper.test.tearDown = test_tearDown;

    // -- Functions -- //
    function test_setUp(override) {
        var _const = {};

        fill_const(_const, "Default.Date", rand_str);
        fill_const(_const, "Infrastructure.Result", rand_str);
        fill_const(_const, "Infrastructure.Pass", rand_str);
        fill_const(_const, "Infrastructure.Fail", rand_str);

        _const = angular.merge({}, _const, override);
        window.helper.Const = _const;

        module(app_name, function ($provide) {
            $provide.value("Const", _const);
        });
    }

    function test_tearDown() {
        inject(function ($httpBackend) {
            $httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
        });
    }

    // -- Helpers -- //
    function fill_const(obj, path, generator) {
        var list_path = path.split(".");
        var step = obj;
        for (var i = 0; i < list_path.length; ++i) {
            var item = list_path[i];
            if (i === list_path.length - 1) {
                step[item] = generator();
            } else {
                if (angular.isDefined(step[item]) === false) {
                    step[item] = {};
                }
                step = step[item];
            }
        }
    }

    function rand_str() {
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        var _return = "";
        for (var i = 0; i < 7; ++i) {
            _return += possible[Math.floor(Math.random() * (possible.length - 1))];
        }
        return _return;
    }
})();