<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk popup LoV afdeling
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	03/06/2013
Update Terakhir		:	03/06/2013
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
		if(module == 'rktManualNonInfra'){
			opener.$("#text04_" + row).val(arr[1]);
		}else if(module == 'rktLc'){
			opener.$("#text06_" + row).val(arr[1]);
			opener.$("#text07_" + row + " option[value='"+arr[3]+"']").attr("selected", "selected");
			opener.$("#text08_" + row + " option[value='"+arr[2]+"']").attr("selected", "selected");	
		}
		//default pick di filter
		else{
			opener.$("#src_afd").val(arr[1]);
			opener.$("#topo_afd").val(arr[2]);
			opener.$("#land_afd").val(arr[3]);
		}
		
		opener.addClassEdited(row);
        self.close();
    });
});
</script>
