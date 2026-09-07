<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockMovement;
use App\Models\StockOut;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InventorySystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $petugas;
    protected User $pimpinan;
    protected Category $category;
    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test@pos.co.id'],
            [
                'name' => 'Admin Test',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $this->petugas = User::firstOrCreate(
            ['email' => 'petugas_test@pos.co.id'],
            [
                'name' => 'Petugas Test',
                'password' => Hash::make('password'),
                'role' => 'petugas',
            ]
        );

        $this->pimpinan = User::firstOrCreate(
            ['email' => 'pimpinan_test@pos.co.id'],
            [
                'name' => 'Pimpinan Test',
                'password' => Hash::make('password'),
                'role' => 'pimpinan',
            ]
        );

        $this->category = Category::firstOrCreate(
            ['name' => 'Kategori Unit Test'],
            ['description' => 'Deskripsi Unit Test']
        );

        $this->item = Item::firstOrCreate(
            ['name' => 'Barang Unit Test'],
            [
                'category_id' => $this->category->id,
                'unit' => 'Pcs',
                'stock' => 100,
                'minimum_stock' => 10,
                'location' => 'Rak Test',
                'description' => 'Deskripsi barang test',
                'status' => true,
            ]
        );
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin_test@pos.co.id',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_admin_can_access_dashboard_and_user_management(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Inventory');

        $usersResponse = $this->actingAs($this->admin)->get('/users');
        $usersResponse->assertStatus(200);
        $usersResponse->assertSee('Manajemen Pengguna');
    }

    public function test_petugas_cannot_access_user_management(): void
    {
        $response = $this->actingAs($this->petugas)->get('/users');
        $response->assertStatus(403);
    }

    public function test_admin_can_create_category(): void
    {
        $uniqueName = 'Kategori Baru ' . uniqid();
        $response = $this->actingAs($this->admin)->post('/categories', [
            'name' => $uniqueName,
            'description' => 'Kategori baru untuk pengujian',
        ]);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', ['name' => $uniqueName]);
    }

    public function test_cannot_delete_category_with_items(): void
    {
        $response = $this->actingAs($this->admin)->delete('/categories/' . $this->category->id);
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $this->category->id]);
    }

    public function test_stock_in_increases_stock_and_creates_movement(): void
    {
        $initialStock = $this->item->fresh()->stock;
        $inQty = 25;
        $trxNumber = 'IN-TEST-' . uniqid();

        $response = $this->actingAs($this->petugas)->post('/stock-ins', [
            'transaction_number' => $trxNumber,
            'transaction_date' => date('Y-m-d'),
            'notes' => 'Test penerimaan stok',
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'quantity' => $inQty,
                ],
            ],
        ]);

        $response->assertRedirect('/stock-ins');
        $this->assertEquals($initialStock + $inQty, $this->item->fresh()->stock);

        $this->assertDatabaseHas('stock_ins', ['transaction_number' => $trxNumber]);
        $this->assertDatabaseHas('stock_movements', [
            'item_id' => $this->item->id,
            'type' => 'IN',
            'quantity' => $inQty,
            'stock_after' => $initialStock + $inQty,
        ]);
    }

    public function test_stock_out_decreases_stock_and_creates_movement(): void
    {
        $initialStock = $this->item->fresh()->stock;
        $outQty = 15;
        $trxNumber = 'OUT-TEST-' . uniqid();

        $response = $this->actingAs($this->petugas)->post('/stock-outs', [
            'transaction_number' => $trxNumber,
            'transaction_date' => date('Y-m-d'),
            'notes' => 'Test pengeluaran stok',
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'quantity' => $outQty,
                ],
            ],
        ]);

        $response->assertRedirect('/stock-outs');
        $this->assertEquals($initialStock - $outQty, $this->item->fresh()->stock);

        $this->assertDatabaseHas('stock_outs', ['transaction_number' => $trxNumber]);
        $this->assertDatabaseHas('stock_movements', [
            'item_id' => $this->item->id,
            'type' => 'OUT',
            'quantity' => $outQty,
            'stock_after' => $initialStock - $outQty,
        ]);
    }

    public function test_stock_out_fails_when_quantity_exceeds_stock(): void
    {
        $currentStock = $this->item->fresh()->stock;
        $excessiveQty = $currentStock + 500;
        $trxNumber = 'OUT-FAIL-' . uniqid();

        $response = $this->actingAs($this->petugas)->post('/stock-outs', [
            'transaction_number' => $trxNumber,
            'transaction_date' => date('Y-m-d'),
            'notes' => 'Test pengeluaran berlebih',
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'quantity' => $excessiveQty,
                ],
            ],
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals($currentStock, $this->item->fresh()->stock);
        $this->assertDatabaseMissing('stock_outs', ['transaction_number' => $trxNumber]);
    }

    public function test_reports_pages_render_successfully(): void
    {
        $this->actingAs($this->pimpinan)->get('/reports')->assertStatus(200);
        $this->actingAs($this->pimpinan)->get('/reports/stock')->assertStatus(200);
        $this->actingAs($this->pimpinan)->get('/reports/stock-in')->assertStatus(200);
        $this->actingAs($this->pimpinan)->get('/reports/stock-out')->assertStatus(200);
        $this->actingAs($this->pimpinan)->get('/reports/movements')->assertStatus(200);
    }
}
