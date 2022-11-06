<?php
/**
 * @package   AkeebaLoginGuard
 * @copyright Copyright (c)2016-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Joomla\Plugin\Multifactorauth\Pushbullet\Extension;

// Prevent direct access
defined('_JEXEC') || die;

use Exception;
use Joomla\CMS\Encrypt\Totp;
use Joomla\CMS\Event\MultiFactor\Callback;
use Joomla\CMS\Event\MultiFactor\Captive;
use Joomla\CMS\Event\MultiFactor\GetMethod;
use Joomla\CMS\Event\MultiFactor\GetSetup;
use Joomla\CMS\Event\MultiFactor\SaveSetup;
use Joomla\CMS\Event\MultiFactor\Validate;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Component\Users\Administrator\DataShape\CaptiveRenderOptions;
use Joomla\Component\Users\Administrator\DataShape\MethodDescriptor;
use Joomla\Component\Users\Administrator\DataShape\SetupRenderOptions;
use Joomla\Component\Users\Administrator\Table\MfaTable;
use Joomla\Event\DispatcherInterface;
use Joomla\Event\SubscriberInterface;
use Joomla\Input\Input;
use Joomla\Plugin\Multifactorauth\Pushbullet\Library\Api;
use Joomla\Plugin\Multifactorauth\Pushbullet\Library\PushbulletException;
use RuntimeException;

/**
 * Akeeba LoginGuard Plugin for Multi-factor Authentication "Authentication Code by PushBullet"
 *
 * Requires entering a 6-digit code sent to the user through PushBullet. These codes change automatically every 30
 * seconds.
 */
class Pushbullet extends CMSPlugin implements SubscriberInterface
{
	/**
	 * The PushBullet access token for the PushBullet account which owns the PushBullet OAuth Client defined by the
	 * clientId and secret below.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $accessToken;

	/**
	 * PushBullet OAuth2 Client ID
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $clientId;

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
	 * Autoload this plugin's language files
	 *
	 * @var    boolean
	 * @since  6.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * PushBullet OAuth2 Secret ID
	 *
	 * @var   string
	 * @since  1.0.0
	 */
	private $secret;

	/**
	 * The MFA Method name handled by this plugin
	 *
	 * @var   string
	 * @since  1.0.0
	 */
	private $mfaMethodName = 'pushbullet';

