<x-filament-panels::page>
  <h1 style="font-size: x-large; font-weight: bold">
    Hello, {{ auth()->user()->username }}. Your email address is {{ auth()->user()->email }}
  </h1>
  <div class="space-y-6">
    <div>
      <p style="font-size: larger; margin-bottom: 0.5rem">Latest 4 students you added:</p>
      @livewire(\App\Filament\Widgets\LatestStudentsOverview::class)
    </div>
    <div>
      <p style="font-size: larger; margin-bottom: 0.5rem">Students chart for the last 10 days:</p>
      @livewire(\App\Filament\Widgets\StudentsChart::class)
    </div>
  </div>
</x-filament-panels::page>