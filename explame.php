<?php
require_once 'HtmlParser.php';

// HTML test kodlari
$html = '
<div class="container">
    <a href="https://example.com" data-info="https://data.com">Buy Now</a>
    <a href="https://example2.com" class="btn">Download</a>
    <td>
        <a href="https://link1.com">Link 1</a>
        <li><a href="https://link2.com">Link 2</a></li>
        <li><a href="https://link3.com">Link 3</a></li>
    </td>
    <p data-price="100">Narx: $100</p>
    <p data-price="200">Narx: $200</p>
</div>
';

echo "<h2>1. Ikki belgi orasidagi ma'lumotni olish:</h2>";
$dataInfo = HtmlParser::getBetween($html, 'data-info="', '"');
echo "data-info qiymati: " . $dataInfo . "<br>";

echo "<h2>2. Atribut qiymatlarini olish:</h2>";
// Birinchi href ni olish
$firstHref = HtmlParser::getAttribute($html, 'href');
echo "Birinchi href: " . $firstHref . "<br>";

// 2-4 oralig'idagi href larni olish
$rangeHrefs = HtmlParser::getAttribute($html, 'href', 1, 3);
echo "2-4 oralig'idagi href lar: " . implode(', ', $rangeHrefs) . "<br>";

echo "<h2>3. Tag ichidagi ma'lumotni olish:</h2>";
$aContent = HtmlParser::getTagContent($html, 'a', array(), 'all');
echo "Barcha 'a' tag mazmuni: " . implode(' | ', $aContent) . "<br>";

echo "<h2>4. Ichki elementlarni olish:</h2>";
$nestedLinks = HtmlParser::getNestedElements($html, 'td', 'a');
echo "TD ichidagi A taglar: " . implode(' | ', $nestedLinks) . "<br>";

echo "<h2>5. URL larni olish:</h2>";
$allUrls = HtmlParser::getUrls($html);
echo "Barcha URL lar: " . implode('<br>', $allUrls) . "<br>";

echo "<h2>6. Ma'lum atribut bilan elementni olish:</h2>";
$priceElements = HtmlParser::getTagContent($html, 'p', array('data-price' => '100'));
echo "data-price='100' bo'lgan element: " . $priceElements . "<br>";

echo "<h2>7. CSS selector orqali olish:</h2>";
$btnElements = HtmlParser::getBySelector($html, '.btn');
echo "Class 'btn' bo'lgan elementlar: " . implode(' | ', $btnElements) . "<br>";

?>
