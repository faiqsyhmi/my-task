<?php

namespace Modules\Tasks\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Modules\Tasks\Models\TaskAttachment;
use ZipArchive;

class SecureTaskAttachment implements ValidationRule
{
    private const MIME_TYPES = [
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'webp' => ['image/webp'],
        'pdf' => ['application/pdf'],
        'xlsx' => [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
        ],
        'txt' => ['text/plain'],
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            $fail('The :attribute must be a valid uploaded file.');

            return;
        }

        $extension = strtolower($value->getClientOriginalExtension());
        $mimeType = strtolower($value->getMimeType() ?: '');

        if (! in_array($extension, TaskAttachment::ALLOWED_EXTENSIONS, true)
            || ! in_array($mimeType, self::MIME_TYPES[$extension] ?? [], true)) {
            $fail('The :attribute type is not allowed or does not match its extension.');

            return;
        }

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)
            && @getimagesize($value->getRealPath()) === false) {
            $fail('The :attribute must contain a valid raster image.');

            return;
        }

        if ($extension === 'xlsx' && ! $this->isSafeSpreadsheetPackage($value->getRealPath())) {
            $fail('The :attribute must contain a valid macro-free XLSX workbook.');
        }
    }

    private function isSafeSpreadsheetPackage(string $path): bool
    {
        $archive = new ZipArchive;

        if ($archive->open($path, ZipArchive::RDONLY) !== true) {
            return false;
        }

        try {
            if ($archive->numFiles > 10_000
                || $archive->locateName('[Content_Types].xml') === false
                || $archive->locateName('xl/workbook.xml') === false
                || $archive->locateName('xl/vbaProject.bin', ZipArchive::FL_NOCASE) !== false) {
                return false;
            }

            for ($index = 0; $index < $archive->numFiles; $index++) {
                $entry = $archive->getNameIndex($index);

                if ($entry === false || str_contains(str_replace('\\', '/', $entry), '../')) {
                    return false;
                }
            }

            return true;
        } finally {
            $archive->close();
        }
    }
}
