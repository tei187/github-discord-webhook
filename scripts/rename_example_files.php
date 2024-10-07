<?php

// Parse command line arguments
$options = getopt("d:n", ["directory:", "dry-run"]);

// Set default directory to config folder in project root
$directory = $options['d'] ?? $options['directory'] ?? __DIR__ . '/../config';
$dryRun = isset($options['n']) || isset($options['dry-run']);

if (!is_dir($directory)) {
    echo "Error: The specified directory does not exist.\n";
    exit(1);
}

$files = scandir($directory);
$renamedCount = 0;

foreach ($files as $file) {
    $fullPath = $directory . '/' . $file;
    if (is_file($fullPath) && preg_match('/\.example$/', $file)) {
        $newName = preg_replace('/\.example$/', '', $file);
        $newPath = $directory . '/' . $newName;
        
        if (file_exists($newPath)) {
            echo "Warning: Cannot rename $file, $newName already exists.\n";
            continue;
        }
        
        if ($dryRun) {
            echo "Would rename: $file to $newName\n";
        } else {
            if (rename($fullPath, $newPath)) {
                echo "Renamed: $file to $newName\n";
                $renamedCount++;
            } else {
                echo "Error: Failed to rename $file\n";
            }
        }
    }
}

if ($dryRun) {
    echo "Dry run complete. $renamedCount file(s) would be renamed.\n";
} else {
    echo "Renaming complete. $renamedCount file(s) renamed.\n";
}
