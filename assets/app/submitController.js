'use strict';

angular.module('app.controller', ['app.constant'])
.controller('SubmitController', ['$rootScope','$scope','$http','$stateParams','$location','$timeout', function ($rootScope,$scope,$http,$stateParams,$location,$timeout){
	$rootScope.$broadcast('timer-disabled', {});
	var idBooking 	= $stateParams.idBooking;
	var idTiket 	= $stateParams.idTiket;
	
	console.log('idBooking = '+idBooking);
	console.log('idTiket = '+idTiket);
	$scope.maxlength = 16;
	$scope.data	= {id_trx_tiket_sales:idBooking,refunds:idTiket,id_jenis_identitas:'',nomor_identitas:'',nama_pemohon:'',nomor_telp:'',id_bank:'',rekening:'',atas_nama:'',alasan:''};
	$scope.setMaxLength = function(){
		if($scope.data.id_jenis_identitas == 1) $scope.maxlength = 16;
		else if($scope.data.id_jenis_identitas == 2) $scope.maxlength = 13;
		else if($scope.data.id_jenis_identitas == 3) $scope.maxlength = 10;
		else if($scope.data.id_jenis_identitas == 4) $scope.maxlength = 8;
		else if($scope.data.id_jenis_identitas == 5) $scope.maxlength = 15;
	}
	$http.get(BASE_URL+'/api/tiketSales/info/'+idBooking).success(function(data){
		$scope.tiket 	= data;
		$scope.data.id_status_pesan	= data.id_status_pesan;
	});
	
	$http.get(BASE_URL+'/api/tiketSalesDetail/sum/'+idTiket).success(function(data){
		$scope.total 	= data.total;
		$scope.nominal 	= data.refund;
	});
	
	$scope.identitass	= [];
	$scope.banks	= [];
	
	$http.get(BASE_URL+'/api/identitas/lists').success(function(data){
		$scope.identitass = data;
	});
	$http.get(BASE_URL+'/api/bank/lists').success(function(data){
		$scope.banks = data;
	});
	
	$scope.save		= function(o){
		console.log(o);
		swal({
			title: "Konfirmasi",
			text: "Apakah anda yakin akan mengirimkan pengajuan refund ?",
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
				$http.post(BASE_URL+'/api/tiketRefund/proses/'+o.id_trx_tiket_sales, o)
		        .success(function(resp){
		        	swal('Success', resp.message);
		        	$timeout(function(){ $location.path('/app/home'); }, 3000);
		        })
		        .error(function(resp){
		        	swal('Exception', resp.message);
		        });
			}
		});
	}
	var original 	= $scope.data;
	$scope.isClean = function() {
		return angular.equals(original, $scope.data);
	}
}])
;