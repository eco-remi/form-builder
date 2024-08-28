<?php

namespace App\Entity;

trait HydrateStaticTrait
{
    public function hydrate(\stdClass|array $incomingData): self
    {
        foreach ($incomingData as $key => $value) {
            // prefer setter but useless in most cases
            $setterCustom = self::keyToSetterName($key);
            if (method_exists($this, $setterCustom)) {
                $this->$setterCustom($value);
            } else if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        return $this;
    }

    public static function keyToSetterName(string $key): string
    {
        return 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
    }
}
