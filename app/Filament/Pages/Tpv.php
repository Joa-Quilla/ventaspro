<?php

namespace App\Filament\Pages;

use App\Helpers\AuthHelper;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class Tpv extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'TPV';

    protected static ?string $title = 'Terminal Punto de Venta';

    protected static ?int $navigationSort = -1;

    protected static string|UnitEnum|null $navigationGroup = 'Ventas';

    public string $searchQuery = '';
    public array $cart = [];
    public ?int $selectedCustomer = null;
    public string $paymentMethod = 'cash';

    public function mount(): void
    {
        if (!AuthHelper::hasPermission('tpv.sell')) {
            abort(403);
        }
    }

    public static function canAccess(): bool
    {
        return AuthHelper::hasPermission('tpv.access');
    }

    protected string $view = 'filament.pages.tpv';

    public function getHeading(): string
    {
        return 'Terminal Punto de Venta';
    }

    public function getSearchResultsProperty()
    {
        if (strlen($this->searchQuery) < 2) {
            return [];
        }

        return Product::where('name', 'like', "%{$this->searchQuery}%")
            ->orWhere('sku', 'like', "%{$this->searchQuery}%")
            ->orWhere('barcode', 'like', "%{$this->searchQuery}%")
            ->where('stock', '>', 0)
            ->limit(12)
            ->get();
    }

    public function getCustomersProperty()
    {
        return Customer::orderBy('name')->get();
    }

    public function addToCart(int $productId): void
    {
        $product = Product::find($productId);

        if (!$product || $product->stock <= 0) {
            Notification::make()
                ->danger()
                ->title('Producto no disponible')
                ->send();
            return;
        }

        // Buscar si el producto ya está en el carrito
        $existingIndex = null;
        foreach ($this->cart as $index => $item) {
            if ($item['product_id'] === $productId) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            // Incrementar cantidad si ya existe
            if ($this->cart[$existingIndex]['quantity'] < $product->stock) {
                $this->cart[$existingIndex]['quantity']++;
            } else {
                Notification::make()
                    ->warning()
                    ->title('Stock insuficiente')
                    ->send();
            }
        } else {
            // Agregar nuevo producto
            $this->cart[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->price,
                'quantity' => 1,
                'stock' => $product->stock,
            ];
        }

        $this->searchQuery = '';
    }

    public function removeFromCart(int $index): void
    {
        array_splice($this->cart, $index, 1);
    }

    public function incrementQuantity(int $index): void
    {
        if ($this->cart[$index]['quantity'] < $this->cart[$index]['stock']) {
            $this->cart[$index]['quantity']++;
        } else {
            Notification::make()
                ->warning()
                ->title('Stock insuficiente')
                ->send();
        }
    }

    public function decrementQuantity(int $index): void
    {
        if ($this->cart[$index]['quantity'] > 1) {
            $this->cart[$index]['quantity']--;
        }
    }

    public function getSubtotalProperty(): float
    {
        return array_reduce($this->cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);
    }

    public function getTotalProperty(): float
    {
        return $this->subtotal;
    }

    public function processSale(): void
    {
        if (empty($this->cart)) {
            Notification::make()
                ->warning()
                ->title('Carrito vacío')
                ->body('Agrega productos al carrito antes de procesar la venta')
                ->send();
            return;
        }

        DB::transaction(function () {
            // Obtener datos del cliente si fue seleccionado
            $customer = $this->selectedCustomer ? Customer::find($this->selectedCustomer) : null;

            // Crear la venta
            $sale = Sale::create([
                'user_id' => Auth::id(),
                'customer_id' => $customer?->id,
                'customer_name' => $customer?->name,
                'customer_phone' => $customer?->phone,
                'subtotal' => $this->subtotal,
                'tax' => 0,
                'total' => $this->total,
                'payment_method' => $this->paymentMethod,
                'status' => 'completed',
                'notes' => 'Venta desde TPV',
            ]);

            // Agregar productos y actualizar stock
            foreach ($this->cart as $item) {
                $sale->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);

                // Reducir stock
                $product = Product::find($item['product_id']);
                $product->decrement('stock', $item['quantity']);
            }

            Notification::make()
                ->success()
                ->title('Venta procesada')
                ->body("Venta #{$sale->id} registrada exitosamente")
                ->send();

            // Limpiar el carrito
            $this->clearCart();
        });
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->selectedCustomer = null;
        $this->paymentMethod = 'cash';
        $this->searchQuery = '';
    }
}
