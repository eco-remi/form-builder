<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'form:generate:html',
    description: 'Save only html files generated from json source',
)]
class FormGenerateHtmlCommand extends Command
{
    public function __construct(
        private HttpClientInterface $client,
        ?string $name = null,
    ) {
        $this->client = $this->client->withOptions([
                'verify_host' => false,
                'verify_peer' => false,
        ]);

        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $name = 'Rémi';

        file_put_contents(__DIR__ . '/../../public/form/'
            . $name . '_qcm.html',
            $this->client->request('GET', 'https://127.0.0.1:8000/form?slug=qcm&name=' . $name)->getContent()
        );

        $io->success('Save file : ' . $name);

        return Command::SUCCESS;
    }
}
