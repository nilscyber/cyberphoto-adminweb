<?php
include_once("top.php");

if (!CCheckIP::checkIfCanManagePermissions()) {
    exit;
}

$permissionsModel = new CPermissions();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['saveMatrix'])) {
        $checked = isset($_POST['perm']) ? $_POST['perm'] : array();
        $permissionsModel->saveMatrix($checked);
    } elseif (isset($_POST['addEmployee'])) {
        $permissionsModel->addEmployee($_POST['newEmployeeMail'], $_POST['newEmployeeName']);
    } elseif (isset($_POST['removeEmployee'])) {
        $permissionsModel->deactivateEmployee($_POST['removeEmployeeId']);
    } elseif (isset($_POST['updateEmployee'])) {
        $permissionsModel->updateEmployeeName($_POST['updateEmployeeId'], $_POST['updateEmployeeName']);
    } elseif (isset($_POST['addPermission'])) {
        $permissionsModel->addPermission($_POST['newPermissionKey'], $_POST['newPermissionDescription']);
    } elseif (isset($_POST['updatePermission'])) {
        $permissionsModel->updatePermissionDescription($_POST['updatePermissionKey'], $_POST['updatePermissionDescription']);
    } elseif (isset($_POST['deletePermission'])) {
        $permissionsModel->deletePermission($_POST['deletePermissionKey']);
    }

    header("Location: permissions_admin.php");
    exit;
}

include_once("header.php");

echo "<h1>Behörigheter</h1>";

$employees = $permissionsModel->getAllEmployees();
$permissions = $permissionsModel->getAllPermissions();
$matrix = $permissionsModel->getPermissionMatrix();

// Nycklar som idag faktiskt anropas från kod (CCheckIP::hasPermission - se app/lib/CCheckIpNumber.php).
// Underhålls manuellt: lägg till en nyckel här när du kopplar in en ny CCheckIP::hasPermission()-kontroll
// i koden, så adminsidan varnar innan den kan tas bort av misstag.
$keysUsedInCode = array(
    'trade_in', 'priority', 'purchase_valid', 'purchase_colleague',
    'product_permissions', 'manage_permissions', 'manage_cron_mail',
);

$h = function ($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES);
};
?>

