<?php
// RSS-widget – hämtar flöden, cachar 30 min, renderar HTML
// Anropas direkt (standalone) eller inkluderas från index.php

$RSS_FEEDS = [
    ['name' => 'DPReview',  'url' => 'https://www.dpreview.com/feeds/latest.xml'],
    ['name' => 'PetaPixel', 'url' => 'https://petapixel.com/feed/'],
];

$CACHE_DIR  = __DIR__ . '/cache/rss';
$CACHE_TTL  = 1800; // sekunder (30 min)
$ITEMS_MAX  = 12;   // max antal nyheter totalt

// ---- helpers ----

function rss_fetch_cached(string $url, string $cacheDir, int $ttl): ?string {
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }
    $file = $cacheDir . '/' . md5($url) . '.xml';
    if (file_exists($file) && (time() - filemtime($file)) < $ttl) {
        return file_get_contents($file);
    }
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (cyberphoto-admin rss-reader)',
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $body = curl_exec($ch);
    $err  = curl_error($ch);
    unset($ch);
    if ($body && !$err) {
        file_put_contents($file, $body);
        return $body;
    }
    // returnera gammal cache om hämtningen misslyckades
    return file_exists($file) ? file_get_contents($file) : null;
}

function rss_parse(string $xml): array {
    libxml_use_internal_errors(true);
    $feed = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    if (!$feed) return [];

    $items = [];

    // RSS 2.0
    if (isset($feed->channel->item)) {
        foreach ($feed->channel->item as $item) {
            $items[] = rss_normalize_item($item, 'rss2');
        }
    }
    // Atom
    elseif (isset($feed->entry)) {
        foreach ($feed->entry as $entry) {
            $items[] = rss_normalize_item($entry, 'atom');
        }
    }
    return $items;
}

function rss_normalize_item(SimpleXMLElement $item, string $type): array {
    $ns_media   = $item->children('media',   true) ?: [];
    $ns_content = $item->children('content', true) ?: [];

    if ($type === 'atom') {
        $link = '';
        foreach ($item->link as $l) {
            $rel = (string)($l['rel'] ?? 'alternate');
            if ($rel === 'alternate' || $rel === '') {
                $link = (string)$l['href'];
                break;
            }
        }
        $date = (string)($item->published ?? $item->updated ?? '');
        $desc = strip_tags((string)($item->summary ?? $item->content ?? ''));
    } else {
        $link = (string)($item->link ?? '');
        $date = (string)($item->pubDate ?? '');
        $desc = strip_tags((string)($item->description ?? ''));
    }

    // Thumbnail: försök media:thumbnail, sedan media:content, sedan enclosure
    $thumb = '';
    if (!empty($ns_media)) {
        foreach ($ns_media as $m) {
            $url = (string)($m['url'] ?? '');
            if ($url) { $thumb = $url; break; }
        }
    }
    if (!$thumb && !empty($item->enclosure)) {
        $type_enc = (string)($item->enclosure['type'] ?? '');
        if (strpos($type_enc, 'image') !== false) {
            $thumb = (string)($item->enclosure['url'] ?? '');
        }
    }
    // Sista utväg: leta <img> i description/content
    if (!$thumb) {
        $html = (string)(
            $ns_content[0] ?? $item->description ?? $item->content ?? ''
        );
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', $html, $m)) {
            $thumb = $m[1];
        }
    }

    // Normalisera datum till Unix-timestamp
    $ts = 0;
    if ($date) {
        $ts = @strtotime($date) ?: 0;
    }

    return [
        'title' => trim((string)($item->title ?? '')),
        'link'  => trim($link),
        'thumb' => trim($thumb),
        'desc'  => mb_substr(trim($desc), 0, 160),
        'ts'    => $ts,
    ];
}

// ---- hämta + samla ihop alla flöden ----

$allItems = [];

foreach ($RSS_FEEDS as $feed) {
    $xml = rss_fetch_cached($feed['url'], $CACHE_DIR, $CACHE_TTL);
    if (!$xml) continue;
    $parsed = rss_parse($xml);
    foreach ($parsed as $item) {
        $item['source'] = $feed['name'];
        $allItems[] = $item;
    }
}

// Sortera nyaste först
usort($allItems, fn($a, $b) => $b['ts'] - $a['ts']);
$allItems = array_slice($allItems, 0, $ITEMS_MAX);

// ---- render ----
$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
?>
<div class="rss-widget">
  <div class="rss-header">
    <span class="rss-icon">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="#f97316" aria-hidden="true">
        <circle cx="6.18" cy="17.82" r="2.18"/>
        <path d="M4 4.44v2.83c7.03 0 12.73 5.7 12.73 12.73h2.83c0-8.59-6.97-15.56-15.56-15.56zm0 5.66v2.83c3.9 0 7.07 3.17 7.07 7.07h2.83c0-5.47-4.43-9.9-9.9-9.9z"/>
      </svg>
    </span>
    Branschnyheter
  </div>

  <?php if (empty($allItems)): ?>
    <p class="rss-empty">Kunde inte hämta nyheter just nu.</p>
  <?php else: ?>
    <ul class="rss-list">
      <?php foreach ($allItems as $item): ?>
        <li class="rss-item">
          <?php if ($item['thumb']): ?>
            <a href="<?= $h($item['link']) ?>" target="_blank" rel="noopener" class="rss-thumb-link" tabindex="-1">
              <img class="rss-thumb" src="<?= $h($item['thumb']) ?>" alt="" loading="lazy">
            </a>
          <?php endif; ?>
          <div class="rss-body">
            <a class="rss-title" href="<?= $h($item['link']) ?>" target="_blank" rel="noopener">
              <?= $h($item['title']) ?>
            </a>
            <?php if ($item['desc']): ?>
              <p class="rss-desc"><?= $h($item['desc']) ?>…</p>
            <?php endif; ?>
            <div class="rss-meta">
              <span class="rss-source"><?= $h($item['source']) ?></span>
              <?php if ($item['ts']): ?>
                <span class="rss-date"><?= date('d M H:i', $item['ts']) ?></span>
              <?php endif; ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
