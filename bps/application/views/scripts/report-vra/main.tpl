<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Report VRA
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	02/07/2013
Update Terakhir		:	02/07/2013
Revisi				:	
=========================================================================================================================
*/
$this->headScript()->appendFile('js/accounting.js');
$periodbudget = $this->period;
$periodaktual = $periodbudget - 1;
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
						<input type="text" name="src_ba" id="src_ba" value="" style="width:200px;"  class='filter'/>
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
            <input type="hidden" name="page_rows" id="page_rows" value="20" />
		</fieldset>
	</div>
	<br />
	<div>
		<fieldset>
			<legend>VRA</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">
						<input type="button" name="btn_export_csv" id="btn_export_csv" value="EXPORT DATA" class="button" />
					</td>
					<td width="50%" align="right">
						&nbsp;
					</td>
				</tr>
			</table>
			<div id='scrollarea'>
			<table width='100%' border="0" cellpadding="1" cellspacing="1" class='data_header' id='data_header'>
			<thead>
				<tr>
					<th rowspan='2'>PERIODE<BR>BUDGET</th>
					<th rowspan='2'>BUSINESS<BR>AREA</th>
					<th rowspan='2'>SUB KATEGORI<BR>VRA</th>
					<th rowspan='2'>VRA CODE</th>
					<th rowspan='2'>VRA TYPE</th>
					<th rowspan='2'>DESKRIPSI<BR>VRA TYPE</th>
					<th rowspan='2'>JUMLAH ALAT</th>
					<th rowspan='2'>TAHUN ALAT</th>
					<th rowspan='2'>UOM</th>
					<th colspan='4'>STANDAR KERJA</th>
					<th colspan='7'>GAJI & TUNJANGAN OPERATOR</th>
					<th colspan='7'>GAJI & TUNJANGAN HELPER</th>
					<th colspan='3'>PAJAK & PERIJINAN</th>
					<th colspan='3'>RENTAL</th>
					<th colspan='3'>BAHAN BAKAR</th>
					<th colspan='3'>OLI MESIN</th>
					<th colspan='3'>OLI TRANSMISI</th>
					<th colspan='3'>MINYAK HYDROLIC</th>
					<th colspan='3'>GREASE</th>
					<th colspan='3'>FILTER OLI</th>
					<th colspan='3'>FILTER HYDROLIC</th>
					<th colspan='3'>FILTER SOLAR</th>
					<th colspan='3'>FILTER SOLAR MOISTURE SEPARATOR</th>
					<th colspan='3'>FILTER UDARA</th>
					<th colspan='3'>GANTI SPAREPART</th>
					<th colspan='3'>GANTI BAN LUAR</th>
					<th colspan='3'>GANTI BAN DALAM</th>
					<th colspan='3'>SERVIS WORKSHOP</th>
					<th colspan='3'>OVERHAUL</th>
					<th colspan='3'>SERVIS BENGKEL LUAR</th>
					<th rowspan='2'>TOTAL BIAYA</th>
					<th rowspan='2'>TOTAL RP/QTY</th>
					<th rowspan='2'>RP/QTY VRA TYPE</th>
				</tr>
				<tr>
					<th>QTY/HARI</th>
					<th>HARI<BR>VRA/TAHUN</th>
					<th>QTY/TAHUN</th>
					<th>TOTAL QTY/TAHUN</th>
					<th>TK</th>
					<th>GP/BULAN</th>
					<th>TOTAL GP/BULAN</th>
					<th>TUNJANGAN/BULAN</th>
					<th>TOTAL TUNJANGAN/BULAN</th>
					<th>TOTAL GAJI & TUNJANGAN</th>
					<th>RP/QTY</th>
					<th>TK</th>
					<th>GP/BULAN</th>
					<th>TOTAL GP/BULAN</th>
					<th>TUNJANGAN/BULAN</th>
					<th>TOTAL TUNJANGAN/BULAN</th>
					<th>TOTAL GAJI & TUNJANGAN</th>
					<th>RP/QTY</th>
					<th>QTY/SAT</th>
					<th>HARGA</th>
					<th>RP/QTY</th>
					<th>QTY/SAT</th>
					<th>HARGA</th>
					<th>RP/QTY</th>
					<th>QTY/SAT</th>
					<th>HARGA</th>
					<th>RP/QTY</th>
					<th>QTY/SAT</th>
					<th>HARGA</th>
					<th>RP/QTY</th>
					<th>QTY/SAT</th>
					<th>HARGA</th>
					<th>RP/QTY</th>
					<th>QTY/SAT</th>
					<th>HARGA</th>
					<th>RP/QTY</th>
					<th>QTY/SAT</th>
					<th>HARGA</th>
					<th>RP/QTY</th>
					<th>QTY/SAT</th>
					<th>HARGA</th>
					<th>RP/QTY</th>
					<th>QTY/SAT</th>
					<th>HARGA</th>
					<th>RP/QTY</th>
					<th>QTY/SAT</th>
					<th>HARGA</th>
					<th>RP/QTY</th>
					<th>QTY/SAT</th>
					<th>HARGA</th>
					<th>RP/QTY</th>
					<th>QTY/SAT</th>
					<th>HARGA</th>
					<th>RP/QTY</th>
					<th>QTY/SAT</th>
					<th>HARGA</th>
					<th>RP/QTY</th>
					<th>QTY/SAT</th>
					<th>HARGA</th>
					<th>RP/QTY</th>
					<th>QTY/SAT</th>
					<th>HARGA</th>
					<th>RP/QTY</th>
					<th>QTY/SAT</th>
					<th>HARGA</th>
					<th>RP/QTY</th>
					<th>QTY/SAT</th>
					<th>HARGA</th>
					<th>RP/QTY</th>
					<th>QTY/SAT</th>
					<th>HARGA</th>
					<th>RP/QTY</th>
				</tr>
				<tr class='column_number'>
					<th>1</th>
					<th>2</th>
					<th>3</th>
					<th>4</th>
					<th>5</th>
					<th>6</th>
					<th>7</th>
					<th>8</th>
					<th>9</th>
					<th>10</th>
					<th>11</th>
					<th>12</th>
					<th>13</th>
					<th>14</th>
					<th>15</th>
					<th>16</th>
					<th>17</th>
					<th>18</th>
					<th>19</th>
					<th>20</th>
					<th>21</th>
					<th>22</th>
					<th>23</th>
					<th>24</th>
					<th>25</th>
					<th>26</th>
					<th>27</th>
					<th>28</th>
					<th>29</th>
					<th>30</th>
					<th>31</th>
					<th>32</th>
					<th>33</th>
					<th>34</th>
					<th>35</th>
					<th>36</th>
					<th>37</th>
					<th>38</th>
					<th>39</th>
					<th>40</th>
					<th>41</th>
					<th>42</th>
					<th>43</th>
					<th>44</th>
					<th>45</th>
					<th>46</th>
					<th>47</th>
					<th>48</th>
					<th>49</th>
					<th>50</th>
					<th>51</th>
					<th>52</th>
					<th>53</th>
					<th>54</th>
					<th>55</th>
					<th>56</th>
					<th>57</th>
					<th>58</th>
					<th>59</th>
					<th>60</th>
					<th>61</th>
					<th>62</th>
					<th>63</th>
					<th>64</th>
					<th>65</th>
					<th>66</th>
					<th>67</th>
					<th>68</th>
					<th>69</th>
					<th>70</th>
					<th>71</th>
					<th>72</th>
					<th>73</th>
					<th>74</th>
					<th>75</th>
					<th>76</th>
					<th>77</th>
					<th>78</th>
					<th>79</th>
					<th>80</th>
					<th>81</th>
					<th>82</th>
					<th>83</th>
					<th>84</th>
				</tr>
			</thead>
			<tbody name='data' id='data'>
				<tr style="display:none" class='rowdata'>
					<td>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" style='width:50px' value='2'/>
					</td>
					<td><input type="text" name="text03[]" id="text03_" readonly="readonly" style='width:50px' value='3'/></td>
					<td><input type="text" name="text04[]" id="text04_" readonly="readonly" style='width:150px' value='4'/></td>
					<td><input type="text" name="text05[]" id="text05_" readonly="readonly" style='width:100px' value='5'/></td>
					<td><input type="text" name="text06[]" id="text06_" readonly="readonly" style='width:250px' value='6'/></td>
					<td><input type="text" name="text07[]" id="text07_" readonly="readonly" style='width:250px' value='7'/></td>
					<td><input type="text" name="text08[]" id="text08_" readonly="readonly" style='width:50px' value='8'/></td>
					<td><input type="text" name="text09[]" id="text09_" readonly="readonly" style='width:50px' value='9'/></td>
					<td><input type="text" name="text10[]" id="text10_" readonly="readonly" style='width:50px' value='10'/></td>
					<td><input type="text" name="text11[]" id="text11_" readonly="readonly" style='width:120px' value='11'/></td>
					<td><input type="text" name="text12[]" id="text12_" readonly="readonly" style='width:120px' value='12'/></td>
					<td><input type="text" name="text13[]" id="text13_" readonly="readonly" style='width:120px' value='13'/></td>
					<td><input type="text" name="text14[]" id="text14_" readonly="readonly" style='width:120px' value='14'/></td>
					<td><input type="text" name="text15[]" id="text15_" readonly="readonly" style='width:50px' value='15'/></td>
					<td><input type="text" name="text16[]" id="text16_" readonly="readonly" style='width:120px' value='16'/></td>
					<td><input type="text" name="text17[]" id="text17_" readonly="readonly" style='width:120px' value='17'/></td>
					<td><input type="text" name="text18[]" id="text18_" readonly="readonly" style='width:120px' value='18'/></td>
					<td><input type="text" name="text19[]" id="text19_" readonly="readonly" style='width:120px' value='19'/></td>
					<td><input type="text" name="text20[]" id="text20_" readonly="readonly" style='width:120px' value='20'/></td>
					<td><input type="text" name="text21[]" id="text21_" readonly="readonly" style='width:120px' value='21'/></td>
					<td><input type="text" name="text22[]" id="text22_" readonly="readonly" style='width:50px' value='22'/></td>
					<td><input type="text" name="text23[]" id="text23_" readonly="readonly" style='width:120px' value='23'/></td>
					<td><input type="text" name="text24[]" id="text24_" readonly="readonly" style='width:120px' value='24'/></td>
					<td><input type="text" name="text25[]" id="text25_" readonly="readonly" style='width:120px' value='25'/></td>
					<td><input type="text" name="text26[]" id="text26_" readonly="readonly" style='width:120px' value='26'/></td>
					<td><input type="text" name="text27[]" id="text27_" readonly="readonly" style='width:120px' value='27'/></td>
					<td><input type="text" name="text28[]" id="text28_" readonly="readonly" style='width:120px' value='28'/></td>
					<td><input type="text" name="text29[]" id="text29_" readonly="readonly" style='width:120px' value='29'/></td>
					<td><input type="text" name="text30[]" id="text30_" readonly="readonly" style='width:120px' value='30'/></td>
					<td><input type="text" name="text31[]" id="text31_" readonly="readonly" style='width:120px' value='31'/></td>
					
					<td><input type="text" name="text77[]" id="text77_" readonly="readonly" style='width:120px' value='77'/></td>
					<td><input type="text" name="text78[]" id="text78_" readonly="readonly" style='width:120px' value='78'/></td>
					<td><input type="text" name="text79[]" id="text79_" readonly="readonly" style='width:120px' value='79'/></td>
					
					<td><input type="text" name="text32[]" id="text32_" readonly="readonly" style='width:120px' value='32'/></td>
					<td><input type="text" name="text33[]" id="text33_" readonly="readonly" style='width:120px' value='33'/></td>
					<td><input type="text" name="text34[]" id="text34_" readonly="readonly" style='width:120px' value='34'/></td>
					<td><input type="text" name="text35[]" id="text35_" readonly="readonly" style='width:120px' value='35'/></td>
					<td><input type="text" name="text36[]" id="text36_" readonly="readonly" style='width:120px' value='36'/></td>
					<td><input type="text" name="text37[]" id="text37_" readonly="readonly" style='width:120px' value='37'/></td>
					<td><input type="text" name="text38[]" id="text38_" readonly="readonly" style='width:120px' value='38'/></td>
					<td><input type="text" name="text39[]" id="text39_" readonly="readonly" style='width:120px' value='39'/></td>
					<td><input type="text" name="text40[]" id="text40_" readonly="readonly" style='width:120px' value='40'/></td>
					<td><input type="text" name="text41[]" id="text41_" readonly="readonly" style='width:120px' value='41'/></td>
					<td><input type="text" name="text42[]" id="text42_" readonly="readonly" style='width:120px' value='42'/></td>
					<td><input type="text" name="text43[]" id="text43_" readonly="readonly" style='width:120px' value='43'/></td>
					<td><input type="text" name="text44[]" id="text44_" readonly="readonly" style='width:120px' value='44'/></td>
					<td><input type="text" name="text45[]" id="text45_" readonly="readonly" style='width:120px' value='45'/></td>
					<td><input type="text" name="text46[]" id="text46_" readonly="readonly" style='width:120px' value='46'/></td>
					<td><input type="text" name="text47[]" id="text47_" readonly="readonly" style='width:120px' value='47'/></td>
					<td><input type="text" name="text48[]" id="text48_" readonly="readonly" style='width:120px' value='48'/></td>
					<td><input type="text" name="text49[]" id="text49_" readonly="readonly" style='width:120px' value='49'/></td>
					<td><input type="text" name="text50[]" id="text50_" readonly="readonly" style='width:120px' value='50'/></td>
					<td><input type="text" name="text51[]" id="text51_" readonly="readonly" style='width:120px' value='51'/></td>
					<td><input type="text" name="text52[]" id="text52_" readonly="readonly" style='width:120px' value='52'/></td>
					<td><input type="text" name="text53[]" id="text53_" readonly="readonly" style='width:120px' value='53'/></td>
					<td><input type="text" name="text54[]" id="text54_" readonly="readonly" style='width:120px' value='54'/></td>
					<td><input type="text" name="text55[]" id="text55_" readonly="readonly" style='width:120px' value='55'/></td>
					<td><input type="text" name="text56[]" id="text56_" readonly="readonly" style='width:120px' value='56'/></td>
					<td><input type="text" name="text57[]" id="text57_" readonly="readonly" style='width:120px' value='57'/></td>
					<td><input type="text" name="text58[]" id="text58_" readonly="readonly" style='width:120px' value='58'/></td>
					<td><input type="text" name="text59[]" id="text59_" readonly="readonly" style='width:120px' value='59'/></td>
					<td><input type="text" name="text60[]" id="text60_" readonly="readonly" style='width:120px' value='60'/></td>
					<td><input type="text" name="text61[]" id="text61_" readonly="readonly" style='width:120px' value='61'/></td>
					<td><input type="text" name="text62[]" id="text62_" readonly="readonly" style='width:120px' value='62'/></td>
					<td><input type="text" name="text63[]" id="text63_" readonly="readonly" style='width:120px' value='63'/></td>
					<td><input type="text" name="text64[]" id="text64_" readonly="readonly" style='width:120px' value='64'/></td>
					<td><input type="text" name="text65[]" id="text65_" readonly="readonly" style='width:120px' value='65'/></td>
					<td><input type="text" name="text66[]" id="text66_" readonly="readonly" style='width:120px' value='66'/></td>
					<td><input type="text" name="text67[]" id="text67_" readonly="readonly" style='width:120px' value='67'/></td>
					<td><input type="text" name="text68[]" id="text68_" readonly="readonly" style='width:120px' value='68'/></td>
					<td><input type="text" name="text69[]" id="text69_" readonly="readonly" style='width:120px' value='69'/></td>
					<td><input type="text" name="text70[]" id="text70_" readonly="readonly" style='width:120px' value='70'/></td>
					<td><input type="text" name="text71[]" id="text71_" readonly="readonly" style='width:120px' value='71'/></td>
					<td><input type="text" name="text72[]" id="text72_" readonly="readonly" style='width:120px' value='72'/></td>
					<td><input type="text" name="text73[]" id="text73_" readonly="readonly" style='width:120px' value='73'/></td>
					<td><input type="text" name="text74[]" id="text74_" readonly="readonly" style='width:120px' value='74'/></td>
					<td><input type="text" name="text75[]" id="text75_" readonly="readonly" style='width:120px' value='75'/></td>
					<td><input type="text" name="text76[]" id="text76_" readonly="readonly" style='width:120px' value='76'/></td>
					<td><input type="text" name="text80[]" id="text80_" readonly="readonly" style='width:120px' value='80'/></td>
					<td><input type="text" name="text81[]" id="text81_" readonly="readonly" style='width:120px' value='81'/></td>
					<td><input type="text" name="text82[]" id="text82_" readonly="readonly" style='width:120px' value='82'/></td>
					<td><input type="text" name="text83[]" id="text83_" readonly="readonly" style='width:120px' value='83'/></td>
					<td><input type="text" name="text84[]" id="text84_" readonly="readonly" style='width:120px' value='84'/></td>
					<td><input type="text" name="text85[]" id="text85_" readonly="readonly" style='width:120px' value='85'/></td>
				</tr>			
			</tbody>
			</table>
			</div>
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
	//BUTTON ACTION	
	$("#btn_find").click(function() {
		//clear data
		clearDetail();
		
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find = $("#key_find").val();				//KODE BA
		var region = $("#src_region").val();				//DESKRIPSI REGION
		var ba_code = $("#src_ba").val();					//DESKRIPSI BA
		var search = $("#search").val();					//SEARCH FREE TEXT
		
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else {
			page_num = (page_num) ? page_num : 1;
			getData();
			//jika periode budget yang dipilih <> periode budget aktif, maka tidak dapat melakukan proses perhitungan
			if (budgetperiod != current_budgetperiod) {
				$("#btn_save").hide();
			}else{
				$("#btn_save").show();
			}
		}
    });	
	$("#btn_refresh").click(function() {
		location.reload();
    });
	$("#btn_export_csv").live("click", function() {	
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find = $("#key_find").val();				//KODE BA
		var region = $("#src_region").val();				//DESKRIPSI REGION
		var ba_code = $("#src_ba").val();					//DESKRIPSI BA
		var search = $("#search").val();					//SEARCH FREE TEXT
		
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else {		
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-report-vra/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/search/" + encode64(search),'_blank');
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
	
	//SEARCH FREE TEXT
	$("#search").live("keydown", function(event) {
		//tekan enter
        if (event.keyCode == 13) {
			event.preventDefault();
		}
    });
	
	//PAGING
	$("#btn_first").click(function() {
        page_num = 1;
        clearDetail();
        getData();
    });
    $("#btn_prev").click(function() {
        page_num--;
        clearDetail();
        getData();
    });
    $("#btn_next").click(function() {
        page_num++;
        clearDetail();
        getData();
    });
    $("#btn_last").click(function() {
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
    });
});

function getData(){
    $("#page_num").val(page_num);
	
    //
    $.ajax({
        type    : "post",
        url     : "report-vra/list",
        data    : $("#form_init").serialize(),
        cache   : false,
        dataType: "json",
        success : function(data) {
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
					var lastTr = ($("#data tr").length-1);	
					var tr = $("#data tr:eq(0)").clone().insertAfter($("#data tr:eq("+lastTr+")"));
                    var index = ($("#data tr").length -1);					
					$("#data tr:eq(" + index + ")").find("input, select").each(function() {
						$(this).attr("id", $(this).attr("id") + index);
					});					
					
					$("#data tr:eq(" + index + ") input[id^=btn00_]").val("");
					$("#data tr:eq(" + index + ") input[id^=btn01_]").val("");
					$("#data tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
					$("#data tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
                    $("#data tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
                    $("#data tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
                    $("#data tr:eq(" + index + ") input[id^=text04_]").val(row.VRA_SUB_CAT_DESCRIPTION);
					$("#data tr:eq(" + index + ") input[id^=text05_]").val(row.VRA_CODE);
                    $("#data tr:eq(" + index + ") input[id^=text06_]").val(row.VRA_TYPE);
                    $("#data tr:eq(" + index + ") input[id^=text07_]").val(row.DESCRIPTION_VRA_TYPE);
					$("#data tr:eq(" + index + ") input[id^=text08_]").val(accounting.formatNumber(row.JUMLAH_ALAT, 0));
					$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text09_]").val(row.TAHUN_ALAT);
					$("#data tr:eq(" + index + ") input[id^=text09_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text10_]").val(row.UOM);
					$("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(row.QTY_DAY, 0));
					$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(row.DAY_YEAR_VRA, 0));
					$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(row.QTY_YEAR, 0));
					$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text14_]").val(accounting.formatNumber(row.TOTAL_QTY_TAHUN, 0));
					$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text15_]").val(accounting.formatNumber(row.JUMLAH_OPERATOR, 0));
					$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(row.GAJI_OPERATOR, 2));
					$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(row.TOTAL_GAJI_OPERATOR, 2));
					$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(row.TUNJANGAN_OPERATOR, 2));
					$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(row.TOTAL_TUNJANGAN_OPERATOR, 2));
					$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(row.TOTAL_GAJI_TUNJANGAN_OPERATOR, 2));
					$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(row.RP_QTY_OPERATOR, 2));
					$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(row.JUMLAH_HELPER, 0));
					$("#data tr:eq(" + index + ") input[id^=text22_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(row.GAJI_HELPER, 2));
					$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text24_]").val(accounting.formatNumber(row.TOTAL_GAJI_HELPER, 2));
					$("#data tr:eq(" + index + ") input[id^=text24_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text25_]").val(accounting.formatNumber(row.TUNJANGAN_HELPER, 2));
					$("#data tr:eq(" + index + ") input[id^=text25_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text26_]").val(accounting.formatNumber(row.TOTAL_TUNJANGAN_HELPER, 2));
					$("#data tr:eq(" + index + ") input[id^=text26_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text27_]").val(accounting.formatNumber(row.TOTAL_GAJI_TUNJANGAN_HELPER, 2));
					$("#data tr:eq(" + index + ") input[id^=text27_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text28_]").val(accounting.formatNumber(row.RP_QTY_HELPER, 2));
					$("#data tr:eq(" + index + ") input[id^=text28_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text29_]").val(accounting.formatNumber(row.RVRA1_VALUE1, 4));
					$("#data tr:eq(" + index + ") input[id^=text29_]").addClass("four_decimal");
					$("#data tr:eq(" + index + ") input[id^=text30_]").val(accounting.formatNumber(row.RVRA1_VALUE2, 2));
					$("#data tr:eq(" + index + ") input[id^=text30_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text31_]").val(accounting.formatNumber(row.RVRA1_VALUE3, 2));
					$("#data tr:eq(" + index + ") input[id^=text31_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text32_]").val(accounting.formatNumber(row.RVRA2_VALUE1, 4));
					$("#data tr:eq(" + index + ") input[id^=text32_]").addClass("four_decimal");
					$("#data tr:eq(" + index + ") input[id^=text33_]").val(accounting.formatNumber(row.RVRA2_VALUE2, 2));
					$("#data tr:eq(" + index + ") input[id^=text33_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text34_]").val(accounting.formatNumber(row.RVRA2_VALUE3, 2));
					$("#data tr:eq(" + index + ") input[id^=text34_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text35_]").val(accounting.formatNumber(row.RVRA3_VALUE1, 4));
					$("#data tr:eq(" + index + ") input[id^=text35_]").addClass("four_decimal");
					$("#data tr:eq(" + index + ") input[id^=text36_]").val(accounting.formatNumber(row.RVRA3_VALUE2, 2));
					$("#data tr:eq(" + index + ") input[id^=text36_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text37_]").val(accounting.formatNumber(row.RVRA3_VALUE3, 2));
					$("#data tr:eq(" + index + ") input[id^=text37_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text38_]").val(accounting.formatNumber(row.RVRA4_VALUE1, 4));
					$("#data tr:eq(" + index + ") input[id^=text38_]").addClass("four_decimal");
					$("#data tr:eq(" + index + ") input[id^=text39_]").val(accounting.formatNumber(row.RVRA4_VALUE2, 2));
					$("#data tr:eq(" + index + ") input[id^=text39_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text40_]").val(accounting.formatNumber(row.RVRA4_VALUE3, 2));
					$("#data tr:eq(" + index + ") input[id^=text40_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text41_]").val(accounting.formatNumber(row.RVRA5_VALUE1, 4));
					$("#data tr:eq(" + index + ") input[id^=text41_]").addClass("four_decimal");
					$("#data tr:eq(" + index + ") input[id^=text42_]").val(accounting.formatNumber(row.RVRA5_VALUE2, 2));
					$("#data tr:eq(" + index + ") input[id^=text42_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text43_]").val(accounting.formatNumber(row.RVRA5_VALUE3, 2));
					$("#data tr:eq(" + index + ") input[id^=text43_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text44_]").val(accounting.formatNumber(row.RVRA6_VALUE1, 4));
					$("#data tr:eq(" + index + ") input[id^=text44_]").addClass("four_decimal");
					$("#data tr:eq(" + index + ") input[id^=text45_]").val(accounting.formatNumber(row.RVRA6_VALUE2, 2));
					$("#data tr:eq(" + index + ") input[id^=text45_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text46_]").val(accounting.formatNumber(row.RVRA6_VALUE3, 2));
					$("#data tr:eq(" + index + ") input[id^=text46_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text47_]").val(accounting.formatNumber(row.RVRA7_VALUE1, 4));
					$("#data tr:eq(" + index + ") input[id^=text47_]").addClass("four_decimal");
					$("#data tr:eq(" + index + ") input[id^=text48_]").val(accounting.formatNumber(row.RVRA7_VALUE2, 2));
					$("#data tr:eq(" + index + ") input[id^=text48_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text49_]").val(accounting.formatNumber(row.RVRA7_VALUE3, 2));
					$("#data tr:eq(" + index + ") input[id^=text49_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text50_]").val(accounting.formatNumber(row.RVRA8_VALUE1, 4));
					$("#data tr:eq(" + index + ") input[id^=text50_]").addClass("four_decimal");
					$("#data tr:eq(" + index + ") input[id^=text51_]").val(accounting.formatNumber(row.RVRA8_VALUE2, 2));
					$("#data tr:eq(" + index + ") input[id^=text51_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text52_]").val(accounting.formatNumber(row.RVRA8_VALUE3, 2));
					$("#data tr:eq(" + index + ") input[id^=text52_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text53_]").val(accounting.formatNumber(row.RVRA9_VALUE1, 4));
					$("#data tr:eq(" + index + ") input[id^=text53_]").addClass("four_decimal");
					$("#data tr:eq(" + index + ") input[id^=text54_]").val(accounting.formatNumber(row.RVRA9_VALUE2, 2));
					$("#data tr:eq(" + index + ") input[id^=text54_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text55_]").val(accounting.formatNumber(row.RVRA9_VALUE3, 2));
					$("#data tr:eq(" + index + ") input[id^=text55_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text56_]").val(accounting.formatNumber(row.RVRA10_VALUE1, 4));
					$("#data tr:eq(" + index + ") input[id^=text56_]").addClass("four_decimal");
					$("#data tr:eq(" + index + ") input[id^=text57_]").val(accounting.formatNumber(row.RVRA10_VALUE2, 2));
					$("#data tr:eq(" + index + ") input[id^=text57_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text58_]").val(accounting.formatNumber(row.RVRA10_VALUE3, 2));
					$("#data tr:eq(" + index + ") input[id^=text58_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text59_]").val(accounting.formatNumber(row.RVRA11_VALUE1, 4));
					$("#data tr:eq(" + index + ") input[id^=text59_]").addClass("four_decimal");
					$("#data tr:eq(" + index + ") input[id^=text60_]").val(accounting.formatNumber(row.RVRA11_VALUE2, 2));
					$("#data tr:eq(" + index + ") input[id^=text60_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text61_]").val(accounting.formatNumber(row.RVRA11_VALUE3, 2));
					$("#data tr:eq(" + index + ") input[id^=text61_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text62_]").val(accounting.formatNumber(row.RVRA12_VALUE1, 4));
					$("#data tr:eq(" + index + ") input[id^=text62_]").addClass("four_decimal");
					$("#data tr:eq(" + index + ") input[id^=text63_]").val(accounting.formatNumber(row.RVRA12_VALUE2, 2));
					$("#data tr:eq(" + index + ") input[id^=text63_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text64_]").val(accounting.formatNumber(row.RVRA12_VALUE3, 2));
					$("#data tr:eq(" + index + ") input[id^=text64_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text65_]").val(accounting.formatNumber(row.RVRA13_VALUE1, 4));
					$("#data tr:eq(" + index + ") input[id^=text65_]").addClass("four_decimal");
					$("#data tr:eq(" + index + ") input[id^=text66_]").val(accounting.formatNumber(row.RVRA13_VALUE2, 2));
					$("#data tr:eq(" + index + ") input[id^=text66_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text67_]").val(accounting.formatNumber(row.RVRA13_VALUE3, 2));
					$("#data tr:eq(" + index + ") input[id^=text67_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text68_]").val(accounting.formatNumber(row.RVRA14_VALUE1, 4));
					$("#data tr:eq(" + index + ") input[id^=text68_]").addClass("four_decimal");
					$("#data tr:eq(" + index + ") input[id^=text69_]").val(accounting.formatNumber(row.RVRA14_VALUE2, 2));
					$("#data tr:eq(" + index + ") input[id^=text69_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text70_]").val(accounting.formatNumber(row.RVRA14_VALUE3, 2));
					$("#data tr:eq(" + index + ") input[id^=text70_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text71_]").val(accounting.formatNumber(row.RVRA15_VALUE1, 4));
					$("#data tr:eq(" + index + ") input[id^=text71_]").addClass("four_decimal");
					$("#data tr:eq(" + index + ") input[id^=text72_]").val(accounting.formatNumber(row.RVRA15_VALUE2, 2));
					$("#data tr:eq(" + index + ") input[id^=text72_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text73_]").val(accounting.formatNumber(row.RVRA15_VALUE3, 2));
					$("#data tr:eq(" + index + ") input[id^=text73_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text74_]").val(accounting.formatNumber(row.RVRA16_VALUE1, 4));
					$("#data tr:eq(" + index + ") input[id^=text74_]").addClass("four_decimal");
					$("#data tr:eq(" + index + ") input[id^=text75_]").val(accounting.formatNumber(row.RVRA16_VALUE2, 2));
					$("#data tr:eq(" + index + ") input[id^=text75_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text76_]").val(accounting.formatNumber(row.RVRA16_VALUE3, 2));
					$("#data tr:eq(" + index + ") input[id^=text76_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text77_]").val(accounting.formatNumber(row.RVRA17_VALUE1, 4));
					$("#data tr:eq(" + index + ") input[id^=text77_]").addClass("four_decimal");
					$("#data tr:eq(" + index + ") input[id^=text78_]").val(accounting.formatNumber(row.RVRA17_VALUE2, 2));
					$("#data tr:eq(" + index + ") input[id^=text78_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text79_]").val(accounting.formatNumber(row.RVRA17_VALUE3, 2));
					$("#data tr:eq(" + index + ") input[id^=text79_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text80_]").val(accounting.formatNumber(row.RVRA18_VALUE1, 4));
					$("#data tr:eq(" + index + ") input[id^=text80_]").addClass("four_decimal");
					$("#data tr:eq(" + index + ") input[id^=text81_]").val(accounting.formatNumber(row.RVRA18_VALUE2, 2));
					$("#data tr:eq(" + index + ") input[id^=text81_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text82_]").val(accounting.formatNumber(row.RVRA18_VALUE3, 2));
					$("#data tr:eq(" + index + ") input[id^=text82_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text83_]").val(accounting.formatNumber(row.TOTAL_BIAYA, 2));
					$("#data tr:eq(" + index + ") input[id^=text83_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text84_]").val(accounting.formatNumber(row.TOTAL_RP_QTY, 2));
					$("#data tr:eq(" + index + ") input[id^=text84_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text85_]").val(accounting.formatNumber(row.RP_QTY_VRA_TYPE, 2));
					$("#data tr:eq(" + index + ") input[id^=text85_]").addClass("number");
					$("#data tr:eq(" + index + ")").removeAttr("style");					
                    $("#data tr:eq(1) input[id^=text02_]").focus();					
                });
            }
        }
    });
}
</script>
