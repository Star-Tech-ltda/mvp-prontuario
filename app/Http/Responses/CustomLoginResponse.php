<?php

namespace App\Http\Responses;
use App\Filament\Resources\PatientResource;
use App\Filament\Resources\UserResource;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class CustomLoginResponse implements Responsable
{
    //redirecionar adm para UserResource e usuario para PatientResource
    public function toResponse($request): RedirectResponse|Redirector
    {
        $adm = auth()->user()->isAdmin();

        return redirect()->intended( $adm ?   UserResource::getUrl() : PatientResource::getUrl());
    }
}
