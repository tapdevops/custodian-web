<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Master User
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
                <td width="15%">NAMA PENGGUNA :</td>
                <td width="85%">
                    <input type="text" name="key_find" id="key_find" value="" style="width:200px;" />
                    <input type="button" name="btn_find" id="btn_find" value="CARI" class="button" />
                </td>
            </tr>
        </table>
        <input type="hidden" name="rowid" id="rowid" value="" />
    </fieldset>
</div>
<br />
<div>
    <fieldset>
        <legend>PENGGUNA</legend> 
		<div align="right">
            <input type="button" name="btn_tambah" id="btn_tambah" value="TAMBAH" class="button" />
        </div>
        <br />
        <table border="0" cellpadding="0" cellspacing="0" class="display" id="table1">
            <thead>
                <tr>
                    <th width="5%"  class="ui-state-default" rowspan="2">#</th>
                    <th width="15%" class="ui-state-default" rowspan="2">NAMA PENGGUNA</th>
                    <th width="25%" class="ui-state-default" rowspan="2">NAMA LENGKAP</th>
                    <th width="55%" class="ui-state-default" colspan="4">AKSES SITE</th>
                    <th width="55%" class="ui-state-default" colspan="4">AKSES HO</th>
                    <th class="ui-state-default" rowspan="2">TIPE AKSES</th>
                </tr>
                <tr>
                    <th width="25%" class="ui-state-default">REF. ROLE</th> 
                    <th width="10%" class="ui-state-default">BA / REGION </th>
					<th width="7%" class="ui-state-default">ROLE</th>
					<th width="8%" class="ui-state-default">AKTIF</th>
                    <th width="7%" class="ui-state-default">ROLE</th>
                    <th width="25%" class="ui-state-default">DIV</th> 
                    <th width="25%" class="ui-state-default">CC</th>
                    <th width="8%" class="ui-state-default">AKTIF</th>
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
    $("#btn_find").click(function() {
        oTable.fnFilter($("#key_find").val(), null);
    });
    $("#key_find").keydown(function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });
	 $("#btn_tambah").click(function() {
        popup("setup-master-users/input/q1/add", "detail", 700, 400);
    });
    $("#key_find").keyup(function(event) {
        if (event.keyCode == 13) {
            $("#btn_find").trigger("click");
        }
    });
    $("input[id^=edit]").live("click", function() {
        var rowid = $(this).attr("id").substr(5);
        $("#rowid").val(rowid);
        popup("setup-master-users/input/q1/edit", "detail", 700, 400);
    });
    initData();
});
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
        "sAjaxSource": "<?php echo $this->baseUrl; ?>setup-master-users/users",
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
            { "sClass": "left", "bSortable": true },
			{ "sClass": "center", "bSortable": true },
            { "sClass": "center", "bSortable": true },
            { "sClass": "center", "bSortable": true },
            { "sClass": "center", "bSortable": true },
            { "sClass": "center", "bSortable": true },
            { "sClass": "center", "bSortable": true }
        ]
    });
}
</script>
