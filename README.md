# KeyID

Version 0.1

Klass för att generera ID-strängar med tre inbyggda fördelar:

1. De är inte uppräkningsbara och medför där av en större säkerhet för resurser som publikt exponeras med sitt ID.

2. De är verifierbara med algoritmen Luhn mod N. Ogiltiga ID:n kan därför förkastas utan att behöva kontrollera dem mot tex. en databas.

3. Vid generering av ID är det möjligt att göra dem unika(&ast;) för att motverka kollision(&ast;&ast;) mellan två eller flera resurser.

&ast; Generering av unika ID:n innebär i teroin att de blir uppräkningsbara,
men med en rymd på minst 63^10 per unikt ID är det i praktiken inte rimligt 
att träffa rätt.

&ast;&ast; Kollisionsrisk förekommer som lägst inom rymden 63^10 per mikrosekund.

# Exempel

Generera och verifiera ID:
```php
$KeyID = new KeyID();
$id = $KeyID->Generate();
print $id; // NXb2PhK9GCsp2kANPbD0FuTgXnR6t90m
$KeyId->Validate($id); // true
```

Generera ID med en viss längd:
```php
$KeyID = new KeyID();
$KeyID->SetLength(20);
$id = $KeyID->Generate();
print $id; // M_B9YMOKMTypQQmxB9qm
```

Generera unikt ID:
```php
$KeyID = new KeyID();
for ($i = 0; $i < 5; $i++) {
  print $KeyID->Generate(true); // Sätt till true
}
// Output:
// y1PglLb8ZlAjWDaG63SH3jGQVkqx3ONu
// z1PglLbJuZ7VYmTNxjXjMXPHQxeIGKoO
// A1PglLbVAJpLkZldNTU7W0W6QSwwVvAr
// B1PglLbZ5TKSVtQ_lasxcdQHYU5Alcdq
// D1PglLbrIupaBxreyFJXctDc1WkVmOOK
```
