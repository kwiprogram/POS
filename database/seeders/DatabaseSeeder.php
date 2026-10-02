<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Coffee;
use App\Models\Table;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@coffee.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $cashier = User::create([
            'name' => 'Sarah Cashier',
            'email' => 'cashier@coffee.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'status' => 'active',
        ]);

        // 2. Settings
        Setting::set('shop_name', 'Artisan Coffee House');
        Setting::set('shop_address', '123 Monivong Blvd, Phnom Penh, Cambodia');
        Setting::set('shop_phone', '+855 12 345 678');
        Setting::set('tax_percentage', '5');
        Setting::set('currency_symbol', '$');
        Setting::set('receipt_footer', 'Thank you for visiting Artisan Coffee House! Have a caffeinated day! ☕');

        // 3. Categories
        $catEspresso = Category::create(['name' => 'Espresso & Hot Coffee', 'description' => 'Classic brewed hot coffee & espresso options', 'status' => 'active']);
        $catCold = Category::create(['name' => 'Cold Brew & Iced Teas', 'description' => 'Refreshing chilled coffees and craft teas', 'status' => 'active']);
        $catPastry = Category::create(['name' => 'Pastries & Bakery', 'description' => 'Freshly baked croissants, cookies and snacks', 'status' => 'active']);
        $catBlended = Category::create(['name' => 'Smoothies & Frappes', 'description' => 'Blended iced delights and smoothies', 'status' => 'active']);
        $catSpecial = Category::create(['name' => 'Seasonal Specials', 'description' => 'Limited time artisan seasonal creations', 'status' => 'active']);

        // 4. Coffees
        $coffeesData = [
            ['category_id' => $catEspresso->id, 'name' => 'Double Espresso', 'price' => 2.50, 'stock' => 100, 'status' => 'available', 'description' => 'Rich concentrated double shot of specialty arabica.'],
            ['category_id' => $catEspresso->id, 'name' => 'Caffè Americano', 'price' => 3.00, 'stock' => 100, 'status' => 'available', 'description' => 'Espresso diluted with hot filtered water.'],
            ['category_id' => $catEspresso->id, 'name' => 'Caffè Latte', 'price' => 3.75, 'stock' => 80, 'status' => 'available', 'description' => 'Espresso with steamed milk and a thin layer of micro-foam.'],
            ['category_id' => $catEspresso->id, 'name' => 'Cappuccino', 'price' => 3.75, 'stock' => 80, 'status' => 'available', 'description' => 'Equal parts espresso, steamed milk, and airy foam.'],
            ['category_id' => $catEspresso->id, 'name' => 'Caramel Macchiato', 'price' => 4.25, 'stock' => 60, 'status' => 'available', 'description' => 'Steamed milk with vanilla syrup, marked with espresso & caramel drizzle.'],
            ['category_id' => $catEspresso->id, 'name' => 'Mocha Deluxe', 'price' => 4.50, 'stock' => 50, 'status' => 'available', 'description' => 'Espresso blended with dark Belgian chocolate and whipped cream.'],
            
            ['category_id' => $catCold->id, 'name' => 'Vanilla Cold Brew', 'price' => 4.00, 'stock' => 60, 'status' => 'available', 'description' => '18-hour slow steeped cold brew infused with real vanilla bean.'],
            ['category_id' => $catCold->id, 'name' => 'Iced Matcha Latte', 'price' => 4.50, 'stock' => 45, 'status' => 'available', 'description' => 'Ceremonial grade Uji matcha with oat milk over ice.'],
            ['category_id' => $catCold->id, 'name' => 'Iced Peach Hibiscus Tea', 'price' => 3.50, 'stock' => 50, 'status' => 'available', 'description' => 'Tart hibiscus flower iced tea naturally sweetened with peach.'],
            
            ['category_id' => $catPastry->id, 'name' => 'French Butter Croissant', 'price' => 2.50, 'stock' => 30, 'status' => 'available', 'description' => 'Flaky, buttery multi-layered traditional croissant.'],
            ['category_id' => $catPastry->id, 'name' => 'Blueberry Muffin', 'price' => 2.75, 'stock' => 25, 'status' => 'available', 'description' => 'Moist muffin packed with wild blueberries & crumble top.'],
            ['category_id' => $catPastry->id, 'name' => 'Dark Choco Chip Cookie', 'price' => 2.00, 'stock' => 40, 'status' => 'available', 'description' => 'Chewy giant cookie filled with 70% dark chocolate chunks.'],

            ['category_id' => $catBlended->id, 'name' => 'Java Chip Frappé', 'price' => 4.95, 'stock' => 35, 'status' => 'available', 'description' => 'Blended espresso, chocolate chips, milk, and whipped cream.'],
            ['category_id' => $catBlended->id, 'name' => 'Mango Passion Smoothie', 'price' => 4.50, 'stock' => 30, 'status' => 'available', 'description' => 'Tropical blend of real mango pulp & passionfruit.'],

            ['category_id' => $catSpecial->id, 'name' => 'Pistachio Cream Latte', 'price' => 5.25, 'stock' => 20, 'status' => 'available', 'description' => 'Rich espresso topped with sweet pistachio cold foam & crushed nuts.'],
        ];

        foreach ($coffeesData as $c) {
            Coffee::create($c);
        }

        // 5. Tables
        $tables = [
            ['table_number' => 'Takeaway / Counter', 'capacity' => 0, 'status' => 'available'],
            ['table_number' => 'Table 01', 'capacity' => 2, 'status' => 'available'],
            ['table_number' => 'Table 02', 'capacity' => 2, 'status' => 'occupied'],
            ['table_number' => 'Table 03', 'capacity' => 4, 'status' => 'reserved'],
            ['table_number' => 'Table 04', 'capacity' => 4, 'status' => 'available'],
            ['table_number' => 'Table 05', 'capacity' => 6, 'status' => 'available'],
        ];

        foreach ($tables as $t) {
            Table::create($t);
        }

        // 6. Customers
        $c1 = Customer::create(['name' => 'Walk-in Customer', 'phone' => null, 'member_status' => 'regular']);
        $c2 = Customer::create(['name' => 'Sokha Chan', 'phone' => '012888999', 'member_status' => 'member']);
        $c3 = Customer::create(['name' => 'Bopha Vong', 'phone' => '097777111', 'member_status' => 'vip']);

        // 7. Seed Demo Orders & Payments (for sales reporting and analytics)
        $table2 = Table::where('table_number', 'Table 02')->first();
        $latte = Coffee::where('name', 'Caffè Latte')->first();
        $croissant = Coffee::where('name', 'French Butter Croissant')->first();
        $coldBrew = Coffee::where('name', 'Vanilla Cold Brew')->first();

        // Sample completed order 1
        $ord1 = Order::create([
            'order_number' => 'ORD-' . date('Ymd') . '-0001',
            'customer_id' => $c2->id,
            'table_id' => $table2->id,
            'user_id' => $cashier->id,
            'subtotal' => 6.25,
            'discount' => 0.00,
            'tax' => 0.31,
            'total' => 6.56,
            'payment_status' => 'paid',
            'order_status' => 'completed',
            'created_at' => Carbon::now()->subHours(2),
        ]);

        OrderItem::create([
            'order_id' => $ord1->id,
            'coffee_id' => $latte->id,
            'quantity' => 1,
            'price' => 3.75,
            'discount' => 0.00,
            'subtotal' => 3.75,
        ]);

        OrderItem::create([
            'order_id' => $ord1->id,
            'coffee_id' => $croissant->id,
            'quantity' => 1,
            'price' => 2.50,
            'discount' => 0.00,
            'subtotal' => 2.50,
        ]);

        Payment::create([
            'order_id' => $ord1->id,
            'payment_method' => 'aba_khqr',
            'subtotal' => 6.25,
            'discount' => 0.00,
            'tax' => 0.31,
            'total' => 6.56,
            'amount_paid' => 6.56,
            'change' => 0.00,
            'paid_at' => Carbon::now()->subHours(2),
        ]);

        // Sample pending order 2
        $ord2 = Order::create([
            'order_number' => 'ORD-' . date('Ymd') . '-0002',
            'customer_id' => $c1->id,
            'table_id' => $table2->id,
            'user_id' => $cashier->id,
            'subtotal' => 8.00,
            'discount' => 0.00,
            'tax' => 0.40,
            'total' => 8.40,
            'payment_status' => 'unpaid',
            'order_status' => 'preparing',
            'created_at' => Carbon::now()->subMinutes(15),
        ]);

        OrderItem::create([
            'order_id' => $ord2->id,
            'coffee_id' => $coldBrew->id,
            'quantity' => 2,
            'price' => 4.00,
            'discount' => 0.00,
            'subtotal' => 8.00,
        ]);
    }
}
