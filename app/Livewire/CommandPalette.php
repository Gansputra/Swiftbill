<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class CommandPalette extends Component
{
    public $search = '';

    public function render()
    {
        $products = [];
        if (!empty(trim($this->search))) {
            $products = Product::with('category')
                ->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('sku', 'like', '%' . $this->search . '%')
                ->take(5)
                ->get();
        }

        $allNavigation = [
            [
                'title' => 'Dashboard Utama',
                'subtitle' => 'Ringkasan performa & statistik toko',
                'route' => route('dashboard'),
                'icon' => 'home',
                'role' => 'all',
            ],
            [
                'title' => 'Kasir (POS)',
                'subtitle' => 'Terminal transaksi & cetak struk',
                'route' => route('pos.index'),
                'icon' => 'shopping-cart',
                'role' => 'all',
            ],
            [
                'title' => 'Manajemen Kas',
                'subtitle' => 'Buku kas operasional & rekonsiliasi',
                'route' => route('cash-management'),
                'icon' => 'banknotes',
                'role' => 'all',
            ],
            [
                'title' => 'Kategori Produk',
                'subtitle' => 'Klasifikasi & pengelompokan produk',
                'route' => route('categories.index'),
                'icon' => 'tag',
                'role' => 'admin',
            ],
            [
                'title' => 'Pemasok & Vendor',
                'subtitle' => 'Jaringan pemasok & procurement',
                'route' => route('suppliers.index'),
                'icon' => 'truck',
                'role' => 'admin',
            ],
            [
                'title' => 'Katalog Produk',
                'subtitle' => 'Daftar barang, SKU & pengelolaan stok',
                'route' => route('products.index'),
                'icon' => 'cube',
                'role' => 'admin',
            ],
            [
                'title' => 'Riwayat Stok & Mutasi',
                'subtitle' => 'Audit persediaan & barang masuk/keluar',
                'route' => route('stock-movements.index'),
                'icon' => 'arrows-right-left',
                'role' => 'admin',
            ],
            [
                'title' => 'Wawasan AI Gemini',
                'subtitle' => 'Analisis bisnis pintar & asisten virtual',
                'route' => route('ai-dashboard'),
                'icon' => 'light-bulb',
                'role' => 'admin',
            ],
            [
                'title' => 'Laporan Penjualan',
                'subtitle' => 'Rekapitulasi pendapatan & laba rugi',
                'route' => route('reports.sales'),
                'icon' => 'chart-bar',
                'role' => 'admin',
            ],
            [
                'title' => 'Log Shift Kasir',
                'subtitle' => 'Audit riwayat shift & penutupan kas',
                'route' => route('reports.shifts'),
                'icon' => 'clock',
                'role' => 'admin',
            ],
            [
                'title' => 'Kelola Karyawan',
                'subtitle' => 'Pengaturan pengguna & hak akses role',
                'route' => route('users.index'),
                'icon' => 'users',
                'role' => 'admin',
            ],
            [
                'title' => 'Profil Pengguna',
                'subtitle' => 'Pengaturan foto profil & kata sandi',
                'route' => route('profile'),
                'icon' => 'user-circle',
                'role' => 'all',
            ],
        ];

        $userRole = auth()->user()->role ?? 'cashier';

        $filteredNavigation = collect($allNavigation)
            ->filter(function ($item) use ($userRole) {
                if ($item['role'] === 'admin' && $userRole !== 'admin') {
                    return false;
                }
                if (empty(trim($this->search))) {
                    return true;
                }
                return str_contains(strtolower($item['title']), strtolower($this->search))
                    || str_contains(strtolower($item['subtitle']), strtolower($this->search));
            })
            ->values();

        return view('livewire.command-palette', [
            'products' => $products,
            'navigation' => $filteredNavigation,
        ]);
    }
}
