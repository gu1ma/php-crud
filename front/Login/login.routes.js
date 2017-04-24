(function() {
    'use strict';

    angular
        .module('login')
        .config(Login);

    Login.$inject = ['$stateProvider', '$urlRouterProvider'];

    function Login($stateProvider, $urlRouterProvider) {
        $stateProvider
            .state('login', {
                url: '/login',
                templateUrl: 'Login/Views/Login.html',
                controller: 'LoginCtrl',
                controllerAs: 'login'
            });
    }
})();