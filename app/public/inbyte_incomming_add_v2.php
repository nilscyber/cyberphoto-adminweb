<style>
.inb-form-card {
    display: inline-block;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,.07);
    padding: 24px 28px 20px;
    margin-bottom: 20px;
    min-width: 420px;
}
.inb-form-card .form-title {
    font-size: 15px;
    font-weight: 700;
    color: #111;
    margin: 0 0 18px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e5e7eb;
}
.inb-form-row {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 12px;
}
.inb-form-row label {
    flex: 0 0 130px;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    padding-top: 6px;
}
.inb-form-row .field {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.inb-form-card input[type="text"] {
    font-size: 13px;
    font-family: Arial, sans-serif;
    padding: 5px 8px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    color: #111;
    background: #fff;
    width: 100%;
    box-sizing: border-box;
}
.inb-form-card input[type="text"]:focus {
    outline: none;
    border-color: #2dd4bf;
    box-shadow: 0 0 0 2px rgba(45,212,191,.18);
}
.inb-required {
    color: #dc2626;
    margin-left: 2px;
}
.inb-form-card .form-actions {
    margin-top: 18px;
    padding-top: 14px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 12px;
}
.inb-form-card .btn-submit {
    font-size: 13px;
    font-weight: 700;
    font-family: Arial, sans-serif;
    padding: 7px 20px;
    border: none;
    border-radius: 5px;
    background: #0d9488;
    color: #fff;
    cursor: pointer;
    transition: background .15s;
}
.inb-form-card .btn-submit:hover {
    background: #0f766e;
}
.inb-form-card .form-note {
    font-size: 11px;
    color: #6b7280;
}
</style>

<div class="inb-form-card">
    <div class="form-title">
        <?php if ($addID != ""): ?>Redigera post<?php else: ?>Lägg till post<?php endif; ?>
    </div>
    <form>
        <?php if ($addID != ""): ?>
            <input type="hidden" value="<?php echo $addID; ?>" name="addID">
            <input type="hidden" value="true" name="submC">
        <?php else: ?>
            <input type="hidden" value="true" name="subm">
            <input type="hidden" value="yes" name="add">
        <?php endif; ?>

        <?php if ($addID != ""): ?>
        <div class="inb-form-row">
            <label>Aktiv</label>
            <div class="field">
                <input type="checkbox" name="addActive" value="yes"<?php if ($addActive == "0" || $addActive == "yes") echo " checked"; ?>>
            </div>
        </div>
        <?php endif; ?>

        <div class="inb-form-row">
            <label>Antal<span class="inb-required">*</span></label>
            <div class="field">
                <input type="text" name="addNumber" value="<?php echo htmlspecialchars($addNumber); ?>">
            </div>
        </div>

        <div class="inb-form-row">
            <label>Varav köp<span class="inb-required">*</span></label>
            <div class="field">
                <input type="text" name="addNumberBuy" value="<?php echo htmlspecialchars($addNumberBuy); ?>">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" name="skicka" class="btn-submit">
                <?php if ($addID != ""): ?>Uppdatera<?php elseif ($addidc != ""): ?>Kopiera post<?php else: ?>Lägg till<?php endif; ?>
            </button>
            <span class="form-note"><span class="inb-required">*</span> Obligatoriskt</span>
        </div>
    </form>
</div>
