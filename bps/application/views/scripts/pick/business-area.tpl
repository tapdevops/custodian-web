<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk popup LoV BA
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	24/04/2013
Update Terakhir		:	24/04/2013
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
		
		var module= self.location.toString().split("/")[9];
		
		//default
		opener.$("#key_find").val(arr[1]);
		opener.$("#src_ba").val(arr[2]);
		opener.$("#src_region_code").val(arr[3]);
		opener.$("#src_region").val(arr[4]);
		opener.$("#src_afd").val("");
		
		if(module == 'normaDistribusiVra'){
		
			/*
			-cek jumlah afd dari parameter bussiness area
			-ubah table data opener sesuai banyaknya afd bussiness area
			*/
			$.ajax({
				type     : "post",
				url      : "norma-distribusi-vra/afdba",
				data     : $("#form_init").serialize(),
				cache    : false,
				//dataType : 'json',
				success  : function(data) {
					if (data == "done") {
						alert("Data berhasil disimpan.");
						$("#btn_find").trigger("click");
					}else{
						alert(data);
					}
				}
			});
		}
		
        self.close();
    });
});
</script>
