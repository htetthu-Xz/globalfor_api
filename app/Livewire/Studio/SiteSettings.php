<?php

namespace App\Livewire\Studio;

use App\Models\SiteSetting;
use App\Services\MediaAssetService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class SiteSettings extends Component
{
    use WithFileUploads;

    public string $companyName = '';

    public string $phone = '';

    public string $email = '';

    public string $address = '';

    public string $facebookUrl = '';

    public mixed $newLogo = null;

    public ?string $currentLogoUrl = null;

    public function mount(): void
    {
        $settings = SiteSetting::query()->with('logoMedia')->firstOrCreate(
            [],
            [...SiteSetting::DEFAULTS, 'updated_by' => auth()->id()],
        );

        $this->companyName = $settings->company_name;
        $this->phone = $settings->phone;
        $this->email = $settings->email;
        $this->address = $settings->address;
        $this->facebookUrl = $settings->facebook_url;
        $this->currentLogoUrl = $settings->logoMedia?->publicUrl();
    }

    public function save(MediaAssetService $media): void
    {
        $validated = $this->validate([
            'companyName' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'address' => ['required', 'string', 'max:2000'],
            'facebookUrl' => ['required', 'url:http,https', 'max:2048'],
            'newLogo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        $settings = SiteSetting::query()->with('logoMedia')->firstOrCreate(
            [],
            [...SiteSetting::DEFAULTS, 'updated_by' => auth()->id()],
        );
        $previousLogo = $settings->logoMedia;
        $nextLogoId = $settings->logo_media_id;

        if ($this->newLogo) {
            $nextLogoId = $media->store($this->newLogo, auth()->user(), 'site', 'Company logo')->id;
        }

        DB::transaction(function () use ($settings, $validated, $nextLogoId): void {
            $settings->update([
                'company_name' => trim($validated['companyName']),
                'phone' => trim($validated['phone']),
                'email' => strtolower(trim($validated['email'])),
                'address' => trim($validated['address']),
                'facebook_url' => trim($validated['facebookUrl']),
                'logo_media_id' => $nextLogoId,
                'updated_by' => auth()->id(),
            ]);
        });

        if ($previousLogo && $previousLogo->id !== $nextLogoId) {
            $media->deleteIfUnused($previousLogo);
        }

        session()->flash('status', 'Website details updated across Studio and the public website.');
        $this->redirectRoute('studio.settings');
    }

    public function render(): View
    {
        return view('livewire.studio.site-settings');
    }
}
