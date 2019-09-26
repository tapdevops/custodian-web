<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Input Master User
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	10/04/2013
Update Terakhir		:	10/04/2013
Revisi				:	
=========================================================================================================================
*/
$this->headScript()->appendFile('js/global.js'); ?>
<form name="setup_master_users" id="setup_master_users">
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
    <table border="0" width="100%" cellpadding="0" cellspacing="0" id="main">
        <tr>
            <td width="25%">NAMA PENGGUNA :</td>
            <td width="75%">
                <?php echo $this->setElement($this->input['USER_NAME']); ?>
            </td>
        </tr>
        <tr>
            <td>NAMA LENGKAP :</td>
            <td>
                <?php echo $this->setElement($this->input['FULL_NAME']); ?>
            </td>
        </tr>
		 <tr>
            <td>NIK :</td>
            <td>
                <?php echo $this->setElement($this->input['NIK']); ?>
            </td>
        </tr>
        <tr>
            <td>TIPE AKSES :</td>
            <td>
                <?php echo $this->setElement($this->input['TIPE_AKSES']); ?>
            </td>
        </tr>
    </table>
    <table border="0" width="100%" cellpadding="0" cellspacing="0" id="akses_site">
        <tr>
            <td colspan="2"><hr></td>
        </tr>
        <tr>
            <td colspan="2"><b>AKSES SITE</b></td>
        </tr>
        <tr>
            <td width="25%">PERUSAHAAN :</td>
            <td width="75%">
                <?php echo $this->setElement($this->input['BA_CODE']); ?>
            </td>
        </tr>
        <tr>
            <td>ROLE :</td>
            <td>
                <?php echo $this->setElement($this->input['USER_ROLE']); ?>
            </td>
        </tr>
        <tr>
            <td>REFRENCE ROLE :</td>
            <td>
                <?php echo $this->setElement($this->input['REFERENCE_ROLE']); ?>
            </td>
        </tr>
		<tr>
            <td>STATUS :</td>
            <td>
                <?php echo $this->setElement($this->input['ACTIVE']); ?>
            </td>
        </tr>
    </table>
    <table border="0" width="100%" cellpadding="0" cellspacing="0" id="akses_ho">
        <tr>
            <td colspan="2"><hr></td>
        </tr>
        <tr>
            <td colspan="2"><b>AKSES HO</b></td>
        </tr>
        <tr>
            <td>ROLE :</td>
            <td>
                <?php echo $this->setElement($this->input['HO_USER_ROLE']); ?>
            </td>
        </tr>
        <tr>
            <td width="25%">DIVISION :</td>
            <td>
                <?php echo $this->setElement($this->input['HO_DIV_CODE']); ?>
            </td>
        </tr>
        <tr>
            <td width="25%">COST CENTER :</td>
            <td>
                <?php echo $this->setElement($this->input['HO_CC_CODE']); ?>
            </td>
        </tr>
        <tr>
            <td>STATUS :</td>
            <td>
                <?php echo $this->setElement($this->input['HO_STATUS_ACTIVE']); ?>
            </td>
        </tr>
    </table>
