<?php
// File: app/Services/AuthService.php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthService
{
    /**
     * Authenticate user
     */
    public function authenticate(array $credentials, $remember = false)
    {
        $email = $credentials['email'];
        $password = $credentials['password'];

        // Find user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        // Check if account is locked
        if ($user->locked_until && $user->locked_until > now()) {
            return ['success' => false, 'message' => 'Account is temporarily locked'];
        }

        // Check if account is active
        if (!$user->is_active) {
            return ['success' => false, 'message' => 'Account has been deactivated'];
        }

        // Verify password
        if (!Hash::check($password, $user->password_hash)) {
            // Increment failed login attempts
            $user->increment('login_attempts');

            // Lock account after 5 failed attempts
            if ($user->login_attempts >= 5) {
                $user->update(['locked_until' => now()->addMinutes(30)]);
                return ['success' => false, 'message' => 'Account locked due to multiple failed attempts'];
            }

            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        // Reset login attempts on successful login
        $user->update([
            'login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now()
        ]);

        // Login user
        Auth::login($user, $remember);

        return ['success' => true, 'user' => $user];
    }

    /**
     * Register new user
     */
    public function register(array $data)
    {
        // Get customer role
        $customerRole = Role::where('name', 'customer')->first();

        if (!$customerRole) {
            throw new \Exception('Customer role not found. Please contact administrator.');
        }

        // Create user
        $user = User::create([
            'role_id' => $customerRole->id,
            'username' => $this->generateUsername($data['first_name'], $data['last_name']),
            'email' => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'gender' => $data['gender'] ?? null,
            'is_active' => true
        ]);

        return $user;
    }

    /**
     * Update user profile
     */
    public function updateProfile(User $user, array $data)
    {
        // Handle avatar upload
        if (isset($data['avatar'])) {
            $data['avatar'] = $this->uploadAvatar($data['avatar'], $user);
        }

        $user->update($data);

        return $user;
    }

    /**
     * Change user password
     */
    public function changePassword(User $user, array $data)
    {
        // Verify current password
        if (!Hash::check($data['current_password'], $user->password_hash)) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }

        // Update password
        $user->update([
            'password_hash' => Hash::make($data['password'])
        ]);

        return ['success' => true, 'message' => 'Password changed successfully'];
    }

    /**
     * Generate unique username
     */
    protected function generateUsername($firstName, $lastName)
    {
        $baseUsername = strtolower($firstName . $lastName);
        $baseUsername = preg_replace('/[^a-z0-9]/', '', $baseUsername);

        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Upload user avatar
     */
    protected function uploadAvatar($file, User $user)
    {
        // Delete old avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('avatars', $filename, 'public');
    }
}
