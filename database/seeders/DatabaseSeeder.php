<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockInDetail;
use App\Models\StockMovement;
use App\Models\StockOut;
use App\Models\StockOutDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic internal pos inventory data.
     */
    public function run(): void
    {
        // 1. Create Default Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@pos.co.id'],
            [
                'name' => 'Administrator Pos',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $petugas = User::firstOrCreate(
            ['email' => 'petugas@pos.co.id'],
            [
                'name' => 'Petugas Inventaris & Logistik',
                'password' => Hash::make('password'),
                'role' => 'petugas',
            ]
        );

        $pimpinan = User::firstOrCreate(
            ['email' => 'pimpinan@pos.co.id'],
            [
                'name' => 'Kepala Kantor Pos',
                'password' => Hash::make('password'),
                'role' => 'pimpinan',
            ]
        );

        // 2. Create Categories
        $categoriesData = [
            [
                'name' => 'Perlengkapan & Segel Pos',
                'description' => 'Segel plastik (plastic seal), timbel, gembok kantong, dan perlengkapan keamanan kiriman pos.',
            ],
            [
                'name' => 'Kantong & Karung Kiriman',
                'description' => 'Kantong kanvas pos, karung pos plastik oranye, dan wadah konsolidasi kiriman.',
            ],
            [
                'name' => 'Alat Tulis Kantor (ATK)',
                'description' => 'Kertas thermal struk, pulpen loket, staples, tinta cap pos, dan perlengkapan administrasi.',
            ],
            [
                'name' => 'Dokumen & Formulir Pos',
                'description' => 'Formulir bukti kirim khusus, amplop resmi kantor pos, resi manual, dan label alamat.',
            ],
            [
                'name' => 'Material Packing & Lakban',
                'description' => 'Lakban bertuliskan Pos Indonesia, bubble wrap, plastik pelindung paket, dan tali pengikat.',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $catData) {
            $categories[$catData['name']] = Category::firstOrCreate(
                ['name' => $catData['name']],
                ['description' => $catData['description']]
            );
        }

        // 3. Create Items
        $itemsData = [
            [
                'category' => 'Perlengkapan & Segel Pos',
                'name' => 'Segel Plastik Kuning Bersegel Pos (Plastic Seal)',
                'unit' => 'Pcs',
                'stock' => 1500,
                'minimum_stock' => 300,
                'location' => 'Rak A-01 Gudang Logistik',
                'description' => 'Segel pengaman bernomor seri untuk kantong kiriman berharga.',
            ],
            [
                'category' => 'Perlengkapan & Segel Pos',
                'name' => 'Tinta Cap Tanggal Pos (Stamp Ink Red/Black)',
                'unit' => 'Botol',
                'stock' => 8,
                'minimum_stock' => 10, // menipis
                'location' => 'Lemari B-02',
                'description' => 'Tinta cap khusus tanggal kiriman loket pos.',
            ],
            [
                'category' => 'Kantong & Karung Kiriman',
                'name' => 'Kantong Kanvas Pos Oranye Ukuran Besar',
                'unit' => 'Lembar',
                'stock' => 120,
                'minimum_stock' => 30,
                'location' => 'Palet C-01 Gudang Distribusi',
                'description' => 'Kantong kain kanvas tebal untuk kiriman pos kilat khusus antarkota.',
            ],
            [
                'category' => 'Kantong & Karung Kiriman',
                'name' => 'Karung Plastik Pos Sedang 50kg',
                'unit' => 'Lembar',
                'stock' => 0, // habis
                'minimum_stock' => 50,
                'location' => 'Palet C-02',
                'description' => 'Karung pelindung kiriman paket pos standar.',
            ],
            [
                'category' => 'Material Packing & Lakban',
                'name' => 'Lakban Cokelat Bertuliskan Logo Pos Indonesia',
                'unit' => 'Roll',
                'stock' => 350,
                'minimum_stock' => 50,
                'location' => 'Rak D-03',
                'description' => 'Lakban segel resmi untuk pengemasan paket kiriman pos.',
            ],
            [
                'category' => 'Material Packing & Lakban',
                'name' => 'Bubble Wrap Tebal 50m x 1.25m',
                'unit' => 'Roll',
                'stock' => 3,
                'minimum_stock' => 5, // menipis
                'location' => 'Area Packing Gudang',
                'description' => 'Plastik gelembung pelindung kiriman barang pecah belah.',
            ],
            [
                'category' => 'Alat Tulis Kantor (ATK)',
                'name' => 'Kertas Thermal Struk Loket 80mm',
                'unit' => 'Roll',
                'stock' => 200,
                'minimum_stock' => 40,
                'location' => 'Lemari ATK Lt. 1',
                'description' => 'Kertas kasir thermal untuk pencetakan bukti resi loket kantor pos.',
            ],
            [
                'category' => 'Dokumen & Formulir Pos',
                'name' => 'Amplop Bergaris Air Mail Resmi Pos',
                'unit' => 'Pack',
                'stock' => 85,
                'minimum_stock' => 20,
                'location' => 'Rak Dokumen E-01',
                'description' => 'Amplop surat udara resmi untuk kiriman dokumen dinas & luar negeri.',
            ],
        ];

        foreach ($itemsData as $it) {
            $cat = $categories[$it['category']];
            $item = Item::firstOrCreate(
                ['name' => $it['name']],
                [
                    'category_id' => $cat->id,
                    'unit' => $it['unit'],
                    'stock' => $it['stock'],
                    'minimum_stock' => $it['minimum_stock'],
                    'location' => $it['location'],
                    'description' => $it['description'],
                    'status' => true,
                ]
            );

            // Record initial movement log if new
            if ($item->wasRecentlyCreated && $item->stock > 0) {
                StockMovement::create([
                    'item_id' => $item->id,
                    'user_id' => $admin->id,
                    'type' => 'IN',
                    'quantity' => $item->stock,
                    'stock_before' => 0,
                    'stock_after' => $item->stock,
                    'reference_type' => 'InitialStock',
                    'reference_id' => $item->id,
                    'notes' => 'Pencatatan saldo awal stok barang kantor pos.',
                    'created_at' => now()->subDays(10),
                ]);
            }
        }

        // 4. Sample Transaction Stock In
        $segel = Item::where('name', 'like', '%Segel Plastik%')->first();
        $lakban = Item::where('name', 'like', '%Lakban Cokelat%')->first();
        if ($segel && $lakban && !StockIn::where('transaction_number', 'IN-20260901-0001')->exists()) {
            $stockIn = StockIn::create([
                'transaction_number' => 'IN-20260901-0001',
                'transaction_date' => now()->subDays(5)->toDateString(),
                'user_id' => $petugas->id,
                'notes' => 'Penerimaan pasokan segel dan lakban dari Bagian Pengadaan Pusat.',
                'created_at' => now()->subDays(5),
            ]);

            StockInDetail::create([
                'stock_in_id' => $stockIn->id,
                'item_id' => $segel->id,
                'quantity' => 500,
            ]);

            StockInDetail::create([
                'stock_in_id' => $stockIn->id,
                'item_id' => $lakban->id,
                'quantity' => 100,
            ]);

            StockMovement::create([
                'item_id' => $segel->id,
                'user_id' => $petugas->id,
                'type' => 'IN',
                'quantity' => 500,
                'stock_before' => 1000,
                'stock_after' => 1500,
                'reference_type' => 'StockIn',
                'reference_id' => $stockIn->id,
                'notes' => 'Stok Masuk No. ' . $stockIn->transaction_number,
                'created_at' => now()->subDays(5),
            ]);

            StockMovement::create([
                'item_id' => $lakban->id,
                'user_id' => $petugas->id,
                'type' => 'IN',
                'quantity' => 100,
                'stock_before' => 250,
                'stock_after' => 350,
                'reference_type' => 'StockIn',
                'reference_id' => $stockIn->id,
                'notes' => 'Stok Masuk No. ' . $stockIn->transaction_number,
                'created_at' => now()->subDays(5),
            ]);
        }

        // 5. Sample Transaction Stock Out
        $thermal = Item::where('name', 'like', '%Kertas Thermal%')->first();
        if ($thermal && !StockOut::where('transaction_number', 'OUT-20260903-0001')->exists()) {
            $stockOut = StockOut::create([
                'transaction_number' => 'OUT-20260903-0001',
                'transaction_date' => now()->subDays(3)->toDateString(),
                'user_id' => $petugas->id,
                'notes' => 'Distribusi kebutuhan kertas struk untuk loket pelayanan pos cabang.',
                'created_at' => now()->subDays(3),
            ]);

            StockOutDetail::create([
                'stock_out_id' => $stockOut->id,
                'item_id' => $thermal->id,
                'quantity' => 20,
            ]);

            StockMovement::create([
                'item_id' => $thermal->id,
                'user_id' => $petugas->id,
                'type' => 'OUT',
                'quantity' => 20,
                'stock_before' => 220,
                'stock_after' => 200,
                'reference_type' => 'StockOut',
                'reference_id' => $stockOut->id,
                'notes' => 'Stok Keluar No. ' . $stockOut->transaction_number . ' - Distribusi loket cabang',
                'created_at' => now()->subDays(3),
            ]);
        }
    }
}
