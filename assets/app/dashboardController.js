'use strict';

angular.module('app.controller', ['app.constant', 'ngCookies'])
.controller('DashboardController', ['$rootScope','$scope','$state','$http','$cookies','$interval', function ($rootScope,$scope,$state,$http,$cookies,$interval){
	if($cookies.token == null) $state.go('app.signin');
	
	$rootScope.$broadcast('timer-disabled', {});
	
	$http.get(BASE_URL+'/api/tiketRefund/statistic').success(function(data){
		$scope.refund = data;
	});
	
	$rootScope.dashTimer = $interval(function () {
		$.ajax({
            url: BASE_URL+'/api/tiketRefund/statistic',
            dataType: 'json',
            headers: {'X-Authorization': $cookies.token},
            contentType: 'application/json; charset=utf-8',
            success: function (response) {
            	$scope.refund = data;
            },
            error: function (response) {
				if(response.message == 'Unauthorized'){
					$rootScope.$broadcast('session-expired', {});
				}
            }
        });
	}, 15 * 1000);
	
}])
;