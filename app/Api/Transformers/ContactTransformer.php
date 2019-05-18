<?php

namespace App\Api\Transformers;

use App\Api\Models\ReserveContact;

class ContactTransformer extends BaseTransformer
{
    public function transform(ReserveContact $contact)
    {
        $row = [
            'id'        => (int) $contact->id,
            'name'      => $contact->name,
            'title'     => $contact->title,
            'phone'     => $contact->phone,
            'email'     => $contact->email,
            'emergency' => $contact->emergency,
        ];

        return $row;
    }
}
