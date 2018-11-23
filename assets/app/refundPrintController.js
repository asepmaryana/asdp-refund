angular.module('app.controller', ['ui.bootstrap'])
.controller('RefundPrintController', ['$rootScope','$scope','$http','$cookies','$state','$timeout', function($rootScope,$scope,$http,$cookies,$state,$timeout){
    $scope.total	= 0;
    $scope.nominal	= 0;
	$scope.kode		= '';
	$scope.mode		= '';
	$scope.refund	= null;
	
	$scope.lookup			= function(kode) {
		$http.get(BASE_URL+'/api/tiketRefund/cetak/'+kode)
		.success(function(res){
			$scope.mode	= 'print';
			$scope.refund	= res.data;
			$scope.refund.success = res.success;
			$scope.refund.message = res.message;
			$scope.refund.rute	= res.data.asal+' - '+res.data.tujuan;
		})
		.error(function(res){
			$scope.mode	= '';
			$scope.refund	= null;
			swal('Exception', res.message);
		});
		$scope.kode = '';
	}
	$scope.clear	= function(){
		$scope.mode	= '';
		$scope.refund	= null;
		$scope.kode	= '';
	}
	$scope.enterPressed = function (keyEvent) {
		if (keyEvent.keyCode == 13) $scope.lookup($scope.kode);
	};
	$scope.print	= function(){
		var mode = 'iframe'; //popup
        var close = mode == "popup";
        var options = {mode: mode,popClose: close};
        $("#printTiketRefund").printArea(options);
	}
	$scope.finish	= function(){
		$scope.clear();
	}
}])
;