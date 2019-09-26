// JavaScript Document
$(document).ready(function(){
	var kompeni = $('#prshid').val();
	var tahun = $('#thunid').val();
	var strUrl = "../ajax/tahunduba?site_code="+kompeni+"&tahun=";
	$.ajax({
	  url: strURL+tahun,
	  cache: false,
	  success: function(html){
			$("#perdid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
	var badate = $('#perdid').val();
	var strUrl = "../ajax/intervalduba?site_code="+kompeni+"&badate=";
	$.ajax({
	  url: strURL+badate,
	  cache: false,
	  success: function(html){
			$("#dubaint").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
	
	var acc = $('#accid').val();
	var strUrl = "../ajax/afdduba?site_code="+kompeni+"&acc=";
	$.ajax({
	  url: strURL+acc,
	  cache: false,
	  success: function(html){
			$("#bagnid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
			//alert(strURL+tahun);
	  }
	});
	var afd = $('#bagnid').val();
	var acc = '&acc='+$('#accid').val();
	var strUrl = "../ajax/mandorduba?site_code="+kompeni+"&all=true&afd=";
	$.ajax({
	  url: strURL+afd+acc,
	  cache: false,
	  success: function(html){
			$("#mndrid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});

});