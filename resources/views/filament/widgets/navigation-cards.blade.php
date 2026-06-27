<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Overview
        </x-slot>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
            @foreach ($this->getModules() as $module)
                <a
                    href="{{ $module['url'] }}"
                    class="group fi-wi-stats-overview-stat relative rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
                    style="display: flex; align-items: flex-start; gap: 1rem; padding: 1.25rem; text-decoration: none; transition: box-shadow .15s;"
                    onmouseover="this.style.boxShadow='0 0 0 2px var(--primary-500, #16a34a)'"
                    onmouseout="this.style.boxShadow='none'"
                >
                    {{-- Icon bubble --}}
                    <div
                        class="fi-color-primary"
                        style="flex-shrink:0; display:flex; align-items:center; justify-content:center; width:2.75rem; height:2.75rem; border-radius:.5rem; background:rgb(var(--primary-50)); color:rgb(var(--primary-600));"
                    >
                        <x-filament::icon :icon="$module['icon']" style="width:1.5rem; height:1.5rem;" />
                    </div>

                    {{-- Text --}}
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:.5rem;">
                            <p class="fi-wi-stats-overview-stat-label" style="font-size:.875rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $module['title'] }}
                            </p>
                            <span
                                class="fi-badge fi-color-success fi-size-sm"
                                style="flex-shrink:0; display:inline-flex; align-items:center; border-radius:9999px; padding:.125rem .5rem; font-size:.75rem; font-weight:500; background:rgb(var(--primary-50)); color:rgb(var(--primary-700)); box-shadow: inset 0 0 0 1px rgb(var(--primary-600)/.2);"
                            >
                                {{ number_format($module['count']) }}
                            </span>
                        </div>
                        <p class="fi-wi-stats-overview-stat-description" style="margin-top:.125rem; font-size:.75rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $module['description'] }}
                        </p>
                    </div>

                    {{-- Chevron --}}
                    <x-filament::icon
                        icon="heroicon-m-chevron-right"
                        style="flex-shrink:0; align-self:center; width:1rem; height:1rem; color: rgb(156 163 175); transition: transform .15s;"
                    />
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>