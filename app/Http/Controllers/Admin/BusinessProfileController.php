<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessProfileController extends Controller
{
    public function edit()
    {
        $businessProfile = BusinessProfile::first();
        if (!$businessProfile) {
            $businessProfile = BusinessProfile::create([
                'business_name' => 'My Business',
            ]);
        }
        return view('admin.business_profile.edit', compact('businessProfile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_address' => 'nullable|string',
            'business_email' => 'nullable|email|max:255',
            'business_website' => 'nullable|url|max:255',
            'business_phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'footer_text' => 'nullable|string',
        ]);

        $businessProfile = BusinessProfile::first();
        if (!$businessProfile) {
            $businessProfile = new BusinessProfile();
        }

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            if ($businessProfile->logo_path) {
                Storage::disk('public')->delete($businessProfile->logo_path);
            }
            $path = $request->file('logo')->store('business_logos', 'public');
            $data['logo_path'] = $path;
        }

        $businessProfile->fill($data);
        $businessProfile->save();

        return redirect()->route('admin.business-profile.edit')->with('success', 'Business profile updated successfully.');
    }
}
