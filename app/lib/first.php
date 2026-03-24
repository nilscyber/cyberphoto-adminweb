<% 
session_start(); 
@mysqli_connect(getenv('DB_HOST') ?: 'db', getenv('DB_USER') ?: 'appuser', getenv('DB_PASS') ?: 'apppass');
@mysqli_select_db("cyberphoto");
@mysqli_query("INSERT INTO track (link, sessid, ip) values ('$SCRIPT_FILENAME', '$COOKIE_ID', '$REMOTE_ADDR') ");
%>