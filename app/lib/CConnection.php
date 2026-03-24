<?php

# PHP class

class CConnection {
  var $Host = "127.0.0.1:/ssd/var/lib/mysql/mysql.sock";
  var $User = "apache";
  var $Password = "";
  var $Database = "cyberphoto";

  var $Link_ID  = 0;  # Result of mysqli_connect().
  var $Query_ID = 0;  # Result of most recent mysqli_query().
  var $Record   = array();  # current mysqli_fetch_array()-result.
  var $Row;           # current row number.

  var $Errno    = 0;  # error state of query...
  var $Error    = "";

  # insert functions here.

  function halt($msg)
 {
    printf("</td></tr></table><b>Database error:</b> %s<br>\n", $msg);
    printf("<b>MySQL Error</b>: %s (%s)<br>\n",
      $this->Errno,
      $this->Error);
    die("Session halted.");
  }

  function connect()
 {
    if ( 0 == $this->Link_ID )
      {
	$this->Link_ID = Db::getConnection();
	if (!$this->Link_ID)
	  {
	    $this->halt("Link-ID == false, connect failed");
	  }
	if (!mysqli_query($this->Link_ID, sprintf("use %s",$this->Database)))
	  {
	    $this->halt("cannot use database ".$this->Database);
	  }
      }
  }

  function query($Query_String) {
    $this->connect();

#   printf("Debug: query = %s<br>n", $Query_String);

    $this->Query_ID = mysqli_query($this->Link_ID, $Query_String);
    $this->Row   = 0;
    $this->Errno = mysqli_errno();
    $this->Error = mysqli_error();
    if (!$this->Query_ID) {
      //$this->halt("Invalid SQL: ".$Query_String);
      die();
    }

    return $this->Query_ID;
  }

  function next_record() {
    $this->Record = mysqli_fetch_array($this->Query_ID);
    $this->Row   += 1;
    $this->Errno = mysqli_errno();
    $this->Error = mysqli_error();

    $stat = is_array($this->Record);
    if (!$stat) {
      mysqli_free_result($this->Query_ID);
      $this->Query_ID = 0;
    }
    return $stat;
  }

  function seek($pos) {
    $status = mysqli_data_seek($this->Query_ID, $pos);
    if ($status)
      $this->Row = $pos;
    return;
  }

}

?>
