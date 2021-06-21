<?php
declare(strict_types=1);

use Crash\Random;
use Crash\Test;

/**
 * Utility class for generating and executing Crash\Tests.
 *
 * @author Kerry <DevelopmentHero@gmail.com>
 */
class Crash {

    /**
     * Default directory of Crash\Tests.
     */
    public const Tests = __DIR__ . \DIRECTORY_SEPARATOR . "Crash" . \DIRECTORY_SEPARATOR . "Tests";

    /**
     * Loads and runs the Tests from a specified path.
     *
     * @param null|string $Path The path to load the Test from.
     *
     * @return array An array containing information about the results if the executed Tests.
     */
    public static function Test(?string $Path = null): array {
        $Tests = [];
        //Search for class files.
        foreach(
            new \RegexIterator(
                new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($Path)),
                '/^.+\.php$/i',
                \RecursiveRegexIterator::GET_MATCH
            )
            as
            $File
        ) {
            $Class = "Crash\\Tests\\" . \ltrim(\str_replace([$Path, ".php", \DIRECTORY_SEPARATOR], ["", "", "\\"], $File[0]), "\\");
            try {
                //Instantiate Test.
                $Test = new $Class();
                if(!$Test instanceof Test) {
                    $Tests[$Class] = [Test::Result => Test::Skipped];
                    continue;
                }

                //Run Test.
                $Tests[$Class] = $Test();

            } catch(\Throwable $Exception) {
                $Tests[$Class] = [
                    Test::Result  => Test::Crashed,
                    Test::Code    => $Exception->getCode(),
                    Test::Message => $Exception->getMessage(),
                    Test::File    => $Exception->getFile(),
                    Test::Line    => $Exception->getLine(),
                    Test::Trace   => $Exception->getTrace()
                ];
            }
        }
        return $Tests;
    }

    /**
     * Creates a Test class from a specified class.
     *
     * @param null|string $Class     The fully qualified class name of the class to create a Test from.
     * @param bool        $Inherit   Flag indicating whether to include inherited methods as Test cases.
     * @param bool        $Overwrite Flag indicating whether to overwrite any existing Test class.
     *
     * @return string|null The fully qualified name of the created Test class; otherwise, null.
     *
     * @throws \InvalidArgumentException|\ReflectionException Thrown if the specified class doesn't exist.
     */
    public static function Create(?string $Class = null, bool $Inherit = false, bool $Overwrite = false): ?string {
        if(!\class_exists($Class)) {
            throw new \InvalidArgumentException("Class \"{$Class}\" doesn't exist!");
        }
        $Reflector = new \ReflectionClass($Class);
        if($Reflector->isAbstract()) {
            return null;
        }

        //Create target directory.
        $Directory = \str_replace("\\", \DIRECTORY_SEPARATOR, $Reflector->getNamespaceName());
        if(!\is_dir(self::Tests . \DIRECTORY_SEPARATOR . $Directory)) {
            $Path = self::Tests;
            foreach(\explode("\\", $Directory) as $Namespace) {
                $Path .= \DIRECTORY_SEPARATOR . $Namespace;
                if(!\is_dir($Path)) {
                    \mkdir($Path);
                }
            }
        }
        $Path = self::Tests . \DIRECTORY_SEPARATOR . $Directory . \DIRECTORY_SEPARATOR . $Reflector->getShortName() . ".php";
        if(!$Overwrite && \is_file($Path)) {
            return null;
        }

        //Create Test.
        $File = \fopen($Path, "w+b");
        \fwrite($File, "<?php" . \PHP_EOL);
        \fwrite($File, "declare(strict_types=1);" . \PHP_EOL . \PHP_EOL);
        if($Reflector->inNamespace()) {
            \fwrite($File, "namespace Crash\\Tests\\{$Reflector->getNamespaceName()};" . \PHP_EOL . \PHP_EOL);
        } else {
            \fwrite($File, "namespace Crash\\Tests;" . \PHP_EOL . \PHP_EOL);
        }
        \fwrite($File, "use Crash\\Test;" . \PHP_EOL . \PHP_EOL);
        \fwrite($File, "class {$Reflector->getShortName()} extends Test {" . \PHP_EOL . \PHP_EOL);

        //Create constructor/dependency property.
        if(\PHP_VERSION_ID > 80000) {
            if(\PHP_VERSION_ID > 74000) {
                \fwrite($File, "    protected ?\\{$Reflector->getName()} \${$Reflector->getShortName()} = null;" . \PHP_EOL . \PHP_EOL);
            } else {
                \fwrite($File, "    protected \${$Reflector->getShortName()} = null;" . \PHP_EOL . \PHP_EOL);
            }
            \fwrite($File, "    public function __construct() {" . \PHP_EOL);
        } else {
            \fwrite($File, "    public function __construct(protected ?\\{$Reflector->getName()} \${$Reflector->getShortName()} = null) {" . \PHP_EOL);
        }
        \fwrite($File, "        \$this->{$Reflector->getShortName()} = new \\{$Reflector->getName()}();" . \PHP_EOL);
        \fwrite($File, "    }" . \PHP_EOL . \PHP_EOL);

        //Create Test cases.
        foreach($Reflector->getMethods() as $Method) {
            //Skip magic and internal methods.
            if(
                $Method->isConstructor()
                || $Method->isDestructor()
                || $Method->isAbstract()
                || $Method->isInternal()
                || $Method->isPrivate()
                || \strpos($Method->getName(), "__") === 0
                || (!$Inherit && $Method->getDeclaringClass()->getName() !== $Reflector->getName())
            ) {
                continue;
            }

            if(\PHP_VERSION_ID >= 80000) {
                //Copy Attributes.
                $Attributes = \array_merge(
                    $Method->getAttributes(Crash\Test\Case\Crash::class),
                    $Method->getAttributes(Crash\Test\Case\Repeat::class),
                    $Method->getAttributes(Crash\Test\Case\Values::class),
                    $Method->getAttributes(Crash\Test\Case\Skip::class)
                );
                if(\count($Attributes) > 0) {
                    foreach($Attributes as $Attribute) {
                        \fwrite($File, "    " . Crash\Attribute::FromReflector($Attribute) . \PHP_EOL);
                    }
                }
            }

            //Create Test case stub.
            \fwrite($File, "    public function {$Method->getName()}(): void {" . \PHP_EOL);

            //Format parameters.
            $Parameters = [];
            foreach($Method->getParameters() as $Parameter) {
                $String = "";
                if(\PHP_VERSION_ID >= 80000) {
                    $String .= "{$Parameter->getName()}: ";
                }
                if($Parameter->isDefaultValueAvailable()) {
                    $Parameters[] = $String . \json_encode($Parameter->getDefaultValue());
                    continue;
                }
                switch($Type = \ltrim((string)$Parameter->getType(), "?")) {
                    case "int":
                        $String .= Random::Int(true);
                        break;
                    case "float":
                        $String .= Random::Float(true);
                        break;
                    case "string":
                        $String .= Random::String();
                        break;
                    case "bool":
                    case "boolean":
                        $String .= \json_encode(Random::Bool());
                        break;
                    case "array":
                        $String .= \json_encode(Random::Array());
                        break;
                    case "callable":
                        $String .= "static function() {echo \"Replace me\";}";
                        break;
                    case "mixed":
                    case "":
                        $String .= "null";
                        break;
                    default:
                        $String .= "new \\{$Type}()";
                }
                $Parameters[] = $String;
            }

            //Create default assertion.
            \fwrite(
                $File,
                "        \assert(\$this->{$Reflector->getShortName()}"
                . ($Method->isStatic() ? "::" : "->")
                . "{$Method->getName()}("
                . \implode(", ", $Parameters)
                . ") "
            );

            //Format return type.
            if($Method->hasReturnType()) {
                switch($Type = \ltrim((string)$Method->getReturnType(), "?")) {
                    case "int":
                        \fwrite($File, "=== " . Random::Int(true));
                        break;
                    case "float":
                        \fwrite($File, "=== " . Random::Float(true));
                        break;
                    case "string":
                        \fwrite($File, "=== \"" . Random::String() . "\"");
                        break;
                    case "bool":
                    case "boolean":
                        \fwrite($File, "=== " . \json_encode(Random::Bool()));
                        break;
                    case "array":
                        \fwrite($File, "=== " . \json_encode(Random::Array()));
                        break;
                    case "mixed":
                    case "void":
                        \fwrite($File, "=== null");
                        break;
                    case "self":
                        \fwrite($File, "instanceof \\{$Reflector->getName()}");
                        break;
                    default:
                        \fwrite($File, "instanceof \\{$Type}");
                }
            }
            \fwrite($File, ");" . \PHP_EOL);
            \fwrite($File, "    }" . \PHP_EOL . \PHP_EOL);
        }

        \fwrite($File, "}" . \PHP_EOL);
        \fclose($File);

        return "Crash\\Tests\\{$Class}";

    }


    /**
     * Creates a set of Test classes from a specified path.
     *
     * @param string $Path      The path to iterate through.
     * @param bool   $Inherit   Flag indicating whether to include inherited methods as Test cases.
     * @param bool   $Overwrite Flag indicating whether to overwrite any existing Test class.
     *
     * @return array An array containing the created Test classes.
     */
    public static function CreateFromPath(string $Path, bool $Inherit = false, bool $Overwrite = false): array {
        $Tests = [];
        \ob_start();
        $Classes = \get_declared_classes();
        foreach(
            new \RegexIterator(
                new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($Path)),
                '/^.+\.php$/i',
                \RecursiveRegexIterator::GET_MATCH
            )
            as
            $File
        ) {
            try {
                include $File[0];
            } catch(\Throwable $Exception) {
                \ob_end_clean();
                echo $Exception->getMessage() . \PHP_EOL;
                \ob_start();
            }
        }
        \ob_end_flush();
        foreach(\array_diff(\get_declared_classes(), $Classes) as $Class) {
            $Tests[] = static::Create($Class, $Inherit, $Overwrite);
        }

        return $Tests;
    }

}