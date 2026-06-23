<?php

namespace Database\Seeders;

use App\Models\ApprovalChain;
use App\Models\ApprovalChainStep;
use App\Models\Attendance;
use App\Models\Division;
use App\Models\EmployeeDetail;
use App\Models\Holiday;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Position;
use App\Models\RequestApproval;
use App\Models\Shift;
use App\Models\User;
use App\Models\UserSchedule;
use App\Models\WorkLocation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');
        $today = Carbon::parse('2026-06-23');

        // ─── DIVISIONS ───
        $it = Division::create(['nama_bagian' => 'IT']);
        $hrd = Division::create(['nama_bagian' => 'HRD']);
        $keuangan = Division::create(['nama_bagian' => 'Keuangan']);
        $ppl = Division::create(['nama_bagian' => 'Pengembangan Perangkat Lunak', 'parent_id' => $it->id]);

        // ─── POSITIONS ───
        $kepalaIT = Position::create(['nama' => 'Kepala IT', 'division_id' => $it->id]);
        $kepalaHRD = Position::create(['nama' => 'Kepala HRD', 'division_id' => $hrd->id]);
        $kepalaKeuangan = Position::create(['nama' => 'Kepala Keuangan', 'division_id' => $keuangan->id]);
        $developer = Position::create(['nama' => 'Developer', 'division_id' => $ppl->id]);
        $staffHR = Position::create(['nama' => 'Staff HR', 'division_id' => $hrd->id]);
        $staffKeuangan = Position::create(['nama' => 'Staff Keuangan', 'division_id' => $keuangan->id]);

        // ─── USERS ───
        $budi = User::create(['name' => 'Budi Santoso', 'email' => 'budi@hrsi.test', 'password' => $password, 'nip' => 'EMP001', 'phone' => '08120000001', 'aktif' => true]);
        $siti = User::create(['name' => 'Siti Rahmawati', 'email' => 'siti@hrsi.test', 'password' => $password, 'nip' => 'EMP002', 'phone' => '08120000002', 'aktif' => true]);
        $ahmad = User::create(['name' => 'Ahmad Hidayat', 'email' => 'ahmad@hrsi.test', 'password' => $password, 'nip' => 'EMP003', 'phone' => '08120000003', 'aktif' => true]);
        $dewi = User::create(['name' => 'Dewi Lestari', 'email' => 'dewi@hrsi.test', 'password' => $password, 'nip' => 'EMP004', 'phone' => '08120000004', 'aktif' => true]);
        $rudi = User::create(['name' => 'Rudi Hermawan', 'email' => 'rudi@hrsi.test', 'password' => $password, 'nip' => 'EMP005', 'phone' => '08120000005', 'aktif' => true]);
        $ani = User::create(['name' => 'Ani Wulandari', 'email' => 'ani@hrsi.test', 'password' => $password, 'nip' => 'EMP006', 'phone' => '08120000006', 'aktif' => true]);
        $dodi = User::create(['name' => 'Dodi Firmansyah', 'email' => 'dodi@hrsi.test', 'password' => $password, 'nip' => 'EMP007', 'phone' => '08120000007', 'aktif' => true]);
        $fitri = User::create(['name' => 'Fitri Handayani', 'email' => 'fitri@hrsi.test', 'password' => $password, 'nip' => 'EMP008', 'phone' => '08120000008', 'aktif' => true]);

        $budi->assignRole('Manager');
        $siti->assignRole('HRD');
        $ahmad->assignRole('Karyawan');
        $dewi->assignRole('Karyawan');
        $rudi->assignRole('Karyawan');
        $ani->assignRole('Karyawan');
        $dodi->assignRole('Manager');
        $fitri->assignRole('Karyawan');

        // ─── EMPLOYEE DETAILS ───
        EmployeeDetail::create([
            'user_id' => $budi->id, 'division_id' => $it->id, 'position_id' => $kepalaIT->id,
            'status_karyawan' => 'Tetap', 'tgl_masuk' => '2020-01-15', 'tempat_lahir' => 'Jakarta', 'tanggal_lahir' => '1985-03-10',
            'jenis_kelamin' => 'Pria', 'agama' => 'Islam', 'status_marital' => 'Menikah',
            'no_ktp' => '3174011503850001', 'no_hp' => '08120000001',
            'gaji_pokok' => 15000000, 'uang_makan' => 500000, 'uang_transport' => 300000,
        ]);
        EmployeeDetail::create([
            'user_id' => $siti->id, 'division_id' => $hrd->id, 'position_id' => $kepalaHRD->id,
            'status_karyawan' => 'Tetap', 'tgl_masuk' => '2021-03-01', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-07-22',
            'jenis_kelamin' => 'Wanita', 'agama' => 'Islam', 'status_marital' => 'Menikah',
            'no_ktp' => '3274012207900002', 'no_hp' => '08120000002',
            'gaji_pokok' => 14000000, 'uang_makan' => 500000, 'uang_transport' => 300000,
        ]);
        EmployeeDetail::create([
            'user_id' => $ahmad->id, 'division_id' => $ppl->id, 'position_id' => $developer->id,
            'supervisor_id' => $budi->id,
            'status_karyawan' => 'Tetap', 'tgl_masuk' => '2022-06-01', 'tempat_lahir' => 'Surabaya', 'tanggal_lahir' => '1995-11-05',
            'jenis_kelamin' => 'Pria', 'agama' => 'Islam', 'status_marital' => 'Menikah',
            'no_ktp' => '3574010511950003', 'no_hp' => '08120000003',
            'gaji_pokok' => 8500000, 'uang_makan' => 500000, 'uang_transport' => 300000,
        ]);
        EmployeeDetail::create([
            'user_id' => $dewi->id, 'division_id' => $ppl->id, 'position_id' => $developer->id,
            'supervisor_id' => $budi->id,
            'status_karyawan' => 'Kontrak', 'tgl_masuk' => '2023-09-15', 'tempat_lahir' => 'Yogyakarta', 'tanggal_lahir' => '1998-02-14',
            'jenis_kelamin' => 'Wanita', 'agama' => 'Kristen', 'status_marital' => 'Belum Menikah',
            'no_ktp' => '3474011402980004', 'no_hp' => '08120000004',
            'gaji_pokok' => 7500000, 'uang_makan' => 500000, 'uang_transport' => 300000,
        ]);
        EmployeeDetail::create([
            'user_id' => $rudi->id, 'division_id' => $hrd->id, 'position_id' => $staffHR->id,
            'supervisor_id' => $siti->id,
            'status_karyawan' => 'Tetap', 'tgl_masuk' => '2022-01-10', 'tempat_lahir' => 'Semarang', 'tanggal_lahir' => '1993-08-30',
            'jenis_kelamin' => 'Pria', 'agama' => 'Islam', 'status_marital' => 'Menikah',
            'no_ktp' => '3374013008930005', 'no_hp' => '08120000005',
            'gaji_pokok' => 7000000, 'uang_makan' => 500000, 'uang_transport' => 300000,
        ]);
        EmployeeDetail::create([
            'user_id' => $ani->id, 'division_id' => $hrd->id, 'position_id' => $staffHR->id,
            'supervisor_id' => $siti->id,
            'status_karyawan' => 'Kontrak', 'tgl_masuk' => '2024-03-01', 'tempat_lahir' => 'Malang', 'tanggal_lahir' => '1997-06-18',
            'jenis_kelamin' => 'Wanita', 'agama' => 'Islam', 'status_marital' => 'Belum Menikah',
            'no_ktp' => '3574011806970006', 'no_hp' => '08120000006',
            'gaji_pokok' => 6500000, 'uang_makan' => 500000, 'uang_transport' => 300000,
        ]);
        EmployeeDetail::create([
            'user_id' => $dodi->id, 'division_id' => $keuangan->id, 'position_id' => $kepalaKeuangan->id,
            'status_karyawan' => 'Tetap', 'tgl_masuk' => '2021-07-01', 'tempat_lahir' => 'Medan', 'tanggal_lahir' => '1988-12-01',
            'jenis_kelamin' => 'Pria', 'agama' => 'Islam', 'status_marital' => 'Menikah',
            'no_ktp' => '1274010112880007', 'no_hp' => '08120000007',
            'gaji_pokok' => 13000000, 'uang_makan' => 500000, 'uang_transport' => 300000,
        ]);
        EmployeeDetail::create([
            'user_id' => $fitri->id, 'division_id' => $keuangan->id, 'position_id' => $staffKeuangan->id,
            'supervisor_id' => $dodi->id,
            'status_karyawan' => 'Tetap', 'tgl_masuk' => '2023-04-01', 'tempat_lahir' => 'Bogor', 'tanggal_lahir' => '1996-09-25',
            'jenis_kelamin' => 'Wanita', 'agama' => 'Islam', 'status_marital' => 'Menikah',
            'no_ktp' => '3274012509960008', 'no_hp' => '08120000008',
            'gaji_pokok' => 7000000, 'uang_makan' => 500000, 'uang_transport' => 300000,
        ]);

        $it->update(['head_id' => $budi->id]);
        $hrd->update(['head_id' => $siti->id]);
        $keuangan->update(['head_id' => $dodi->id]);

        // ─── HOLIDAYS ───
        Holiday::create(['tanggal' => '2026-05-01', 'keterangan' => 'Hari Buruh Internasional', 'is_recurring' => true]);
        Holiday::create(['tanggal' => '2026-05-13', 'keterangan' => 'Idul Fitri 1447 H', 'is_recurring' => false]);
        Holiday::create(['tanggal' => '2026-05-14', 'keterangan' => 'Idul Fitri 1447 H', 'is_recurring' => false]);
        Holiday::create(['tanggal' => '2026-06-01', 'keterangan' => 'Hari Lahir Pancasila', 'is_recurring' => true]);

        // ─── WORK LOCATIONS ───
        WorkLocation::create(['nama' => 'Cabang Bandung', 'alamat' => 'Jl. Merdeka No. 50, Bandung', 'lat' => -6.9147, 'lon' => 107.6098, 'radius' => 100, 'is_office' => true, 'is_active' => true]);

        // ─── APPROVAL CHAINS ───
        $managerRole = Role::where('name', 'Manager')->first();
        $hrdRole = Role::where('name', 'HRD')->first();

        $chainTahunan = ApprovalChain::create(['nama' => 'Cuti Tahunan', 'slug' => 'cuti-tahunan']);
        $chainTahunanStep1 = ApprovalChainStep::create(['approval_chain_id' => $chainTahunan->id, 'step_order' => 1, 'approver_type' => 'supervisor']);
        $chainTahunanStep2 = ApprovalChainStep::create(['approval_chain_id' => $chainTahunan->id, 'step_order' => 2, 'approver_type' => 'role', 'role_id' => $hrdRole->id]);

        $chainSakit = ApprovalChain::create(['nama' => 'Cuti Sakit', 'slug' => 'cuti-sakit']);
        $chainSakitStep1 = ApprovalChainStep::create(['approval_chain_id' => $chainSakit->id, 'step_order' => 1, 'approver_type' => 'supervisor']);

        $chainKhusus = ApprovalChain::create(['nama' => 'Cuti Khusus', 'slug' => 'cuti-khusus']);
        $chainKhususStep1 = ApprovalChainStep::create(['approval_chain_id' => $chainKhusus->id, 'step_order' => 1, 'approver_type' => 'role', 'role_id' => $managerRole->id]);
        $chainKhususStep2 = ApprovalChainStep::create(['approval_chain_id' => $chainKhusus->id, 'step_order' => 2, 'approver_type' => 'role', 'role_id' => $hrdRole->id]);

        // ─── LEAVE TYPES ───
        $cutiTahunan = LeaveType::create([
            'nama' => 'Cuti Tahunan', 'slug' => 'cuti-tahunan',
            'deskripsi' => 'Cuti tahunan karyawan', 'kuota_per_tahun' => 12,
            'is_paid' => true, 'requires_attachment' => false, 'is_active' => true,
            'approval_chain_id' => $chainTahunan->id,
        ]);
        $cutiSakit = LeaveType::create([
            'nama' => 'Cuti Sakit', 'slug' => 'cuti-sakit',
            'deskripsi' => 'Cuti karena sakit (disertai surat dokter)', 'kuota_per_tahun' => 10,
            'is_paid' => true, 'requires_attachment' => true, 'is_active' => true,
            'approval_chain_id' => $chainSakit->id,
        ]);
        $cutiMenikah = LeaveType::create([
            'nama' => 'Cuti Menikah', 'slug' => 'cuti-menikah',
            'deskripsi' => 'Cuti pernikahan', 'kuota_per_tahun' => 3,
            'is_paid' => true, 'requires_attachment' => true, 'is_active' => true, 'max_days' => 3,
            'approval_chain_id' => $chainKhusus->id,
        ]);
        $cutiMelahirkan = LeaveType::create([
            'nama' => 'Cuti Melahirkan', 'slug' => 'cuti-melahirkan',
            'deskripsi' => 'Cuti melahirkan', 'kuota_per_tahun' => 90,
            'is_paid' => true, 'requires_attachment' => true, 'is_active' => true, 'max_days' => 90,
            'approval_chain_id' => $chainKhusus->id,
        ]);

        // ─── LEAVE BALANCES ───
        $allDummyUsers = [$ahmad, $dewi, $rudi, $ani, $fitri, $budi, $siti, $dodi];
        $leaveTypes = [$cutiTahunan, $cutiSakit, $cutiMenikah, $cutiMelahirkan];
        foreach ($allDummyUsers as $u) {
            foreach ($leaveTypes as $lt) {
                LeaveBalance::create([
                    'user_id' => $u->id, 'leave_type_id' => $lt->id,
                    'year' => 2026, 'kuota' => $lt->kuota_per_tahun ?? 0, 'terpakai' => 0,
                ]);
            }
        }

        // ─── MASTER DATA (ensure exist) ───
        $pagiShift = Shift::firstOrCreate(
            ['nama' => 'Pagi'],
            ['jam_masuk' => '08:00', 'jam_pulang' => '17:00', 'toleransi_menit' => 15, 'is_active' => true]
        );
        Shift::firstOrCreate(
            ['nama' => 'Siang'],
            ['jam_masuk' => '12:00', 'jam_pulang' => '21:00', 'toleransi_menit' => 15, 'is_active' => true]
        );
        $kantorPusat = WorkLocation::firstOrCreate(
            ['nama' => 'Kantor Pusat'],
            ['alamat' => 'Jl. Contoh No. 1', 'lat' => -6.2088, 'lon' => 106.8456, 'radius' => 100, 'is_office' => true, 'is_active' => true]
        );

        $holidayDates = Holiday::pluck('tanggal')->map(fn($d) => Carbon::parse($d)->toDateString())->toArray();

        $date = Carbon::parse('2026-04-01');
        $endDate = Carbon::parse('2026-06-23');

        $employees = [$ahmad, $dewi, $rudi, $ani, $fitri, $budi, $siti, $dodi];

        while ($date->lte($endDate)) {
            $dateStr = $date->toDateString();

            if ($date->isWeekend() || in_array($dateStr, $holidayDates)) {
                $date->addDay();
                continue;
            }

            foreach ($employees as $emp) {
                UserSchedule::create([
                    'user_id' => $emp->id,
                    'shift_id' => $pagiShift->id,
                    'tanggal' => $dateStr,
                ]);

                if ($date->gte($today)) {
                    continue;
                }

                $rand = rand(1, 100);
                if ($rand <= 68) {
                    Attendance::create([
                        'user_id' => $emp->id, 'tanggal' => $dateStr,
                        'check_in' => $date->copy()->setTime(7, rand(45, 59))->format('H:i:s'),
                        'check_out' => $date->copy()->setTime(17, rand(0, 15))->format('H:i:s'),
                        'check_in_lat' => -6.2088, 'check_in_lon' => 106.8456,
                        'check_out_lat' => -6.2088, 'check_out_lon' => 106.8456,
                        'check_in_address' => $kantorPusat->alamat,
                        'check_out_address' => $kantorPusat->alamat,
                        'work_from' => 'wfo', 'status' => 'hadir',
                    ]);
                } elseif ($rand <= 83) {
                    $terlambat = rand(10, 55);
                    Attendance::create([
                        'user_id' => $emp->id, 'tanggal' => $dateStr,
                        'check_in' => $date->copy()->setTime(8, rand(15, 59))->format('H:i:s'),
                        'check_out' => $date->copy()->setTime(17, rand(0, 30))->format('H:i:s'),
                        'check_in_lat' => -6.2088, 'check_in_lon' => 106.8456,
                        'check_out_lat' => -6.2088, 'check_out_lon' => 106.8456,
                        'check_in_address' => $kantorPusat->alamat,
                        'check_out_address' => $kantorPusat->alamat,
                        'work_from' => 'wfo', 'terlambat_menit' => $terlambat, 'status' => 'terlambat',
                    ]);
                } elseif ($rand <= 93) {
                    Attendance::create([
                        'user_id' => $emp->id, 'tanggal' => $dateStr,
                        'check_in' => $date->copy()->setTime(7, rand(50, 59))->format('H:i:s'),
                        'check_in_lat' => -6.2088, 'check_in_lon' => 106.8456,
                        'check_in_address' => $kantorPusat->alamat,
                        'work_from' => 'wfo', 'status' => 'hadir',
                        'keterangan' => 'Lupa check-out',
                    ]);
                }
            }

            $date->addDay();
        }

        // ─── LEAVE REQUESTS ───
        $leaveAll = function ($user, $leaveType, $chainId, $start, $end, $days, $ket, $status) {
            return LeaveRequest::create([
                'user_id' => $user->id, 'leave_type_id' => $leaveType->id,
                'approval_chain_id' => $chainId,
                'tanggal_pengajuan' => Carbon::parse($start)->subDays(5)->toDateString(),
                'tanggal_mulai' => $start, 'tanggal_selesai' => $end,
                'jumlah_hari' => $days, 'keterangan' => $ket, 'status' => $status,
            ]);
        };

        // 1. Ahmad → Cuti Tahunan (Jun 15-17, 3 hari) → fully approved
        $lr1 = $leaveAll($ahmad, $cutiTahunan, $chainTahunan->id, '2026-06-15', '2026-06-17', 3, 'Cuti tahunan liburan ke Bali', 'approved');
        RequestApproval::create(['requestable_type' => LeaveRequest::class, 'requestable_id' => $lr1->id, 'approval_chain_step_id' => $chainTahunanStep1->id, 'step_order' => 1, 'approver_id' => $budi->id, 'status' => 'approved', 'action_at' => '2026-06-10 09:00:00']);
        RequestApproval::create(['requestable_type' => LeaveRequest::class, 'requestable_id' => $lr1->id, 'approval_chain_step_id' => $chainTahunanStep2->id, 'step_order' => 2, 'approver_id' => $siti->id, 'status' => 'approved', 'action_at' => '2026-06-10 10:00:00']);

        // 2. Dewi → Cuti Sakit (Jun 10, 1 hari) → pending
        $lr2 = $leaveAll($dewi, $cutiSakit, $chainSakit->id, '2026-06-10', '2026-06-10', 1, 'Sakit demam', 'pending');
        RequestApproval::create(['requestable_type' => LeaveRequest::class, 'requestable_id' => $lr2->id, 'approval_chain_step_id' => $chainSakitStep1->id, 'step_order' => 1, 'approver_id' => $budi->id, 'status' => 'pending']);

        // 3. Rudi → Cuti Menikah (Jul 5-7, 3 hari) → step 1 approved (Manager), step 2 pending (HRD)
        $lr3 = $leaveAll($rudi, $cutiMenikah, $chainKhusus->id, '2026-07-05', '2026-07-07', 3, 'Menikah', 'pending');
        RequestApproval::create(['requestable_type' => LeaveRequest::class, 'requestable_id' => $lr3->id, 'approval_chain_step_id' => $chainKhususStep1->id, 'step_order' => 1, 'approver_id' => $budi->id, 'status' => 'approved', 'action_at' => '2026-06-15 09:30:00']);
        RequestApproval::create(['requestable_type' => LeaveRequest::class, 'requestable_id' => $lr3->id, 'approval_chain_step_id' => $chainKhususStep2->id, 'step_order' => 2, 'approver_id' => $siti->id, 'status' => 'pending']);

        // 4. Ani → Cuti Tahunan (Jun 22-23, 2 hari) → rejected by supervisor
        $lr4 = $leaveAll($ani, $cutiTahunan, $chainTahunan->id, '2026-06-22', '2026-06-23', 2, 'Mau libur panjang', 'rejected');
        RequestApproval::create(['requestable_type' => LeaveRequest::class, 'requestable_id' => $lr4->id, 'approval_chain_step_id' => $chainTahunanStep1->id, 'step_order' => 1, 'approver_id' => $siti->id, 'status' => 'rejected', 'catatan' => 'Tidak bisa, banyak kerjaan', 'action_at' => '2026-06-18 11:00:00']);

        // 5. Fitri → Cuti Tahunan (Jun 29-30, 2 hari) → pending (supervisor not yet approved)
        $lr5 = $leaveAll($fitri, $cutiTahunan, $chainTahunan->id, '2026-06-29', '2026-06-30', 2, 'Urusan keluarga', 'pending');
        RequestApproval::create(['requestable_type' => LeaveRequest::class, 'requestable_id' => $lr5->id, 'approval_chain_step_id' => $chainTahunanStep1->id, 'step_order' => 1, 'approver_id' => $dodi->id, 'status' => 'pending']);

        // 6. Ahmad → Cuti Sakit (Jun 8-9, 2 hari) → cancelled
        $leaveAll($ahmad, $cutiSakit, $chainSakit->id, '2026-06-08', '2026-06-09', 2, 'Sakit kepala, tapi sudah sembuh', 'cancelled');

        // 7. Dewi → Cuti Tahunan (May 18-20, 3 hari) → fully approved (old, consume balance)
        $lr7 = $leaveAll($dewi, $cutiTahunan, $chainTahunan->id, '2026-05-18', '2026-05-20', 3, 'Liburan ke Jogja', 'approved');
        RequestApproval::create(['requestable_type' => LeaveRequest::class, 'requestable_id' => $lr7->id, 'approval_chain_step_id' => $chainTahunanStep1->id, 'step_order' => 1, 'approver_id' => $budi->id, 'status' => 'approved', 'action_at' => '2026-05-10 08:00:00']);
        RequestApproval::create(['requestable_type' => LeaveRequest::class, 'requestable_id' => $lr7->id, 'approval_chain_step_id' => $chainTahunanStep2->id, 'step_order' => 2, 'approver_id' => $siti->id, 'status' => 'approved', 'action_at' => '2026-05-10 09:00:00']);

        // 8. Ani → Cuti Sakit (May 4, 1 hari) → approved
        $lr8 = $leaveAll($ani, $cutiSakit, $chainSakit->id, '2026-05-04', '2026-05-04', 1, 'Sakit flu', 'approved');
        RequestApproval::create(['requestable_type' => LeaveRequest::class, 'requestable_id' => $lr8->id, 'approval_chain_step_id' => $chainSakitStep1->id, 'step_order' => 1, 'approver_id' => $siti->id, 'status' => 'approved', 'action_at' => '2026-05-03 10:00:00']);

        // ─── UPDATE LEAVE BALANCES ───
        $usedBalances = [
            [$ahmad->id, $cutiTahunan->id, 3],
            [$dewi->id, $cutiTahunan->id, 3],
            [$ani->id, $cutiSakit->id, 1],
        ];
        foreach ($usedBalances as $ub) {
            $bal = LeaveBalance::where('user_id', $ub[0])->where('leave_type_id', $ub[1])->where('year', 2026)->first();
            if ($bal) {
                $bal->increment('terpakai', $ub[2]);
            }
        }
    }
}
