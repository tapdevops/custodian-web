<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Master Setting
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	11/04/2013
Update Terakhir		:	11/04/2013
Revisi				:	
=========================================================================================================================
*/
$this->headScript()->appendFile('js/global.js');
$this->headScript()->appendFile('js/jquery.dataTables.min.js');
$this->headLink()->appendStylesheet('css/dataTables.css'); ?>
<form name="master-setting" id="master-setting">
<div>
    <fieldset>
        <legend>PENCARIAN</legend>
        <table border="0" width="100%" cellpadding="0" cellspacing="0" id="main">
            <tr>
                <td width="15%">MASTER :</td>
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
        <legend>MASTER</legend>
		<table border="0" cellpadding="0" cellspacing="0" class="display" id="table1">
            <thead>
                <tr>
                    <th width="75%" class="ui-state-default">MASTER SETTING</th>
					<th width="25%" class="ui-state-default">#</th>
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
<input type="hidden" name="controller" id="controller" value=""/>
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
    $("#key_find").keyup(function(event) {
        if (event.keyCode == 13) {
            $("#btn_find").trigger("click");
        }
    });
	$("input[id^=upload]").live("click", function() {		
		var val = $(this).attr("id");
		if ($('#'+val).is(':disabled') == false) { 
            var controller = "upload/" + val.split("_")[2];
			$("#controller").val(controller);
			popup("upload/main", "detail", 700, 400);
        }
    });
    $("input[id^=sinkron]").live("click", function() {
        var val = $(this).attr("id");
        //alert(val);
        if ($('#'+val).is(':disabled') == false) {
            var controller = "sinkron/" + val.split("_")[2];
            alert(controller);
            $("#controller").val(controller);
            //popup("upload/main", "detail", 700, 400);
        }
    });
	$("input[id^=list]").live("click", function() {
		var val = $(this).attr("id");
		if ($('#'+val).is(':disabled') == false) { 
			var module = val.split("_")[2];
			popup_full(module+"/main", "detail");
		}
    });
	$("input[id^=download]").live("click", function() {
		var val = $(this).attr("id");
		if ($('#'+val).is(':disabled') == false) { 
			var module = val.split("_")[2];
			window.open("download/data-" + module,'_blank');
		}
    });
    initMasterSetting();
});
function initMasterSetting() {
    oTable = $("#table1").dataTable({
        "bJQueryUI": true,
        "bProcessing": true,
        "bServerSide": true,
        "bLengthChange": true,
        "bSortClasses": true,
        "bAutoWidth": false,
        "sPaginationType": "full_numbers",
        "sDom": "<\"H\"pli>rt<\"F\"pli>",
        "sAjaxSource": "<?php echo $this->baseUrl; ?>ho-prebudget-master/master-setting",
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
            { "sClass": "left", "bSortable": true },
            { "sClass": "center", "bSortable": false }
        ]
    });
}
</script>
