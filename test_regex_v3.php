<?php
$distrik_names = [
    "KIOS-D4-001",
    "KIOS-D4-002",
    "KIOS-LANTAI-061A",
    "KIOS-LANTAI-061B",
    "KIOS-LANTAI-062",
    "KIOS A1-05",
    "KIOS-B2 - 010",
    "KIOS-D4"
];

foreach ($distrik_names as $name) {
    $cleanName = trim($name);
    // Regex looking for separator followed by digits and optional letters at the end
    $cleanName = preg_replace('/[\s\p{Pd}]+\d+[a-zA-Z]*$/u', '', $cleanName);
    echo "Original: '$name' -> Cleaned: '$cleanName'\n";
}
