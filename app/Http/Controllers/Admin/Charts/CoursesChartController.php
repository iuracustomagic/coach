<?php

namespace App\Http\Controllers\Admin\Charts;

use Backpack\CRUD\app\Http\Controllers\ChartController;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;




/**
 * Class CoursesChartController
 * @package App\Http\Controllers\Admin\Charts
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CoursesChartController extends ChartController
{

    public function setup()
    {
        $this->chart = new Chart();
        $userAvailableCourses = \App\Models\UserAvailableCourses::all()->count();
        $userResults = \App\Models\UserResults::all()->count();
        $userCoursePassed = \App\Models\UserResults::all()->where('course_is_passed', 1)->count();
        $courseStarted = $userResults - $userCoursePassed;
        $coursesNotStarted = $userAvailableCourses - $userResults;

        // MANDATORY. Set the labels for the dataset points
        $this->chart->dataset('Red', 'pie', [$userCoursePassed, $courseStarted, $coursesNotStarted])
            ->backgroundColor([
                'rgb(77, 189, 116)',
                'rgb(70, 127, 208)',
                'rgb(96, 92, 168)',
            ]);

        $this->chart->labels([
            'Пройдено',
            'Начато',
            'Не начато',
        ]);

        // RECOMMENDED. Set URL that the ChartJS library should call, to get its data using AJAX.
        $this->chart->load(backpack_url('charts/courses'));

        // OPTIONAL
         $this->chart->displayAxes(false);
         $this->chart->displayLegend(true);

    }

    /**
     * Respond to AJAX calls with all the chart data points.
     *
     * @return json
     */
    // public function data()
    // {
    //     $users_created_today = \App\User::whereDate('created_at', today())->count();

    //     $this->chart->dataset('Users Created', 'bar', [
    //                 $users_created_today,
    //             ])
    //         ->color('rgba(205, 32, 31, 1)')
    //         ->backgroundColor('rgba(205, 32, 31, 0.4)');
    // }
}
