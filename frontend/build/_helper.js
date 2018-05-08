// ======================================
// # Build Helper Functions
// ======================================
(function () {
    module.exports = {
        load_all: load_all,
        load_and_remove: load_and_remove,
        load_selected: load_selected,
    };

    // -- Functions -- //
    function load_all(require_all) {
        var list_file = require_all.keys();
        load_selected(require_all, list_file);
    }
    function load_and_remove(require_all, to_remove) {
        var list_file = require_all.keys();
        var remove_it = list_file.splice(list_file.indexOf(to_remove), 1);
        require_all(remove_it[0]);

        return list_file;
    }
    function load_selected(require_all, list_file) {
        for (var i = 0; i < list_file.length; i++) {
            var key = list_file[i];
            require_all(key);
        }
    }
})();
