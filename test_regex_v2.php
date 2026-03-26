<?php
$distrik_names = [
    "KIOS-D4-001",
    "KIOS-D4- 002",
    "KIOS-D4 – 003", // En-dash or em-dash
    "KIOS-D4",
    "KIOS A1-05",
    "KIOS-B2 - 010",
    "KIOS-D4-001 "
];

foreach ($distrik_names as $name) {
    $cleanName = trim($name);
    // Try the improved regex
    $cleanName = preg_replace('/[\s\p{Pd}]+\d+$/u', '', $cleanName);
    echo "Original: '$name' -> Cleaned: '$cleanName'\n";
}
