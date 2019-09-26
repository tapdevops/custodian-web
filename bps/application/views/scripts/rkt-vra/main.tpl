<?php
/*
=========================================================================================================================
Project                :     Budgeting & Planning System
Versi                :     2.0.0
Deskripsi            :     View untuk Menampilkan RKT VRA
Function             :    
Disusun Oleh        :     IT Enterprise Solution - PT Triputra Agro Persada
Developer            :     Sabrina Ingrid Davita
Dibuat Tanggal        :     10/07/2013
Update Terakhir        :    30/06/2014
Revisi                :    
    YIR 19/06/2014    :     - perubahan LoV menjadi combo box untuk pilihan region & maturity status
    SID 30/06/2014    :     - fungsi save temp hanya dilakukan ketika ada perubahan data & pindah ke next page
                        - menghilangkan filter maturity status
                        - perbaikan LoV afdeling saat pengisian RKT
                        - penambahan info untuk lock table pada tombol cari, simpan, hapus
=========================================================================================================================
*/
$this->headScript()->appendFile('js/accounting.js');
$this->headScript()->appendFile('js/freezepanes/jquery.freezetablecolumns.1.1.js');
?>
<form name="form_init" id="form_init">
    <div>   
        <fieldset>
            <legend>PENCARIAN</legend>
            <table border="0" width="100%" cellpadding="0" cellspacing="0" id="main">
                <tr>
                    <td width="15%">PERIODE BUDGET :</td>
                    <td width="85%">
                        <input type="text" name="budgetperiod" id="budgetperiod" value="<?=$this->period?>" style="width:200px;" class='filter'/>
                        <input type="button" name="pick_period" id="pick_period" value="...">
                    </td>
                </tr>
                <tr>
                    <td>REGION :</td>
                    <td>
                        <?php echo $this->setElement($this->input['src_region_code']);?>
                    </td>
                </tr>
                <tr>
                    <td>BUSINESS AREA :</td>
                    <td>
                        <input type="hidden" name="key_find" id="key_find" value="" style="width:200px;" />
                        <input type="text" name="src_ba" id="src_ba" value="" style="width:200px;" class='filter'/>
                        <input type="button" name="pick_ba" id="pick_ba" value="...">
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="button" name="btn_find" id="btn_find" value="CARI" class="button" />
                        <input type="button" name="btn_refresh" id="btn_refresh" value="RESET" class="button" />
                    </td>
                </tr>
            </table>
            <input type="hidden" name="page_num" id="page_num" value="1" />
            <input type="hidden" name="page_rows" id="page_rows" value="50" />
        </fieldset>
    </div>
    <br />
    <div>
        <fieldset>
            <legend>RKT VRA</legend>
            <table border="0" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="15%"><b>Total MPP Helper :</b></td>
                    <td width="85%"><input type="text" name="total_mpp_helper" id="total_mpp_helper" style='width:50px; text-align:right;' readonly='readonly'/></td>
                </tr>
                <tr>
                    <td width="15%"><b>Total MPP Operator :</b></td>
                    <td width="85%"><input type="text" name="total_mpp_operator" id="total_mpp_operator" style='width:50px; text-align:right;' readonly='readonly'/></td>
                </tr>
                <tr>
                    <td width="15%"><b>Total Jam Kerja :</b></td>
                    <td width="85%"><input type="text" name="standar_jam_kerja" id="standar_jam_kerja" style='width:50px; text-align:right;' readonly='readonly'/></td>
                </tr>
            </table>    
    
            <table border="0" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="50%">
                        <input type="button" name="btn_upload" id="btn_upload" value="UPLOAD" class="button" />                    
                        <input type="button" name="btn_export_csv" id="btn_export_csv" value="EXPORT DATA" class="button" />                    
                    </td>
                    <td width="50%" align="right">            
                        <input type="button" name="btn_unlock" id="btn_unlock" value="UNLOCK" class="button" />
                        <input type="button" name="btn_lock" id="btn_lock" value="LOCK" class="button" />    
                        <input type="button" name="btn_add" id="btn_add" value="TAMBAH BARIS" class="button"/>
                        <input type="button" name="btn_save_temp" id="btn_save_temp" value="SIMPAN" class="button" />
                        <input type="button" name="btn_save" id="btn_save" value="HITUNG" class="button" />
                    </td>
                </tr>
            </table>
            
            <table id='mainTable' border="0" cellpadding="1" cellspacing="1" class='data_header'>
            <thead>
                <tr name='content' id='content'>
                    <th>+</th>
                    <th>x</th>
                    <th>PERIODE<BR>BUDGET</th>
                    <th>BUSINESS<BR>AREA</th>
                    <th>SUB KATEGORI<BR>VRA</th>
                    <th>VRA CODE</th>
                    <th>VRA TYPE</th>
                    <th>DESKRIPSI<BR>VRA TYPE</th>
                    <th>INTERNAL<BR>ORDER</th>
                    <th>JUMLAH ALAT</th>
                    <th>TAHUN ALAT</th>
                    <th>UOM</th>
                    <th>STANDAR KERJA<BR>QTY/HARI</th>
                    <th>STANDAR KERJA<BR>HARI VRA/TAHUN</th>
                    <th>STANDAR KERJA<BR>QTY/TAHUN</th>
                    <th>STANDAR KERJA<BR>TOTAL QTY/TAHUN</th>
                    <th>KOMPARISON DG<BR>OUTLOOK HM DAN KM </th>
                    <th>TENAGA KERJA<BR>OPERATOR</th>
                    <th>TENAGA KERJA<BR>HELPER</th>
                    <th>PAJAK & PERIJINAN</th>
                    <th>RENTAL<BR>QTY/SAT</th>
                    <th>RENTAL<BR>HARGA</th>
                    <th>SPAREPART HARGA</th>
                    <th>OVERHAUL HARGA</th>
                    <th>SERVIS WORKSHOP JAM</th>
                    <th>SERVIS BENGKEL LUAR HARGA</th>
                    <th>RP / QTY SD BULAN<BR>DIBUAT BUDGET</th>
                </tr>
            </thead>
            <tbody name='data' id='data'>
                <tr name='content' id='content' style="display:none;" >
                    <td align='center' width='20px'>
                        <input type="button" name="btn00[]" id="btn00_" class='button_add'/>
                    </td>
                    <td align='center' width='20px'>
                        <input type="button" name="btn01[]" id="btn01_" class='button_delete'/>
                    </td>
                    <td width='50px'>
                        <input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
                        <input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
                        <input type="hidden" name="trxrktcode[]" id="trxrktcode_" readonly="readonly"/>
                        <input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
                        <input type="text" name="text02[]" id="text02_" readonly="readonly"/>
                    </td>
                    <td width='50px'><input type="text" name="text03[]" id="text03_" readonly="readonly"/></td>
                    <td width='150px'><input type="text" name="text04[]" id="text04_" readonly="readonly"/></td>
                    <td width='100px'>
                        <input type="hidden" name="text005[]" id="text005_"/>
                        <input type="text" name="text05[]" id="text05_" title="Tekan F9 Untuk Memilih."/>
                    </td>
                    <td width='250px'><input type="text" name="text06[]" id="text06_" readonly="readonly"/></td>
                    <td width='250px'>
                        <input type="hidden" name="text007[]" id="text007_"/>
                        <input type="text" name="text07[]" id="text07_"/>
                    </td>
                    <td width='150px'>
                        <input type="hidden" name="text024[]" id="text024_"/>
                        <input type="text" name="text24[]" id="text24_" />
                    </td>
                    <td width='50px'><input type="text" name="text08[]" id="text08_"/></td>
                    <td width='50px'>
                        <input type="hidden" name="text009[]" id="text009_"/>
                        <input type="text" name="text09[]" id="text09_" maxlength='4' />
                    </td>
                    <td width='50px'><input type="text" name="text10[]" id="text10_" readonly="readonly"/></td>
                    <td width='70px'><input type="text" name="text11[]" id="text11_"/></td>
                    <td width='70px'><input type="text" name="text12[]" id="text12_"/></td>
                    <td width='70px'><input type="text" name="text13[]" id="text13_" readonly="readonly"/></td>
                    <td width='70px'><input type="text" name="text14[]" id="text14_" readonly="readonly"/></td>
                    <td width='50px'><input type="text" name="text25[]" id="text25_"/></td>
                    <td width='50px'><input type="text" name="text15[]" id="text15_"/></td>
                    <td width='50px'><input type="text" name="text16[]" id="text16_"/></td>
                    <td width='120px'><input type="text" name="text17[]" id="text17_"/></td>
                    <td width='120px'><input type="text" name="text18[]" id="text18_"/></td>
                    <td width='120px'><input type="text" name="text19[]" id="text19_"/></td>
                    <td width='120px'><input type="text" name="text20[]" id="text20_"/></td>
                    <td width='120px'><input type="text" name="text21[]" id="text21_"/></td>
                    <td width='50px'><input type="text" name="text22[]" id="text22_"/></td>
                    <td width='120px'><input type="text" name="text23[]" id="text23_"/></td>
                    <td width='120px'><input type="text" name="text26[]" id="text26_"/></td>
                </tr>            
            </tbody>
            </table>
            
            <br />
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="50%">
                        <span id="record_counter">DATA: ? / ?</span>
                    </td>
                    <td width="50%" align="right">
                        <input type="button" name="btn_first" id="btn_first" value="&lt;&lt;" class="button"/>
                        <input type="button" name="btn_prev" id="btn_prev" value="&lt;" class="button"/>
                        <input type="button" name="btn_next" id="btn_next" value="&gt;" class="button"/>
                        <input type="button" name="btn_last" id="btn_last" value="&gt;&gt;" class="button"/>
                        <span id="page_counter" style='margin-left:10px'>HALAMAN: ? / ?</span>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
    <input type="hidden" name="controller" id="controller" value=""/>
