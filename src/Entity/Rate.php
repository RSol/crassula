<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RateRepository")
 * @ORM\Table(name="rate",indexes={@ORM\Index(name="search_idx", columns={"from_currency", "to_currency", "source"})})
 */
class Rate
{
    public const SOURCE_EUROPA = 1;
    public const SOURCE_CBR = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $from_currency;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $to_currency;

    /**
     * @ORM\Column(type="float")
     */
    private $rate;

    /**
     * @ORM\Column(type="smallint")
     */
    private $source;

    /**
     * @ORM\Column(type="datetime", options={"default":0})
     */
    private $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromCurrency(): ?string
    {
        return $this->from_currency;
    }

    public function setFromCurrency(string $from_currency): self
    {
        $this->from_currency = $from_currency;

        return $this;
    }

    public function getToCurrency(): ?string
    {
        return $this->to_currency;
    }

    public function setToCurrency(string $to_currency): self
    {
        $this->to_currency = $to_currency;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getSource(): ?int
    {
        return $this->source;
    }

    public function setSource(int $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @param $source
     * @param $currency
     * @return array
     */
    public static function getParamsBySource($source, $currency): array
    {
        if ($source === self::SOURCE_EUROPA) {
            return [
                'source' => self::SOURCE_EUROPA,
                'from_currency' => 'EUR',
                'to_currency' => $currency,
            ];
        }

        if ($source === self::SOURCE_CBR) {
            return [
                'source' => self::SOURCE_CBR,
                'from_currency' => $currency,
                'to_currency' => 'RUB',
            ];
        }

        return [];
    }
}
