<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCompanyController extends Controller
{
    /**
     * Tampilkan tabel data instansi mitra untuk pengelolaan kuota & data.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $type = $request->query('type');

        $query = Company::query()->withQuotaCounts()->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('field', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        if ($type && in_array($type, ['pemerintah', 'swasta', 'BUMN', 'NGO'])) {
            $query->where('type', $type);
        }

        $companies = $query->paginate(10)->withQueryString();

        return view('admin.companies.index', compact('companies', 'search', 'type'));
    }

    /**
     * Form tambah instansi baru.
     */
    public function create(): View
    {
        $types = ['pemerintah', 'swasta', 'BUMN', 'NGO'];
        return view('admin.companies.create', compact('types'));
    }

    /**
     * Simpan data instansi baru beserta penetapan kuota resmi.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:companies,name'],
            'type' => ['required', 'in:pemerintah,swasta,BUMN,NGO'],
            'quota' => ['required', 'integer', 'min:0', 'max:100'],
            'field' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:25'],
            'is_active' => ['boolean'],
            'is_approved' => ['boolean'],
        ]);

        $company = Company::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'quota' => $validated['quota'],
            'field' => $validated['field'] ?? null,
            'city' => $validated['city'] ?? null,
            'address' => $validated['address'] ?? null,
            'contact_person' => $validated['contact_person'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'is_approved' => $request->boolean('is_approved', true),
            'requested_by' => null,
        ]);

        return redirect()->route('admin.companies.index')
            ->with('success', "Instansi '{$company->name}' dengan kuota resmi {$company->quota} mahasiswa berhasil ditambahkan.");
    }

    /**
     * Form edit instansi dan pengaturan kuota resmi.
     */
    public function edit(Company $company): View
    {
        $types = ['pemerintah', 'swasta', 'BUMN', 'NGO'];
        return view('admin.companies.edit', compact('company', 'types'));
    }

    /**
     * Update data instansi dan kuota.
     */
    public function update(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:companies,name,' . $company->id],
            'type' => ['required', 'in:pemerintah,swasta,BUMN,NGO'],
            'quota' => ['required', 'integer', 'min:0', 'max:100'],
            'field' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:25'],
            'is_active' => ['boolean'],
            'is_approved' => ['boolean'],
        ]);

        $company->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'quota' => $validated['quota'],
            'field' => $validated['field'] ?? null,
            'city' => $validated['city'] ?? null,
            'address' => $validated['address'] ?? null,
            'contact_person' => $validated['contact_person'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'is_approved' => $request->boolean('is_approved', true),
        ]);

        return redirect()->route('admin.companies.index')
            ->with('success', "Data dan kuota instansi '{$company->name}' berhasil diperbarui.");
    }

    /**
     * Hapus instansi.
     */
    public function destroy(Company $company): RedirectResponse
    {
        // Cek apakah ada permohonan magang aktif
        if ($company->applications()->whereIn('status', ['menunggu', 'diterima'])->exists()) {
            return back()->with('error', "Instansi '{$company->name}' tidak dapat dihapus karena masih memiliki riwayat kelompok pendaftar berstatus aktif/diterima. Anda dapat menonaktifkan status aktifnya saja.");
        }

        $name = $company->name;
        $company->delete();

        return redirect()->route('admin.companies.index')
            ->with('success', "Instansi '{$name}' berhasil dihapus dari sistem.");
    }

    /**
     * Toggle status aktif instansi.
     */
    public function toggleActive(Company $company): RedirectResponse
    {
        $company->update(['is_active' => !$company->is_active]);
        $str = $company->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Instansi '{$company->name}' berhasil {$str}.");
    }
}
