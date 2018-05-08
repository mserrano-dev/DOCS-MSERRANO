angular.module("docs.services")
        .service("Request", function (Const) {
            var self, memory = [];

            // -- Service -- //
            return (self = {
                cached: RequestCached,
                standard: RequestStandard,
            });

            // -- Functions -- //
            function is_valid(container) {
                var std_resp = container.data;
                return (std_resp[Const.Infrastructure.Result] !== Const.Infrastructure.Fail);
            }
            function is_loaded(container) {
                return (container.data !== null);
            }
            function get_data_key(container, key) {
                return container.data[key];
            }
            function record_data(container, response) {
                container.data = response.data;
            }
            function clear_cache(container) {
                container.promise = null;
            }
            function call_standard(container, request_fn) {
                var fargs = angular.extend([], arguments);
                fargs.splice(0, 2);
                return request_fn.apply(null, fargs)
                        .then(record_data.bind(null, container));
            }
            function call_cached(container, request_fn) {
                if (container.promise === null) {
                    container.promise = call_standard.apply(null, arguments);
                }
                return container.promise;
            }

            // -- Objects -- //
            function RequestCached(request_fn) {
                var index = memory.length;
                memory.push({promise: null, data: null});

                this.is_valid = is_valid.bind(null, memory[index]);
                this.is_loaded = is_loaded.bind(null, memory[index]);
                this.get_data = get_data_key.bind(null, memory[index]);
                this.call = call_cached.bind(null, memory[index], request_fn);
                this.clear_cache = clear_cache.bind(null, memory[index]);
            }
            function RequestStandard(request_fn) {
                var index = memory.length;
                memory.push({promise: null, data: null});

                this.is_valid = is_valid.bind(null, memory[index]);
                this.is_loaded = is_loaded.bind(null, memory[index]);
                this.get_data = get_data_key.bind(null, memory[index]);
                this.call = call_standard.bind(null, memory[index], request_fn);
            }
        });