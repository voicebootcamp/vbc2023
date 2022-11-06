<?php
/**
 * @package   AkeebaLoginGuard
 * @copyright Copyright (c)2016-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Joomla\Plugin\Multifactorauth\Smsapi\Helper;

use Exception;
use Joomla\CMS\Http\HttpFactory;
use RuntimeException;

defined('_JEXEC') or die;

/**
 * Send SMS messages with SMSAPI.com.
 *
 * This is a very simplified integration, merely enough to use in our software.
 *
 * @package     Joomla\Plugin\Loginguard\Smsapi\Helper
 *
 * @since       6.0.0
 */
class SmsApi
{
	/**
	 * 7-bit SMS alphabet and the byte count of each character.
	 *
	 * @since  6.0.0
	 */
	private const GSM_ALPHABET = [
		'@'  => 1,
		'£'  => 1,
		'$'  => 1,
		'¥'  => 1,
		'è'  => 1,
		'é'  => 1,
		'ù'  => 1,
		'ì'  => 1,
		'ò'  => 1,
		'Ç'  => 1,
		'\n' => 1,
		'Ø'  => 1,
		'ø'  => 1,
		'\r' => 1,
		'Å'  => 1,
		'å'  => 1,
		'Δ'  => 1,
		'_'  => 1,
		'Φ'  => 1,
		'Γ'  => 1,
		'Λ'  => 1,
		'Ω'  => 1,
		'Π'  => 1,
		'Ψ'  => 1,
		'Σ'  => 1,
		'Θ'  => 1,
		'Ξ'  => 1,
		''   => 2,
		'^'  => 2,
		'{'  => 2,
		'}'  => 2,
		'\\' => 2,
		'['  => 2,
		'~'  => 2,
		']'  => 2,
		'|'  => 2,
		'€'  => 2,
		'Æ'  => 1,
		'æ'  => 1,
		'ß'  => 1,
		'É'  => 1,
		' '  => 1,
		'!'  => 1,
		'"'  => 1,
		'#'  => 1,
		'¤'  => 1,
		'%'  => 1,
		'&'  => 1,
		'\'' => 1,
		'('  => 1,
		')'  => 1,
		'*'  => 1,
		'+'  => 1,
		','  => 1,
		'-'  => 1,
		'.'  => 1,
		'/'  => 1,
		'0'  => 1,
		'1'  => 1,
		'2'  => 1,
		'3'  => 1,
		'4'  => 1,
		'5'  => 1,
		'6'  => 1,
		'7'  => 1,
		'8'  => 1,
		'9'  => 1,
		':'  => 1,
		';'  => 1,
		'<'  => 1,
		'='  => 1,
		'>'  => 1,
		'?'  => 1,
		'¡'  => 1,
		'A'  => 1,
		'B'  => 1,
		'C'  => 1,
		'D'  => 1,
		'E'  => 1,
		'F'  => 1,
		'G'  => 1,
		'H'  => 1,
		'I'  => 1,
		'J'  => 1,
		'K'  => 1,
		'L'  => 1,
		'M'  => 1,
		'N'  => 1,
		'O'  => 1,
		'P'  => 1,
		'Q'  => 1,
		'R'  => 1,
		'S'  => 1,
		'T'  => 1,
		'U'  => 1,
		'V'  => 1,
		'W'  => 1,
		'X'  => 1,
		'Y'  => 1,
		'Z'  => 1,
		'Ä'  => 1,
		'Ö'  => 1,
		'Ñ'  => 1,
		'Ü'  => 1,
		'§'  => 1,
		'¿'  => 1,
		'a'  => 1,
		'b'  => 1,
		'c'  => 1,
		'd'  => 1,
		'e'  => 1,
		'f'  => 1,
		'g'  => 1,
		'h'  => 1,
		'i'  => 1,
		'j'  => 1,
		'k'  => 1,
		'l'  => 1,
		'm'  => 1,
		'n'  => 1,
		'o'  => 1,
		'p'  => 1,
		'q'  => 1,
		'r'  => 1,
		's'  => 1,
		't'  => 1,
		'u'  => 1,
		'v'  => 1,
		'w'  => 1,
		'x'  => 1,
		'y'  => 1,
		'z'  => 1,
		'ä'  => 1,
		'ö'  => 1,
		'ñ'  => 1,
		'ü'  => 1,
		'à'  => 1,
	];

