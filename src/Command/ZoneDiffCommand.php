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
    name: 'x-dns:zone:diff',
    description: 'Diff two zones'
)]
class ZoneDiffCommand extends Command
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
                'fqznA',
                InputArgument::REQUIRED,
                'zone A'
            )
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'fqzn of zone B or provider name'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $fqznA = $input->getArgument('fqznA');
        $zoneA = $this->service->getZoneByFqzn($fqznA);

        $target = $input->getArgument('target');


        $targets = [$target];

        if (!$target) {
            $targets = $zoneA->getTargets();
        }

        foreach ($targets as $target) {

            $fqznB = $target;
            if (strpos($target, '@') === false) {
                // if target is just a provider name, prepend the zone name
                $fqznB = $zoneA->getName() . '@' . $target;
            }

            $zoneB = $this->service->getZoneByFqzn($fqznB);


            $io->writeln('Diff between ' . $zoneA->getFqzn() . ' and ' . $zoneB->getFqzn());

            foreach ($zoneA->getRecords() as $record) {
                $key = $record->getKey();
                if ($zoneB->hasKey($key)) {
                    $recordB = $zoneB->getRecord($key);
                    if ($record->getString() == $recordB->getString()) {
                        $io->writeln('  ' . $record->getString());
                    } else {
                        $io->writeln('> ' . $record->getString());
                        $io->writeln('< ' . $recordB->getString());
                    }
                } else {
                    $io->writeln('> ' . $record->getString());
                }
            }
            foreach ($zoneB->getRecords() as $record) {
                $key = $record->getKey();
                if (!$zoneA->hasKey($key)) {
                    $io->writeln('< ' . $record->getString());
                }
            }
        }

        return Command::SUCCESS;
    }
}
