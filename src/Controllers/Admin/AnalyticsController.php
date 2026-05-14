<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\View;
use App\Repositories\AnalyticsRepository;

final class AnalyticsController
{
    private View $view;
    private AnalyticsRepository $repo;

    public function __construct()
    {
        $this->view = new View(base_path('templates'));
        $this->repo = new AnalyticsRepository();
    }

    public function index(Request $request): string
    {
        $days = (int) $request->query('days', 30);
        $days = in_array($days, [7, 14, 30, 90], true) ? $days : 30;

        $data = [
            'title'           => 'Analytics',
            'days'            => $days,
            'visitors'        => $this->repo->visitorsCount($days),
            'pageviews'       => $this->repo->pageViewsCount($days),
            'loggedUsers'     => $this->repo->loggedSessionsCount($days),
            'funnel'          => $this->repo->funnel($days),
            'topProducts'     => $this->repo->topProducts($days, 10),
            'topCategories'   => $this->repo->topCategories($days, 10),
            'topSearches'     => $this->repo->topSearches($days, 15),
            'dailyTraffic'    => $this->repo->dailyTraffic(min($days, 30)),
            'abandonedCarts'  => $this->repo->abandonedCartsCount(3),
            'addToCartEvents' => $this->repo->eventsCount('add_to_cart', $days),
        ];

        return $this->view->render('admin/analytics/index', $data);
    }
}
