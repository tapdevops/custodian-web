<?php
// Fungsi header dengan mengirimkan raw data excel
header("Content-type: application/vnd-ms-excel");
// Mendefinisikan nama file ekspor
header("Content-Disposition: attachment; filename=Result_Laporan_Rekapitulasi_-_Dokumen_$GrupDokumen.xls");

include ("./config/config_db.php");
error_reporting(E_ALL);
$PHP_SELF = "http://".$_SERVER['HTTP_HOST'];

// foreach ($_POST as $key => $value) {
//     echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
// }
// exit();

//load report class
require_once "./include/class.report-recapitulation.php";
$reportClass = new reportRekapitulasi();

$Output = "";

//connection to database
include ("./config/config_db.php");

if ($_POST['optTipe']=="kekurangan") {
    $data= $reportClass->getDataReport($_POST['optPT'],$_POST['optArea'], $_POST['optTahun']);
    $Output .= "<table>
        <tr>
            <td colspan='7'><h3>Laporan Rekapitulasi Kekurangan Pembebasan Lahan</h3></td>
        </tr>
    </table>";
}
else if ($_POST['optTipe']=="ketersediaan"){
    $data= $reportClass->getDataReportRekapitulasi($_POST['optPT'],$_POST['optArea'], $_POST['optTahun']);
    $Output .= "<table>
        <tr>
            <td colspan='7'><h3>Laporan Rekapitulasi Pembebasan Lahan</h3></td>
        </tr>
    </table>";
}

$result = $reportClass->drawTableHeader($data,$_POST['optTipe']);

$explode = explode("<div class='h5'>Tanggal Cetak : ".date('j M Y')."</div>", $result);

$Output .= str_replace("colspan=100", "colspan=39", $explode[1]);

// Menampilkan Dokumen
echo $Output;

exit();
?>
