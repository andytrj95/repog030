<style type="text/css">
td {align:center}
td {valign:top}
td {color:#336699}
td {wrap:nowrap}
.jdl {background-color: #FF9999}
.jdl {font-family: Times New Roman; font-size: 10pt}
.dtl {background-color: #FFFFCC}
.dtl {font-family: Times New Roman; font-size: 8pt}
.fot {background-color: #FF9999}
.fot {font-family: Times New Roman; font-size: 10pt}
</style>
<br/>
<h3 class="mt-2">Cek Saldo Awal & Akhir</h3>
<ol class="breadcrumb mb-4">
<li class="breadcrumb-item active">beranda/cabang/ceksaldo</li>
</ol>

			<form action="" method="post" name="postform">
			  <p><strong>Note:</strong> type="date" tidak didukung di Browser Safari atau Internet Explorer 11 (atau Versi lama).</p>	
			  <label for="birthday">Periode:</label>
			  <input type="date" id="birthday" name="tanggal_awal">
			  <input type="submit" class="btn btn-info btn-sm" name="pencarian" role="button" value="submit" />	
			</form>	

        
		<?php
			include ('/../concbngmks.php');
			if(isset($_POST['pencarian'])){
			$tanggal_awal=$_POST['tanggal_awal'];
			$tahunn=date("ym", strtotime("-1 months"));
			//print_r($tahunn);
			//die('');
			$tahun=substr($tanggal_awal,2,2);
			$bln=substr($tanggal_awal,5,2);
			$hr=substr($tanggal_awal,8,2);	
			
			$region5="st_".$tahun.$bln.$hr;
			$region6="PR_".$tahun.$bln.$hr;
			$jefta="kodetoko_".$tahunn;
			
			$reg="$tahun$bln$hr";
			
			

	$qrystrk = "SELECT COUNT(KODETOKO) AS TOKO FROM MSTR_TOKO_TODAY WHERE tglbuka < CURDATE()-1 AND KODETOKO NOT IN('TLRD','T0L1')";
	
	$resultk = mysql_query($qrystrk);

	if (!$resultk) {
	    die('Invalid query1: ' . mysql_error());
	 }
	 $num_rows = mysql_num_rows($resultk);

	if ($num_rows==0) {
	    die('Coba Lihat');
	 }
	$row = mysql_fetch_row($resultk);
	$toko = $row[0];
	mysql_free_result($resultk);
	if(empty($tanggal_awal)){

			?>
						
			<script language="JavaScript">
				alert('Tanggal Awal dan Tanggal Akhir Harap di Isi!');
				document.location='ceksaldo.php';
			</script>
			<?php
			}else{
			?>
			
		<table border=0 cellpadding=3 cellspacing=0 width="700">
		<tr>
		<td><font size="4pt">Cek Saldo Awal Toko</td>
		</tr>
		<tr><td><B>Cabang Makassar dan Kendari</B></td></tr>
		<tr><td>Periode :
		<?php 
		echo "".$tanggal_awal."";
		?>
		
		<tr><td>Jumlah Toko Cab. Makassar dan Kendari:
		<?php 
		echo "".$toko."";
		?>
		<tr>
		<?php
		$qry ="<td align=LEFT nowrap><span class=style1><a href=\"./exic.php?tanggal_awal=" . @$_POST['tanggal_awal']."\"  target=\"_blank\">Export Ke Excel</a></td>";
		echo $qry;
?>
			<?php
			$query1=mysql_query("DROP TABLE ceksaldo");
			$query2=mysql_query("DROP TABLE ST_CEK");
			$query3=mysql_query("CREATE TABLE `CEKSALDO` (
								`KDTK` VARCHAR (300),
								`NAMA` VARCHAR (300),
								`SALDO_AKH` DOUBLE ,
								`RP_SLD_AKH` DOUBLE ,
								`SALDO_AWAL` DOUBLE ,
								`RP_SLD_AWL` DOUBLE ,
								`SLSQTY` DOUBLE ,
								`SLSRP` DOUBLE 
								)");
			$query3=mysql_query("INSERT INTO CEKSALDO   SELECT KODE_TOKO AS KDTK,NULL,SUM(SALDO_AKH)AS SALDO_AKH,SUM(RP_SLD_AKH) AS RP_SLD_AKH,NULL,NULL,NULL,NULL FROM " . $jefta . " GROUP BY kode_toko");
			$query4=mysql_query("CREATE TABLE ST_CEK SELECT KODE_TOKO,SUM(BEGBAL) AS SALDO_AWAL,SUM(BEGBAL*LCOST) AS RP_SLD_AWL FROM " . $region5 . " GROUP BY KODE_TOKO");			
			$query5=mysql_query("UPDATE CEKSALDO A,ST_CEK B SET A.SALDO_AWAL=B.SALDO_AWAL, A.RP_SLD_AWL=B.RP_SLD_AWL  WHERE A.KDTK=B.KODE_TOKO");			
			$query6=mysql_query("UPDATE CEKSALDO SET slsqty=saldo_akh-saldo_awal,slsrp=rp_sld_akh-rp_sld_awl");			
			$query8=mysql_query("UPDATE CEKSALDO A,MSTR_TOKO_TODAY B SET A.NAMA=B.NAMATOKO WHERE A.KDTK=B.KODETOKO");			
			$query9=mysql_query("DROP TABLE ST_CEK");			
			$query10=mysql_query("SELECT * FROM CEKSALDO");			
		}	

		?>
		
		</p>
			<table width="600" cellpadding="2" cellspacing="0" border="1">
			<tr>
			
			
			<td class="jdl" align="LEFT" rowspan="1"  nowrap>NO</td>
			<td class="jdl" align="LEFT" rowspan="1"  nowrap>KODE TOKO</td>
			<td class="jdl" align="center" rowspan="1"  nowrap>NAMA TOKO</d>
			<td class="jdl" align="center" rowspan="1"  nowrap>QTY SALDO AKHIR</td>
			<td class="jdl" align="center" rowspan="1"  nowrap>RUPIAH SALDO AKHIR</td>
			<td class="jdl" align="center" rowspan="1"  nowrap>QTY SALDO AWAL</td>
			<td class="jdl" align="center" rowspan="1"  nowrap>RUPIAH SALDO AWAL</td>
			<td class="jdl" align="center" rowspan="1"  nowrap>SELISIH QTY</td>
			<td class="jdl" align="center" rowspan="1"  nowrap>SELISIH RUPIAH</td>
			<?php
			$nomor=1;
			while($row=mysql_fetch_array($query10)){
			$harga=number_format($row['SLSRP'],0,",",".")
			?>
			<tr>
				<td class=dtl align= nowrap><?php echo $nomor;?></td>
				<td class=dtl align=LEFT nowrap><?php echo $row['KDTK'];?></td>
				<td class=dtl align=left nowrap><?php echo $row['NAMA'];?></td>
				<td class=dtl align=right nowrap><?php echo number_format($row['SALDO_AKH']);?></td>
				<td class=dtl align=right nowrap><?php echo number_format($row['RP_SLD_AKH']);?></td>
				<td class=dtl align=right nowrap><?php echo number_format($row['SALDO_AWAL']);?></td>	
				<td class=dtl align=right nowrap><?php echo number_format($row['RP_SLD_AWL']);?></td>	
				<td class=dtl align=right nowrap><?php echo number_format($row['SLSQTY']);?></td>	
				<td class=dtl align=right nowrap><?php echo number_format($row['SLSRP']);?></td>	
				
				
			</tr>
			
			<?php
			$nomor++;
			}
			?>    
			<tr>
					<?php
				//jika pencarian data tidak ditemukan
				if(mysql_num_rows($query10)==0){
					echo "<font color=red><blink>Pencarian data tidak ditemukan!</blink></font>";
				}
				?>
				</td>
			</tr> 
		
		<?php
		}
		else{
			unset($_POST['pencarian']);
		}
		?>
			


			</td>
			</tr> 
		</table>
		<iframe width=174 height=189 name="gToday:normal:calender/normal.js" id="gToday:normal:calender/normal.js" src="calender/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;"></iframe>
	</body>
</html>