# PHP HTML Parser 📄

Kuchli va oson foydalaniladigan PHP HTML parser kutubxonasi. HTML kodlardan ma'lumot olish, taglarni qidirish va turli xil web scraping vazifalarini bajarish uchun mo'ljallangan.

## 🚀 Xususiyatlar

- ✅ Ikki belgi orasidagi ma'lumotlarni olish
- ✅ Atribut qiymatlarini olish va filterlash
- ✅ CSS selector orqali elementlarni topish
- ✅ Ichma-ich joylashgan elementlarni qidirish
- ✅ Tag ichidagi ma'lumotlarni olish
- ✅ URL larni avtomatik topish va olish
- ✅ HTML taglarni tozalab, faqat matnni olish
- ✅ Bir nechta elementdan ma'lum oraliqni tanlash

## 📦 O'rnatish

Faylni yuklab oling va loyihangizga qo'shing:

```php
require_once 'HtmlParser.php';
```

## 🔧 Foydalanish

### 1. Ikki belgi orasidagi ma'lumotni olish

```php
$html = '<a href="https://example.com" data-info="https://api.data.com/info">Buy Now</a>';

// data-info qiymatini olish
$dataInfo = HtmlParser::getBetween($html, 'data-info="', '"');
echo $dataInfo; // https://api.data.com/info

// Birinchi topilgan qiymatni olish
$firstLink = HtmlParser::getBetween($html, 'href="', '"', 0);

// Oxirgi topilgan qiymatni olish
$lastLink = HtmlParser::getBetween($html, 'href="', '"', -1);

// Barcha qiymatlarni olish
$allLinks = HtmlParser::getBetween($html, 'href="', '"', 'all');
```

### 2. Atribut qiymatlarini olish

```php
$html = '
<a href="https://link1.com">Link 1</a>
<a href="https://link2.com">Link 2</a>
<a href="https://link3.com">Link 3</a>
<a href="https://link4.com">Link 4</a>
<a href="https://link5.com">Link 5</a>
';

// Birinchi href ni olish
$firstHref = HtmlParser::getAttribute($html, 'href');
echo $firstHref; // https://link1.com

// 2-4 oralig'idagi href larni olish (2-dan boshlab 3 ta)
$rangeHrefs = HtmlParser::getAttribute($html, 'href', 1, 3);
print_r($rangeHrefs); 
// Array: [https://link2.com, https://link3.com, https://link4.com]

// 5-href dan boshqa barcha href larni olish
$manyHrefs = HtmlParser::getAttribute($html, 'href', 0, 10);
```

### 3. Tag ichidagi ma'lumotni olish

```php
$html = '
<div class="product">Mahsulot 1</div>
<div class="product">Mahsulot 2</div>
<div class="news">Yangilik</div>
';

// Barcha div taglar ichidagi ma'lumotni olish
$allDivs = HtmlParser::getTagContent($html, 'div', array(), 'all');
print_r($allDivs);

// Class="product" bo'lgan div larni olish
$products = HtmlParser::getTagContent($html, 'div', array('class' => 'product'), 'all');
print_r($products); // [Mahsulot 1, Mahsulot 2]

// Birinchi product div ni olish
$firstProduct = HtmlParser::getTagContent($html, 'div', array('class' => 'product'), 0);
echo $firstProduct; // Mahsulot 1
```

### 4. Ichma-ich joylashgan elementlarni olish

```php
$html = '
<table>
    <tr>
        <td>
            <a href="https://link1.com">Havola 1</a>
            <span>Matn</span>
        </td>
        <td>
            <a href="https://link2.com">Havola 2</a>
            <li><a href="https://link3.com">Havola 3</a></li>
        </td>
    </tr>
</table>
';

// TD ichidagi barcha A taglarni olish
$nestedLinks = HtmlParser::getNestedElements($html, 'td', 'a');
print_r($nestedLinks);
// [Havola 1, Havola 2, Havola 3]

// Ma'lum atributli ota va bola elementlarni olish
$specificLinks = HtmlParser::getNestedElements(
    $html, 
    'td',           // Ota element
    'a',            // Bola element
    array(),        // Ota element atributlari (bo'sh)
    array('href' => 'https://link2.com') // Bola element atributlari
);
```

### 5. CSS Selector orqali qidirish

```php
$html = '
<div id="main">Asosiy</div>
<p class="text">Matn 1</p>
<p class="text">Matn 2</p>
<span class="highlight">Ajratilgan</span>
';

// ID bo'yicha qidirish
$mainDiv = HtmlParser::getBySelector($html, '#main');

// Class bo'yicha qidirish
$textElements = HtmlParser::getBySelector($html, '.text');

// Tag bo'yicha qidirish
$allParagraphs = HtmlParser::getBySelector($html, 'p');

// Birinchi elementni olish
$firstText = HtmlParser::getBySelector($html, '.text', 0);
```

### 6. URL larni olish

```php
$html = '
<a href="https://website1.com">Sayt 1</a>
<img src="https://image1.jpg" alt="Rasm">
<a href="mailto:test@email.com">Email</a>
<img src="https://image2.png">
';

// Barcha URL larni olish (href va src)
$allUrls = HtmlParser::getUrls($html);
print_r($allUrls);

// Faqat href larni olish
$hrefUrls = HtmlParser::getUrls($html, 'href');
print_r($hrefUrls);

// Faqat src larni olish  
$srcUrls = HtmlParser::getUrls($html, 'src');
print_r($srcUrls);
```

### 7. Matnni tozalash

