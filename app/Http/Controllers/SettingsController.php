<?php

namespace App\Http\Controllers;

use App\Models\Alsintan;
use App\Models\Crop;
use App\Models\Poktan;
use App\Models\Saprodi;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SettingsController extends Controller
{
    public function index(): View
    {
        $appearance = $this->setting('appearance', ['density' => 'comfortable']);
        $appearance['density'] = $appearance['density'] === 'compact' ? 'bright' : $appearance['density'];

        return view('settings.index', [
            'profile' => $this->setting('profile', $this->defaultProfile()),
            'appearance' => $appearance,
            'securityUsers' => User::query()->orderBy('name')->get(['id', 'name', 'email', 'role', 'is_active']),
            'dataCounts' => [
                'poktans' => Poktan::count(),
                'alsintans' => Alsintan::count(),
                'saprodis' => Saprodi::count(),
                'crops' => Crop::count(),
            ],
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $profile = array_merge($this->defaultProfile(), $this->setting('profile', []), collect($validated)->except('logo')->all());

        if ($request->hasFile('logo')) {
            $oldLogo = $profile['logo'] ?? null;
            $profile['logo'] = $request->file('logo')->store('institution-logos', 'public');

            if (is_string($oldLogo) && str_starts_with($oldLogo, 'institution-logos/')) {
                Storage::disk('public')->delete($oldLogo);
            }
        }

        $this->saveSetting('profile', $profile);

        return back()->with('success', 'Profil instansi berhasil disimpan.');
    }

    public function updateAppearance(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'density' => ['required', 'in:comfortable,bright'],
        ]);

        $this->saveSetting('appearance', $validated);

        return back()->with('success', 'Preferensi tampilan berhasil disimpan.');
    }

    public function updateSecurity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'logout_other_sessions' => ['nullable', 'boolean'],
        ]);

        $user = User::query()->findOrFail($validated['user_id']);
        $isCurrentUser = $user->is($request->user());

        if ($isCurrentUser) {
            $request->validate([
                'current_password' => ['required', 'current_password'],
            ]);
        }

        $user->forceFill(['password' => $validated['password']])->save();

        if ($isCurrentUser && $request->boolean('logout_other_sessions')) {
            Auth::logoutOtherDevices($validated['password']);
        } elseif ($request->boolean('logout_other_sessions') && config('session.driver') === 'database') {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }

    public function downloadBackup(): StreamedResponse
    {
        $backup = [
            'version' => 1,
            'generated_at' => now()->toIso8601String(),
            'data' => [
                'poktans' => Poktan::query()->get()->map->getAttributes()->all(),
                'alsintans' => Alsintan::query()->get()->map->getAttributes()->all(),
                'saprodis' => Saprodi::query()->get()->map->getAttributes()->all(),
                'crops' => Crop::query()->get()->map->getAttributes()->all(),
                'settings' => SystemSetting::query()->get()->map->getAttributes()->all(),
            ],
        ];

        return response()->streamDownload(function () use ($backup): void {
            echo json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        }, 'backup-simantap-'.now()->format('Ymd-His').'.json', ['Content-Type' => 'application/json']);
    }

    public function restoreBackup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'backup' => ['required', 'file', 'mimes:json,txt', 'max:5120'],
        ]);

        try {
            $backup = json_decode($validated['backup']->get(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return back()->with('error', 'Berkas backup tidak valid.');
        }

        if (($backup['version'] ?? null) !== 1 || ! is_array($backup['data'] ?? null)) {
            return back()->with('error', 'Format backup tidak didukung.');
        }

        $data = $backup['data'];
        foreach (['poktans', 'alsintans', 'saprodis', 'crops', 'settings'] as $key) {
            if (! is_array($data[$key] ?? null)) {
                return back()->with('error', 'Isi backup tidak lengkap.');
            }
        }

        DB::transaction(function () use ($data): void {
            Alsintan::query()->delete();
            Crop::query()->delete();
            Poktan::query()->delete();
            Saprodi::query()->delete();
            SystemSetting::query()->delete();

            $this->restoreRows('poktans', $data['poktans']);
            $this->restoreRows('alsintans', $data['alsintans']);
            $this->restoreRows('saprodis', $data['saprodis']);
            $this->restoreRows('crops', $data['crops']);
            $this->restoreRows('system_settings', $data['settings']);
        });

        return back()->with('success', 'Backup berhasil dipulihkan.');
    }

    /** @return array<string, mixed> */
    private function setting(string $key, array $default): array
    {
        return SystemSetting::query()->where('key', $key)->value('value') ?? $default;
    }

    /** @param array<string, mixed> $value */
    private function saveSetting(string $key, array $value): void
    {
        SystemSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /** @return array<string, string|null> */
    private function defaultProfile(): array
    {
        return [
            'name' => 'Dinas Pertanian Kabupaten Kutai Barat',
            'address' => null,
            'phone' => null,
            'email' => null,
            'logo' => null,
        ];
    }

    /** @param array<int, array<string, mixed>> $rows */
    private function restoreRows(string $table, array $rows): void
    {
        $allowedColumns = [
            'poktans' => ['id', 'name', 'chairperson', 'district', 'village', 'commodity', 'member_count', 'phone', 'address', 'status', 'created_at', 'updated_at'],
            'alsintans' => ['id', 'type', 'brand_type', 'inventory_number', 'poktan_id', 'district', 'village', 'procurement_year', 'condition', 'usage_status', 'photo_path', 'notes', 'latitude', 'longitude', 'created_at', 'updated_at'],
            'saprodis' => ['id', 'name', 'category', 'unit', 'stock', 'minimum_stock', 'notes', 'created_at', 'updated_at'],
            'crops' => ['id', 'commodity', 'poktan_id', 'district', 'planted_area', 'harvested_area', 'production', 'unit', 'period', 'notes', 'created_at', 'updated_at'],
            'system_settings' => ['id', 'key', 'value', 'created_at', 'updated_at'],
        ];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            DB::table($table)->insert(collect($row)->only($allowedColumns[$table])->all());
        }
    }
}
