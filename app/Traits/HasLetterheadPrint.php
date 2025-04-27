<?php

namespace App\Traits;

trait HasLetterheadPrint
{
    public function getLetterhead(): array
    {
        $title = config('letterhead.title');
        $name = config('letterhead.name');
        $address = $this->getLetterheadAddressLines();

        return [
            'title' => $title,
            'name' => $name,
            'address' => $address,
        ];
    }

    public function getLetterheadAddressLines(): array
    {
        $addressString = config('letterhead.address');
        $addressParts = preg_split('/\s*~nl\s*/', $addressString, -1, PREG_SPLIT_NO_EMPTY);
        $addressLines = [];
        foreach ($addressParts as $index => $part) {
            $addressLines['line' . ($index + 1)] = trim($part);
        }

        return $addressLines;
    }
}
