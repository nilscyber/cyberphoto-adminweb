<select class="select-modern select-modern--section" name="choose_section" onchange="this.form.submit()">
    <option value="0">-- Välj sektion --</option>
    <?php $banners->getActiveSections($_SESSION['bannerdepartment']); ?>
</select>
