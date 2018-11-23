'use strict';

angular.module('app.controller', ['app.constant'])
.controller('RefundController', ['$rootScope','$scope','$http','$location', function ($rootScope,$scope,$http,$location){
	$rootScope.$broadcast('timer-disabled', {});
	$scope.tikets = [];
    $scope.unselectedTiket = [];
    $scope.selectedTiket = [];
    $scope.total	= 0;
    $scope.nominal	= 0;
	$scope.data		= null;
	$scope.kode		= '';
	$scope.refund	= null;
	
	$scope.printSelected  = function(){
		console.log('selected: ');
		console.log($scope.selectedTiket);
		console.log('total = '+$scope.total);
		console.log('refund = '+$scope.nominal);
	}
	
	$scope.selectAllTiket = function($event){
        if($event.target.checked){
            for ( var i = 0; i < $scope.unselectedTiket.length; i++) {
                var p = $scope.unselectedTiket[i];
                if($scope.selectedTiket.indexOf(p.id_trx_tiket_sales_detail) < 0){
                	$scope.tikets.push(p);
                    $scope.selectedTiket.push(p.id_trx_tiket_sales_detail);
                    $scope.total += parseInt(p.tarif);
                    $scope.nominal = $scope.total * (75/100);
                }
            }
        } else {
        	$scope.tikets = [];
            $scope.selectedTiket = [];
            $scope.total 	= 0;
            $scope.nominal	= 0;
        }
        $scope.printSelected();
    }
	
	$scope.updateSelectedTiket = function($event, r){
        var checkbox = $event.target;
        if(checkbox.checked  && $scope.selectedTiket.indexOf(r.id_trx_tiket_sales_detail) < 0){
        	$scope.tikets.push(r);
            $scope.selectedTiket.push(r.id_trx_tiket_sales_detail);
            $scope.total += parseInt(r.tarif);
        } else {
        	$scope.tikets.splice($scope.tikets.indexOf(r), 1);
            $scope.selectedTiket.splice($scope.selectedTiket.indexOf(r.id_trx_tiket_sales_detail), 1);
            $scope.total -= parseInt(r.tarif);
        }
        $scope.nominal = $scope.total * (75/100);
        $scope.printSelected();
    }
    
    $scope.isTiketSelected = function(r){
        return $scope.selectedTiket.indexOf(r.id_trx_tiket_sales_detail) >= 0;
    }

    $scope.isAllTiketSelected = function(){
        return $scope.unselectedTiket.length === $scope.selectedTiket.length;
    }
    
	$scope.lookup			= function(kode) {		
		//console.log(kode);
		$scope.booking	= kode;
		$http.get(BASE_URL+'/api/tiketSales/cek/'+kode)
		.success(function(res){
			$scope.data = res.data;
			$scope.data.success = res.success;
			$scope.data.message = res.message;
			$scope.data.rute = res.data.asal+' - '+res.data.tujuan;
			for(var i=0; i<res.data.details.length; i++) $scope.unselectedTiket.push(res.data.details[i]);
		})
		.error(function(res){
			$scope.data	= res;
			$scope.data.success = res.success;
			$scope.data.message = res.message;
			$scope.selectedTiket = [];
			$scope.unselectedTiket	= [];
            $scope.total 	= 0;
            $scope.nominal	= 0;
		});
		$scope.kode = '';
	}
	$scope.clear	= function(){
		$scope.data	= null;
		$scope.kode	= '';
		$scope.booking = '';
		$scope.tikets = [];
		$scope.selectedTiket = [];
		$scope.unselectedTiket = [];
	    $scope.total 	= 0;
        $scope.nominal	= 0;
	}
	$scope.enterPressed = function (keyEvent) {
		if (keyEvent.keyCode == 13) $scope.lookup($scope.kode);
	};
	$scope.next		= function(o){
		var tiket = '';
		angular.forEach($scope.selectedTiket, function(val, key){
			tiket += val+',';
		});
		if(tiket.length > 0) tiket = tiket.substring(0, tiket.length-1);
		$location.path('/app/submit/'+o.id_trx_tiket_sales+'/'+tiket);
	}
	var original 	= $scope.data;
	$scope.isClean = function() {
		return angular.equals(original, $scope.data);
	}
	$scope.finish	= function(){
		$scope.clear();
	}
}])
;