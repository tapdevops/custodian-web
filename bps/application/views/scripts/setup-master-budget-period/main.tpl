<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Master Periode Budget
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	10/04/2013
Update Terakhir		:	10/04/2013
Revisi				:	
=========================================================================================================================
*/
$this->headScript()->appendFile('js/jquery.dataTables.min.js');
$this->headLink()->appendStylesheet('css/dataTables.css'); ?>
<form name="setup_master_users" id="setup_master_users">
<div>
    <fieldset>
        <legend>PENCARIAN</legend>
        <table border="0" width="100%" cellpadding="0" cellspacing="0" id="main">
            <tr>
                <td width="15%">PERIODE BUDGET :</td>
                <td width="55%">
                    <input type="text" name="key_find" id="key_find" value="" style="width:200px;" />
                    <input type="button" name="btn_find" id="btn_find" value="CARI" class="button" />
                </td>
				<!-- 
					<td width="50%" align="right">	
						<input type="button" name="btn_unlock" id="btn_unlock" value="UNLOCK" class="button" /> 
						<input type="button" name="btn_lock" id="btn_lock" value="LOCK" class="button" />
						
					</td>
					-->	
            </tr>
        </table>
        <input type="hidden" name="rowid" id="rowid" value="" />
    </fieldset>
</div>
<br />
<div>
    <fieldset>
        <legend>DAFTAR PERIODE BUDGET</legend>
        <div align="right">
			<input type="button" name="btn_add" id="btn_add" value="TAMBAH BARU" class="button" />&nbsp;
		</div>
		<br />
		<table border="0" cellpadding="0" cellspacing="0" class="display" id="table1">
            <thead>
                <tr>
                    <th width="10%"  class="ui-state-default">#</th>
                    <th width="25%" class="ui-state-default">PERIODE BUDGET</th>
                    <th width="25%" class="ui-state-default">AWAL</th>
                    <th width="25%" class="ui-state-default">AKHIR</th>
					<th width="15%" class="ui-state-default">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="100%" colspan="3" class="dataTables_empty">Sedang Proses</td>
                </tr>
            </tbody>
        </table>
    </fieldset>
</div>
</form>
<?php
echo $this->partial('popup.tpl', array('width'  => 650,
                                       'height' => 300)); ?>
<script type="text/javascript">
var oTable;
$(document).ready(function() {
	$("#btn_lock").hide();
	$("#btn_unlock").hide();

    $("#btn_find").click(function() {
        oTable.fnFilter($("#key_find").val(), null);
    });
    $("#key_find").keydown(function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });
    $("#key_find").keyup(function(event) {
        if (event.keyCode == 13) {
            $("#btn_find").trigger("click");
        }
    });
	$("#btn_add").click(function() {
        popup("setup-master-budget-period/input/q1/add", "detail", 700, 400);
    });
    $("input[id^=edit]").live("click", function() {
        var rowid = $(this).attr("id").substr(5).replace("/", "_");
	    $("#rowid").val(rowid);
		popup("setup-master-budget-period/input/q1/edit/rowid/"+rowid, "detail", 700, 400);
    });
    initData();
	cek_squence();
	
	$("#btn_lock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Melakukan Finalisasi Data?")){
			$.ajax({
				type     : "post",
				url      : "setup-master-budget-period/upd-locked-seq-status",
				data     : $("#form_init").serialize()+"&status=LOCKED",
				cache    : false,
				dataType : 'json',
				success  : function(data) {
					alert("Finalisasi Data Berhasil.");
					$("#btn_find").trigger("click");
					cek_squence();
				}
			});	
		}
    });
	
	$("#btn_unlock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Memproses Ulang Data?")){
			$.ajax({
				type     : "post",
				url      : "setup-master-budget-period/upd-locked-seq-status",
				data     : $("#form_init").serialize()+"&status=",
				cache    : false,
				dataType : 'json',
				success  : function(data) {
					alert("Anda Dapat Melakukan Proses Ulang Data.");
					$("#btn_find").trigger("click");
					cek_squence();
				}
			});	
		}
    });
	
});

function cek_squence(){
	$.ajax({
		type    : "post",
		url     : "setup-master-budget-period/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
		data    : $("#setup_master_users").serialize(),
		cache   : false,
		dataType: "json",
		success : function(data) {
			if(data==1){
					$.ajax({
						type    : "post",
						url     : "setup-master-budget-period/check-locked-seq", //check apakah status lock sendiri apakah lock
						data    : $("#setup_master_users").serialize(),
						cache   : false,
						dataType: "json",
						success : function(data) {
							if(data.STATUS == 'LOCKED'){
								$("#btn_unlock").show();
								$("#btn_lock").hide();
								$(".button_edit").hide();
							}else{
								$("#btn_unlock").hide();
								$("#btn_lock").show();
								$(".button_edit").show();
							}
						}
					})
												
			}else{
				alert('Lakukan finalisasi (LOCK) terlebih dahulu terhadap : '+data+'');
				$("#btn_unlock").hide();
				$("#btn_lock").hide();
				$(".button_edit").hide();
			}
		}
	})
}
function initData() {
    oTable = $("#table1").dataTable({
        "bJQueryUI": true,
        "bProcessing": true,
        "bServerSide": true,
        "bLengthChange": true,
        "bSortClasses": true,
        "bAutoWidth": false,
        "sPaginationType": "full_numbers",
        "sDom": "<\"H\"pli>rt<\"F\"pli>",
        "sAjaxSource": "<?php echo $this->baseUrl; ?>setup-master-budget-period/list",
        "iDisplayLength": 25,
        "oLanguage": {
            "sUrl": "js/dataTables.langEN.txt"
        },
        "aaSorting": [
            [ 1, "ASC" ]
        ],
        "fnServerData": function(sSource, aoData, fnCallback) {
            $.ajax({
                "dataType": "json",
                "type": "post",
                "url": sSource,
                "data": aoData,
                "success": function(data) {
                    //alert(JSON.stringify(data));
                    fnCallback(data);
                },
                "error": function(jqXHR) {
                    var data = jqXHR.responseText;
                    if (data == "session is expired") {
                        //alert("Sorry, your session is expired!");
                        top.location.href = "<?php echo $this->baseUrl; ?>index/login";
                    } else {
                        alert(data);
                    }
                }
            } );
        },
        "aoColumns": [
            { "sClass": "center", "bSortable": false },
            { "sClass": "left", "bSortable": true },
            { "sClass": "left", "bSortable": true },
            { "sClass": "left", "bSortable": true },
			{ "sClass": "center", "bSortable": true }
        ]
    });
}
</script>
