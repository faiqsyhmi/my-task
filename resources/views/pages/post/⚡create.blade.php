<?php
 
use Livewire\Component;
 
new class extends Component {
    public string $nama = '';
 
    public string $umur = '';

    public string $email = '';
    
    public string $description = '';

 
    public function save()
    {
        $this->validate([
            'nama' => 'required|max:255',
            'umur' => 'required|max:2',
            'email' => 'required|email',
            'description' => 'required|max:255',
        ]);
 
        dd($this->nama, $this->umur, $this->email, $this->description);
    }
};
?>
 
<form wire:submit="save">
    <label>
        Nama
        <input type="text" wire:model="nama">
        @error('nama') <span style="color: red;">{{ $message }}</span> @enderror
    </label>
 
    <label>
        Umur
        <input type="text" wire:model="umur">
        @error('umur') <span style="color: red;">{{ $message }}</span> @enderror
    </label>

    <label>
        Email
        <input type="email" wire:model="email">
        @error('email') <span style="color: red;">{{ $message }}</span> @enderror
    </label>

    <label>
        Description
        <textarea wire:model="description"></textarea>
        @error('description') <span style="color: red;">{{ $message }}</span> @enderror
    </label>
 
    <button type="submit">Save Post</button>
</form>