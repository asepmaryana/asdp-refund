'use strict';

angular.module('app.controller', ['app.constant'])
.controller('RefundStatusController', ['$rootScope','$scope','$http', function ($rootScope,$scope,$http){
	$rootScope.$broadcast('timer-disabled', {});
	$scope.tikets = [];
    $scope.total	= 0;
    $scope.nominal	= 0;
	$scope.data		= null;
	$scope.kode		= '';
	$scope.refund	= null;
    
	$scope.lookup			= function(kode) {		
		//console.log(kode);
		$scope.booking	= kode;
		$http.get(BASE_URL+'/api/tiketRefund/cek/'+kode)
		.success(function(res){
			$scope.data = res.data;
			$scope.data.success = res.success;
			$scope.data.message = res.message;
			$scope.data.rute = res.data.asal+' - '+res.data.tujuan;
			if(angular.isDefined(res.data.rekening)) $scope.data.rekening	= res.data.rekening + " ("+res.data.nama_bank+")";
		})
		.error(function(res){
			$scope.data	= res;
			$scope.data.success = res.success;
			$scope.data.message = res.message;
            $scope.total 	= 0;
            $scope.nominal	= 0;
		});
		$scope.kode = '';
	}
	$scope.clear	= function(){
		$scope.data	= null;
		$scope.kode	= '';
		$scope.booking = '';
	    $scope.total 	= 0;
        $scope.nominal	= 0;
	}
	$scope.enterPressed = function (keyEvent) {
		if (keyEvent.keyCode == 13) $scope.lookup($scope.kode);
	};
}])
;