<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserAddress;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    //Display the user's profile form.
    public function edit(Request $request): Response
    {
        $userAddress = UserAddress::where('user_id', Auth::id())->first();

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => Auth::user(), // Pass the authenticated user
            'address' => $userAddress, // Pass the user's address
        ]);
    
    }

    //Update the user's profile information.
    public function updateAccount(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'username'    => 'required|string|max:50|unique:users,username,' . $user->id,
            'email'       => 'required|string|email|max:50|unique:users,email,' . $user->id,
            'first_name'  => 'nullable|string|max:50',
            'last_name'   => 'nullable|string|max:50',
            'contact_num' => 'nullable|string|max:20',
        ]);

        $validated['first_name'] = ucwords(strtolower($validated['first_name']));
        $validated['last_name']  = ucwords(strtolower($validated['last_name']));

        $user->update($validated);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    
    public function updateAddress(Request $request): RedirectResponse
    {
    
        $validated = $request->validate([
            'username' => 'required|string|max:50', 
            'street'   => 'nullable|string|max:50',
            'purok'    => 'nullable|string|max:50',
            'barangay' => 'nullable|string|max:50',
            'city'     => 'nullable|string|max:50',
            'zipcode'  => 'nullable|string|max:20',
        ]);

        $validated['street']    = ucwords(strtolower($validated['street']));
        $validated['purok']     = ucwords(strtolower($validated['purok']));
        $validated['barangay']  = ucwords(strtolower($validated['barangay']));
        $validated['city']      = ucwords(strtolower($validated['city']));

        
        $user = \App\Models\User::where('username', $validated['username'])->firstOrFail();

        
        $address = UserAddress::updateOrCreate(
            ['user_id' => $user->id], 
            $validated 
        );
        
        return redirect()->back()->with('success', 'Address updated successfully!');
    }
    
    /* Another version of the profile update with alternate logic.
    public function updateAlternate(ProfileUpdateRequest $request): RedirectResponse
    {
        // Second instance: Alternative validation rules
        $validated = $request->validate([
            'first_name'        =>      'nullable|string|max:255',
            'last_name'         =>      'nullable|string|max:255',
            'contact_num'       =>      'nullable|string|max:11',
            'username'          =>      'nullable|string|max:255|unique:users,username,' . $request->user()->id,
            'email'             =>      'nullable|email|max:255|unique:users,email,' . $request->user()->id,
        ]);

        // Second instance: Alternative fill (only if provided)
        $request->user()->fill([
            'first_name'        =>      $validated['first_name'] ?? $request->user()->first_name,
            'last_name'         =>      $validated['last_name'] ?? $request->user()->last_name,
            'contact_num'       =>      $validated['contact_num'] ?? $request->user()->contact_num,
            'username'          =>      $validated['username'] ?? $request->user()->username,
            'email'             =>      $validated['email'] ?? $request->user()->email,
        ]);

        // Second instance: Reset email verification status if changed
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Second instance: Save updated data
        $request->user()->save();

        // Second instance: Return with alternate status message
        return Redirect::route('profile.edit')->with('status', 'Profile successfully updated with alternative logic!');
    }
        */

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Another version of the account deletion process.
     */
    public function destroyAlternate(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        // Second instance: Another process for session handling and token regeneration
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Second instance: Different redirect target after deletion
        return Redirect::route('home');
    }

    
}
