<?php

namespace Phpactor\Extension\LanguageServerPsalm;

use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\Extension\LanguageServerPsalm\DiagnosticProvider\PsalmDiagnosticProvider;
use Phpactor\Extension\LanguageServerPsalm\Handler\PsalmService;
use Phpactor\Extension\LanguageServerPsalm\Model\Linter;
use Phpactor\Extension\LanguageServerPsalm\Model\Linter\PsalmLinter;
use Phpactor\Extension\LanguageServerPsalm\Model\PsalmConfig;
use Phpactor\Extension\LanguageServerPsalm\Model\PsalmProcess;
use Phpactor\Extension\LanguageServer\LanguageServerExtension;
use Phpactor\Extension\Logger\LoggingExtension;
use Phpactor\FilePathResolverExtension\FilePathResolverExtension;
use Phpactor\LanguageServer\Core\Server\Transmitter\MessageTransmitter;
use Phpactor\MapResolver\Resolver;

class LanguageServerPsalmExtension implements Extension
{
    public const PARAM_PSALM_BIN = 'language_server_psalm.bin';

    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container)
    {
        $container->register(PsalmDiagnosticProvider::class, function (Container $container) {
            return new PsalmDiagnosticProvider(
                $container->get(Linter::class)
            );
        }, [
            LanguageServerExtension::TAG_DIAGNOSTICS_PROVIDER => [],
        ]);

        $container->register(Linter::class, function (Container $container) {
            return new PsalmLinter($container->get(PsalmProcess::class));
        });

        $container->register(PsalmProcess::class, function (Container $container) {
            $binPath = $container->get(FilePathResolverExtension::SERVICE_FILE_PATH_RESOLVER)->resolve($container->getParameter(self::PARAM_PSALM_BIN));
            $root = $container->get(FilePathResolverExtension::SERVICE_FILE_PATH_RESOLVER)->resolve('%project_root%');

            return new PsalmProcess(
                $root,
                new PsalmConfig($binPath),
                $container->get(LoggingExtension::SERVICE_LOGGER)
            );
        });
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Resolver $schema)
    {
        $schema->setDefaults([
            self::PARAM_PSALM_BIN => '%project_root%/vendor/bin/psalm',
        ]);
    }
}
