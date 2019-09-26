<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Input Master Periode Budget
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	18/06/2013
Update Terakhir		:	18/06/2013
Revisi				:	
=========================================================================================================================
*/
$this->headScript()->appendFile('js/global.js'); ?>
<form name="setup_master_budget_period" id="setup_master_budget_period">
<div>
    <table border="0" width="100%" cellpadding="0" cellspacing="0" id="buttons">
        <tr>
            <td width="50%">&nbsp;</td>
            <td width="50%" align="right">
                <input type="button" name="tombol_simpan" id="tombol_simpan" value="SIMPAN" class="button" />
                <input type="button" name="btn_cancel" id="btn_cancel" value="BATAL" class="button" />
            </td>
        </tr>
    </table>
</div>
<br />
<div>
    <table border="0" width="100%" cellpadding="0" cellspacing="0" id="main">
        <tr>
            <td width="25%">PERIODE BUDGET :</td>
            <td width="75%">
                <?php echo $this->setElement($this->input['PERIOD_BUDGET']); ?>
            </td>
        </tr>
        <tr>
            <td>AWAL :</td>
            <td>
                <?php echo $this->setElement($this->input['START_BUDGETING']); ?>
            </td>
        </tr>
        <tr>
            <td>AKHIR :</td>
            <td>
                <?php echo $this->setElement($this->input['END_BUDGETING']); ?>
            </td>
        </tr>
		<tr>
            <td>STATUS :</td>
            <td>
                <?php echo $this->setElement($this->input['STATUS']); ?>
            </td>
        </tr>
    </table>
</div>
</form>
<input type="hidden" name="rowid" id="rowid" value="" />
<script type="text/javascript">
var url = location.href.toString().split("/");
var mod = url[url.length-3];
        
$(document).ready(function() {
    init();
    $("input[type=text]").keyup(function(event) {
        var str = $(this).val().toUpperCase();
        $(this).val(str);
    });
	$( "#PERIOD_BUDGET" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd'
	});
	$( "#START_BUDGETING" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd'
	});
	$( "#END_BUDGETING" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd'
	});
    
	$( "#START_BUDGETING" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd'
	});
	$( "#END_BUDGETING" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd'
	});
    $("#tombol_simpan").click(function() {
        var err = false;
        $("#main").find("input[type=text], select").each(function() {
            if ($(this).val() == "" || $(this).val() == "-1") {
                alert($(this).attr("id").toUpperCase() + " Harus Diisi.");
                $(this).focus();
                err = true;
                return false;
            }
        });
        if (err == false) {
            $.ajax({
                "type"    : "post",
                "url"     : "<?php echo $this->baseUrl; ?>setup-master-budget-period/save",
                "data"    : $("#setup_master_budget_period").serialize() + "&rowid=" + encodeURIComponent($("#rowid").val()),
                "success" : function(response) {                    
                    if (response == "done") {
						alert("Data berhasil disimpan.");
                        opener.$("#btn_find").trigger("click");
                        $("#btn_cancel").trigger("click");
                    }else{
						alert(response);
					}
                }
            });
        }
    });
    $("#btn_cancel").click(function() {
        self.close();
    });
});
function init() {
    $("span[class=title]").html($("span[class=title]").html() + " (" + mod.toUpperCase() + ")");
	
	if (mod == "edit") {	
		var rowid = url[url.length-1].replace("_","/");
		$("#rowid").val(rowid);
		$("#PERIOD_BUDGET").attr("readonly", true);
        setTimeout("getRow()", 500);
    }
	else if (mod == "add") {	
        $( "#PERIOD_BUDGET" ).datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd'
		});		
        setTimeout("getRow()", 500);
    }
}
function getRow() {
    $.ajax({
        "type"     : "post",
        "url"      : "<?php echo $this->baseUrl; ?>setup-master-budget-period/row",
        "data"     : "rowid=" + encodeURIComponent($("#rowid").val()),
        "dataType" : "json",
        "success"  : function(data) {
            for (key in data) {
                $("#" + key).val(data[key]);
            }
        }
    });
}
</script>
