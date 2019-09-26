<?php
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Doni Romdoni																						=
= Dibuat Tanggal	: 06 Juni 2012																						=
= Update Terakhir	: 06 Juni 2012																						=
= Revisi			:																									=
= Purpose			: class untuk menampilkam data report Rekapitulasi kekurangan Dokumen GRL							=														=
=========================================================================================================================
*/

class reportRekapitulasi{

private $connection;

function __construct() {
	//this is construct function will run automatically
}


function getPTOption($value){
/*
	create select option from database of Company
*/
	$query="SELECT *
			  FROM M_Company
			  WHERE Company_Delete_Time is NULL
			  AND Company_Area != ''
			  ORDER BY Company_Name";
	$result=mysql_query($query);

	$option = "<option value=''>--- Semua Perusahaan ---</option>";
	while ($object = mysql_fetch_object($result) ){
		if ($value == $object->Company_ID) $selected = 'selected';
		else $selected ="";

		$option .="<option $selected value='".$object->Company_ID."'>".$object->Company_Name."</option>";
	}
	return $option;
}

function getAreaOption($value){
/*
	create select option from database of Company Area
*/
	$query="SELECT distinct Company_ID_Area, Company_Area
			  FROM M_Company
			  WHERE Company_Delete_Time is NULL
			  ORDER BY Company_Area";
	$result=mysql_query($query);

	$option = "<option value=''>--- Semua Area ---</option>";
	while ($object = mysql_fetch_object($result) ){
		if ($value == $object->Company_ID_Area) $selected = 'selected';
		else $selected ="";

		$option .="<option $selected value='".$object->Company_ID_Area."'>".$object->Company_Area."</option>";
	}
	return $option;
}

function getYearOption($value){
/*
	create year option from database of document period
*/
	$value = date('Y');
	$query="SELECT distinct date_format(DLA_Period,'%Y') yearPeriod
			  FROM M_DocumentLandAcquisition
			  WHERE DLA_Delete_Time is NULL
			  ORDER BY DLA_Period desc";
	$result=mysql_query($query);
	$option = "<option value=''>--- Semua Tahun ---</option>";
	while ($object = mysql_fetch_object($result) ){
		if ($value == $object->yearPeriod) $selected = 'selected';
		else $selected ="";

		$option .="<option $selected value='".$object->yearPeriod."'>".$object->yearPeriod."</option>";
	}
	return $option;
}


function getDataReport($pt,$area,$tahun){
/*
*/
	if ($pt) $where .= "AND rekap_dokumen.id_perusahaan = '$pt'";
	if ($area) $where .= " AND rekap_dokumen.kode_area = '$area' ";
	if ($tahun) $where .= " AND rekap_dokumen.tahun = '$tahun' ";
	$query="SELECT rekap_dokumen.*, rekap_kekurangan.* from
				(
					SELECT
			com.`company_id_area` kode_area,
			com.`company_area` area,
			com.`company_id` id_perusahaan,
					dok.`DLA_CompanyID` kode_pt,
					com.company_name nama_pt,
					date_format( dok.`DLA_Period` , '%Y' ) tahun,
					date_format( dok.`DLA_Period` , '%d-%m-%Y') tanggal,
					dok.`DLA_Phase` tahap,
					sum( dok.`DLA_AreaStatement` ) luas_ha,
					sum( dok.`DLA_AreaTotalPrice` ) nominal_ha,
					sum( dok.`DLA_PlantTotalPrice` ) tanam_tumbuh,
					sum( dok.`DLA_GrandTotal` ) total_grl,
					count( dok.`DLA_ID` ) jumlah_orang,
					date_format(max( dok.`DLA_Update_Time` ), '%d/%m/%y')ket
					FROM `M_DocumentLandAcquisition` dok
					LEFT JOIN `M_Company` com ON dok.`DLA_CompanyID` = com.company_id
					WHERE 1
					GROUP BY com.`Company_ID_area` , dok.`DLA_CompanyID` , dok.`DLA_Phase`
				) rekap_dokumen
			left join
				(
					select kode_pt_atr,tahap_atr,
					sum(jml_kekurangan*(1-abs(sign(id_atribut-1)))) as ktp,
					sum(jml_kekurangan*(1-abs(sign(id_atribut-2)))) as kk,
					sum(jml_kekurangan*(1-abs(sign(id_atribut-3)))) as ba_ukur,
					sum(jml_kekurangan*(1-abs(sign(id_atribut-4)))) as peta,
					sum(jml_kekurangan*(1-abs(sign(id_atribut-5)))) as ba_tt,
					sum(jml_kekurangan*(1-abs(sign(id_atribut-6)))) as si,
					sum(jml_kekurangan*(1-abs(sign(id_atribut-7)))) as skt,
					sum(jml_kekurangan*(1-abs(sign(id_atribut-8)))) as sk_phgr,
					sum(jml_kekurangan*(1-abs(sign(id_atribut-9)))) as kwi,
					sum(jml_kekurangan*(1-abs(sign(id_atribut-10)))) as foto,
					sum(jml_kekurangan*(1-abs(sign(id_atribut-11)))) as srt_waris,
					sum(jml_kekurangan*(1-abs(sign(id_atribut-12)))) as sil,
					sum(jml_kekurangan*(1-abs(sign(id_atribut-13)))) as rkp_bagr,
					sum(jml_kekurangan*(1-abs(sign(id_atribut-14)))) as notaris
					from
					(
						SELECT
						dok.DLA_CompanyID kode_pt_atr,
						dok.DLA_Phase tahap_atr,
						dok_atribut.DLAA_LAA_ID id_atribut,
						m_atribut.LAA_Acronym atribut,
						sum(if(dok_atribut.DLAA_LAAS_ID=2,1,0))jml_kekurangan
						FROM `M_DocumentLandAcquisitionAttribute` dok_atribut
						left join M_LandAcquisitionAttribute m_atribut on m_atribut.LAA_ID = dok_atribut.`DLAA_LAA_ID`
						left join M_DocumentLandAcquisition dok on dok_atribut.DLAA_DLA_ID = dok.DLA_ID
						group by dok.DLA_CompanyID, dok.DLA_Phase, dok_atribut.DLAA_LAA_ID
					) rekap_kekurangan
					group by kode_pt_atr,tahap_atr
				)rekap_kekurangan
			on rekap_dokumen.kode_pt = rekap_kekurangan.kode_pt_atr
			and rekap_dokumen.tahap = rekap_kekurangan.tahap_atr
			where 1
			$where
	";
	//echo $query;
	$result=mysql_query($query);
	while ($object = mysql_fetch_object($result) ){
		//print company header
		if ($tahun <> $object->tahun){
			if ($totalHa_tahun) {
				$row .= "<tr style='font-weight:bolder;' bgcolor='#CCCCCC'>";
				$row .= "<td colspan=4 align='right'>Total ".$tahun."</td>";
				$row .= "<td align='right'>".number_format($totalHa_tahun,2)."</td>";
				$row .= "<td align='right'>&nbsp;</td>";
				$row .= "<td align='right'>&nbsp;</td>";
				$row .= "<td align='right'>".number_format($totalGRL_tahun)."</td>";
				$row .= "<td align='center'>".number_format($totalOrang_tahun)."</td>";
				$row .= "<td align='center' colspan=2>$tKel1_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel2_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel3_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel4_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel5_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel6_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel7_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel8_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel9_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel10_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel11_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel12_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel13_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel14_tahun</td>";
				$row .= "<td align='right'>&nbsp;</td>";
				$row .= "</tr>";

				//reset summary value
				$totalHa_tahun = "";
				$totalGRL_tahun = "";
				$totalOrang_tahun = "";
				$tKel1_tahun = "";
				$tKel2_tahun = "";
				$tKel3_tahun = "";
				$tKel4_tahun = "";
				$tKel5_tahun = "";
				$tKel6_tahun = "";
				$tKel7_tahun = "";
				$tKel8_tahun = "";
				$tKel9_tahun = "";
				$tKel10_tahun = "";
				$tKel11_tahun = "";
				$tKel12_tahun = "";
				$tKel13_tahun = "";
				$tKel14_tahun = "";
			}

			if ($pt <> $object->nama_pt){
				if ($totalHa) {
					$row .= "<tr style='font-weight:bolder; color:#FFF;' bgcolor='#000'>";
					$row .= "<td colspan=4 align='right'>Total ".$pt."</td>";
					$row .= "<td align='right'>".number_format($totalHa,2)."</td>";
					$row .= "<td align='right'>&nbsp;</td>";
					$row .= "<td align='right'>&nbsp;</td>";
					$row .= "<td align='right'>".number_format($totalGRL)."</td>";
					$row .= "<td align='center'>".number_format($totalOrang)."</td>";
					$row .= "<td align='center' colspan=2>$tKel1</td>";
					$row .= "<td align='center' colspan=2>$tKel2</td>";
					$row .= "<td align='center' colspan=2>$tKel3</td>";
					$row .= "<td align='center' colspan=2>$tKel4</td>";
					$row .= "<td align='center' colspan=2>$tKel5</td>";
					$row .= "<td align='center' colspan=2>$tKel6</td>";
					$row .= "<td align='center' colspan=2>$tKel7</td>";
					$row .= "<td align='center' colspan=2>$tKel8</td>";
					$row .= "<td align='center' colspan=2>$tKel9</td>";
					$row .= "<td align='center' colspan=2>$tKel10</td>";
					$row .= "<td align='center' colspan=2>$tKel11</td>";
					$row .= "<td align='center' colspan=2>$tKel12</td>";
					$row .= "<td align='center' colspan=2>$tKel13</td>";
					$row .= "<td align='center' colspan=2>$tKel14</td>";
					$row .= "<td align='right'>&nbsp;</td>";
					$row .= "</tr>";

					//reset summary value
					$totalHa = "";
					$totalGRL = "";
					$totalOrang = "";
					$tKel1 = "";
					$tKel2 = "";
					$tKel3 = "";
					$tKel4 = "";
					$tKel5 = "";
					$tKel6 = "";
					$tKel7 = "";
					$tKel8 = "";
					$tKel9 = "";
					$tKel10 = "";
					$tKel11 = "";
					$tKel12 = "";
					$tKel13 = "";
					$tKel14 = "";
				}
			}
			$row .= "<tr><td colspan=100 align='center'><b>".$object->nama_pt."</b></td></tr>";
			$pt = $object->nama_pt;
			$tahun = $object->tahun;
		}

		//total per tahun
		$totalHa_tahun = $totalHa_tahun + $object->luas_ha;
		$totalGRL_tahun = $totalGRL_tahun + $object->total_grl;
		$totalOrang_tahun = $totalOrang_tahun + $object->jumlah_orang;

		//total per PT
		$totalHa = $totalHa + $object->luas_ha;
		$totalGRL = $totalGRL + $object->total_grl;
		$totalOrang = $totalOrang + $object->jumlah_orang;

		//total kekurangan per tahun
		$tKel1_tahun=$tKel1_tahun+$object->ktp;
		$tKel2_tahun=$tKel2_tahun+$object->kk;
		$tKel3_tahun=$tKel3_tahun+$object->ba_ukur;
		$tKel4_tahun=$tKel4_tahun+$object->peta;
		$tKel5_tahun=$tKel5_tahun+$object->ba_tt;
		$tKel6_tahun=$tKel6_tahun+$object->si;
		$tKel7_tahun=$tKel7_tahun+$object->skt;
		$tKel8_tahun=$tKel8_tahun+$object->sk_phgr;
		$tKel9_tahun=$tKel9_tahun+$object->kwi;
		$tKel10_tahun=$tKel10_tahun+$object->foto;
		$tKel11_tahun=$tKel11_tahun+$object->srt_waris;
		$tKel12_tahun=$tKel12_tahun+$object->sil;
		$tKel13_tahun=$tKel13_tahun+$object->rkp_bagr;
		$tKel14_tahun=$tKel14_tahun+$object->notaris;

		//total kekurangan per PT
		$tKel1=$tKel1+$object->ktp;
		$tKel2=$tKel2+$object->kk;
		$tKel3=$tKel3+$object->ba_ukur;
		$tKel4=$tKel4+$object->peta;
		$tKel5=$tKel5+$object->ba_tt;
		$tKel6=$tKel6+$object->si;
		$tKel7=$tKel7+$object->skt;
		$tKel8=$tKel8+$object->sk_phgr;
		$tKel9=$tKel9+$object->kwi;
		$tKel10=$tKel10+$object->foto;
		$tKel11=$tKel11+$object->srt_waris;
		$tKel12=$tKel12+$object->sil;
		$tKel13=$tKel13+$object->rkp_bagr;
		$tKel14=$tKel14+$object->notaris;

		$row .= "<tr style='font-size:8px'>";
		//$row .= "<td>$no</td>";
		$row .= "<td align='center'>".$object->area."</td>";
		$row .= "<td align='center'>".$object->tahun."</td>";
		$row .= "<td align='center'>".$object->tanggal."</td>";
		$row .= "<td align='center'>".$object->tahap."</td>";
		$row .= "<td align='right'>".number_format($object->luas_ha,2)."</td>";
		$row .= "<td align='right'>".number_format($object->nominal_ha)."</td>";
		$row .= "<td align='right'>".number_format($object->tanam_tumbuh)."</td>";
		$row .= "<td align='right'>".number_format($object->total_grl)."</td>";
		$row .= "<td align='center'>".number_format($object->jumlah_orang)."</td>";

		$style=(!$object->ktp)? "style='background:#CCC;'":"";
		$object->ktp=(!$object->ktp)? "&nbsp;":$object->ktp;
		$row .= "<td align='center' $style colspan=2 width=80>".$object->ktp."</td>";

		$style=(!$object->kk)? "style='background:#CCC;'":"";
		$object->kk=(!$object->kk)? "&nbsp;":$object->kk;
		$row .= "<td align='center' $style colspan=2 width=80>".$object->kk."</td>";

		$style=(!$object->ba_ukur)? "style='background:#CCC;'":"";
		$object->ba_ukur=(!$object->ba_ukur)? "&nbsp;":$object->ba_ukur;
		$row .= "<td align='center' $style colspan=2 width=80>".$object->ba_ukur."</td>";

		$style=(!$object->peta)? "style='background:#CCC;'":"";
		$object->peta=(!$object->peta)? "&nbsp;":$object->peta;
		$row .= "<td align='center' $style colspan=2 width=80>".$object->peta."</td>";

		$style=(!$object->ba_tt)? "style='background:#CCC;'":"";
		$object->ba_tt=(!$object->ba_tt)? "&nbsp;":$object->ba_tt;
		$row .= "<td align='center' $style colspan=2 width=80>".$object->ba_tt."</td>";

		$style=(!$object->si)? "style='background:#CCC;'":"";
		$object->si=(!$object->si)? "&nbsp;":$object->si;
		$row .= "<td align='center' $style colspan=2 width=80>".$object->si."</td>";

		$style=(!$object->skt)? "style='background:#CCC;'":"";
		$object->skt=(!$object->skt)? "&nbsp;":$object->skt;
		$row .= "<td align='center' $style colspan=2 width=80>".$object->skt."</td>";

		$style=(!$object->sk_phgr)? "style='background:#CCC;'":"";
		$object->sk_phgr=(!$object->sk_phgr)? "&nbsp;":$object->sk_phgr;
		$row .= "<td align='center' $style colspan=2 width=80>".$object->sk_phgr."</td>";

		$style=(!$object->kwi)? "style='background:#CCC;'":"";
		$object->kwi=(!$object->kwi)? "&nbsp;":$object->kwi;
		$row .= "<td align='center' $style colspan=2 width=80>".$object->kwi."</td>";

		$style=(!$object->foto)? "style='background:#CCC;'":"";
		$object->foto=(!$object->foto)? "&nbsp;":$object->foto;
		$row .= "<td align='center' $style colspan=2 width=80>".$object->foto."</td>";

		$style=(!$object->srt_waris)? "style='background:#CCC;'":"";
		$object->srt_waris=(!$object->srt_waris)? "&nbsp;":$object->srt_waris;
		$row .= "<td align='center' $style colspan=2 width=80>".$object->srt_waris."</td>";

		$style=(!$object->sil)? "style='background:#CCC;'":"";
		$object->sil=(!$object->sil)? "&nbsp;":$object->sil;
		$row .= "<td align='center' $style colspan=2 width=80>".$object->sil."</td>";

		$style=(!$object->rkp_bagr)? "style='background:#CCC;'":"";
		$object->rkp_bagr=(!$object->rkp_bagr)? "&nbsp;":$object->rkp_bagr;
		$row .= "<td align='center' $style colspan=2 width=80>".$object->rkp_bagr."</td>";

		$style=(!$object->notaris)? "style='background:#CCC;'":"";
		$object->notaris=(!$object->notaris)? "&nbsp;":$object->notaris;
		$row .= "<td align='center' $style colspan=2 width=80>".$object->notaris."</td>";

		$row .= "<td align='right' colspan=2 width=80>Updated ".$object->ket."</td>";
		$row .= "</tr>";
	}
		if ($tahun <> $object->tahun){
			if ($totalHa_tahun) {
				$row .= "<tr style='font-weight:bolder;' bgcolor='#CCCCCC'>";
				$row .= "<td colspan=4 align='right'>Total ".$tahun."</td>";
				$row .= "<td align='right'>".number_format($totalHa_tahun,2)."</td>";
				$row .= "<td align='right'>&nbsp;</td>";
				$row .= "<td align='right'>&nbsp;</td>";
				$row .= "<td align='right'>".number_format($totalGRL_tahun)."</td>";
				$row .= "<td align='center'>".number_format($totalOrang_tahun)."</td>";
				$row .= "<td align='center' colspan=2>$tKel1_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel2_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel3_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel4_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel5_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel6_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel7_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel8_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel9_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel10_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel11_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel12_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel13_tahun</td>";
				$row .= "<td align='center' colspan=2>$tKel14_tahun</td>";
				$row .= "<td align='right'>&nbsp;</td>";
				$row .= "</tr>";

				//reset summary value
				$totalHa_tahun = "";
				$totalGRL_tahun = "";
				$totalOrang_tahun = "";
				$tKel1_tahun = "";
				$tKel2_tahun = "";
				$tKel3_tahun = "";
				$tKel4_tahun = "";
				$tKel5_tahun = "";
				$tKel6_tahun = "";
				$tKel7_tahun = "";
				$tKel8_tahun = "";
				$tKel9_tahun = "";
				$tKel10_tahun = "";
				$tKel11_tahun = "";
				$tKel12_tahun = "";
				$tKel13_tahun = "";
				$tKel14_tahun = "";
			}

			if ($pt <> $object->nama_pt){
				if ($totalHa) {
					$row .= "<tr style='font-weight:bolder; color:#FFF;' bgcolor='#000'>";
					$row .= "<td colspan=4 align='right'>Total ".$pt."</td>";
					$row .= "<td align='right'>".number_format($totalHa,2)."</td>";
					$row .= "<td align='right'>&nbsp;</td>";
					$row .= "<td align='right'>&nbsp;</td>";
					$row .= "<td align='right'>".number_format($totalGRL)."</td>";
					$row .= "<td align='center'>".number_format($totalOrang)."</td>";
					$row .= "<td align='center' colspan=2>$tKel1</td>";
					$row .= "<td align='center' colspan=2>$tKel2</td>";
					$row .= "<td align='center' colspan=2>$tKel3</td>";
					$row .= "<td align='center' colspan=2>$tKel4</td>";
					$row .= "<td align='center' colspan=2>$tKel5</td>";
					$row .= "<td align='center' colspan=2>$tKel6</td>";
					$row .= "<td align='center' colspan=2>$tKel7</td>";
					$row .= "<td align='center' colspan=2>$tKel8</td>";
					$row .= "<td align='center' colspan=2>$tKel9</td>";
					$row .= "<td align='center' colspan=2>$tKel10</td>";
					$row .= "<td align='center' colspan=2>$tKel11</td>";
					$row .= "<td align='center' colspan=2>$tKel12</td>";
					$row .= "<td align='center' colspan=2>$tKel13</td>";
					$row .= "<td align='center' colspan=2>$tKel14</td>";
					$row .= "<td align='right'>&nbsp;</td>";
					$row .= "</tr>";

					//reset summary value
					$totalHa = "";
					$totalGRL = "";
					$totalOrang = "";
					$tKel1 = "";
					$tKel2 = "";
					$tKel3 = "";
					$tKel4 = "";
					$tKel5 = "";
					$tKel6 = "";
					$tKel7 = "";
					$tKel8 = "";
					$tKel9 = "";
					$tKel10 = "";
					$tKel11 = "";
					$tKel12 = "";
					$tKel13 = "";
					$tKel14 = "";
				}
			}
			$row .= "<tr><td colspan=100 align='center'><b>".$object->nama_pt."</b></td></tr>";
			$pt = $object->nama_pt;
			$tahun = $object->tahun;
		}

	return $row;
}

function getDataReportRekapitulasi($pt,$area,$tahun){
/*
*/
	if ($pt) $where .= "and rekap_dokumen.id_perusahaan = '$pt'";
	if ($area) $where .= " and rekap_dokumen.kode_area = '$area' ";
	if ($tahun) $where .= " and rekap_dokumen.tahun = '$tahun' ";
	$query="
			select rekap_dokumen.*, rekap_kekurangan.* from
				(
					SELECT
			com.`company_id_area` kode_area,
			com.`company_area` area,
			com.`company_id` id_perusahaan,
					dok.`DLA_CompanyID` kode_pt,
					com.company_name nama_pt,
					date_format( dok.`DLA_Period` , '%Y' ) tahun,
					date_format( dok.`DLA_Period` , '%d-%m-%Y') tanggal,
					dok.`DLA_Phase` tahap,
					sum( dok.`DLA_AreaStatement` ) luas_ha,
					sum( dok.`DLA_AreaTotalPrice` ) nominal_ha,
					sum( dok.`DLA_PlantTotalPrice` ) tanam_tumbuh,
					sum( dok.`DLA_GrandTotal` ) total_grl,
					count( dok.`DLA_ID` ) jumlah_orang,
					date_format(max( dok.`DLA_Update_Time` ), '%d/%m/%y')ket
					FROM `M_DocumentLandAcquisition` dok
					LEFT JOIN `M_Company` com ON dok.`DLA_CompanyID` = com.company_id
					WHERE 1
					GROUP BY com.`Company_ID_area` , dok.`DLA_CompanyID` , dok.`DLA_Phase`
				) rekap_dokumen
			left join
				(
					select kode_pt_atr,tahap_atr,
					sum(jml_kelengkapan*(1-abs(sign(id_atribut-1)))) as ktp_kelengkapan,
					sum(jml_tdkperlu*(1-abs(sign(id_atribut-1)))) as ktp_tdkperlu,
					sum(jml_kondisional*(1-abs(sign(id_atribut-1)))) as ktp_kondisional,
					sum(jml_kelengkapan*(1-abs(sign(id_atribut-2)))) as kk_kelengkapan,
					sum(jml_tdkperlu*(1-abs(sign(id_atribut-2)))) as kk_tdkperlu,
					sum(jml_kondisional*(1-abs(sign(id_atribut-2)))) as kk_kondisional,
					sum(jml_kelengkapan*(1-abs(sign(id_atribut-3)))) as ba_ukur_kelengkapan,
					sum(jml_tdkperlu*(1-abs(sign(id_atribut-3)))) as ba_ukur_tdkperlu,
					sum(jml_kondisional*(1-abs(sign(id_atribut-3)))) as ba_ukur_kondisional,
					sum(jml_kelengkapan*(1-abs(sign(id_atribut-4)))) as peta_kelengkapan,
					sum(jml_tdkperlu*(1-abs(sign(id_atribut-4)))) as peta_tdkperlu,
					sum(jml_kondisional*(1-abs(sign(id_atribut-4)))) as peta_kondisional,
					sum(jml_kelengkapan*(1-abs(sign(id_atribut-5)))) as ba_tt_kelengkapan,
					sum(jml_tdkperlu*(1-abs(sign(id_atribut-5)))) as ba_tt_tdkperlu,
					sum(jml_kondisional*(1-abs(sign(id_atribut-5)))) as ba_tt_kondisional,
					sum(jml_kelengkapan*(1-abs(sign(id_atribut-6)))) as si_kelengkapan,
					sum(jml_tdkperlu*(1-abs(sign(id_atribut-6)))) as si_tdkperlu,
					sum(jml_kondisional*(1-abs(sign(id_atribut-6)))) as si_kondisional,
					sum(jml_kelengkapan*(1-abs(sign(id_atribut-7)))) as skt_kelengkapan,
					sum(jml_tdkperlu*(1-abs(sign(id_atribut-7)))) as skt_tdkperlu,
					sum(jml_kondisional*(1-abs(sign(id_atribut-7)))) as skt_kondisional,
					sum(jml_kelengkapan*(1-abs(sign(id_atribut-8)))) as sk_phgr_kelengkapan,
					sum(jml_tdkperlu*(1-abs(sign(id_atribut-8)))) as sk_phgr_tdkperlu,
					sum(jml_kondisional*(1-abs(sign(id_atribut-8)))) as sk_phgr_kondisional,
					sum(jml_kelengkapan*(1-abs(sign(id_atribut-9)))) as kwi_kelengkapan,
					sum(jml_tdkperlu*(1-abs(sign(id_atribut-9)))) as kwi_tdkperlu,
					sum(jml_kondisional*(1-abs(sign(id_atribut-9)))) as kwi_kondisional,
					sum(jml_kelengkapan*(1-abs(sign(id_atribut-10)))) as foto_kelengkapan,
					sum(jml_tdkperlu*(1-abs(sign(id_atribut-10)))) as foto_tdkperlu,
					sum(jml_kondisional*(1-abs(sign(id_atribut-10)))) as foto_kondisional,
					sum(jml_kelengkapan*(1-abs(sign(id_atribut-11)))) as srt_waris_kelengkapan,
					sum(jml_tdkperlu*(1-abs(sign(id_atribut-11)))) as srt_waris_tdkperlu,
					sum(jml_kondisional*(1-abs(sign(id_atribut-11)))) as srt_waris_kondisional,
					sum(jml_kelengkapan*(1-abs(sign(id_atribut-12)))) as sil_kelengkapan,
					sum(jml_tdkperlu*(1-abs(sign(id_atribut-12)))) as sil_tdkperlu,
					sum(jml_kondisional*(1-abs(sign(id_atribut-12)))) as sil_kondisional,
					sum(jml_kelengkapan*(1-abs(sign(id_atribut-13)))) as rkp_bagr_kelengkapan,
					sum(jml_tdkperlu*(1-abs(sign(id_atribut-13)))) as rkp_bagr_tdkperlu,
					sum(jml_kondisional*(1-abs(sign(id_atribut-13)))) as rkp_bagr_kondisional,
					sum(jml_kelengkapan*(1-abs(sign(id_atribut-14)))) as notaris_kelengkapan,
					sum(jml_tdkperlu*(1-abs(sign(id_atribut-14)))) as notaris_tdkperlu,
					sum(jml_kondisional*(1-abs(sign(id_atribut-14)))) as notaris_kondisional
					from
					(
						SELECT
						dok.DLA_CompanyID kode_pt_atr,
						dok.DLA_Phase tahap_atr,
						dok_atribut.DLAA_LAA_ID id_atribut,
						m_atribut.LAA_Acronym atribut,
						sum(if(dok_atribut.DLAA_LAAS_ID=1,1,0))jml_kelengkapan,
						sum(if(dok_atribut.DLAA_LAAS_ID=3,1,0))jml_tdkperlu,
						sum(if(dok_atribut.DLAA_LAAS_ID=4,1,0))jml_kondisional
						FROM `M_DocumentLandAcquisitionAttribute` dok_atribut
						left join M_LandAcquisitionAttribute m_atribut on m_atribut.LAA_ID = dok_atribut.`DLAA_LAA_ID`
						left join M_DocumentLandAcquisition dok on dok_atribut.DLAA_DLA_ID = dok.DLA_ID
						group by dok.DLA_CompanyID, dok.DLA_Phase, dok_atribut.DLAA_LAA_ID
					) rekap_kekurangan
					group by kode_pt_atr,tahap_atr
				)rekap_kekurangan
			on rekap_dokumen.kode_pt = rekap_kekurangan.kode_pt_atr
			and rekap_dokumen.tahap = rekap_kekurangan.tahap_atr
			where 1
			$where
	";
	//echo $query;
	$result=mysql_query($query);
	while ($object = mysql_fetch_object($result) ){

		//print company header
		if ($tahun <> $object->tahun){
			if ($totalHa_tahun) {
				//Total Akhir per Tahun
				$total_Kel_tahun=$total_Kel1_tahun+$total_Kel2_tahun+$total_Kel3_tahun+$total_Kel4_tahun+$total_Kel5_tahun+$total_Kel6_tahun+$total_Kel7_tahun+$total_Kel8_tahun+$total_Kel9_tahun+$total_Kel10_tahun+$total_Kel11_tahun+$total_Kel12_tahun+$total_Kel3_tahun+$total_Kel14_tahun;
				$total_sKel_tahun=$total_sKel1_tahun+$total_sKel2_tahun+$total_sKel3_tahun+$total_sKel4_tahun+$total_sKel5_tahun+$total_sKel6_tahun+$total_sKel7_tahun+$total_sKel8_tahun+$total_sKel9_tahun+$total_sKel10_tahun+$total_sKel11_tahun+$total_sKel12_tahun+$total_sKel13_tahun+$total_sKel14_tahun;

				$row .= "<tr style='font-weight:bolder; font-size:7px;' bgcolor='#CCCCCC'>";
				$row .= "<td colspan=4 align='right'>Total ".$tahun."</td>";
				$row .= "<td align='right'>".number_format($totalHa_tahun,2)."</td>";
				$row .= "<td align='right'>&nbsp;</td>";
				$row .= "<td align='right'>&nbsp;</td>";
				$row .= "<td align='right'>".number_format($totalGRL_tahun)."</td>";
				$row .= "<td align='center'>".number_format($totalOrang_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel1_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel1_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel2_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel2_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel3_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel3_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel4_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel4_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel5_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel5_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel6_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel6_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel7_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel7_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel8_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel8_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel9_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel9_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel10_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel10_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel11_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel11_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel12_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel12_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel13_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel13_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel14_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel14_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel_tahun)."</td>";
				$row .= "</tr>";

				// Menghitung persentase
				$pKel1_tahun=$total_Kel1_tahun/$total_sKel1_tahun*100;
				$pKel2_tahun=$total_Kel2_tahun/$total_sKel2_tahun*100;
				$pKel3_tahun=$total_Kel3_tahun/$total_sKel3_tahun*100;
				$pKel4_tahun=$total_Kel4_tahun/$total_sKel4_tahun*100;
				$pKel5_tahun=$total_Kel5_tahun/$total_sKel5_tahun*100;
				$pKel6_tahun=$total_Kel6_tahun/$total_sKel6_tahun*100;
				$pKel7_tahun=$total_Kel7_tahun/$total_sKel7_tahun*100;
				$pKel8_tahun=$total_Kel8_tahun/$total_sKel8_tahun*100;
				$pKel9_tahun=$total_Kel9_tahun/$total_sKel9_tahun*100;
				$pKel10_tahun=$total_Kel10_tahun/$total_sKel10_tahun*100;
				$pKel11_tahun=$total_Kel11_tahun/$total_sKel11_tahun*100;
				$pKel12_tahun=$total_Kel12_tahun/$total_sKel12_tahun*100;
				$pKel13_tahun=$total_Kel13_tahun/$total_sKel13_tahun*100;
				$pKel14_tahun=$total_Kel14_tahun/$total_sKel14_tahun*100;
				$pKel_tahun=$total_Kel_tahun/$total_sKel_tahun*100;

				//Menampilkan Persentase
				$row .= "<tr style='font-weight:bolder; font-size:7px;' bgcolor='#CCCCCC'>";
				$row .= "<td colspan=9 align='right'>&nbsp;</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel1_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel2_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel3_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel4_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel5_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel6_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel7_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel8_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel9_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel10_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel11_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel12_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel13_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel14_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel_tahun,0)."%</td>";
				$row .= "</tr>";

				//reset summary value
				$totalHa_tahun = "";
				$totalGRL_tahun = "";
				$totalOrang_tahun = "";
				$total_Kel1_tahun= "";
				$total_Kel2_tahun= "";
				$total_Kel3_tahun= "";
				$total_Kel4_tahun= "";
				$total_Kel5_tahun= "";
				$total_Kel6_tahun= "";
				$total_Kel7_tahun= "";
				$total_Kel8_tahun= "";
				$total_Kel9_tahun= "";
				$total_Kel10_tahun= "";
				$total_Kel11_tahun= "";
				$total_Kel12_tahun= "";
				$total_Kel13_tahun= "";
				$total_Kel14_tahun= "";
				$total_sKel1_tahun= "";
				$total_sKel2_tahun= "";
				$total_sKel3_tahun= "";
				$total_sKel4_tahun= "";
				$total_sKel5_tahun= "";
				$total_sKel6_tahun= "";
				$total_sKel7_tahun= "";
				$total_sKel8_tahun= "";
				$total_sKel9_tahun= "";
				$total_sKel10_tahun= "";
				$total_sKel11_tahun= "";
				$total_sKel12_tahun= "";
				$total_sKel13_tahun= "";
				$total_sKel14_tahun= "";
				$total_Kel_tahun= "";
				$total_sKel_tahun= "";
			}

			if ($pt <> $object->nama_pt){
				if ($totalHa) {
					//Total Akhir per PT
					$total_Kel=$total_Kel1+$total_Kel2+$total_Kel3+$total_Kel4+$total_Kel5+$total_Kel6+$total_Kel7+$total_Kel8+$total_Kel9+$total_Kel10+$total_Kel11+$total_Kel12+$total_Kel3+$total_Kel14;
					$total_sKel=$total_sKel1+$total_sKel2+$total_sKel3+$total_sKel4+$total_sKel5+$total_sKel6+$total_sKel7+$total_sKel8+$total_sKel9+$total_sKel10+$total_sKel11+$total_sKel12+$total_sKel13+$total_sKel14;

					$row .= "<tr style='font-weight:bolder; color:#FFF; font-size:7px;' bgcolor='#000'>";
					$row .= "<td colspan=4 align='right'>Total ".$pt."</td>";
					$row .= "<td align='right'>".number_format($totalHa,2)."</td>";
					$row .= "<td align='right'>&nbsp;</td>";
					$row .= "<td align='right'>&nbsp;</td>";
					$row .= "<td align='right'>".number_format($totalGRL)."</td>";
					$row .= "<td align='center'>".number_format($totalOrang)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel1)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel1)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel2)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel2)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel3)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel3)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel4)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel4)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel5)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel5)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel6)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel6)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel7)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel7)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel8)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel8)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel9)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel9)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel10)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel10)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel11)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel11)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel12)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel12)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel13)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel13)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel14)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel14)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel)."</td>";
					$row .= "</tr>";

					// Menghitung persentase
					$pKel1=$total_Kel1/$total_sKel1*100;
					$pKel2=$total_Kel2/$total_sKel2*100;
					$pKel3=$total_Kel3/$total_sKel3*100;
					$pKel4=$total_Kel4/$total_sKel4*100;
					$pKel5=$total_Kel5/$total_sKel5*100;
					$pKel6=$total_Kel6/$total_sKel6*100;
					$pKel7=$total_Kel7/$total_sKel7*100;
					$pKel8=$total_Kel8/$total_sKel8*100;
					$pKel9=$total_Kel9/$total_sKel9*100;
					$pKel10=$total_Kel10/$total_sKel10*100;
					$pKel11=$total_Kel11/$total_sKel11*100;
					$pKel12=$total_Kel12/$total_sKel12*100;
					$pKel13=$total_Kel13/$total_sKel13*100;
					$pKel14=$total_Kel14/$total_sKel14*100;
					$pKel=$total_Kel/$total_sKel*100;

					//Menampilkan Persentase
					$row .= "<tr style='font-weight:bolder; color:#FFF; font-size:7px;' bgcolor='#000'>";
					$row .= "<td colspan=9 align='right'>&nbsp;</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel1,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel2,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel3,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel4,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel5,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel6,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel7,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel8,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel9,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel10,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel11,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel12,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel13,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel14,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel,0)."%</td>";
					$row .= "</tr>";

					//reset summary value
					$totalHa = "";
					$totalGRL = "";
					$totalOrang = "";
					$total_Kel1= "";
					$total_Kel2= "";
					$total_Kel3= "";
					$total_Kel4= "";
					$total_Kel5= "";
					$total_Kel6= "";
					$total_Kel7= "";
					$total_Kel8= "";
					$total_Kel9= "";
					$total_Kel10= "";
					$total_Kel11= "";
					$total_Kel12= "";
					$total_Kel13= "";
					$total_Kel14= "";
					$total_sKel1= "";
					$total_sKel2= "";
					$total_sKel3= "";
					$total_sKel4= "";
					$total_sKel5= "";
					$total_sKel6= "";
					$total_sKel7= "";
					$total_sKel8= "";
					$total_sKel9= "";
					$total_sKel10= "";
					$total_sKel11= "";
					$total_sKel12= "";
					$total_sKel13= "";
					$total_sKel14= "";
					$total_Kel= "";
					$total_sKel= "";
				}
			}
			$row .= "<tr><td colspan=100 align='center'><b>".$object->nama_pt."</b></td></tr>";
			$pt = $object->nama_pt;
			$tahun = $object->tahun;
		}

		//total per tahun
		$totalHa_tahun = $totalHa_tahun + $object->luas_ha;
		$totalGRL_tahun = $totalGRL_tahun + $object->total_grl;
		$totalOrang_tahun = $totalOrang_tahun + $object->jumlah_orang;

		//total per PT
		$totalHa = $totalHa + $object->luas_ha;
		$totalGRL = $totalGRL + $object->total_grl;
		$totalOrang = $totalOrang + $object->jumlah_orang;

		//jumlah seharusnya ada
		$sKel1=$object->jumlah_orang-$object->ktp_tdkperlu;
		$sKel2=$object->jumlah_orang-$object->kk_tdkperlu;
		$sKel3=$object->jumlah_orang-$object->ba_ukur_tdkperlu;
		$sKel4=$object->jumlah_orang-$object->peta_tdkperlu;
		$sKel5=$object->jumlah_orang-$object->ba_tt_tdkperlu;
		$sKel6=$object->jumlah_orang-$object->si_tdkperlu;
		$sKel7=$object->jumlah_orang-$object->skt_tdkperlu;
		$sKel8=$object->jumlah_orang-$object->sk_phgr_tdkperlu;
		$sKel9=$object->jumlah_orang-$object->kwi_tdkperlu;
		$sKel10=$object->jumlah_orang-$object->foto_tdkperlu;
		$sKel11=$object->jumlah_orang-$object->srt_waris_tdkperlu;
		$sKel12=$object->jumlah_orang-$object->sil_tdkperlu;
		$sKel13=$object->jumlah_orang-$object->rkp_bagr_tdkperlu;
		$sKel14=$object->notaris_kelengkapan;

		//total yang ada per tahun
		$total_Kel1_tahun=$total_Kel1_tahun+$object->ktp_kelengkapan;
		$total_Kel2_tahun=$total_Kel2_tahun+$object->kk_kelengkapan;
		$total_Kel3_tahun=$total_Kel3_tahun+$object->ba_ukur_kelengkapan;
		$total_Kel4_tahun=$total_Kel4_tahun+$object->peta_kelengkapan;
		$total_Kel5_tahun=$total_Kel5_tahun+$object->ba_tt_kelengkapan;
		$total_Kel6_tahun=$total_Kel6_tahun+$object->si_kelengkapan;
		$total_Kel7_tahun=$total_Kel7_tahun+$object->skt_kelengkapan;
		$total_Kel8_tahun=$total_Kel8_tahun+$object->sk_phgr_kelengkapan;
		$total_Kel9_tahun=$total_Kel9_tahun+$object->kwi_kelengkapan;
		$total_Kel10_tahun=$total_Kel10_tahun+$object->foto_kelengkapan;
		$total_Kel11_tahun=$total_Kel11_tahun+$object->srt_waris_kelengkapan;
		$total_Kel12_tahun=$total_Kel12_tahun+$object->sil_kelengkapan;
		$total_Kel13_tahun=$total_Kel13_tahun+$object->rkp_bagr_kelengkapan;
		$total_Kel14_tahun=$total_Kel14_tahun+$object->notaris_kelengkapan;

		//total jumlah seharusnya ada per tahun
		$total_sKel1_tahun=$total_sKel1_tahun+$sKel1;
		$total_sKel2_tahun=$total_sKel2_tahun+$sKel2;
		$total_sKel3_tahun=$total_sKel3_tahun+$sKel3;
		$total_sKel4_tahun=$total_sKel4_tahun+$sKel4;
		$total_sKel5_tahun=$total_sKel5_tahun+$sKel5;
		$total_sKel6_tahun=$total_sKel6_tahun+$sKel6;
		$total_sKel7_tahun=$total_sKel7_tahun+$sKel7;
		$total_sKel8_tahun=$total_sKel8_tahun+$sKel8;
		$total_sKel9_tahun=$total_sKel9_tahun+$sKel9;
		$total_sKel10_tahun=$total_sKel10_tahun+$sKel10;
		$total_sKel11_tahun=$total_sKel11_tahun+$sKel11;
		$total_sKel12_tahun=$total_sKel12_tahun+$sKel12;
		$total_sKel13_tahun=$total_sKel13_tahun+$sKel13;
		$total_sKel14_tahun=$total_sKel14_tahun+$sKel14;

		//total yang ada per PT
		$total_Kel1=$total_Kel1+$object->ktp_kelengkapan;
		$total_Kel2=$total_Kel2+$object->kk_kelengkapan;
		$total_Kel3=$total_Kel3+$object->ba_ukur_kelengkapan;
		$total_Kel4=$total_Kel4+$object->peta_kelengkapan;
		$total_Kel5=$total_Kel5+$object->ba_tt_kelengkapan;
		$total_Kel6=$total_Kel6+$object->si_kelengkapan;
		$total_Kel7=$total_Kel7+$object->skt_kelengkapan;
		$total_Kel8=$total_Kel8+$object->sk_phgr_kelengkapan;
		$total_Kel9=$total_Kel9+$object->kwi_kelengkapan;
		$total_Kel10=$total_Kel10+$object->foto_kelengkapan;
		$total_Kel11=$total_Kel11+$object->srt_waris_kelengkapan;
		$total_Kel12=$total_Kel12+$object->sil_kelengkapan;
		$total_Kel13=$total_Kel13+$object->rkp_bagr_kelengkapan;
		$total_Kel14=$total_Kel14+$object->notaris_kelengkapan;

		//total jumlah seharusnya ada per PT
		$total_sKel1=$total_sKel1+$sKel1;
		$total_sKel2=$total_sKel2+$sKel2;
		$total_sKel3=$total_sKel3+$sKel3;
		$total_sKel4=$total_sKel4+$sKel4;
		$total_sKel5=$total_sKel5+$sKel5;
		$total_sKel6=$total_sKel6+$sKel6;
		$total_sKel7=$total_sKel7+$sKel7;
		$total_sKel8=$total_sKel8+$sKel8;
		$total_sKel9=$total_sKel9+$sKel9;
		$total_sKel10=$total_sKel10+$sKel10;
		$total_sKel11=$total_sKel11+$sKel11;
		$total_sKel12=$total_sKel12+$sKel12;
		$total_sKel13=$total_sKel13+$sKel13;
		$total_sKel14=$total_sKel14+$sKel14;

		//menampilkan data
		$row .= "<tr style='font-size:8px'>";
		//$row .= "<td>$no</td>";
		$row .= "<td align='center'>".$object->area."</td>";
		$row .= "<td align='center'>".$object->tahun."</td>";
		$row .= "<td align='center'>".$object->tanggal."</td>";
		$row .= "<td align='center'>".$object->tahap."</td>";
		$row .= "<td align='right'>".number_format($object->luas_ha,2)."</td>";
		$row .= "<td align='right'>".number_format($object->nominal_ha)."</td>";
		$row .= "<td align='right'>".number_format($object->tanam_tumbuh)."</td>";
		$row .= "<td align='right'>".number_format($object->total_grl)."</td>";
		$row .= "<td align='center'>".number_format($object->jumlah_orang)."</td>";

		$style=(!$object->ktp_kelengkapan)? "style='background:#CCC;'":"";
		$row .= "<td align='center' $style width='40'>".$object->ktp_kelengkapan."</td>";
		$row .= "<td align='center' $style width='40'>".$sKel1."</td>";

		$style=(!$object->kk_kelengkapan)? "style='background:#CCC;'":"";
		$row .= "<td align='center' $style width='40'>".$object->kk_kelengkapan."</td>";
		$row .= "<td align='center' $style width='40'>".$sKel2."</td>";

		$style=(!$object->ba_ukur_kelengkapan)? "style='background:#CCC;'":"";
		$row .= "<td align='center' $style width='40'>".$object->ba_ukur_kelengkapan."</td>";
		$row .= "<td align='center' $style width='40'>".$sKel3."</td>";

		$style=(!$object->peta_kelengkapan)? "style='background:#CCC;'":"";
		$row .= "<td align='center' $style width='40'>".$object->peta_kelengkapan."</td>";
		$row .= "<td align='center' $style width='40'>".$sKel4."</td>";

		$style=(!$object->ba_tt_kelengkapan)? "style='background:#CCC;'":"";
		$row .= "<td align='center' $style width='40'>".$object->ba_tt_kelengkapan."</td>";
		$row .= "<td align='center' $style width='40'>".$sKel5."</td>";

		$style=(!$object->si_kelengkapan)? "style='background:#CCC;'":"";
		$row .= "<td align='center' $style width='40'>".$object->si_kelengkapan."</td>";
		$row .= "<td align='center' $style width='40'>".$sKel6."</td>";

		$style=(!$object->skt_kelengkapan)? "style='background:#CCC;'":"";
		$row .= "<td align='center' $style width='40'>".$object->skt_kelengkapan."</td>";
		$row .= "<td align='center' $style width='40'>".$sKel7."</td>";

		$style=(!$object->sk_phgr_kelengkapan)? "style='background:#CCC;'":"";
		$row .= "<td align='center' $style width='40'>".$object->sk_phgr_kelengkapan."</td>";
		$row .= "<td align='center' $style width='40'>".$sKel8."</td>";

		$style=(!$object->kwi_kelengkapan)? "style='background:#CCC;'":"";
		$row .= "<td align='center' $style width='40'>".$object->kwi_kelengkapan."</td>";
		$row .= "<td align='center' $style width='40'>".$sKel9."</td>";

		$style=(!$object->foto_kelengkapan)? "style='background:#CCC;'":"";
		$row .= "<td align='center' $style width='40'>".$object->foto_kelengkapan."</td>";
		$row .= "<td align='center' $style width='40'>".$sKel10."</td>";

		$style=(!$object->srt_waris_kelengkapan)? "style='background:#CCC;'":"";
		$row .= "<td align='center' $style width='40'>".$object->srt_waris_kelengkapan."</td>";
		$row .= "<td align='center' $style width='40'>".$sKel11."</td>";

		$style=(!$object->sil_kelengkapan)? "style='background:#CCC;'":"";
		$row .= "<td align='center' $style width='40'>".$object->sil_kelengkapan."</td>";
		$row .= "<td align='center' $style width='40'>".$sKel12."</td>";

		$style=(!$object->rkp_bagr_kelengkapan)? "style='background:#CCC;'":"";
		$row .= "<td align='center' $style width='40'>".$object->rkp_bagr_kelengkapan."</td>";
		$row .= "<td align='center' $style width='40'>".$sKel13."</td>";

		$style=(!$object->notaris_kelengkapan)? "style='background:#CCC;'":"";
		$row .= "<td align='center' $style width='40'>".$object->notaris_kelengkapan."</td>";
		$row .= "<td align='center' $style width='40'>".$sKel14."</td>";

		$row .= "<td align='right' colspan=2>Updated ".$object->ket."</td>";
		$row .= "</tr>";

	}
		if ($tahun <> $object->tahun){
			if ($totalHa_tahun) {
				//Total Akhir per Tahun
				$total_Kel_tahun=$total_Kel1_tahun+$total_Kel2_tahun+$total_Kel3_tahun+$total_Kel4_tahun+$total_Kel5_tahun+$total_Kel6_tahun+$total_Kel7_tahun+$total_Kel8_tahun+$total_Kel9_tahun+$total_Kel10_tahun+$total_Kel11_tahun+$total_Kel12_tahun+$total_Kel3_tahun+$total_Kel14_tahun;
				$total_sKel_tahun=$total_sKel1_tahun+$total_sKel2_tahun+$total_sKel3_tahun+$total_sKel4_tahun+$total_sKel5_tahun+$total_sKel6_tahun+$total_sKel7_tahun+$total_sKel8_tahun+$total_sKel9_tahun+$total_sKel10_tahun+$total_sKel11_tahun+$total_sKel12_tahun+$total_sKel13_tahun+$total_sKel14_tahun;

				$row .= "<tr style='font-weight:bolder; font-size:7px;' bgcolor='#CCCCCC'>";
				$row .= "<td colspan=4 align='right'>Total ".$tahun."</td>";
				$row .= "<td align='right'>".number_format($totalHa_tahun,2)."</td>";
				$row .= "<td align='right'>&nbsp;</td>";
				$row .= "<td align='right'>&nbsp;</td>";
				$row .= "<td align='right'>".number_format($totalGRL_tahun)."</td>";
				$row .= "<td align='center'>".number_format($totalOrang_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel1_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel1_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel2_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel2_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel3_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel3_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel4_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel4_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel5_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel5_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel6_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel6_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel7_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel7_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel8_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel8_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel9_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel9_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel10_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel10_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel11_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel11_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel12_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel12_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel13_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel13_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel14_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel14_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_Kel_tahun)."</td>";
				$row .= "<td align='center'>".number_format($total_sKel_tahun)."</td>";
				$row .= "</tr>";

				// Menghitung persentase
				$pKel1_tahun=$total_Kel1_tahun/$total_sKel1_tahun*100;
				$pKel2_tahun=$total_Kel2_tahun/$total_sKel2_tahun*100;
				$pKel3_tahun=$total_Kel3_tahun/$total_sKel3_tahun*100;
				$pKel4_tahun=$total_Kel4_tahun/$total_sKel4_tahun*100;
				$pKel5_tahun=$total_Kel5_tahun/$total_sKel5_tahun*100;
				$pKel6_tahun=$total_Kel6_tahun/$total_sKel6_tahun*100;
				$pKel7_tahun=$total_Kel7_tahun/$total_sKel7_tahun*100;
				$pKel8_tahun=$total_Kel8_tahun/$total_sKel8_tahun*100;
				$pKel9_tahun=$total_Kel9_tahun/$total_sKel9_tahun*100;
				$pKel10_tahun=$total_Kel10_tahun/$total_sKel10_tahun*100;
				$pKel11_tahun=$total_Kel11_tahun/$total_sKel11_tahun*100;
				$pKel12_tahun=$total_Kel12_tahun/$total_sKel12_tahun*100;
				$pKel13_tahun=$total_Kel13_tahun/$total_sKel13_tahun*100;
				$pKel14_tahun=$total_Kel14_tahun/$total_sKel14_tahun*100;
				$pKel_tahun=$total_Kel_tahun/$total_sKel_tahun*100;

				//Menampilkan Persentase
				$row .= "<tr style='font-weight:bolder; font-size:7px;' bgcolor='#CCCCCC'>";
				$row .= "<td colspan=9 align='right'>&nbsp;</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel1_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel2_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel3_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel4_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel5_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel6_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel7_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel8_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel9_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel10_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel11_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel12_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel13_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel14_tahun,0)."%</td>";
				$row .= "<td align='center' colspan=2>".number_format($pKel_tahun,0)."%</td>";
				$row .= "</tr>";

				//reset summary value
				$totalHa_tahun = "";
				$totalGRL_tahun = "";
				$totalOrang_tahun = "";
				$total_Kel1_tahun= "";
				$total_Kel2_tahun= "";
				$total_Kel3_tahun= "";
				$total_Kel4_tahun= "";
				$total_Kel5_tahun= "";
				$total_Kel6_tahun= "";
				$total_Kel7_tahun= "";
				$total_Kel8_tahun= "";
				$total_Kel9_tahun= "";
				$total_Kel10_tahun= "";
				$total_Kel11_tahun= "";
				$total_Kel12_tahun= "";
				$total_Kel13_tahun= "";
				$total_Kel14_tahun= "";
				$total_sKel1_tahun= "";
				$total_sKel2_tahun= "";
				$total_sKel3_tahun= "";
				$total_sKel4_tahun= "";
				$total_sKel5_tahun= "";
				$total_sKel6_tahun= "";
				$total_sKel7_tahun= "";
				$total_sKel8_tahun= "";
				$total_sKel9_tahun= "";
				$total_sKel10_tahun= "";
				$total_sKel11_tahun= "";
				$total_sKel12_tahun= "";
				$total_sKel13_tahun= "";
				$total_sKel14_tahun= "";
				$total_Kel_tahun= "";
				$total_sKel_tahun= "";
			}

			if ($pt <> $object->nama_pt){
				if ($totalHa) {
					//Total Akhir per PT
					$total_Kel=$total_Kel1+$total_Kel2+$total_Kel3+$total_Kel4+$total_Kel5+$total_Kel6+$total_Kel7+$total_Kel8+$total_Kel9+$total_Kel10+$total_Kel11+$total_Kel12+$total_Kel3+$total_Kel14;
					$total_sKel=$total_sKel1+$total_sKel2+$total_sKel3+$total_sKel4+$total_sKel5+$total_sKel6+$total_sKel7+$total_sKel8+$total_sKel9+$total_sKel10+$total_sKel11+$total_sKel12+$total_sKel13+$total_sKel14;

					$row .= "<tr style='font-weight:bolder; color:#FFF; font-size:7px;' bgcolor='#000'>";
					$row .= "<td colspan=4 align='right'>Total ".$pt."</td>";
					$row .= "<td align='right'>".number_format($totalHa,2)."</td>";
					$row .= "<td align='right'>&nbsp;</td>";
					$row .= "<td align='right'>&nbsp;</td>";
					$row .= "<td align='right'>".number_format($totalGRL)."</td>";
					$row .= "<td align='center'>".number_format($totalOrang)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel1)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel1)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel2)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel2)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel3)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel3)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel4)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel4)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel5)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel5)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel6)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel6)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel7)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel7)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel8)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel8)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel9)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel9)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel10)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel10)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel11)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel11)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel12)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel12)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel13)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel13)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel14)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel14)."</td>";
					$row .= "<td align='center'>".number_format($total_Kel)."</td>";
					$row .= "<td align='center'>".number_format($total_sKel)."</td>";
					$row .= "</tr>";

					// Menghitung persentase
					$pKel1=$total_Kel1/$total_sKel1*100;
					$pKel2=$total_Kel2/$total_sKel2*100;
					$pKel3=$total_Kel3/$total_sKel3*100;
					$pKel4=$total_Kel4/$total_sKel4*100;
					$pKel5=$total_Kel5/$total_sKel5*100;
					$pKel6=$total_Kel6/$total_sKel6*100;
					$pKel7=$total_Kel7/$total_sKel7*100;
					$pKel8=$total_Kel8/$total_sKel8*100;
					$pKel9=$total_Kel9/$total_sKel9*100;
					$pKel10=$total_Kel10/$total_sKel10*100;
					$pKel11=$total_Kel11/$total_sKel11*100;
					$pKel12=$total_Kel12/$total_sKel12*100;
					$pKel13=$total_Kel13/$total_sKel13*100;
					$pKel14=$total_Kel14/$total_sKel14*100;
					$pKel=$total_Kel/$total_sKel*100;

					//Menampilkan Persentase
					$row .= "<tr style='font-weight:bolder; color:#FFF; font-size:7px;' bgcolor='#000'>";
					$row .= "<td colspan=9 align='right'>&nbsp;</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel1,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel2,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel3,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel4,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel5,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel6,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel7,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel8,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel9,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel10,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel11,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel12,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel13,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel14,0)."%</td>";
					$row .= "<td align='center' colspan=2>".number_format($pKel,0)."%</td>";
					$row .= "</tr>";

					//reset summary value
					$totalHa = "";
					$totalGRL = "";
					$totalOrang = "";
					$total_Kel1= "";
					$total_Kel2= "";
					$total_Kel3= "";
					$total_Kel4= "";
					$total_Kel5= "";
					$total_Kel6= "";
					$total_Kel7= "";
					$total_Kel8= "";
					$total_Kel9= "";
					$total_Kel10= "";
					$total_Kel11= "";
					$total_Kel12= "";
					$total_Kel13= "";
					$total_Kel14= "";
					$total_sKel1= "";
					$total_sKel2= "";
					$total_sKel3= "";
					$total_sKel4= "";
					$total_sKel5= "";
					$total_sKel6= "";
					$total_sKel7= "";
					$total_sKel8= "";
					$total_sKel9= "";
					$total_sKel10= "";
					$total_sKel11= "";
					$total_sKel12= "";
					$total_sKel13= "";
					$total_sKel14= "";
					$total_Kel= "";
					$total_sKel= "";
				}
			}
			$row .= "<tr><td colspan=100 align='center'><b>".$object->nama_pt."</b></td></tr>";
			$pt = $object->nama_pt;
			$tahun = $object->tahun;
		}

	return $row;
}

