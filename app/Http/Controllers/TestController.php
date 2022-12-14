<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    protected $tanggal = '2023-01-11';
    protected $table = 'attendance';

    public function test()
    {
        $listmesinabsensi = DB::table('biometricmachine')->where('status', 'enable')->get();
        $gagalMesin = [];
        if (empty($listmesinabsensi)) {
            return "Finger Print Machine not register or not enable";
        }
        $hasil = [];
        foreach ($listmesinabsensi as $key => $mesinabsensi) {
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

                for ($a = 0; $a < count($buffer); $a++) {
                    $data = $this->Parse_Data($buffer[$a], "<Row>", "</Row>");
                    $employee_id = substr($this->Parse_Data($data, "<PIN>", "</PIN>"), 0, 4);
                    $datetime = $this->Parse_Data($data, "<DateTime>", "</DateTime>");
                    $status = $this->Parse_Data($data, "<Status>", "</Status>");
                    $date = date("Y-m-d", strtotime($datetime));
                    $time = date("H:i:s", strtotime($datetime));
                    $day = date("w", strtotime($datetime));

                    if ($employee_id == '1776' && date("Y", strtotime($datetime)) == "2023") {
                        // if ($date == date("Y-m-d", strtotime($this->tanggal))) {
                        // $cek_data_att = DB::table($this->table)->where('nip', '1815')->where('tanggal', date("Y-m-d", strtotime($this->tanggal)))->first();
                        $hasil[] = "$employee_id $datetime $status";
                    }
                }
            } else {
                $msg .= "\n FAILED connect machine: " . $errno . " " . $errstr;
                // return $msg;
                $last_connect = date('Y-m-d H:i:s');
                $last_response = $msg;
                $save_log = DB::table('biometricmachine')->where('id', $mesinabsensi->id)->update([
                    'last_connect' => $last_connect,
                    'last_response' => $last_response,
                ]);
                array_push($gagalMesin, 'Gagal Sinkron pada Mesin : ' . $mesinabsensi->name . ' (' . $mesinabsensi->ipaddress . ')');
                continue;
            }
            $last_connect = date('Y-m-d H:i:s');
            $last_response = $msg;

            $save_log = DB::table('biometricmachine')->where('id', $mesinabsensi->id)->update([
                'last_connect' => $last_connect,
                'last_response' => $last_response,
            ]);
        }
        $gagalMesin = implode("<br>", $gagalMesin);
        dd($hasil);
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

    public function test2()
    {
        $array = [
            '1816 17:00:00',
            '1815 17:00:00',
            '1815 17:01:00',
            '1816 17:02:00',
            '1815 17:02:00',
        ];

        $result = null;
        foreach ($array as $value) {
            if (strpos($value, '1815') !== false) {
                $result = $value;
                break;
            }
        }
        echo $result;
    }

    public function test3()
    {
        $biometric = DB::table('biometricmachine')->where('id', 1)->first();
        dispatch(new \App\Jobs\GetAttendanceByDateAndBiometric('2023-01-12', 'attendance_baru', $biometric));
        dd($biometric);
    }
}
