<?php
/**
 * Created by PhpStorm.
 * User: brandon
 * Date: 4/1/16
 * Time: 10:51 PM
 */

require __DIR__ . '/bootstrap.php';

$jsonFlags = JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_PRETTY_PRINT;
$testData = ['a' => 1, 'b' => 2, 'c' => true, 'd' => null, 'e' => '"Hello" <<World>>', 'f' => ['g', 'h', 'i']];
$toJson = flip('json_encode')->__invoke($jsonFlags);
$json64e = combine('urlencode', combine('base64_encode', $toJson));
$json64d = combine(flip('json_decode')->__invoke(true), combine('base64_decode', 'urldecode'));
?>

<p><?= $toJson($testData); ?></p>
<p><?= $json64e($testData); ?></p>
<p><?= $toJson($json64d($json64e($testData))); ?></p>

<p>Time Taken: <?= microtime(true) - $start; ?> seconds.</p>
<p>Memory: <?= memory_get_peak_usage(false)/1024/1024; ?>/<?= memory_get_peak_usage(true)/1024/1024; ?> MiB.</p>