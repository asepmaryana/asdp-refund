'use strict';

angular.module('app', ['ui.router', 'oc.lazyLoad', 'ngAnimate', 'ngCookies', 'ui.bootstrap', 'app.controller', 'app.directive', 'angularMoment'])
.run(function($rootScope, $state, $interval, $timeout, $cookies, $http, amMoment) {	
	amMoment.changeLocale('id');
	$rootScope.$on('loading:show', function() { $(".preloader").show(); });
	$rootScope.$on('loading:hide', function() { $(".preloader").hide(); });
	$rootScope.$on('$locationChangeStart', function(event, next, prev) {});
	$rootScope.$on('login-succeed', function(event, args) {
		$rootScope.authenticated	= true;
		$http.get(BASE_URL+'/api/auth/info').success(function(data){
			$rootScope.user = data;
		});
	});
	$rootScope.$on('session-expired', function(event, args) {
		$rootScope.$broadcast('timer-disabled', {});
		$rootScope.authenticated	= false;
		delete $rootScope.user;
		delete $cookies.token;
		delete $http.defaults.headers.common['X-Authorization'];
		$timeout( function(){ window.location.href = BASE_URL; }, 1000);
	});
	$rootScope.$on('auth-not-authenticated', function(event, args) {
		$rootScope.authenticated	= false;
		delete $rootScope.user;
		delete $cookies.token;
		$state.go('app.signin');
	});
	$rootScope.$on('timer-disabled', function(event, args) {
		if (angular.isDefined($rootScope.dashTimer)) $interval.cancel($rootScope.dashTimer);
	});
})
.filter('tanggal', function () { 
    return function (text) {    	
        return (text == '0000-00-00') ? '' : moment(text).format('DD-MM-YYYY');
    };    
})
.config(function($stateProvider,$urlRouterProvider,$httpProvider) {
    $urlRouterProvider.otherwise('/app/home');
    $stateProvider
	    .state('app', {
	    	abstract: true,
	    	url: '/app',
	    	templateUrl: 'assets/views/root.html',
	    	controller: 'RootController'
	    })
	    .state('app.home', {
	    	url: '/home',
	    	parent: 'app',
	    	views:{
				'page':{
					templateUrl: 'assets/views/home.html'
				}
			}
	    })
	    .state('app.refund', {
        	url: '/refund',
        	parent: 'app',        	
	    	views:{
				'page':{
					templateUrl: 'assets/views/refund/refund.html',
			    	controller: 'RefundController'
				}
			},
        	resolve: {
        		loadModule: ['$ocLazyLoad', function($ocLazyLoad){
					return $ocLazyLoad.load(['assets/app/refundController.js']);
				}]
        	}
        })
        .state('app.submit', {
        	url: '/submit/:idBooking/:idTiket',
        	parent: 'app',        	
	    	views:{
				'page':{
					templateUrl: 'assets/views/refund/submit.html',
			    	controller: 'SubmitController'
				}
			},
        	resolve: {
        		loadModule: ['$ocLazyLoad', function($ocLazyLoad){
					return $ocLazyLoad.load(['assets/app/submitController.js']);
				}]
        	}
        })
        .state('app.status', {
        	url: '/status',
        	parent: 'app',        	
	    	views:{
				'page':{
					templateUrl: 'assets/views/refund/status.html',
			    	controller: 'RefundStatusController'
				}
			},
        	resolve: {
        		loadModule: ['$ocLazyLoad', function($ocLazyLoad){
					return $ocLazyLoad.load(['assets/app/refundStatusController.js']);
				}]
        	}
        })
        .state('app.signin', {
        	url: '/signin',
        	parent: 'app',        	
	    	views:{
				'page':{
					templateUrl: 'assets/views/signin.html',
			    	controller: 'SigninController'
				}
			},
        	resolve: {
        		loadModule: ['$ocLazyLoad', function($ocLazyLoad){
					return $ocLazyLoad.load(['assets/app/signinController.js']);
				}]
        	}
        })
        .state('app.dashboard', {
        	url: '/dashboard',
        	parent: 'app',        	
	    	views:{
				'page':{
					templateUrl: 'assets/views/dashboard.html',
			    	controller: 'DashboardController'
				}
			},
        	resolve: {
        		loadModule: ['$ocLazyLoad', function($ocLazyLoad){
					return $ocLazyLoad.load(['assets/app/dashboardController.js']);
				}]
        	}
        })
        .state('app.refundList', {
        	url: '/refund/list/:idStatus',
        	parent: 'app',        	
	    	views:{
				'page':{
					templateUrl: 'assets/views/refund/refundList.html',
			    	controller: 'RefundListController'
				}
			},
        	resolve: {
        		loadModule: ['$ocLazyLoad', function($ocLazyLoad){
					return $ocLazyLoad.load(['assets/app/refundListController.js']);
				}]
        	}
        })
        .state('app.refundView', {
        	url: '/refund/view/:idRefund/:idDetail',
        	parent: 'app',        	
	    	views:{
				'page':{
					templateUrl: 'assets/views/refund/refundView.html',
			    	controller: 'RefundViewController'
				}
			},
        	resolve: {
        		loadModule: ['$ocLazyLoad', function($ocLazyLoad){
					return $ocLazyLoad.load(['assets/app/refundViewController.js']);
				}]
        	}
        })
        .state('app.refundPrint', {
        	url: '/refund/print',
        	parent: 'app',
			views:{
				'page':{
					templateUrl: 'assets/views/refund/refundPrint.html',
					controller: 'RefundPrintController',
				}
			},
        	resolve: {
        		loadModule: ['$ocLazyLoad', function($ocLazyLoad){
					return $ocLazyLoad.load(['assets/app/refundPrintController.js']);
				}]
        	}
        })
        ;
    
    $httpProvider.interceptors.push(['$rootScope', '$q', '$cookies', 'EVENTS', function ($rootScope, $q, $cookies, EVENTS) {
		return {
			'request': function (config) {
				$rootScope.$broadcast('loading:show');
				config.headers = config.headers || {};
				return config;
			},
			'response': function(response) {
				$rootScope.$broadcast('loading:hide');
				return response;
			},
			'responseError': function (response) {
				$rootScope.$broadcast('loading:hide');
				$rootScope.$broadcast({
					401: EVENTS.notAuthenticated,
					403: EVENTS.notAuthorized,
					500: EVENTS.internalError
				}[response.status], response);
								
				return $q.reject(response);
			}
		};
    }]);
    
  })
  ;