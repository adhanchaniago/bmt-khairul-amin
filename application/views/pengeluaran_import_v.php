<html>
<head>
  <title>IMPORT EXCEL CI 3</title>
</head>
<body>
  <h1>Data Siswa</h1><hr>
  <a href="<?php echo base_url("index.php/pengeluaran_kas/form"); ?>">Import Data</a><br><br>
  <table border="1" cellpadding="8">
  <tr>
    <th>NIS</th>
    <th>Nama</th>
    <th>Jenis Kelamin</th>
    <th>Alamat</th>
    <th>Alamat</th>
    <th>Alamat</th>
    <th>Alamat</th>
  </tr>
  <?php
  if( ! empty($tbl_trans_kas)){ // Jika data pada database tidak sama dengan empty (alias ada datanya)
    foreach($tbl_trans_kas as $data){ // Lakukan looping pada variabel siswa dari controller
      echo "<tr>";
      echo "<td>".$data->id."</td>";
      echo "<td>".$data->tgl_catat."</td>";
      echo "<td>".$data->keterangan."</td>";
      echo "<td>".$data->dari_kas_id."</td>";
      echo "<td>".$data->jns_trans."</td>";
      echo "<td>".$data->jumlah."</td>";
      echo "<td>".$data->user_name."</td>";
      echo "</tr>";
    }
  }else{ // Jika data tidak ada
    echo "<tr><td colspan='4'>Data tidak ada</td></tr>";
  }
  ?>
  </table>
</body>
</html>