<?php

namespace LinkORB\Component\XDns\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use LinkORB\Component\XDns\XDnsService;

#[AsCommand(
    name: 'x-dns:zone:push',
    description: 'Push a zone to a provider B'
)]
class ZonePushCommand extends Command
{
    public function __construct(
        private readonly XDnsService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'fqzn',
                InputArgument::REQUIRED,
                'Zone'
            )
            ->addArgument(
                'providerName',
                InputArgument::OPTIONAL,
                'Provider name'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $fqzn = $input->getArgument('fqzn');
        $providerName = $input->getArgument('providerName');

        $targets = [$providerName];
        $zone = $this->service->getZoneByFqzn($fqzn);
        if (!$providerName) {
            $targets = $zone->getTargets();
        }

        foreach ($targets as $providerName) {
            $io->writeln('Pushing zone ' . $fqzn . ' to ' . $providerName);
            $provider = $this->service->getProvider($providerName);

            $adapter = $provider->getAdapter();
            $adapter->pushZone($zone);
            $io->writeln('OK');
        }

        return Command::SUCCESS;
    }
}
