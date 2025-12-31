<?php

namespace LinkORB\Component\XDns\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use LinkORB\Component\XDns\XDnsService;

#[AsCommand(
    name: 'x-dns:provider:list',
    description: 'List configured DNS providers'
)]
class ProviderListCommand extends Command
{
    public function __construct(
        private readonly XDnsService $service,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $providers = $this->service->getProviders();
        $configErrors = $this->service->getConfigErrors();

        $io->writeln("Providers: " . count($providers));
        foreach ($providers as $provider) {
            $io->writeln('  - ' . $provider->getName());
        }

        // Show configuration errors for providers that failed to load
        if (!empty($configErrors)) {
            $io->newLine();
            $io->warning('Some providers failed to load due to configuration errors:');
            foreach ($configErrors as $providerName => $error) {
                $io->writeln(sprintf('  <fg=red>✗</> %s: %s', $providerName, $error));
            }
        }

        return Command::SUCCESS;
    }
}
