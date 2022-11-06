<?php
/**
 * @package   AkeebaLoginGuard
 * @copyright Copyright (c)2016-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Prevent direct access
namespace Joomla\Plugin\Multifactorauth\Smsapi\Extension;

defined('_JEXEC') || die;

use Joomla\Component\Users\Administrator\DataShape\CaptiveRenderOptions;
use Joomla\Component\Users\Administrator\DataShape\MethodDescriptor;
use Joomla\Component\Users\Administrator\DataShape\SetupRenderOptions;
use Joomla\Component\Users\Administrator\Table\MfaTable;
use Joomla\CMS\Event\MultiFactor\Callback;
use Joomla\CMS\Event\MultiFactor\Captive;
use Joomla\CMS\Event\MultiFactor\GetMethod;
use Joomla\CMS\Event\MultiFactor\GetSetup;
use Joomla\CMS\Event\MultiFactor\SaveSetup;
use Joomla\CMS\Event\MultiFactor\Validate;
use Exception;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Encrypt\Totp;
use Joomla\CMS\Factory;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Input\Input;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Event\SubscriberInterface;
use Joomla\Plugin\Multifactorauth\Smsapi\Helper\SmsApi as ApiHelper;
use Joomla\Utilities\IpHelper;
use RuntimeException;

/**
 * Akeeba LoginGuard Plugin for Multi-factor Authentication Method "Authentication Code by SMS (SMSAPI.com)"
 *
 * Requires entering a 6-digit code sent to the user through a text message. These codes change automatically every 5
 * minutes.
 */
class Smsapi extends CMSPlugin implements SubscriberInterface
{
	/**
	 * Auto-load the language files
	 *
	 * @var    bool
	 * @since  6.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * The MFA Method name handled by this plugin
	 *
	 * @var    string
	 * @since  1.1.0
	 */
	private $mfaMethodName = 'smsapi';

	/**
	 * Forbid registration of legacy (Joomla 3) event listeners.
	 *
	 * @var    boolean
	 * @since  6.0.0
	 *
	 * @deprecated
	 */
	protected $allowLegacyListeners = false;

