<?php
$departmentsBySite = [
    1 => [1   => 'Foto-video'],
    2 => [101 => 'Foto-video', 102 => 'Mobiltelefoni', 103 => 'Batterier', 104 => 'Outdoor'],
    3 => [201 => 'Foto-video', 202 => 'Mobiltelefoni', 203 => 'Batterier', 204 => 'Outdoor'],
    4 => [301 => 'Foto-video', 302 => 'Mobiltelefoni', 303 => 'Batterier', 304 => 'Outdoor'],
];

$site = (int)$_SESSION['bannersite'];
$departments = $departmentsBySite[$site] ?? [];
if (empty($departments)) return;
?>
<select class="select-modern" name="choose_department" onchange="this.form.submit()">
    <option value="0">-- Välj avdelning --</option>
    <?php foreach ($departments as $id => $label): ?>
        <option value="<?= $id ?>" <?= $_SESSION['bannerdepartment'] == $id ? 'selected' : '' ?>>
            <?= htmlspecialchars($label) ?>
        </option>
    <?php endforeach; ?>
</select>
