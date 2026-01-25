<x-filament-panels::page>
    {{-- Sección de Filtros --}}
    <x-filament::section>
        <x-slot name="heading">
            Filtros del Reporte
        </x-slot>

        <div class="grid grid-cols-3 gap-6" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
            <div class="fi-fo-field-wrp">
                <div class="grid gap-y-2">
                    <label for="reportType"
                        class="fi-fo-field-wrp-label inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Tipo de Reporte
                    </label>
                    <div class="fi-input-wrp">
                        <select wire:model.live="reportType" id="reportType"
                            class="fi-input fi-select block w-full rounded-lg border-none bg-white shadow-sm ring-1 ring-gray-950/10 transition duration-75 focus:ring-2 focus:ring-primary-600 disabled:opacity-70 dark:bg-white/5 dark:ring-white/20 dark:focus:ring-primary-500 sm:text-sm sm:leading-6">
                            <option value="sales">Reporte de Ventas</option>
                            <option value="products">Productos Más Vendidos</option>
                            <option value="low_stock">Productos con Stock Bajo</option>
                            <option value="inventory">Inventario Completo</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="fi-fo-field-wrp">
                <div class="grid gap-y-2">
                    <label for="startDate"
                        class="fi-fo-field-wrp-label inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Fecha Inicio
                    </label>
                    <div class="fi-input-wrp">
                        <input type="date" wire:model.live="startDate" id="startDate"
                            class="fi-input block w-full rounded-lg border-none bg-white shadow-sm ring-1 ring-gray-950/10 transition duration-75 focus:ring-2 focus:ring-primary-600 disabled:opacity-70 dark:bg-white/5 dark:ring-white/20 dark:focus:ring-primary-500 sm:text-sm sm:leading-6" />
                    </div>
                </div>
            </div>

            <div class="fi-fo-field-wrp">
                <div class="grid gap-y-2">
                    <label for="endDate"
                        class="fi-fo-field-wrp-label inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Fecha Fin
                    </label>
                    <div class="fi-input-wrp">
                        <input type="date" wire:model.live="endDate" id="endDate"
                            max="{{ now()->format('Y-m-d') }}"
                            class="fi-input block w-full rounded-lg border-none bg-white shadow-sm ring-1 ring-gray-950/10 transition duration-75 focus:ring-2 focus:ring-primary-600 disabled:opacity-70 dark:bg-white/5 dark:ring-white/20 dark:focus:ring-primary-500 sm:text-sm sm:leading-6" />
                    </div>
                </div>
            </div>
        </div>

        <div class="fi-fo-field-wrp-hint text-sm text-gray-500 dark:text-gray-400 mt-4">
            💡 Selecciona las opciones y haz clic en "Generar Reporte" arriba a la derecha
        </div>
    </x-filament::section>

    {{-- Sección de Resultados --}}
    @if (!empty($data))
        <x-filament::section class="mt-6">
            <x-slot name="heading">
                Resultados del Reporte
            </x-slot>

            <x-slot name="headerEnd">
                <x-filament::badge color="success">
                    {{ count($data) }} registros
                </x-filament::badge>
            </x-slot>

            <div
                class="fi-ta-content relative divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10">
                <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                    <thead class="divide-y divide-gray-200 dark:divide-white/5">
                        <tr class="bg-gray-50 dark:bg-white/5">
                            @foreach (array_keys($data[0] ?? []) as $header)
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                        {{ $header }}
                                    </span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                        @foreach ($data as $index => $row)
                            <tr class="fi-ta-row transition duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                                @foreach ($row as $value)
                                    <td
                                        class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                        <div class="fi-ta-col-wrp px-3 py-4">
                                            <div class="fi-ta-text text-sm leading-6 text-gray-950 dark:text-white">
                                                {{ $value }}
                                            </div>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @else
        <x-filament::section class="mt-6">
            <div class="text-center py-12">
                <div class="fi-ta-empty-state-icon-ctn flex justify-center">
                    <div
                        class="fi-ta-empty-state-icon flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-500/20">
                        <x-filament::icon icon="heroicon-o-document-chart-bar"
                            class="fi-ta-empty-state-icon-svg h-6 w-6 text-gray-500 dark:text-gray-400" />
                    </div>
                </div>
                <h4 class="fi-ta-empty-state-heading mt-4 text-base font-semibold text-gray-950 dark:text-white">
                    Sin datos para mostrar
                </h4>
                <p class="fi-ta-empty-state-description text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Configura los filtros arriba y haz clic en el botón "Generar Reporte" para ver los resultados
                </p>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