	/**
	 * Hex representation of each GSM character
	 *
	 * @since  6.0.0
	 */
	private const GSM_TO_HEX = [
		'@'    => '00',
		'£'    => '01',
		'$'    => '02',
		'¥'    => '03',
		'è'    => '04',
		'é'    => '05',
		'ù'    => '06',
		'ì'    => '07',
		'ò'    => '08',
		'Ç'    => '09',
		'\n'   => '0A',
		'Ø'    => '0B',
		'ø'    => '0C',
		'\r'   => '0D',
		'Å'    => '0E',
		'å'    => '0F',
		'Δ'    => '10',
		'_'    => '11',
		'Φ'    => '12',
		'Γ'    => '13',
		'Λ'    => '14',
		'Ω'    => '15',
		'Π'    => '16',
		'Ψ'    => '17',
		'Σ'    => '18',
		'Θ'    => '19',
		'Ξ'    => '1A',
		'	' => '1B0A',
		'^'    => '1B14',
		'{'    => '1B28',
		'}'    => '1B29',
		'\\'   => '1B2F',
		'['    => '1B3C',
		'~'    => '1B3D',
		']'    => '1B3E',
		'|'    => '1B40',
		'€'    => '1B65',
		'Æ'    => '1C',
		'æ'    => '1D',
		'ß'    => '1E',
		'É'    => '1F',
		' '    => '20',
		'!'    => '21',
		'"'    => '22',
		'#'    => '23',
		'¤'    => '24',
		'%'    => '25',
		'&'    => '26',
		'\''   => '27',
		'('    => '28',
		')'    => '29',
		'*'    => '2A',
		'+'    => '2B',
		','    => '2C',
		'-'    => '2D',
		'.'    => '2E',
		'/'    => '2F',
		'0'    => '30',
		'1'    => '31',
		'2'    => '32',
		'3'    => '33',
		'4'    => '34',
		'5'    => '35',
		'6'    => '36',
		'7'    => '37',
		'8'    => '38',
		'9'    => '39',
		':'    => '3A',
		';'    => '3B',
		'<'    => '3C',
		'='    => '3D',
		'>'    => '3E',
		'?'    => '3F',
		'¡'    => '40',
		'A'    => '41',
		'B'    => '42',
		'C'    => '43',
		'D'    => '44',
		'E'    => '45',
		'F'    => '46',
		'G'    => '47',
		'H'    => '48',
		'I'    => '49',
		'J'    => '4A',
		'K'    => '4B',
		'L'    => '4C',
		'M'    => '4D',
		'N'    => '4E',
		'O'    => '4F',
		'P'    => '50',
		'Q'    => '51',
		'R'    => '52',
		'S'    => '53',
		'T'    => '54',
		'U'    => '55',
		'V'    => '56',
		'W'    => '57',
		'X'    => '58',
		'Y'    => '59',
		'Z'    => '5A',
		'Ä'    => '5B',
		'Ö'    => '5C',
		'Ñ'    => '5D',
		'Ü'    => '5E',
		'§'    => '5F',
		'¿'    => '60',
		'a'    => '61',
		'b'    => '62',
		'c'    => '63',
		'd'    => '64',
		'e'    => '65',
		'f'    => '66',
		'g'    => '67',
		'h'    => '68',
		'i'    => '69',
		'j'    => '6A',
		'k'    => '6B',
		'l'    => '6C',
		'm'    => '6D',
		'n'    => '6E',
		'o'    => '6F',
		'p'    => '70',
		'q'    => '71',
		'r'    => '72',
		's'    => '73',
		't'    => '74',
		'u'    => '75',
		'v'    => '76',
		'w'    => '77',
		'x'    => '78',
		'y'    => '79',
		'z'    => '7A',
		'ä'    => '7B',
		'ö'    => '7C',
		'ñ'    => '7D',
		'ü'    => '7E',
		'à'    => '7F',
	];