function drawTableHeader($rows,$optTipe){
	$table  ="<div id='header'>
			  <input type='button' name='PrintButton' id='PrintButton' onclick='printPage()' value='CETAK' class='print-button' />
				 <div id='header-inside'>
					<div class='tap'>PT Triputra Agro Persada </div>
					<div class='custodian'>Custodian Department </div>
					<div class='alamat'>Jalan DR.Ide Anak Agung Gde Agung Kav. E.3.2. No 1<br />
					Jakarta - 12950</div>
				 </div>
			  </div>
			  <div id='content'>";
	if ($optTipe=="kekurangan")
		$table .="<div id='title'>Laporan Rekapitulasi Kekurangan Dokumen Pembebasan Lahan</div>";
	else
		$table .="<div id='title'>Laporan Rekapitulasi Dokumen Pembebasan Lahan</div>";
	$table .="<div class='h5'>Tanggal Cetak : ".date('j M Y')."</div>";
	$table .= "<table width='100%' cellpadding='0' cellspacing='0' border='1' style='border:3px solid #000;'>";
	$table .= "<tr>";
	$table .= "<th colspan=3>Perusahaan</th>";
	$table .= "<th rowspan=2>Tahap</th>";
	$table .= "<th colspan=2>Luas Area</th>";
	$table .= "<th rowspan=2>Tanam Tumbuh</th>";
	$table .= "<th rowspan=2>Total GRL</th>";
	$table .= "<th rowspan=2>Jml Orang</th>";
	$table .= "<th colspan=28>Kelengkapan Dokumen</th>";
	$table .= "<th rowspan=2 colspan=2>Ket</th>";
	$table .= "</tr>";
	$table .= "<tr>";
	$table .= "<th>Area</th>";
	$table .= "<th>Tahun</th>";
	$table .= "<th>Tanggal</th>";
	$table .= "<th>Ha</th>";
	$table .= "<th>Nominal</th>";
	$table .= "<th colspan=2 valign='top'>KTP</th>";
	$table .= "<th colspan=2 valign='top'>KK</th>";
	$table .= "<th colspan=2 valign='top'>BA Ukur</th>";
	$table .= "<th colspan=2 valign='top'>Peta</th>";
	$table .= "<th colspan=2 valign='top'>BA T.T</th>";
	$table .= "<th colspan=2 valign='top'>S/I</th>";
	$table .= "<th colspan=2 valign='top'>SKT</th>";
	$table .= "<th colspan=2 valign='top'>SPPH</th>";
	$table .= "<th colspan=2 valign='top'>Kwi<br>tansi</th>";
	$table .= "<th colspan=2 valign='top'>Foto</th>";
	$table .= "<th colspan=2 valign='top'>Srt Waris</th>";
	$table .= "<th colspan=2 valign='top'>Sil<br>silah</th>";
	$table .= "<th colspan=2 valign='top'>Rkp<br>BAGR</th>";
	$table .= "<th colspan=2 valign='top'>Nota<br>ris</th>";
	$table .= "</tr>$rows</table>";
	return $table;
}

//end class
}

?>
