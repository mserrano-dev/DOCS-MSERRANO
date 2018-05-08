angular.module("docs.services")
        .service("Midtier", function (Const, $http, Request) {
            var self; // "As much on the midtier as possible"

            // -- Service -- //
            return (self = {
                docs_constants: new Request.cached(function () {
                    var get = {}, post = {};
                    var config = {
                        method: 'GET',
                        url: '/docs_constants',
                        params: get,
                        data: post,
                    };

                    return $http(config);
                }),
                checker: new Request.standard(function () {
                    var get = {}, post = {};
                    var config = {
                        method: 'GET',
                        url: '/checker',
                        params: get,
                        data: post,
                    };

                    return $http(config);
                }),
            });

        });