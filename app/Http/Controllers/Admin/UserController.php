<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role_filter') && !empty($request->role_filter)) {
            $query->where('role', $request->role_filter);
        }

        // Filter by status
        if ($request->has('status_filter') && !empty($request->status_filter)) {
            if ($request->status_filter === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status_filter === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by registration method
        if ($request->has('filter') && !empty($request->filter)) {
            if ($request->filter === 'google') {
                $query->whereNotNull('google_id');
            } elseif ($request->filter === 'email') {
                $query->whereNull('google_id');
            }
        }

        // Sort functionality
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        $allowedSorts = ['first_name', 'last_name', 'email', 'created_at', 'email_verified_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:admin,user,supplier',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'company_name' => 'required_if:role,supplier|nullable|string|max:255',
            'company_description' => 'nullable|string|max:1000',
            'business_license' => 'nullable|string|max:255',
            'supplier_categories' => 'nullable|array',
            'password' => 'required|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $userData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => $request->has('is_active'),
            'phone' => $request->phone,
            'city' => $request->city,
            'company_name' => $request->company_name,
            'company_description' => $request->company_description,
            'business_license' => $request->business_license,
            'supplier_categories' => $request->supplier_categories,
            'password' => Hash::make($request->password),
            'newsletter_subscription' => $request->has('newsletter_subscription'),
            'terms_accepted' => true,
            'email_verified_at' => now(),
        ];

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $userData['profile_picture'] = $path;
        }

        User::create($userData);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès !');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,user,supplier',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'company_name' => 'required_if:role,supplier|nullable|string|max:255',
            'company_description' => 'nullable|string|max:1000',
            'business_license' => 'nullable|string|max:255',
            'supplier_categories' => 'nullable|array',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $userData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => $request->has('is_active'),
            'phone' => $request->phone,
            'city' => $request->city,
            'company_name' => $request->company_name,
            'company_description' => $request->company_description,
            'business_license' => $request->business_license,
            'supplier_categories' => $request->supplier_categories,
            'newsletter_subscription' => $request->has('newsletter_subscription'),
        ];

        // Update password only if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $userData['profile_picture'] = $path;
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès !');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Delete profile picture if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès !');
    }

    /**
     * Toggle user email verification status
     */
    public function toggleVerification(User $user)
    {
        $user->update([
            'email_verified_at' => $user->email_verified_at ? null : now()
        ]);

        $status = $user->email_verified_at ? 'vérifié' : 'non vérifié';
        return redirect()->back()
            ->with('success', "Statut de vérification mis à jour : {$status}");
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activé' : 'désactivé';
        return redirect()->back()
            ->with('success', "Utilisateur {$status} avec succès !");
    }

    /**
     * Export users data
     */
    public function export(Request $request)
    {
        $users = User::all();
        
        $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'Prénom', 'Nom', 'Email', 'Téléphone', 'Ville', 
                'Newsletter', 'Vérifié', 'Google ID', 'Date création'
            ]);

            // CSV data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->first_name,
                    $user->last_name,
                    $user->email,
                    $user->phone,
                    $user->city,
                    $user->newsletter_subscription ? 'Oui' : 'Non',
                    $user->email_verified_at ? 'Oui' : 'Non',
                    $user->google_id ? 'Oui' : 'Non',
                    $user->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
