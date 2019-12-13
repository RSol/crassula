<?php

namespace App\Controller;

use App\Entity\Rate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RateController extends AbstractController
{
    /**
     * @Route("/rate/{from}/{to}/{amount}", name="rate", methods={"GET","HEAD"})
     * @param $from
     * @param $to
     * @param $amount
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function index($from, $to, $amount, ValidatorInterface $validator): JsonResponse
    {
        $source = $this->getParameter('rate.source');

        $from = strtoupper($from);
        $to = strtoupper($to);
        $amount = (float)$amount;

        $constraints = new Assert\Collection([
            'from' => [new Assert\Length(['value' => 3]), new Assert\NotBlank, new Assert\Currency()],
            'to' => [new Assert\Length(['value' => 3]), new Assert\NotBlank, new Assert\Currency()],
            'amount' => [new Assert\Type(['type' => 'float']), new Assert\notBlank],
        ]);

        $violations = $validator->validate([
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
        ], $constraints);

        if (count($violations) > 0) {
            return $this->json([
                'success' => false,
                'message' => $violations->get(0)->getMessage(),
            ]);
        }

        $rate = $this->getDoctrine()
            ->getRepository(Rate::class)
            ->findRate($source, $from, $to);
        if (!$rate) {
            return $this->json([
                'success' => false,
                'message' => "Don't find rate for {$from} to {$to} pair",
            ]);
        }

        return $this->json([
            'success' => true,
            'value' => $amount * $rate,
        ]);
    }
}
