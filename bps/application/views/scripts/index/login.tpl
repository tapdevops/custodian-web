<? 
$global = new Application_Model_Global();
$ua = $global->getBrowser();

if (($ua['name'] == 'Mozilla Firefox' && (int)($ua['version']) < 10) || ($ua['name'] == 'Internet Explorer' && (int)($ua['version']) < 8)){ 
?>
	<div class="login_box">
		<div class="login_content txt" style='padding-top: 50px;'>
			<span style='color:red; font-size:18px;'>OUTDATED BROWSER</span><br><br>
			Anda Harus Update Browser Anda<BR>
			Untuk Dapat Menggunakan<BR>
			"BUDGET PREPARATION SYSTEM".<BR><BR>
			Minimum browser :<BR>
			Internet Explorer 7.0, Mozilla Firefox 10.0<BR><BR>
			
			<a href='http://portal.tap-agri.com/custom/firefox.zip' style='color:blue;' target='_blank'>KLIK DISINI UNTUK DOWNLOAD.</a>
		</div>
	</div>

	<script type="text/javascript">
	$(document).ready(function() {
		$(".page").css('height','0');
	});
	</script>	
<? } else { ?>
	<style>
		select{
    text-align-last:center;
    padding-right: 29px;
    #direction: rtl;
}
	</style>
	<div class="login_box" style="height:325px;">
		<div class="login_content">
			<form name="login" id="login" method="post" action="index/login">
				<table border="0" width="100%" cellspacing="2" cellpadding="4">
					<tr>
						<td><img src='images/logo.png'></td>
					</tr>
					<tr>
						<td align='center'><span class="txt">Nama Pengguna :</span></td>
					</tr>
					<tr>
						<td><input type="text" name="username" id="username" value="" class="textbox ucase" /></td>
					</tr>
					<tr>
						<td align='center'><span class="txt">Kata Sandi :</span></td>
					</tr>
					<tr>
						<td><input type="password" name="userpass" id="userpass" value="" class="textbox" /></td>
					</tr>
					<tr>
						<td align='center'><span class="txt">Tipe Login :</span></td>
					</tr>
					<tr>
						<td>
							<select name="usertipe" id="usertipe" class="textbox" style="width:260px; border:thin solid #4D4D4D; padding:3px;">
								<option value="1">Budgeting Site</option>
								<option value="2">Budgeting HO</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
						<?php if($this->msg != '') { ?>
							<span class="msg"><?php echo $this->msg; ?></span>
						<?php } else {?>
							&nbsp;
						<?php } ?>
						</td>
					</tr>
					<tr>
						<td align='right'><input type="submit" name="submit" id="submit" value="LOGIN" class="button" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<div class="note">
		<span class="best_view">
			Minimum resolusi layar monitor :<BR>
			1024 x 768<br /><BR>
			Minimum browser :<br />
			Internet Explorer 7.0, Mozilla Firefox 10.0<br /><br />
		</span>
	</div>
	
	<script type="text/javascript">
	$(document).ready(function() {
		$("#username").focus();
	});
	</script>
<? } ?>