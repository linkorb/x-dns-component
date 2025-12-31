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
    name: 'x-dns:zone:list',
    description: 'List zones at selected provider'
)]
class ZoneListCommand extends Command
{
    public function __construct(
        private readonly XDnsService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'providerName',
            InputArgument::REQUIRED,
            'The provider name'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $providerName = $input->getArgument('providerName');
        $provider = $this->service->getProvider($providerName);
        $adapter = $provider->getAdapter();
        $zoneNames = $adapter->getZoneNames();
        foreach ($zoneNames as $zoneName) {
            $io->writeln($zoneName);
        }

        return Command::SUCCESS;
    }
}
