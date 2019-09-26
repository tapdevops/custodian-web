<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk popup LoV COA
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
			opener.$("#text06_" + row).val(arr[1]);
			opener.$("#text07_" + row).val(arr[2]);
		}
		
		//aset
		else if(module == 'asset'){
			opener.$("#text06_" + row).val(arr[1]);
			opener.$("#text07_" + row).val(arr[2]);
		}
		
		//mapping group BUM - COA
		else if(module == 'mappingGroupBumCoa'){
			opener.$("#text04_" + row).val(arr[1]);
			opener.$("#text05_" + row).val(arr[2]);
		}
		
		//material
		else if(module == 'material'){
			opener.$("#text08_" + row).val(arr[1]);
			opener.$("#text09_" + row).val(arr[2]);
		}
		
		//mapping group CSR - COA
		else if(module == 'mappingGroupCsrCoa'){
			opener.$("#text04_" + row).val(arr[1]);
			opener.$("#text05_" + row).val(arr[2]);
		}
		
		//mapping group Internal Relation - COA
		else if(module == 'mappingGroupIrCoa'){
			opener.$("#text04_" + row).val(arr[1]);
			opener.$("#text05_" + row).val(arr[2]);
		}
		
		//mapping group SHE - COA
		else if(module == 'mappingGroupSheCoa'){
			opener.$("#text04_" + row).val(arr[1]);
			opener.$("#text05_" + row).val(arr[2]);
		}
		
		//mapping group OPEX - COA
		else if(module == 'mappingGroupOpexCoa'){
			opener.$("#text04_" + row).val(arr[1]);
			opener.$("#text05_" + row).val(arr[2]);
		}
		
		opener.addClassEdited(row);
        self.close();
    });
});
</script>
