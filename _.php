<?php
/**
 * UTF-8 i18n and l10n class for working with strings and dates.
 * This class is based heavly on the great work done by Alix Alex.
 *
 * @see https://github.com/alixaxel/phunction
 */
class _
{
	/**
	 * Convert a string to UTF-8 and remove invalid bytes sequences.
	 *
	 * @param string $s string to convert
	 * @param string $e current encoding of string (default to UTF-8)
	 * @param string $c TRUE to keep "Other" characters
	 * @return string
	 */
	static function convert($s,$e=0,$c=1)
	{
		// Try to figureout what the encoding is if posible
		if(function_exists('mb_detect_encoding'))$e=mb_detect_encoding($s,'auto');

		// Convert to valid UTF-8
		if(($s=@iconv(!$e?'UTF-8':$e,'UTF-8//IGNORE',$s))!==false)
		{
			// Optionally remove "other" characters and windows useless "\r"
			return$c?preg_replace('~\p{C}+~u','',$s):preg_replace(array('~\r\n?~','~[^\P{C}\t\n]+~u'),array("\n",''),$s);
		}
	}


	/**
	 * Return an IntlDateFormatter object using the current system locale
	 *
	 * @see IntlDateFormatter
	 * @param string $l locale string
	 * @param integer $d datetype IntlDateFormatter constant
	 * @param integer $t timetype IntlDateFormatter constant
	 * @param string $z timezone Time zone ID, default is system default
	 * @return IntlDateFormatter
	 */
	static function date($l=0,$d=IntlDateFormatter::MEDIUM,$t=IntlDateFormatter::SHORT,$z=NULL)
	{
		return new IntlDateFormatter($l?:setlocale(LC_ALL,0),$d,$t,$z);
	}


	/**
	 * Format the given string using the current system locale
	 * Basically, it's sprintf on i18n steroids.
	 *
	 * @see MessageFormatter
	 * @param string $s string to parse
	 * @param array $p params to insert
	 * @return string
	 */
	static function format($s,array$p=NULL)
	{
		return msgfmt_format_message(setlocale(LC_ALL,0),$s,$p);
	}


	/**
	 * Normalize the given UTF-8 string
	 *
	 * @see http://stackoverflow.com/a/7934397/99923
	 * @param string $s string to normalize
	 * @param int $f form to normalize as
	 * @return string
	 */
	static function normalize($s,$f=Normalizer::FORM_D)
	{
		return normalizer_normalize($s,$f);
	}


	/**
	 * Remove accents from characters
	 *
	 * @param string $s string to remove accents from
	 * @return string
	 */
	static function unaccent($s)
	{
		// Only process if there are entities
		if(strpos($s=htmlentities($s,ENT_QUOTES,'UTF-8'),'&')!==false)
		// Remove accent HTML entities
		$s=html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i','$1',$s),ENT_QUOTES,'UTF-8');
		return$s;
	}


	/**
	 * Convert a string to an ASCII/URL safe slug
	 *
	 * @param string $s string to convert
	 * @param string $c character to separate words with
	 * @param string $e extra characters to include
	 * @return
	 */
	static function slug($s,$c='-',$e=null)
	{
		return strtolower(trim(preg_replace('~[^0-9a-z'.preg_quote($e,'~').']+~i',$c,self::unaccent($s)),$c));
	}
}

