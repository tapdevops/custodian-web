// ***************************************** CEK BROWSER *****************************************
if( ($.browser.msie && $.browser.version < 8) || ($.browser.mozilla && $.browser.version < 10) ) {
	var pathname = window.location.pathname;
	
	if (pathname != '/public/index/outdated-browser'){
		window.location = "/public/index/outdated-browser";
	}
}
// ***************************************** END OF CEK BROWSER *****************************************

// ***************************************** CEK INPUTAN HARUS NUMBER *****************************************
function numberOnly(objElement, event, negative, decimal) {
    if (!objElement)
        return false;

    /* ========================================
    tab = 9 ; enter = 13
    left = 37 ; right = 39
    backspace = 8 ; delete = 46
    0-9 = 48-57 or 96-105 (numlock on)
    negative: dash = 189 or 109 (numlock on)
    decimal: dot = 190 or 110 (numlock on)
    ======================================== */

    var negative = (typeof negative == 'undefined') ? false : negative;
    var decimal  = (typeof negative == 'undefined') ? false : decimal;;
	
	if (event.keyCode == 9  || event.keyCode == 13 ||
        event.keyCode == 37 || event.keyCode == 39 ||
        event.keyCode == 8  || event.keyCode == 46) {
            return true;
    } else if (event.keyCode == 86  || event.keyCode == 17 ||
        event.keyCode == 67) {
			return true;			
    } else if ((event.keyCode >= 48 && event.keyCode <= 57)  ||
               (event.keyCode >= 96 && event.keyCode <= 105)) {
            return true;
    } else if (negative == true) {
        if ((event.keyCode == 189 && objElement.value.substr(0, 1) != '-') ||
            (event.keyCode == 109 && objElement.value.substr(0, 1) != '-')) {
                return true;
        }
    } else if (decimal == true) {
        if ((event.keyCode == 190 && objElement.value.indexOf(".") == -1) ||
            (event.keyCode == 110 && objElement.value.indexOf(".") == -1)) {
                return true;
        }
    } else {
        return false;
    }
}
// ***************************************** END OF CEK INPUTAN HARUS NUMBER *****************************************

// ***************************************** ENCODE & DECODE 64 *****************************************
var keyStr = "ABCDEFGHIJKLMNOP" +
             "QRSTUVWXYZabcdef" +
             "ghijklmnopqrstuv" +
             "wxyz0123456789+/" +
             "=";

function encode64(input) {
	input = escape(input);
    var output = "";
    var chr1, chr2, chr3 = "";
    var enc1, enc2, enc3, enc4 = "";
    var i = 0;

    do {
		chr1 = input.charCodeAt(i++);
        chr2 = input.charCodeAt(i++);
        chr3 = input.charCodeAt(i++);

        enc1 = chr1 >> 2;
        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
        enc4 = chr3 & 63;

        if (isNaN(chr2)) {
           enc3 = enc4 = 64;
        } else if (isNaN(chr3)) {
           enc4 = 64;
        }

        output = output +
           keyStr.charAt(enc1) +
           keyStr.charAt(enc2) +
           keyStr.charAt(enc3) +
           keyStr.charAt(enc4);
        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";
    } while (i < input.length);

    return output;
}

function decode64(input) {
    var output = "";
    var chr1, chr2, chr3 = "";
    var enc1, enc2, enc3, enc4 = "";
    var i = 0;

    // remove all characters that are not A-Z, a-z, 0-9, +, /, or =
    var base64test = /[^A-Za-z0-9\+\/\=]/g;
    if (base64test.exec(input)) {
        alert("There were invalid base64 characters in the input text.\n" +
              "Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
              "Expect errors in decoding.");
    }
    input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

    do {
        enc1 = keyStr.indexOf(input.charAt(i++));
        enc2 = keyStr.indexOf(input.charAt(i++));
        enc3 = keyStr.indexOf(input.charAt(i++));
        enc4 = keyStr.indexOf(input.charAt(i++));

        chr1 = (enc1 << 2) | (enc2 >> 4);
        chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
        chr3 = ((enc3 & 3) << 6) | enc4;

        output = output + String.fromCharCode(chr1);

        if (enc3 != 64) {
           output = output + String.fromCharCode(chr2);
        }
        if (enc4 != 64) {
           output = output + String.fromCharCode(chr3);
        }

        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";

    } while (i < input.length);
    
	return unescape(output);
}
// ***************************************** END OF ENCODE & DECODE 64 *****************************************

function caretPosition(objElement) {
    var CaretPos = 0;

    // IE Support
    if (document.selection) {
        objElement.focus();
        var Sel = document.selection.createRange();
        Sel.moveStart ("character", -objElement.value.length);
        CaretPos = Sel.text.length;
    }

    // Firefox support
    else if (objElement.selectionStart || objElement.selectionStart == "0") {
        CaretPos = objElement.selectionStart;
    }

    return CaretPos;
}

