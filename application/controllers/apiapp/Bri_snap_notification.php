<?php
/**
 * BRI SNAP BI - Push Notification Payment (Intrabank)
 *
 * Endpoint: POST /snap/v1.0/transfer-va/notify-payment-intrabank
 * Purpose : Menerima push notifikasi pembayaran VA dari BRI, format resmi SNAP BI v3.1.
 *            Menggantikan logic di Notification.php lama (field brivaNo/billAmount/dll).
 *
 * Header WAJIB (verifikasi):
 *   - Authorization  : Bearer {accessToken dari /access-token/b2b}
 *   - X-TIMESTAMP    : timestamp BRI
 *   - X-SIGNATURE    : HMAC_SHA512, simetris, stringToSign =
 *                      method + ":" + endpoint + ":" + accessToken + ":" + sha256(body) + ":" + timestamp
 *   - X-PARTNER-ID   : partnerId BRI
 *   - CHANNEL-ID     : channel id (umumnya 00004)
 *   - X-EXTERNAL-ID  : unique id per request
 *
 * Body BARU (mapping dr field lama):
 *   partnerServiceId                              -> (was: gak ada, referensi prefix VA)
 *   customerNo                                    -> (was: bagian dari brivaNo)
 *   virtualAccountNo                              -> (was: brivaNo, 16 digit)
 *   trxDateTime                                   -> (was: transactionDateTime)
 *   paymentRequestId                              -> (was: journalSeq, jadi unique ref)
 *   additionalInfo.paymentAmount                  -> (was: billAmount)  MANDATORY
 *   additionalInfo.terminalId                      -> (was: terminalId)  enum 1-9
 *   additionalInfo.bankId                         -> (was: gak ada, "002" utk BRI)
 *   additionalInfo.idApp / passApp                -> (opsional)
 *
 * Response codes (service code "34"):
 *   2003400  Success
 *   4003400  Bad Request
 *   4003401  Invalid Field Format
 *   4003402  Invalid Mandatory Field
 *   4013400  Unauthorized. Verify Client Secret Fail
 *   4043416  Partner Not Found
 *   5003400  General Error
 *   5003402  Unknown Error
 *   5043400  Timeout
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, authorization, x-timestamp, x-signature, x-partner-id, channel-id, x-external-id, content-type');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');

require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

class Bri_snap_notification extends MY_Controller
{
    // BRI SNAP credentials dari tabel pengaturan_akun (bri_snap_client_id / bri_snap_client_secret)
    private $clientId      = '';
    private $clientSecret  = '';
    private $partnerId     = 'labschool';

    // Token endpoint & partnerServiceId disesuaikan dgn BRI Sandbox
    private $tokenEndpoint = '/snap/v1.0/access-token/b2b';
    private $notifEndpoint = '/snap/v1.0/transfer-va/notify-payment-intrabank';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Spp_payment_detail');
        $this->clientId     = $this->briSnapSetting('bri_snap_client_id');
        $this->clientSecret = $this->briSnapSetting('bri_snap_client_secret');
    }

    private function briSnapSetting($name)
    {
        $row = $this->mymodel->getbywhere('pengaturan_akun', 'name_setting', $name, 'row');
        return $row ? $row->value : '';
    }

    public function index()
    {
        $status = '';
        $headers = array();
        foreach (getallheaders() as $name => $value) {
            // lowercase: BRI kirim 'Channel-Id', 'X-External-Id', dll — cari case-insensitive
            $headers[strtolower($name)] = $value;
        }

        $rawBody = file_get_contents('php://input');

        // 1. Log raw request untuk debug (konsisten dgn Notification.php lama)
        $this->mymodel->insertid('bri_res', array('text' => '[notif] ' . $rawBody));

        // 2. Kumpulkan header wajib
        $authorization = isset($headers['authorization']) ? $headers['authorization'] : '';
        $timestamp     = isset($headers['x-timestamp'])   ? $headers['x-timestamp']   : '';
        $signature     = isset($headers['x-signature'])   ? $headers['x-signature']   : '';
        $partnerIdH    = isset($headers['x-partner-id'])  ? $headers['x-partner-id']  : '';
        $channelId     = isset($headers['channel-id'])    ? $headers['channel-id']    : '';
        $externalId    = isset($headers['x-external-id']) ? $headers['x-external-id'] : '';

        if (empty($authorization) || empty($timestamp) || empty($signature) || empty($partnerIdH) || empty($channelId) || empty($externalId)) {
            return $this->respondNotif('4013400', 'Unauthorized. Verify Client Secret Fail', null);
        }

        // 3. Parse Bearer token
        $bearerToken = '';
        if (stripos($authorization, 'Bearer ') === 0) {
            $bearerToken = trim(substr($authorization, 7));
        }
        if (empty($bearerToken)) {
            return $this->respondNotif('4013400', 'Unauthorized. Verify Client Secret Fail', null);
        }

        // 4. Verifikasi signature HMAC_SHA512 (simetris, clientSecret)
        $expectedSig = $this->generateSignatureVA('POST', $this->notifEndpoint, $bearerToken, $rawBody, $timestamp, $this->clientSecret);
        if (!hash_equals($expectedSig, $signature)) {
            return $this->respondNotif('4013400', 'Unauthorized. Verify Client Secret Fail', null);
        }

        return $this->processBody($rawBody);
    }

    /**
     * Proses body notifikasi payment VA (parse JSON s/d response SNAP BI).
     * Public agar bisa dipanggil dari controller lain
     * (mis. Bri_snap_notify_payment_intrabank saat body terdeteksi notifikasi).
     *
     * @param string $rawBody raw JSON dari php://input
     */
    public function processBody($rawBody)
    {
        // 5. Parse body
        $data_json = json_decode($rawBody, true);
        if (!$data_json) {
            return $this->respondNotif('4003400', 'Bad Request. Invalid JSON body', null);
        }

        // 6. Validasi mandatory fields (sesuai spec SNAP BI)
        $partnerServiceId  = isset($data_json['partnerServiceId'])  ? $data_json['partnerServiceId']  : '';
        $customerNo        = isset($data_json['customerNo'])        ? $data_json['customerNo']        : '';
        $virtualAccountNo  = isset($data_json['virtualAccountNo'])  ? $data_json['virtualAccountNo']  : '';
        $trxDateTime       = isset($data_json['trxDateTime'])       ? $data_json['trxDateTime']       : '';
        $paymentRequestId  = isset($data_json['paymentRequestId'])  ? $data_json['paymentRequestId']  : '';
        $additionalInfo    = isset($data_json['additionalInfo'])    ? $data_json['additionalInfo']    : array();

        if (empty($virtualAccountNo) || empty($trxDateTime)) {
            return $this->respondNotif('4003402', 'Invalid Mandatory Field. virtualAccountNo / trxDateTime required', null);
        }
        if (!isset($additionalInfo['paymentAmount'])) {
            return $this->respondNotif('4003402', 'Invalid Mandatory Field. additionalInfo.paymentAmount required', null);
        }

        $billAmount = $additionalInfo['paymentAmount'];
        $terminalId = isset($additionalInfo['terminalId']) ? $additionalInfo['terminalId'] : '';
        $bankId     = isset($additionalInfo['bankId'])     ? $additionalInfo['bankId']     : '';
        $idApp      = isset($additionalInfo['idApp'])      ? $additionalInfo['idApp']      : '';
        $passApp    = isset($additionalInfo['passApp'])    ? $additionalInfo['passApp']    : '';

        // 7. Insert raw response ke payment_response_bri (mapping dr field lama)
        $this->mymodel->insertid('payment_response_bri', array(
            'briva_no'         => $virtualAccountNo,
            'bill_amount'      => $billAmount,
            'transaction_date' => $trxDateTime,
            'journal_id'       => $paymentRequestId, // paymentRequestId dipakai sebagai unique ref pengganti journalSeq
            'terminal_id'      => $terminalId,
            'partner_service_id' => $partnerServiceId,
            'customer_no'      => $customerNo,
            'bank_id'          => $bankId,
            'id_app'           => $idApp,
            'pass_app'         => $passApp,
        ));

        // 8. Jalankan logic bisnis (mapping identik dgn Notification.php)
        $vaLookup = !empty($virtualAccountNo) ? $virtualAccountNo : $customerNo;

        try {
            $paymentStatus = $this->processPaymentNotification($vaLookup, $billAmount);
        } catch (Exception $e) {
            return $this->respondNotif('5003400', 'General Error. ' . $e->getMessage(), null);
        }

        // 9. Response sesuai spec SNAP BI
        $vaData = array(
            'partnerServiceId'   => $partnerServiceId,
            'customerNo'         => $customerNo,
            'virtualAccountNo'   => $virtualAccountNo,
            'inquiryRequestId'   => $paymentRequestId, // inquiry dan payment request id disatukan sesuai spec
            'paymentRequestId'   => $paymentRequestId,
            'trxDateTime'        => $trxDateTime,
            'paymentStatus'      => $paymentStatus, // '00' = Success, '01' = Pending
        );

        return $this->respondNotif('2003400', 'Success', $vaData);
    }

    /**
     * Process payment sesuai logic Notification.php lama.
     * Field sumber: virtualAccountNo (dipakai untuk lookup va_number di tabel).
     * Field billAmount dipakai dimana-mana (sama persis dgn Notification.php).
     *
     * @param string $vaLookup   virtualAccountNo atau fallback customerNo
     * @param mixed  $billAmount amount dari additionalInfo.paymentAmount
     * @return string paymentStatus code (00 = Success)
     */
    private function processPaymentNotification($vaLookup, $billAmount)
    {
        $ajaran_aktif = $this->mymodel->withquery(
            "select * from tahun_ajaran where tanggal_mulai <= '" . date('Y-m-d') . "' and tanggal_selesai >= '" . date('Y-m-d') . "'",
            'row'
        );

        $tipe_tagihan     = substr($vaLookup, 5, 2);
        $kode_pendaftaran = substr($vaLookup, 7, 2);
        $jenjang          = substr($vaLookup, 7, 2);
        $paymentStatus    = '00'; // default Success

        // ---- Tagihan lain (PSBB) ----
        if (($tipe_tagihan == '65' || $tipe_tagihan == '75' || $tipe_tagihan == '85' || $tipe_tagihan == '95') && $kode_pendaftaran != '01') {
            $jenjang = substr($vaLookup, 5, 2);
            if ($jenjang == '85') {
                $jenjang = 'sd';
            } elseif ($jenjang == '65') {
                $jenjang = 'smp';
            } elseif ($jenjang == '75') {
                $jenjang = 'sma';
            } elseif ($jenjang == '95') {
                $jenjang = 'ft';
            }

            $get_tagihan = $this->mymodel->withquery(
                "select t.*, tm.tipe_bank, tm.nama_transaksi, s.id_tahun_ajaran, s.id_kelas, s.nama_lengkap
                 from transaksi_lain_" . strtolower($jenjang) . " t
                 join siswa_" . strtolower($jenjang) . "_aktif s on t.id_siswa_aktif = s.id_siswa_" . strtolower($jenjang) . "_aktif
                 join transaksi_lain_" . strtolower($jenjang) . "_manajemen tm on t.id_transaksi_lain = tm.id
                 where t.va_number = '" . $vaLookup . "' and t.status_transaksi = 1",
                'row'
            );

            if (!empty($get_tagihan)) {
                $this->mymodel->update(
                    'transaksi_lain_' . $jenjang,
                    array(
                        'status_transaksi' => 2,
                        'tanggal_bayar'    => date('Y-m-d H:i:s'),
                        'file_kwitansi'    => $get_tagihan->kode_tagihan . '-' . str_replace(' ', '_', $get_tagihan->nama_lengkap) . '.pdf',
                    ),
                    'id',
                    $get_tagihan->id
                );
            }

            $this->cetak_kwitansi_ot(array(
                'jenjang'             => $jenjang,
                'nama_lengkap'        => $get_tagihan->nama_lengkap,
                'va_number'           => $get_tagihan->va_number,
                'kode_tagihan'        => $get_tagihan->kode_tagihan,
                'total_biaya'         => $get_tagihan->nominal_bayar,
                'total_biaya_terbilang' => ($get_tagihan->nominal_bayar) . ' Rupiah',
                'id_siswa'            => $get_tagihan->id_siswa_aktif,
                'jenis_kwitansi'      => $get_tagihan->nama_transaksi,
            ));

            return $paymentStatus;
        }

        // ---- Transaksi PSB ----
        $get_transaksi = $this->mymodel->withquery(
            "select * from transaksi where va_number = '" . $vaLookup . "' and status_transaksi = 0 order by id_transaksi DESC limit 1",
            'row'
        );
        if ($get_transaksi) {
            $no_transaksi = $get_transaksi->no_transaksi;
            $ajaran_aktif = $this->mymodel->withquery(
                "select * from tahun_ajaran where tanggal_mulai <= '" . date('Y-m-d') . "' and tanggal_selesai >= '" . date('Y-m-d') . "'",
                'row'
            );
            $tahun_pelajaran = date('y', strtotime($ajaran_aktif->tanggal_mulai . '+1 years')) . date('y', strtotime($ajaran_aktif->tanggal_selesai . '+1 years'));

            $this->mymodel->update(
                'transaksi',
                array('status_transaksi' => 1, 'updated_at' => date('Y-m-d H:i:s')),
                'id_transaksi',
                $get_transaksi->id_transaksi
            );

            $unit = '11';
            $get_no_urut = $this->mymodel->withquery(
                "select no_peserta, id_siswa_sd as id_siswa from siswa_sd where no_peserta like '%" . $tahun_pelajaran . "%' and no_peserta != '' and is_mutasi='2' order by no_peserta DESC",
                'row'
            );
            if (empty($get_no_urut)) {
                $no_urut = '0001';
            } else {
                $no_urut = (int) substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut + 1;
                $no_urut = sprintf('%04d', $no_urut);
            }
            $no_peserta = $tahun_pelajaran . $unit . $no_urut;

            $cek_no_peserta = $this->mymodel->withquery(
                "select no_peserta, id_siswa_sd as id_siswa, nama_lengkap, email, nama_ibu, nama_ayah, no_peserta from siswa_sd where no_transaksi = '" . $no_transaksi . "'",
                'row'
            );
            if (!empty($cek_no_peserta) && $cek_no_peserta->no_peserta == '') {
                $tahun_ajaran_psb = $this->mymodel->withquery("select * from tahun_ajaran_psb where id = '1'", 'row')->label;
                $gelombang = $this->mymodel->withquery("select * from web_periode_daftar where kelas = 'SD'", 'row')->gelombang;
                $this->mymodel->update(
                    'siswa_sd',
                    array('no_peserta' => $no_peserta, 'tahun_ajaran' => $tahun_ajaran_psb, 'gelombang' => $gelombang),
                    'id_siswa_sd',
                    $cek_no_peserta->id_siswa
                );
            }
            $id_siswa = $cek_no_peserta->id_siswa;
            $jalur = 'PSB';
            $tipe_siswa = 'sd';

            $this->cetak_kartu(array('tipe_siswa' => $tipe_siswa, 'id_siswa' => $id_siswa));
            $cek_transaksi = $this->mymodel->withquery(
                "select id_transaksi, no_transaksi, va_number, nama_bank, total_biaya, expired_datetime, status_transaksi from transaksi where no_transaksi = '" . $no_transaksi . "'",
                'row'
            );
            $nama_ortu = $cek_no_peserta->nama_ibu;
            if (empty($cek_no_peserta->nama_ibu)) {
                $nama_ortu = $cek_no_peserta->nama_ayah;
            }

            $this->cetak_kwitansi(array(
                'tipe_siswa' => $tipe_siswa,
                'jenjang' => $jenjang,
                'nama_ortu' => $nama_ortu,
                'nama_lengkap' => $cek_no_peserta->nama_lengkap,
                'va_number' => $cek_transaksi->va_number,
                'total_biaya' => $cek_transaksi->total_biaya,
                'total_biaya_terbilang' => ($cek_transaksi->total_biaya) . ' Rupiah',
                'id_siswa' => $id_siswa,
                'no_transaksi' => $get_transaksi->no_transaksi,
                'jalur' => $jalur,
                'jenis_kwitansi' => 'uang pendaftaran',
            ));

            $tahun_ajaran = $this->mymodel->getbywhere('tahun_ajaran_psb', 'id', '1', 'row')->label;
            $data_email = array(
                'email' => $cek_no_peserta->email,
                'nama_lengkap' => $cek_no_peserta->nama_lengkap,
                'tipe_pendaftaran' => 'PSB ' . strtoupper($tipe_siswa),
                'jenjang' => strtoupper($tipe_siswa),
                'transaksi' => $cek_transaksi,
                'nama_panitia' => 'Panitia PSB ' . strtoupper($tipe_siswa) . ' Labschool Cibubur ' . $tahun_ajaran,
                'kartu_peserta' => $tipe_siswa . '-' . $no_peserta . '-' . $cek_no_peserta->nama_lengkap . '.pdf',
                'kwitansi' => $cek_transaksi->no_transaksi . '-' . $cek_no_peserta->nama_lengkap . '.pdf',
            );
            if ($cek_transaksi->status_transaksi == '0') {
                $this->send_email_paid('', $data_email['email'], $data_email);
            }
        }

        // ---- Transaksi Daftar Ulang (cicilan) ----
        $get_transaksi = $this->mymodel->withquery(
            "select * from transaksi where va_number = '" . $vaLookup . "' and status_transaksi = 1 order by id_transaksi DESC limit 1",
            'row'
        );
        if ($get_transaksi) {
            $paymentAmount = (float) $get_transaksi->payment_amount + (float) $billAmount;
            if ($paymentAmount >= (float) $get_transaksi->total_biaya) {
                $this->mymodel->update(
                    'transaksi',
                    array('status_transaksi' => 1, 'updated_at' => date('Y-m-d H:i:s')),
                    'id_transaksi',
                    $get_transaksi->id_transaksi
                );
                $trx_id = explode('-', $get_transaksi->no_transaksi);
                $jenis_pembayaran = $trx_id[0];
                $jenjang = $trx_id[1];
                $id_siswa = $trx_id[3];
                $this->mymodel->update('status_daftar_ulang_sd', array('tgl_bayar' => date('Y-m-d H:i:s')), 'id_siswa_sd', $id_siswa);

                if ($jenjang == 'SD') {
                    $tipe_siswa = 'sd';
                    $get_siswa = $this->mymodel->getbywhere('siswa_sd', 'id_siswa_sd', $id_siswa, 'row');
                }
                $this->mymodel->update('transaksi', array('payment_amount' => $paymentAmount), 'id_transaksi', $get_transaksi->id_transaksi);

                if ($jenis_pembayaran == 'LDUI') {
                    $nama_ortu = $get_siswa->nama_ibu;
                    if (empty($get_siswa->nama_ibu)) {
                        $nama_ortu = $get_siswa->nama_ayah;
                    }
                    if ($get_siswa->is_mutasi == 2) {
                        $jalur = 'PSB';
                    } else {
                        $jalur = 'PSB MUTASI';
                    }

                    $this->cetak_kwitansi(array(
                        'tipe_siswa' => $tipe_siswa,
                        'jenjang' => $jenjang,
                        'nama_ortu' => $nama_ortu,
                        'nama_lengkap' => $get_siswa->nama_lengkap,
                        'email' => $get_siswa->email,
                        'va_number' => $get_transaksi->va_number,
                        'total_biaya' => $get_transaksi->total_biaya,
                        'total_biaya_terbilang' => ($get_transaksi->total_biaya) . ' Rupiah',
                        'id_siswa' => $id_siswa,
                        'no_transaksi' => $get_transaksi->no_transaksi,
                        'jalur' => $jalur,
                        'jenis_kwitansi' => 'uang pangkal',
                    ));

                    $data_email = array(
                        'email' => $get_siswa->email,
                        'nama_lengkap' => $get_siswa->nama_lengkap,
                        'tipe_pendaftaran' => 'PSB ' . strtoupper($tipe_siswa),
                        'jenjang' => strtoupper($tipe_siswa),
                        'nama_panitia' => 'Panitia PSB ' . strtoupper($tipe_siswa) . ' Labschool Cibubur ' . date('Y', strtotime('+1 years')) . '-' . date('Y', strtotime('+2 years')),
                        'kartu_siswa_sementara' => $tipe_siswa . '-' . $get_siswa->no_peserta . '-' . $get_siswa->nama_lengkap . '.pdf',
                        'kwitansi' => $get_transaksi->no_transaksi . '-' . $get_siswa->nama_lengkap . '.pdf',
                    );
                    $this->mymodel->update(
                        'status_daftar_ulang_sd',
                        array(
                            'status' => 2,
                            'kwitansi' => $data_email['kwitansi'],
                            'kartu_sementara' => $data_email['kartu_siswa_sementara'],
                            'tgl_bayar' => date('Y-m-d H:i:s'),
                        ),
                        'id_siswa_sd',
                        $id_siswa
                    );
                    if ($get_transaksi->status_transaksi == '1') {
                        $this->send_email_paid_daftar_ulang('', $data_email['email'], $data_email);
                    }
                }
            } elseif ($paymentAmount < (float) $get_transaksi->total_biaya) {
                $this->mymodel->update(
                    'transaksi',
                    array('status_transaksi' => 0, 'payment_amount' => $paymentAmount),
                    'id_transaksi',
                    $get_transaksi->id_transaksi
                );
            }
        }

        // ---- Transaksi SPP ----
        $get_transaksi_spp = $this->mymodel->withquery(
            "select * from transaksi_spp where va_number = '" . $vaLookup . "' and status_transaksi = '1' order by id_transaksi DESC",
            'row'
        );
        if ($get_transaksi_spp) {
            $trx_id = explode('-', $get_transaksi_spp->no_transaksi);
            $jenjang = $trx_id[1];
            $id_siswa = $trx_id[3];
            if ($jenjang == 'SD') {
                $tipe_siswa = 'sd';
                $get_siswa = $this->mymodel->getbywhere('siswa_sd', 'id_siswa_sd', $id_siswa, 'row');
            }
            $total_biaya = $get_transaksi_spp->total_biaya * $get_transaksi_spp->count_bill;

            $data_transaksi = array(
                'status_transaksi' => '2',
                'updated_at' => date('Y-m-d H:i:s'),
                'file_kwitansi' => $get_transaksi_spp->no_transaksi . '-' . str_replace(' ', '_', $get_transaksi_spp->user_name) . '.pdf',
            );

            $this->mymodel->update(
                'transaksi_spp',
                $data_transaksi,
                "status_transaksi = '1' and expired_datetime >= '" . date('Y-m-d H:i:s') . "' and va_number=",
                $vaLookup
            );

            // jenjang dari no_transaksi (mis. LISPP-sd-26_27-...), bukan hardcode SD
            $jenjang = strtoupper($trx_id[1]);

            // CEK UJIAN TERDEKAT: boleh_ujian = YA bila invoice bulan cutoff (setting_sync_ujian) lunas.
            // Null-guard wajib: siswa bayar bulan != bulan cutoff -> $cek_transaksi kosong.
            $setting_ujian = $this->mymodel->getbywhere('setting_sync_ujian', 'jenjang', strtolower($jenjang), 'row');
            if (!empty($setting_ujian) && !empty($ajaran_aktif)) {
                $bulan_ini = formatBulan($setting_ujian->tanggal_terakhir_bayar);
                $cek_transaksi = $this->mymodel->withquery(
                    "select status_transaksi, user_name, bulan, id_siswa_aktif from transaksi_spp where id_spp = '" . $get_transaksi_spp->id_spp . "' and no_transaksi like '%" . $jenjang . "%' and status_transaksi = '2' and bulan = '" . $bulan_ini . "' and id_tahun_ajaran = '" . $ajaran_aktif->id_tahun_ajaran . "' order by id_transaksi DESC",
                    'row'
                );
                if (!empty($cek_transaksi) && $cek_transaksi->status_transaksi == 2) {
                    $get_siswa_spp = $this->mymodel->withquery(
                        "select nomor_peserta_ujian from siswa_" . strtolower($jenjang) . "_aktif where id_siswa_" . strtolower($jenjang) . "_aktif = '" . $cek_transaksi->id_siswa_aktif . "'",
                        'row'
                    );
                    if (!empty($get_siswa_spp) && !empty($get_siswa_spp->nomor_peserta_ujian)) {
                        $this->mymodel->update(
                            'ujian_ruang_detail',
                            array('nama_siswa' => $cek_transaksi->user_name, 'boleh_ujian' => 'YA'),
                            'nomor_peserta_ujian',
                            $get_siswa_spp->nomor_peserta_ujian
                        );
                    }
                }
            }

            if (!$this->_update_spp_detail($get_transaksi_spp, $data_transaksi['updated_at'])) {
                $this->db->trans_rollback();
                return $this->response(array('responseCode' => '5000', 'responseDescription' => 'Payment detail SPP update failed'), '500');
            }

            $this->cetak_kwitansi_spp(array(
                'tipe_siswa' => $tipe_siswa,
                'jenjang' => $jenjang,
                'nama_lengkap' => $get_transaksi_spp->user_name,
                'va_number' => $get_transaksi_spp->va_number,
                'total_biaya' => $total_biaya,
                'total_biaya_terbilang' => ($total_biaya) . ' Rupiah',
                'id_siswa' => $get_transaksi_spp->id_siswa_aktif,
                'detail_bulan' => $get_transaksi_spp->detail_bulan,
                'no_transaksi' => $get_transaksi_spp->no_transaksi,
            ));
        }

        return $paymentStatus;
    }

    /**
     * Generate signature VA (HMAC_SHA512, simetris).
     * Pola: method + ":" + endpoint + ":" + token + ":" + sha256(body) + ":" + timestamp
     */
    private function generateSignatureVA($method, $endpoint, $token, $body, $timestamp, $clientSecret)
    {
        $hashedBody = hash('sha256', $body);
        $stringToSign = $method . ':' . $endpoint . ':' . $token . ':' . $hashedBody . ':' . $timestamp;
        return base64_encode(hash_hmac('sha512', $stringToSign, $clientSecret, true));
    }

    private function respondNotif($code, $message, $vaData)
    {
        $msg = array(
            'responseCode'    => $code,
            'responseMessage' => $message,
        );
        if ($vaData !== null) {
            $msg['virtualAccountData'] = $vaData;
        }

        $this->mymodel->insertid('bri_res', array('text' => '[notif_resp] ' . json_encode($msg)));

        // HTTP status dari prefix responseCode (MY_Controller::response() mengabaikan
        // param status, jadi kirim via output langsung).
        $prefix = substr($code, 0, 3);
        $http   = 200;
        if ($prefix === '400') {
            $http = 400;
        } elseif ($prefix === '401') {
            $http = 401;
        } elseif ($prefix === '404') {
            $http = 404;
        } elseif ($prefix === '500') {
            $http = 500;
        } elseif ($prefix === '504') {
            $http = 504;
        }

        $this->output
            ->set_status_header($http)
            ->set_content_type('application/json')
            ->set_output(json_encode($msg));
    }

    // ---- Helper methods (duplikat dari Notification.php lama) ----

    function cetak_kartu($get = '')
    {
        $ch = curl_init();
        $header[] = 'Content-Type: application/json';
        if ($get) {
            $url = site_url('/apiapp/siswa/export_pdf_siswa') . '?id_siswa=' . $get['id_siswa'] . '&tipe_siswa=' . $get['tipe_siswa'];
            curl_setopt($ch, CURLOPT_URL, $url);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36');
        curl_exec($ch);
        curl_close($ch);
    }

    function cetak_kwitansi($get = '')
    {
        $ch = curl_init();
        $header[] = 'Content-Type: application/json';
        if ($get) {
            $url = site_url('/apiapp/siswa/export_pdf_kwitansi') . '?' . http_build_query($get);
            curl_setopt($ch, CURLOPT_URL, $url);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36');
        curl_exec($ch);
        curl_close($ch);
    }

    function cetak_kwitansi_spp($get = '')
    {
        $ch = curl_init();
        $header[] = 'Content-Type: application/json';
        if ($get) {
            $url = site_url('/apiapp/siswa/export_pdf_kwitansi_spp') . '?' . http_build_query($get);
            curl_setopt($ch, CURLOPT_URL, $url);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36');
        curl_exec($ch);
        curl_close($ch);
    }

    function cetak_kwitansi_ot($get = '')
    {
        $ch = curl_init();
        $header[] = 'Content-Type: application/json';
        if ($get) {
            $url = site_url('/apiapp/tagihan_lain/export_pdf_kwitansi') . '?' . http_build_query($get);
            curl_setopt($ch, CURLOPT_URL, $url);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36');
        $rs = curl_exec($ch);
        curl_close($ch);
        return $rs;
    }

    public function send_email_paid($file = '', $to = '', $data)
    {
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = 'smtp.office365.com';
        $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply@labschoolcibubur.sch.id';
        $mail->Password = ''; // [JACOS] TODO(manual): password SMTP Jacos — kredensial lama dihapus
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->addReplyTo('noreply@labschoolcibubur.sch.id', 'Labschool Cibubur');
        $mail->setFrom('noreply@labschoolcibubur.sch.id', 'Labschool Cibubur');
        $mail->addAddress($to);
        $mail->Subject = '[No Reply] KWITANSI DAN KARTU PESERTA SISWA LABSCHOOL CIBUBUR';
        $mail->isHTML(true);
        if (!empty($data['kartu_peserta'])) {
            $mail->AddAttachment('./uploads/kartu_peserta/' . $data['kartu_peserta']);
        }
        if (!empty($data['kwitansi'])) {
            $mail->AddAttachment('./uploads/kwitansi/' . $data['kwitansi']);
        }
        $data_['to'] = $to;
        $data_['nama_lengkap'] = $data['nama_lengkap'];
        $data_['jenjang'] = $data['jenjang'];
        $data_['email'] = isset($data['email']) ? $data['email'] : '';
        $data_['nama_panitia'] = $data['nama_panitia'];
        $data_['tipe_pendaftaran'] = $data['tipe_pendaftaran'];
        $data_['transaksi'] = $data['transaksi'];
        $mailContent = $this->load->view('template_email_pembayaran', $data_, true);
        $mail->Body = $mailContent;
        $mail->send();
    }

    public function send_email_paid_daftar_ulang($file = '', $to = '', $data)
    {
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = 'smtp.office365.com';
        $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply@labschoolcibubur.sch.id';
        $mail->Password = ''; // [JACOS] TODO(manual): password SMTP Jacos — kredensial lama dihapus
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->addReplyTo('noreply@labschoolcibubur.sch.id', 'Labschool Cibubur');
        $mail->setFrom('noreply@labschoolcibubur.sch.id', 'Labschool Cibubur');
        $mail->addAddress($to);
        $mail->Subject = '[No Reply] KWITANSI DAN KARTU SISWA SEMENTARA LABSCHOOL CIBUBUR';
        $mail->isHTML(true);
        if (!empty($data['kartu_siswa_sementara'])) {
            $mail->AddAttachment('./uploads/kartu_siswa_sementara/' . $data['kartu_siswa_sementara']);
        }
        if (!empty($data['kwitansi'])) {
            $mail->AddAttachment('./uploads/kwitansi/' . $data['kwitansi']);
        }
        $data_['to'] = $to;
        $data_['nama_lengkap'] = $data['nama_lengkap'];
        $data_['jenjang'] = $data['jenjang'];
        $data_['nama_panitia'] = $data['nama_panitia'];
        $data_['tipe_pendaftaran'] = $data['tipe_pendaftaran'];
        $data_['email'] = $data['email'];
        $mailContent = $this->load->view('template_email_pendaftaran_ulang', $data_, true);
        $mail->Body = $mailContent;
        $mail->send();
    }

    private function _update_spp_detail($transaction, $updated_at)
    {
        $transaction_parts = explode('-', $transaction->no_transaksi);
        $jenjang = isset($transaction_parts[1]) ? $transaction_parts[1] : '';
        $table = $this->spp_payment_detail->table_for($jenjang);
        $months = $this->spp_payment_detail->months($transaction->detail_bulan);
        if ($table === false || $months === false || empty($transaction->id_siswa_aktif) || empty($transaction->id_tahun_ajaran)) {
            return false;
        }

        $where = array(
            'id' => $transaction->id_spp,
            'id_siswa_aktif' => $transaction->id_siswa_aktif,
            'id_tahun_ajaran' => $transaction->id_tahun_ajaran
        );

        foreach ($months as $month) {
            $this->db->where($where)->update($table, array($month => $updated_at));
            $check = $this->db->select($month)->where($where)->get($table)->row();
            if (empty($check) || (string) $check->$month !== (string) $updated_at) {
                return false;
            }
        }
        return true;
    }
}
