<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Contact;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactPolicy
{
    use HandlesAuthorization;

    public function update(Admin $admin, Contact $contact)
    {
        return $admin->id === $contact->admin_id;
    }

    public function delete(Admin $admin, Contact $contact)
    {
        return $admin->id === $contact->admin_id;
    }
    public function view(Admin $admin, Contact $contact)
    {
        return $admin->id === $contact->admin_id;
    }
}
