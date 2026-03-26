<?php
$distrik_names = [
    "KIOS-D4-001",
    "KIOS-D4-002",
    "KIOS-D4",
    "KIOS A1-05",
    "KIOS-B2 - 010"
];

foreach ($distrik_names as $name) {
    $cleanName = trim($name);
    $cleanName = preg_replace('/\s*-\s*\d+$/', '', $cleanName);
    echo "Original: '$name' -> Cleaned: '$cleanName'\n";
}
