(function() {
    'use strict';

    angular
        .module('user')
        .config(User);

    User.$inject = ['$stateProvider', '$urlRouterProvider'];

    function User($stateProvider, $urlRouterProvider) {
        $stateProvider
            .state('user', {
                url: '/user',
                templateUrl: 'User/Views/User.html',
                //controller: 'UserCtrl',
                //controllerAs: 'user'
            });
    }
})();