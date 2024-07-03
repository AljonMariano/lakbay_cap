<?php

namespace App\Http\Livewire\Profile;

use Livewire\Component;

class UpdateProfileInformationForm extends Component
{
    public $name;

    public function mount()
    {
        $this->name = auth()->user()->name;
    }

    public function updateProfileInformation()
    {
        // Logic to update profile information
    }

    public function render()
    {
        return view('livewire.profile.update-profile-information-form');
    }
}
