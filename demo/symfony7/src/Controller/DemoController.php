<?php

declare(strict_types=1);

namespace App\Controller;

use DateTimeImmutable;
use Nowo\RelativeTimeBundle\Service\RelativeTimeFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Demo page for Relative Time Bundle filters and service.
 */
class DemoController extends AbstractController
{
    public function __construct(
        private readonly RelativeTimeFormatter $relativeTimeFormatter,
    ) {
    }

    #[Route('/', name: 'demo_home')]
    public function home(): Response
    {
        $now = new DateTimeImmutable('now');

        $examples = [
            ['label' => 'Just now', 'date' => $now->modify('-3 seconds')],
            ['label' => '45 seconds ago', 'date' => $now->modify('-45 seconds')],
            ['label' => '5 minutes ago', 'date' => $now->modify('-5 minutes')],
            ['label' => '3 hours ago', 'date' => $now->modify('-3 hours')],
            ['label' => '2 days ago', 'date' => $now->modify('-2 days')],
            ['label' => '2 weeks ago', 'date' => $now->modify('-2 weeks')],
            ['label' => '3 months ago', 'date' => $now->modify('-3 months')],
            ['label' => '1 year ago', 'date' => $now->modify('-1 year')],
            ['label' => 'In 8 minutes', 'date' => $now->modify('+8 minutes')],
            ['label' => 'In 2 days', 'date' => $now->modify('+2 days')],
        ];

        $rows = [];
        foreach ($examples as $example) {
            $rows[] = [
                'label' => $example['label'],
                'iso'   => $example['date']->format(DateTimeImmutable::ATOM),
                'en'    => $this->relativeTimeFormatter->format($example['date'], 'en', $now),
                'es'    => $this->relativeTimeFormatter->format($example['date'], 'es', $now),
                'fr'    => $this->relativeTimeFormatter->format($example['date'], 'fr', $now),
                'de'    => $this->relativeTimeFormatter->format($example['date'], 'de', $now),
            ];
        }

        return $this->render('demo/home.html.twig', [
            'version_badge' => 'Symfony 7.4',
            'now'           => $now,
            'rows'          => $rows,
        ]);
    }
}
