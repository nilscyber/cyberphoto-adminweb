<?php

// Funktion för att beräkna röda dagar i Sverige
function beraknaRodaDagar($ar) {
    $rodaDagar = [];

    // Nyårsdagen
    $rodaDagar[] = "$ar-01-01";

    // Trettondedag jul
    $rodaDagar[] = "$ar-01-06";

    // Första maj
    $rodaDagar[] = "$ar-05-01";

    // Sveriges nationaldag
    $rodaDagar[] = "$ar-06-06";

    // Julafton och juldagarna
    $rodaDagar[] = "$ar-12-24";
    $rodaDagar[] = "$ar-12-25";
    $rodaDagar[] = "$ar-12-26";

    // Nyårsafton
    $rodaDagar[] = "$ar-12-31";

    // Påsken (beräknas dynamiskt)
    $paskeSondag = (new DateTime())->setDate($ar, 3, 21)->modify('+' . (easter_days($ar)) . ' days');
    $rodaDagar[] = $paskeSondag->modify('-2 days')->format('Y-m-d'); // Långfredagen
    $rodaDagar[] = $paskeSondag->modify('+1 day')->format('Y-m-d'); // Annandag påsk
    $rodaDagar[] = $paskeSondag->format('Y-m-d'); // Påskdagen

    // Kristi himmelsfärdsdag
    $rodaDagar[] = $paskeSondag->modify('+39 days')->format('Y-m-d');

    // Pingst (pingstdagen)
    $rodaDagar[] = $paskeSondag->modify('+10 days')->format('Y-m-d');

    // Midsommarafton och midsommardagen (fredagen och lördagen i midsommarveckan)
    $midsommar = (new DateTime("June 20 $ar"))->modify('next Friday');
    $rodaDagar[] = $midsommar->format('Y-m-d'); // Midsommarafton
    $rodaDagar[] = $midsommar->modify('+1 day')->format('Y-m-d'); // Midsommardagen

    return $rodaDagar;
}

// Funktion för att beräkna procent av arbetstid
function procentArbetstid() {
    $nu = new DateTime();
    $ar = $nu->format('Y');
    $manadensStart = (new DateTime())->modify('first day of this month');
    $manadensSlut = (new DateTime())->modify('last day of this month');
    $rodaDagar = beraknaRodaDagar($ar);

    $arbetadeDagar = 0;
    $totalaArbetsdagar = 0;

    while ($manadensStart <= $manadensSlut) {
        if ($manadensStart->format('N') < 6 && !in_array($manadensStart->format('Y-m-d'), $rodaDagar)) {
            $totalaArbetsdagar++;
            if ($manadensStart < $nu) {
                $arbetadeDagar++;
            }
        }
        $manadensStart->modify('+1 day');
    }

    $arbetstimmarPerDag = 9;
    $dagensArbetstidStart = new DateTime($nu->format('Y-m-d') . ' 08:00:00');
    $dagensArbetstidSlut = new DateTime($nu->format('Y-m-d') . ' 17:00:00');

    if ($nu >= $dagensArbetstidStart && $nu <= $dagensArbetstidSlut) {
        $tidSomGåttIdag = $dagensArbetstidStart->diff($nu)->h + ($dagensArbetstidStart->diff($nu)->i / 60);
        $arbetadeDagar += $tidSomGåttIdag / $arbetstimmarPerDag;
    } else if ($nu > $dagensArbetstidSlut) {
        $arbetadeDagar++;
    }

    $arbetadProcent = 100 * $arbetadeDagar / $totalaArbetsdagar;
    $kvarvarandeProcent = 100 - $arbetadProcent;

    return [
        'arbetad' => round($arbetadProcent, 2),
        'kvarvarande' => round($kvarvarandeProcent, 2),
    ];
}

// Hämta resultat
$resultat = procentArbetstid();
$arbetadProcent = $resultat['arbetad'];
$kvarvarandeProcent = $resultat['kvarvarande'];
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arbetstid</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .progress-bar {
            width: 100%;
            height: 30px;
            background-color: #f3f3f3;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .progress {
            height: 100%;
            display: flex;
        }
        .progress-done {
            width: <?= $arbetadProcent ?>%;
            background-color: #4caf50;
            text-align: center;
            color: white;
            font-weight: bold;
            transition: width 0.5s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .progress-left {
            width: <?= $kvarvarandeProcent ?>%;
            background-color: #ff9800;
            text-align: center;
            color: white;
            font-weight: bold;
            transition: width 0.5s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .info {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Arbetstid för månaden</h1>
    <div class="progress-bar">
        <div class="progress">
            <div class="progress-done"><?= $arbetadProcent ?>%</div>
            <div class="progress-left"><?= $kvarvarandeProcent ?>%</div>
        </div>
    </div>
    <div class="info">
        <p><strong>Arbetad tid:</strong> <?= $arbetadProcent ?>%</p>
        <p><strong>Kvarvarande tid:</strong> <?= $kvarvarandeProcent ?>%</p>
    </div>
</body>
</html>
