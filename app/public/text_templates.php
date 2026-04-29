<?php
include_once("top.php");
include_once("header.php");
echo "<h1>Textmallar att använda</h1>";
?>

<style>
/* === Textmallar-sida === */
.tm-wrapper {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 860px;
    margin: 20px 0 40px 0;
    padding: 0 16px;
}

.tm-section {
    background: #fffff0;
    border: 1px solid #e0d890;
    border-radius: 6px;
    padding: 18px 22px;
    margin-bottom: 20px;
}

.tm-section h2 {
    font-size: 14px;
    font-weight: 700;
    color: #333;
    margin: 0 0 10px 0;
    padding-bottom: 6px;
    border-bottom: 2px solid #d4c84a;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Klickbara rader */
.tm-row {
    display: block;
    padding: 7px 10px;
    margin: 3px -10px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 13.5px;
    color: #222;
    line-height: 1.5;
    transition: background 0.12s, color 0.12s;
    position: relative;
    user-select: none;
}

.tm-row:hover {
    background: #4a90d9;
    color: #fff;
}

.tm-row:hover::after {
    content: "?? Kopierat!";
    display: none; /* visas via JS */
}

/* Flash-effekt vid kopiering */
.tm-row.copied {
    background: #27ae60 !important;
    color: #fff !important;
}

.tm-row.copied::after {
    content: "? Kopierat!";
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    font-weight: 600;
    opacity: 0.9;
}

/* Info-toast */
#tm-toast {
    position: fixed;
    bottom: 28px;
    right: 28px;
    background: #27ae60;
    color: #fff;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    box-shadow: 0 4px 16px rgba(0,0,0,0.2);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s;
    z-index: 9999;
}

#tm-toast.show {
    opacity: 1;
}

/* Tips-text längst upp */
.tm-tip {
    background: #e8f4fd;
    border-left: 4px solid #4a90d9;
    padding: 10px 14px;
    border-radius: 0 4px 4px 0;
    font-size: 13px;
    color: #444;
    margin-bottom: 22px;
}

/* Notering/varning */
.tm-note {
    font-style: italic;
    color: #555;
}

.tm-avoid {
    font-weight: 700;
    font-style: italic;
    color: #c0392b;
}
</style>

<div class="tm-wrapper">

    <div class="tm-tip">
        Klicka på valfri rad för att kopiera texten till urklipp - klistra sedan in direkt på produkten.
    </div>

    <!-- SKICK MED ORIGINALKARTONG -->
    <div class="tm-section">
        <h2>Skick - Med originalkartong</h2>
        <span class="tm-row" onclick="copyText(this)">Begagnat ex i superskick (5/5). 6 månaders garanti!</span>
        <span class="tm-row" onclick="copyText(this)">Begagnat ex i mycket fint skick (4/5). 6 månaders garanti!</span>
        <span class="tm-row" onclick="copyText(this)">Begagnat ex i fint skick (3/5). 6 månaders garanti!</span>
        <span class="tm-row" onclick="copyText(this)">Begagnat ex i bra skick (2/5). 6 månaders garanti!</span>
        <span class="tm-row" onclick="copyText(this)">Begagnat ex i bruksskick (1/5). 6 månaders garanti!</span>
    </div>

    <!-- SKICK UTAN ORIGINALKARTONG -->
    <div class="tm-section">
        <h2>Skick - Saknar originalkartong</h2>
        <span class="tm-row" onclick="copyText(this)">Begagnat ex i superskick (5/5), saknar originalkartong. 6 månaders garanti!</span>
        <span class="tm-row" onclick="copyText(this)">Begagnat ex i mycket fint skick (4/5), saknar originalkartong. 6 månaders garanti!</span>
        <span class="tm-row" onclick="copyText(this)">Begagnat ex i fint skick (3/5), saknar originalkartong. 6 månaders garanti!</span>
        <span class="tm-row" onclick="copyText(this)">Begagnat ex i bra skick (2/5, saknar originalkartong). 6 månaders garanti!</span>
        <span class="tm-row" onclick="copyText(this)">Begagnat ex i bruksskick (1/5), saknar originalkartong. 6 månaders garanti!</span>
    </div>

    <!-- ANTAL EXPONERINGAR -->
    <div class="tm-section">
        <h2>Antal exponeringar</h2>
        <span class="tm-row" onclick="copyText(this)">4 600</span>
        <span class="tm-row" onclick="copyText(this)">går ej utläsa</span>
    </div>

    <!-- TILLBEHÖR -->
    <div class="tm-section">
        <h2>Tillbehör skrivs</h2>
        <span class="tm-row" onclick="copyText(this)">Batteri, laddare, rem</span>
        <span class="tm-row" onclick="copyText(this)">Batteri 2 st, laddare, rem</span>
        <span class="tm-row" onclick="copyText(this)">Batteri, laddare (ej original), rem</span>
        <span class="tm-row" onclick="copyText(this)">Batteri, laddare/nätdel, USB-kabel, rem</span>
        <br>
        <span class="tm-row" onclick="copyText(this)">Lock fram/bak, motljusskydd</span>
        <span class="tm-row" onclick="copyText(this)">Lock fram/bak, motljusskydd, mjuk väska</span>
        <span class="tm-row" onclick="copyText(this)">Lock fram/bak, motljusskydd, stativfot, mjuk väska</span>
        <span class="tm-row" onclick="copyText(this)">Lock fram/bak, motljusskydd, hård väska</span>
    </div>

    <!-- NOTERING -->
    <div class="tm-section">
        <h2>Notering</h2>
        <span class="tm-row tm-note" onclick="copyText(this)">Ett par små repor på LCD, påverkar ej bild eller funktion</span>
        <br>
        <p class="tm-avoid">Undvik alltid texten &quot;komplett i kartong&quot;</p>
    </div>

</div>

<!-- Toast-notis -->
<div id="tm-toast">Kopierat till urklipp!</div>

<script>
// Kopiera text och visa feedback
function copyText(el) {
    var text = el.innerText.trim();

    // Moderna webbläsare
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            showCopied(el);
        });
    } else {
        // Fallback för äldre webbläsare / HTTP
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        try {
            document.execCommand('copy');
            showCopied(el);
        } catch(e) {
            alert('Kunde ej kopiera: ' + text);
        }
        document.body.removeChild(ta);
    }
}

function showCopied(el) {
    // Flash på raden
    el.classList.add('copied');
    setTimeout(function() {
        el.classList.remove('copied');
    }, 1200);

    // Toast nere till höger
    var toast = document.getElementById('tm-toast');
    toast.classList.add('show');
    clearTimeout(toast._t);
    toast._t = setTimeout(function() {
        toast.classList.remove('show');
    }, 1800);
}
</script>

<?php
include_once("footer.php");
?>