function dateCheck(strDate) {
    if (strDate == "")
        return "TANGGAL TIDAK BOLEH KOSONG, ULANGI!";

    var strFormat = /^\d{2}\-\d{2}\-\d{4}$/;
    if (!strFormat.test(strDate))
        return "TANGGAL [" + strDate + "] BUKAN FORMAT DD-MM-YYYY, ULANGI!";

    var intD = parseInt(strDate.split("-")[0], 10);
    var intM = parseInt(strDate.split("-")[1], 10);
    var intY = parseInt(strDate.split("-")[2], 10);
    var objDate = new Date(intY, (intM - 1), intD, 0, 0, 0, 0);
    if (objDate.getDate() != intD || objDate.getMonth() != (intM - 1) || objDate.getFullYear() != intY)
        return "TANGGAL [" + strDate + "] TIDAK VALID, ULANGI!";

    var objToday = new Date();
    if (objDate > objToday)
        return "TANGGAL [" + strDate + "] LEBIH DARI HARI INI, ULANGI!";

    return "valid";
}

function numberFormat(objElement) {
    if (!objElement)
        return false;

    var strSign = '';
    var strIn   = objElement.value;
    var strOut  = strIn.replace(/,/g, '');
    if (strOut == '') {
        //objElement.value = '0';
        return false;
    }
    if (strOut == '-')
        return false;
    if (strOut.length > 1 && strOut.substr(0, 1) == '0') {
        objElement.value = strOut.substr(1, strOut.length-1);
        return false;
    }
    if (parseInt(strOut) < 0) {
        strOut = Math.abs(strOut);
        strOut = strOut.toString();
        strSign = '-';
    }

    var arrStr = Array();
    var i;
    if (i = (strOut.length % 3))
        arrStr[arrStr.length] = strOut.substr(0, i);
    for (;i < strOut.length; i+=3)
        arrStr[arrStr.length] = strOut.substr(i, 3);

    objElement.value = strSign + arrStr.join(',');
    return false;
}

//cek jika class nominal, maka hanya dapat mengetikan angka
$("[class*=integer]").live("keydown", function(event) {
	if (numberOnly(this, event, false , true)) {

	} else {
		// others
		event.preventDefault();
	}
});
$("[class*=number]").live("keydown", function(event) {
	if (numberOnly(this, event, false , true)) {

	} else {
		// others
		event.preventDefault();
	}
});
$("[class*=four_decimal]").live("keydown", function(event) {
	if (numberOnly(this, event, false , true)) {

	} else {
		// others
		event.preventDefault();
	}
});
$("[class*=integer]").live("blur", function(event) {
	var value = this.value;
	if($(this).attr('readonly') == false){
		this.value = (accounting.formatNumber(value, 0));
	}
});
$("[class*=integer]").live("focus", function(event) {
	var value = this.value;
	if(($(this).attr('readonly') == false) && (value == 0 || value == 0.00 || value == 0.0000)){
		this.value = "";
	}
});
$("[class*=number]").live("blur", function(event) {
	var value = this.value;
	if($(this).attr('readonly') == false){
		this.value = (accounting.formatNumber(value, 2));
	}
});
$("[class*=number]").live("focus", function(event) {
	var value = this.value;
	if(($(this).attr('readonly') == false) && (value == 0 || value == 0.00 || value == 0.0000)){
		this.value = "";
	}
});
$("[class*=four_decimal]").live("blur", function(event) {
	var value = this.value;
	if($(this).attr('readonly') == false){
		this.value = (accounting.formatNumber(value, 4));
	}
});
$("[class*=four_decimal]").live("focus", function(event) {
	var value = this.value;
	if(($(this).attr('readonly') == false) && (value == 0 || value == 0.00 || value == 0.0000)){
		this.value = "";
	}
});


//perubahan text jika rubah data
$("[id^=text]").live("change", function(event) {
	var row = $(this).attr("id").split("_")[1];
	$("#data tr:eq(" + row + ") select[id^=text], #data tr:eq(" + row + ") input[id^=text]").addClass("edited");
    $("#data tr:eq(" + row + ") input[id^=tChange_]").val("Y");
	
	//untuk norma wra
	$("#data1 tr:eq(" + (row - 1000)+ ") select[id^=text], #data1 tr:eq(" + (row - 1000) + ") input[id^=text]").addClass("edited");
	$("#data1 tr:eq(" + (row - 1000)+ ") input[id^=tChange_]").val("Y");

	//left freezepanes
	$("#data_freeze tr:eq(" + row + ") select[id^=text], #data_freeze tr:eq(" + row + ") input[id^=text]").addClass("edited");
	$("#data_freeze tr:eq(" + row + ") input[id^=tChange_]").val("Y");
	
});