</form>    
<?php
// you may change these value
echo $this->partial('popup.tpl', array('width'  => 1024,
                                       'height' => 400));

?>
<script type="text/javascript">
var count = 0;
var page_num  = parseInt($("#page_num").val(), 10);
var page_rows = parseInt($("#page_rows").val(), 10);
var page_max  = 0;
$(document).ready(function() {
    $("#btn_lock").hide();
    $("#btn_unlock").hide();
    //set nama kolom yang mengandung tahun
    $(".period_budget").html($("#budgetperiod").val());
    $(".period_before").html(parseInt($("#budgetperiod").val()) - 1);

    //FREEZE PANES
    $('#mainTable').freezeTableColumns({ 
        width:       970,   // required
        height:      400,   // required
        numFrozen:   12,     // optional
        frozenWidth: 470,   // optional
        clearWidths: false,  // optional
    });//freezeTableColumns
    
    
    //BUTTON ACTION    
    $("#btn_find").click(function() {
        //clear data
        clearDetail();
        
        var reference_role = "<?=$this->referencerole?>";
        var region = $("#src_region").val();
        var ba_code = $("#src_ba").val();
        var budgetperiod = $("#budgetperiod").val();
        var current_budgetperiod = "<?=$this->period?>";
        
        if ( ba_code == '' ) {
            alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
        } else {
            $.ajax({
                type    : "post",
                url     : "rkt-vra/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
                data    : $("#form_init").serialize(),
                cache   : false,
                dataType: "json",
                success : function(data) {
                    if(data==1){
                        //cek status sequence current norma/rkt
                        page_num = (page_num) ? page_num : 1;
                        getData(); 
                        $.ajax({
                            type     : "post",
                            url      : "rkt-vra/get-status-periode", //cek status periode
                            data     : $("#form_init").serialize(),
                            cache    : false,
                            dataType: "json",
                            success  : function(data) {
                                $("#btn_save_temp").hide();
                                if (data == 'CLOSE') {
                                        $("#btn_save").hide();
                                        $("#btn_add").hide();
                                        $("#btn_upload").hide();
                                        $("#btn001_").hide();
                                    }else{
                                        $.ajax({
                                            type    : "post",
                                            url     : "rkt-vra/check-locked-seq", //check apakah status lock sendiri apakah lock
                                            data    : $("#form_init").serialize(),
                                            cache   : false,
                                            dataType: "json",
                                            success : function(data) {
                                                if(data.STATUS == 'LOCKED'){
                                                    $("#btn_save").hide();
                                                    $("#btn_add").hide();
                                                    $("input[id^=btn01_]").hide();
                                                    $("#btn_upload").hide();
                                                    $("#btn_unlock").show();
                                                    $("#btn_lock").hide();
                                                    $("#btn001_").hide();
                                                }else{
                                                    $("#btn001_").show();
                                                    $("#btn_save").show();
                                                    $("#btn_add").show();
                                                    $("#btn_upload").show();
                                                    $("input[id^=btn01_]").show();
                                                    $("#btn_unlock").hide();
                                                    $("#btn_lock").show();
                                                }
                                            }
                                        })
                                    }
                                }
                        });
                    }else{
                        alert('Lakukan finalisasi (LOCK) terlebih dahulu terhadap : '+data+'');
                        $("#btn_save").hide();
                        $("#btn_unlock").hide();
                        $("#btn_lock").hide();
                        $("#btn_upload").hide();    
                        $("#btn001_").hide();
                    }
                }
            })        

        }
    });    
    $("#btn_refresh").click(function() {
        location.reload();
    });

    $("#btn_add").live("click", function(event) {
        var budgetperiod = $("#budgetperiod").val();
        var regioncode = $("#src_region").val();
        var bacode = $("#key_find").val();
    
        if( bacode == '' || regioncode == ''){
            alert('Anda Harus Memilih Region dan Business Area Terlebih Dahulu.');
        }
        else{
            //left freeze panes
            var tr = $("#data_freeze tr:eq(0)").clone();
            $("#data_freeze").append(tr);
            var index = ($("#data_freeze tr").length - 1);                    
            $("#data_freeze tr:eq(" + index + ")").find("input, select").each(function() {
                $(this).attr("id", $(this).attr("id") + index);
            });    
            
            //right freeze panes

            var tr = $("#data tr:eq(0)").clone();
            $("#data").append(tr);
            var index = ($("#data tr").length - 1);                    
            $("#data tr:eq(" + index + ")").find("input, select").each(function() {
                $(this).attr("id", $(this).attr("id") + index);
            });        
                        
            //set default field
            setDefaultField(index);
        }
    });

    $("#btn_upload").live("click", function() {
        $("#controller").val('upload/rkt-vra');
        popup("upload/main", "detail", 700, 400);
    });

    $("input[id^=btn00_]").live("click", function(event) {
        $("#btn_add").trigger("click");
    });

    $("input[id^=btn01_]").live("click", function(event) {
        
        var row = $(this).attr("id").split("_")[1];
        var rowid = $("#text00_" + row).val();
        
        $.ajax({
            type    : "post",
            url     : "rkt-vra/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
            data    : $("#form_init").serialize(),
            cache   : false,
            dataType: "json",
            success : function(data) {
                if(data==1){
                    //cek status sequence current norma/rkt
                    $.ajax({
                        type    : "post",
                        url     : "rkt-vra/check-locked-seq",
                        data    : $("#form_init").serialize(),
                        cache   : false,
                        dataType: "json",
                        success : function(data) {
                            if(data.STATUS == 'LOCKED'){ 
                                alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
                            }else{
                                if(confirm("Apakah Anda Yakin Untuk Menghapus Data ke - " + row + " ?")){
                                    //cek jika rowid kosong & klik delete, maka kosongkan seluruh data
                                    if (rowid == '') {
                                        clearTextField(row);
                                    }
                                    else {
                                        $.ajax({
                                            type     : "post",
                                            url      : "rkt-vra/delete",
                                            data     : { 
                                                         ROW_ID: $("#text00_" + row).val(), 
                                                         PERIOD_BUDGET: $("#text02_" + row).val(),
                                                         BA_CODE: $("#text03_" + row).val(),
                                                         VRA_CODE: $("#text05_" + row).val(),
                                                         DESCRIPTION_VRA: $("#text07_" + row).val(),
                                                         TAHUN_TANAM: $("#text09_" + row).val(),
                                                         OLD_VRA_CODE: $("#text005_" + row).val(),
                                                         OLD_DESCRIPTION_VRA: $("#text007_" + row).val(),
                                                         OLD_INTERNAL_ORDER: $("#text024_" + row).val(),
                                                         OLD_TAHUN_TANAM: $("#text009_" + row).val()
                                                       },
                                            cache    : false,
                                            dataType : 'json',
                                            success  : function(data) {
                                                if (data.return == "locked") {
                                                    alert("Anda tidak dapat menghapus data karena sedang terjadi proses perhitungan "+ data.module +" oleh "+ data.insert_user +".\nData Anda belum terhapus. Harap mencoba melakukan proses penghapusan data beberapa saat lagi.");
                                                }else if (data.return == "done") {
                                                    clearTextField(row);
                                                    alert("Data berhasil dihapus.");
                                                }else{
                                                    alert(data.return);
                                                }
                                            }
                                        });
                                    }    
                                }
                            }
                        }
                    })
                }else{
                    alert('Lakukan finalisasi (LOCK) terlebih dahulu terhadap : '+data+'');
                }
            }
        })
    });

    $("#btn_save").click( function() {
        var reference_role = "<?=$this->referencerole?>";
        var region = $("#src_region").val();
        var ba_code = $("#src_ba").val();
        if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
            alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
        } else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
            alert("Anda Harus Memilih Region Terlebih Dahulu.");
        } else if ( validateInput() == false ) {
            alert("Field yang Berwarna Merah, Harus Diisi Terlebih Dahulu.");
        } else if ( validate() == false ) {
            alert("Field yang Berwarna Merah, Harus Diisi Terlebih Dahulu.");
        } else if ( validateTahunAlat() == false ) {
            alert("Tahun Alat Harus 4 Digit.");
        } else if ( validateJumlahAlat() == false ) {
            alert("Jumlah Qty Rental Tidak Boleh Lebih Besar Dari Jumlah Alat.");
        } else if ( validateJamKerjaWra() == false ) {
            alert("Jumlah Jam Kerja WRA Tidak Boleh Lebih Besar Dari Jumlah Jam Kerja di Norma Jam Kerja.");
        } else {
            var total_mpp_operator = ($("#total_mpp_operator").val()).split(',').join('');
            var total_mpp_helper = ($("#total_mpp_helper").val()).split(',').join('');
            
            var sum_helper = parseInt(0);
            var sum_operator = parseInt(0);
            
            //cek max tenaga kerja
            $("input[id^=text15_]").each(function(key,value) {
                var tenaga_operator = ($("#text15_"+key).val()) ? ($("#text15_"+key).val()).split(',').join('') : 0;
                var tenaga_helper = ($("#text16_"+key).val()) ? ($("#text16_"+key).val()).split(',').join('') : 0;
                
                sum_operator += parseInt(tenaga_operator);
                sum_helper += parseInt(tenaga_helper);
            });
            
            var selisih_helper = parseInt(total_mpp_helper) - parseInt(sum_helper);
            var selisih_operator = parseInt(total_mpp_operator) - parseInt(sum_operator);
            
            if((parseInt(selisih_helper) < 0) || (parseInt(selisih_operator) < 0)){
                alert("Total Helper / MPP Helper : " + sum_helper + " / " + total_mpp_helper + ". Total Operator / MPP Operator : " + sum_operator + " / " + total_mpp_operator + ".  Data Tidak Bisa Dihitung Apabila Total Helper atau Total Operator Lebih Besar Dari MPP Helper atau MPP Operator.");
                
                if (parseInt(selisih_helper) < 0){
                    $("input[id^=text16_]").addClass("error");
                }else{
                    $("input[id^=text16_]").removeClass("error");
                }
                if (parseInt(selisih_operator) < 0){
                    $("input[id^=text15_]").addClass("error");
                }else{
                    $("input[id^=text15_]").removeClass("error");
                }
            } else{
                alert("Total Helper / MPP Helper : " + sum_helper + " / " + total_mpp_helper + ". Total Operator / MPP Operator : " + sum_operator + " / " + total_mpp_operator + ".");
                
                $("input[id^=text15_]").removeClass("error");
                $("input[id^=text16_]").removeClass("error");
                
                $.ajax({
                type    : "post",
                url     : "rkt-vra/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
                data    : $("#form_init").serialize(),
                cache   : false,
                dataType: "json",
                success : function(data) {
                    if(data==1){
                        //cek status sequence current norma/rkt
                        $.ajax({
                            type    : "post",
                            url     : "rkt-vra/check-locked-seq",
                            data    : $("#form_init").serialize(),
                            cache   : false,
                            dataType: "json",
                            success : function(data) {
                                if(data.STATUS == 'LOCKED'){ 
                                    alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
                                }else{
                                        $.ajax({
                                            type     : "post",
                                            url      : "rkt-vra/save",
                                            data     : $("#form_init").serialize(),
                                            cache    : false,
                                            dataType : 'json',
                                            success  : function(data) {
                                                if (data.return == "locked") {
                                                    alert("Anda tidak dapat melakukan perhitungan data karena sedang terjadi proses perhitungan "+ data.module +" oleh "+ data.insert_user +".\nData Anda belum tersimpan. Harap mencoba melakukan proses perhitungan beberapa saat lagi.");
                                                }else if (data.return == "done") {
                                                    alert("Data berhasil dihitung.");
                                                    $("#btn_find").trigger("click");
                                                }else if (data.return == "kosong"){    
                                                    alert("Tidak Bisa Proses, Karena Tidak Ada Data yang diubah ");
                                                }else{
                                                    alert(data.return);
                                                }
                                            }
                                        });
                                }
                            }
                        })
                    }else{
                        alert('Lakukan finalisasi (LOCK) terlebih dahulu terhadap : '+data+'');
                    }
                }
            })
            }
        }
    });

    //untuk proses simpan draft
    $("#btn_save_temp").click( function() {
        //DEKLARASI VARIABEL
        var reference_role = "<?=$this->referencerole?>";    //REFERENCE_ROLE
        var budgetperiod = $("#budgetperiod").val();        //PERIODE BUDGET
        var current_budgetperiod = "<?=$this->period?>";    //PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
        var src_region_code = $("#src_region_code").val();    //KODE REGION
        var key_find = $("#key_find").val();                //KODE BA
        var region = $("#src_region").val();                //DESKRIPSI REGION
        var ba_code = $("#src_ba").val();                    //DESKRIPSI BA
        var search = $("#search").val();                    //SEARCH FREE TEXT
        var src_coa_code = $("#src_coa_code").val();        //SEARCH KODE COA
        var coa = $("#src_coa").val();                        //SEARCH DESKRIPSI COA
    
        if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
            alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
        } else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
            alert("Anda Harus Memilih Region Terlebih Dahulu.");
        } else {
            $.ajax({
                type     : "post",
                url      : "rkt-vra/save-temp",
                data     : $("#form_init").serialize(),
                cache    : false,
                success  : function(data) {
                    if (data == "done") {
                        alert("Data berhasil disimpan tetapi belum diproses. Silahkan klik tombol Hitung untuk memproses data.");    
                    }else if (data == "no_alert") {
                    }else{
                        alert(data);
                    }
                }
            });            
        } 
    });
    $("#btn_export_csv").live("click", function() {
        //DEKLARASI VARIABEL
        var reference_role = "<?=$this->referencerole?>";    //REFERENCE_ROLE
        var budgetperiod = $("#budgetperiod").val();        //PERIODE BUDGET
        var current_budgetperiod = "<?=$this->period?>";    //PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
        var src_region_code = $("#src_region_code").val();    //KODE REGION
        var key_find = $("#key_find").val();                //KODE BA
        var region = $("#src_region").val();                //DESKRIPSI REGION
        var ba_code = $("#src_ba").val();                    //DESKRIPSI BA
    
        if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
            alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
        } else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
            alert("Anda Harus Memilih Region Terlebih Dahulu.");
        } else {
            window.open("<?=$_SERVER['PHP_SELF']?>/download/data-rkt-vra/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find,'_blank');
        }
    });
    
    //PICK PERIODE BUDGET
    $("#pick_period").click(function() {
        popup("pick/budget-period", "pick", 700, 400 );
    });    
    $("#budgetperiod").live("keydown", function(event) {
        //tekan F9
        if (event.keyCode == 120) {
            //lov
            popup("pick/budget-period", "pick", 700, 400 );
        }else{
            event.preventDefault();
        }
    });    
        
    $("#src_region").live("keydown", function(event) {
        //tekan F9
        if (event.keyCode == 120) {
            //lov
            popup("pick/region", "pick", 700, 400 );
        }else{
            event.preventDefault();
        }
    });
    
    //PICK BA
    $("#pick_ba").click(function() {
        var regionCode = $("#src_region_code").val();
        popup("pick/business-area/regioncode/"+regionCode, "pick", 700, 400 );
    });
    $("#src_ba").live("keydown", function(event) {
        //tekan F9
        if (event.keyCode == 120) {
            //lov
            popup("pick/business-area", "pick", 700, 400 );
        }else{
            event.preventDefault();
        }
    });
    
    //PAGING
    $("#btn_first").click(function() {
        $("#btn_save_temp").trigger("click");
        page_num = 1;
        clearDetail();
        getData();
    });
    $("#btn_prev").click(function() {
        $("#btn_save_temp").trigger("click");
        page_num--;
        clearDetail();
        getData();
    });
    $("#btn_next").click(function() {
        $("#btn_save_temp").trigger("click");
        page_num++;
        clearDetail();
        getData();
    });
    $("#btn_last").click(function() {
        $("#btn_save_temp").trigger("click");
        page_num = page_max;
        clearDetail();
        getData();
    });
    $("#data tr input").live("focus", function() {
        var tr = $(this).parent().parent();
        var table = $(tr).parent();
        var index = $(table).children().index(tr);

        $("#record_counter").html("DATA: " + (((page_num - 1) * page_rows) + index) + " / " + count);
        $("#data").find("tr").attr("bgcolor", "#FFFFFF");
        $("#data").find("tr:eq(" + (index) + ")").attr("bgcolor", "#FFFF00");
        $("#data_freeze").find("tr").attr("bgcolor", "#FFFFFF");
        $("#data_freeze").find("tr:eq(" + (index) + ")").attr("bgcolor", "#FFFF00");
    });    
    $("#data_freeze tr input").live("focus", function() {
        var tr = $(this).parent().parent();
        var table = $(tr).parent();
        var index = $(table).children().index(tr);

        $("#record_counter").html("DATA: " + (((page_num - 1) * page_rows) + index) + " / " + count);
        $("#data").find("tr").attr("bgcolor", "#FFFFFF");
        $("#data").find("tr:eq(" + (index) + ")").attr("bgcolor", "#FFFF00");
        $("#data_freeze").find("tr").attr("bgcolor", "#FFFFFF");
        $("#data_freeze").find("tr:eq(" + (index) + ")").attr("bgcolor", "#FFFF00");
    });
    
    //LOV UNTUK INPUTAN
    $("input[id^=text05_]").live("keydown", function(event) {
        var row = $(this).attr("id").split("_")[1];
        var bacode = $("#key_find").val();
        //tekan F9
        if (event.keyCode == 120) {
            //lov
            popup("pick/vra/module/rktVra/row/" + row, "pick");
            
        }else{
            event.preventDefault();
        }
    });    
    
    $("#btn_lock").live("click", function(event) {
        if(confirm("Anda Yakin Untuk Melakukan Finalisasi Data?")){
            $.ajax({
                type     : "post",
                url      : "rkt-vra/upd-locked-seq-status",
                data     : $("#form_init").serialize()+"&status=LOCKED",
                cache    : false,
                dataType : 'json',
                success  : function(data) {
                    alert("Finalisasi Data Berhasil.");
                    $("#btn_find").trigger("click");
                }
            });    
        }
    });
    
    $("#btn_unlock").live("click", function(event) {
        if(confirm("Anda Yakin Untuk Memproses Ulang Data?")){
            $.ajax({
                type     : "post",
                url      : "rkt-vra/upd-locked-seq-status",
                data     : $("#form_init").serialize()+"&status=UNLOCKED",
                cache    : false,
                dataType : 'json',
                success  : function(data) {
                    
                    alert("Anda Dapat Melakukan Proses Ulang Data.");
                    $("#btn_find").trigger("click");
                }
            });    
        }
    });
});

