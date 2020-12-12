<?php

namespace Phpactor\Extension\LanguageServerPsalm\Tests\Model;

use Generator;
use Phpactor\Extension\LanguageServerPsalm\Model\PsalmConfig;
use Phpactor\Extension\LanguageServerPsalm\Model\PsalmProcess;
use Phpactor\LanguageServerProtocol\DiagnosticSeverity;
use Phpactor\LanguageServerProtocol\Position;
use Phpactor\LanguageServerProtocol\Range;
use Phpactor\LanguageServerProtocol\Diagnostic;
use Psr\Log\NullLogger;
use Phpactor\Extension\LanguageServerPsalm\Tests\IntegrationTestCase;

class PsalmProcessTest extends IntegrationTestCase
{
    /**
     * @dataProvider provideLint
     */
    public function testLint(string $source, array $expectedDiagnostics): void
    {
        $this->workspace()->reset();
        $this->workspace()->put('test.php', $source);
        $linter = new PsalmProcess(
            $this->workspace()->path(),
            new PsalmConfig(__DIR__ . '/../../vendor/bin/psalm'),
            new NullLogger()
        );
        $diagnostics = \Amp\Promise\wait($linter->analyse($this->workspace()->path('test.php')));
        self::assertEquals($expectedDiagnostics, $diagnostics);
    }

    /**
     * @return Generator<mixed>
     */
    public function provideLint(): Generator
    {
        yield [
            '<?php $foobar = "string";',
            []
        ];

        yield [
            '<?php $foobar = $barfoo;',
            [
                Diagnostic::fromArray([
                    'range' => new Range(
                        new Position(0, 15),
                        new Position(0, 22)
                    ),
                    'message' => 'Cannot find referenced variable $barfoo in global scope',
                    'severity' => DiagnosticSeverity::ERROR,
                    'source' => 'psalm'
                ])
            ]
        ];
    }
}
