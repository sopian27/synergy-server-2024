<!DOCTYPE html>
<html>
<head>
    <title>Notifikasi IKP</title>
</head>
<body>
Halo, Terdeteksi pembuatan IKP dengan data dibawah ini: <br><br>
<?php 
function myprint_r($my_array) {
    if (is_array($my_array)) {
        echo "<table border=1 cellspacing=0 cellpadding=3 width=100%>";
        echo '<tr><td colspan=2 style="background-color:#333333;"><strong><font color=white>OBJECT</font></strong></td></tr>';
        foreach ($my_array as $k => $v) {
                echo '<tr><td valign="top" style="width:40px;background-color:#F0F0F0;">';
                echo '<strong>' . $k . "</strong></td><td>";
                myprint_r($v);
                echo "</td></tr>";
        }
        echo "</table>";
        return;
    }
    echo $my_array;
}
myprint_r($table); 
?>
</body>
</html>