	/**
	 * GSM alphabet homoglyphs.
	 *
	 * This transliterates uppercase Greek letters to their Latin-1 homoglyphs. Accented letters and letters with
	 * diacritics are converted to homoglyphs WITHOUT accents and diacritics as per the transliteration convention
	 * commonly employed in Greece. That is to say both ΆΛΛΑ and ΑΛΛΆ become AΛΛA when transliterated to the GSM
	 * alphabet. Greek users figure it out based on context
	 *
	 * @since  6.0.0
	 */
	private const GSM_HOMOGLYPHS = [
		'Α' => 'A',
		'Ά' => 'A',
		'Β' => 'B',
		'Ε' => 'E',
		'Έ' => 'E',
		'Ζ' => 'Z',
		'Η' => 'H',
		'Ή' => 'H',
		'Ι' => 'I',
		'Ί' => 'I',
		'Ϊ' => 'I',
		'Κ' => 'K',
		'Μ' => 'M',
		'Ν' => 'N',
		'Ο' => 'O',
		'Ό' => 'O',
		'Ρ' => 'P',
		'Τ' => 'T',
		'Υ' => 'Y',
		'Ύ' => 'Y',
		'Ϋ' => 'Y',
		'Χ' => 'X',
	];

	/**
	 * GSM message limits based on whether it's in the GSM alphabet and the number of parts.
	 *
	 * IMPORTANT: You cannot send more than 1530 characters in the GSM alphabet or more than 670 characters using UTF-8.
	 * There is a hard limit of 10 message parts in text messages.
	 *
	 * @since  6.0.0
	 */
	private const GSM_MESSAGE_LIMITS = [
		true  => [
			1  => 160,
			2  => 306,
			3  => 459,
			4  => 612,
			5  => 765,
			6  => 918,
			7  => 1071,
			8  => 1224,
			9  => 1337,
			10 => 1530,
		],
		false => [
			1  => 70,
			2  => 134,
			3  => 201,
			4  => 268,
			5  => 335,
			6  => 402,
			7  => 469,
			8  => 536,
			9  => 603,
			10 => 670,
		],
	];

	/**
	 * Valid international phone number country prefixes
	 *
	 * @since 6.0.0
	 * @see https://www.globalcallforwarding.com/international-call-prefixes/
	 */
	private const VALID_COUNTRY_PREFIXES = [
		'1',
		'20',
		'212',
		'213',
		'216',
		'218',
		'220',
		'221',
		'222',
		'223',
		'224',
		'225',
		'226',
		'227',
		'228',
		'229',
		'230',
		'231',
		'232',
		'233',
		'234',
		'235',
		'236',
		'237',
		'238',
		'239',
		'240',
		'241',
		'242',
		'243',
		'244',
		'245',
		'246',
		'247',
		'248',
		'249',
		'250',
		'251',
		'252',
		'253',
		'254',
		'255',
		'256',
		'257',
		'258',
		'260',
		'261',
		'262',
		'263',
		'264',
		'265',
		'266',
		'267',
		'268',
		'269',
		'269',
		'27',
		'290',
		'290',
		'291',
		'297',
		'298',
		'299',
		'30',
		'31',
		'32',
		'33',
		'34',
		'350',
		'351',
		'352',
		'353',
		'354',
		'355',
		'356',
		'357',
		'358',
		'359',
		'36',
		'370',
		'371',
		'372',
		'373',
		'374',
		'375',
		'376',
		'377',
		'378',
		'379',
		'380',
		'381',
		'382',
		'385',
		'386',
		'387',
		'388',
		'389',
		'39',
		'40',
		'41',
		'420',
		'421',
		'423',
		'43',
		'44',
		'45',
		'46',
		'47',
		'48',
		'49',
		'500',
		'501',
		'502',
		'503',
		'504',
		'505',
		'506',
		'507',
		'508',
		'509',
		'51',
		'52',
		'53',
		'54',
		'55',
		'56',
		'57',
		'58',
		'590',
		'591',
		'592',
		'593',
		'594',
		'595',
		'596',
		'597',
		'598',
		'599',
		'60',
		'61',
		'62',
		'63',
		'64',
		'65',
		'66',
		'670',
		'672',
		'673',
		'674',
		'675',
		'676',
		'677',
		'678',
		'679',
		'680',
		'681',
		'682',
		'683',
		'685',
		'686',
		'687',
		'688',
		'689',
		'690',
		'691',
		'692',
		'7',
		'800',
		'808',
		'81',
		'82',
		'84',
		'850',
		'852',
		'853',
		'855',
		'856',
		'86',
		'870',
		'878',
		'880',
		'881',
		'882',
		'883',
		'886',
		'888',
		'90',
		'91',
		'92',
		'93',
		'94',
		'95',
		'960',
		'961',
		'962',
		'963',
		'964',
		'965',
		'966',
		'967',
		'968',
		'970',
		'971',
		'972',
		'973',
		'974',
		'975',
		'976',
		'977',
		'979',
		'98',
		'991',
		'992',
		'993',
		'994',
		'995',
		'996',
		'998',
	];

