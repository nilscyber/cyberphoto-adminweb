<?php
if ($addStoreValue == "" && $addArtnr != "") {
    $addStoreValue = $monitor->getStoreValue($addArtnr);
}
?>
<style>
.mon-form-card {
    display: inline-block;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,.07);
    padding: 24px 28px 20px;
    margin-bottom: 20px;
    min-width: 480px;
}
.mon-form-card .form-title {
    font-size: 15px;
    font-weight: 700;
    color: #111;
    margin: 0 0 18px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e5e7eb;
}
.mon-form-row {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 12px;
}
.mon-form-row label {
    flex: 0 0 130px;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    padding-top: 6px;
}
.mon-form-row .field {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.mon-form-row .hint {
    font-size: 11px;
    color: #6b7280;
}
.mon-form-card input[type="text"],
.mon-form-card select,
.mon-form-card textarea {
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
.mon-form-card input[type="text"]:focus,
.mon-form-card select:focus,
.mon-form-card textarea:focus {
    outline: none;
    border-color: #2dd4bf;
    box-shadow: 0 0 0 2px rgba(45,212,191,.18);
}
.mon-form-card textarea {
    resize: vertical;
    min-height: 64px;
}
.mon-required {
    color: #dc2626;
    margin-left: 2px;
}
.mon-form-card .form-actions {
    margin-top: 18px;
    padding-top: 14px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 12px;
}
.mon-form-card .btn-submit {
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
.mon-form-card .btn-submit:hover {
    background: #0f766e;
}
.mon-form-card .form-note {
    font-size: 11px;
    color: #6b7280;
}
</style>

<div class="mon-form-card">
    <div class="form-title">
        <?php if ($addID != ""): ?>Redigera bevakning<?php else: ?>Lägg till bevakning<?php endif; ?>
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
        <div class="mon-form-row">
            <label>Aktiv</label>
            <div class="field">
                <input type="checkbox" name="addActive" value="yes"<?php if ($addActive == "1" || $addActive == "yes") echo " checked"; ?>>
            </div>
        </div>
        <?php endif; ?>

        <div class="mon-form-row">
            <label>Artikel nr<span class="mon-required">*</span></label>
            <div class="field">
                <input type="text" name="addArtnr" value="<?php echo htmlspecialchars($addArtnr); ?>" style="width:160px">
            </div>
        </div>

        <div class="mon-form-row">
            <label>Bevaka<span class="mon-required">*</span></label>
            <div class="field">
                <select name="addType" style="width:160px">
                    <option value="0"<?php if ($addType == 0) echo " selected"; ?>>Lagersaldo</option>
                    <option value="3"<?php if ($addType == 3) echo " selected"; ?>>Order nr</option>
                </select>
            </div>
        </div>

        <div class="mon-form-row">
            <label>Uppfyll<span class="mon-required">*</span></label>
            <div class="field">
                <select name="addMoreLess" style="width:200px">
                    <option value="0"<?php if ($addMoreLess == 0) echo " selected"; ?>>Mindre än</option>
                    <option value="1"<?php if ($addMoreLess == 1) echo " selected"; ?>>Mer än</option>
                    <option value="2"<?php if ($addMoreLess == 2) echo " selected"; ?>>Alla ändringar</option>
                </select>
                <span class="hint">Spelar ingen roll om du bevakar en order</span>
            </div>
        </div>

        <div class="mon-form-row">
            <label>Värde<span class="mon-required">*</span></label>
            <div class="field">
                <input type="text" name="addStoreValue" value="<?php echo htmlspecialchars($addStoreValue); ?>" style="width:100px">
                <span class="hint">Endast heltal tillåtet</span>
            </div>
        </div>

        <div class="mon-form-row">
            <label>Skickas till</label>
            <div class="field">
                <select name="addRecipient" style="width:240px">
                    <option value=""></option>
                    <option value="ekonomi"<?php if ($addRecipient == "ekonomi") echo " selected"; ?>>OTRS ekonomikö</option>
                    <option value="inbyte"<?php if ($addRecipient == "inbyte") echo " selected"; ?>>OTRS inbyteskö</option>
                    <option value="kundtjanst"<?php if ($addRecipient == "kundtjanst") echo " selected"; ?>>OTRS kundtjänstkö</option>
                    <option value="produkt"<?php if ($addRecipient == "produkt") echo " selected"; ?>>OTRS säljkö</option>
                    <option value="service"<?php if ($addRecipient == "service") echo " selected"; ?>>OTRS servicekö</option>
                </select>
                <span class="hint">Lämna tomt för att skicka till din e-post</span>
            </div>
        </div>

        <div class="mon-form-row">
            <label>Egen notis</label>
            <div class="field">
                <textarea name="addComment" rows="3"><?php echo htmlspecialchars($addComment); ?></textarea>
                <span class="hint">Inkluderas i avisering om ifyllt</span>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" name="skicka" class="btn-submit">
                <?php if ($addID != ""): ?>Uppdatera<?php elseif ($addidc != ""): ?>Kopiera post<?php else: ?>Lägg till<?php endif; ?>
            </button>
            <span class="form-note"><span class="mon-required">*</span> Obligatoriska fält</span>
        </div>
    </form>
</div>
