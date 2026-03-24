#!/usr/bin/perl

use DBI;
use CGI;
use CGI qw/escape unescape/;
use POSIX;

$mailprog = '/usr/sbin/sendmail';

$today="";

$today = strftime "%Y-%m-%d", localtime;

# Anslut till databasen 
my $dbh = DBI->connect("DBI:mysql:cyberphoto", "apache");  
die unless $dbh;

my $select ="SELECT psl.idPSL, CustNo, CustName, Address1, Address2, Zipcode, City, Cod_amount, 
psl.CodeFTY, prc.idPRC, prc.ParcelNo, Kund.email, Ordertabell.mail_kommentar, Ordertabell.email, Ordertabell.ordernr ";
$select .= "FROM psl, prc, Ordertabell, Kund WHERE psl.idPSL=prc.idPSL AND psl.CustNo=Ordertabell.ordernr ";
$select .= "AND Ordertabell.kundnr=Kund.kundnr ";
$select .= "AND Ordertabell.skickat='$today' AND codePSS <> 'D' AND (Kund.email<>'' || Ordertabell.email<>'') ";
my $cursor = $dbh->prepare( $select );

$cursor->execute;
my $idPSL;
my $CustNo;
my $CustName;
my $Address1;
my $Address2;
my $Zipcode;
my $City;
my $Cod_amount;
my $CodeFTY;
my $idPRC;
my $kollinr;
my $faktmail;
my $comment;
my $levmail;
my $ordernr;
my $levsatt = "";
$counter = "";
$counter2 = "";
$ordernrlast = "";

while (($idPSL, $CustNo, $CustName, $Address1, $Address2, $Zipcode, $City, $Cod_amount, $CodeFTY, $idPRC, $kollinr, $faktmail, $comment, $levmail, $ordernr ) = $cursor->fetchrow_array)
{

$counter++;

if ( $levmail =~ /^[_.0-9a-z\-A-Z]+@[0-9a-zA-Z][.0-9a-zA-Z\-]+[A-Za-z]{2,3}$/ ) {
	$email = $levmail;
	
}
elsif ( $faktmail =~ /^[_.0-9a-z\-A-Z]+@[0-9a-zA-Z][.0-9a-zA-Z\-]+[A-Za-z]{2,3}$/ ) {
	$email = $faktmail;	
}
#if ($levmail)
#{ $email = $levmail; }
#else
#{ $email = $faktmail; }

#if ( $email =~ /^[_.0-9a-z\-]+@[0-9a-z][.0-9a-z\-]+[a-z]{2,3}$/ ) {
#if ( $email =~ /^[_.0-9a-z\-A-Z]+@[0-9a-zA-Z][.0-9a-zA-Z\-]+[A-Za-z]{2,3}$/ ) {
if ($email) {
$counter2++;
# Open mailprogram
open(OUTFILE,"|$mailprog -t");
#print OUTFILE "To: nils\n";
print OUTFILE "To: $email\n";
print OUTFILE "From: order\@cyberphoto.se\n";
print OUTFILE "Subject: Beställning $CustNo\n\n";



if ($CodeFTY eq 'P15STE') {
  $levsatt='postens företagspaket'; 
}
elsif ($CodeFTY eq 'P25') {
  $levsatt='postens postpaket'; 
}
elsif ($CodeFTY eq 'ZBREVPF') {
  $levsatt='brev';
}
elsif ($CodeFTY eq 'P94') {
  $levsatt='företagspaket Utrikes';
}
elsif ($CodeFTY eq 'P22') {
  $levsatt='hempaket';
}

elsif ($CodeFTY eq 'P31STE') {
  $levsatt='företagspaket 0900';
}


  print OUTFILE "Hej! \n\nEr beställning skickas idag med $levsatt till:  \n";
  print OUTFILE "\n";
  print OUTFILE "$CustName\n";
  if ($Address1) 
    {  print OUTFILE "$Address1\n"; }
  print OUTFILE "$Address2\n$Zipcode $City \n\n";
  print OUTFILE "Beställningen har ordernummer: $CustNo\n\n";

  if ($Cod_amount > '0') {
   print OUTFILE "Paketet skickas mot postförskott. \nPostförskottsbeloppet är: ";
   printf OUTFILE ("%d kr", $Cod_amount);
   print OUTFILE "\n\n";
  }

  unless ($CodeFTY eq 'ZBREVPF')  {
    print OUTFILE <<eof;
Om du vill se var paketet finns just nu gå in på nedanstående länk: 
http://www.cyberphoto.se/?kollinr=$kollinr

Du kan även ringa 020-611 611 för att kontrollera var ditt paket 
befinner sig. Ange kollinummer: $kollinr
eof
    }

if ($comment)  {
print OUTFILE "\nÖvrigt:\n$comment\n";
}

print OUTFILE <<eof;

Om något är oklart går det bra att skicka ett mail
till order\@cyberphoto.se eller ringa 090-141141.


Med vänlig hälsning

CyberPhoto

eof
print <<eof;

Namn:     \t$CustName
Ordernummer:\t$CustNo
Email:    \t$email
Comment:  \t$comment
eof

print OUTFILE "\n";
close OUTFILE;
$email = Null
#$ordernrlast = Null;
}
}

$cursor->finish;

$dbh->disconnect;
print "Antal mail: $counter2\nAntal ordrar: $counter\n";

