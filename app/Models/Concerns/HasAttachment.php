<?php
namespace App\Models\Concerns;

/** Pièce jointe / localisation d'un message (direct ou de groupe). */
trait HasAttachment
{
    public function getFileSizeLabelAttribute(): string
    {
        $b = (int) $this->attachment_size;
        if ($b >= 1048576) {
            return round($b / 1048576, 1) . ' Mo';
        }
        return max(1, (int) round($b / 1024)) . ' Ko';
    }

    public function getHasLocationAttribute(): bool
    {
        return $this->lat !== null && $this->lng !== null;
    }

    public function getMapUrlAttribute(): ?string
    {
        return $this->has_location ? 'https://www.google.com/maps/search/?api=1&query=' . $this->lat . ',' . $this->lng : null;
    }

    public function getEmbedMapUrlAttribute(): ?string
    {
        if (! $this->has_location) {
            return null;
        }
        $a = (float) $this->lat;
        $o = (float) $this->lng;
        return 'https://www.openstreetmap.org/export/embed.html?bbox=' . ($o - 0.004) . ',' . ($a - 0.0025) . ',' . ($o + 0.004) . ',' . ($a + 0.0025) . '&layer=mapnik&marker=' . $a . ',' . $o;
    }

    /** Court résumé pour les notifications. */
    public function getPreviewTextAttribute(): string
    {
        $prefix = match ($this->type) {
            'image'    => '📷 Photo',
            'file'     => '📎 ' . $this->attachment_name,
            'location' => '📍 Position exacte',
            default    => '',
        };
        $text = trim($prefix . ($prefix && $this->body ? ' — ' : '') . $this->body);
        return mb_strimwidth($text, 0, 90, '…');
    }
}
