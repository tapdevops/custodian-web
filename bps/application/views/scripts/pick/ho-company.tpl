<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk popup LoV periode budget
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	19/06/2013
Update Terakhir		:	19/06/2013
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
        var row = self.location.toString().split("/")[11];
        
		//default
        if (module == 'hoCapex') {
            opener.$('#text09_' + row).val(arr[1]);
            opener.$('#text10_' + row).val(arr[2]);
            opener.$('#text11_' + row).val(arr[3]);
            opener.$('#text12_' + row).val(arr[4]);
            opener.addClassEdited(row);
        } else if (module == 'hoOpex') {
            opener.$('#text09_' + row).val(arr[1]);
            opener.$('#text10_' + row).val(arr[2]);
            opener.$('#text11_' + row).val(arr[3]);
            opener.$('#text12_' + row).val(arr[4]);
            opener.addClassEdited(row);
        } else if (module == 'hoActOutlook') {
            opener.$('#text08_' + row).val(arr[1] + ' - ' + arr[2]);
            opener.addClassEdited(row);
        } else if (module == 'hoSpd') {
            opener.$('#text13_' + row).val(arr[1]);
            opener.$('#text14_' + row).val(arr[2]);
            opener.$('#text15_' + row).val(arr[3]);
            opener.$('#text16_' + row).val(arr[4]);
            opener.addClassEdited(row);
        } else {
            opener.$("#text08_" + row).val(arr[2]);
            opener.addClassEdited(row);
        }
        
        self.close();
    });
});
</script>