	/**
	 * The SMSAPI.com API token (OAuth)
	 *
	 * @var    string
	 * @since  6.0.0
	 */
	private static $token = '';

	/**
	 * The SMSAPI.com username
	 *
	 * @var    string
	 * @since      6.0.0
	 * @deprecated 7.0
	 */
	private static $username = '';

	/**
	 * The SMSAPI.com API password (MD5)
	 *
	 * @var    string
	 * @since      6.0.0
	 * @deprecated 7.0
	 */
	private static $passwordMd5 = '';

	/**
	 * Sets the authentication token.
	 *
	 * You must call this before sendMessage().
	 *
	 * @param   string  $token  The SMSAPI.com authentication token.
	 *
	 * @since   6.0.0
	 */
	public static function setToken(string $token): void
	{
		self::$token       = $token;
		self::$username    = '';
		self::$passwordMd5 = '';
	}

	/**
	 * @param   string|null  $username
	 * @param   string|null  $password
	 *
	 * @since      6.0.0
	 * @deprecated 7.0
	 */
	public static function setUsernamePassword(?string $username, ?string $password): void
	{
		self::$token       = '';
		self::$username    = $username ?? '';
		self::$passwordMd5 = $password ?? '';
	}

	/**
	 * Is this helper properly configured to send SMS messages?
	 *
	 * @return  bool
	 *
	 * @since   6.0.0
	 */
	public static function isConfigured(): bool
	{
		return (!empty(self::$token) || (!empty(self::$username) && !empty(self::$passwordMd5)));
	}

	/**
	 * Send a text message to a mobile phone
	 *
	 * @param   string       $phoneNumber     The phone number in international format, WITHOUT the + sign in front
	 * @param   string       $message         The message to send
	 * @param   string|null  $from            Sender name. Must be pre-authorised in SMSAPI.com. Empty to use default.
	 * @param   array        $overrideParams  Override any parameters to the API.
	 *
	 * @throws  Exception
	 * @see     https://www.smsapi.com/docs/?php--curl#2-single-sms
	 *
	 * @since   6.0.0
	 */
	public static function sendMessage(string $phoneNumber, string $message, ?string $from = null, array $overrideParams = []): void
	{
		$params = [
			'to'      => $phoneNumber,
			'message' => $message,
		];

		if (!empty($from))
		{
			$params['from'] = $from;
		}

		$params = array_merge($params, $overrideParams);

		self::sendTextWithApi($params);
	}

