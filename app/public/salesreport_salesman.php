<?php 
include_once("top.php");
include_once("header.php");

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
