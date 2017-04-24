(function() {
    'use strict';

    angular
        .module('syslog')
        .config(Syslog);

    Syslog.$inject = ['$stateProvider', '$urlRouterProvider'];

    function Syslog($stateProvider, $urlRouterProvider) {
        $stateProvider
            .state('syslog', {
                url: '/syslog',
                templateUrl: 'Syslog/Views/SyslogConfig.html',
                controller: 'SyslogCtrl',
                controllerAs: 'syslog'
            });
    }
})();