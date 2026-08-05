<?php

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component
{
    public Category $category;

    public function mount(Category $category): void
    {
        $this->category = $category;
    }
};
?>

<x-slot:header>
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit kategori</h2>
</x-slot:header>

<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <livewire:kategori.kategori :category="$category" />
        </div>
    </div>
</div>