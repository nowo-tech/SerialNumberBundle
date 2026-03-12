<?php

declare(strict_types=1);

namespace App\Controller;

use Nowo\SerialNumberBundle\Service\SerialNumberGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use const STR_PAD_LEFT;

class DemoController extends AbstractController
{
    private const CONST_FA = 'FA';

    public function __construct(
        private readonly SerialNumberGenerator $serialNumberGenerator,
    ) {
    }

    #[Route('/', name: 'demo_home')]
    public function home(): Response
    {
        $year  = (int) date('Y');
        $month = (int) date('n');

        // Example 1: FA + RI + year + id (numeric, 5 digits)
        $ex1Context = [
            'const'   => self::CONST_FA,
            'product' => 'RI',
            'year'    => $year,
        ];
        $ex1Pattern = '{const}-{product}-{year}-{id}';
        $ex1Id      = 42;
        $ex1Serial  = $this->serialNumberGenerator->generate($ex1Context, $ex1Pattern, $ex1Id, 5);

        // Example 2: FA + PO + year + id (hexadecimal)
        $ex2Context = [
            'const'   => self::CONST_FA,
            'product' => 'PO',
            'year'    => $year,
            'id_hex'  => str_pad(dechex(255), 4, '0', STR_PAD_LEFT),
        ];
        $ex2Pattern = '{const}-{product}-{year}-{id_hex}';
        $ex2Serial  = $this->serialNumberGenerator->generate($ex2Context, $ex2Pattern, 0, null);

        // Example 3: FA + RI + year + month + id (numeric)
        $ex3Context = [
            'const'   => self::CONST_FA,
            'product' => 'RI',
            'year'    => $year,
            'month'   => $month,
        ];
        $ex3Pattern = '{const}-{product}-{year}-{month}-{id}';
        $ex3Id      = 7;
        $ex3Serial  = $this->serialNumberGenerator->generate($ex3Context, $ex3Pattern, $ex3Id, 4);

        return $this->render('demo/home.html.twig', [
            'version_badge' => 'Symfony 7.0',
            'examples'      => [
                [
                    'name'    => 'FA + RI + year + id (numeric)',
                    'pattern' => $ex1Pattern,
                    'context' => $ex1Context,
                    'id'      => $ex1Id,
                    'padding' => 5,
                    'serial'  => $ex1Serial,
                ],
                [
                    'name'    => 'FA + PO + year + id (hexadecimal)',
                    'pattern' => $ex2Pattern,
                    'context' => $ex2Context,
                    'id'      => null,
                    'padding' => null,
                    'serial'  => $ex2Serial,
                ],
                [
                    'name'    => 'FA + RI + year + month + id (numeric)',
                    'pattern' => $ex3Pattern,
                    'context' => $ex3Context,
                    'id'      => $ex3Id,
                    'padding' => 4,
                    'serial'  => $ex3Serial,
                ],
            ],
        ]);
    }
}
