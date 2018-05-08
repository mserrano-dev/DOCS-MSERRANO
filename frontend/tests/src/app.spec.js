describe("!! app.js !! ", function () {
    "use strict";

    // setUp - constants
    beforeEach(function () {
        helper.test.setUp({});
    });

    // setUp - test case
    var rnd;
    beforeEach(function () {
        rnd = helper.number.rand();
    });

    // teardown
    var obj;
    afterEach(function () {
        obj = null;

        helper.test.tearDown();
    });

    it("should exist", function () {
        helper.expect.module_to_exist("docs");
    });

    describe("module", function () {
        beforeEach(function () {
            obj = angular.module("docs");
        });

        describe("docs.routing", function () {
            it("should be in dependency list", function () {
                helper.expect.in_array("docs.routing", obj.requires);
            });

            it("should be instantiated", function () {
                helper.expect.module_to_exist("docs.routing");
            });

            it("should use ngRoute", function () {
                obj = angular.module("docs.routing");
                helper.expect.in_array("ngRoute", obj.requires);
            });
        });

        describe("docs.constants", function () {
            it("should be in dependency list", function () {
                helper.expect.in_array("docs.constants", obj.requires);
            });

            it("should be instantiated", function () {
                helper.expect.module_to_exist("docs.constants");
            });
        });

        describe("docs.controllers", function () {
            it("should be in dependency list", function () {
                helper.expect.in_array("docs.controllers", obj.requires);
            });

            it("should be instantiated", function () {
                helper.expect.module_to_exist("docs.controllers");
            });
        });

        describe("docs.services", function () {
            it("should be in dependency list", function () {
                helper.expect.in_array("docs.services", obj.requires);
            });

            it("should be instantiated", function () {
                helper.expect.module_to_exist("docs.services");
            });
        });
    });
});