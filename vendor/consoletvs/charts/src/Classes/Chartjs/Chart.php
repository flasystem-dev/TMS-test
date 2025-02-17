<?php

namespace ConsoleTVs\Charts\Classes\Chartjs;

use ConsoleTVs\Charts\Classes\BaseChart;
use ConsoleTVs\Charts\Features\Chartjs\Chart as ChartFeatures;

class Chart extends BaseChart
{
    use ChartFeatures;

    /**
     * Chartjs dataset class.
     *
     * @var object
     */
    public $dataset = Dataset::class;

    /**
     * Initiates the Chartjs Line Chart.
     *
     * @return self
     */
    public function __construct()
    {
        parent::__construct();

        $this->container = 'charts::chartjs.container';
        $this->script = 'charts::chartjs.script';

        $this->options([
            'maintainAspectRatio' => false,
            'scales'              => [
                'x' => [ // 3.x에서는 xAxes 대신 x로 설정
                    'beginAtZero' => true,
                ],
                'y' => [ // 3.x에서는 yAxes 대신 y로 설정
                    'beginAtZero' => true,
                ],
            ]
        ]);
    }
}
