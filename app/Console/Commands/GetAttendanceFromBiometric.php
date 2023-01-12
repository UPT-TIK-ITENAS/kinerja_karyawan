<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetAttendanceFromBiometric extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get attendance from all biometric machine';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $biometric = DB::table('biometricmachine')->where('status', 'enable')->get();
        $current_date = date('Y-m-d');
        foreach ($biometric as $key => $value) {
            dispatch(new \App\Jobs\GetAttendanceByDateAndBiometric($current_date, 'attendance_baru', $value))->delay(10);
            $this->info("Attendance successfully retrieved from biometric $value->name");
        }
        return Command::SUCCESS;
    }
}
