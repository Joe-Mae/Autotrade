<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Valeurs par défaut du site, utilisées tant qu'aucune valeur n'est enregistrée.
     */
    public static function defaults(): array
    {
        return [
            'site_name' => 'AUTOTRADE',
            'site_tagline' => "Vente de voitures d'exception",
            'logo' => '',

            'hero_badge' => "Vente de voitures d'exception depuis 2009",
            'hero_title_1' => 'ACHETEZ.',
            'hero_title_2' => 'PROFITEZ.',
            'hero_text' => "Devenez propriétaire d'une pièce d'exception. Supercars, berlines de luxe, SUV et électriques : des véhicules inspectés, garantis 12 mois et prêts à prendre la route avec vous.",
            'hero_cta_primary' => 'VOIR LES VÉHICULES À VENDRE',
            'hero_cta_secondary' => 'PRENDRE RENDEZ-VOUS',

            'hero_slide_1_image' => 'assets/car-zenith.jpg',
            'hero_slide_1_caption' => 'ZENITH PURE X',
            'hero_slide_1_url' => '/vehicule/zenith-pure-x',
            'hero_slide_2_image' => 'assets/car-electron.jpg',
            'hero_slide_2_caption' => 'ELECTRON Z',
            'hero_slide_2_url' => '/vehicule/electron-z',
            'hero_slide_3_image' => 'assets/car-storm.jpg',
            'hero_slide_3_caption' => 'STORM RIDER GT',
            'hero_slide_3_url' => '/vehicule/storm-rider-gt',
            'hero_slide_4_image' => 'assets/car-apex.jpg',
            'hero_slide_4_caption' => 'APEX PROTO-1',
            'hero_slide_4_url' => '/vehicule/apex-proto-1',

            'contact_address' => '18 rue des Ateliers, 75011 Paris',
            'contact_phone' => '01 84 00 12 90',
            'contact_email' => 'contact@autotrade.fr',
            'contact_hours' => 'Lundi – Vendredi : 9h – 19h · Samedi : 10h – 17h',

            // E-mails et notifications
            'notification_email' => '',
            'mail_mailer' => 'log',
            'mail_host' => '',
            'mail_port' => '',
            'mail_username' => '',
            'mail_password' => '',
            'mail_encryption' => 'tls',
            'mail_from_address' => '',
            'mail_from_name' => '',
        ];
    }

    /**
     * Réglages complets : valeurs par défaut recouvertes par celles enregistrées en base.
     */
    public static function map(): array
    {
        return array_merge(static::defaults(), static::query()
            ->get()
            ->filter(static fn ($setting) => $setting->value !== null && trim((string) $setting->value) !== '')
            ->pluck('value', 'key')
            ->all());
    }

    public static function get(string $key, $default = null)
    {
        $map = static::map();

        return array_key_exists($key, $map) ? $map[$key] : $default;
    }

    public static function put(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Adresse qui reçoit les notifications internes.
     * Repli sur l'adresse de contact du site si aucune adresse dédiée n'est définie.
     */
    public static function notificationEmail(): string
    {
        $map = static::map();

        $email = trim((string) ($map['notification_email'] ?? ''));

        if ($email === '') {
            $email = trim((string) ($map['contact_email'] ?? ''));
        }

        return $email;
    }

    /**
     * Les diapositives du hero ayant une image renseignée.
     */
    public static function heroSlides(): array
    {
        $map = static::map();
        $slides = [];

        for ($i = 1; $i <= 4; $i++) {
            $image = trim((string) ($map["hero_slide_{$i}_image"] ?? ''));

            if ($image === '') {
                continue;
            }

            $slides[] = [
                'image' => $image,
                'caption' => $map["hero_slide_{$i}_caption"] ?? '',
                'url' => $map["hero_slide_{$i}_url"] ?: '/catalogue',
            ];
        }

        return $slides;
    }
}
