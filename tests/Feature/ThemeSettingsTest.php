<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Setting;
use App\Models\User;
use App\Support\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_settings_page_exposes_the_three_colour_fields(): void
    {
        $this->actingAs(User::firstOrFail())
            ->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('Palette du site')
            ->assertSee('name="color_primary"', false)
            ->assertSee('name="color_secondary"', false)
            ->assertSee('name="color_dark"', false)
            ->assertSee("Palette d'origine", false)
            ->assertSee('Jaune &amp; violet', false);
    }

    public function test_old_palette_is_used_when_nothing_is_saved(): void
    {
        $this->assertSame('#cfff04', Setting::defaults()['color_primary']);
        $this->assertSame('#536900', Setting::defaults()['color_secondary']);
        $this->assertSame('#0a0a0a', Setting::defaults()['color_dark']);

        $this->get('/')
            ->assertOk()
            ->assertSee('--volt: #cfff04', false)
            ->assertSee('--violet: #536900', false)
            ->assertSee('--page-bg: #0a0a0a', false)
            ->assertSee('--volt-text: #536900', false);
    }

    public function test_colours_are_saved_in_lower_case(): void
    {
        $admin = User::firstOrFail();

        $this->actingAs($admin)->get(route('admin.settings'));

        $this->actingAs($admin)
            ->post(route('admin.settings.update'), [
                '_token' => session()->token(),
                'site_name' => 'AUTOTRADE',
                'site_tagline' => 'Vente de véhicules',
                'hero_title_1' => 'ACHETEZ.',
                'color_primary' => '#FFD600',
                'color_secondary' => '#8A2BE2',
                'color_dark' => '#000000',
            ])
            ->assertRedirect(route('admin.settings'));

        $this->assertSame('#ffd600', Setting::get('color_primary'));
        $this->assertSame('#8a2be2', Setting::get('color_secondary'));
        $this->assertSame('#000000', Setting::get('color_dark'));

        // Rendu direct du partial : les réglages alimentent bien les variables CSS.
        $html = view('partials.theme', ['site' => Setting::map()])->render();

        $this->assertStringContainsString('--volt: #ffd600', $html);
        $this->assertStringContainsString('--violet: #8a2be2', $html);
        $this->assertStringContainsString('--page-bg: #000000', $html);
    }

    public function test_an_invalid_colour_is_rejected(): void
    {
        $admin = User::firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.settings.update'), [
                'site_name' => 'AUTOTRADE',
                'site_tagline' => 'Vente de véhicules',
                'hero_title_1' => 'ACHETEZ.',
                'color_primary' => 'rouge',
            ])
            ->assertSessionHasErrors('color_primary');

        $this->assertSame('#cfff04', Setting::get('color_primary'));
    }

    public function test_theme_derives_a_readable_light_accent_and_button_text(): void
    {
        $variables = Theme::variables([
            'color_primary' => '#cfff04',
            'color_secondary' => '#536900',
            'color_dark' => '#0a0a0a',
        ]);

        // Le citron d'origine redonne exactement l'olive du mode clair.
        $this->assertSame('#536900', $variables['light']['--volt-text']);
        $this->assertSame('#000000', $variables['dark']['--ink']);
        $this->assertSame('#000000', Theme::contrastText('#ffd600'));
        $this->assertSame('#ffffff', Theme::contrastText('#000033'));
        $this->assertSame('#cfff04', Theme::accent('valeur-invalide'));
    }

    public function test_emails_use_the_configured_primary_colour(): void
    {
        Setting::put('color_primary', '#ffd600');

        $html = view('emails.contact-message-confirmation', [
            'contact' => new ContactMessage([
                'nom' => 'Client',
                'email' => 'client@example.test',
                'message' => 'Bonjour',
            ]),
            'site' => Setting::map(),
        ])->render();

        $this->assertStringContainsString('border-bottom:2px solid #ffd600', $html);
        $this->assertStringContainsString('background:#ffd600', $html);
        $this->assertStringContainsString('color:#000000', $html);
    }
}
