<?php

$html = file_get_contents(__DIR__.'/../storage/logs/fb-embed-full.html');
$w = strpos($html, 'wire:id');
$d = strpos($html, 'fb-canvas-drop-zone');
$e = strpos($html, 'fb-embed-wrap');
echo "wire:id at {$w}\n";
echo "drop at {$d}\n";
echo "embed-wrap at {$e}\n";
echo "wire:id BEFORE drop: ".($w !== false && $d !== false && $w < $d ? 'YES' : 'NO')."\n";

if (preg_match('/<div[^>]*class="[^"]*fb-embed-wrap[^"]*"[^>]*>/', $html, $m)) {
    echo 'wrap opening: '.substr($m[0], 0, 400)."\n";
} elseif (preg_match('/<div[^>]*fb-embed-wrap[^>]*>/', $html, $m)) {
    echo 'wrap opening2: '.substr($m[0], 0, 400)."\n";
} else {
    echo "NO fb-embed-wrap opening tag found\n";
}

// Is drop inside an element that has wire:id? Check by finding the wire:id element and seeing if it contains drop.
if (preg_match('/<(div|style|section|main|article)([^>]*wire:id="([^"]+)"[^>]*)>/i', $html, $m, PREG_OFFSET_CAPTURE)) {
    $tag = $m[1][0];
    $attrs = $m[2][0];
    $id = $m[3][0];
    $start = $m[0][1];
    echo "First wire root: <{$tag} wire:id={$id}> at {$start}\n";
    echo "attrs snippet: ".substr($attrs, 0, 120)."\n";
}

echo str_contains($html, 'x-on:drop.prevent') ? "HAS alpine drop\n" : "NO alpine drop\n";
echo str_contains($html, 'fb-embed-wrap') ? "HAS fb-embed-wrap class\n" : "NO fb-embed-wrap\n";

// Count how many times wire:id appears on elements that also contain fb-canvas nearby
$between = substr($html, (int) $e, max(0, (int) $d - (int) $e));
echo "chars from embed-wrap to drop: ".strlen($between)."\n";
echo "wire:id in that range: ".(substr_count($between, 'wire:id'))."\n";
