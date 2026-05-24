<?php

namespace App\Services\MasterData;

use App\Models\GolonganRuang;
use App\Models\JenisBiaya;
use App\Models\JenisDiklat;
use App\Models\JenisPegawai;
use App\Models\JenisSip;
use App\Models\KategoriDiklat;
use App\Models\Profesi;
use App\Models\UnitKerja;
use App\Repositories\MasterData\MasterDataRepository;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class MasterDataService
{
    private const MASTER_DATA = [
        'kategori-diklat' => [
            'model' => KategoriDiklat::class,
            'table' => 'kategori_diklat',
            'label' => 'Kategori Diklat',
            'fields' => ['nama'],
        ],
        'tipe-diklat' => [
            'model' => JenisDiklat::class,
            'table' => 'jenis_diklat',
            'label' => 'Tipe Diklat',
            'fields' => ['nama'],
        ],
        'jenis-pegawai' => [
            'model' => JenisPegawai::class,
            'table' => 'jenis_pegawai',
            'label' => 'Jenis Pegawai',
            'fields' => ['nama'],
        ],
        'unit-kerja' => [
            'model' => UnitKerja::class,
            'table' => 'unit_kerja',
            'label' => 'Unit Kerja',
            'fields' => ['nama'],
        ],
        'jenis-biaya' => [
            'model' => JenisBiaya::class,
            'table' => 'jenis_biaya',
            'label' => 'Jenis Biaya',
            'fields' => ['nama'],
        ],
        'golongan-ruang' => [
            'model' => GolonganRuang::class,
            'table' => 'golongan_ruang',
            'label' => 'Golongan Ruang',
            'fields' => ['nama'],
        ],
        'profesi' => [
            'model' => Profesi::class,
            'table' => 'profesi',
            'label' => 'Profesi',
            'fields' => ['nama', 'kategori_tenaga'],
        ],
        'jenis-sip' => [
            'model' => JenisSip::class,
            'table' => 'jenis_sip',
            'label' => 'Jenis SIP',
            'fields' => ['nama'],
        ],
    ];

    public function __construct(private readonly MasterDataRepository $masterDataRepository)
    {
    }

    public function getLabel(string $type): string
    {
        return (string) ($this->getConfig($type)['label'] ?? 'Master data');
    }

    public function getValidationRules(string $type, ?int $id = null): array
    {
        $config = $this->getConfig($type);
        $rules = [
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique($config['table'], 'nama')->ignore($id),
            ],
        ];

        if (in_array('kategori_tenaga', $config['fields'], true)) {
            $rules['kategori_tenaga'] = ['nullable', 'string', 'max:100'];
        }

        return $rules;
    }

    public function create(string $type, array $payload): array
    {
        $config = $this->getConfig($type);
        $attributes = $this->filterPayload($payload, $config['fields']);
        $model = $this->masterDataRepository->create($config['model'], $attributes);

        return $this->formatResponse($model, $config['fields']);
    }

    public function update(string $type, int $id, array $payload): array
    {
        $config = $this->getConfig($type);
        $model = $this->masterDataRepository->findById($config['model'], $id);

        if ($model === null) {
            throw new InvalidArgumentException($this->getLabel($type) . ' tidak ditemukan.', 404);
        }

        $attributes = $this->filterPayload($payload, $config['fields']);
        $model = $this->masterDataRepository->update($model, $attributes);

        return $this->formatResponse($model, $config['fields']);
    }

    public function delete(string $type, int $id): void
    {
        $config = $this->getConfig($type);
        $model = $this->masterDataRepository->findById($config['model'], $id);

        if ($model === null) {
            throw new InvalidArgumentException($this->getLabel($type) . ' tidak ditemukan.', 404);
        }

        $this->masterDataRepository->delete($model);
    }

    private function getConfig(string $type): array
    {
        $config = self::MASTER_DATA[$type] ?? null;

        if ($config === null) {
            throw new InvalidArgumentException('Tipe master data tidak valid.');
        }

        return $config;
    }

    private function filterPayload(array $payload, array $fields): array
    {
        $attributes = [];

        foreach ($fields as $field) {
            if (array_key_exists($field, $payload)) {
                $attributes[$field] = $payload[$field];
            }
        }

        return $attributes;
    }

    private function formatResponse(object $model, array $fields): array
    {
        $data = [
            'id' => (int) $model->id,
            'nama' => (string) $model->nama,
        ];

        foreach ($fields as $field) {
            if ($field !== 'nama') {
                $data[$field] = $model->getAttribute($field);
            }
        }

        return $data;
    }
}