	/**
	 * Get the best message representation.
	 *
	 * This method tries to figure out if transliteration to GSM is possible and selects it instead of the UTF-8 message
	 * if it will result in fewer message parts, or if $preferTransliterated is true.
	 *
	 * This is STRONGLY recommended when sending messages in Greek.
	 *
	 * For all other languages it's as good as not using it at all, since they belong to one of two groups:
	 *
	 * 1. Central- and Northern-European languages covered entirely by the GSM alphabet such as German, French, Spanish,
	 *    Finnish, etc. The UTF-8 and GSM messages are identical.
	 * 2. Every language using glyphs not covered by the GSM alphabet such as Czech, Bulgarian, Chinese etc. There is no
	 *    GSM representation of these messages therefore UTF-8 will always be selected.
	 *
	 * @param   string  $message
	 * @param   bool    $preferTransliterated
	 *
	 * @return  string
	 *
	 * @since   version
	 */
	public static function getBestMessageRepresentation(string $message, bool $preferTransliterated = false): string
	{
		try
		{
			$transliterated = self::transliterate($message);
		}
		catch (Exception $e)
		{
			return $message;
		}

		$inUtf = self::countCharacters($message);
		$inGsm = self::countCharacters($message, true);

		if (!$inGsm['valid'])
		{
			return $message;
		}

		if ($preferTransliterated || ($inGsm['parts'] < $inUtf['parts']))
		{
			return $transliterated;
		}

		return $message;
	}

	/**
	 * Get information about the number and type of characters in the message
	 *
	 * @param   string  $message        The message to send
	 * @param   bool    $transliterate  Should I try to transliterate to the GSM character set if possible?
	 *
	 * @return  array{utf8:bool, characters:int, parts:int, valid:bool}
	 *
	 * @since   6.0.0
	 */
	public static function countCharacters(string $message, bool $transliterate = false): array
	{
		if ($transliterate)
		{
			try
			{
				$message = self::transliterate($message);
			}
			catch (RuntimeException $e)
			{

			}
		}

		$ret = [
			'utf8'       => true,
			'characters' => function_exists('mb_strlen') ? mb_strlen($message, 'UTF-8') : strlen($message),
			'parts'      => 0,
			'valid'      => true,
		];

		if (self::isGSMAlphabet($message))
		{
			$limits            = self::GSM_MESSAGE_LIMITS[true];
			$ret['utf8']       = false;
			$ret['characters'] = self::countGSMCharacters($message);
		}
		else
		{
			$limits = self::GSM_MESSAGE_LIMITS[false];
		}

		foreach ($limits as $parts => $maxCharacters)
		{
			if ($ret['characters'] > $maxCharacters)
			{
				continue;
			}

			$ret['parts'] = $parts;

			return $ret;
		}

		$ret['valid'] = false;

		return $ret;
	}

	/**
	 * Convert a GSM-alphabet message to GSM hex representation
	 *
	 * @param   string  $message  The message to transcode
	 *
	 * @return  string  The transcoded message
	 *
	 * @since   6.0.0
	 */
	public static function toGsmHex(string $message): string
	{
		if (function_exists('mb_strlen') && function_exists('mb_substr'))
		{
			$chars = [];

			$strlen = mb_strlen($message, 'UTF-8');

			while ($strlen)
			{
				$chars[] = mb_substr($message, 0, 1, 'UTF-8');
				$message = mb_substr($message, 1, $strlen, 'UTF-8');
				$strlen  = mb_strlen($message, 'UTF-8');
			}
		}
		else
		{
			$chars = explode('', $message);
		}

		return implode(
			'', array_map(function (string $x) {
				if (!isset(self::GSM_TO_HEX[$x]))
				{
					throw new RuntimeException('This is not a GSM-coded message.');
				}

				return self::GSM_TO_HEX[$x];
			}, $chars)
		);
	}

