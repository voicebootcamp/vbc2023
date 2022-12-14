<?php
/*------------------------------------------------------------------------
# downloadInvoice.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2019 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;
class OsbInvoice 
{
	public static function sendEmails() {
		
	}
	/**
	 * Get specify config value
	 *
	 * @param string $key
	 */
	public static function getConfigValue($key) {
		$db = JFactory::getDBO ();
		$sql = 'SELECT config_value FROM #__app_sch_configuation WHERE config_key  like "' . $key . '"';
		$db->setQuery ( $sql );
		return $db->loadResult ();
	}

	public static function getInvoiceNumber($row = null)
    {
        $db     = JFactory::getDbo();
		$db1    = JFactory::getDbo();
        $query  = $db->getQuery(true);
        $query->select('MAX(invoice_number)')
            ->from('#__app_sch_orders');

        if (self::getConfigValue ( 'reset_invoice'))
        {
            $currentYear = date('Y');
            $query->where('invoice_year = ' . $currentYear);

			$db1->setQuery("Update #__app_sch_orders set invoice_year = '$currentYear' where id = '$row->id'");
			$db1->execute();
        }

        $db->setQuery($query);
        $invoiceNumber = (int) $db->loadResult();

        if (!$invoiceNumber)
        {
            $invoiceNumber = (int) self::getConfigValue ( 'invoice_start_number' );
        }
        else
        {
            $invoiceNumber++;
        }

        return $invoiceNumber;
    }

	
	/**
	 * Format invoice number
	 * @param string $invoiceNumber
	 * @param Object $config
	 */
	public static function formatInvoiceNumber($invoiceNumber, $configClass, $row) 
	{
		$db = JFactory::getDbo();
		$db->setQuery("Select invoice_year from #__app_sch_orders where id = '$row->id'");
		$invoice_year = $db->loadResult();
		if ($invoice_year > 0)
        {
            $year = $invoice_year;
        }
        elseif (!empty($row->created_date))
        {
            $date = JFactory::getDate($row->created_date);
            $year = $date->format('Y');
        }
        else
        {
            $year = 0;
        }

        $invoicePrefix = str_replace('[YEAR]', $year, $configClass['invoice_prefix']);

        return $invoicePrefix . str_pad($invoiceNumber, $configClass['invoice_number_length'] ?: 4, '0', STR_PAD_LEFT);
	}
	/**
	 * Convert all img tags to use absolute URL
	 * @param string $html_content
	 */
	public static function convertImgTags($html_content) {
		$patterns = array ();
		$replacements = array ();
		$i = 0;
		$src_exp = "/src=\"(.*?)\"/";
		$link_exp = "[^http:\/\/www\.|^www\.|^https:\/\/|^http:\/\/]";
		$siteURL = JURI::root ();
		preg_match_all ( $src_exp, $html_content, $out, PREG_SET_ORDER );
		foreach ( $out as $val ) {
			$links = preg_match ( $link_exp, $val [1], $match, PREG_OFFSET_CAPTURE );
			if ($links == '0') {
				$patterns [$i] = $val [1];
				$patterns [$i] = "\"$val[1]";
				$replacements [$i] = $siteURL . $val [1];
				$replacements [$i] = "\"$replacements[$i]";
			}
			$i ++;
		}
		$mod_html_content = str_replace ( $patterns, $replacements, $html_content );
		
		return $mod_html_content;
	}

	/*
	//for the case: the original download function doesn't work correctly

	static function processDownload2($path, $filename , $detectFilename = false)
	{
		$fsize = @filesize ( $path );
		$mod_date = date ( 'r', filemtime ( $path ) );
		if ($detectFilename) {
			$pos = strpos ( $filename, '_' );
			$filename = substr ( $filename, $pos + 1 );
		}
		ob_clean();
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        header('Content-Type: ' . finfo_file($finfo, $path));
  
        $finfo = finfo_open(FILEINFO_MIME_ENCODING);
        header('Content-Transfer-Encoding: ' . finfo_file($finfo, $path)); 
       
        header('Content-disposition: attachment; filename="' . $filename . '";' . ' modification-date="' . $mod_date . '";' . ' size=' . $fsize . ';' ); 
       
        readfile($path);
        die();
	}
	*/
	
	/**
	 * Process download a file
	 *
	 * @param string $file : Full path to the file which will be downloaded
	 */
	public static function processDownload($filePath, $filename, $detectFilename = false) {
		jimport ( 'joomla.filesystem.file' );
		$fsize = @filesize ( $filePath );
		$mod_date = date ( 'r', filemtime ( $filePath ) );
		$cont_dis = 'attachment';
		if ($detectFilename) {
			$pos = strpos ( $filename, '_' );
			$filename = substr ( $filename, $pos + 1 );
		}
		$ext = JFile::getExt ( $filename );
		$mime = self::getMimeType ( $ext );
		// required for IE, otherwise Content-disposition is ignored
		if (ini_get ( 'zlib.output_compression' )) {
			ini_set ( 'zlib.output_compression', 'Off' );
		}
		header ( "Pragma: public" );
		header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header ( "Expires: 0" );
		header ( "Content-Transfer-Encoding: binary" );
		header ( 'Content-Disposition:' . $cont_dis . ';' . ' filename="' . $filename . '";' . ' modification-date="' . $mod_date . '";' . ' size=' . $fsize . ';' ); //RFC2183
		header ( "Content-Type: " . $mime ); // MIME type
		header ( "Content-Length: " . $fsize );
		
		if (! ini_get ( 'safe_mode' )) { // set_time_limit doesn't work in safe mode
			@set_time_limit ( 0 );
		}
		
		self::readfile_chunked ( $filePath );
	}
	
	/**
	 * Get mimetype of a file
	 *
	 * @return string
	 */
	public static function getMimeType($ext) {
		require_once JPATH_ROOT . "/components/com_osservicesbooking/helpers/mime.mapping.php";
		foreach ( $mime_extension_map as $key => $value ) {
			if ($key == $ext) {
				return $value;
			}
		}
		
		return "";
	}
	
	/**
	 * Read file
	 *
	 * @param string $filename
	 * @param  $retbytes
	 * @return unknown
	 */
	public static function readfile_chunked($filename, $retbytes = true) {
		$chunksize = 1 * (1024 * 1024); // how many bytes per chunk
		$buffer = '';
		$cnt = 0;
		$handle = fopen ( $filename, 'rb' );
		if ($handle === false) {
			return false;
		}
		while ( ! feof ( $handle ) ) {
			$buffer = fread ( $handle, $chunksize );
			echo $buffer;
			@ob_flush ();
			flush ();
			if ($retbytes) {
				$cnt += strlen ( $buffer );
			}
		}
		$status = fclose ( $handle );
		if ($retbytes && $status) {
			return $cnt; // return num. bytes delivered like readfile() does.
		}
		return $status;
	}
}
?>