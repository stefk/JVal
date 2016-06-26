<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

foreach ([__DIR__.'/../../../autoload.php', __DIR__.'/../vendor/autoload.php'] as $file) {
    if (file_exists($file)) {
        require $file;
        break;
    }
}

set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

$writeln = function ($msg, $type = null) {
    switch ($type) {
        case 'info':
            $code = "\033[0;36m";
            break;
        case 'error':
            $code = "\033[31m";
            break;
        default:
            $code = "\033[0m";
    }

    echo "{$code}{$msg}\033[0m\n";
};

if ($argc !== 3 && $argc !== 4) {
    $writeln('Usage: jval data_file schema_file [schema_uri]', 'info');
    exit(1);
}

$dataFile = [$argv[1], realpath($argv[1])];
$schemaFile = [$argv[2], realpath($argv[2])];
$schemaUri = isset($argv[3]) ? $argv[3] : '';

foreach ([$dataFile, $schemaFile] as $fileInfo) {
    if (!file_exists($fileInfo[1])) {
        $writeln("File \"{$fileInfo[0]}\" does not exist", 'error');
        exit(1);
    }
}

try {
    $instance = JVal\Utils::loadJsonFromFile($dataFile[1]);
    $schema = JVal\Utils::loadJsonFromFile($schemaFile[1]);
    $validator = JVal\Validator::buildDefault();
    $errors = $validator->validate($instance, $schema, $schemaUri);
    echo json_encode($errors, JSON_PRETTY_PRINT) . "\n";
    ($count = count($errors)) > 0 ?
        $writeln("{$count} errors.", 'error') :
        $writeln('No error.', 'info');
} catch (Exception $ex) {
    $writeln($ex->getMessage(), 'error');
    $writeln($ex->getTraceAsString());
}
