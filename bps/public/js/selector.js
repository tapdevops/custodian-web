// JavaScript Document

function getXMLHTTP(){ // fuction to get xmlhttp object
	//Create a boolean variable to check for a valid IE instance.
	var xmlhttp = false;
	
	//Check if we are using IE.
	try {
		//If the javascript version is greater than 5.
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		//If not, then use the older active x object.
		try {
			//If we are using IE.
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			//Else we must be using a non-IE browser.
			xmlhttp = false;
		}
	}
	
	//If we are using a non-IE browser, create a JavaScript instance of the object.
	if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
		xmlhttp = new XMLHttpRequest();
	}

	return xmlhttp;
}

function refreshNoBA1(strURL){    
	/* 
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var tahun = document.getElementById('thunid').value;
	var bulan = document.getElementById('perdid').value;
	if (req){
		strURL = strURL+"tahun="+tahun+"&bulan="+bulan;
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('nobaid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	}
	*/
	var tahun = $('#thunid').val();
	var bulan = $('#perdid').val();
	$.ajax({
	  url: strURL+"tahun="+tahun+"&bulan="+bulan,
	  cache: false,
	  success: function(html){
			$("#nobaid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refreshNoBA2(strURL){    
	/*
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var tahun = document.getElementById('thunid').value;
	var bulan = document.getElementById('perdid').value;
	if (req){
		strURL = strURL+"tahun="+tahun+"&bulan="+bulan;
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('noba2id').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	}
	*/
	var tahun = $('#thunid').val();
	var bulan = $('#perdid').val();
	$.ajax({
	  url: strURL+"tahun="+tahun+"&bulan="+bulan,
	  cache: false,
	  success: function(html){
			$("#noba2id").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refreshNoBA3(strURL){    
	/* 
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var tahun = document.getElementById('thunid').value;
	var bulan = document.getElementById('perdid').value;
	if (req){
		strURL = strURL+"tahun="+tahun+"&bulan="+bulan;
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('nobaid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	}
	*/
	var tahun = $('#thunid').val();
	var bulan = $('#perdid').val();
	var afdeling = $('#bagnid').val();
	$.ajax({
	  url: strURL+"tahun="+tahun+"&bulan="+bulan+"&afdeling="+afdeling,
	  cache: false,
	  success: function(html){
			$("#nobaid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refreshNoBA4(strURL){    
	/* 
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var tahun = document.getElementById('thunid').value;
	var bulan = document.getElementById('perdid').value;
	if (req){
		strURL = strURL+"tahun="+tahun+"&bulan="+bulan;
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('nobaid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	}
	*/
	var tahun = $('#thunid').val();
	var bulan = $('#perdid').val();
	var afdeling = $('#bagnid').val();
	$.ajax({
	  url: strURL+"tahun="+tahun+"&bulan="+bulan+"&afdeling="+afdeling,
	  cache: false,
	  success: function(html){
			$("#noba2id").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refreshSiteCode(strURL){    
	/* 
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var tahun = document.getElementById('thunid').value;
	var bulan = document.getElementById('perdid').value;
	if (req){
		strURL = strURL+"tahun="+tahun+"&bulan="+bulan;
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('nobaid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	}
	*/
	var sitecode = $('#prshid').val();
/*	var bulan = $('#perdid').val();
	var afdeling = $('#bagnid').val();*/
	$.ajax({
	  url: strURL+"site_code="+sitecode,
	  cache: false,
	  success: function(html){
			$("#thunid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function maintainSignature(strURL){         
	/*
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var numSign = document.getElementById('signid').value;
	if (req){
		strURL = strURL+"numsign="+numSign;
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						//document.getElementById('signature').innerHTML=req.responseText;
						$("#signature").remove();
						$("#signid").after(req.responseText);
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	}
	*/
	var numSign = $('#signid').val();
	$.ajax({
	  url: strURL+"numsign="+numSign,
	  cache: false,
	  success: function(html){
			$("#signature").remove();
			$("#signid").after(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refrKemandoran(strURL){         
	/*
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var afdeling = document.getElementById('bagnid').value;
	if (req){
		strURL = strURL+afdeling+"&all=0";
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('mndrid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	}
	*/
	var afdeling = $('#bagnid').val();
	$.ajax({
	  url: strURL+afdeling+"&all=0",
	  cache: false,
	  success: function(html){
			$("#mndrid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refrKemandoran2(strURL){   
	/*
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var afdeling = document.getElementById('bagnid').value;
	if (req){
		strURL = strURL+afdeling+"&all=1";
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('mndrid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	}
	*/
	var afdeling = $('#bagnid').val();
	$.ajax({
	  url: strURL+afdeling+"&all=1",
	  cache: false,
	  success: function(html){
			$("#mndrid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refrKemandoran3(strURL){   
	/*
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var section = document.getElementById('sksiid').value;
	if (req){
		strURL = strURL+section+"&all=0";
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('mndrid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	}
	*/
	var section = $('#sksiid').val();
	$.ajax({
	  url: strURL+section+"&all=0",
	  cache: false,
	  success: function(html){
			$("#mndrid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refrKemandoran4(strURL){     
	/*
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var section = document.getElementById('sksiid').value;
	if (req){
		strURL = strURL+section+"&all=1";
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('mndrid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	}
	*/
	var section = $('#sksiid').val();
	$.ajax({
	  url: strURL+section+"&all=1",
	  cache: false,
	  success: function(html){
			$("#mndrid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refrKemandoran5(strURL){     
	/*
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var section = document.getElementById('sksiid').value;
	if (req){
		strURL = strURL+section+"&all=1";
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('mndrid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	}
	*/
	var afdeling = $('#bagnid').val();
	$.ajax({
	  url: strURL+afdeling+"&all=0&location=PAN",
	  cache: false,
	  success: function(html){
			$("#mndrid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refrKemandoran6(strURL){     
	/*
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var section = document.getElementById('sksiid').value;
	if (req){
		strURL = strURL+section+"&all=1";
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('mndrid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	}
	*/
	var afdeling = $('#bagnid').val();
	$.ajax({
	  url: strURL+afdeling+"&all=1&location=PAN",
	  cache: false,
	  success: function(html){
			$("#mndrid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refrKemandoran7(strURL){     
	/*
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var section = document.getElementById('sksiid').value;
	if (req){
		strURL = strURL+section+"&all=1";
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('mndrid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	}
	*/
	var afdeling = $('#bagnid').val();
	$.ajax({
	  url: strURL+afdeling+"&all=0&location1=PAN&location2=TAN",
	  cache: false,
	  success: function(html){
			$("#mndrid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refrKemandoran8(strURL){     
	/*
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var section = document.getElementById('sksiid').value;
	if (req){
		strURL = strURL+section+"&all=1";
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('mndrid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	}
	*/
	var afdeling = $('#bagnid').val();
	$.ajax({
	  url: strURL+afdeling+"&all=1&location1=PAN&location2=TAN",
	  cache: false,
	  success: function(html){
			$("#mndrid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refrKemandoran9(strURL){     
	/*
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var section = document.getElementById('sksiid').value;
	if (req){
		strURL = strURL+section+"&all=1";
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('mndrid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	}
	*/
	var bagian = $('#bagnid').val();
	var section = $('#sksiid').val();
	$.ajax({
	  url: strURL+section+"&afd="+bagian+"&all=1",
	  cache: false,
	  success: function(html){
			$("#mndrid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}
function refrSection(strURL){    
	/*
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var afdeling = document.getElementById('bagnid').value;
	if (req){
		strURL = strURL+afdeling+"&all=0";
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('sksiid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	}
	*/
	var afdeling = $('#bagnid').val();
	$.ajax({
	  url: strURL+afdeling+"&all=0",
	  cache: false,
	  success: function(html){
			$("#sksiid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refrSection2(strURL){    
	/*
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var departemen = document.getElementById('bagnid').value;
	if (req){
		strURL = strURL+departemen+"&all=1";
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('sksiid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	}
	*/
	var afdeling = $('#bagnid').val();
	$.ajax({
	  url: strURL+afdeling+"&all=1",
	  cache: false,
	  success: function(html){
			$("#sksiid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refrSubdept(strURL){    
	/*     
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var departemen = document.getElementById('deptid').value;
	if (req){
		strURL = strURL+departemen+"&all=0";
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('bagnid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	}
	*/
	var departemen = $('#deptid').val();
	$.ajax({
	  url: strURL+departemen+"&all=0",
	  cache: false,
	  success: function(html){
			$("#bagnid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refrSubdept2(strURL){      
	/*
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var departemen = document.getElementById('deptid').value;
	if (req){
		strURL = strURL+departemen+"&all=1";
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('bagnid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	} 
	*/
	var departemen = $('#deptid').val();
	$.ajax({
	  url: strURL+departemen+"&all=1",
	  cache: false,
	  success: function(html){
			$("#bagnid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refrSubdept3(strURL){      
	/*
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var departemen = document.getElementById('deptid').value;
	if (req){
		strURL = strURL+departemen+"&all=1";
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('bagnid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	} 
	*/
	$.ajax({
	  url: strURL+"?location=PAN&all=0",
	  cache: false,
	  success: function(html){
			$("#bagnid").html(html);
			$("#bagnid").change();
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refrSubdept4(strURL){      
	/*
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var departemen = document.getElementById('deptid').value;
	if (req){
		strURL = strURL+departemen+"&all=1";
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('bagnid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	} 
	*/
	$.ajax({
	  url: strURL+"?location=PAN&all=1",
	  cache: false,
	  success: function(html){
			$("#bagnid").html(html);
			$("#bagnid").change();
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refrSubdept5(strURL){      
	/*
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var departemen = document.getElementById('deptid').value;
	if (req){
		strURL = strURL+departemen+"&all=1";
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('bagnid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	} 
	*/
	$.ajax({
	  url: strURL+"?location1=PAN&location2=TAN&all=0",
	  cache: false,
	  success: function(html){
			$("#bagnid").html(html);
			$("#bagnid").change();
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function refrSubdept6(strURL){      
	/*
	var req = getXMLHTTP(); // fuction to get xmlhttp object
	var departemen = document.getElementById('deptid').value;
	if (req){
		strURL = strURL+departemen+"&all=1";
		req.onreadystatechange = function(){
				if (req.readyState == 4) { //data is retrieved from server
					if (req.status == 200) { // which reprents ok status                    
						document.getElementById('bagnid').innerHTML=req.responseText;
					} else { 
						alert("There was a problem while using XMLHTTP:\n");
					}
				}            
		}        
    	req.open("GET", strURL, true); //open url using get method
    	req.send(null);
	} 
	*/
	$.ajax({
	  url: strURL+"?location1=PAN&location2=TAN&all=1",
	  cache: false,
	  success: function(html){
			$("#bagnid").html(html);
			$("#bagnid").change();
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
	  }
	});
}

function tahunlamp(strURL)
{
	var tahun = $('#thunid').val();
	$.ajax(
	{
	  url: strURL+tahun,
	  cache: false,
	  success: function(html){
			$("#badateid").html(html);
			intervallamp('../ajax/intervallamp?badate=');
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
			//alert(strURL+tahun);
	  }
	}
	);
}
function intervallamp(strURL)
{
	var badate = $('#badateid').val();
	//alert(strURL+badate);
	$.ajax(
	{
	  url: strURL+badate,
	  cache: false,
	  success: function(html){
			$("#lampint").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
			//alert(strURL+tahun);
	  }
	}
	);
}

function tahunbapk(strURL)
{
	var tahun = $('#thunid').val();
	//alert(strURL+tahun);
	$.ajax(
	{
	  url: strURL+tahun,
	  cache: false,
	  success: function(html){
			$("#perdid").html(html);
			intervabapk('../ajax/intervalbapk?badate=');
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
			//alert(strURL+tahun);
	  }
	}
	);
}
function intervabapk(strURL)
{
	var badate = $('#perdid').val();
	//alert('PERIODE CHANGE');
	//alert(strURL+badate);
	$.ajax(
	{
	  url: strURL+badate,
	  cache: false,
	  success: function(html){
			$("#bapkint").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
			//alert(strURL+tahun);
	  }
	}
	);
}

function tahunduba(strURL)
{
	var tahun = $('#thunid').val();
	//alert(strURL+tahun);
	$.ajax(
	{
	  url: strURL+tahun,
	  cache: false,
	  success: function(html){
			$("#perdid").html(html);
			intervalduba('../ajax/intervalduba?badate=');
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
			//alert(strURL+tahun);
	  }
	}
	);
}
function intervalduba(strURL)
{
	var badate = $('#perdid').val();
	//alert('PERIODE CHANGE');
	//alert(strURL+badate);
	$.ajax(
	{
	  url: strURL+badate,
	  cache: false,
	  success: function(html){
			$("#dubaint").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
			//alert(strURL+tahun);
	  }
	}
	);
}
function afdduba(strURL)
{
	var acc = $('#accid').val();
	//alert('PERIODE CHANGE');
	//alert(strURL+acc);
	$.ajax(
	{
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
	var strUrl = "../ajax/mandorduba?site_code="+kompeni+"&afd=";
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
}

function kemandoranduba(strURL)
{
//alert($('#bagnid').val())
var afd = $('#bagnid').val();
var acc = '&acc='+$('#accid').val();
	//alert('PERIODE CHANGE');
	//alert(strURL+acc);
	$.ajax(
	{
	  url: strURL+afd+acc,
	  cache: false,
	  success: function(html){
			$("#mndrid").html(html);
	  },
	  error: function(html){
			alert("There was a problem while using XMLHTTP:\n");
			//alert(strURL+tahun);
	  }
	}
	);
}
window.onload=function(){
	document.body.style.cursor = 'auto';
}