```php
$html = '<p>Bu <strong>muhim</strong> matn &quot;qo\'shtirnoq&quot; bilan.</p>';

// HTML taglarni olib tashlash
$cleanText = HtmlParser::cleanText($html);
echo $cleanText; // Bu muhim matn "qo'shtirnoq" bilan.

// HTML entities ni decode qilmasdan
$rawText = HtmlParser::cleanText($html, false);
echo $rawText; // Bu muhim matn &quot;qo'shtirnoq&quot; bilan.
```

## 🎯 Amaliy Misollar

### Web sahifadan mahsulot ma'lumotlarini olish

```php
$productHtml = '
<div class="product">
    <h3 class="title">Telefon Samsung Galaxy</h3>
    <span class="price" data-price="500">$500</span>
    <a href="https://shop.com/buy/123" class="buy-btn" data-id="123">Sotib olish</a>
    <div class="description">
        <p>Ajoyib telefon <strong>yangi</strong> texnologiya bilan</p>
    </div>
</div>
';

// Mahsulot nomini olish
$title = HtmlParser::getTagContent($productHtml, 'h3', array('class' => 'title'));
$cleanTitle = HtmlParser::cleanText($title);
echo "Nomi: " . $cleanTitle . "\n";

// Narxni olish
$price = HtmlParser::getAttribute($productHtml, 'data-price');
echo "Narxi: $" . $price . "\n";

// Sotib olish havolasini olish
$buyLink = HtmlParser::getAttribute($productHtml, 'href');
echo "Havola: " . $buyLink . "\n";

// Mahsulot ID sini olish
$productId = HtmlParser::getAttribute($productHtml, 'data-id');
echo "ID: " . $productId . "\n";

// Tavsifni olish
$description = HtmlParser::getTagContent($productHtml, 'div', array('class' => 'description'));
$cleanDescription = HtmlParser::cleanText($description);
echo "Tavsif: " . $cleanDescription . "\n";
```

### Jadvaldan ma'lumot olish

```php
$tableHtml = '
<table class="data-table">
    <tr>
        <td data-name="John">John Doe</td>
        <td><a href="mailto:john@email.com">john@email.com</a></td>
        <td><a href="https://john-website.com">Website</a></td>
    </tr>
    <tr>
        <td data-name="Jane">Jane Smith</td>
        <td><a href="mailto:jane@email.com">jane@email.com</a></td>
        <td><a href="https://jane-website.com">Website</a></td>
    </tr>
</table>
';

// Barcha ismlarni olish
$names = HtmlParser::getAttribute($tableHtml, 'data-name', 0, 10);
print_r($names);

// Email manzillarini olish
$emails = HtmlParser::getNestedElements($tableHtml, 'tr', 'a');
$emailLinks = array();
foreach($emails as $email) {
    if(strpos($email, 'mailto:') !== false) {
        $emailAddr = HtmlParser::getAttribute($email, 'href');
        $emailLinks[] = str_replace('mailto:', '', $emailAddr);
    }
}
print_r($emailLinks);
```

### JSON ma'lumotlarni olish

```php
$htmlWithJson = '
<script type="application/json" id="data">
{"products": [{"name": "Telefon", "price": 500}]}
</script>
';

// JSON ma'lumotni olish
$jsonData = HtmlParser::getTagContent($htmlWithJson, 'script', array('type' => 'application/json'));
$data = json_decode($jsonData, true);
print_r($data);
```

## 🛠️ Parametrlar

### getBetween() parametrlari
- `$html` - HTML kod (string)
- `$start` - Boshlanish belgisi (string)
- `$end` - Tugash belgisi (string)  
- `$index` - Element indeksi (int: 0,1,2... yoki -1 oxirgi uchun, 'all' barchasi uchun)

### getAttribute() parametrlari
- `$html` - HTML kod (string)
- `$attribute` - Atribut nomi (string)
- `$start_index` - Boshlang'ich indeks (int, default: 0)
- `$count` - Nechta element olish (int, default: 1)

### getTagContent() parametrlari
- `$html` - HTML kod (string)
- `$tag` - Tag nomi (string)
- `$attributes` - Atributlar array ko'rinishida (array, default: empty)
- `$index` - Element indeksi (int/string: 0,1,2... yoki 'all')

## ⚠️ Muhim Eslatmalar

1. **Xotira iste'moli**: Katta HTML fayllar bilan ishlaganda `getBySelector()` ko'proq xotira ishlatadi
2. **Regex xavfsizligi**: Foydalanuvchi ma'lumotlarini to'g'ridan-to'g'ri regex ga bermang
3. **Encoding**: UTF-8 encoding dan foydalaning
4. **Error handling**: Katta loyihalarda try-catch bloklar qo'shing

## 📈 Performance Maslahatlar

- Oddiy operatsiyalar uchun `getBetween()` dan foydalaning
- Murakkab selectorlar uchun `getBySelector()` ishlatingg
- Katta HTML lar uchun ma'lumotni qismlarga bo'ling
- Bir nechta element olayotganda `'all'` parametrini ishlating

## 🤝 Hissa qo'shish

Agar xatolik topsangiz yoki yangi funksiya taklif qilmoqchi bo'lsangiz, GitHub da Issue oching yoki Pull Request yuboring.

## 📄 Litsenziya

MIT License - bepul foydalanish va o'zgartirish mumkin.

---

**Yaratuvchi:** UzbekApis  
**Versiya:** 1.0.0  
**So'nggi yangilanish:** 2025
