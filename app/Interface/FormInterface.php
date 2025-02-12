<?php

namespace App\Interface;

interface FormInterface
{
       /**
     * Cancels the form action and performs a redirection.
     */
    public function cancel(): void;

    /**
     * Stores the form data and optionally resets the form.
     *
     * @param  bool  $reset  Determines if the form should be reset after storing.
     */
    public function store(bool $reset = false): void;

    /**
     * Updates the form data.
     */
    public function update(): void;

    /**
     * Sets the route to redirect to after performing an action.
     *
     * @param  string|null  $route  The route to set. If null, the current route is used.
     * @return string The route to redirect to.
     */
    public function setRedirectAfterActionRoute(?string $route = null): string;

    /**
     * Sets the route to redirect to after creating a new resource.
     *
     * @param  string|null  $route  The route to set. If null, the current route is used.
     * @return string The route to redirect to.
     */
    public function setRedirectToCreateRoute(?string $route = null): string;
}