	/**
	 * Constructor. Loads the language files as well.
	 *
	 * @param   DispatcherInterface  &$subject   The object to observe
	 * @param   array                 $config    An optional associative array of configuration settings.
	 *                                           Recognized key values include 'name', 'group', 'params', 'language'
	 *                                           (this list is not meant to be comprehensive).
	 */
	public function __construct($subject, array $config = [])
	{
		parent::__construct($subject, $config);

		// Load the PushBullet API parameters
		$this->accessToken = $this->params->get('access_token', null);
		$this->clientId    = $this->params->get('client_id', null);
		$this->secret      = $this->params->get('secret', null);
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
	 * @param   GetMethod  $event  The event we are handling
	 *
	 * @return  void
	 */
	public function onUserMultifactorGetMethod(GetMethod $event): void
	{
		// This plugin is disabled if you haven't configured it yet
		if (empty($this->accessToken) || empty($this->clientId) || empty($this->secret))
		{
			return;
		}

		$event->addResult(
			new MethodDescriptor([
				'name'               => $this->mfaMethodName,
				'display'            => Text::_('PLG_MULTIFACTORAUTH_PUSHBULLET_LBL_DISPLAYEDAS'),
				'shortinfo'          => Text::_('PLG_MULTIFACTORAUTH_PUSHBULLET_LBL_SHORTINFO'),
				'image'              => 'media/plg_loginguard_pushbullet/images/pushbullet.png',
				'allowEntryBatching' => false,
				'help_url'           => $this->params->get('helpurl', 'https://github.com/akeeba/loginguard/wiki/Pushbullet'),
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
	 * @throws  PushbulletException
	 */
	public function onUserMultifactorGetSetup(GetSetup $event): void
	{
		/** @var MfaTable $record The #__loginguard_tfa record currently selected by the user. */
		$record = $event['record'];

		$helpURL = $this->params->get('helpurl', 'https://github.com/akeeba/loginguard/wiki/Pushbullet');

		// Make sure we are actually meant to handle this Method
		if ($record->method != $this->mfaMethodName)
		{
			return;
		}

		// Load the options from the record (if any)
		$options      = $this->decodeRecordOptions($record);
		$key          = $options['key'] ?? '';
		$token        = $options['token'] ?? '';
		$isConfigured = !empty($key) && !empty($token);

		// If there's a key or token in the session use that instead.
		$session = $this->getApplication()->getSession();
		$key     = $session->get('com_users_mfa.pushbullet.key', $key);
		$token   = $session->get('com_users_mfa.pushbullet.token', $token);

		// Initialize objects
		$totp = new Totp(30, 6, 20);

		// If there's still no key in the options, generate one and save it in the session
		if (empty($key))
		{
			$key = $totp->generateSecret();
			$session->set('com_users_mfa.pushbullet.key', $key);
		}

		$session->set('com_users_mfa.pushbullet.user_id', $record->user_id);

		// If there is no token we need to show the OAuth2 button
		if (empty($token))
		{
			$layoutPath = PluginHelper::getLayoutPath('loginguard', 'pushbullet', 'oauth2');
			ob_start();
			include $layoutPath;
			$html = ob_get_clean();

			$event->addResult(
				new SetupRenderOptions([
					'default_title' => Text::_('PLG_MULTIFACTORAUTH_PUSHBULLET_LBL_DISPLAYEDAS'),
					'pre_message'   => Text::_('PLG_MULTIFACTORAUTH_PUSHBULLET_LBL_SETUP_INSTRUCTIONS'),
					'field_type'    => 'custom',
					'html'          => $html,
					'show_submit'   => false,
					'help_url'      => $helpURL,
				])
			);

			return;
		}

		// We have a token and a key. Send a push message with a new code and ask the user to enter it.
		if (!$isConfigured)
		{
			$this->sendCode($key, $token);

			$event->addResult(
				new SetupRenderOptions([
					'default_title' => Text::_('PLG_MULTIFACTORAUTH_PUSHBULLET_LBL_DISPLAYEDAS'),
					'hidden_data'   => [
						'key' => $key,
					],
					'field_type'    => 'input',
					'input_type'    => 'number',
					'input_value'   => '',
					'placeholder'   => Text::_('PLG_MULTIFACTORAUTH_PUSHBULLET_LBL_SETUP_PLACEHOLDER'),
					'label'         => Text::_('PLG_MULTIFACTORAUTH_PUSHBULLET_LBL_SETUP_LABEL'),
					'help_url'      => $helpURL,
				])
			);
		}
		else
		{
			$event->addResult(
				new SetupRenderOptions([
					'default_title' => Text::_('PLG_MULTIFACTORAUTH_PUSHBULLET_LBL_DISPLAYEDAS'),
					'field_type'    => 'custom',
					'html'          => '',
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
	 */
	public function onUserMultifactorSaveSetup(SaveSetup $event): void
	{
		/**
		 * @var MfaTable $record The #__loginguard_tfa record currently selected by the user.
		 * @var Input    $input  The user input you are going to take into account.
		 */
		$record = $event['record'];
		$input  = $event['input'];

		// Make sure we are actually meant to handle this Method
		if ($record->method != $this->mfaMethodName)
		{
			return;
		}

		// Load the options from the record (if any)
		$options      = $this->decodeRecordOptions($record);
		$key          = $options['key'] ?? '';
		$token        = $options['token'] ?? '';
		$isConfigured = !empty($key) && !empty($token);

		$session = $this->getApplication()->getSession();

		// If there is no key in the options fetch one from the session
		if (empty($key))
		{
			$key = $session->get('com_users_mfa.pushbullet.key', null);
		}

		// If there is no key in the options fetch one from the session
		if (empty($token))
		{
			$token = $session->get('com_users_mfa.pushbullet.token', null);
		}

		// If there is still no key in the options throw an error
		if (empty($key))
		{
			throw new RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		// If there is still no token in the options throw an error
		if (empty($token))
		{
			throw new RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		/**
		 * If the code is empty but the key already existed in $options someone is simply changing the title / default
		 * Method status. We can allow this and stop checking anything else now.
		 */
		$code = $input->getCmd('code');

		if (empty($code) && $isConfigured)
		{
			$event->addResult($options);

			return;
		}

		// In any other case validate the submitted code
		$totp    = new Totp();
		$isValid = $totp->checkCode((string) $key, (string) $code);

		if (!$isValid)
		{
			throw new RuntimeException(Text::_('PLG_MULTIFACTORAUTH_PUSHBULLET_ERR_INVALID_CODE'), 500);
		}

		// The code is valid. Unset the key from the session.
		$session->set('com_users_mfa.totp.key', null);

		// Return the configuration to be serialized
		$event->addResult([
			'key'   => $key,
			'token' => $token,
		]);
	}

	/**
	 * Returns the information which allows LoginGuard to render the Captive MFA page. This is the page which appears
	 * right after you log in and asks you to validate your login with MFA.
	 *
	 * @param   Captive  $event  The event we are handling
	 *
	 * @return  void
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
		$options = $this->decodeRecordOptions($record);
		$key     = $options['key'] ?? '';
		$token   = $options['token'] ?? '';
		// Send a push message with a new code and ask the user to enter it.
		try
		{
			$this->sendCode($key, $token);
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
				'placeholder'  => Text::_('PLG_MULTIFACTORAUTH_PUSHBULLET_LBL_SETUP_PLACEHOLDER'),
				// Label to show above the HTML input box. Leave empty if you don't need it.
				'label'        => Text::_('PLG_MULTIFACTORAUTH_PUSHBULLET_LBL_SETUP_LABEL'),
				// Custom HTML. Only used when field_type = custom.
				'html'         => '',
				// Custom HTML to display below the MFA form
				'post_message' => '',
				// URL for help content
				'help_url'     => $this->params->get('helpurl', 'https://github.com/akeeba/loginguard/wiki/Pushbullet'),
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
	 */
	public function onUserMultifactorValidate(Validate $event): void
	{
		/**
		 * @var   MfaTable    $record The MFA Method's record you're validating against
		 * @var   User        $user   The user record
		 * @var   string|null $code   The submitted code
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
		$options = $this->decodeRecordOptions($record);
		$key     = $options['key'] ?? '';

		// If there is no key in the options throw an error
		if (empty($key))
		{
			$event->addResult(false);

			return;
		}

		// Check the MFA code for validity
		$event->addResult((new Totp())->checkCode((string) $key, (string) $code));
	}

	/**
	 * Handle the OAuth2 callback
	 *
	 * The user is redirected to the callback URL by PushBullet itself. A code is sent back as a query string parameter.
	 * The code is sent back to PushBullet and we are given back a token. What happens next depends on the state URL
	 * parameter.
	 *
	 * If state=0 the MFA setup was initiated by the frontend of the site. Therefore we just need to save the token in
	 * the session and redirect the user back to the MFA Method setup page. This will be picked up by the
	 * onUserMultifactorGetSetup Method and a code will be sent to the user which he has to enter to finalize the setup.
	 *
	 * If state=1 the MFA setup was initiated by the backend of the site. The callback is always in the frontend of
	 * the site since PushBullet checks the path of the URL versus what has been configured. However, since I'm in the
	 * frontend of the site I cannot set a session variable and read it from the backend. In this case I redirect the
	 * browser to the backend callback URL passing the token as a query string parameter. When this is detected the
	 * token is read from the q.s.p. and the rest of the process described above (save to session and redirect to setup
	 * page) takes place.
	 *
	 * @param   Callback  $event
	 *
	 * @throws PushbulletException
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

		$input = $this->getApplication()->input;

		// Should I redirect to the back-end?
		$backend = $input->getInt('state', 0);

		// Do I have a token access variable?
		$token = $input->getString('token', null);

		// If I have no token and it's the front-end I have received a token in the URL fragment from PushBullet
		if (empty($token) && !$this->getApplication()->isClient('administrator'))
		{
			// The returned URL has a code query string parameter I need to use to retrieve a token
			$code  = $input->getString('code', null);
			$api   = new Api($this->accessToken);
			$token = $api->getToken($code, $this->clientId, $this->secret);
		}

		// Do I have to redirect to the backend?
		if ($backend == 1)
		{
			$redirectURL = Uri::base() . 'administrator/index.php?option=com_users&view=callback&task=callback&method=pushbullet&token=' . $token;

			$this->getApplication()->redirect($redirectURL);
		}

		// Sanity check: am I the same user as the one being edited or a Super User?
		$session = $this->getApplication()->getSession();
		$user_id = $session->get('com_users_mfa.pushbullet.user_id', null);

		$user = $this->getApplication()->getIdentity();

		if (!($user instanceof User) || (!empty($user_id) && ($user->id != $user_id) && !$user->authorise('core.admin')))
		{
			return;
		}

		// Set the token to the session
		$session->set('com_users_mfa.pushbullet.token', $token);
		$session->set('com_users_mfa.pushbullet.user_id', null);

		// Redirect to the editor page
		$userPart    = empty($user_id) ? '' : ('&user_id=' . $user_id);
		$redirectURL = 'index.php?option=com_users&view=method&task=add&method=pushbullet' . $userPart;

		$this->getApplication()->redirect($redirectURL);
	}

	/**
	 * Creates a new TOTP code based on secret key $key and sends it to the user via PushBullet using the access token
	 * $token.
	 *
	 * @param   string     $key    The TOTP secret key
	 * @param   string     $token  The PushBullet access token
	 * @param   User|null  $user   The Joomla! user to use
	 *
	 * @return  void
	 *
	 * @throws  PushbulletException  If something goes wrong
	 */
	private function sendCode(string $key, string $token, ?User $user = null)
	{
		static $alreadySent = false;

		// Make sure we have a user
		if (!is_object($user) || !($user instanceof User))
		{
			$user = $this->getApplication()->getIdentity()
				?: Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById(0);
		}

		// Get the API objects
		$totp       = new Totp(30, 6);
		$pushBullet = new Api($token);

		// Create the list of variable replacements
		$code = $totp->getCode($key);

		$replacements = [
			'[CODE]'     => $code,
			'[SITENAME]' => $this->getApplication()->get('sitename'),
			'[SITEURL]'  => Uri::base(),
			'[USERNAME]' => $user->username,
			'[EMAIL]'    => $user->email,
			'[FULLNAME]' => $user->name,
		];

		// Get the title and body of the push message
		$subject = Text::_('PLG_MULTIFACTORAUTH_PUSHBULLET_PUSH_TITLE');
		$subject = str_ireplace(array_keys($replacements), array_values($replacements), $subject);
		$message = Text::_('PLG_MULTIFACTORAUTH_PUSHBULLET_PUSH_MESSAGE');
		$message = str_ireplace(array_keys($replacements), array_values($replacements), $message);

		if ($alreadySent)
		{
			return;
		}

		$alreadySent = true;

		// Push the message to all of the user's devices
		$pushBullet->pushNote('', $subject, $message);
	}

	/**
	 * Decodes the options from a #__loginguard_tfa record into an options object.
	 *
	 * @param   MfaTable  $record
	 *
	 * @return  array
	 */
	private function decodeRecordOptions(MfaTable $record): array
	{
		$options = [
			'key'   => '',
			'token' => '',
		];

		if (!empty($record->options))
		{
			$recordOptions = $record->options;

			$options = array_merge($options, $recordOptions);
		}

		return $options;
	}
}
