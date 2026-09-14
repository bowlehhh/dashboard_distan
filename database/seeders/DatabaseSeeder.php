<?php

namespace Database\Seeders;

use App\Models\Alsintan;
use App\Models\Crop;
use App\Models\Poktan;
use App\Models\Saprodi;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => config('simantap.admin.email')],
            [
                'name' => 'Admin SIMANTAP',
                'password' => config('simantap.admin.password'),
                'role' => 'admin',
                'unit_kerja' => 'Dinas Pertanian',
                'is_active' => true,
            ],
        );

        $groups = collect([
            ['name' => 'Suka Maju', 'chairperson' => 'Joko Santoso', 'district' => 'Barong Tongkok', 'village' => 'Geleo Baru', 'commodity' => 'Padi', 'member_count' => 28, 'status' => 'Aktif'],
            ['name' => 'Tani Makmur', 'chairperson' => 'Budi Hartono', 'district' => 'Melak', 'village' => 'Melak Ulu', 'commodity' => 'Jagung', 'member_count' => 32, 'status' => 'Aktif'],
            ['name' => 'Harapan Baru', 'chairperson' => 'Slamet', 'district' => 'Jempang', 'village' => 'Tanjung Isuy', 'commodity' => 'Kedelai', 'member_count' => 21, 'status' => 'Aktif'],
        ])->map(fn (array $data) => Poktan::query()->updateOrCreate(['name' => $data['name']], $data));

        foreach ([['Traktor Roda 2', 'Yanmar TF 85', 'ALS-001', 0, 'Barong Tongkok'], ['Pompa Air', 'Honda WB 30', 'ALS-002', 1, 'Melak'], ['Hand Tractor', 'Quick G1000', 'ALS-003', 2, 'Jempang'], ['Cultivator', 'Kubota', 'ALS-004', 1, 'Melak']] as [$type, $brand, $inventory, $index, $district]) {
            Alsintan::query()->updateOrCreate(['inventory_number' => $inventory], ['type' => $type, 'brand_type' => $brand, 'poktan_id' => $groups[$index]->id, 'district' => $district, 'village' => 'Kutai Barat', 'procurement_year' => 2022, 'usage_status' => 'Digunakan']);
        }
        foreach ([['Urea', 0, 'Kg', 1250], ['NPK Phonska', 1, 'Kg', 820], ['Benih Padi Inpari 32', 2, 'Kg', 340], ['Pestisida Nabati', 1, 'Liter', 95]] as [$name, $index, $unit, $quantityDistributed]) {
            Saprodi::query()->updateOrCreate(['name' => $name], ['poktan_id' => $groups[$index]->id, 'unit' => $unit, 'quantity_distributed' => $quantityDistributed, 'distributed_year' => 2026]);
        }
        foreach ([['Padi', 'Barong Tongkok', 6520, '27100 Ton'], ['Jagung', 'Melak', 3410, '14500 Ton'], ['Kedelai', 'Jempang', 850, '2200 Ton']] as [$commodity, $district, $area, $production]) {
            Crop::query()->updateOrCreate(['commodity' => $commodity, 'district' => $district], ['planted_area' => $area, 'harvested_area' => $area * .92, 'production' => $production, 'period' => '2026']);
        }
    }
}
