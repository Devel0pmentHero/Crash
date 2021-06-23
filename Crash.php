<?php
require_once __DIR__ . "/../../autoload.php";

$Options = \getopt("tTcCiIoOp::P::", ["test", "Test", "create", "Create", "path::", "Path::", "inherit", "Inherit", "overwrite", "Overwrite", "Class::", "class::"]);

if(isset($Options["t"]) || isset($Options["T"]) || isset($Options["Test"]) || isset($Options["test"])) {
    echo \json_encode(\Crash::Test($Options["p"] ?? $Options["P"] ?? $Options["Path"] ?? $Options["path"] ?? Crash::Tests), \JSON_PRETTY_PRINT);
} else if(isset($Options["c"]) || isset($Options["C"]) || isset($Options["create"]) || isset($Options["Create"])) {
    $Inherit   = isset($Options["i"]) || isset($Options["I"]) || isset($Options["Inherit"]) || isset($Options["inherit"]);
    $Overwrite = isset($Options["o"]) || isset($Options["O"]) || isset($Options["overwrite"]) || isset($Options["Overwrite"]);
    if(isset($Options["Class"]) || isset($Options["class"])) {
        print "Created class:" . \PHP_EOL;
        print \Crash::Create($Options["Class"] ?? $Options["class"], $Inherit, $Overwrite) . \PHP_EOL;
        exit;
    }
    print "Created classes:" . \PHP_EOL;
    foreach(
        \Crash::CreateFromPath($Options["p"] ?? $Options["P"] ?? $Options["Path"] ?? $Options["path"] ?? "", $Inherit, $Overwrite)
        as
        $Class
    ) {
        print $Class . \PHP_EOL;
    }
} else {
    print "usage:" . \PHP_EOL;
    print "php Crash.php -t[--test] -p[--path]=\"./tests\"" . \PHP_EOL;
    print "php Crash.php -c[--create] -p[--path|--class]=\"./src\" -i[--inherit] -o[--overwrite]" . \PHP_EOL;
}