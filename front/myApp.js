(function() {
    'use strict';

    angular.module('myApp', ['ui.router', 'login', 'user', 'syslog'])
        .config(function ($urlRouterProvider) {
            // Se for inserido uma rota que não existe, o usuário é redirecionado
            // para a rota login.
            $urlRouterProvider.otherwise('/login');
        });
})();
