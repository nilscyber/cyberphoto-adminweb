<?php
// Minimal bootstrap  ingen header/top/footer här
include_once("Db.php");
include_once("CStatistics.php"); // justera filnamn/inkludering efter hur din klass ligger

$statistics = new CStatistics();

// Bygg HTML-rapporten
$bodyInner = $statistics->getDailyReportByArticle(24);

$body  = "<html><head>";
$body .= "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
$body .= "<style>
            table.stat-table{border-collapse:collapse;width:100%;font-size:12px;}
            table.stat-table th,table.stat-table td{border:1px solid #ccc;padding:4px 6px;}
            table.stat-table th{background:#d1f2f0;font-weight:bold;text-align:left;}
          </style>";
$body .= "</head><body>";
$body .= "<h1>Daglig rapport  Missad försäljning</h1>";
$body .= $bodyInner;
$body .= "</body></html>";

// Mottagare  i testfasen du
$to = "stefan@cyberphoto.se, emil.lindberg@cyberphoto.se";

$rawSubject = "Daglig rapport - Missad försäljning (senaste 24h)";

// MIME-koda för mail-header
$subject = '=?UTF-8?B?' . base64_encode($rawSubject) . '?=';

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: no-reply@cyberphoto.se\r\n";

mail($to, $subject, $body, $headers);

