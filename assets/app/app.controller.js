'use strict';

angular.module('app.controller', ['app.constant', 'ngCookies'])
.controller('RootController', ['$rootScope','$scope','$http','$timeout','$cookies','EVENTS', function ($rootScope,$scope,$http,$timeout,$cookies,EVENTS) {
	$rootScope.authenticated	= false;
	$rootScope.user = {};
	$rootScope.isAuthenticated	= function(){
		return $rootScope.authenticated;
	}
	$scope.logout = function () {
		swal({
			title: "Konfirmasi",
			text: "Apakah anda mau keluar ?",
			icon: "warning",
			buttons: true,
			dangerMode: true,
			showCancelButton: true,
		    confirmButtonColor: '#DD6B55',
		    confirmButtonText: 'Ya',
		    cancelButtonText: 'Tidak',
		    closeOnConfirm: true,
		    closeOnCancel: true
		},
		function(isConfirm){
			if (isConfirm) {
				$http.get(BASE_URL + '/api/auth/logout')
				.success(function (res) {
					swal('Success', 'Anda telah berhasil logout.');
					$rootScope.$broadcast('session-expired', {});
				})
				.error(function (res) {
					$rootScope.$broadcast('session-expired', {});
				});
			}
		});
	};	
	$scope.$on(EVENTS.notAuthorized, function(event) {
		//swal('Exception', 'Anda tidak diijinkan untuk membuka resource tersebut.');
		$rootScope.$broadcast('session-expired', {});
	});
	$scope.$on(EVENTS.notAuthenticated, function(event) {
		swal('Exception', 'Sesi anda telah berakhir, silahkan login kembali.');
		$rootScope.$broadcast('session-expired', {});
	});
	$scope.$on(EVENTS.internalError, function(event) {
		swal('Exception', 'Error di sisi server.');
	});
	$scope.$on(EVENTS.profileChanged, function(event, args) {
		$rootScope.user = args.user;
	});
	$scope.redirect	= function(milis) {
		$timeout( function(){ window.location.href = BASE_URL; }, milis);
	}	
}])
;