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
    name: 'x-dns:zone:show',
    description: 'Show zone details from the selected provider'
)]
class ZoneShowCommand extends Command
{
    public function __construct(
        private readonly XDnsService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'fqzn',
            InputArgument::REQUIRED,
            'The fully qualified zone name (with @provider postfix)'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $fqzn = $input->getArgument('fqzn');
        $zone = $this->service->getZoneByFqzn($fqzn);

        $io->writeln("Zone: " . $zone->getFqzn());
        $io->writeln("Records: " . count($zone->getRecords()));
        $io->writeln("Targets: " . implode(", ", $zone->getTargets()));

        foreach ($zone->getRecords() as $record) {
            $io->writeln('    <comment>' . $record->getName() . "</comment> " . $record->getType() . " <info>" . $record->getValue() . "</info> (ttl " . $record->getTtl() . ')');
        }

        return Command::SUCCESS;
    }
}
