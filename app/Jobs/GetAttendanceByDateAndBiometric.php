<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use stdClass;

class GetAttendanceByDateAndBiometric implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tanggal, $table, $biometric;
    public $tries = 5;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $tanggal, string $table, stdClass $biometric)
    {
        $this->tanggal = $tanggal;
        $this->table = $table;
        $this->biometric = $biometric;
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addMinutes(10);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->getAttendance();
        $this->recalculateTelat();
        $biometric_name = $this->biometric->name;
        $biometric_ip = $this->biometric->ipaddress;
        Log::info("'GetAttendanceByDateAndBiometric: $this->tanggal $biometric_ip $biometric_name", ['table' => $this->table]);
    }

    public function getAttendance()
    {
        $mesinabsensi = $this->biometric;
        $gagalMesin = [];
        if (empty($mesinabsensi)) {
            return "Finger Print Machine not register or not enable";
        }

        $msg = "Sync data from machine " . $mesinabsensi->name . "(" . $mesinabsensi->ipaddress . ":" . $mesinabsensi->port . ")";
        $Connect = @fsockopen($mesinabsensi->ipaddress, $mesinabsensi->port, $errno, $errstr, 1);
        if ($Connect) {
            $msg .= "\n SUCCESS connect machine";
            if (!isset($Key)) $Key = "0";
            $soap_request = "<GetAttLog>
                                <ArgComKey xsi:type=\"xsd:integer\">" . $Key . "</ArgComKey>
                                <Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg>
                            </GetAttLog>";
            $newLine = "\r\n";
            fputs($Connect, "POST /iWsService HTTP/1.0" . $newLine);
            fputs($Connect, "Content-Type: text/xml" . $newLine);
            fputs($Connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($Connect, $soap_request . $newLine);
            $buffer = "";
            while ($Response = fgets($Connect, 1024)) {
                $buffer = $buffer . $Response;
            }

            fclose($Connect);
            $Response = $buffer;
            $buffer = $this->Parse_Data($buffer, "<GetAttLogResponse>", "</GetAttLogResponse>");
            $buffer = explode("\r\n", $buffer);

            if (count($buffer) < 10) {
                $msg .= "\n RESPONSE DATA: \n" . $Response . " \n";
            } else {
                $msg .= "\n RESPONSE DATA: " . count($buffer) . " log data \n";
            }
            $users = User::get();
            for ($a = 0; $a < count($buffer); $a++) {
                $data = $this->Parse_Data($buffer[$a], "<Row>", "</Row>");

                $employee_id = substr($this->Parse_Data($data, "<PIN>", "</PIN>"), 0, 4);
                $datetime = $this->Parse_Data($data, "<DateTime>", "</DateTime>");
                $status = $this->Parse_Data($data, "<Status>", "</Status>");
                $date = date("Y-m-d", strtotime($datetime));
                $time = date("H:i:s", strtotime($datetime));
                $day = date("w", strtotime($datetime));

                if ($date == date("Y-m-d", strtotime($this->tanggal))) {
                    $cek_data_att = DB::table($this->table)->where('nip', $employee_id)->where('tanggal', date("Y-m-d", strtotime($this->tanggal)))->first();
                    foreach ($users as $user) {
                        if ($user->nopeg == $employee_id) {
                            if (empty($cek_data_att)) {
                                if ($time < '10:00:00') {
                                    DB::table($this->table)->insert([
                                        'nip' => $employee_id,
                                        'tanggal' => $date,
                                        'hari' => $day,
                                        'jam_masuk' => $datetime,
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ]);
                                    if ($user->telegram_id) {
                                        try {
                                            $user->notify((new \App\Notifications\AttendanceNotification())->delay(now()->addSeconds(5)));
                                        } catch (\Throwable $th) {
                                            Log::info($th->getMessage());
                                        }
                                    }
                                } else if ($time >= '10:00:00' && $time < '15:00:00') {
                                    DB::table($this->table)->insert([
                                        'nip' => $employee_id,
                                        'tanggal' => $date,
                                        'hari' => $day,
                                        'jam_siang' => $datetime,
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ]);
                                    if ($user->telegram_id) {
                                        try {
                                            $user->notify((new \App\Notifications\AttendanceNotification())->delay(now()->addSeconds(5)));
                                        } catch (\Throwable $th) {
                                            Log::info($th->getMessage());
                                        }
                                    }
                                } else if ($time >= '15:00:01' && $time <= '23:59:00') {
                                    DB::table($this->table)->insert([
                                        'nip' => $employee_id,
                                        'tanggal' => $date,
                                        'hari' => $day,
                                        'jam_pulang' => $datetime,
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ]);
                                    if ($user->telegram_id) {
                                        try {
                                            $user->notify((new \App\Notifications\AttendanceNotification())->delay(now()->addSeconds(5)));
                                        } catch (\Throwable $th) {
                                            Log::info($th->getMessage());
                                        }
                                    }
                                }
                            } else if ($cek_data_att->modify_by != 1) {
                                $data = DB::table($this->table)->where('nip', $employee_id)->where('tanggal', $date);
                                $data2 = $data->first();

                                if ($time < '10:00:00') {
                                    if ($user->telegram_id && $data2->jam_masuk == null) {
                                        if ($user->telegram_id) {
                                            try {
                                                $user->notify((new \App\Notifications\AttendanceNotification())->delay(now()->addSeconds(5)));
                                            } catch (\Throwable $th) {
                                                Log::info($th->getMessage());
                                            }
                                        }
                                    }
                                    $data->update([
                                        'jam_masuk' => $datetime,
                                        'updated_at' => now()
                                    ]);
                                } else if ($time >= '10:00:00' && $time < '15:00:00') {
                                    if ($user->telegram_id && $data2->jam_masuk == null) {
                                        if ($user->telegram_id) {
                                            try {
                                                $user->notify((new \App\Notifications\AttendanceNotification())->delay(now()->addSeconds(5)));
                                            } catch (\Throwable $th) {
                                                Log::info($th->getMessage());
                                            }
                                        }
                                    }
                                    $data->update([
                                        'jam_siang' => $datetime,
                                        'updated_at' => now()
                                    ]);
                                } else if ($time >= '15:00:01' && $time <= '23:59:00') {
                                    if ($user->telegram_id && $data2->jam_masuk == null) {
                                        if ($user->telegram_id) {
                                            try {
                                                $user->notify((new \App\Notifications\AttendanceNotification())->delay(now()->addSeconds(5)));
                                            } catch (\Throwable $th) {
                                                Log::info($th->getMessage());
                                            }
                                        }
                                    }
                                    $data->update([
                                        'jam_pulang' => $datetime,
                                        'updated_at' => now()
                                    ]);
                                }
                            } else {
                                continue;
                            }
                        }
                    }
                    $cek_status = DB::table($this->table)->where(['tanggal' => $date])->where('modify_by', '=', 1)->get();
                    foreach ($cek_status as $cs) {
                        // Jika full terisi
                        if (!empty($cs->jam_masuk) && !empty($cs->jam_siang) && !empty($cs->jam_pulang)) {
                            DB::table($this->table)->where(['nip' => $cs->nip, 'tanggal' => $date])->update([
                                'status' => 1,
                                'durasi' => '08:00:00',
                                'updated_at' => now()
                            ]);
                        }
                        // Jika tidak ada sama sekali
                        else if (empty($cs->jam_masuk) && empty($cs->jam_siang) && empty($cs->jam_pulang)) {
                            DB::table($this->table)->where(['nip' => $cs->nip, 'tanggal' => $date])->update([
                                'status' => 0,
                                'durasi' => '00:00:00',
                                'updated_at' => now()
                            ]);
                        }
                        // Jika hanya ada sore yang terisi
                        else if (empty($cs->jam_masuk) && empty($cs->jam_siang) && !empty($cs->jam_pulang)) {
                            DB::table($this->table)->where(['nip' => $cs->nip, 'tanggal' => $date])->update([
                                'status' => 0,
                                'durasi' => '00:00:00',
                                'updated_at' => now()
                            ]);
                        }
                        // Jika hanya ada siang yang terisi
                        else if (empty($cs->jam_masuk) && !empty($cs->jam_siang) && empty($cs->jam_pulang)) {
                            DB::table($this->table)->where(['nip' => $cs->nip, 'tanggal' => $date])->update([
                                'status' => 0,
                                'durasi' => '00:00:00',
                                'updated_at' => now()
                            ]);
                        }
                        // Jika hanya ada pagi yang terisi
                        else if (!empty($cs->jam_masuk) && empty($cs->jam_siang) && empty($cs->jam_pulang)) {
                            DB::table($this->table)->where(['nip' => $cs->nip, 'tanggal' => $date])->update([
                                'status' => 0,
                                'durasi' => '00:00:00',
                                'updated_at' => now()
                            ]);
                        }
                        // Jika hanya ada siang dan sore terisi
                        else if (empty($cs->jam_masuk) && !empty($cs->jam_siang) && !empty($cs->jam_pulang)) {
                            DB::table($this->table)->where(['nip' => $cs->nip, 'tanggal' => $date])->update([
                                'status' => 0,
                                'durasi' => '04:00:00',
                                'updated_at' => now()
                            ]);
                        }
                        // Jika hanya ada pagi dan sore terisi
                        else if (!empty($cs->jam_masuk) && empty($cs->jam_siang) && !empty($cs->jam_pulang)) {
                            DB::table($this->table)->where(['nip' => $cs->nip, 'tanggal' => $date])->update([
                                'status' => 0,
                                'durasi' => '04:00:00',
                                'updated_at' => now()
                            ]);
                        }
                        // Jika hanya ada pagi dan siang terisi
                        else if (!empty($cs->jam_masuk) && !empty($cs->jam_siang) && empty($cs->jam_pulang)) {
                            DB::table($this->table)->where(['nip' => $cs->nip, 'tanggal' => $date])->update([
                                'status' => 0,
                                'durasi' => '04:00:00',
                                'updated_at' => now()
                            ]);
                        }
                    }
                }
            }
        } else {
            $msg .= "\n FAILED connect machine: " . $errno . " " . $errstr;
            $last_connect = date('Y-m-d H:i:s');
            $last_response = $msg;
            $save_log = DB::table('biometricmachine')->where('id', $mesinabsensi->id)->update([
                'last_connect' => $last_connect,
                'last_response' => $last_response,
            ]);
            array_push($gagalMesin, 'Gagal Sinkron pada Mesin : ' . $mesinabsensi->name . ' (' . $mesinabsensi->ipaddress . ')');
        }
        $last_connect = date('Y-m-d H:i:s');
        $last_response = $msg;

        DB::table('biometricmachine')->where('id', $mesinabsensi->id)->update([
            'last_connect' => $last_connect,
            'last_response' => $last_response,
        ]);

        $gagalMesin = implode("<br>", $gagalMesin);
    }


    public function Parse_Data($data, $p1, $p2)
    {
        $data = " " . $data;
        $hasil = "";
        $awal = strpos($data, $p1);
        if ($awal != "") {
            $akhir = strpos(strstr($data, $p1), $p2);
            if ($akhir != "") {
                $hasil = substr($data, $awal + strlen($p1), $akhir - strlen($p1));
            }
        }
        return $hasil;
    }

    public function recalculateTelat()
    {
        $date = $this->tanggal;
        $cek_status = DB::table($this->table)->where(['tanggal' => $date])->where('modify_by', '=', 0)->get();
        foreach ($cek_status as $cs) {
            // Jika full terisi
            if (!empty($cs->jam_masuk) && !empty($cs->jam_siang) && !empty($cs->jam_pulang)) {
                DB::table($this->table)->where(['nip' => $cs->nip, 'tanggal' => $date])->update([
                    'status' => 1,
                    'durasi' => '08:00:00',
                    'telat_masuk' => lateMasuk($cs->jam_masuk, $cs->jam_siang, $cs->hari),
                    'telat_siang' => lateSiang2($cs->jam_siang, $cs->jam_pulang, $cs->hari),
                    'updated_at' => now()
                ]);
            }
            // Jika tidak ada sama sekali
            else if (empty($cs->jam_masuk) && empty($cs->jam_siang) && empty($cs->jam_pulang)) {
                DB::table($this->table)->where(['nip' => $cs->nip, 'tanggal' => $date])->update([
                    'status' => 0,
                    'durasi' => '00:00:00',
                    'telat_masuk' => lateMasuk($cs->jam_masuk, $cs->jam_siang, $cs->hari),
                    'telat_siang' => lateSiang2($cs->jam_siang, $cs->jam_pulang, $cs->hari),
                    'updated_at' => now()
                ]);
            }
            // Jika hanya ada sore yang terisi
            else if (empty($cs->jam_masuk) && empty($cs->jam_siang) && !empty($cs->jam_pulang)) {
                DB::table($this->table)->where(['nip' => $cs->nip, 'tanggal' => $date])->update([
                    'status' => 0,
                    'durasi' => '00:00:00',
                    'telat_masuk' => lateMasuk($cs->jam_masuk, $cs->jam_siang, $cs->hari),
                    'telat_siang' => lateSiang2($cs->jam_siang, $cs->jam_pulang, $cs->hari),
                    'updated_at' => now()
                ]);
            }
            // Jika hanya ada siang yang terisi
            else if (empty($cs->jam_masuk) && !empty($cs->jam_siang) && empty($cs->jam_pulang)) {
                DB::table($this->table)->where(['nip' => $cs->nip, 'tanggal' => $date])->update([
                    'status' => 0,
                    'durasi' => '00:00:00',
                    'telat_masuk' => lateMasuk($cs->jam_masuk, $cs->jam_siang, $cs->hari),
                    'telat_siang' => lateSiang2($cs->jam_siang, $cs->jam_pulang, $cs->hari),
                    'updated_at' => now()
                ]);
            }
            // Jika hanya ada pagi yang terisi
            else if (!empty($cs->jam_masuk) && empty($cs->jam_siang) && empty($cs->jam_pulang)) {
                DB::table($this->table)->where(['nip' => $cs->nip, 'tanggal' => $date])->update([
                    'status' => 0,
                    'durasi' => '00:00:00',
                    'telat_masuk' => lateMasuk($cs->jam_masuk, $cs->jam_siang, $cs->hari),
                    'telat_siang' => lateSiang2($cs->jam_siang, $cs->jam_pulang, $cs->hari),
                    'updated_at' => now()
                ]);
            }
            // Jika hanya ada siang dan sore terisi
            else if (empty($cs->jam_masuk) && !empty($cs->jam_siang) && !empty($cs->jam_pulang)) {
                DB::table($this->table)->where(['nip' => $cs->nip, 'tanggal' => $date])->update([
                    'status' => 0,
                    'durasi' => '04:00:00',
                    'telat_masuk' => lateMasuk($cs->jam_masuk, $cs->jam_siang, $cs->hari),
                    'telat_siang' => lateSiang2($cs->jam_siang, $cs->jam_pulang, $cs->hari),
                    'updated_at' => now()
                ]);
            }
            // Jika hanya ada pagi dan sore terisi
            else if (!empty($cs->jam_masuk) && empty($cs->jam_siang) && !empty($cs->jam_pulang)) {
                DB::table($this->table)->where(['nip' => $cs->nip, 'tanggal' => $date])->update([
                    'status' => 0,
                    'durasi' => '04:00:00',
                    'telat_masuk' => lateMasuk($cs->jam_masuk, $cs->jam_siang, $cs->hari),
                    'telat_siang' => lateSiang2($cs->jam_siang, $cs->jam_pulang, $cs->hari),
                    'updated_at' => now()
                ]);
            }
            // Jika hanya ada pagi dan siang terisi
            else if (!empty($cs->jam_masuk) && !empty($cs->jam_siang) && empty($cs->jam_pulang)) {
                DB::table($this->table)->where(['nip' => $cs->nip, 'tanggal' => $date])->update([
                    'status' => 0,
                    'durasi' => '04:00:00',
                    'telat_masuk' => lateMasuk($cs->jam_masuk, $cs->jam_siang, $cs->hari),
                    'telat_siang' => lateSiang2($cs->jam_siang, $cs->jam_pulang, $cs->hari),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
