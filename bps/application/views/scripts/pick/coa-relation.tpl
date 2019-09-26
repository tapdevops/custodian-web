<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk popup LoV COA dari mapping COA - relation
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	15/07/2013
Update Terakhir		:	15/07/2013
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
		
		//RKT CSR
		if(module == 'rktCsr'){
			opener.$("#text06_" + row).val(arr[1]);
			opener.$("#text07_" + row).val(arr[2]);
		}
		//default
		else{
			opener.$("#src_coa_code").val(arr[1]);
			opener.$("#src_coa").val(arr[2]);	
			opener.$("#src_group_code").val(arr[3]);
			opener.$("#src_group_desc").val(arr[4]);				
		}
				
		opener.addClassEdited(row);
        self.close();
    });
});
</script>
