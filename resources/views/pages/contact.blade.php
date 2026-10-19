@extends('layouts.app')

@section('title', 'Nous contacter — ' . $site['site_name'])

@section('content')
<section class="container py-5" style="max-width:1280px">
    <p class="section-label">Contact</p>
    <h1 class="page-title mt-3">NOUS <span class="text-volt">CONTACTER</span></h1>
    <div class="row g-5 mt-4">
        <div class="col-lg-6">
            <div class="mb-5 ps-3" style="border-left:2px solid var(--volt)">
                <p class="small text-uppercase text-muted-custom fw-bold mb-1" style="font-size:10px;letter-spacing:0.15em">Showroom & vente</p>
                <p class="font-display fs-3 mb-0">{{ $site['contact_address'] }}</p>
            </div>
            <div class="mb-5 ps-3" style="border-left:2px solid var(--volt)">
                <p class="small text-uppercase text-muted-custom fw-bold mb-1" style="font-size:10px;letter-spacing:0.15em">Téléphone</p>
                <p class="font-display fs-3 mb-0">{{ $site['contact_phone'] }}</p>
            </div>
            <div class="mb-5 ps-3" style="border-left:2px solid var(--volt)">
                <p class="small text-uppercase text-muted-custom fw-bold mb-1" style="font-size:10px;letter-spacing:0.15em">E-mail</p>
                <p class="font-display fs-3 mb-0">{{ $site['contact_email'] }}</p>
            </div>
            <div class="ps-3" style="border-left:2px solid var(--volt)">
                <p class="small text-uppercase text-muted-custom fw-bold mb-1" style="font-size:10px;letter-spacing:0.15em">Horaires</p>
                <p class="text-muted-custom mb-0">{{ $site['contact_hours'] }}</p>
            </div>
        </div>
        <div class="col-lg-6">
            <form method="POST" action="{{ route('contact.store') }}" class="border border-custom bg-panel p-4 p-md-5">
                @csrf
                <h2 class="font-display fs-3 mb-4">Écrivez-nous</h2>
                <input name="nom" value="{{ old('nom') }}" required class="form-control form-control-dark mb-3" placeholder="Nom et prénom">
                @error('nom')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                <input name="email" type="email" value="{{ old('email') }}" required class="form-control form-control-dark mb-3" placeholder="Adresse e-mail">
                @error('email')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                <input name="telephone" value="{{ old('telephone') }}" required class="form-control form-control-dark mb-3" placeholder="Téléphone">
@error('telephone')<div class="text-danger small mb-2">{{ $message }}</div>@enderror                <textarea name="message" required rows="5" class="form-control form-control-dark mb-3" placeholder="Votre demande">{{ old('message') }}</textarea>
                @error('message')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                <button type="submit" class="btn-volt w-100 skew-reverse border-0">ENVOYER LE MESSAGE</button>
            </form>
        </div>
    </div>
</section>
@endsection
