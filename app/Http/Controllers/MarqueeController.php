<?php

namespace App\Http\Controllers;

use App\Models\MarqueeItem;
use Illuminate\Http\Request;

class MarqueeController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'marquee_nom' => 'required|string|max:80',
            'marquee_spec' => 'nullable|string|max:120',
            'marquee_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $item = new MarqueeItem([
            'nom' => $data['marquee_nom'],
            'spec' => $data['marquee_spec'] ?? null,
            'image' => $this->storeImage($request->file('marquee_image')),
            'sort_order' => (int) (MarqueeItem::max('sort_order') + 10),
        ]);
        $item->save();

        return redirect()->route('admin.settings')
            ->with('success', 'Image ajoutée au bandeau défilant.');
    }

    public function destroy(MarqueeItem $marquee)
    {
        $marquee->delete();

        return redirect()->route('admin.settings')
            ->with('success', 'Image retirée du bandeau défilant.');
    }

    private function storeImage($file): string
    {
        $directory = public_path('uploads/marquee');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $name = $file->hashName();
        $file->move($directory, $name);

        return 'uploads/marquee/'.$name;
    }
}