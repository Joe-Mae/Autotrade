<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\MarqueeItem;
use App\Support\MailSamples;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SettingsController extends Controller
{
    private const TEXT_KEYS = [
        'site_name', 'site_tagline',
        'hero_badge', 'hero_title_1', 'hero_title_2', 'hero_text',
        'hero_cta_primary', 'hero_cta_secondary',
        'contact_address', 'contact_phone', 'contact_email', 'contact_hours',
        'notification_email',
        'mail_mailer', 'mail_host', 'mail_port', 'mail_username', 'mail_encryption',
        'mail_from_address', 'mail_from_name',
        'hero_slide_1_caption', 'hero_slide_1_url',
        'hero_slide_2_caption', 'hero_slide_2_url',
        'hero_slide_3_caption', 'hero_slide_3_url',
        'hero_slide_4_caption', 'hero_slide_4_url',
    ];

    private const IMAGE_KEYS = [
        'logo',
        'hero_slide_1_image',
        'hero_slide_2_image',
        'hero_slide_3_image',
        'hero_slide_4_image',
    ];

    public function edit()
    {
        return view('admin.settings', ['site' => Setting::map(), 'marqueeItems' => MarqueeItem::orderBy('sort_order')->orderBy('id')->get()]);
    }

    public function update(Request $request)
    {
        $rules = [
            'site_name' => 'required|string|max:60',
            'site_tagline' => 'required|string|max:120',
            'hero_badge' => 'nullable|string|max:120',
            'hero_title_1' => 'required|string|max:40',
            'hero_title_2' => 'nullable|string|max:40',
            'hero_text' => 'nullable|string|max:600',
            'hero_cta_primary' => 'nullable|string|max:60',
            'hero_cta_secondary' => 'nullable|string|max:60',
            'contact_address' => 'nullable|string|max:160',
            'contact_phone' => 'nullable|string|max:60',
            'contact_email' => 'nullable|email|max:120',
            'contact_hours' => 'nullable|string|max:200',
            'notification_email' => 'nullable|email|max:120',
            'mail_mailer' => 'nullable|in:log,smtp',
            'mail_host' => 'nullable|string|max:160',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:160',
            'mail_password' => 'nullable|string|max:200',
            'mail_encryption' => 'nullable|in:tls,ssl,none',
            'mail_from_address' => 'nullable|email|max:120',
            'mail_from_name' => 'nullable|string|max:80',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ];

        foreach (range(1, 4) as $i) {
            $rules["hero_slide_{$i}_caption"] = 'nullable|string|max:60';
            $rules["hero_slide_{$i}_url"] = 'nullable|string|max:160';
            $rules["hero_slide_{$i}_image"] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:6144';
        }

        $data = $request->validate($rules);

        foreach (self::TEXT_KEYS as $key) {
            Setting::put($key, $data[$key] ?? null);
        }

        // Le mot de passe SMTP n'est remplacé que si une nouvelle valeur est saisie.
        if ($request->boolean('clear_mail_password')) {
            Setting::put('mail_password', '');
        } elseif ($request->filled('mail_password')) {
            Setting::put('mail_password', (string) $request->input('mail_password'));
        }

        foreach (self::IMAGE_KEYS as $key) {
            if ($request->boolean("remove_{$key}")) {
                Setting::put($key, '');

                continue;
            }

            if ($request->hasFile($key)) {
                Setting::put($key, $this->storeImage($request->file($key)));
            }
        }

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Paramètres du site enregistrés.');
    }

    /**
     * Envoie les quatre e-mails du site à une adresse de test.
     */
    public function sendTestMail(Request $request)
    {
        $data = $request->validate([
            'test_email' => 'required|email|max:120',
        ]);

        $mails = MailSamples::mails($data['test_email']);
        $sent = 0;
        $failure = null;

        foreach ($mails as $mailable) {
            try {
                Mail::to($data['test_email'])->send($mailable);
                $sent++;
            } catch (Throwable $e) {
                $failure ??= $e->getMessage();
            }
        }

        if ($failure !== null) {
            return redirect()
                ->route('admin.settings')
                ->with('error', $sent.'/'.count($mails).' e-mail(s) envoyé(s). Erreur : '.$failure);
        }

        return redirect()
            ->route('admin.settings')
            ->with('success', $sent.' e-mail(s) de test envoyé(s) à '.$data['test_email'].'.');
    }

    /**
     * Enregistre une image téléversée dans public/uploads/site et renvoie son chemin public.
     */
    private function storeImage($file): string
    {
        $directory = public_path('uploads/site');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $name = $file->hashName();
        $file->move($directory, $name);

        return 'uploads/site/'.$name;
    }
}
