angular.module('app.controller', ['ui.bootstrap'])
.controller('RefundViewController', ['$rootScope','$scope','$http','$cookies','$state','$stateParams','$timeout','$location','$window', function($rootScope,$scope,$http,$cookies,$state,$stateParams,$timeout,$location,$window){
	$rootScope.$broadcast('timer-disabled', {});
	if($cookies.token == null) $state.go('app.signin');
	
	var idRefund	= $stateParams.idRefund;
	var idDetail	= $stateParams.idDetail;
	
	$http.get(BASE_URL+'/api/statusRefund/lists').success(function(data){
		$scope.status = data;
	});
	
	$http.get(BASE_URL+'/api/tiketRefund/info/'+idRefund).success(function(data){
		$scope.data 	= data;
		$scope.data.rute= data.asal+' - '+data.tujuan;
		$scope.data.nomor_identitas = data.nomor_identitas +' ('+data.jenis_identitas+')';
		$scope.data.alasan	= data.alasan;
		
		$http.get(BASE_URL+'/api/tiketRefundDetail/info/'+idDetail).success(function(tiket){
			$scope.data.tarif 	= tiket.tarif;
			$scope.data.refund	= tiket.refund;
			$scope.data.catatan	= tiket.catatan;
			$scope.data.nama	= tiket.nama;
			$scope.data.alamat	= tiket.alamat;
			$scope.data.id_status_refund = tiket.id_status_refund;
		});
		
	});
	$scope.save		= function(o){
		console.log(o);
		swal({
			title: "Konfirmasi",
			text: "Apakah anda melakukan update status refund ?",
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
				$http.post(BASE_URL+'/api/tiketRefundDetail/update/'+idDetail, o)
		        .success(function(resp){
		        	swal('Success', resp.message);
		        	$timeout(function(){ $location.path('/app/refund/list/_'); }, 3000);
		        })
		        .error(function(resp){
		        	swal('Exception', resp.message);
		        });
			}
		});
	}
	$scope.back	= function(){
		$window.history.back();
	}
}])
;