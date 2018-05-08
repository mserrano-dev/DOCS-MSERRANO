"use strict";

(function () {
    window.helper = (window.helper || {});
    window.helper.request = (window.helper.request || {});

    window.helper.request.pass = request_pass;
    window.helper.request.fail = request_fail;

    // -- Functions -- //
    function request_pass(override) {
        return request_data(helper.Const.Infrastructure.Pass, override);
    }
    function request_fail(override) {
        return request_data(helper.Const.Infrastructure.Fail, override);
    }

    // -- Helpers -- //
    function request_data(result, override) {
        var standard_resp = {};
        standard_resp[helper.Const.Infrastructure.Result] = result;
        return angular.merge({}, standard_resp, override);
    }
})();