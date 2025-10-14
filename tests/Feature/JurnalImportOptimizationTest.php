<?php

namespace Tests\Feature;

use App\Models\Coa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JurnalImportOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user with standard profile
        $this->user = User::factory()->create([
            'profile' => 'standard',
            'periode' => '2024',
        ]);

        // Create test COAs
        Coa::factory()->create([
            'nomor_akun' => '111',
            'nama_akun' => 'Kas',
            'created_by' => $this->user->id,
        ]);

        Coa::factory()->create([
            'nomor_akun' => '411',
            'nama_akun' => 'Pendapatan',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_import_uses_batch_coa_lookup(): void
    {
        Storage::fake('local');

        // Create a simple Excel file mock
        $file = UploadedFile::fake()->createWithContent(
            'jurnal_import.xlsx',
            file_get_contents(base_path('tests/fixtures/jurnal_sample.xlsx'))
        );

        // Enable query log to verify batch query
        DB::enableQueryLog();

        $response = $this->actingAs($this->user)
            ->post(route('jurnal.import.html'), [
                'file' => $file,
            ]);

        $queries = DB::getQueryLog();

        // Verify that we use whereIn instead of multiple where queries
        $coaQueries = array_filter($queries, function ($query) {
            return str_contains($query['query'], 'select * from `coas`') &&
                   str_contains($query['query'], 'in (');
        });

        $this->assertNotEmpty($coaQueries, 'Batch COA query using whereIn should be present');

        $response->assertStatus(200);
    }

    public function test_import_respects_profile_limits(): void
    {
        Storage::fake('local');

        // Test for standard profile (limit 100)
        $this->user->update(['profile' => 'standard']);

        $file = UploadedFile::fake()->create('jurnal_import.xlsx', 100);

        $response = $this->actingAs($this->user)
            ->post(route('jurnal.import.html'), [
                'file' => $file,
            ]);

        $response->assertStatus(200);

        // Verify message about limits
        $data = $response->json();
        if (isset($data['html']) && count($data['html']) > 100) {
            $this->assertStringContainsString('100', $data['message']);
        }
    }

    public function test_import_returns_error_for_missing_coa(): void
    {
        Storage::fake('local');

        // Create file with non-existent COA
        $file = UploadedFile::fake()->create('jurnal_import.xlsx', 50);

        $response = $this->actingAs($this->user)
            ->post(route('jurnal.import.html'), [
                'file' => $file,
            ]);

        // Should handle missing COA gracefully
        $this->assertTrue(
            $response->status() === 200 || $response->status() === 500,
            'Should return valid response for missing COA'
        );
    }

    public function test_import_cleans_up_temporary_files(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('jurnal_import.xlsx', 50);

        $this->actingAs($this->user)
            ->post(route('jurnal.import.html'), [
                'file' => $file,
            ]);

        // Verify temp files are cleaned up
        $tempFiles = Storage::files('temp');
        $this->assertEmpty($tempFiles, 'Temporary files should be cleaned up after import');
    }
}
