<?php

namespace Phpactor\Extension\LanguageServerPsalm;

use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
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
    public const PARAM_PHPSTAN_BIN = 'language_server_phpstan.bin';
    public const PARAM_LEVEL = 'phpstan.level';

    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container)
    {
        $container->register(PsalmService::class, function (Container $container) {
            return new PsalmService(
                $container->get(MessageTransmitter::class),
                $container->get(Linter::class)
            );
        }, [
            LanguageServerExtension::TAG_LISTENER_PROVIDER => [],
            LanguageServerExtension::TAG_SERVICE_PROVIDER => [],
        ]);

        $container->register(Linter::class, function (Container $container) {
            return new PsalmLinter($container->get(PsalmProcess::class));
        });

        $container->register(PsalmProcess::class, function (Container $container) {
            $binPath = $container->get(FilePathResolverExtension::SERVICE_FILE_PATH_RESOLVER)->resolve($container->getParameter(self::PARAM_PHPSTAN_BIN));
            $root = $container->get(FilePathResolverExtension::SERVICE_FILE_PATH_RESOLVER)->resolve('%project_root%');

            return new PsalmProcess(
                $root,
                new PsalmConfig($binPath, $container->getParameter(self::PARAM_LEVEL)),
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
            self::PARAM_PHPSTAN_BIN => '%project_root%/vendor/bin/phpstan',
            self::PARAM_LEVEL => null,
        ]);
    }
}
