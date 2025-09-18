<?php

class HtmlParser {
    
    /**
     * HTML koddan ikki belgi orasidagi ma'lumotni olish
     * 
     * @param string $html - HTML kod
     * @param string $start - Boshlanish belgisi
     * @param string $end - Tugash belgisi
     * @param int $index - Qaysi elementni olish (0 - birinchi, -1 - oxirgi)
     * @return string|array - Bitta yoki barcha natijalar
     */
    public static function getBetween($html, $start, $end, $index = 0) {
        $pattern = preg_quote($start, '/') . '(.*?)' . preg_quote($end, '/');
        preg_match_all('/' . $pattern . '/s', $html, $matches);
        
        if (empty($matches[1])) {
            return '';
        }
        
        if ($index === -1) {
            return end($matches[1]); // Oxirgi element
        } elseif ($index === 'all') {
            return $matches[1]; // Barcha elementlar
        } else {
            return isset($matches[1][$index]) ? $matches[1][$index] : '';
        }
    }
    
    /**
     * Atribut qiymatini olish
     * 
     * @param string $html - HTML kod
     * @param string $attribute - Atribut nomi
     * @param int $start_index - Qaysi elementdan boshlash
     * @param int $count - Nechta element olish
     * @return string|array - Bitta yoki bir nechta qiymat
     */
    public static function getAttribute($html, $attribute, $start_index = 0, $count = 1) {
        $pattern = '/' . preg_quote($attribute, '/') . '=["\']([^"\']*)["\']|' . 
                   preg_quote($attribute, '/') . '=([^\s>]+)/i';
        preg_match_all($pattern, $html, $matches);
        
        $results = array();
        foreach ($matches[1] as $key => $value) {
            if (!empty($value)) {
                $results[] = $value;
            } elseif (!empty($matches[2][$key])) {
                $results[] = $matches[2][$key];
            }
        }
        
        if (empty($results)) {
            return $count === 1 ? '' : array();
        }
        
        // Belgilangan oraliqdan olish
        $sliced = array_slice($results, $start_index, $count);
        
        return $count === 1 ? (isset($sliced[0]) ? $sliced[0] : '') : $sliced;
    }
    
    /**
     * CSS selector orqali elementlarni olish
     * 
     * @param string $html - HTML kod
     * @param string $selector - CSS selector
     * @param int $index - Qaysi elementni olish
     * @return string|array - Element yoki elementlar
     */
    public static function getBySelector($html, $selector, $index = 'all') {
        // DOMDocument yaratish
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();
        
        $xpath = new DOMXPath($dom);
        
        // CSS selectorni XPath ga o'tkazish
        $xpathQuery = self::cssToXPath($selector);
        $nodes = $xpath->query($xpathQuery);
        
        $results = array();
        foreach ($nodes as $node) {
            $results[] = $dom->saveHTML($node);
        }
        
        if ($index === 'all') {
            return $results;
        } elseif ($index === -1) {
            return end($results);
        } else {
            return isset($results[$index]) ? $results[$index] : '';
        }
    }
    
    /**
     * Tag ichidagi ma'lumotni olish
     * 
     * @param string $html - HTML kod
     * @param string $tag - Tag nomi
     * @param array $attributes - Atributlar array ko'rinishida
     * @param int $index - Qaysi elementni olish
     * @return string|array - Tag ichidagi ma'lumot
     */
    public static function getTagContent($html, $tag, $attributes = array(), $index = 0) {
        $attrPattern = '';
        if (!empty($attributes)) {
            foreach ($attributes as $attr => $value) {
                $attrPattern .= '(?=.*' . preg_quote($attr, '/') . '=["\']' . 
                               preg_quote($value, '/') . '["\'])';
            }
        }
        
        $pattern = '/<' . preg_quote($tag, '/') . $attrPattern . '[^>]*>(.*?)<\/' . 
                   preg_quote($tag, '/') . '>/is';
        preg_match_all($pattern, $html, $matches);
        
        if (empty($matches[1])) {
            return $index === 'all' ? array() : '';
        }
        
        if ($index === 'all') {
            return $matches[1];
        } elseif ($index === -1) {
            return end($matches[1]);
        } else {
            return isset($matches[1][$index]) ? $matches[1][$index] : '';
        }
    }
    
    /**
     * Ichki elementlarni olish (masalan td ichidagi a taglar)
     * 
     * @param string $html - HTML kod
     * @param string $parent_tag - Ota element
     * @param string $child_tag - Bola element
     * @param array $parent_attrs - Ota element atributlari
     * @param array $child_attrs - Bola element atributlari
     * @return array - Topilgan elementlar
     */
    public static function getNestedElements($html, $parent_tag, $child_tag, 
                                           $parent_attrs = array(), $child_attrs = array()) {
        // Birinchi ota elementlarni topish
        $parents = self::getTagContent($html, $parent_tag, $parent_attrs, 'all');
        
        $results = array();
        foreach ($parents as $parent_content) {
            // Har bir ota element ichidan bola elementlarni topish
            $children = self::getTagContent($parent_content, $child_tag, $child_attrs, 'all');
            $results = array_merge($results, $children);
        }
        
        return $results;
    }
    
    /**
     * URL larni olish
     * 
     * @param string $html - HTML kod
     * @param string $type - 'href', 'src' yoki 'all'
     * @return array - Topilgan URL lar
     */
    public static function getUrls($html, $type = 'all') {
        $urls = array();
        
        if ($type === 'href' || $type === 'all') {
            preg_match_all('/href=["\']([^"\']*)["\']/', $html, $matches);
            $urls = array_merge($urls, $matches[1]);
        }
        
        if ($type === 'src' || $type === 'all') {
            preg_match_all('/src=["\']([^"\']*)["\']/', $html, $matches);
            $urls = array_merge($urls, $matches[1]);
        }
        
        return array_unique($urls);
    }
    
    /**
     * Matnni tozalash
     * 
     * @param string $html - HTML kod
     * @param bool $decode_entities - HTML entities ni decode qilish
     * @return string - Tozalangan matn
     */
    public static function cleanText($html, $decode_entities = true) {
        $text = strip_tags($html);
        if ($decode_entities) {
            $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        }
        return trim(preg_replace('/\s+/', ' ', $text));
    }
    
    /**
     * CSS selectorni XPath ga o'tkazish
     * 
     * @param string $selector - CSS selector
     * @return string - XPath
     */
    private static function cssToXPath($selector) {
        $selector = trim($selector);
        
        // Oddiy selectorlar uchun
        if (strpos($selector, '#') === 0) {
            // ID selector
            return "//*[@id='" . substr($selector, 1) . "']";
        } elseif (strpos($selector, '.') === 0) {
            // Class selector
            return "//*[contains(@class, '" . substr($selector, 1) . "')]";
        } elseif (preg_match('/^[a-zA-Z]+$/', $selector)) {
            // Tag selector
            return "//" . $selector;
        } else {
            // Murakkab selectorlar uchun oddiy XPath
            return "//*[contains(@class, '" . $selector . "')]";
        }
    }
}
?>
