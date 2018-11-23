angular.module('app.controller', ['ui.bootstrap'])
.controller('RefundListController', ['$rootScope','$scope','$http','$cookies','$state','$stateParams', function($rootScope,$scope,$http,$cookies,$state,$stateParams){
	$rootScope.$broadcast('timer-disabled', {});
	if($cookies.token == null) $state.go('app.signin');
	var idStatus	= $stateParams.idStatus;
	if(idStatus == '_') idStatus = '';
	//moment().format('YYYY-MM-DD')
	$scope.crit		= {kode_booking:'',kode_refund:'',tanggal:'',id_status_refund:idStatus};
	$scope.status	= [];
	$scope.rows		= [];
	$scope.mode		= '';
	$http.get(BASE_URL+'/api/statusRefund/lists').success(function(data){
		$scope.status = data;
	});
	$scope.buildUrl	= function() {
		
		var kode_booking 	= angular.isDefined($scope.crit.kode_booking) ? $scope.crit.kode_booking : '';
		var kode_refund 	= angular.isDefined($scope.crit.kode_refund) ? $scope.crit.kode_refund : '';
		var tanggal 		= angular.isDefined($scope.crit.tanggal) ? $scope.crit.tanggal : '';
		var status 			= angular.isDefined($scope.crit.id_status_refund) ? $scope.crit.id_status_refund : '';
		
		if(kode_booking == '') kode_booking = '_';
        if(kode_booking != '_') kode_booking = kode_booking.replace(/ /, '_');
        
        if(kode_refund == '') kode_refund = '_';
        if(kode_refund != '_') kode_refund = kode_refund.replace(/ /, '_');
        
		if(tanggal == '') tanggal = '_';
		if(status == '') status = '_';
		
		return BASE_URL+'/api/tiketRefund/lists/'+kode_booking+'/'+kode_refund+'/'+tanggal+'/'+status;
	}
	$scope.view		= function(crit, doc) {
		if(doc == '') {
			$scope.mode		= 'show';
			$http.get($scope.buildUrl()).success(function(data){
				$scope.rows = data;			
			});
		}
		else window.open($scope.buildUrl()+'/'+doc);
	}
	$scope.view($scope.crit,'');
	$scope.print	= function(){
		$scope.mode		= 'print';
		var mode = 'iframe'; //popup
        var close = mode == "popup";
        var options = {mode: mode,popClose: close};
        $("#printableTiketRefund").printArea(options);
	}
}])
;