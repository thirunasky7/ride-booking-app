<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingsUpdateRequest;
use App\Services\SettingsService;

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

        $this->settingsService->update($data);

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Settings updated successfully.');
    }
}
