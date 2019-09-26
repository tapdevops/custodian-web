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
$this->headScript()->appendFile('js/accounting.js');
echo $this->partial('datatables.tpl', array(
    'columns'        => $this->columns,
    'iDisplayLength' => 25, // 25, 50 or 100
    'sDom'           => '<"H"pli>rt<"F"pli>',
    //'sScrollXInner'  => 700
)); ?>
<script type="text/javascript">
function decodeHtml(str) {
    var map = {
        '&amp;': '&',
        '&lt;': '<',
        '&gt;': '>',
        '&quot;': '"',
        '&#039;': "'"
    };
    return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function(m) {return map[m];});
}

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

		//default
        if(module == 'hoCapex'){
            opener.$('#text05_' + row).val(arr[1]);
            opener.$('#text06_' + row).val(decodeHtml(arr[2]));
            opener.addClassEdited(row);
        } else if (module == 'hoOpex') {
            opener.$('#text05_' + row).val(arr[1]);
            opener.$('#text06_' + row).val(decodeHtml(arr[2]));
            opener.addClassEdited(row);
        } else if (module == 'hoSpd') {
            opener.$('#text09_' + row).val(arr[1]);
            opener.$('#text10_' + row).val(decodeHtml(arr[2]));
            opener.$('#text22_' + row).val(accounting.formatNumber(arr[3], 2));
            opener.$('#text51_' + row).val(arr[3]);
            opener.$('#text52_' + row).val(arr[4]);
            opener.$('#text53_' + row).val(arr[5]);
            opener.$('#text54_' + row).val(arr[6]);
            opener.$('#text55_' + row).val(arr[7]);
            opener.$('#text56_' + row).val(arr[8]);
            opener.$('#text57_' + row).val(arr[9]);
            opener.$('#text58_' + row).val(arr[10]);
            opener.addClassEdited(row);
            opener.$('#text10_' + row).trigger("change");
        }
        //opener.$('input[id^=text06_]').text(arr[2]).html();
        //opener.$('input[id^=text05_]').val(arr[2]);
		
        self.close();
    });
});
</script>