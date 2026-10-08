#!/usr/bin/env php
<?php
/**
 * tool/run_cli.php — runner CLI untuk controller CI3 project ini.
 * Kenapa: application/config/constants.php membaca $_SERVER['SERVER_NAME']
 * yang tidak ada di CLI, membuat php index.php <controller> kosong.
 *
 * Pemakaian: php tool/run_cli.php <controller-segment> [args...]
 * contoh   : php tool/run_cli.php tools/translator_test deepl_real
 */
if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}

$_SERVER['SERVER_NAME'] = 'jacos.test';
$_SERVER['SERVER_PORT'] = '8080';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['REQUEST_METHOD'] = 'GET';

set_error_handler(function($no,$str,$file,$line){ echo 'ERR['.$no.'] '.substr($str,0,60).' at '.$file.':'.$line.PHP_EOL; $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 6); foreach ($bt as $i=>$f){ if($i==0) continue; echo '   #'.(isset($f['line'])?$f['line']:'-').' '.(isset($f['function'])?$f['function']:'').'
'; } return true; });
include __DIR__ . '/../index.php';