	/**
	 * Constructor. Loads the language files as well.
	 *
	 * @param   DispatcherInterface  &$subject  The object to observe
	 * @param   array                 $config   An optional associative array of configuration settings.
	 *                                          Recognized key values include 'name', 'group', 'params', 'language'
	 *                                          (this list is not meant to be comprehensive).
	 */
	public function __construct($subject, array $config = [])
	{
		parent::__construct($subject, $config);

		$this->initialiseApiHelper();
	}

	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 *
	 * @since   6.0.0
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onUserMultifactorGetMethod' => 'onUserMultifactorGetMethod',
			'onUserMultifactorCaptive'   => 'onUserMultifactorCaptive',
			'onUserMultifactorGetSetup'  => 'onUserMultifactorGetSetup',
			'onUserMultifactorSaveSetup' => 'onUserMultifactorSaveSetup',
			'onUserMultifactorValidate'  => 'onUserMultifactorValidate',
			'onUserMultifactorCallback'  => 'onUserMultifactorCallback',
		];
	}

	/**
	 * Gets the identity of this MFA Method
	 *
	 * @param   GetMethod  $event
	 *
	 * @return  void
	 * @since   6.0.0
	 */
	public function onUserMultifactorGetMethod(GetMethod $event): void
	{
		// This plugin is disabled if you haven't configured it yet
		if (!ApiHelper::isConfigured())
		{
			return;
		}

		$event->addResult(
			new MethodDescriptor([
				'name'      => $this->mfaMethodName,
				'display'   => Text::_('PLG_MULTIFACTORAUTH_SMSAPI_LBL_DISPLAYEDAS'),
				'shortinfo' => Text::_('PLG_MULTIFACTORAUTH_SMSAPI_LBL_SHORTINFO'),
				'image'     => 'media/plg_loginguard_smsapi/images/smsapi.svg',
				'help_url'  => $this->params->get('helpurl', 'https://github.com/akeeba/loginguard/wiki/SMSAPI'),
			])
		);
	}

	/**
	 * Returns the information which allows LoginGuard to render the MFA setup page. This is the page which allows the
	 * user to add or modify a MFA Method for their user account. If the record does not correspond to your plugin
	 * return an empty array.
	 *
	 * @param   GetSetup  $event
	 *
	 * @return  void
	 * @since   6.0.0
	 */
	public function onUserMultifactorGetSetup(GetSetup $event): void
	{
		/**
		 * @var   MfaTable $record The #__loginguard_tfa record currently selected by the user.
		 */
		$record = $event['record'];

		$helpURL = $this->params->get('helpurl', 'https://github.com/akeeba/loginguard/wiki/SMSAPI');

		// Make sure we are actually meant to handle this Method
		if ($record->method != $this->mfaMethodName)
		{
			return;
		}

		// Load the options from the record (if any)
		$options        = $this->_decodeRecordOptions($record);
		$key            = $options['key'] ?? '';
		$phone          = $options['phone'] ?? '';
		$isAlreadySetup = !empty($key) && !empty($phone);
		$session        = $this->getApplication()->getSession();

		// If there's a key or phone number in the session use that instead.
		$key   = $session->get('com_loginguard.smsapi.key', $key);
		$phone = $session->get('com_loginguard.smsapi.phone', $phone);

		// Initialize objects
		$totp = new Totp(180, 6, 20);

		// If there's still no key in the options, generate one and save it in the session
		if (empty($key))
		{
			$key = $totp->generateSecret();
			$session->set('com_loginguard.smsapi.key', $key);
		}

		$session->set('com_loginguard.smsapi.user_id', $record->user_id);

		$requestPhone = empty($phone);

		// We have a phone and a key. Send an SMS message with a new code and ask the user to enter it.
		try
		{
			if (!empty($phone) && !$isAlreadySetup)
			{
				$this->sendCode($key, $phone);
			}
		}
		catch (Exception $e)
		{
			$this->getApplication()->enqueueMessage($e->getMessage(), 'error');

			$requestPhone = true;
		}

		// If there is no phone we need to show the phone entry page
		if ($requestPhone)
		{
			$this->getApplication()->getDocument()->getWebAssetManager()->getRegistry()->addExtensionRegistryFile('plg_loginguard_smsapi');
			$layoutPath = PluginHelper::getLayoutPath('loginguard', 'smsapi', 'phone');
			ob_start();
			include $layoutPath;
			$html = ob_get_clean();

			$event->addResult(
				new SetupRenderOptions([
					'default_title' => Text::_('PLG_MULTIFACTORAUTH_SMSAPI_LBL_DISPLAYEDAS'),
					'pre_message'   => Text::_('PLG_MULTIFACTORAUTH_SMSAPI_LBL_SETUP_INSTRUCTIONS'),
					'field_type'    => 'custom',
					'html'          => $html,
					'show_submit'   => false,
					'help_url'      => $helpURL,
				])
			);

			return;
		}

		if (!$isAlreadySetup)
		{
			$event->addResult(
				new SetupRenderOptions([
					'default_title' => Text::_('PLG_MULTIFACTORAUTH_SMSAPI_LBL_DISPLAYEDAS'),
					'hidden_data'   => [
						'key' => $key,
					],
					'field_type'    => 'input',
					'input_type'    => 'number',
					'input_value'   => '',
					'placeholder'   => Text::_('PLG_MULTIFACTORAUTH_SMSAPI_LBL_SETUP_PLACEHOLDER'),
					'label'         => Text::_('PLG_MULTIFACTORAUTH_SMSAPI_LBL_SETUP_LABEL'),
					'help_url'      => $helpURL,
				])
			);
		}
		else
		{
			$layoutPath = PluginHelper::getLayoutPath('loginguard', 'smsapi', 'info');
			ob_start();
			include $layoutPath;
			$html = ob_get_clean();

			$event->addResult(
				new SetupRenderOptions([
					'default_title' => Text::_('PLG_MULTIFACTORAUTH_SMSAPI_LBL_DISPLAYEDAS'),
					'hidden_data'   => [
						'key' => $key,
					],
					'field_type'    => 'custom',
					'html'          => $html,
					'help_url'      => $helpURL,
				])
			);
		}
	}

	/**
	 * Parse the input from the MFA setup page and return the configuration information to be saved to the database. If
	 * the information is invalid throw a RuntimeException to signal the need to display the editor page again. The
	 * message of the exception will be displayed to the user. If the record does not correspond to your plugin return
	 * an empty array.
	 *
	 * @param   SaveSetup  $event
	 *
	 * @return  void
	 * @since   6.0.0
	 */
	public function onUserMultifactorSaveSetup(SaveSetup $event): void
	{
		/**
		 * @var   MfaTable $record The #__loginguard_tfa record currently selected by the user.
		 * @var   Input    $input  The user input you are going to take into account.
		 */
		$record = $event['record'];
		$input  = $event['input'];

		// Make sure we are actually meant to handle this Method
		if ($record->method != $this->mfaMethodName)
		{
			return;
		}

		// Load the options from the record (if any)
		$options        = $this->_decodeRecordOptions($record);
		$key            = $options['key'] ?? '';
		$phone          = $options['phone'] ?? '';
		$isAlreadySetup = !empty($key) && !empty($phone);
		$session        = $this->getApplication()->getSession();

		// If there is no key in the options fetch one from the session
		if (empty($key))
		{
			$key = $session->get('com_loginguard.smsapi.key', null);
		}

		// If there is no key in the options fetch one from the session
		if (empty($phone))
		{
			$phone = $session->get('com_loginguard.smsapi.phone', null);
		}

		// If there is still no key in the options throw an error
		if (empty($key))
		{
			throw new RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		// If there is still no phone in the options throw an error
		if (empty($phone))
		{
			throw new RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		/**
		 * If the code is empty but the key already existed in $options someone is simply changing the title / default
		 * Method status. We can allow this and stop checking anything else now.
		 */
		$code = $input->getInt('code');

		if (empty($code) && $isAlreadySetup)
		{
			$event->addResult($options);

			return;
		}

		// In any other case validate the submitted code
		$totp    = new Totp(180, 6, 20);
		$isValid = $totp->checkCode($key, $code);

		if (!$isValid)
		{
			throw new RuntimeException(Text::_('PLG_MULTIFACTORAUTH_SMSAPI_ERR_INVALID_CODE'), 500);
		}

		// The code is valid. Unset the key from the session.
		$session->set('com_loginguard.totp.key', null);

		// Return the configuration to be serialized
		$event->addResult([
			'key'   => $key,
			'phone' => $phone,
		]);
	}

	/**
	 * Returns the information which allows LoginGuard to render the Captive MFA page. This is the page which appears
	 * right after you log in and asks you to validate your login with MFA.
	 *
	 * @param   Captive  $event
	 *
	 * @return  void
	 * @since   6.0.0
	 */
	public function onUserMultifactorCaptive(Captive $event): void
	{
		/**
		 * @var   MfaTable $record The #__loginguard_tfa record currently selected by the user.
		 */
		$record = $event['record'];

		// Make sure we are actually meant to handle this Method
		if ($record->method != $this->mfaMethodName)
		{
			return;
		}

		// Load the options from the record (if any)
		$options = $this->_decodeRecordOptions($record);
		$key     = $options['key'] ?? '';
		$phone   = $options['phone'] ?? '';

		// Send a push message with a new code and ask the user to enter it.
		try
		{
			$this->sendCode($key, $phone);
		}
		catch (Exception $e)
		{
			return;
		}

		$event->addResult(
			new CaptiveRenderOptions([
				// Custom HTML to display above the MFA form
				'pre_message'  => '',
				// How to render the MFA code field. "input" (HTML input element) or "custom" (custom HTML)
				'field_type'   => 'input',
				// The type attribute for the HTML input box. Typically "text" or "password". Use any HTML5 input type.
				'input_type'   => 'number',
				// Placeholder text for the HTML input box. Leave empty if you don't need it.
				'placeholder'  => Text::_('PLG_MULTIFACTORAUTH_SMSAPI_LBL_SETUP_PLACEHOLDER'),
				// Label to show above the HTML input box. Leave empty if you don't need it.
				'label'        => Text::_('PLG_MULTIFACTORAUTH_SMSAPI_LBL_SETUP_LABEL'),
				// Custom HTML. Only used when field_type = custom.
				'html'         => '',
				// Custom HTML to display below the MFA form
				'post_message' => '',
				// URL for help content
				'help_url'     => $this->params->get('helpurl', 'https://github.com/akeeba/loginguard/wiki/SMSAPI'),
			])
		);
	}

	/**
	 * Validates the Two Factor Authentication code submitted by the user in the Captive Multi-factor Authentication
	 * page. If the record does not correspond to your plugin return FALSE.
	 *
	 * @param   Validate  $event
	 *
	 * @return  void
	 * @since   1.1.0
	 */
	public function onUserMultifactorValidate(Validate $event): void
	{
		/**
		 * @var   MfaTable $record The MFA Method's record you're validatng against
		 * @var   User     $user   The user record
		 * @var   string   $code   The submitted code
		 */
		$record = $event['record'];
		$user   = $event['user'];
		$code   = $event['code'];

		// Make sure we are actually meant to handle this Method
		if ($record->method != $this->mfaMethodName)
		{
			$event->addResult(false);

			return;
		}

		// Double check the MFA Method is for the correct user
		if ($user->id != $record->user_id)
		{
			$event->addResult(false);

			return;
		}

		// Load the options from the record (if any)
		$options = $this->_decodeRecordOptions($record);
		$key     = $options['key'] ?? '';

		// If there is no key in the options throw an error
		if (empty($key))
		{
			$event->addResult(false);

			return;
		}

		// Check the MFA code for validity
		$totp = new Totp(180, 6, 20);

		$event->addResult($totp->checkCode($key, $code));
	}

	/**
	 * Handle the callback.
	 *
	 * When the user enters their phone number they are redirected to this callback. This callback stores the necessary
	 * parameters to the session and redirects the user back to the setup page.
	 *
	 *
	 * @param   Callback  $event
	 *
	 * @return  void
	 *
	 * @since   6.0.0
	 */
	public function onUserMultifactorCallback(Callback $event): void
	{
		/**
		 * @var   string $method The MFA Method used during the callback.
		 */
		$method = $event['method'];

		if ($method != $this->mfaMethodName)
		{
			return;
		}

		$input   = $this->getApplication()->input;
		$session = $this->getApplication()->getSession();

		// Sanity check: am I the same user as the one being edited or a Super User?
		$user    = $this->getApplication()->getIdentity();
		$user_id = $session->get('com_loginguard.smsapi.user_id', null);

		if (!($user instanceof User) || (!empty($user_id) && ($user->id != $user_id) && !$user->authorise('core.admin')))
		{
			return;
		}

		// Do I have a phone variable?
		$phone = $input->getString('phone', null);

		if (empty($phone))
		{
			return;
		}

		$phone = preg_replace("/[^0-9]/", "", $phone);

		// Set the phone to the session
		$session->set('com_loginguard.smsapi.phone', $phone);
		$session->set('com_loginguard.smsapi.user_id', null);

		// Redirect to the editor page
		$userPart    = empty($user_id) ? '' : ('&user_id=' . $user_id);
		$redirectURL = Route::_('index.php?option=com_loginguard&view=method&task=add&method=smsapi' . $userPart, false);

		$this->getApplication()->redirect($redirectURL);
	}

	/**
	 * Set up the API helper with the username/password or OAuth token needed to send SMS.
	 *
	 * @since   6.0.0
	 */
	private function initialiseApiHelper(): void
	{
		$username    = $this->params->get('username', null);
		$password    = $this->params->get('password', null);
		$token       = $this->params->get('token', null);

		if (!empty($username) && !empty($passwordMd5) && !empty($token))
		{
			if ($this->params->get('authMethod') === 'token')
			{
				$username = '';
				$password = '';
			}
			else
			{
				$token = '';
			}
		}

		if (!empty($token))
		{
			ApiHelper::setToken($token);
		}
		else
		{
			ApiHelper::setUsernamePassword($username, $password);
		}
	}

	/**
	 * Creates a new TOTP code based on secret key $key and sends it to the user via SMSAPI to the phone number $token
	 *
	 * @param   string     $key    The TOTP secret key
	 * @param   string     $phone  The phone number with the international prefix
	 * @param   User|null  $user   The Joomla! user to use
	 *
	 * @return  void
	 * @throws  RuntimeException
	 *
	 * @since   1.1.0
	 */
	private function sendCode(string $key, string $phone, User $user = null): void
	{
		static $alreadySent = false;

		if ($alreadySent)
		{
			return;
		}

		// Make sure we have a user
		if (!is_object($user) || !($user instanceof User))
		{
			$user = $this->getApplication()->getIdentity()
				?: Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById(0);
		}

		// Get the API objects
		$totp = new Totp(180, 6, 20);

		// Create the list of variable replacements
		$code = $totp->getCode($key);

		$replacements = [
			'[CODE]'     => $code,
			'[SITENAME]' => $this->getApplication()->get('sitename', 'Joomla! Site'),
			'[SITEURL]'  => Uri::base(),
			'[USERNAME]' => $user->username,
			'[EMAIL]'    => $user->email,
			'[FULLNAME]' => $user->name,
		];

		// Get the title and body of the push message
		$message = Text::_('PLG_MULTIFACTORAUTH_SMSAPI_MESSAGE');
		$message = str_ireplace(array_keys($replacements), array_values($replacements), $message);

		// Should I try to recode the message?
		$transliterate = $this->params->get('transliterate', 0) == 1;

		if ($transliterate)
		{
			$message = ApiHelper::getBestMessageRepresentation($message, true);
		}

		$additional = [];
		$from       = trim($this->params->get('from', '')) ?: '';

		// When $transliterate is true try to send in binary encoding
		if ($transliterate)
		{
			try
			{
				$message                  = ApiHelper::toGsmHex($message);
				$additional['datacoding'] = 'bin';
			}
			catch (Exception $e)
			{
				// Nope, that's a UTF-8 message.
			}
		}

		// Send the text using the default Sender
		ApiHelper::sendMessage($phone, $message, $from, $additional);

		$alreadySent = true;
	}

	/**
	 * Decodes the options from a #__loginguard_tfa record into an options object.
	 *
	 * @param   MfaTable  $record
	 *
	 * @return  array
	 * @since   1.1.0
	 */
	private function _decodeRecordOptions(MfaTable $record): array
	{
		$options = [
			'key'   => '',
			'phone' => '',
		];

		if (!empty($record->options))
		{
			$recordOptions = $record->options;

			$options = array_merge($options, $recordOptions);
		}

		return $options;
	}

	/**
	 * Attempt IP geolocation to get the visitor's country using the free of charge iplocation.net service.
	 *
	 * @param   string  $default  The default country code to return if geolocation fails.
	 *
	 * @return  string  The country 2 letter country ISO code (or your default value, whatever that may be)
	 *
	 * @since   6.0.0
	 */
	private function geolocation(string $default = 'auto'): string
	{
		$ip = IpHelper::getIp();

		if (empty($ip))
		{
			return $default;
		}

		$response = HttpFactory::getHttp()->get(sprintf('https://api.iplocation.net/?ip=%s', $ip));

		if ($response->code != 200)
		{
			return $default;
		}

		try
		{
			$info = @json_decode($response->body);

			if ($info === null)
			{
				throw new RuntimeException('Could not perform IP geolocation');
			}
		}
		catch (Exception $e)
		{
			return $default;
		}

		$country = $info->country_code2 ?? '';

		if (($country === '-') || strlen($country) != 2)
		{
			return $default;
		}

		return $country;
	}
}
