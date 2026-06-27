<?php

namespace Tests\Unit;

use App\Services\Diklat\DiklatStatusResolver;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class DiklatStatusResolverTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_display_status_resolves_mendatang_berlangsung_and_selesai(): void
    {
        Carbon::setTestNow('2026-06-28');

        $resolver = new DiklatStatusResolver();

        $this->assertSame('mendatang', $resolver->displayStatus('2026-06-29', '2026-06-30'));
        $this->assertSame('berlangsung', $resolver->displayStatus('2026-06-27', '2026-06-28'));
        $this->assertSame('selesai', $resolver->displayStatus('2026-06-20', '2026-06-27'));
    }

    public function test_jadwal_status_resolves_belum_sedang_and_sudah_terlaksana(): void
    {
        Carbon::setTestNow('2026-06-28');

        $resolver = new DiklatStatusResolver();

        $this->assertSame('belum terlaksana', $resolver->jadwalStatus('2026-06-29', '2026-06-30'));
        $this->assertSame('sedang terlaksana', $resolver->jadwalStatus('2026-06-27', '2026-06-28'));
        $this->assertSame('sudah terlaksana', $resolver->jadwalStatus('2026-06-20', '2026-06-27'));
    }
}
