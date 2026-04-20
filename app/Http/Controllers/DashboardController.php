<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Totals
        $totalStudents = DB::table('students')->count();

        $maleStudents = DB::table('students')
            ->where('gender', 'male')
            ->where('is_active', 1)
            ->count();

        $femaleStudents = DB::table('students')
            ->where('gender', 'female')
            ->where('is_active', 1)
            ->count();

        $graduatedAndMutatedStudents = DB::table('students')
            ->whereIn('status_lulus', ['lulus', 'mutasi'])
            ->count();

        // Group by enrollment_year
        $studentsByYear = DB::table('students')
            ->select('enrollment_year', DB::raw('count(*) as total'))
            ->groupBy('enrollment_year')
            ->orderBy('enrollment_year', 'asc')
            ->get();

        // Prepare chart data
        $chartLabels = $studentsByYear->pluck('enrollment_year');
        $chartData = $studentsByYear->pluck('total');

        // Server Info
        $phpVersion = phpversion();
        $mysqlVersion = DB::select('select version() as version')[0]->version ?? 'Unknown';

        return view('dashboard', compact(
            'totalStudents', 'maleStudents', 'femaleStudents', 'graduatedAndMutatedStudents', 'chartLabels', 'chartData', 'phpVersion', 'mysqlVersion'
        ));
    }
}