//perubahan text jika pilih LOV
function addClassEdited(row){	
	$("#data tr:eq(" + row + ") select[id^=text], #data tr:eq(" + row + ") input[id^=text]").addClass("edited");
    $("#data tr:eq(" + row + ") input[id^=tChange_]").val("Y");
	
	//left freezepanes
	$("#data_freeze tr:eq(" + row + ") select[id^=text], #data_freeze tr:eq(" + row + ") input[id^=text]").addClass("edited");
	$("#data_freeze tr:eq(" + row + ") input[id^=tChange_]").val("Y");
}

//jika ada rowid temp, maka text warna abu2
function cekTempData(row){	
	$("#data_freeze tr:eq(" + row + ") select[id^=text], #data_freeze tr:eq(" + row + ") input[id^=text]").addClass("edited");
	$("#data_freeze tr:eq(" + row + ") input[id^=tChange_]").val("Y");
	
	$("#data tr:eq(" + row + ") select[id^=text], #data tr:eq(" + row + ") input[id^=text]").addClass("edited");	
	$("#data tr:eq(" + row + ") input[id^=tChange_]").val("Y");
	
}

// validasi input
$("[class*=required]").live("blur", function(event) {
	var value = this.value.replace(" ", "");
	//if (value == '' || value == 0 || value == 0.00) {
	if (value == '') {
		$(this).addClass("error");
	}else{
		$(this).removeClass("error");
	}
});


// validasi input not zero 
$("[class*=requirednotzero]").live("blur", function(event) {
	var value = this.value.replace(" ", "");
	//
	//if (value == '') {
	if (value == '' || value == 0 || value == 0.00) {
		$(this).addClass("error");
	}else{
		$(this).removeClass("error");
	}
});

//validasi text field yang harus diisi
function validateInput(){	
	var result = true;
	
	$("[class*=required]").each(function() {
		var value = this.value.replace(" ", "");
		//if (value == '' || value == 0 || value == 0.00) {
		if (value == '') {
			$(this).addClass("error");
			$(this).focus();
			result = false;
		}else{
			$(this).removeClass("error");
		}
	});
	
	//$("[class*=requirednotzero]").live("blur", function(event) { remark by doni
	$("[class*=requirednotzero]").each(function() {
		var mystring = this.value;
		var value = mystring.split(',').join('');
		
		if (value == '' || value == 0 || value == 0.00) {
			$(this).addClass("error");
			$(this).focus();
			result = false;
		}else{
			$(this).removeClass("error");
		}
	});
	
	return result;
}

//HAPUS ISI TEXT FIELD - saat delete row
function clearTextField(row){
	//alert('clear');
	$("#data tr:eq(" + row + ") input[id^=text]").removeClass("required");
	$("#data tr:eq(" + row + ") input[id^=text]").removeClass("requirednotzero");
	$("#data tr:eq(" + row + ")").find("input").each(function() {
		$(this).attr("value", "");
		$(this).removeAttr("style");
		$(this).removeClass();
	});
	$("#data tr:eq(" + row + ")").css("display", "none");
	
	$("#data_freeze tr:eq(" + row + ") input[id^=text]").removeClass("required");
	$("#data_freeze tr:eq(" + row + ") input[id^=text]").removeClass("requirednotzero");
	$("#data_freeze tr:eq(" + row + ")").find("input").each(function() {
		$(this).attr("value", "");
		$(this).removeAttr("style");
		$(this).removeClass();
	});
	$("#data_freeze tr:eq(" + row + ")").css("display", "none");
}

//tiap klik yang mengandung "PICK", lgsg jalankan clear data
$("[id^=pick]").live("click", function(event) {
	clearDetail();
});

//clear data detail
function clearDetail() {
    $("#page_counter").html("HALAMAN: ? / ?");
    $("#btn_first").attr("disabled", true);
    $("#btn_prev").attr("disabled", true);
    $("#btn_next").attr("disabled", true);
    $("#btn_last").attr("disabled", true);
    $("#data").find("tr:gt(0)").remove();
    $("#data_freeze").find("tr:gt(0)").remove();
    $("#record_counter").html("DATA: ? / ?");
	$("#tfoot").hide();
	$("#info_vra").hide();
}

//tiap klik "ADD ROW" jumlah data bertambah
$("[id^=btn_add]").live("click", function(event) {
	count ++;
});

$("input[id^=btn00_]").live("click", function(event) {
	count ++;
});

//tiap klik "DELETE ROW" jumlah data berkurang
$("input[id^=btn01_]").live("click", function(event) {
	count --;
});

//generate trx code
function genTransactionCode(budgetperiod, bacode, rktcode) {
    //20142121CR20130626AB123
	//FORMAT : = TAHUN BUDGET + BA_CODE + RKT_CODE + DATE INSERT + 5 DIGIT RANDOM CODE
	
	var rand_text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	var d1 = new Date();

    for( var i=0; i < 5; i++ ) {
        rand_text += possible.charAt(Math.floor(Math.random() * possible.length));
	}
	
	var trxCode = String(budgetperiod) + String(bacode) + String(rktcode) + String(d1.getFullYear()) + String(d1.getMonth() + 1) + String(d1.getDate()) + String(rand_text);
	
	return trxCode;
}