<style>
  .perm-form-card {
    display: inline-block;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,.07);
    padding: 20px 24px 16px;
    margin: 0 16px 20px 0;
    vertical-align: top;
  }
  .perm-form-card .form-title {
    font-size: 15px;
    font-weight: 700;
    color: #111;
    margin: 0 0 14px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e5e7eb;
  }
  .perm-form-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
  }
  .perm-form-row label {
    flex: 0 0 130px;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
  }
  .perm-form-card input[type="text"] {
    font-size: 13px;
    font-family: Arial, sans-serif;
    padding: 5px 8px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    width: 220px;
  }
  .perm-form-card .btn-submit {
    font-size: 13px;
    font-weight: 700;
    font-family: Arial, sans-serif;
    padding: 7px 20px;
    border: none;
    border-radius: 5px;
    background: #0d9488;
    color: #fff;
    cursor: pointer;
  }
  .perm-form-card .btn-submit:hover { background: #0f766e; }
  .perm-matrix th, .perm-matrix td { text-align: center; }
  .perm-matrix td:first-child, .perm-matrix th:first-child { text-align: left; }
  .muted { color:#777; font-size:12px; }
</style>

<div class="perm-form-card">
  <div class="form-title">Lägg till anställd</div>
  <form method="post">
    <div class="perm-form-row">
      <label>Namn</label>
      <input type="text" name="newEmployeeName">
    </div>
    <div class="perm-form-row">
      <label>E-post</label>
      <input type="text" name="newEmployeeMail">
    </div>
    <button type="submit" name="addEmployee" value="1" class="btn-submit">Lägg till</button>
  </form>

  <?php if (!empty($employees)) { ?>
  <div style="margin-top:16px; padding-top:12px; border-top:1px solid #e5e7eb;">
    <?php foreach ($employees as $emp) { ?>
      <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
        <form method="post" style="display:flex; align-items:center; gap:8px; flex:1;">
          <input type="text" name="updateEmployeeName" value="<?php echo $h($emp['name']); ?>" style="width:160px;">
          <span class="muted"><?php echo $h($emp['login_mail']); ?></span>
          <input type="hidden" name="updateEmployeeId" value="<?php echo (int)$emp['employee_id']; ?>">
          <button type="submit" name="updateEmployee" value="1" class="btn-submit" style="padding:4px 10px;">Spara</button>
        </form>
        <form method="post" onsubmit="return confirm('Ta bort <?php echo $h($emp['name']); ?>? Personen försvinner från listan och tappar alla behörigheter.');">
          <input type="hidden" name="removeEmployeeId" value="<?php echo (int)$emp['employee_id']; ?>">
          <button type="submit" name="removeEmployee" value="1" class="btn-submit" style="background:#b91c1c; padding:4px 10px;">Ta bort</button>
        </form>
      </div>
    <?php } ?>
  </div>
  <?php } ?>
</div>

<div class="perm-form-card">
  <div class="form-title">Lägg till behörighet</div>
  <form method="post">
    <div class="perm-form-row">
      <label>Nyckel</label>
      <input type="text" name="newPermissionKey">
    </div>
    <div class="perm-form-row">
      <label>Beskrivning</label>
      <input type="text" name="newPermissionDescription">
    </div>
    <button type="submit" name="addPermission" value="1" class="btn-submit">Lägg till</button>
  </form>

  <?php if (!empty($permissions)) { ?>
  <div style="margin-top:16px; padding-top:12px; border-top:1px solid #e5e7eb;">
    <?php foreach ($permissions as $perm) {
        $usedInCode = in_array($perm['permission_key'], $keysUsedInCode, true);
        $confirmMsg = $usedInCode
            ? 'VARNING: "' . $perm['description'] . '" (' . $perm['permission_key'] . ') anropas idag från kod (CCheckIP). Tar du bort den slutar den funktionen fungera för ALLA, utan felmeddelande. Fortsätt?'
            : 'Ta bort behörigheten "' . $perm['description'] . '"? Alla anställdas kryss för den försvinner.';
    ?>
      <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
        <form method="post" style="display:flex; align-items:center; gap:8px; flex:1;">
          <input type="text" name="updatePermissionDescription" value="<?php echo $h($perm['description']); ?>" style="width:220px;">
          <span class="muted">(<?php echo $h($perm['permission_key']); ?>)</span>
          <?php if ($usedInCode) { ?><span style="color:#b91c1c; font-weight:700;" title="Anropas från CCheckIP i koden">⚠ används i kod</span><?php } ?>
          <input type="hidden" name="updatePermissionKey" value="<?php echo $h($perm['permission_key']); ?>">
          <button type="submit" name="updatePermission" value="1" class="btn-submit" style="padding:4px 10px;">Spara</button>
        </form>
        <form method="post" onsubmit="return confirm('<?php echo addslashes($confirmMsg); ?>');">
          <input type="hidden" name="deletePermissionKey" value="<?php echo $h($perm['permission_key']); ?>">
          <button type="submit" name="deletePermission" value="1" class="btn-submit" style="background:#b91c1c; padding:4px 10px;">Ta bort</button>
        </form>
      </div>
    <?php } ?>
  </div>
  <?php } ?>
</div>

<form method="post">
<table class="table-list perm-matrix" style="width:100%;">
  <thead>
    <tr>
      <th>Anställd</th>
      <?php foreach ($permissions as $perm) { ?>
        <th title="Nyckel: <?php echo $h($perm['permission_key']); ?>"><?php echo $h($perm['description']); ?></th>
      <?php } ?>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($employees)) { ?>
      <tr><td colspan="<?php echo 1 + count($permissions); ?>">Inga anställda tillagda.</td></tr>
    <?php } else { ?>
      <?php foreach ($employees as $emp) {
        $empId = (int)$emp['employee_id'];
      ?>
        <tr>
          <td><?php echo $h($emp['name']); ?> <span class="muted">(<?php echo $h($emp['login_mail']); ?>)</span></td>
          <?php foreach ($permissions as $perm) {
            $key = $perm['permission_key'];
            $isChecked = isset($matrix[$empId][$key]);
          ?>
            <td>
              <input type="checkbox" name="perm[<?php echo $empId; ?>][<?php echo $h($key); ?>]" value="1"<?php echo $isChecked ? ' checked' : ''; ?>>
            </td>
          <?php } ?>
        </tr>
      <?php } ?>
    <?php } ?>
  </tbody>
</table>
<div style="margin-top:14px;">
  <button type="submit" name="saveMatrix" value="1" class="btn-submit" style="font-size:13px;font-weight:700;font-family:Arial,sans-serif;padding:7px 20px;border:none;border-radius:5px;background:#0d9488;color:#fff;cursor:pointer;">Spara</button>
</div>
</form>

<div class="perm-form-card" style="display:block; max-width:800px;">
  <div class="form-title">Mall: koppla in en ny nyckel i koden</div>
  <p style="font-size:13px; color:#374151; margin-top:0;">
    Att lägga till en nyckel här på sidan gör den bara valbar - den styr inget
    förrän en utvecklare kopplar in kontrollen i koden. Fyll i det som står
    inom [hakparenteser] och skicka till Claude (eller en annan utvecklare)
    för att få den nya behörigheten att faktiskt gata något.
  </p>
  <textarea readonly rows="12" style="width:100%; box-sizing:border-box; font-family:Consolas,monospace; font-size:12px; padding:10px; border:1px solid #d1d5db; border-radius:4px;" onclick="this.select();">Lägg till en behörighetskontroll i cyberphoto-adminweb.

Nyckel: [t.ex. mina_nyckel]
Beskrivning (samma som i permissions_admin.php): [t.ex. "Se hemliga rapporten"]
Sida/funktion som ska gatas: [t.ex. app/public/hemlig_rapport.php]
Vad som ska hända om personen INTE har behörigheten: [t.ex. "exit direkt" / "dölj en tabell/sektion" / "dölj menylänken"]

Gör så här:
1. Lägg till "if (!CCheckIP::hasPermission('[nyckel]')) { ... }" på rätt ställe
   i filen ovan (mönster: se app/public/new_products.php där
   $canSeeUpcoming = CCheckIP::hasPermission('product_permissions') styr en tabell,
   eller app/public/permissions_admin.php som gör exit direkt om det saknas).
2. Om det ska synas i menyn: lägg till en gated <li> i app/public/menu.php,
   samma mönster som "Behörigheter"-länken.
3. Lägg till '[nyckel]' i $keysUsedInCode-arrayen i
   app/public/permissions_admin.php, så adminsidan varnar innan någon
   råkar ta bort nyckeln.
4. Kom ihåg att jag (eller IT-chefen) sedan måste kryssa i nyckeln för
   rätt personer på permissions_admin.php - den koden ger ingen åtkomst
   automatiskt.</textarea>
</div>

<?php
include_once("footer.php");
?>
