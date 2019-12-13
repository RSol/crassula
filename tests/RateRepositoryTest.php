<?php


namespace App\Tests;


use App\Entity\Rate;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RateRepositoryTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testFindRateEuropa():void
    {
        $source = Rate::SOURCE_EUROPA;

        $rate = $this->entityManager
            ->getRepository(Rate::class)
            ->findRate($source, 'EUR', 'USD');

        $this->assertNotEquals(0, $rate);

        $rate = $this->entityManager
            ->getRepository(Rate::class)
            ->findRate($source, 'USD', 'EUR');

        $this->assertNotEquals(0, $rate);

        $rate = $this->entityManager
            ->getRepository(Rate::class)
            ->findRate($source, 'RUB', 'EUR');

        $this->assertNotEquals(0, $rate);

        $rate = $this->entityManager
            ->getRepository(Rate::class)
            ->findRate($source, 'USD', 'TST');

        $this->assertEquals(0, $rate);
    }

    public function testFindRateCbr()
    {
        $source = Rate::SOURCE_CBR;

        $rate = $this->entityManager
            ->getRepository(Rate::class)
            ->findRate($source, 'EUR', 'RUB');

        $this->assertNotEquals(0, $rate);

        $rate = $this->entityManager
            ->getRepository(Rate::class)
            ->findRate($source, 'RUB', 'EUR');

        $this->assertNotEquals(0, $rate);

        $rate = $this->entityManager
            ->getRepository(Rate::class)
            ->findRate($source, 'USD', 'EUR');

        $this->assertNotEquals(0, $rate);

        $rate = $this->entityManager
            ->getRepository(Rate::class)
            ->findRate($source, 'USD', 'TST');

        $this->assertEquals(0, $rate);
    }
}