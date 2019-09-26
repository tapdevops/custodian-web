<?php
$this->headScript()->appendFile('js/global.js'); ?>
<form name="parameter-value" id="parameter-value">
<div>
    <table border="0" width="100%" cellpadding="0" cellspacing="0" id="buttons">
        <tr>
            <td width="50%">&nbsp;</td>
            <td width="50%" align="right">
                <input type="button" name="btn_simpan" id="btn_simpan" value="SIMPAN" class="button" />
                <input type="button" name="btn_cancel" id="btn_cancel" value="BATAL" class="button" />
            </td>
        </tr>
    </table>
</div>
<br />
<div>
    <table border="0" width="100%" cellpadding="3" cellspacing="0" id="main">
        <tr>
            <td width="25%"><strong>KODE PARAMETER</strong>:</td>
            <td width="75%">
                <?php echo $this->setElement($this->input['PARAMETER_CODE']); ?>
                <input type="button" name="pick_parameter_code" id="pick_parameter_code" value="...">
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <?php echo $this->setElement($this->input['PARAMETER_NAME']); ?>
            </td>
        </tr>
        <tr>
            <td><strong>KODE NILAI PARAMETER</strong>:</td>
            <td>
				<?php echo $this->setElement($this->input['PARAMETER_VALUE_CODE']); ?>
            </td>
        </tr>
        <tr>
            <td><strong>NILAI 1</strong>:</td>
            <td>
				<?php echo $this->setElement($this->input['PARAMETER_VALUE']); ?>
            </td>
        </tr>
		<tr>
            <td><strong>NILAI 2</strong>:</td>
            <td>
				<?php echo $this->setElement($this->input['PARAMETER_VALUE_2']); ?>
            </td>
        </tr>
		<tr>
            <td><strong>NILAI 3 (FORMAT TANGGAL)</strong>:</td>
            <td>
				<?php echo $this->setElement($this->input['PARAMETER_VALUE_3']); ?>
            </td>
        </tr>
        <input type="hidden" name="rowid" id="rowid" value="" />
    </table>
</div>
</form>
<?php
echo $this->partial('popup.tpl', array('width'  => 650,
                                       'height' => 300)); ?>
<script type="text/javascript">
var url = location.href.toString().split("/");
var mod = url[url.length-1];
$(document).ready(function() {
    init();
    $("#pick_parameter_code").click(function() {
		popup("pick/parameter", "pick");
    });
    $("#pick_parameter_code").live("keydown", function(event) {
        if (event.keyCode == 120) {
            // shortcut key F9
			popup("pick/parameter", "pick");
        } else {
            // others
            event.preventDefault();
        }
    });
    $("#btn_simpan").click(function() {
        var err = false;
        $("#main").find(".required").each(function() {
            if ($(this).val() == "" || $(this).val() == "-1") {
                alert($(this).attr("id").toUpperCase() + " belum diisi.");
                $(this).addClass("empty");
				$(this).focus();
                err = true;
                return false;
            }
        });
        if (err == false) {
            $.ajax({
                "type"    : "post",
                "url"     : "<?php echo $this->baseUrl; ?>setup-master-parameter-value/save",
                "data"    : $("#parameter-value").serialize(),
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
	$(".datepicker").datepicker({
        dateFormat  : "dd-mm-yy",
        changeMonth : true,
        changeYear  : true,
        maxDate     : 'today'
    });
    $("#PARAMETER_VALUE_3").blur(function() {
        var msg = dateCheck($(this).val());
        if (msg != "valid") {
            alert(msg);
            $(this).val("<?php echo date('d-m-Y'); ?>");
            $(this).focus();
        }
    });
    $("#btn_cancel").click(function() {
        self.close();
    });
});
function init() {
    $("span[class=title]").html($("span[class=title]").html() + " (" + mod.toUpperCase() + ")");
    if (mod == "edit") {
        $("#rowid").val(opener.$("#rowid").val());
        $("#pick_parameter_code").css("display","none");
        setTimeout("getRow()", 500);
    }
}
function getRow() {
    $.ajax({
        "type"     : "post",
        "url"      : "<?php echo $this->baseUrl; ?>setup-master-parameter-value/get-row",
        "data"     : "rowid=" + $("#rowid").val(),
        "dataType" : "json",
        "success"  : function(data) {
            //alert(JSON.stringify(data));
            for (key in data) {
                $("#" + key).val(data[key]);
            }
        }
    });
}
</script>
