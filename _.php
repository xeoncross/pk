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
	 * @param string $string to convert
	 * @param string $encoding current encoding of string (default to UTF-8)
	 * @param string $control TRUE to keep "Other" characters
	 * @return string
	 */
	static function convert($string, $encoding = 0, $control = 1)
	{
		// Try to figureout what the encoding is if posible
		if(function_exists('mb_detect_encoding')) $encoding = mb_detect_encoding($string, 'auto');

		// Convert to valid UTF-8
		if(($string = @iconv( ! $encoding ? 'UTF-8' : $encoding, 'UTF-8//IGNORE', $string)) !== false)
		{
			// Optionally remove "other" characters and windows useless "\r"
			return $control ? preg_replace('~\p{C}+~u', '', $string) : preg_replace(array('~\r\n?~', '~[^\P{C}\t\n]+~u'), array("\n", ''), $string);
		}
	}


	/**
	 * Return an IntlDateFormatter object using the current system locale
	 *
	 * @see IntlDateFormatter
	 * @param string $locale string
	 * @param integer $datetype IntlDateFormatter constant
	 * @param integer $timetype IntlDateFormatter constant
	 * @param string $timezone Time zone ID, default is system default
	 * @return IntlDateFormatter
	 */
	static function date($locale = 0, $datetime = IntlDateFormatter::MEDIUM, $timetype = IntlDateFormatter::SHORT, $timezone = NULL)
	{
		return new IntlDateFormatter($locale ?: setlocale(LC_ALL,0), $datetime, $timetype, $timezone);
	}


	/**
	 * Format the given string using the current system locale
	 * Basically, it's sprintf on i18n steroids.
	 *
	 * @see MessageFormatter
	 * @param string $string to parse
	 * @param array $params to insert
	 * @return string
	 */
	static function format($string, array $params = NULL)
	{
		return msgfmt_format_message(setlocale(LC_ALL,0), $string, $params);
	}


	/**
	 * Normalize the given UTF-8 string
	 *
	 * @see http://stackoverflow.com/a/7934397/99923
	 * @param string $string to normalize
	 * @param int $form to normalize as
	 * @return string
	 */
	static function normalize($string, $form = Normalizer::FORM_D)
	{
		return normalizer_normalize($string, $form);
	}


	/**
	 * Remove accents from characters
	 *
	 * @param string $string to remove accents from
	 * @return string
	 */
	static function unaccent($string)
	{
		// Only process if there are entities
		if(strpos($string = htmlentities($string, ENT_QUOTES, 'UTF-8'), '&') !== false)

		// Remove accent HTML entities
		return html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1', $string), ENT_QUOTES, 'UTF-8');
	}


	/**
	 * Convert a string to an ASCII/URL safe slug
	 *
	 * @param string $string to convert
	 * @param string $character to separate words with
	 * @param string $extra characters to include
	 * @return string
	 */
	static function slug($string, $character = '-', $extra = null)
	{
		return strtolower(trim(preg_replace('~[^0-9a-z' . preg_quote($extra,'~') . ']+~i', $character, self::unaccent($string)), $character));
	}
}

