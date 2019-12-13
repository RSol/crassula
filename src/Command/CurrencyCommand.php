<?php


namespace App\Command;


use App\Entity\Rate;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class CurrencyCommand extends Command
{
    private $urlEuropa = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';
    private $urlCbr = 'https://www.cbr.ru/scripts/XML_daily.asp';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * CurrencyCommand constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'app:currency';
    }


    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Currencies exchange rate export.')
            ->setHelp('Export currencies exchange rate from ecb.europa.eu and cbr.ru');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln([
            '<info>Export currencies exchange rate</info>',
            '===============================',
            '',
        ]);

        $output->writeln($this->europa());
        $output->writeln($this->cbr());


        $output->writeln([
            '<info>Finished.</info>',
            ''
        ]);
    }

    /**
     * @return Generator
     * @throws Exception
     */
    private function europa(): ?Generator
    {
        yield '<options=bold>Start export from ecb.europa.eu</>';

        yield 'Get url content';
        $xml = file_get_contents($this->urlEuropa);
        $crawler = new Crawler($xml);

        $rates = $crawler
            ->filter('default|Cube')
            ->each(static function (Crawler $node, $i) {
                if (!$node->attr('currency')) {
                    return [];
                }
                return [
                    'from_currency' => 'EUR',
                    'to_currency' => $node->attr('currency'),
                    'source' => Rate::SOURCE_EUROPA,
                    'rate' => (float)$node->attr('rate'),
                ];
            });
        $rates = array_filter($rates);

        foreach ($this->setRates($rates) as $message) {
            yield $message;
        }
        yield '';
    }

    /**
     * @return Generator
     * @throws Exception
     */
    private function cbr(): ?Generator
    {
        yield '<options=bold>Start export from cbr.ru</>';

        yield 'Get url content';

        $xml = file_get_contents($this->urlCbr);

        $crawler = new Crawler($xml);

        $rates = $crawler
            ->filter('Valute')
            ->each(static function (Crawler $node, $i) {
                $nominal = (int)$node->children('Nominal')->text();
                $rate = (float)str_replace(',', '.', $node->children('Value')->text());
                return [
                    'from_currency' => $node->children('CharCode')->text(),
                    'rate' => $rate / $nominal,
                    'to_currency' => 'RUB',
                    'source' => Rate::SOURCE_CBR,
                ];
            });

        foreach ($this->setRates($rates) as $message) {
            yield $message;
        }
        yield '';
    }

    /**
     * @param $rates
     * @return Generator
     * @throws Exception
     */
    private function setRates($rates): ?Generator
    {
        foreach ($rates as $rate) {
            $entity = $this->em->getRepository(Rate::class)
                ->findOneBy([
                    'from_currency' => $rate['from_currency'],
                    'to_currency' => $rate['to_currency'],
                    'source' => $rate['source'],
                ]);
            if ($entity === null) {
                $entity = new Rate();
            }

            $entity->setFromCurrency($rate['from_currency']);
            $entity->setToCurrency($rate['to_currency']);
            $entity->setSource($rate['source']);
            $entity->setRate($rate['rate']);
            $entity->setUpdatedAt(new DateTime());

            $this->em->persist($entity);
            $this->em->flush();

            yield "Set {$rate['from_currency']} to {$rate['to_currency']} rate";
        }
    }

}