	/**
	 * Split a phone number to an international prefix and number
	 *
	 * @param   string  $phone
	 *
	 * @return  array{prefix:string,number:string}
	 *
	 * @since   6.0.0
	 */
	public static function splitPhoneNumber(string $phone): array
	{
		$phone = ltrim(trim($phone), '+ ');

		foreach (self::VALID_COUNTRY_PREFIXES as $prefix)
		{
			$prefixLen = strlen($prefix);

			if (substr($phone, 0, $prefixLen) === $prefix)
			{
				return [
					'prefix' => $prefix,
					'number' => substr($phone, $prefixLen),
				];
			}
		}

		return [
			'prefix' => '',
			'number' => $phone,
		];
	}

	/**
	 * Partially redact a phone number, as long as it's at least three digits long.
	 *
	 * @param   string  $number  The number to redact
	 * @param   string  $symbol  The redaction symbol, defaults to a bullet.
	 *
	 * @return  string
	 *
	 * @since   6.0.0
	 */
	public static function redactNumber(string $number, string $symbol = '•'): string
	{
		$numLen = strlen($number);

		if ($numLen < 3)
		{
			return $number;
		}
		elseif ($numLen < 5)
		{
			return substr($number, 0, 1) . str_repeat($symbol, $numLen - 2) . substr($number, -1);
		}
		else
		{
			return substr($number, 0, 2) . str_repeat($symbol, $numLen - 4) . substr($number, -2);
		}
	}

	/**
	 * Transliterate a message to the GSM character set if possible
	 *
	 * @param   string  $message  The message to convert
	 *
	 * @return  string  The converted message
	 * @throws  RuntimeException  When the conversion is not possible (unsupported characters).
	 *
	 * @since   6.0.0
	 */
	private static function transliterate(string $message): string
	{
		// Is it already in GSM alphabet?
		if (self::isGSMAlphabet($message))
		{
			return $message;
		}

		// Do I just need to do homoglyph replacement?
		if (self::isGSMAlphabet($message, true))
		{
			return self::convertHomoglyphs($message);
		}

		/**
		 * In many cases if I convert the message to uppercase and convert homoglyphs I will get a message in the GSM
		 * alphabet, e.g. when I have Greek text.
		 */
		$temp = self::conditionalStrToUpper($message);
		$temp = self::convertHomoglyphs($temp);

		if (self::isGSMAlphabet($temp))
		{
			return $temp;
		}

		// All else failed. Throw an error.
		throw new RuntimeException('Cannot convert message to GSM.');
	}

	/**
	 * Counts the number of GSM characters (bytes) in a message encoded in the GSM character set.
	 *
	 * Note that some characters in the GSM alphabet take up two bytes (escape plus the character code). The count is
	 * implemented by replacing the GSM characters with spaces (one or two, depending on the character) and then
	 * counting the length of the string that consists solely of spaces.
	 *
	 * @param   string  $message  The message to count
	 *
	 * @return  int  The number of GSM characters (bytes).
	 *
	 * @since   6.0.0
	 */
	private static function countGSMCharacters(string $message): int
	{
		$temp = array_map(function (int $x) {
			return str_repeat(' ', $x);
		}, self::GSM_ALPHABET);

		return strlen(str_replace(array_keys($temp), array_values($temp), $message));
	}

	/**
	 * Convert homoglyphs to their canonical GSM character set.
	 *
	 * This essentially replaces uppercase Greek characters with their Latin-1 homoglyphs, e.g. capital omikron (Ο) with
	 * capital o (O).
	 *
	 * @param   string  $message  The original message
	 *
	 * @return  string
	 *
	 * @since   6.0.0
	 */
	private static function convertHomoglyphs(string $message): string
	{
		return str_replace(array_keys(self::GSM_HOMOGLYPHS), array_values(self::GSM_HOMOGLYPHS), $message);
	}

