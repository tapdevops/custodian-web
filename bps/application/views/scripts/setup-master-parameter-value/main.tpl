<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Parameter
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	10/04/2012
Update Terakhir		:	10/04/2012
Revisi				:	
=========================================================================================================================
*/
$this->headScript()->appendFile('js/global.js');
$this->headScript()->appendFile('js/jquery.dataTables.min.js');
$this->headLink()->appendStylesheet('css/dataTables.css'); ?>
<form name="parameter-value" id="parameter-value">
<div>
    <fieldset>
        <legend>PENCARIAN :</legend>
        <table border="0" width="100%" cellpadding="0" cellspacing="0" id="main">
            <tr>
                <td width="15%">PARAMETER :</td>
                <td width="85%">
                    <input type="hidden" name="PARAMETER_CODE" id="PARAMETER_CODE" value="" style="width:200px;" />
                    <input type="text" name="PARAMETER_NAME" id="PARAMETER_NAME" value="" style="width:200px;" />
                    <input type="button" name="pick_parametercode" id="pick_parametercode" value="...">
                </td>
            </tr>
		
            <tr>
				<td></td>
				<td><input type="button" name="btn_find" id="btn_find" value="CARI" class="button" /></td>
            </tr>
        </table>
        <input type="hidden" name="rowid" id="rowid" value="" />
    </fieldset>
</div>
<br />
<div>
    <fieldset>
        <legend>PARAMETER</legend>
		<div align="right">
			<input type="button" name="btn_add2" id="btn_add2" value="TAMBAH BARU" class="button" />&nbsp;
		</div>
		<br />
        <table border="0" cellpadding="0" cellspacing="0" class="display" id="table1">
            <thead>
                <tr>
                    <th width="10%" class="ui-state-default">#</th>
                    <th width="20%" class="ui-state-default">PARAMETER</th>
                    <th width="10%" class="ui-state-default">KODE NILAI PARAMETER</th>
                    <th width="20%" class="ui-state-default">KETERANGAN</th>
					<th width="20%" class="ui-state-default">KETERANGAN 2</th>
					<th width="20%" class="ui-state-default">KETERANGAN 3</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="100%" colspan="4" class="dataTables_empty">Sedang Proses</td>
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
    $("#btn_find").click(function() {
        //if ($("#key_find").val() != "") {
            oTable.fnFilter($("#PARAMETER_CODE").val(), null);
        //}
    });
  $("#PARAMETER_NAME").live("keydown", function(event) {
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/parameter", "pick", 700, 400 );
        }else{
			//event.preventDefault();
		}
    });
    $("#pick_parametercode").click(function() {
		popup("pick/parameter", "pick");
    });
    $("#btn_add2").click(function() {
        popup("setup-master-parameter-value/input/q1/add", "detail", 700, 400);
    });
    $("input[id^=edit]").live("click", function() {
        var rowid = $(this).attr("id").substr(5);
		$("#rowid").val(rowid);
        popup("setup-master-parameter-value/input/q1/edit", "detail", 700, 400);
    });
    $("input[id^=delete]").live("click", function() {
        var rowid = $(this).attr("id").substr(7);
        var data  = "rowid=" + rowid;
        if (confirm("Anda yakin ingin menghapus data nilai parameter ini?")) {
            $.ajax({
                "type"    : "post",
                "url"     : "<?php echo $this->baseUrl; ?>setup-master-parameter-value/delete",
                "data"    : data,
                "success" : function(response) {
                    if (response == "done") {
						alert("Data berhasil dihapus.");
                        $("#btn_find").trigger("click");
                    }else{
						alert("Data gagal dihapus.");
					}
                }
            });
        }
    });
    initParameterValue();
});
function initParameterValue() {
    oTable = $("#table1").dataTable({
        "bJQueryUI": true,
        "bProcessing": true,
        "bServerSide": true,
        "bLengthChange": true,
        "bSortClasses": true,
        "bAutoWidth": false,
        "sPaginationType": "full_numbers",
        "sDom": "<\"H\"pli>rt<\"F\"pli>",
        "sAjaxSource": "<?php echo $this->baseUrl; ?>setup-master-parameter-value/parameter-value",
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
			{ "sClass": "left", "bSortable": true },
			{ "sClass": "left", "bSortable": true } 
        ]
    });
}
</script>
