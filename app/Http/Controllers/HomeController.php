<?php

namespace App\Http\Controllers;

use App\Models\MarqueeItem;
use App\Models\Setting;
use App\Models\Vehicle;

class HomeController extends Controller
{
    public function index()
    {
        $selection = Vehicle::aVendre()->disponible()->take(3)->get();

        $slides = Setting::heroSlides();

        // Repli si aucune diapositive n'est configurée.
        if (empty($slides)) {
            $slides = Vehicle::aVendre()->disponible()->orderByDesc('annee')->take(4)->get()
                ->map(fn (Vehicle $v) => [
                    'image' => $v->image,
                    'caption' => $v->nom,
                    'url' => route('vehicule.show', $v),
                ])->all();
        }

        $marqueeItems = MarqueeItem::orderBy('sort_order')->orderBy('id')->get();

        return view('pages.home', compact('selection', 'slides', 'marqueeItems'));
    }
}
