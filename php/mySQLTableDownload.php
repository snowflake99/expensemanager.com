<?php 
    session_start();

    include 'config.php';
    include 'opendb.php';
    
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
        $selmonth = $_POST['month'];
        $selyear = $_POST['year'];

        $sql="SELECT id FROM _users WHERE username='$_SESSION[username]'";
        $result=mysql_query($sql);
        
        while ($row = mysql_fetch_array($result)) {
            $usrId = $row{'id'};
        }

        $table = $usrId."_".$selmonth."_".$selyear."_";
 
        $count = 0;
        
        $sqlquery = "select * from $table";
        $result = mysql_query($sqlquery) or die(mysql_error());  
        $count = mysql_num_fields($result);

        for ($i = 0; $i < $count; $i++)	{
            $header .= mysql_field_name($result, $i)."\t";
        }
        
        while($row = mysql_fetch_row($result))	{
          $line = '';
          foreach($row as $value)	{
            if(!isset($value) || $value == "")	{
              $value = "\t";
            }	else  {
        # important to escape any quotes to preserve them in the data.
              $value = str_replace('"', '""', $value);
        # needed to encapsulate data in quotes because some data might be multi line.
        # the good news is that numbers remain numbers in Excel even though quoted.
              $value = '"' . $value . '"' . "\t";
            }
            $line .= $value;
          }
          $data .= trim($line)."\n";
        }
        # this line is needed because returns embedded in the data have "\r"
        # and this looks like a "box character" in Excel
          $data = str_replace("\r", "", $data);
        
        
        # Nice to let someone know that the search came up empty.
        # Otherwise only the column name headers will be output to Excel.
        if ($data == "") {
          $data = "\nno matching records found\n";
        }
        
        $count = mysql_num_fields($result);
        
        # This line will stream the file to the user rather than spray it across the screen
        header("Content-type: application/octet-stream");
        //header("Content-type: text/plain");
        
        # replace excelfile.xls with whatever you want the filename to default to
        header("Content-Disposition: attachment; filename=excelfile.xls");
        
        header("Pragma: no-cache");
        header("Expires: 0");
        
        //echo $header."\n".$data;
        echo $header."\n".$data."\n";
    }

    include 'closedb.php';

    die();
?>
