<?php

namespace App\Traits;

trait HasGoogle2FA
{
    public function hasGoogle2FAEnabled()
    {
        return $this->google2fa_enabled_at !== null;
    }

    public function enableGoogle2FA()
    {
        return $this->forceFill(['google2fa_enabled_at' => $this->freshTimestamp()])->save();
    }

    public function disableGoogle2FA()
    {
        return $this->forceFill(['google2fa_enabled_at' => null])->save();
    }
}
