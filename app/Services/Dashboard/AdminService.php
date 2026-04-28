<?php

namespace App\Services\Dashboard;

use App\Repositories\Dashboard\AdminDashboardRepository;

class AdminService
{
    public function __construct(
        private readonly AdminDashboardRepository $adminDashboardRepository,
    ) {
    }

    public function build(int $userId): array
    {
        return [
            'welcome' => 'Selamat datang admin',
            'summary' => [
                'label' => 'Dashboard admin',
                'jumlah_pegawai' => $this->adminDashboardRepository->getTotalPegawai(),
                'jumlah_pegawai_aktif' => $this->adminDashboardRepository->getTotalPegawaiAktif(),
                'jumlah_permintaan_update_data' => $this->adminDashboardRepository->getTotalPermintaanUpdateData(),
                'jumlah_permintaan_disetujui' => $this->adminDashboardRepository->getTotalPermintaanUpdateDataDisetujui(),
            ],
        ];
    }
}
