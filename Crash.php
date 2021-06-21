<?php
require_once "vendor/autoload.php";

$Options = \getopt("tTcCiIoOp::P::", ["test", "Test", "create", "Create", "path::", "Path::", "inherit", "Inherit", "overwrite", "Overwrite"]);

if(isset($Options["t"]) || isset($Options["T"]) || isset($Options["Test"]) || isset($Options["test"])) {
    echo \json_encode(\Crash::Test($Options["p"] ?? $Options["P"] ?? $Options["Path"] ?? $Options["path"] ?? Crash::Tests), \JSON_PRETTY_PRINT);
} else if(isset($Options["c"]) || isset($Options["C"]) || isset($Options["create"]) || isset($Options["Create"])) {

    $Path = $Options["p"] ?? $Options["P"] ?? $Options["Path"] ?? $Options["path"] ?? "";

    if(!\is_dir($Path)) {
        echo "Value of required parameter \"Path\" is not a valid directory! \"{$Path}\"";
        exit;
    }
    print "Created classes:" . \PHP_EOL;
    foreach(
        \Crash::CreateFromPath(
            $Path,
            isset($Options["i"]) || isset($Options["I"]) || isset($Options["Inherit"]) || isset($Options["inherit"]),
            isset($Options["o"]) || isset($Options["O"]) || isset($Options["overwrite"]) || isset($Options["Overwrite"])
        )
        as
        $Class
    ) {
        print $Class . \PHP_EOL;
    }
} else {
    print "usage:" . \PHP_EOL;
    print "php Crash.php -t[--test] -p[--path]=\"./tests\" " . \PHP_EOL;
    print "php Crash.php -c[--create] -i[--inherit] -o[--overwrite] -p[--path]=\"./src\" " . \PHP_EOL;
}