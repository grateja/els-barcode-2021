<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Hash;

class VerifyPassword implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($current_password)
    {
        $this->current_password = $current_password;
    }
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Hash::check($value, $this->current_password);
    }
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Wrong password.';
    }
}
