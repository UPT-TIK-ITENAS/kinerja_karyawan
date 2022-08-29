<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

    class BiometricController extends Controller
    {
    public function SyncAndInsertBiometric(Request $request)
    {
        $listmesinabsensi = DB::table('biometricmachine')->where('status', 'enable')->get();
        if (empty($listmesinabsensi)) {
            return "Finger Print Machine not register or not enable";
        }

        foreach ($listmesinabsensi as $mesinabsensi) {
            $msg = "Sync data from machine " . $mesinabsensi->name . "(" . $mesinabsensi->ipaddress . ":" . $mesinabsensi->port . ")";
            $Connect = fsockopen($mesinabsensi->ipaddress, $mesinabsensi->port, $errno, $errstr, 1);
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
                // return $msg;
                // //looping setiap baris data
                $for_array = [];
                for ($a = 0; $a < count($buffer); $a++) {
                    $data = $this->Parse_Data($buffer[$a], "<Row>", "</Row>");

                    $employee_id = substr($this->Parse_Data($data, "<PIN>", "</PIN>"), 0, 4);
                    $datetime = $this->Parse_Data($data, "<DateTime>", "</DateTime>");
                    $status = $this->Parse_Data($data, "<Status>", "</Status>");
                    $date = date("Y-m-d", strtotime($datetime));
                    $time = date("H:i:s", strtotime($datetime));
                    $day = date("w", strtotime($datetime));

                    if ($date == date("Y-m-d", strtotime($request->tanggal))) {
                        $cek_data_att = DB::table('attendance_baru')->where('nip', $employee_id)->where('tanggal', date("Y-m-d", strtotime($request->tanggal)))->first();
                        $users = DB::table('users')->get();
                        foreach($users as $user){
                            if($user->nopeg == $employee_id){
                                if (empty($cek_data_att)) {
                                    if ($time < '12:45:00') {
                                        $insert_att = DB::table('attendance_baru')->insert([
                                            'nip' => $employee_id,
                                            'tanggal' => $date,
                                            'hari' => $day,
                                            'jam_masuk' => $datetime,
                                        ]);
                                    } else if ($time >= '12:45:00' && $time < '15:30:00') {
                                        $insert_att = DB::table('attendance_baru')->insert([
                                            'nip' => $employee_id,
                                            'tanggal' => $date,
                                            'hari' => $day,
                                            'jam_siang' => $datetime,
                                        ]);
                                    } else if ($time >= '15:30:00' && $time <= '23:59:00') {
                                        $insert_att = DB::table('attendance_baru')->insert([
                                            'nip' => $employee_id,
                                            'tanggal' => $date,
                                            'hari' => $day,
                                            'jam_pulang' => $datetime,
                                        ]);
                                    }
                                } else {
                                    if ($time < '12:45:00') {
                                        $upd_att = DB::table('attendance_baru')->where('nip', $employee_id)->where('tanggal', $date)->update([
                                            'jam_masuk' => $datetime,
                                        ]);
                                    } else if ($time >= '12:45:00' && $time < '15:30:00') {
                                        $upd_att = DB::table('attendance_baru')->where('nip', $employee_id)->where('tanggal', $date)->update([
                                            'jam_siang' => $datetime,
                                        ]);
                                    } else if ($time >= '15:30:00' && $time <= '23:59:00') {
                                        $upd_att = DB::table('attendance_baru')->where('nip', $employee_id)->where('tanggal', $date)->update([
                                            'jam_pulang' => $datetime,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $msg .= "\n FAILED connect machine: " . $errno . " " . $errstr;
                return $msg;
            }
            $last_connect = date('Y-m-d H:i:s');
            $last_response = $msg;

            $save_log = DB::table('biometricmachine')->where('id', $mesinabsensi->id)->update([
                'last_connect' => $last_connect,
                'last_response' => $last_response,
            ]);
        }
        return redirect()->route('admin.datapresensi')->with('success', 'Sinkronisasi Berhasil!');
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
}
