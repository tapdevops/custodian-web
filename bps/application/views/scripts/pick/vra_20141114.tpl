<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk popup LoV VRA
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	05/06/2013
Update Terakhir		:	05/06/2013
Revisi				:	
	- YULIUS		:	
						- Tambah kondisi Periode Budget
						- Tambah Validasi untuk NormaPerkerasanJalan
=========================================================================================================================
*/
echo $this->partial('datatables.tpl', array(
    'columns'        => $this->columns,
    'iDisplayLength' => 25, // 25, 50 or 100
    'sDom'           => '<"H"pli>rt<"F"pli>',
    //'sScrollXInner'  => 700
)); ?>
<script type="text/javascript">
$(document).ready(function() {
	$("#table1 tr input[id^=pick]").live("click", function() {
        var tr  = $(this).parent().parent();
        var td  = $(tr).children();
        var arr = new Array();
        $(td).each(function(idx) {
            if (idx > 0) {
                arr[idx] = $(this).html();
            }
        });
		
		var module = self.location.toString().split("/")[7];
		var row = self.location.toString().split("/")[9];
		var ba_code = self.location.toString().split("/")[11];
		
		//mapping job type - VRA
		if(module == 'mappingJobTypeVra'){
			opener.$("#text04_" + row).val(arr[1]);
			opener.$("#text05_" + row).val(arr[2]);
			opener.addClassEdited(row);
			self.close();
		}
		
		else if(module == 'normaDistribusiVraNonInfra'){
			opener.$("#text04_" + row).val(arr[1]);
			opener.$("#text05_" + row).val(arr[2]);
			opener.$("#text06_" + row).val(arr[3]);
			opener.addClassEdited(row);
			self.close();	
		}
		
		//RKT VRA
		else if(module == 'rktVra'){
			opener.$("#text05_" + row).val(arr[1]);
			opener.$("#text06_" + row).val(arr[2]);
			opener.$("#text10_" + row).val(arr[3]);
			opener.$("#text04_" + row).val(arr[4]);
			opener.$("#text11_" + row).val("");
			opener.$("#text12_" + row).val("");
			opener.$("#text13_" + row).val("");
			opener.$("#text14_" + row).val("");
			opener.addClassEdited(row);
			self.close();
		}
		
		//NormaPerkerasanJalan
		else if(module == 'normaPerkerasanJalan'){
			var budgetperiod = self.location.toString().split("/")[15];
				$.ajax({
					type     : "post",
					url      : "norma-perkerasan-jalan/get-value-vra",
					data     : {vra_code:arr[1], ba_code:ba_code, period:budgetperiod},
					cache    : false,
					dataType : 'json',
					success  : function(data) {
						var kode = self.location.toString().split("/")[13];
						if(kode=="DT"){
							//alert(kode);
							opener.$("#text11_" + row).val(arr[1]);
							opener.$("#text12_" + row).val(data.VALUE);
							//var text = opener.$("#text12_" + row).val();
							opener.addClassEdited(row);
							self.close();
							
						}else if(kode=="EX"){
							opener.$("#text16_" + row).val(arr[1]);
							opener.$("#text17_" + row).val(data.VALUE);
							var text = opener.$("#text17_" + row).val();
							opener.addClassEdited(row);
							self.close();
						}else if(kode=="VC"){
							opener.$("#text19_" + row).val(arr[1]);
							opener.$("#text20_" + row).val(data.VALUE);
							//var text = opener.$("#text20_" + row).val();
							opener.addClassEdited(row);
							self.close();
						}else{
							opener.$("#text22_" + row).val(arr[1]);
							opener.$("#text23_" + row).val(data.VALUE);
							//var text = opener.$("#text23_" + row).val();
							opener.addClassEdited(row);
							self.close();
						}
					}
				});
			}
		//opener.addClassEdited(row);
		//self.close();
		
    });
});
</script>
