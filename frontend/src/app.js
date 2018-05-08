angular.module("docs", [
    "pascalprecht.translate",
    "ngSanitize",
    "ngMaterial",
    "ngAnimate",
    "ngAria",
    //
    "docs.blocks",
    "docs.constants",
    "docs.controllers",
    "docs.routing",
    "docs.services",
]);

angular.module("docs.blocks", []);
angular.module("docs.constants", []);
angular.module("docs.controllers", []);
angular.module("docs.routing", ["ngRoute"]);
angular.module("docs.services", []);