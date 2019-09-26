<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Master Hak Akses User
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Doni Romdoni
Dibuat Tanggal		: 	06/05/2013
Update Terakhir		:	06/05/2013
Revisi				:	
=========================================================================================================================
*/
$this->headScript()->appendFile('js/jquery.dataTables.min.js');
$this->headLink()->appendStylesheet('css/dataTables.css');
?>
<form name="setup_master_accessrights" id="setup_master_accessrights">
<div>
    <fieldset>
        <legend>CARI:</legend>
        <table border="0" width="100%" cellpadding="0" cellspacing="0" id="main">
            <tr>
                <td width="10%">HAK ASES :</td>
                <td width="90%">
                    <?php echo $this->setElement($this->main['accessright']); ?>
                </td>
            </tr>
            <tr>
                <td>MODUL :</td>
                <td>
                    <?php echo $this->setElement($this->main['module']); ?>
                    <input type="button" name="btn_find" id="btn_find" value="FIND" class="button" />
                </td>
            </tr>
        </table>
    </fieldset>
</div>
<br />
<div>
    <fieldset>
        <legend>HAK AKSES PENGGUNA</legend>
        <table border="0" cellpadding="0" cellspacing="0" class="display" id="table1">
            <thead>
                <tr>
                    <th width="25%" class="ui-state-default">HAK AKSES</th>
                    <th width="60%" class="ui-state-default">MODUL</th>
                    <th width="15%" class="ui-state-default">AUTHORIZED</th>
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
<script type="text/javascript">
var oTable;
$(document).ready(function() {
    $("#accessright").change(function() {
        $("#btn_find").trigger("click");
    });
    $("#module").keydown(function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });
    $("#module").keyup(function(event) {
        if (event.keyCode == 13) {
            $("#btn_find").trigger("click");
        }
    });
    $("#btn_find").click(function() {
        var search = $("#accessright").val() + "~" + $("#module").val();
        oTable.fnFilter(search, null);
    });
    $("input[id^=authorized]").live("click", function() {
        var rowid = $(this).attr("id").substr(11);
        var authorized = ($(this).is(":checked")) ? 'Y' : 'N';
        $.ajax({
            "type"    : "post",
            "url"     : "<?php echo $this->baseUrl; ?>ho-setup-hak-akses/save",
            "data"    : "rowid=" + encodeURIComponent(rowid) + "&authorized=" + authorized,
            "success" : function(response) {
            }
        });
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
        "sAjaxSource": "<?php echo $this->baseUrl; ?>ho-setup-hak-akses/list",
        "iDisplayLength": 25,
        "oLanguage": {
            "sUrl": "js/dataTables.langEN.txt"
        },
        "aaSorting": [
            [ 0, "ASC" ]
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
            { "sWidth": "25%", "sClass": "center", "bSortable": true },
            { "sWidth": "60%", "sClass": "left", "bSortable": true },
            { "sWidth": "15%", "sClass": "center", "bSortable": true }
        ]
    });
}
</script>