	/**
	 * Is the message written in the GSM alphabet?
	 *
	 * @param   string  $message         The message to test
	 * @param   bool    $withHomoglyphs  Should I consider homoglyphs as acceptable for the purpose of this test?
	 *
	 * @return  bool
	 *
	 * @since   6.0.0
	 */
	private static function isGSMAlphabet(string $message, bool $withHomoglyphs = false): bool
	{
		$allowedGlyphs = array_keys(self::GSM_ALPHABET);

		if ($withHomoglyphs)
		{
			$allowedGlyphs = $allowedGlyphs + array_keys(self::GSM_HOMOGLYPHS);
		}

		$temp = str_replace($allowedGlyphs, '', $message);

		return empty($temp);
	}

	/**
	 * Internal implementation of the SMSAPI.com API call.
	 *
	 * @param   array  $params  The API parameters to send
	 * @param   bool   $backup  Use the backup server?
	 *
	 * @since   6.0.0
	 * @internal
	 */
	private static function sendTextWithApi(array $params, bool $backup = false): void
	{
		if (!function_exists('curl_init'))
		{
			throw new RuntimeException('This server does not support sending SMS messages.');
		}

		if (empty(self::$token) && (empty(self::$username) || empty(self::$passwordMd5)))
		{
			throw new RuntimeException('You must set up the SMS service authentication before you can send SMS messages.');
		}

		$headers = [];

		if (empty(self::$token))
		{
			$params['username'] = self::$username;
			$params['password'] = self::$passwordMd5;
		}
		else
		{
			$headers = [
				'Authorization' => sprintf('Bearer %s', self::$token),
			];
		}

		$params['format'] = 'json';

		$url = $backup ? 'https://api2.smsapi.com/sms.do' : 'https://api.smsapi.com/sms.do';

		$http     = HttpFactory::getHttp();
		//var_dump($url, $params, $headers);die;
		$response = $http->post($url, $params, $headers);

		if ($response->getStatusCode() !== 200)
		{
			self::sendTextWithApi($params, $backup);

			return;
		}

		try
		{
			$flags       = defined('JSON_THROW_ON_ERROR')
				? (JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR)
				: JSON_INVALID_UTF8_SUBSTITUTE;
			$apiResponse = @json_decode($response->body, true, 512, $flags);

			if ($apiResponse === null)
			{
				throw new RuntimeException('Invalid response from the SMS service provider.');
			}
		}
		catch (Exception $e)
		{
			throw new RuntimeException('Invalid response from the SMS service provider.');
		}

		if (isset($apiResponse['error']))
		{
			$message = sprintf('SMS service provider error: %s', $apiResponse['message'] ?? sprintf('Unknown error #%d.', $apiResponse['error']));

			throw new RuntimeException($message);
		}

		if (!isset($apiResponse['count']))
		{
			throw new RuntimeException('Cannot understand the response from the SMS service provider.');
		}

		if ($apiResponse['count'] < 1)
		{
			throw new RuntimeException('The SMS service provider failed to send the text message.');
		}
	}

	/**
	 * Uppercase the characters of a string which are not valid GSM alphabet characters.
	 *
	 * @param   string  $message  The original message, encoded in UTF-8
	 *
	 * @return  string  The conditionally uppercased message
	 *
	 * @since   6.0.0
	 */
	private static function conditionalStrToUpper(string $message): string
	{
		if (function_exists('mb_strlen') && function_exists('mb_substr'))
		{
			$chars = [];

			$strlen = mb_strlen($message, 'UTF-8');

			while ($strlen)
			{
				$chars[] = mb_substr($message, 0, 1, 'UTF-8');
				$message = mb_substr($message, 1, $strlen, 'UTF-8');
				$strlen  = mb_strlen($message, 'UTF-8');
			}
		}
		else
		{
			$chars = explode('', $message);
		}

		$allowed = array_keys(self::GSM_ALPHABET);
		$chars   = array_map(function (string $c) use ($allowed) {
			return in_array($c, $allowed, true)
				? $c
				: (function_exists('mb_strtoupper') ? mb_strtoupper($c, 'UTF-8') : strtoupper($c));
		}, $chars);

		return implode($chars);
	}
}