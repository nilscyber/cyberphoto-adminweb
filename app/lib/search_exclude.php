<?php
// Enkla listor  ändra fritt utan att röra klasskoden.
// Tomma listor = ingen effekt.

$EXCL_ARTICLES = array(
    // 'P18960', 'ABC123', ...
);

$EXCL_LOCATORS = array(
    1004170, 1004179, 1004216
);

// Beteende för locator-exkludering:
// true  = exkludera produkt om det finns >0 kvantitet på någon svartlistad plats
// false = exkludera produkt om den överhuvudtaget förekommer på platsen (oavsett kvantitet)
$EXCL_LOCATOR_REQUIRE_QTY = false;
