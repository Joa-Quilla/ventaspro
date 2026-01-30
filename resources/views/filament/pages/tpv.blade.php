<x-filament-panels::page>
    <!-- Instrucciones de Atajos -->
    <div
        class="mb-4 p-3 bg-orange-50 dark:bg-orange-500/10 rounded-lg border border-orange-200 dark:border-orange-500/30">
        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
            <span class="font-semibold text-orange-600 dark:text-orange-400">⌨️ Atajos de Teclado:</span>
            <span><kbd
                    class="px-2 py-1 bg-white dark:bg-gray-800 rounded border border-gray-300 dark:border-gray-600 text-xs font-mono">F2</kbd>
                Buscar producto</span>
            <span><kbd
                    class="px-2 py-1 bg-white dark:bg-gray-800 rounded border border-gray-300 dark:border-gray-600 text-xs font-mono">F9</kbd>
                Cobrar</span>
            <span><kbd
                    class="px-2 py-1 bg-white dark:bg-gray-800 rounded border border-gray-300 dark:border-gray-600 text-xs font-mono">ESC</kbd>
                Cancelar</span>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <!-- Panel Izquierdo: Búsqueda y Productos -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Búsqueda de Productos -->
            <x-filament::section>
                <x-slot name="heading">
                    Buscar Producto
                </x-slot>

                <div class="fi-fo-field-wrp">
                    <div class="fi-input-wrp">
                        <input type="text" wire:model.live="searchQuery"
                            placeholder="Escanear código de barras o buscar por nombre..."
                            class="fi-input block w-full rounded-lg border-none bg-white shadow-sm ring-1 ring-gray-950/10 transition duration-75 focus:ring-2 focus:ring-primary-600 dark:bg-white/5 dark:ring-white/20 dark:focus:ring-primary-500 sm:text-sm sm:leading-6"
                            autofocus />
                    </div>
                </div>

                <!-- Resultados de Búsqueda -->
                @if ($searchQuery && count($this->searchResults) > 0)
                    <div
                        style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-top: 1rem; max-height: 24rem; overflow-y: auto;">
                        @foreach ($this->searchResults as $product)
                            <button wire:click="addToCart({{ $product->id }})" type="button"
                                class="fi-btn relative grid w-full place-content-start gap-1.5 rounded-lg p-3 text-start shadow-sm ring-1 ring-gray-950/5 transition duration-75 hover:bg-gray-50 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/10">
                                <div class="font-semibold text-sm text-gray-950 dark:text-white">{{ $product->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $product->sku }}</div>
                                <div class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                    ${{ number_format($product->price, 2) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Stock: {{ $product->stock }}
                                </div>
                            </button>
                        @endforeach
                    </div>
                @elseif($searchQuery)
                    <div style="text-align: center; padding: 2rem; color: #9ca3af;">
                        No se encontraron productos
                    </div>
                @endif
            </x-filament::section>

            <!-- Carrito de Compra -->
            <x-filament::section>
                <x-slot name="heading">
                    Carrito de Compra
                </x-slot>

                @if (count($cart) > 0)
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @foreach ($cart as $index => $item)
                            <div style="display: flex; align-items: center; gap: 1rem; padding: 0.75rem; background: rgba(0,0,0,0.02); border-radius: 0.5rem;"
                                class="dark:bg-white/5">
                                <div style="flex: 1;">
                                    <div class="font-semibold text-sm text-gray-950 dark:text-white">{{ $item['name'] }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        ${{ number_format($item['price'], 2) }} c/u</div>
                                </div>

                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <button wire:click="decrementQuantity({{ $index }})" type="button"
                                        class="fi-btn fi-btn-size-sm relative inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-sm font-semibold shadow-sm ring-1 ring-gray-950/10 transition duration-75 hover:bg-gray-50 dark:ring-white/20 dark:hover:bg-white/10">
                                        -
                                    </button>

                                    <input type="number" wire:model.blur="cart.{{ $index }}.quantity"
                                        class="fi-input w-16 text-center rounded-lg border-none bg-white shadow-sm ring-1 ring-gray-950/10 dark:bg-white/5 dark:ring-white/20 sm:text-sm sm:leading-6"
                                        min="1" />

                                    <button wire:click="incrementQuantity({{ $index }})" type="button"
                                        class="fi-btn fi-btn-size-sm relative inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-sm font-semibold shadow-sm ring-1 ring-gray-950/10 transition duration-75 hover:bg-gray-50 dark:ring-white/20 dark:hover:bg-white/10">
                                        +
                                    </button>
                                </div>

                                <div style="min-width: 6rem; text-align: right;"
                                    class="font-bold text-sm text-gray-950 dark:text-white">
                                    ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                </div>

                                <button wire:click="removeFromCart({{ $index }})" type="button"
                                    class="fi-btn fi-btn-color-danger fi-btn-size-sm relative inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-sm font-semibold text-white shadow-sm bg-red-600 ring-1 ring-red-600 hover:bg-red-500 dark:bg-red-500 dark:ring-red-500 dark:hover:bg-red-400">
                                    ×
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 3rem;" class="text-gray-400 dark:text-gray-500">
                        <svg style="width: 4rem; height: 4rem; margin: 0 auto 1rem; opacity: 0.5;" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p class="font-medium">El carrito está vacío</p>
                        <p class="text-sm mt-1">Busca y agrega productos para comenzar</p>
                    </div>
                @endif
            </x-filament::section>
        </div>

        <!-- Panel Derecho: Resumen y Pago -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Resumen del Total -->
            <x-filament::section>
                <x-slot name="heading">
                    Resumen
                </x-slot>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; justify-content: space-between;"
                        class="text-sm text-gray-600 dark:text-gray-400">
                        <span>Subtotal:</span>
                        <span class="font-semibold">${{ number_format($this->subtotal, 2) }}</span>
                    </div>

                    <div style="border-top: 1px solid rgba(0,0,0,0.1); padding-top: 0.75rem; display: flex; justify-content: space-between; align-items: center;"
                        class="dark:border-white/10">
                        <span class="text-lg font-bold text-gray-950 dark:text-white">Total:</span>
                        <span class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                            ${{ number_format($this->total, 2) }}
                        </span>
                    </div>
                </div>
            </x-filament::section>

            <!-- Cliente -->
            <x-filament::section>
                <x-slot name="heading">
                    Cliente
                </x-slot>

                <div class="fi-fo-field-wrp">
                    <div class="fi-input-wrp">
                        <select wire:model.live="selectedCustomer"
                            class="fi-select fi-input block w-full rounded-lg border-none bg-white shadow-sm ring-1 ring-gray-950/10 transition duration-75 focus:ring-2 focus:ring-primary-600 dark:bg-white/5 dark:ring-white/20 dark:focus:ring-primary-500 sm:text-sm sm:leading-6">
                            <option value="">Cliente General</option>
                            @foreach ($this->customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </x-filament::section>

            <!-- Método de Pago -->
            <x-filament::section>
                <x-slot name="heading">
                    Método de Pago
                </x-slot>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;">
                    <button wire:click="$set('paymentMethod', 'cash')" type="button"
                        style="{{ $paymentMethod === 'cash' ? 'border: 3px solid #f97316; background: #fff7ed;' : 'border: 1px solid #e5e7eb;' }}"
                        class="fi-btn relative grid place-content-center gap-1 p-3 rounded-lg shadow-sm transition duration-75 hover:bg-gray-50 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/10">
                        <div class="text-center">
                            <div class="text-2xl mb-1">💵</div>
                            <div class="text-sm font-semibold"
                                style="{{ $paymentMethod === 'cash' ? 'color: #f97316;' : '' }}">
                                Efectivo
                            </div>
                        </div>
                    </button>

                    <button wire:click="$set('paymentMethod', 'card')" type="button"
                        style="{{ $paymentMethod === 'card' ? 'border: 3px solid #f97316; background: #fff7ed;' : 'border: 1px solid #e5e7eb;' }}"
                        class="fi-btn relative grid place-content-center gap-1 p-3 rounded-lg shadow-sm transition duration-75 hover:bg-gray-50 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/10">
                        <div class="text-center">
                            <div class="text-2xl mb-1">💳</div>
                            <div class="text-sm font-semibold"
                                style="{{ $paymentMethod === 'card' ? 'color: #f97316;' : '' }}">
                                Tarjeta
                            </div>
                        </div>
                    </button>

                    <button wire:click="$set('paymentMethod', 'transfer')" type="button"
                        style="{{ $paymentMethod === 'transfer' ? 'border: 3px solid #f97316; background: #fff7ed;' : 'border: 1px solid #e5e7eb;' }}"
                        class="fi-btn relative grid place-content-center gap-1 p-3 rounded-lg shadow-sm transition duration-75 hover:bg-gray-50 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/10">
                        <div class="text-center">
                            <div class="text-2xl mb-1">🏦</div>
                            <div class="text-sm font-semibold"
                                style="{{ $paymentMethod === 'transfer' ? 'color: #f97316;' : '' }}">
                                Transferencia
                            </div>
                        </div>
                    </button>
                </div>
            </x-filament::section>

            <!-- Botones de Acción -->
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <button wire:click="processSale" @if (count($cart) === 0) disabled @endif type="button"
                    class="fi-btn fi-btn-size-lg relative inline-flex items-center justify-center w-full py-3 text-base font-bold rounded-lg shadow-sm transition duration-75 text-white bg-primary-600 ring-1 ring-primary-600 hover:bg-primary-500 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-primary-500 dark:ring-primary-500 dark:hover:bg-primary-400">
                    Cobrar ${{ number_format($this->total, 2) }}
                </button>

                <button wire:click="clearCart" @if (count($cart) === 0) disabled @endif type="button"
                    class="fi-btn fi-btn-size-md relative inline-flex items-center justify-center w-full py-2 text-sm font-semibold rounded-lg shadow-sm ring-1 ring-gray-950/10 transition duration-75 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:ring-white/20 dark:hover:bg-white/5">
                    Cancelar Venta
                </button>
            </div>
        </div>
    </div>

    @script
        <script>
            // Atajos de teclado
            document.addEventListener('keydown', function(e) {
                // F2: Focus en búsqueda
                if (e.key === 'F2') {
                    e.preventDefault();
                    document.querySelector('input[wire\\:model\\.live="searchQuery"]')?.focus();
                }

                // F9: Procesar venta (cobrar)
                if (e.key === 'F9') {
                    e.preventDefault();
                    const cobrarBtn = document.querySelector('button[wire\\:click="processSale"]');
                    if (cobrarBtn && !cobrarBtn.disabled) {
                        cobrarBtn.click();
                    }
                }

                // ESC: Cancelar venta
                if (e.key === 'Escape') {
                    e.preventDefault();
                    const cancelarBtn = document.querySelector('button[wire\\:click="clearCart"]');
                    if (cancelarBtn && !cancelarBtn.disabled) {
                        cancelarBtn.click();
                    }
                }
            });
        </script>
    @endscript
</x-filament-panels::page>
