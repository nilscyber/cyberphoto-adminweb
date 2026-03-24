<?php
// /admin/ajax/drawer_details.php
header('Content-Type: text/html; charset=UTF-8');

include_once(__DIR__ . '/../Db.php');

$type = isset($_GET['type']) ? $_GET['type'] : '';
$id   = isset($_GET['id'])   ? trim($_GET['id']) : '';
$h    = function($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); };

if ($type === '' || $id === '') { echo '<p>Ogiltig förfrågan.</p>'; exit; }

try {
    switch ($type) {

        case 'product':
            $pdo = Db::getConnection(false); // Läsning MariaDB
            $sql = "SELECT p.article, p.product_name, p.price, p.stock,
                           m.name AS manufacturer
                      FROM products p
                 LEFT JOIN xc_manufacturer m ON m.manufacturerid = p.manufacturerid
                     WHERE p.article = :id
                     LIMIT 1";
            $st = $pdo->prepare($sql);
            $st->execute(array(':id'=>$id));
            $row = $st->fetch(PDO::FETCH_ASSOC);

            if (!$row) { echo '<p>Produkten hittades inte.</p>'; exit; }

            echo '<div style="font-weight:bold;font-size:16px;margin-bottom:6px;">'.$h($row['product_name']).'</div>';
            echo '<div><strong>Artikel:</strong> '.$h($row['article']).'</div>';
            echo '<div><strong>Tillverkare:</strong> '.$h($row['manufacturer']).'</div>';
            echo '<div><strong>Pris:</strong> '.number_format((float)$row['price'], 2, ',', ' ').' kr</div>';
            echo '<div><strong>Lager:</strong> '.$h($row['stock']).'</div>';
            echo '<div style="margin-top:10px;"><a target="_blank" rel="noopener" href="/admin/product_info.php?article='.$h($row['article']).'">Öppna full vy »</a></div>';
            break;

        case 'order':
            $pdo = Db::getConnection(false);
            $sql = "SELECT o.order_no, o.date_created, o.total, o.status, o.customer_id
                      FROM orders o
                     WHERE o.order_no = :id
                     LIMIT 1";
            $st = $pdo->prepare($sql);
            $st->execute(array(':id'=>(int)$id));
            $o = $st->fetch(PDO::FETCH_ASSOC);

            if (!$o) { echo '<p>Ordern hittades inte.</p>'; exit; }

            echo '<div style="font-weight:bold;font-size:16px;margin-bottom:6px;">Order '.$h($o['order_no']).'</div>';
            echo '<div><strong>Status:</strong> '.$h($o['status']).'</div>';
            echo '<div><strong>Datum:</strong> '.$h($o['date_created']).'</div>';
            echo '<div><strong>Belopp:</strong> '.number_format((float)$o['total'], 2, ',', ' ').' kr</div>';
            echo '<div><strong>Kund:</strong> '.$h($o['customer_id']).'</div>';
            echo '<div style="margin-top:10px;"><a target="_blank" rel="noopener" href="/admin/order_info.php?order='.$h($o['order_no']).'">Öppna full vy »</a></div>';
            break;

        case 'customer':
            $pdo = Db::getConnection(false);
            $sql = "SELECT c.id, c.name, c.email, c.phone, c.created_at
                      FROM customers c
                     WHERE c.id = :id
                     LIMIT 1";
            $st = $pdo->prepare($sql);
            $st->execute(array(':id'=>(int)$id));
            $c = $st->fetch(PDO::FETCH_ASSOC);

            if (!$c) { echo '<p>Kunden hittades inte.</p>'; exit; }

            echo '<div style="font-weight:bold;font-size:16px;margin-bottom:6px;">'.$h($c['name']).'</div>';
            echo '<div><strong>Kundnr:</strong> '.$h($c['id']).'</div>';
            echo '<div><strong>E-post:</strong> '.$h($c['email']).'</div>';
            echo '<div><strong>Telefon:</strong> '.$h($c['phone']).'</div>';
            echo '<div><strong>Registrerad:</strong> '.$h($c['created_at']).'</div>';
            echo '<div style="margin-top:10px;"><a target="_blank" rel="noopener" href="/admin/customer_info.php?customer_id='.$h($c['id']).'">Öppna full vy »</a></div>';
            break;

        default:
            echo '<p>Okänd typ.</p>';
    }
} catch (Exception $e) {
    echo '<p>Fel vid hämtning av data.</p>';
}
