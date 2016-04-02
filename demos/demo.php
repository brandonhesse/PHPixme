<html>
<head>
    <title>PHPixing php</title>
</head>
<body>
<?php
/**
 * Created by PhpStorm.
 * User: rgladson
 * Date: 12/30/2015
 * Time: 10:33 AM
 */
require __DIR__ . '/bootstrap.php';
const testData = [1, 2, 4, 8, 16];
$jsonTestData = json_encode(testData);
$testFn = function (...$args) {
    $output = implode(', ', $args);
    echo '$testFn received: ' . $output . BR;
    return $output;
};
$timesTwo = function ($value) {
    return $value * 2;
};
$stringify = function ($value) {
    return "'$value'";
};
$curry2 = curry(2);
$quaternary = nAry(4);
$map = binary('array_map');
$mapX2 = $map($timesTwo);
$joinComma = binary('implode')->__invoke(', ');
$makeView = combine($stringify, $timesTwo);
?>
<h1>Time to fix PHP!</h1>
<p>
    Behold! There is nothing up my sleeves!<br>
    <?= $jsonTestData ?>
</p>

<section>
    <h2>Testing Quaternary!</h2>
    <p>
        <?php
        $output = call_user_func_array($quaternary($testFn), testData)
        ?>
        Quaternary returned: <?= $output ?>
    </p>
</section>
<section>
    <h2>Testing Unary</h2>
    <p>
        <?php
        $output = call_user_func_array(unary($testFn), testData)
        ?>
        Unary returned: <?= $output ?>

    </p>
</section>
<section>
    <h2>Currying native function test. Target: array_map</h2>
    <p>
        <?= $jsonTestData ?> &mdash;$map2x&rightarrow; <?= json_encode($mapX2(testData)) ?>
    </p>
</section>
<section>
    <h2>imploding with a pre-formatted join!</h2>
    <p>
        <?= $jsonTestData ?> &mdash;$joinComma&rightarrow; <?= $joinComma(testData) ?>
    </p>
</section>
<section>
    <h2>Combine $timesTwo with $stringify, then map it and join it through the pre-formated join!</h2>
    <p>
        <?= $jsonTestData ?> &mdash;map($makeView)&rightarrow;
        <?= $joinComma($map($makeView, testData)) ?>
    </p>
</section>
<section>
    <h2>Again, but now using fold to produce the join!</h2>
    <p>
        <?= $jsonTestData ?> &mdash;map($makeView)&mdash;fold&rightarrow;
        <?= fold(function ($output, $value) {
            return $output ? "$output, $value" : $value;
        }, '', $map($makeView, testData)) ?>
    </p>
</section>
<section>
    <h2>Again, but now using reduce to produce the join!</h2>
    <p>
        <?= $jsonTestData ?> &mdash;map($makeView)&mdash;reduce&rightarrow;
        <?= reduce(function ($output, $value) {
            return "$output, $value";
        }, $map($makeView, testData)) ?>
    </p>
</section>
<section>
    <h2>Watch in <i>horror</i> as I flip a native function! array_filter</h2>
    <p>
        <?= $jsonTestData ?> &mdash;map2x&mdash;filterWith(_ mod 8 = 0)&mdash;values&rightarrow;
        <?= json_encode(array_values(
            flip('array_filter')
                ->__invoke(function ($value) {
                    return $value % 8 === 0;
                })
                ->__invoke($mapX2(testData))
        )) ?>
    </p>
</section>
<section>
    <h2>For my next trick, I will flip array_walk! (With a little help from binary)</h2>
    <p>
        The key-value pairs of <?= $jsonTestData ?>:
    </p>
    <ul>
        <?php
        $keyPairs = flip(
            binary('array_walk')
        )->__invoke(
            function ($value, $key) {
                echo "<li>$key &DoubleRightArrow; $value</li>" . PHP_EOL;
            }
        );
        $keyPairs(testData);
        ?>
    </ul>
</section>
<section>
    <h2>Thrill as I convert this Hash Array to an array of Tuples!</h2>
    <p>
        <?php
        $tupleMaker = S(
            ternary('array_map')
                ->__invoke(
                    function ($val, $key) {
                        return [$key, $val];
                    }
                ),
            'array_keys'
        );
        $testDataAsTuple = $tupleMaker(testData);
        ?>
        <?= $jsonTestData ?> &mdash;$map(x, y -> [y,x])&rightarrow;
        <?= json_encode($testDataAsTuple) ?>
    </p>
    <p>And to show why I picked these numbers</p>
    <ul>
        <?php
        flip(
            binary('array_walk')
        )->__invoke(function ($tuple) {
            echo "<li>2<sup>$tuple[0]</sup> = $tuple[1]</li>" . PHP_EOL;
        }, $testDataAsTuple);
        ?>
    </ul>
    <p>Time taken: <?= microtime(true) - $start; ?> seconds.</p>
    <p>Peak Mem Usage: <?= memory_get_peak_usage(false) / 1024 / 1024; ?> MiB, Max This Script: <?= memory_get_peak_usage(true) / 1024 / 1024; ?> MiB</p>
</section>
</body>
</html>
