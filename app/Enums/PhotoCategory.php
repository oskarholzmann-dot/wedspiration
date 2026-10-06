<?php

namespace App\Enums;

enum PhotoCategory: string
{
    case Ceremony = 'ceremony';
    case Reception = 'reception';
    case Dress = 'dress';
    case Flowers = 'flowers';
    case Venue = 'venue';
    case Details = 'details';

    /**
     * Get the human readable label for the category.
     */
    public function label(): string
    {
        return ucfirst($this->value);
    }
}
