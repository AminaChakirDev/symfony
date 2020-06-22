<?php


namespace App\Service;


class Slugify
{
    public function generate(string $input) :string {

        $slug = trim($input);

        $utf8 = array(
            '/[áàâãªä]/u' => 'a',
            '/[íìîï]/u' => 'i',
            '/[éèêë]/u' => 'e',
            '/[óòôõºö]/u' => 'o',
            '/[úùûü]/u' => 'u',
            '/ç/' => 'c',
            '/ñ/' => 'n',
        );
        $slug = preg_replace(array_keys($utf8), array_values($utf8), $slug);
        $slug = str_replace(' ', '-', $slug);
        $slug = preg_replace('#[^0-9a-zA-Z-]#', '', $slug);
        $slug = preg_replace('#-+#', '-', $slug);
        $slug = strtolower($slug);

        return $slug;
    }
}