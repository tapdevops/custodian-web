<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk popup LoV RVRA
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	05/06/2013
Update Terakhir		:	05/06/2013
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
		
		//default pilihan
		opener.$("#src_rvra_code").val(arr[1]);
		opener.$("#src_rvra_desc").val(arr[2]);
		
		//mapping job type - VRA
		if(module == 'mappingJobTypeVra'){
			opener.$("#text02_" + row).val(arr[1]);
			opener.$("#text03_" + row).val(arr[2]);
		}
		
		opener.addClassEdited(row);
        self.close();
    });
});
</script>