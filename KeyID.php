<?php
/**
 * KeyID version 0.1
 * 
 * Klass för att generera ID-strängar med tre inbyggda fördelar:
 * 
 * 1) De är inte uppräkningsbara och medför där av en större säkerhet för 
 * resurser som publikt exponeras med sitt ID.
 * 
 * 2) De är verifierbara med algoritmen Luhn mod N. Ogilta ID:n kan därför 
 * förkastas utan att behöva kontrollera dem mot tex. en databas.
 * 
 * 3) Vid generering av ID är det möjligt att göra dem unika(*) för att motverka
 * kollision(**) mellan två eller flera resurser.
 * 
 * (*) Generering av unika ID:n innebär i teroin att de blir uppräkningsbara,
 * men med en rymd på minst 63^10 per unikt ID är det i praktiken inte rimligt 
 * att träffa rätt.
 * 
 * (**) Kollisionsrisk förekommer med som lägst inom rymden 63^10 per mikrosekund.
 */
class KeyID {
    
    private $lenght;
    private $pool;
    private $map;
    private $radix;
    private $id;
    
    public function __construct() {
        $this->lenght = 32;
        $this->pool   = str_split("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_");
        $this->map    = array_flip($this->pool);
        $this->radix  = count($this->pool);
    }
        
    /**
     * Generate an ID.
     * 
     * @param bool $unique Set to TRUE to generate a unique ID based on microtime. Set to FALSE to generate a random ID. Defaults to FALSE.
     * @return string The ID
     */
    public function Generate($unique = false) {
        if ($unique === true) {
            $mt = microtime(true);
            $unique_seed = [
                intval(($mt-intval($mt))*1000), 
                intval(date('s')), intval(date('i')), intval(date('G')), 
                intval(date('j')), intval(date('W')), intval(date('o'))
            ];
        }
        
        $this->id = "";
        $sum = 0;
        
        for ($i = 0; $i < $this->lenght-1; $i++) {
            $rand      = $unique === true && $i < 7 ? $unique_seed[$i] % $this->radix : rand(0, $this->radix-1);
            $this->id .= $this->pool[$rand];
            $add       = ($i % 2 === 0 ? 2 : 1) * $rand;
            $sum      += floor($add / $this->radix) + ($add % $this->radix);
        }
        
        $index = ($this->radix - ($sum % $this->radix)) % $this->radix;
        return $this->id . $this->pool[$index];
    }
    
    /**
     * Verify an ID.
     * 
     * The verification is done in three steps:
     * 
     *      1. Check lenght of ID and that it only contains aA-zZ and 0-9
     *      2. Calculate and compare the ID's control character
     * 
     * @param string $id The ID to be verified
     * @return boolean Returns true if ID is valid, otherwise false
     */
    public function Validate($id = "") {
        if (preg_match("/^[\w]{".$this->lenght."}$/s", $id) !== 1) {
            return false;
        }
        
        $chars = str_split($id);
        $sum = 0;
        
        for ($i = 0; $i < $this->lenght; $i++) {
            $add  = ($i % 2 === 0 ? 2 : 1) * $this->map[$chars[$i]];
            $sum += floor($add / $this->radix) + ($add % $this->radix);
        }
        
        return $sum % $this->radix === 0;
    }
    
    /**
     * Set the length of ID to generate or validate. Must be minimum 16. Defaults to 32.
     * 
     * @param int $length 
     */
    public function SetLength($length = 32) {
        if (empty($length)) {
            $this->lenght = 32;
        } else {
            $this->lenght = $length < 16 ? 16 : intval($length);
        }
    }
}
