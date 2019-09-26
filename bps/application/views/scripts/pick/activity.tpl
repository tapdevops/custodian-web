<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk popup LoV Aktivitas
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	30/04/2013
Update Terakhir		:	30/04/2013
Revisi				:	
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
		
		//mapping aktivitas - COA
		if(module == 'mappingActivityCoa'){
			opener.$("#text03_" + row).val(arr[1]);
			opener.$("#text04_" + row).val(arr[2]);
		}
		//activity opsi
		else if (module == 'activityOpsi') {
			opener.$("#text02_" + row).val(arr[1]);
			opener.$("#text03_" + row).val(arr[2]);
		}
		//activity mapping
		else if (module == 'activityMapping') {
			opener.$("#text02_" + row).val(arr[1]);
			opener.$("#text03_" + row).val(arr[2]);
		}
		//normaDistribusiVraNonInfra
		else if (module == 'normaDistribusiVraNonInfra') {
			opener.$("#text02_" + row).val(arr[1]);
			opener.$("#text03_" + row).val(arr[2]);
		}
		//default pick di filter
		else{
			opener.$("#activity_code").val(arr[1]);
			opener.$("#activity_name").val(arr[2]);
		}
		
		opener.addClassEdited(row);
        self.close();
    });
});
</script>
