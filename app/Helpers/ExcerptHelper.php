<?php 
    // app/Helpers/ExcerptHelper.php

namespace App\Helpers;

class ExcerptHelper
{
    public static function generate(string $html, int $length = 200): string
    {
        // 1. Hapus block-level nodes yang tidak relevan untuk excerpt:
        //    - <figure> : wrapper image upload dari TipTap
        //    - <img>    : tag gambar langsung
        //    - <pre>    : code block
        //    - <code>   : inline code
        //    - <blockquote> : kutipan
        $tagsToRemove = ['figure', 'img', 'pre', 'code', 'blockquote', 'iframe', 'video', 'audio'];

        foreach ($tagsToRemove as $tag) {
            // Hapus tag beserta seluruh isinya (self-closing & pair)
            $html = preg_replace('/<' . $tag . '(\s[^>]*)?>.*?<\/' . $tag . '>/is', '', $html);
            $html = preg_replace('/<' . $tag . '(\s[^>]*)?\/>/is', '', $html);  // self-closing
            $html = preg_replace('/<' . $tag . '(\s[^>]*)?>/is', '', $html);    // tag tanpa penutup (img)
        }

        // 2. Hapus tag <a> tapi pertahankan teks anchor-nya
        //    (href tidak akan ikut karena sudah jadi atribut, bukan text node)
        $html = preg_replace('/<a[^>]*>(.*?)<\/a>/is', '$1', $html);

        // 3. Hapus semua sisa HTML tags
        $html = strip_tags($html);

        // 4. Hapus URL yang masih tersisa sebagai plain text
        $html = preg_replace('/https?:\/\/\S+/i', '', $html);

        // 5. Hapus pola nama file attachment (contoh: "photo.jpg 67.98 KB")
        $html = preg_replace('/\S+\.(jpe?g|png|gif|webp|svg|pdf|docx?|xlsx?|zip|rar)\s*[\d.,]*\s*(B|KB|MB|GB)?/i', '', $html);

        // 6. Hapus karakter non-printable dan bersihkan whitespace
        $html = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $html);
        $html = preg_replace('/\s+/', ' ', $html);
        $html = trim($html);

        // 7. Potong dengan word boundary supaya tidak putus di tengah kata
        if (mb_strlen($html) > $length) {
            $html = mb_substr($html, 0, $length);
            // Mundur ke spasi terakhir agar tidak putus di tengah kata
            $lastSpace = mb_strrpos($html, ' ');
            if ($lastSpace !== false) {
                $html = mb_substr($html, 0, $lastSpace);
            }
            $html .= '...';
        }

        return $html;
    }
}
?>