<?php

namespace App\View\Components\Atoms;

use Illuminate\View;
use Illuminate\View\ComponentAttributeBag;

abstract class Component extends View\Component
{
    protected const string DEFAULT = 'default';

    protected array $smartAttributes = [];

    public function modifierClasses(ComponentAttributeBag $attributes, array $modifiers): string
    {
        $modifier = $this->findModifier($attributes, $modifiers);

        return $modifiers[$modifier];
    }

    protected function classes(array $classList): string
    {
        $classes = [];

        foreach ($classList as $class => $constraint) {
            if (is_numeric($class)) {
                $classes[] = $constraint;
            } elseif ($constraint) {
                $classes[] = $class;
            }
        }

        return implode(' ', $classes);
    }

    private function findModifier(ComponentAttributeBag $attributes, array $modifiers): string
    {
        $keys = collect($modifiers)->keys()->except(self::DEFAULT)->toArray();
        $modifiers = $attributes->only($keys)->getAttributes();
        $modifier = collect($modifiers)->filter()->keys()->first();

        if ($modifier && ! in_array($modifier, $this->smartAttributes)) {
            $this->smartAttributes[] = $modifier;
        }

        return $modifier ?? self::DEFAULT;
    }
}
