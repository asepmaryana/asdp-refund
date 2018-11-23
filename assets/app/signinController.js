'use strict';

angular.module('app.controller', ['app.constant', 'ngCookies'])
.controller('SigninController', ['$rootScope','$scope','$state','$http','$cookies', function ($rootScope,$scope,$state,$http,$cookies){
	$rootScope.$broadcast('timer-disabled', {});
	if($cookies.token != null) $state.go('app.dashboard');
	$scope.data	= {username:'', password:'', remember:'0'};
	$scope.enterPressed = function (keyEvent) {
		if (keyEvent.keyCode == 13) $scope.login($scope.data);
	};
	$scope.login	= function(data) {
		if(data.username == '') swal('Exception', 'Username belum diisi !');
		else if(data.password == '') swal('Exception', 'Password belum diisi !');
		else 
		{
			$http.post(BASE_URL + '/api/auth/login', data)
			.success(function(res){
				$rootScope.id_peran 		= res.id_peran;
				$cookies.token 				= res.token;
				$http.defaults.headers.common['X-Authorization'] = res.token;
				if(res.id_peran == 3) $state.go('app.dashboard');
				else $state.go('app.refund');
				$rootScope.$broadcast('login-succeed', {});
			})
			.error(function(res){
				swal('Exception', res.message);
			});
		}
	};
}])
;