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
        var row = self.location.toString().split("/")[9];
		
		if (module == 'hoCapex') {
            opener.$('#text08_' + row).val(arr[1]);
            opener.$('#text09_' + row).val('');
            opener.$('#text10_' + row).val('');
            opener.$('#text11_' + row).val('');
            opener.$('#text12_' + row).val('');
            opener.addClassEdited(row);
        } else if (module == 'hoOpex') {
            opener.$('#text08_' + row).val(arr[1]);
            opener.$('#text09_' + row).val('');
            opener.$('#text10_' + row).val('');
            opener.$('#text11_' + row).val('');
            opener.$('#text12_' + row).val('');
            opener.addClassEdited(row);
        } else if (module == 'hoActOutlook') {
            opener.$('#text07_' + row).val(arr[1]);
            opener.$('#text08_' + row).val('');
            opener.addClassEdited(row);
        } else if (module == 'hoSpd') {
            opener.$('#text11_' + row).val(arr[1]);
            opener.$('#text12_' + row).val(arr[1]);
            opener.$('#text13_' + row).val('');
            opener.$('#text14_' + row).val('');
            opener.$('#text15_' + row).val('');
            opener.$('#text16_' + row).val('');
            opener.addClassEdited(row);
        } else {
            opener.$('input[id^=text07_]').val(arr[1]);
        }
        //default
        //opener.$('input[id^=text05_]').val(arr[2]);
		
        self.close();
    });
});
</script>
