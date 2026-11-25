<?php
namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

/** Construit le contenu d'un message : texte, fichier, photo ou position exacte. */
class ChatPayload
{
    public static function rules(): array
    {
        return [
            'body' => 'nullable|string|max:2000',
            'file' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip',
            'lat'  => 'nullable|numeric|between:-90,90',
            'lng'  => 'nullable|numeric|between:-180,180',
        ];
    }

    /** Retourne les champs à enregistrer, ou null si le message est vide. */
    public static function build(Request $request): ?array
    {
        $body = trim((string) $request->input('body', ''));

        $payload = [
            'body'            => $body,
            'type'            => 'text',
            'attachment_path' => null,
            'attachment_name' => null,
            'attachment_mime' => null,
            'attachment_size' => null,
            'lat'             => null,
            'lng'             => null,
            'forwarded_from'  => null,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $mime = (string) $file->getMimeType();
            $payload['type']            = str_starts_with($mime, 'image/') ? 'image' : 'file';
            $payload['attachment_path'] = $file->store('chat', 'local'); // disque privé : servi par un contrôleur
            $payload['attachment_name'] = Str::limit(basename($file->getClientOriginalName()), 150, '');
            $payload['attachment_mime'] = $mime;
            $payload['attachment_size'] = $file->getSize();
        } elseif ($request->filled('lat') && $request->filled('lng')) {
            $payload['type'] = 'location';
            $payload['lat']  = $request->input('lat');
            $payload['lng']  = $request->input('lng');
        }

        if ($payload['type'] === 'text' && $body === '') {
            return null;
        }

        return $payload;
    }

    /** Copie d'un message existant pour le transférer. */
    public static function forwardOf($source, string $originalSender): array
    {
        return [
            'body'            => (string) $source->body,
            'type'            => $source->type ?: 'text',
            'attachment_path' => $source->attachment_path,
            'attachment_name' => $source->attachment_name,
            'attachment_mime' => $source->attachment_mime,
            'attachment_size' => $source->attachment_size,
            'lat'             => $source->lat,
            'lng'             => $source->lng,
            'forwarded_from'  => $source->forwarded_from ?: $originalSender,
        ];
    }
}