</div>
</form>
<input type="hidden" name="rowid" id="rowid" value="" />
<script type="text/javascript">
var url = location.href.toString().split("/");
var mod = url[url.length-1];
$(document).ready(function() {
    init();

    /*$('#HO_DIV_CODE').change(function() {
        var tipe = $('#TIPE_AKSES').val();
        var div = $('#HO_DIV_CODE').val();
        var awal = '<option value="">Pilih</option>';

        $('#HO_CC_CODE').empty();
        $('#HO_CC_CODE').append(awal);

        if (tipe == '2' || tipe == 'ALL') {
            if (div == 'ALL') {
                var option = '<option value="ALL" selected="selected">ALL</option>';
                $('#HO_CC_CODE').append(option);
            } else {
                $.ajax({
                    "type"      : "post",
                    "url"       : "<?php echo $this->baseUrl; ?>setup-master-users/cc",
                    "data"      : { div: div },
                    "dataType"  : "json",
                    "success"   : function(data) {
                        if (data.length < 1) {
                            alert('Belum ada Cost Center untuk Divisi ini');
                        } else {
                            var option = '';
                            if (data.length > 1) {
                                option = '<option value="ALL">ALL</option>';
                            }

                            $.each(data, function(key, value) {
                                option += '<option value="'+value.HCC_CC+'">'+value.HCC_CC+ ' - ' +value.HCC_COST_CENTER+'</option>';
                            });
                            $('#HO_CC_CODE').append(option);
                        }
                    }
                });
            }
        }
    }).change();*/

    $('#TIPE_AKSES').change(function() {
        var nilai = $('#TIPE_AKSES').val();

        if (nilai == '1') {
            $('#akses_site').show();
            $('#akses_ho').hide();
        } else if (nilai == '2') {
            $('#akses_site').hide();
            $('#akses_ho').show();
        } else if (nilai == 'ALL') {
            $('#akses_site').show();
            $('#akses_ho').show();
        } else {
            $('#akses_site').hide();
            $('#akses_ho').hide();
        }
    }).change();
    
    $("input[type=text]").keyup(function(event) {
        var str = $(this).val().toUpperCase();
        $(this).val(str);
    });
    $("#USER_NAME").keydown(function(event) {
        if (event.keyCode == 32) {
            event.preventDefault();
        }
    });
    $("#btn_simpan").click(function() {
        var err = false;
        if ($('#USER_NAME').val() == '') {
            alert('Nama Pengguna Harus Diisi');
            err = true;
            return false;
        } else if ($('#FULL_NAME').val() == '') {
            alert('Nama Lengkap Harus Diisi');
            err = true;
            return false;
        } else if ($('#NIK').val() == '') {
            alert('NIK Harus Diisi');
            err = true;
            return false;
        } else if ($('#TIPE_AKSES').val() == '') {
            alert('Tipe Akses Harus Dipilih');
            err = true;
            return false;
        } else if ($('#TIPE_AKSES').val() == 'ALL') {
            if ($('#BA_CODE').val() == '') {
                alert('Perusahaan Harus Diisi');
                err = true;
                return false;
            } else if ($('#USER_ROLE') == '') {
                alert('Role Harus Dipilih');
                err = true;
                return false;
            } else if ($('#REFERENCE_ROLE').val() == '') {
                alert('Reference Role Harus Dipilih');
                err = true;
                return false;
            } else if ($('#ACTIVE').val() == '') {
                alert('Status Harus Dipilih');
                err = true;
                return false;
            } else if ($('#HO_DIV_CODE').val() == '') {
                alert('Division Harus Diisi');
                err = true;
                return false;
            } else if ($('#HO_CC_CODE').val() == '') {
                alert('Cost Center Harus Diisi');
                err = true;
                return false;
            } else if ($('#HO_USER_ROLE').val() == '') {
                alert('Role Akses HO Harus Dipilih');
                err = true;
                return false;
            } else if ($('#HO_STATUS_ACTIVE').val() == '') {
                alert('Status HO Harus Dipilih');
                err = true;
                return false;
            }
        } else if ($('#TIPE_AKSES').val() == '1') {
            if ($('#BA_CODE').val() == '') {
                alert('Perusahaan Harus Diisi');
                err = true;
                return false;
            } else if ($('#USER_ROLE') == '') {
                alert('Role Harus Dipilih');
                err = true;
                return false;
            } else if ($('#REFERENCE_ROLE').val() == '') {
                alert('Reference Role Harus Dipilih');
                err = true;
                return false;
            } else if ($('#ACTIVE').val() == '') {
                alert('Status Harus Dipilih');
                err = true;
                return false;
            }
        } else if ($('#TIPE_AKSES').val() == '2') {
            if ($('#HO_DIV_CODE').val() == '') {
                alert('Division Harus Diisi');
                err = true;
                return false;
            } else if ($('#HO_CC_CODE').val() == '') {
                alert('Cost Center Harus Diisi');
                err = true;
                return false;
            } else if ($('#HO_USER_ROLE').val() == '') {
                alert('Role Akses HO Harus Dipilih');
                err = true;
                return false;
            } else if ($('#HO_STATUS_ACTIVE').val() == '') {
                alert('Status HO Harus Dipilih');
                err = true;
                return false;
            }
        }

        if (err == false) {
            $.ajax({
                "type"    : "post",
                "url"     : "<?php echo $this->baseUrl; ?>setup-master-users/save",
                "data"    : $("#setup_master_users").serialize() + "&rowid=" + encodeURIComponent($("#rowid").val()),
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
        /*$("#main").find("input[type=text], select").each(function() {
            if ($('#USER_NAME').val() == '') {
                alert($(this).attr("id").toUpperCase() + " BELUM ANDA ISI...!");
            }
            if ($(this).val() == "" || $(this).val() == "-1") {
                alert($(this).attr("id").toUpperCase() + " BELUM ANDA ISI...!");
                $(this).focus();
                err = true;
                return false;
            }
        });
        if (err == false) {
            $.ajax({
                "type"    : "post",
                "url"     : "<?php echo $this->baseUrl; ?>setup-master-users/save",
                "data"    : $("#setup_master_users").serialize() + "&rowid=" + encodeURIComponent($("#rowid").val()),
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
        }*/
    });
    $("#btn_cancel").click(function() {
        self.close();
    });
});
function init() {
    $("span[class=title]").html($("span[class=title]").html() + " (" + mod.toUpperCase() + ")");
    if (mod == "edit") {	
        $("#rowid").val(opener.$("#rowid").val());
        $("#USER_NAME").attr("readonly", true);
		$("#FULL_NAME").attr("readonly", true);
        setTimeout("getRow()", 500);
    }
}
function getRow() {
    $.ajax({
        "type"     : "post",
        "url"      : "<?php echo $this->baseUrl; ?>setup-master-users/row",
        "data"     : "rowid=" + encodeURIComponent($("#rowid").val()),
        "dataType" : "json",
        "success"  : function(data) {
            for (key in data) {
                $("#" + key).val(data[key]);
            }

            $('#TIPE_AKSES').change(function() {
                var nilai = $('#TIPE_AKSES').val();

                if (nilai == '1') {
                    $('#akses_site').show();
                    $('#akses_ho').hide();
                } else if (nilai == '2') {
                    $('#akses_site').hide();
                    $('#akses_ho').show();
                } else if (nilai == 'ALL') {
                    $('#akses_site').show();
                    $('#akses_ho').show();
                } else {
                    $('#akses_site').hide();
                    $('#akses_ho').hide();
                }
            }).change();

           $('#HO_DIV_CODE').trigger('change'); 
        }
    });
}
</script>
