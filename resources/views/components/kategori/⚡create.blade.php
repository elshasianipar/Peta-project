<?php

use App\Models\kategori;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component
{
    public ?kategori $category = null;

    public function mount(): void
    {
        $this->category = null;
    }
};
?>

<x-slot:header>
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Tambah kategori</h2>
</x-slot:header>

<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <livewire:kategori.kategori />
        </div>
    </div>
</div>