function setDefaultField(index){
    //DEKLARASI VARIABEL
    var reference_role = "<?=$this->referencerole?>";    //REFERENCE_ROLE
    var budgetperiod = $("#budgetperiod").val();        //PERIODE BUDGET
    var current_budgetperiod = "<?=$this->period?>";    //PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
    var src_region_code = $("#src_region_code").val();    //KODE REGION
    var key_find = $("#key_find").val();                //KODE BA
    var region = $("#src_region").val();                //DESKRIPSI REGION
    var ba_code = $("#src_ba").val();                    //DESKRIPSI BA
    var trxCode = genTransactionCode(budgetperiod, key_find, 'VRA');
    
    //left freeze panes
    $("#data_freeze tr:eq(" + index + ") input[id^=text00_]").val("");
    $("#data_freeze tr:eq(" + index + ") input[id^=text01_]").val("");
    $("#data_freeze tr:eq(" + index + ") input[id^=trxrktcode_]").val(trxCode);
    $("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(budgetperiod);
    $("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(key_find);
    $("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val("");
    $("#data_freeze tr:eq(" + index + ") input[id^=text005_]").val("");
    $("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val("");
    $("#data_freeze tr:eq(" + index + ") input[id^=text05_]").addClass("required");
    $("#data_freeze tr:eq(" + index + ") input[id^=text06_]").val("");
    $("#data_freeze tr:eq(" + index + ") input[id^=text007_]").val("");
    $("#data_freeze tr:eq(" + index + ") input[id^=text07_]").val("");
    $("#data_freeze tr:eq(" + index + ") input[id^=text07_]").addClass("required");
    $("#data_freeze tr:eq(" + index + ") input[id^=text024_]").val("-");
    $("#data_freeze tr:eq(" + index + ") input[id^=text24_]").val("-");
    $("#data_freeze tr:eq(" + index + ") input[id^=text08_]").val("");
    $("#data_freeze tr:eq(" + index + ") input[id^=text08_]").addClass("integer");
    $("#data_freeze tr:eq(" + index + ") input[id^=text08_]").addClass("requirednotzero");
    $("#data_freeze tr:eq(" + index + ") input[id^=text009_]").val("");
    $("#data_freeze tr:eq(" + index + ") input[id^=text09_]").val("");
    $("#data_freeze tr:eq(" + index + ") input[id^=text09_]").css("text-align","right");
    $("#data_freeze tr:eq(" + index + ") input[id^=text09_]").addClass("required");
    $("#data_freeze tr:eq(" + index + ") input[id^=text10_]").val("");
    $("#data_freeze tr:eq(" + index + ")").removeAttr("style");
    
    //right freeze panes
    $("#data tr:eq(" + index + ") input[id^=text11_]").val("");
    $("#data tr:eq(" + index + ") input[id^=text11_]").addClass("integer");
    $("#data tr:eq(" + index + ") input[id^=text12_]").val("");
    $("#data tr:eq(" + index + ") input[id^=text12_]").addClass("integer");
    $("#data tr:eq(" + index + ") input[id^=text13_]").val("");
    $("#data tr:eq(" + index + ") input[id^=text13_]").addClass("integer");
    $("#data tr:eq(" + index + ") input[id^=text14_]").val("");
    $("#data tr:eq(" + index + ") input[id^=text14_]").addClass("integer");
    $("#data tr:eq(" + index + ") input[id^=text25_]").val("");                           //tambahan
    $("#data tr:eq(" + index + ") input[id^=text25_]").addClass("integer");                //tambahan
    $("#data tr:eq(" + index + ") input[id^=text25_]").addClass("requirednotzero");        //tambahan
    $("#data tr:eq(" + index + ") input[id^=text15_]").val("");
    $("#data tr:eq(" + index + ") input[id^=text15_]").addClass("integer");
    $("#data tr:eq(" + index + ") input[id^=text16_]").val("");
    $("#data tr:eq(" + index + ") input[id^=text16_]").addClass("integer");
    $("#data tr:eq(" + index + ") input[id^=text17_]").val("");
    $("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number");
    $("#data tr:eq(" + index + ") input[id^=text18_]").val("");
    $("#data tr:eq(" + index + ") input[id^=text18_]").addClass("integer");
    $("#data tr:eq(" + index + ") input[id^=text19_]").val("");
    $("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number");
    $("#data tr:eq(" + index + ") input[id^=text20_]").val("");
    $("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number");
    $("#data tr:eq(" + index + ") input[id^=text21_]").val("");
    $("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number");
    $("#data tr:eq(" + index + ") input[id^=text22_]").val("");
    $("#data tr:eq(" + index + ") input[id^=text22_]").addClass("integer");
    $("#data tr:eq(" + index + ") input[id^=text23_]").val("");
    $("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number");
    $("#data tr:eq(" + index + ") input[id^=text26_]").val("");                            //tambahan
    $("#data tr:eq(" + index + ") input[id^=text26_]").addClass("number");                //tambahan
    $("#data tr:eq(" + index + ") input[id^=text26_]").addClass("requirednotzero");        //tambahan
    $("#data tr:eq(" + index + ")").removeAttr("style");
    $("#data tr:eq(" + index + ") input[id^=text23_]").focus();
}

function validate(){    
    var result = true;
    
    $("input[id^=text18_]").each(function(key,value) {
        if (key > 0){
            var mystring = this.value;
            var value = mystring.split(',').join('');
            
            var row = $(this).attr("id").split("_")[1];
            var mystring1 = $("#text19_" + row).val();
            var harga = mystring1.split(',').join('');
            
            /*if (((parseFloat(value)) && (parseFloat(value) != 0.00)) && ((parseFloat(harga) == '') || (parseFloat(harga) == 0.00))) {
                $("#text19_"+key).addClass("error");
                $("#text19_"+key).focus();
                result = false;
            }else{
                $("#text19_"+key).removeClass("error");
            }*/
        }
    });
    
    $("input[id^=text19_]").each(function(key,value) {
        if (key > 0) {
            var mystring = this.value;
            var value = mystring.split(',').join('');
                    
            var row = $(this).attr("id").split("_")[1];
            var mystring1 = $("#text18_" + row).val();
            var qty = mystring1.split(',').join('');
            
            if (((parseFloat(value)) && (parseFloat(value) != 0.00)) && ((parseFloat(qty) == '') || (parseFloat(qty) == 0.00))) {
                $("#text18_"+key).addClass("error");
                $("#text18_"+key).focus();
                result = false;
            }else{
                $("#text18_"+key).removeClass("error");
            }
        }
    });
    return result;
}

function validateJumlahAlat(){    
    var result = true;
    
    $("input[id^=text18_]").each(function(key,value) {
        if ((key > 0) && ($("#text03_" + key).val() != "")){
            var mystring = this.value;
            var value = mystring.split(',').join('');
            
            var row = $(this).attr("id").split("_")[1];
            var mystring1 = $("#text08_" + row).val();
            var jumlah_alat = mystring1.split(',').join('');
            
            if (parseFloat(value) > parseFloat(jumlah_alat)) {
                $("#text18_"+key).addClass("error");
                $("#text18_"+key).focus();
                result = false;
            }else{
                $("#text18_"+key).removeClass("error");
            }
        }
    });
    return result;
}

function validateTahunAlat(){
    var result = true;
    
    $("input[id^=text09_]").each(function(key,value) {
        if ((key > 0) && ($("#text03_" + key).val() != "")){
            var mystring = this.value;
            var value = mystring.split(',').join('');
                    
            if(value.length != 4) {
                $("#text09_"+key).css("background", "red");
                $("#text09_"+key).focus();
                result = false;
            }else{
                $("#text09_"+key).removeClass("error");
            }
        }
    });
    
    return result;
}

function validateJamKerjaWra(){    
    var result = true;
    var total_jam_kerja = 0;
    var standar_jam_kerja = ($("#standar_jam_kerja").val()).split(',').join('');
    
    $("input[id^=text22_]").each(function(key,value) {
        if (key > 0){
            var mystring = this.value;
            var value = mystring.split(',').join('');
            
            total_jam_kerja += parseFloat(value);
        }
    });
    
    if ( parseFloat(total_jam_kerja) > parseFloat(standar_jam_kerja) ) {
        $("input[id^=text22_]").addClass("error");
        result = false;
    }else{
        $("input[id^=text22_]").removeClass("error");
    }
    return result;
}

function getData(){
    $("#page_num").val(page_num);
    
    //
    $.ajax({
        type    : "post",
        url     : "rkt-vra/list",
        data    : $("#form_init").serialize(),
        cache   : false,
        dataType: "json",
        success : function(data) {
            if (data.return == 'locked') {
                alert("Anda tidak dapat melakukan perubahan data karena sedang terjadi proses perhitungan "+ data.module +" oleh "+ data.insert_user +".");
                $("#btn_add").hide();
                $("#btn_save_temp").hide();
                $("#btn_save").hide();
                $("#btn01_").hide();
            } else {
                count = data.count;
                page_max = Math.ceil(count / page_rows);
                if (page_max == 0) {
                    page_max = 1;
                }
                $("#btn_first").attr("disabled", page_num == 1);
                $("#btn_prev").attr("disabled", page_num == 1);
                $("#btn_next").attr("disabled", page_num == page_max);
                $("#btn_last").attr("disabled", page_num == page_max);
                $("#page_counter").html("HALAMAN: " + page_num + " / " + page_max);
                if (count > 0) {
                    $.each(data.rows, function(key, row) {
                        //left freeze panes
                        var tr = $("#data_freeze tr:eq(0)").clone();
                        $("#data_freeze").append(tr);
                        var index = ($("#data_freeze tr").length - 1);                    
                        $("#data_freeze tr:eq(" + index + ")").find("input, select").each(function() {
                            $(this).attr("id", $(this).attr("id") + index);
                        });        
                        
                        //mewarnai jika row nya berasal dari temporary table
                        if (row.FLAG_TEMP) {cekTempData(index);}            
                        
                        $("#data_freeze tr:eq(" + index + ") input[id^=btn00_]").val("");
                        $("#data_freeze tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
                        
                        //tambah rowid temp
                        $("#data_freeze tr:eq(" + index + ") input[id^=trxrktcode_]").val(row.TRX_RKT_VRA_CODE);
                        
                        //left freeze panes row
                        $("#data_freeze tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
                        $("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
                        $("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
                        $("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(row.VRA_SUB_CAT_DESCRIPTION);
                        $("#data_freeze tr:eq(" + index + ") input[id^=text005_]").val(row.VRA_CODE);
                        $("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(row.VRA_CODE);
                        $("#data_freeze tr:eq(" + index + ") input[id^=text05_]").addClass("required");
                        $("#data_freeze tr:eq(" + index + ") input[id^=text06_]").val(row.VRA_TYPE);
                        $("#data_freeze tr:eq(" + index + ") input[id^=text007_]").val(row.DESCRIPTION_VRA);
                        $("#data_freeze tr:eq(" + index + ") input[id^=text07_]").val(row.DESCRIPTION_VRA);
                        $("#data_freeze tr:eq(" + index + ") input[id^=text07_]").addClass("required");
                        $("#data_freeze tr:eq(" + index + ") input[id^=text24_]").val(row.INTERNAL_ORDER);
                        $("#data_freeze tr:eq(" + index + ") input[id^=text024_]").val(row.INTERNAL_ORDER);
                        $("#data_freeze tr:eq(" + index + ") input[id^=text08_]").val(accounting.formatNumber(row.JUMLAH_ALAT, 0));
                        $("#data_freeze tr:eq(" + index + ") input[id^=text08_]").addClass("integer");
                        $("#data_freeze tr:eq(" + index + ") input[id^=text08_]").addClass("requirednotzero");
                        $("#data_freeze tr:eq(" + index + ") input[id^=text09_]").val(row.TAHUN_ALAT);
                        $("#data_freeze tr:eq(" + index + ") input[id^=text009_]").val(row.TAHUN_ALAT);
                        $("#data_freeze tr:eq(" + index + ") input[id^=text09_]").addClass("required");
                        $("#data_freeze tr:eq(" + index + ") input[id^=text09_]").css("text-align","right");
                        $("#data_freeze tr:eq(" + index + ") input[id^=text10_]").val(row.UOM);
                        $("#data_freeze tr:eq(" + index + ")").removeAttr("style");
                    
                        //right freeze panes
                        var tr = $("#data tr:eq(0)").clone();
                        $("#data").append(tr);
                        var index = ($("#data tr").length - 1);                    
                        $("#data tr:eq(" + index + ")").find("input, select").each(function() {
                            $(this).attr("id", $(this).attr("id") + index);
                        });
                        //row.FLAG_TEMP = 'Y';
                        //mewarnai jika row nya berasal dari temporary table
                        if (row.FLAG_TEMP) {cekTempData(index);}                    
                        
                        $("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(row.QTY_DAY, 0));
                        $("#data tr:eq(" + index + ") input[id^=text11_]").addClass("integer");
                        $("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(row.DAY_YEAR_VRA, 0));
                        $("#data tr:eq(" + index + ") input[id^=text12_]").addClass("integer");
                        $("#data tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(row.QTY_YEAR, 0));
                        $("#data tr:eq(" + index + ") input[id^=text13_]").addClass("integer");
                        $("#data tr:eq(" + index + ") input[id^=text14_]").val(accounting.formatNumber(row.TOTAL_QTY_TAHUN, 0));
                        $("#data tr:eq(" + index + ") input[id^=text14_]").addClass("integer");
                        $("#data tr:eq(" + index + ") input[id^=text25_]").val(accounting.formatNumber(row.KOMPARISON_OUT_HM_KM, 0));             //tambahan
                        $("#data tr:eq(" + index + ") input[id^=text25_]").addClass("integer");            
                        $("#data tr:eq(" + index + ") input[id^=text25_]").addClass("requirednotzero");                        
                        $("#data tr:eq(" + index + ") input[id^=text15_]").val(accounting.formatNumber(row.JUMLAH_OPERATOR, 0));
                        $("#data tr:eq(" + index + ") input[id^=text15_]").addClass("integer");
                        $("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(row.JUMLAH_HELPER, 0));
                        $("#data tr:eq(" + index + ") input[id^=text16_]").addClass("integer");
                        $("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(row.RVRA1_VALUE2, 2));
                        $("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number");
                        $("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(row.RVRA17_VALUE1, 0));
                        $("#data tr:eq(" + index + ") input[id^=text18_]").addClass("integer");
                        $("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(row.RVRA17_VALUE2, 2));
                        $("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number");
                        $("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(row.RVRA12_VALUE2, 2));
                        $("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number");
                        $("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(row.RVRA16_VALUE2, 2));
                        $("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number");
                        $("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(row.RVRA15_VALUE1, 0));
                        $("#data tr:eq(" + index + ") input[id^=text22_]").addClass("integer");
                        $("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(row.RVRA18_VALUE2, 2));
                        $("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number");
                        $("#data tr:eq(" + index + ") input[id^=text26_]").val(accounting.formatNumber(row.RP_QTY_BULAN_BUDGET, 2));        //tambahan
                        $("#data tr:eq(" + index + ") input[id^=text26_]").addClass("number");                                                //tambahan
                        $("#data tr:eq(" + index + ") input[id^=text26_]").addClass("requirednotzero");                        
                        $("#data tr:eq(" + index + ")").removeAttr("style");                    
                        $("#data tr:eq(1) input[id^=text02_]").focus();
                        
                        //TOTAL MPP
                        $("#total_mpp_operator").val(accounting.formatNumber(row.TOTAL_MPP_OPERATOR, 0));
                        $("#total_mpp_helper").val(accounting.formatNumber(row.TOTAL_MPP_HELPER, 0));
                        $("#standar_jam_kerja").val(accounting.formatNumber(row.STANDAR_JAM_KERJA_WRA, 0));
                    });
                }
            }
        }
    });
}
</script>
