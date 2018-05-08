"use strict";

(function () {
    window.helper = (window.helper || {});
    window.helper.number = (window.helper.expect || {});

    // -- Functions -- //
    window.helper.number.rand = number_random;

    // -- Helpers -- //
    function number_random() {
        return Math.floor(Math.random(0, 200) * 100);
    }
})();