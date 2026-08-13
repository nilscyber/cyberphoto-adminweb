<?php
include_once("top.php");
include_once("header.php");

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";

// Initiera $history om inte redan satt
$history = isset($_GET['history']) ? $_GET['history'] : 'day';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

echo "<h1>Försäljningsrapport säljare</h1>";
?>

<form method="GET">
    <div style="float: left; margin-right: 20px;">
        <label for="history">Välj period:</label><br>
        <select name="history" id="history" onchange="toggleDateFields(this.value); this.form.submit();">
            <?php
            $options = [
                'day' => 'Idag',
                'this_week' => 'Denna vecka',
                'last_week' => 'Föregående vecka',
                'this_month' => 'Denna månad',
                'last_month' => 'Föregående månad',
                'custom' => 'Eget intervall',
            ];
            foreach ($options as $value => $label) {
                $selected = ($history === $value) ? 'selected' : '';
                echo "<option value=\"$value\" $selected>$label</option>\n";
            }
            ?>
        </select>
    </div>

    <div id="custom_dates" style="float: left; display: none;">
        <label for="date_from">Från:</label><br>
        <input type="date" name="date_from" id="date_from" value="<?php echo htmlspecialchars($date_from); ?>" onchange="this.form.submit();"><br>
        <label for="date_to">Till:</label><br>
        <input type="date" name="date_to" id="date_to" value="<?php echo htmlspecialchars($date_to); ?>" onchange="this.form.submit();">
    </div>

    <div style="clear: both;"></div>
</form>

<script>
function toggleDateFields(value) {
    const customBox = document.getElementById('custom_dates');
    customBox.style.display = (value === 'custom') ? 'block' : 'none';
}

// Kör direkt när sidan laddas
document.addEventListener('DOMContentLoaded', function() {
    toggleDateFields(document.getElementById('history').value);
});
</script>



<?php
$sales->displaySalesPerUser();

echo '<hr style="border: 1px solid #C0C0C0;">';

include_once("footer.php");
?>

<div id="seller_modal_backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:1000;">
    <div id="seller_modal" style="background:#fff; max-width:700px; width:90%; margin:5% auto; border-radius:8px; box-shadow:0 4px 20px rgba(0,0,0,0.3); max-height:80vh; display:flex; flex-direction:column;">
        <div style="display:flex; justify-content:space-between; align-items:center; padding:15px 20px; border-bottom:1px solid #e5e7eb;">
            <h3 id="seller_modal_title" style="margin:0;">Produkter</h3>
            <button type="button" id="seller_modal_close" aria-label="Stäng" style="background:none; border:none; font-size:22px; line-height:1; cursor:pointer; color:#555;">&times;</button>
        </div>
        <div id="seller_modal_body" style="padding:15px 20px; overflow-y:auto;">
            <p class="italic">Laddar...</p>
        </div>
    </div>
</div>

<script>
(function () {
    var backdrop = document.getElementById('seller_modal_backdrop');
    var modalBody = document.getElementById('seller_modal_body');
    var modalTitle = document.getElementById('seller_modal_title');

    function openModal(seller, name, start, end) {
        modalTitle.textContent = 'Produkter – ' + name;
        modalBody.innerHTML = '<p class="italic">Laddar...</p>';
        backdrop.style.display = 'block';

        var url = 'ajax/salesman_products.php?seller=' + encodeURIComponent(seller)
            + '&start=' + encodeURIComponent(start)
            + '&end=' + encodeURIComponent(end);

        fetch(url)
            .then(function (res) { return res.text(); })
            .then(function (html) { modalBody.innerHTML = html; })
            .catch(function () { modalBody.innerHTML = '<p class="italic">Kunde inte hämta produkter.</p>'; });
    }

    function closeModal() {
        backdrop.style.display = 'none';
    }

    document.addEventListener('click', function (e) {
        var row = e.target.closest ? e.target.closest('tr[data-seller]') : null;
        if (row) {
            openModal(row.getAttribute('data-seller'), row.getAttribute('data-name'), row.getAttribute('data-start'), row.getAttribute('data-end'));
            return;
        }
        if (e.target === backdrop || e.target.id === 'seller_modal_close') {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && backdrop.style.display === 'block') {
            closeModal();
        }
    });
})();
</script>
