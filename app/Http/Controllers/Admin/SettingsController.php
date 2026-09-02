<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingsUpdateRequest;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function __construct(
        protected SettingsService $settingsService,
    ) {}

    public function edit()
    {
        $settings = $this->settingsService->get();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(SettingsUpdateRequest $request)
    {
        $data = $request->validated();
        $data['razorpay_enabled'] = $request->boolean('razorpay_enabled');

        if (empty($data['razorpay_key_secret'])) {
            unset($data['razorpay_key_secret']);
        }

        if (empty($data['razorpay_webhook_secret'])) {
            unset($data['razorpay_webhook_secret']);
        }

        $settings = $this->settingsService->get();

        if ($request->boolean('remove_logo') && $settings->logo_path) {
            Storage::disk('public')->delete($settings->logo_path);
            $data['logo_path'] = null;
        }

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        unset($data['logo'], $data['remove_logo']);

        $this->settingsService->update($data);

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Settings updated successfully.');
    }
}
