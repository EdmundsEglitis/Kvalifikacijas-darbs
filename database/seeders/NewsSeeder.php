<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\League;
use App\Models\News;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        // 1) HERO (homepage banner)
        $heroPath = $this->downloadImageToPublic(
            url: 'https://picsum.photos/seed/lbl-hero/1600/900',
            disk: 'public',
            dir:  'news/hero',
            ext:  'jpg'
        );

        News::create([
            'position'    => 'hero',
            'hero_image'  => $heroPath,   // make sure news table has nullable hero_image column
            'title'       => null,
            'content'     => null,
            'league_id'   => null,
        ]);

        // 2) Per-league cards with TRIX image in content
        $leagues = League::orderBy('id')->get();

        foreach ($leagues as $league) {
            // Secondary Left
            $content = $this->trixContentWithImage(
                seed: "league-{$league->id}-1",
                heading: $this->titleFor($league->name),
                paragraph: $this->paragraph()
            );

            News::create([
                'position'  => 'secondary-1',
                'title'     => $this->titleFor($league->name),
                'content'   => $content,
                'league_id' => $league->id,
                'hero_image'=> null,
            ]);

            // Secondary Right
            $content2 = $this->trixContentWithImage(
                seed: "league-{$league->id}-2",
                heading: $this->titleFor($league->name),
                paragraph: $this->paragraph()
            );

            News::create([
                'position'  => 'secondary-2',
                'title'     => $this->titleFor($league->name),
                'content'   => $content2,
                'league_id' => $league->id,
                'hero_image'=> null,
            ]);

            // Small slots (slot-1..slot-3) — text-only here
            foreach (['slot-1','slot-2','slot-3'] as $slot) {
                News::create([
                    'position'  => $slot,
                    'title'     => $this->titleFor($league->name),
                    'content'   => '<p>'.$this->paragraph().'</p>',
                    'league_id' => $league->id,
                    'hero_image'=> null,
                ]);
            }
        }

        // Optional: a few global (no-league) small cards
        foreach (['secondary-1','secondary-2','slot-1','slot-2','slot-3'] as $pos) {
            News::create([
                'position'  => $pos,
                'title'     => $this->titleFor('LBL'),
                'content'   => '<p>'.$this->paragraph().'</p>',
                'league_id' => null,
                'hero_image'=> null,
            ]);
        }
    }

    /** ---------- Helpers below ---------- */

    /** Build Trix-compatible HTML with <figure data-trix-attachment="..."> and a real image on disk */
    private function trixContentWithImage(string $seed, string $heading, string $paragraph): string
    {
        // 1) Put an actual file on disk so the URL resolves
        $imgPath = $this->downloadImageToPublic(
            url: 'https://picsum.photos/seed/'.urlencode($seed).'/1080/720',
            disk: 'public',
            dir:  'news/body',
            ext:  'jpg'
        );

        // 2) Gather image meta (mimetype, size, w/h)
        $absPath  = Storage::disk('public')->path($imgPath);
        $url      = Storage::disk('public')->url($imgPath); // uses APP_URL
        $mime     = mime_content_type($absPath) ?: 'image/jpeg';
        $size     = @filesize($absPath) ?: 0;
        [$w,$h]   = @getimagesize($absPath) ?: [1080,720];
        $fileName = basename($imgPath);

        // 3) JSON for data-trix-attachment (must be HTML-escaped)
        $attachment = [
            "contentType" => $mime,
            "filename"    => $fileName,
            "filesize"    => $size,
            "height"      => $h,
            "href"        => $url,
            "url"         => $url,
            "width"       => $w,
        ];
        $attachmentEsc = htmlspecialchars(json_encode($attachment), ENT_QUOTES, 'UTF-8');

        // 4) Optional Trix attributes (gallery presentation)
        $attrs = ["presentation" => "gallery"];
        $attrsEsc = htmlspecialchars(json_encode($attrs), ENT_QUOTES, 'UTF-8');

        // 5) Exact Trix markup (similar to what your RichEditor saves)
        $figure = <<<HTML
<figure data-trix-attachment="{$attachmentEsc}" data-trix-content-type="{$mime}" data-trix-attributes="{$attrsEsc}" class="attachment attachment--preview attachment--jpg">
  <a href="{$url}">
    <img src="{$url}" width="{$w}" height="{$h}">
    <figcaption class="attachment__caption">
      <span class="attachment__name">{$fileName}</span>
      <span class="attachment__size">{$this->kb($size)} KB</span>
    </figcaption>
  </a>
</figure>
HTML;

        // 6) Wrap in paragraphs/headings
        $html  = '<h2>'.e($heading).'</h2>';
        $html .= '<p>'.$figure.'</p>';
        $html .= '<p>'.e($paragraph).'</p>';

        return $html;
    }

    private function titleFor(string $leagueName): string
    {
        $heads = [
            'Uzvara pēdējā sekundē',
            'Aizraujoša cīņa Kurzemē',
            'Rīdzinieki pārsteidz',
            'Jaunais talants iemirdzas',
            'Treneris: “Aizsardzība izšķīra visu”',
            'Soli līdz playoffiem',
        ];
        return Arr::random($heads) . ' — ' . $leagueName;
    }

    private function paragraph(): string
    {
        $lines = [
            'Komandas apmainījās vadību visas spēles garumā, un izšķirošais metiens tika realizēts pēdējās sekundēs.',
            'Spēles liktenis izšķīrās ceturtajā ceturtdaļā, kad aizsardzība nostrādāja nevainojami.',
            'Līdzjutēji radīja elektrizējošu atmosfēru, kas palīdzēja mājiniekiem atspēlēties.',
            'Jaunais saspēles vadītājs iemirdzējās ar 8 rezultatīvām piespēlēm un precīzu tālmetienu.',
        ];
        return Arr::random($lines);
    }

    private function kb(int $bytes): string
    {
        return number_format(max(1, $bytes) / 1024, 2); // avoid 0.00
    }

    /** Download image to the public disk; return relative path (e.g. news/body/abc.jpg) */
    private function downloadImageToPublic(string $url, string $disk, string $dir, string $ext = 'jpg'): string
    {
        $name = Str::random(16) . '.' . $ext;
        $path = trim($dir,'/').'/'.$name;

        try {
            $res = Http::timeout(15)->get($url);
            if ($res->successful() && $res->body()) {
                Storage::disk($disk)->put($path, $res->body());
                return $path;
            }
        } catch (\Throwable $e) {
            // ignore; fall back below
        }

        // fallback 1x1 png
        $fallback = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8Xw8AAkMBQZ0bD5EAAAAASUVORK5CYII=');
        Storage::disk($disk)->put($path, $fallback);
        return $path;
    }
}
