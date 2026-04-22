<?php

namespace App\Services\ChangeRequest;

use App\Models\PegawaiPribadi;
use App\Models\PerubahanData;
use App\Repositories\ChangeRequest\ChangeRequestRepository;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ChangeRequestAdminService
{
    public function __construct(private readonly ChangeRequestRepository $changeRequestRepository)
    {
    }

    public function list(?string $status = null, ?string $fitur = null): array
    {
        return $this->changeRequestRepository->list($status, $fitur)
            ->map(function (PerubahanData $item): array {
            return [
                'id' => (int) $item->id,
                'by_user' => [
                    'id' => (int) ($item->user?->id ?? 0),
                    'username' => (string) ($item->user?->username ?? ''),
                    'role' => (string) ($item->user?->role ?? ''),
                    'nama' => (string) ($item->user?->pegawai?->nama ?? ''),
                ],
                'fitur' => (string) $item->fitur,
                'status' => (string) $item->status,
                'note' => (string) ($item->note ?? ''),
                'jumlah_detail' => (int) $item->details_count,
                'created_at' => optional($item->created_at)?->toDateTimeString(),
                'updated_at' => optional($item->updated_at)?->toDateTimeString(),
            ];
            })->values()->all();
    }

    public function detail(int $id): ?array
    {
        $item = $this->changeRequestRepository->findByIdWithDetails($id);

        if ($item === null) {
            return null;
        }

        return [
            'id' => (int) $item->id,
            'by_user' => [
                'id' => (int) ($item->user?->id ?? 0),
                'username' => (string) ($item->user?->username ?? ''),
                'role' => (string) ($item->user?->role ?? ''),
                'nama' => (string) ($item->user?->pegawai?->nama ?? ''),
            ],
            'fitur' => (string) $item->fitur,
            'status' => (string) $item->status,
            'note' => (string) ($item->note ?? ''),
            'created_at' => optional($item->created_at)?->toDateTimeString(),
            'updated_at' => optional($item->updated_at)?->toDateTimeString(),
            'details' => $item->details->map(function ($detail): array {
                return [
                    'id' => (int) $detail->id,
                    'target_table' => (string) $detail->target_table,
                    'kolom' => (string) $detail->kolom,
                    'old_value' => $detail->old_value,
                    'value' => $detail->value,
                    'created_at' => optional($detail->created_at)?->toDateTimeString(),
                    'updated_at' => optional($detail->updated_at)?->toDateTimeString(),
                ];
            })->values()->all(),
        ];
    }

    public function accept(int $id, ?string $adminNote = null): ?array
    {
        return DB::transaction(function () use ($id, $adminNote): ?array {
            $item = $this->changeRequestRepository->findByIdForAction($id);

            if ($item === null) {
                return null;
            }

            if ((string) $item->status !== 'pending') {
                throw new InvalidArgumentException('Pengajuan sudah diproses sebelumnya.');
            }

            if ((string) $item->fitur === 'profile') {
                $this->applyProfileDetails($item);
            }

            $item->status = 'approved';
            $item->note = $this->mergeAdminNote($item->note, 'APPROVED', $adminNote);
            $this->changeRequestRepository->save($item);

            return $this->detail((int) $item->id);
        });
    }

    public function reject(int $id, ?string $adminNote = null): ?array
    {
        return DB::transaction(function () use ($id, $adminNote): ?array {
            $item = $this->changeRequestRepository->findByIdForAction($id);

            if ($item === null) {
                return null;
            }

            if ((string) $item->status !== 'pending') {
                throw new InvalidArgumentException('Pengajuan sudah diproses sebelumnya.');
            }

            $item->status = 'rejected';
            $item->note = $this->mergeAdminNote($item->note, 'REJECTED', $adminNote);
            $this->changeRequestRepository->save($item);

            return $this->detail((int) $item->id);
        });
    }

    private function applyProfileDetails(PerubahanData $item): void
    {
        $pegawai = $item->user?->pegawai;

        if ($pegawai === null) {
            throw new InvalidArgumentException('Data pegawai pengaju tidak ditemukan.');
        }

        $pribadi = $pegawai->pribadi;
        if ($pribadi === null) {
            $pribadi = PegawaiPribadi::query()->create([
                'pegawai_id' => $pegawai->id,
            ]);
        }

        $pegawaiAllowedColumns = [
            'nip',
            'nik',
            'nama',
            'status_pegawai',
            'tgl_masuk',
            'tmt_cpns',
            'tmt_pns',
            'profesi_id',
            'jenis_pegawai_id',
        ];

        $pribadiAllowedColumns = [
            'jenis_kelamin',
            'tanggal_lahir',
            'agama',
            'status_perkawinan',
            'alamat',
            'no_telp',
            'no_kk',
            'email',
        ];

        foreach ($item->details as $detail) {
            $targetTable = (string) $detail->target_table;
            $column = (string) $detail->kolom;
            $value = $detail->value;

            if ($targetTable === 'pegawai') {
                if (! in_array($column, $pegawaiAllowedColumns, true)) {
                    continue;
                }

                $pegawai->{$column} = $this->castValueForColumn($column, $value);
                continue;
            }

            if ($targetTable === 'pegawai_pribadi') {
                if (! in_array($column, $pribadiAllowedColumns, true)) {
                    continue;
                }

                $pribadi->{$column} = $this->castValueForColumn($column, $value);
            }
        }

        $pegawai->save();
        $pribadi->save();
    }

    private function castValueForColumn(string $column, mixed $value): mixed
    {
        if ($value === '') {
            return null;
        }

        return match ($column) {
            'profesi_id', 'jenis_pegawai_id' => $value === null ? null : (int) $value,
            default => $value,
        };
    }

    private function mergeAdminNote(?string $existingNote, string $decision, ?string $adminNote): string
    {
        $existingNote = trim((string) $existingNote);
        $adminNote = trim((string) $adminNote);

        if ($adminNote === '') {
            return $existingNote;
        }

        $decisionNote = sprintf('[%s] %s', $decision, $adminNote);

        if ($existingNote === '') {
            return $decisionNote;
        }

        return $existingNote."\n".$decisionNote;
    }
}
