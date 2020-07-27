# Dadata API Client

> Data cleansing, enrichment and suggestions via [Dadata API](https://dadata.ru/api)

[![Latest Stable Version][packagist-image]][packagist-url]
[![Total Downloads][downloads-image]][packagist-url]
[![License][license-image]][packagist-url]

Thin PHP wrapper over Dadata API.

## Installation

```sh
composer require hflabs/dadata
```

Requirements:

-   PHP 5.6+
-   Guzzle 6

## Usage

Create API client instance:

```php
> $token = "Replace with Dadata API key";
> $secret = "Replace with Dadata secret key";
> $dadata = new \Dadata\DadataClient($token, $secret);
```

Then call API methods as specified below.

## Postal Address

### [Validate and cleanse address](https://dadata.ru/api/clean/address/)

```php
> $response = $dadata->clean("address", "мск сухонская 11 89");
> var_dump($response);
array(80) {
  ["source"]=>
  string(31) "мск сухонская 11 89"
  ["result"]=>
  string(56) "г Москва, ул Сухонская, д 11, кв 89"
  ["postal_code"]=>
  string(6) "127642"
  ["country"]=>
  string(12) "Россия"
  ["federal_district"]=>
  string(22) "Центральный"
  ["region"]=>
  string(12) "Москва"
  ["city_area"]=>
  string(31) "Северо-восточный"
  ["city_district"]=>
  string(37) "Северное Медведково"
  ["street"]=>
  string(18) "Сухонская"
  ["house"]=>
  string(2) "11"
  ["flat"]=>
  string(2) "89"
  ["flat_area"]=>
  string(4) "34.6"
  ["flat_price"]=>
  string(7) "6854710"
  ["fias_id"]=>
  string(36) "5ee84ac0-eb9a-4b42-b814-2f5f7c27c255"
  ["timezone"]=>
  string(5) "UTC+3"
  ["geo_lat"]=>
  string(10) "55.8782557"
  ["geo_lon"]=>
  string(8) "37.65372"
  ["qc_geo"]=>
  int(0)
  ["metro"]=>
  array(3) {...}
}
```

### [Geocode address](https://dadata.ru/api/geocode/)

Same API method as "validate and cleanse":

```php
> $response = $dadata->clean("address", "мск сухонская 11 89");
> var_dump($response);
array(80) {
  ["source"]=>
  string(31) "мск сухонская 11 89"
  ["result"]=>
  string(56) "г Москва, ул Сухонская, д 11, кв 89"
  ...
  ["geo_lat"]=>
  string(10) "55.8782557"
  ["geo_lon"]=>
  string(8) "37.65372"
  ["beltway_hit"]=>
  string(7) "IN_MKAD"
  ["beltway_distance"]=>
  NULL
  ["qc_geo"]=>
  int(0)
  ...
}
```

### [Reverse geocode address](https://dadata.ru/api/geolocate/)

```php
> $response = $dadata->geolocate("address", 55.878, 37.653);
> var_dump($response);
array(4) {
  [0]=>
  array(3) {
    ["value"]=>
    string(47) "г Москва, ул Сухонская, д 11"
    ...
  }
  [1]=>
  array(3) {
    ["value"]=>
    string(49) "г Москва, ул Сухонская, д 11А"
    ...
  }
  [2]=>
  array(3) {
    ["value"]=>
    string(47) "г Москва, ул Сухонская, д 13"
    ...
  }
  ...
}
```

### [GeoIP city](https://dadata.ru/api/iplocate/)

```php
> $response = $dadata->iplocate("46.226.227.20");
> var_dump($response);
array(3) {
  ["value"]=>
  string(21) "г Краснодар"
  ["unrestricted_value"]=>
  string(66) "350000, Краснодарский край, г Краснодар"
  ["data"]=>
  array(81) {
      ...
  }
}
```

### [Autocomplete (suggest) address](https://dadata.ru/api/suggest/address/)

```php
> $response = $dadata->suggest("address", "самара метал");
> var_dump($response);
array(5) {
  [0]=>
  array(3) {
    ["value"]=>
    string(49) "г Самара, пр-кт Металлургов"
    ...
  }
  [1]=>
  array(3) {
    ["value"]=>
    string(44) "г Самара, ул Металлистов"
    ...
  }
  [2]=>
  array(3) {
    ["value"]=>
    string(95) "г Самара, поселок Зубчаниновка, ул Металлургическая"
    ...
  }
  ...
}
```

Show suggestions in English:

```php
> $response = $dadata->suggest("address", "samara metal", 5, ["language" => "en"]);
> var_dump($response);
array(5) {
  [0]=>
  array(3) {
    ["value"]=>
    string(42) "Russia, gorod Samara, prospekt Metallurgov"
    ...
  }
  [1]=>
  array(3) {
    ["value"]=>
    string(39) "Russia, gorod Samara, ulitsa Metallistov"
    ...
  }
  [2]=>
  array(3) {
    ["value"]=>
    string(69) "Russia, gorod Samara, poselok Zubchaninovka, ulitsa Metallurgicheskaya"
    ...
  }
  ...
}
```

Constrain by city (Yuzhno-Sakhalinsk):

```php
> $locations = [[ "kladr_id" => "6500000100000" ]];
> $response = $dadata->suggest("address", "Ватутина", 5, ["locations" => $locations]);
> var_dump($response);
array(1) {
  [0]=>
  array(3) {
    ["value"]=>
    string(53) "г Южно-Сахалинск, ул Ватутина"
    ...
  }
}
```

Constrain by specific geo point and radius (in Vologda city):

```php
> $geo = [[ "lat" => 59.244634,  "lon" => 39.913355, "radius_meters" => 200 ]];
> $response = $dadata->suggest("address", "сухонская", 5, ["locations_geo" => $geo]);
> var_dump($response);
array(1) {
  [0]=>
  array(3) {
    ["value"]=>
    string(42) "г Вологда, ул Сухонская"
    ...
  }
}
```

Boost city to top (Toliatti):

```php
> $boost = [[ "kladr_id" => "6300000700000" ]];
> $response = $dadata->suggest("address", "авто", 5, ["locations_boost" => $boost]);
> var_dump($response);
array(5) {
  [0]=>
  array(3) {
    ["value"]=>
    string(85) "Самарская обл, г Тольятти, Автозаводское шоссе"
    ...
  }
  [1]=>
  array(3) {
    ["value"]=>
    string(81) "Самарская обл, г Тольятти, ул Автомобилистов"
    ...
  }
  [2]=>
  array(3) {
    ["value"]=>
    string(81) "Самарская обл, г Тольятти, ул Автостроителей"
    ...
  }
  ...
}
```

### [Find address by FIAS ID](https://dadata.ru/api/find-address/)

```php
> $response = $dadata->findById("address", "9120b43f-2fae-4838-a144-85e43c2bfb29");
> var_dump($response);
array(1) {
  [0]=>
  array(3) {
    ["value"]=>
    string(36) "г Москва, ул Снежная"
    ...
  }
}
```

Find by KLADR ID:

```php
> $response = $dadata->findById("address", "77000000000268400");
```

### [Find postal office](https://dadata.ru/api/suggest/postal_unit/)

Suggest postal office by address or code:

```php
> $response = $dadata->suggest("postal_unit", "дежнева 2а");
> var_dump($response);
array(1) {
  [0]=>
  array(3) {
    ["value"]=>
    string(6) "127642"
    ["unrestricted_value"]=>
    string(52) "г Москва, проезд Дежнёва, д 2А"
    ["data"]=>
    array(15) {
        ...
    }
  }
}
```

Find postal office by code:

```php
> $response = $dadata->findById("postal_unit", "127642");
> var_dump($response);
array(1) {
  [0]=>
  array(3) {
    ["value"]=>
    string(6) "127642"
    ["unrestricted_value"]=>
    string(52) "г Москва, проезд Дежнёва, д 2А"
    ["data"]=>
    array(15) {
        ...
    }
  }
}
```

Find nearest postal office:

```php
> $response = $dadata->geolocate("postal_unit", 55.878, 37.653, 1000);
> var_dump($response);
array(2) {
  [0]=>
  array(3) {
    ["value"]=>
    string(6) "127642"
    ["unrestricted_value"]=>
    string(52) "г Москва, проезд Дежнёва, д 2А"
    ["data"]=>
    array(15) {
        ...
    }
  },
  ...
}
```

### [Get City ID for delivery services](https://dadata.ru/api/delivery/)

```php
> $response = $dadata->findById("delivery", "3100400100000");
> var_dump($response);
array(1) {
  [0]=>
  array(3) {
    ["value"]=>
    string(13) "3100400100000"
    ["unrestricted_value"]=>
    string(36) "fe7eea4a-875a-4235-aa61-81c2a37a0440"
    ["data"]=>
    array(5) {
      ...
      ["boxberry_id"]=>
      string(5) "01929"
      ["cdek_id"]=>
      string(3) "344"
      ["dpd_id"]=>
      string(9) "196006461"
    }
  }
}
```

### [Get address strictly according to FIAS](https://dadata.ru/api/find-fias/)

```php
> $response = $dadata->findById("fias", "9120b43f-2fae-4838-a144-85e43c2bfb29");
> var_dump($response);
array(1) {
  [0]=>
  array(3) {
    ["value"]=>
    string(36) "г Москва, ул Снежная"
    ...
  }
}
```

### [Suggest country](https://dadata.ru/api/suggest/country/)

```php
> $response = $dadata->suggest("country", "та");
> var_dump($response);
array(4) {
  [0]=>
  array(3) {
    ["value"]=>
    string(22) "Таджикистан"
    ...
  },
  [1]=>
  array(3) {
    ["value"]=>
    string(14) "Таиланд"
    ...
  }
  [2]=>
  array(3) {
    ["value"]=>
    string(14) "Тайвань"
    ...
  }
  ...
}
```

## Company or individual enterpreneur

### [Find company by INN](https://dadata.ru/api/find-party/)

```php
> $response = $dadata->findById("party", "7707083893");
> var_dump($response);
array(5) {
  [0]=>
  array(3) {
    ["value"]=>
    string(23) "ПАО СБЕРБАНК"
    ["unrestricted_value"]=>
    string(23) "ПАО СБЕРБАНК"
    ["data"]=>
    array(29) {
      ["kpp"]=>
      string(9) "773601001"
      ["inn"]=>
      string(10) "7707083893"
      ...
    }
  },
  ...
}
```

Find by INN and KPP:

```php
> $response = $dadata->findById("party", "7707083893", 1, ["kpp" => "540602001"]);
> var_dump($response);
array(1) {
  [0]=>
  array(3) {
    ["value"]=>
    string(51) "СИБИРСКИЙ БАНК ПАО СБЕРБАНК"
    ["unrestricted_value"]=>
    string(51) "СИБИРСКИЙ БАНК ПАО СБЕРБАНК"
    ["data"]=>
    array(29) {
      ["kpp"]=>
      string(9) "540602001"
      ["inn"]=>
      string(10) "7707083893"
      ...
    }
  }
}
```

### [Suggest company](https://dadata.ru/api/suggest/party/)

```php
> $response = $dadata->suggest("party", "сбер");
> var_dump($response);
array(5) {
  [0]=>
  array(3) {
    ["value"]=>
    string(23) "ПАО СБЕРБАНК"
    ...
  }
  [1]=>
  array(3) {
    ["value"]=>
    string(48) "АО "СБЕРЭНЕРГОСЕРВИС-ЮГРА""
    ...
  }
  [2]=>
  array(3) {
    ["value"]=>
    string(27) "АО "СБЕРБРОКЕР""
    ...
  }
  ...
}
```

Constrain by specific regions (Saint Petersburg and Leningradskaya oblast):

```php
> $locations = [[ "kladr_id" => "7800000000000" ], [ "kladr_id" => "4700000000000"]];
> $response = $dadata->suggest("party", "сбер", 5, ["locations" => $locations]);
```

Constrain by active companies:

```php
> $status = [ "ACTIVE" ];
> $response = $dadata->suggest("party", "сбер", 5, ["status" => $status]);
```

Constrain by individual entrepreneurs:

```php
> $response = $dadata->suggest("party", "сбер", 5, ["type" => "INDIVIDUAL"]);
```

Constrain by head companies, no branches:

```php
> $branch_type = [ "MAIN" ];
> $response = $dadata->suggest("party", "сбер", 5, ["branch_type" => $branch_type]);
```

### [Find affiliated companies](https://dadata.ru/api/find-affiliated/)

```php
> $response = $dadata->findAffiliated("7736207543");
> var_dump($response);
array(5) {
  [0]=>
  array(3) {
    ["value"]=>
    string(36) "ООО "ДЗЕН.ПЛАТФОРМА""
    ...
  }
  [1]=>
  array(3) {
    ["value"]=>
    string(21) "ООО "ЕДАДИЛ""
    ...
  }
  [2]=>
  array(3) {
    ["value"]=>
    string(21) "ООО "ЗНАНИЕ""
    ...
  }
  ...
}
```

Search only by manager INN:

```php
> $response = $dadata->findAffiliated("773006366201", 5, ["scope" => "MANAGERS"]);
> var_dump($response);
array(3) {
  [0]=>
  array(3) {
    ["value"]=>
    string(21) "ООО "ЯНДЕКС""
    ...
  }
  [1]=>
  array(3) {
    ["value"]=>
    string(13) "МФ "ФОИ""
    ...
  }
  [2]=>
  array(3) {
    ["value"]=>
    string(22) "АНО ДПО "ШАД""
    ...
  }
}
```

## Bank

### [Find bank by BIC, SWIFT or INN](https://dadata.ru/api/find-bank/)

```php
> $response = $dadata->findById("bank", "044525225");
> var_dump($response);
array(1) {
  [0]=>
  array(3) {
    ["value"]=>
    string(23) "ПАО Сбербанк"
    ["unrestricted_value"]=>
    string(23) "ПАО Сбербанк"
    ["data"]=>
    array(14) {
       ["bic"]=>
      string(9) "044525225"
      ["swift"]=>
      string(8) "SABRRUMM"
      ["inn"]=>
      string(10) "7707083893"
      ...
    }
  }
}
```

Find by SWIFT code:

```php
> $response = $dadata->findById("bank", "SABRRUMM");
```

Find by INN:

```php
> $response = $dadata->findById("bank", "7728168971");
```

Find by INN and KPP:

```php
> $response = $dadata->findById("bank", "7728168971", 1, ["kpp" => "667102002"]);
```

Find by registration number:

```php
> $response = $dadata->findById("bank", "1481");
```

### [Suggest bank](https://dadata.ru/api/suggest/bank/)

```php
> $response = $dadata->suggest("bank", "ти");
> var_dump($response);
array(5) {
  [0]=>
  array(3) {
    ["value"]=>
    string(28) "АО «Тимер Банк»"
    ...
  }
  [1]=>
  array(3) {
    ["value"]=>
    string(34) "АО «Тинькофф Банк»"
    ...
  }
  [2]=>
  array(3) {
    ["value"]=>
    string(65) "«Азиатско-Тихоокеанский Банк» (ПАО)"
    ...
  }
  ...
}
```

## Personal name

### [Validate and cleanse name](https://dadata.ru/api/clean/name/)

```php
> $response = $dadata->clean("name", "Срегей владимерович иванов");
> var_dump($response);
array(10) {
  ["source"]=>
  string(50) "Срегей владимерович иванов"
  ["result"]=>
  ...
  ["surname"]=>
  string(12) "Иванов"
  ["name"]=>
  string(12) "Сергей"
  ["patronymic"]=>
  string(24) "Владимирович"
  ["gender"]=>
  string(2) "М"
  ["qc"]=>
  int(1)
}
```

### [Suggest name](https://dadata.ru/api/suggest/name/)

```php
> $response = $dadata->suggest("fio", "викт");
> var_dump($response);
array(5) {
  [0]=>
  array(3) {
    ["value"]=>
    string(12) "Виктор"
    ...
  }
  [1]=>
  array(3) {
    ["value"]=>
    string(16) "Виктория"
    ...
  }
  [2]=>
  array(3) {
    ["value"]=>
    string(18) "Викторова"
    ...
  }
  ...
}
```

Suggest female first name:

```php
> $filter = ["parts" => ["NAME"], gender => "FEMALE"];
> $response = $dadata->suggest("fio", "викт", 5, $filter);
> var_dump($response);
array(2) {
  [0]=>
  array(3) {
    ["value"]=>
    string(16) "Виктория"
    ...
  }
  [1]=>
  array(3) {
    ["value"]=>
    string(18) "Викторина"
    ...
  }
}

```

## Phone

### [Validate and cleanse phone](https://dadata.ru/api/clean/phone/)

```php
> $response = $dadata->clean("phone", "9168-233-454");
> var_dump($response);
array(14) {
  ["source"]=>
  string(12) "9168-233-454"
  ["type"]=>
  string(18) "Мобильный"
  ["phone"]=>
  string(16) "+7 916 823-34-54"
  ...
  ["provider"]=>
  string(50) "ПАО "Мобильные ТелеСистемы""
  ["country"]=>
  string(12) "Россия"
  ["region"]=>
  string(51) "Москва и Московская область"
  ["timezone"]=>
  string(5) "UTC+3"
  ["qc"]=>
  int(0)
}
```

## Passport

### [Validate passport](https://dadata.ru/api/clean/passport/)

```php
> $response = $dadata->clean("passport", "4509 235857");
> var_dump($response);
array(4) {
  ["source"]=>
  string(11) "4509 235857"
  ["series"]=>
  string(5) "45 09"
  ["number"]=>
  string(6) "235857"
  ["qc"]=>
  int(0)
}
```

### [Suggest issued by](https://dadata.ru/api/suggest/fms_unit/)

```php
> $response = $dadata->suggest("fms_unit", "772 053");
> var_dump($response);
array(5) {
  [0]=>
  array(3) {
    ["value"]=>
    string(36) "ОВД ЗЮЗИНО Г. МОСКВЫ"
    ...
  }
  [1]=>
  array(3) {
    ["value"]=>
    string(68) "ОВД ЗЮЗИНО Г. МОСКВЫ ПАСПОРТНЫЙ СТОЛ 1"
    ...
  }
  [2]=>
  array(3) {
    ["value"]=>
    string(57) "ОВД ЗЮЗИНО ПС УВД ЮЗАО Г. МОСКВЫ"
    ...
  }
  ...
}
```

## Email

### [Validate email](https://dadata.ru/api/clean/email/)

```php
> $response = $dadata->clean("email", "serega@yandex/ru");
> var_dump($response);
array(6) {
  ["source"]=>
  string(16) "serega@yandex/ru"
  ["email"]=>
  string(16) "serega@yandex.ru"
  ["local"]=>
  string(6) "serega"
  ["domain"]=>
  string(9) "yandex.ru"
  ["type"]=>
  string(8) "PERSONAL"
  ["qc"]=>
  int(4)
}
```

### [Suggest email](https://dadata.ru/api/suggest/email/)

```php
> $response = $dadata->suggest("email", "maria@");
> var_dump($response);
array(5) {
  [0]=>
  array(3) {
    ["value"]=>
    string(13) "maria@mail.ru"
    ...
  }
  [1]=>
  array(3) {
    ["value"]=>
    string(15) "maria@gmail.com"
    ...
  }
  [2]=>
  array(3) {
    ["value"]=>
    string(15) "maria@yandex.ru"
    ...
  }
  ...
}
```

## Other datasets

### [Tax office](https://dadata.ru/api/suggest/fns_unit/)

```php
> $response = $dadata->findById("fns_unit", "5257");
> var_dump($response);
array(1) {
  [0]=>
  array(3) {
    ["value"]=>
    string(118) "Инспекция ФНС России по Канавинскому району г.Нижнего Новгорода"
    ["unrestricted_value"]=>
    string(118) "Инспекция ФНС России по Канавинскому району г.Нижнего Новгорода"
    ["data"]=>
    array(18) {
      ["code"]=>
      string(4) "5257"
      ["oktmo"]=>
      string(8) "22701000"
      ["inn"]=>
      string(10) "5257046101"
      ["kpp"]=>
      string(9) "525701001"
      ...
    }
  }
}

```

### [Regional court](https://dadata.ru/api/suggest/region_court/)

```php
> $response = $dadata->suggest("region_court", "таганско");
> var_dump($response);
array(5) {
  [0]=>
  array(3) {
    ["value"]=>
    string(109) "Судебный участок № 371 Таганского судебного района г. Москвы"
    ...
  }
  [1]=>
  array(3) {
    ["value"]=>
    string(109) "Судебный участок № 372 Таганского судебного района г. Москвы"
    ...
  }
  [2]=>
  array(3) {
    ["value"]=>
    string(109) "Судебный участок № 373 Таганского судебного района г. Москвы"
    ...
  }
  ...
}
```

### [Metro station](https://dadata.ru/api/suggest/metro/)

```php
> $response = $dadata->suggest("metro", "алекс");
> var_dump($response);
array(4) {
  [0]=>
  array(3) {
    ["value"]=>
    string(37) "Александровский сад"
    ...
  }
  [1]=>
  array(3) {
    ["value"]=>
    string(24) "Алексеевская"
    ...
  }
  [2]=>
  array(3) {
    ["value"]=>
    string(54) "Площадь Александра Невского 1"
    ...
  }
  ...
}
```

Constrain by city (Saint Petersburg):

```php
> $filters = [[ "city" => "Санкт-Петербург" ]];
> $response = $dadata->suggest("metro", "алекс", 5, ["filters" => $filters]);
> var_dump($response);
array(2) {
  [0]=>
  array(3) {
    ["value"]=>
    string(54) "Площадь Александра Невского 1"
    ...
  }
  [1]=>
  array(3) {
    ["value"]=>
    string(54) "Площадь Александра Невского 2"
    ...
  }
}
```

### [Car brand](https://dadata.ru/api/suggest/car_brand/)

```php
> $response = $dadata->suggest("car_brand", "фо");
> var_dump($response);
array(3) {
  [0]=>
  array(3) {
    ["value"]=>
    string(10) "Volkswagen"
    ...
  }
  [1]=>
  array(3) {
    ["value"]=>
    string(4) "Ford"
    ...
  }
  [2]=>
  array(3) {
    ["value"]=>
    string(5) "Foton"
    ...
  }
}
```

### [Currency](https://dadata.ru/api/suggest/currency/)

```php
> $response = $dadata->suggest("currency", "руб");
> var_dump($response);
array(2) {
  [0]=>
  array(3) {
    ["value"]=>
    string(33) "Белорусский рубль"
    ...
  }
  [1]=>
  array(3) {
    ["value"]=>
    string(31) "Российский рубль"
    ...
  }
}
```

### [OKVED 2](https://dadata.ru/api/suggest/okved2/)

```php
> $response = $dadata->suggest("okved2", "космических");
> var_dump($response);
array(5) {
  [0]=>
  array(3) {
    ["value"]=>
    string(139) "Производство космических аппаратов (в том числе спутников), ракет-носителей"
    ...
  }
  [1]=>
  array(3) {
    ["value"]=>
    string(139) "Производство частей и принадлежностей летательных и космических аппаратов"
    ...
  }
  [2]=>
  array(3) {
    ["value"]=>
    string(95) "Производство автоматических космических аппаратов"
    ...
  }
  ...
}
```

### [OKPD 2](https://dadata.ru/api/suggest/okpd2/)

```php
> $response = $dadata->suggest("okpd2", "калоши");
> var_dump($response);
array(1) {
  [0]=>
  array(3) {
    ["value"]=>
    string(91) "Услуги по обрезинованию валенок (рыбацкие калоши)"
    ...
  }
}
```

## Profile API

Balance:

```php
> $response = $dadata->getBalance();
> var_dump($response);
float(8238.20)
```

Usage stats:

```php
> $response = $dadata->getDailyStats();
> var_dump($response);
array(2) {
  ["date"]=>
  string(10) "2020-07-27"
  ["services"]=>
  array(3) {
    ["merging"]=>
    int(0)
    ["suggestions"]=>
    int(45521)
    ["clean"]=>
    int(1200)
  }
}

```

Dataset versions:

```php
> $response = $dadata->getVersions();
> var_dump($response);
array(3) {
  ["dadata"]=>
  array(1) {
    ["version"]=>
    string(26) "stable (9048:bf33b2acc8ba)"
  }
  ["suggestions"]=>
  array(2) {
    ["version"]=>
    string(15) "20.5 (b55eb7c4)"
    ["resources"]=>
    array(4) {
      ...
    }
  }
  ["factor"]=>
  array(2) {
    ["version"]=>
    string(16) "20.06 (eb70078e)"
    ["resources"]=>
    array(8) {
      ...
    }
  }
}

```

## Development setup

```sh
$ composer install
$ ./vendor/bin/phpunit tests
```

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Make sure to add or update tests as appropriate.

Use [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0-beta.4/) for commit messages.

## [Changelog](CHANGELOG.md)

This library uses [CalVer](https://calver.org/) with YY.MM.MICRO schema. See changelog for details specific to each release.

## License

[MIT](https://choosealicense.com/licenses/mit/)

<!-- Markdown link & img dfn's -->

[packagist-url]: https://packagist.org/packages/hflabs/dadata
[packagist-image]: https://poser.pugx.org/hflabs/dadata/v/stable.svg
[downloads-image]: https://poser.pugx.org/hflabs/dadata/downloads.svg
[license-image]: https://poser.pugx.org/hflabs/dadata/license.svg
