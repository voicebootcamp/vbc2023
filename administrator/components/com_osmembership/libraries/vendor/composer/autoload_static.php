<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite2cc8ae6a023a4631e7971c65cf5df16
{
    public static $prefixLengthsPsr4 = array (
        'c' => 
        array (
            'chillerlan\\Settings\\' => 20,
            'chillerlan\\QRCode\\' => 18,
        ),
        'V' => 
        array (
            'Valitron\\' => 9,
        ),
        'B' => 
        array (
            'Box\\Spout\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'chillerlan\\Settings\\' => 
        array (
            0 => __DIR__ . '/..' . '/chillerlan/php-settings-container/src',
        ),
        'chillerlan\\QRCode\\' => 
        array (
            0 => __DIR__ . '/..' . '/chillerlan/php-qrcode/src',
        ),
        'Valitron\\' => 
        array (
            0 => __DIR__ . '/..' . '/vlucas/valitron/src/Valitron',
        ),
        'Box\\Spout\\' => 
        array (
            0 => __DIR__ . '/..' . '/box/spout/src/Spout',
        ),
    );

    public static $classMap = array (
        'Box\\Spout\\Autoloader\\Psr4Autoloader' => __DIR__ . '/..' . '/box/spout/src/Spout/Autoloader/Psr4Autoloader.php',
        'Box\\Spout\\Common\\Creator\\HelperFactory' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Creator/HelperFactory.php',
        'Box\\Spout\\Common\\Entity\\Cell' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Entity/Cell.php',
        'Box\\Spout\\Common\\Entity\\Row' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Entity/Row.php',
        'Box\\Spout\\Common\\Entity\\Style\\Border' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Entity/Style/Border.php',
        'Box\\Spout\\Common\\Entity\\Style\\BorderPart' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Entity/Style/BorderPart.php',
        'Box\\Spout\\Common\\Entity\\Style\\CellAlignment' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Entity/Style/CellAlignment.php',
        'Box\\Spout\\Common\\Entity\\Style\\Color' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Entity/Style/Color.php',
        'Box\\Spout\\Common\\Entity\\Style\\Style' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Entity/Style/Style.php',
        'Box\\Spout\\Common\\Exception\\EncodingConversionException' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Exception/EncodingConversionException.php',
        'Box\\Spout\\Common\\Exception\\IOException' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Exception/IOException.php',
        'Box\\Spout\\Common\\Exception\\InvalidArgumentException' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Exception/InvalidArgumentException.php',
        'Box\\Spout\\Common\\Exception\\InvalidColorException' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Exception/InvalidColorException.php',
        'Box\\Spout\\Common\\Exception\\SpoutException' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Exception/SpoutException.php',
        'Box\\Spout\\Common\\Exception\\UnsupportedTypeException' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Exception/UnsupportedTypeException.php',
        'Box\\Spout\\Common\\Helper\\CellTypeHelper' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Helper/CellTypeHelper.php',
        'Box\\Spout\\Common\\Helper\\EncodingHelper' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Helper/EncodingHelper.php',
        'Box\\Spout\\Common\\Helper\\Escaper\\CSV' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Helper/Escaper/CSV.php',
        'Box\\Spout\\Common\\Helper\\Escaper\\EscaperInterface' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Helper/Escaper/EscaperInterface.php',
        'Box\\Spout\\Common\\Helper\\Escaper\\ODS' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Helper/Escaper/ODS.php',
        'Box\\Spout\\Common\\Helper\\Escaper\\XLSX' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Helper/Escaper/XLSX.php',
        'Box\\Spout\\Common\\Helper\\FileSystemHelper' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Helper/FileSystemHelper.php',
        'Box\\Spout\\Common\\Helper\\FileSystemHelperInterface' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Helper/FileSystemHelperInterface.php',
        'Box\\Spout\\Common\\Helper\\GlobalFunctionsHelper' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Helper/GlobalFunctionsHelper.php',
        'Box\\Spout\\Common\\Helper\\StringHelper' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Helper/StringHelper.php',
        'Box\\Spout\\Common\\Manager\\OptionsManagerAbstract' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Manager/OptionsManagerAbstract.php',
        'Box\\Spout\\Common\\Manager\\OptionsManagerInterface' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Manager/OptionsManagerInterface.php',
        'Box\\Spout\\Common\\Type' => __DIR__ . '/..' . '/box/spout/src/Spout/Common/Type.php',
        'Box\\Spout\\Reader\\CSV\\Creator\\InternalEntityFactory' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/CSV/Creator/InternalEntityFactory.php',
        'Box\\Spout\\Reader\\CSV\\Manager\\OptionsManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/CSV/Manager/OptionsManager.php',
        'Box\\Spout\\Reader\\CSV\\Reader' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/CSV/Reader.php',
        'Box\\Spout\\Reader\\CSV\\RowIterator' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/CSV/RowIterator.php',
        'Box\\Spout\\Reader\\CSV\\Sheet' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/CSV/Sheet.php',
        'Box\\Spout\\Reader\\CSV\\SheetIterator' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/CSV/SheetIterator.php',
        'Box\\Spout\\Reader\\Common\\Creator\\InternalEntityFactoryInterface' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/Common/Creator/InternalEntityFactoryInterface.php',
        'Box\\Spout\\Reader\\Common\\Creator\\ReaderEntityFactory' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/Common/Creator/ReaderEntityFactory.php',
        'Box\\Spout\\Reader\\Common\\Creator\\ReaderFactory' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/Common/Creator/ReaderFactory.php',
        'Box\\Spout\\Reader\\Common\\Entity\\Options' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/Common/Entity/Options.php',
        'Box\\Spout\\Reader\\Common\\Manager\\RowManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/Common/Manager/RowManager.php',
        'Box\\Spout\\Reader\\Common\\XMLProcessor' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/Common/XMLProcessor.php',
        'Box\\Spout\\Reader\\Exception\\InvalidValueException' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/Exception/InvalidValueException.php',
        'Box\\Spout\\Reader\\Exception\\IteratorNotRewindableException' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/Exception/IteratorNotRewindableException.php',
        'Box\\Spout\\Reader\\Exception\\NoSheetsFoundException' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/Exception/NoSheetsFoundException.php',
        'Box\\Spout\\Reader\\Exception\\ReaderException' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/Exception/ReaderException.php',
        'Box\\Spout\\Reader\\Exception\\ReaderNotOpenedException' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/Exception/ReaderNotOpenedException.php',
        'Box\\Spout\\Reader\\Exception\\SharedStringNotFoundException' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/Exception/SharedStringNotFoundException.php',
        'Box\\Spout\\Reader\\Exception\\XMLProcessingException' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/Exception/XMLProcessingException.php',
        'Box\\Spout\\Reader\\IteratorInterface' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/IteratorInterface.php',
        'Box\\Spout\\Reader\\ODS\\Creator\\HelperFactory' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/ODS/Creator/HelperFactory.php',
        'Box\\Spout\\Reader\\ODS\\Creator\\InternalEntityFactory' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/ODS/Creator/InternalEntityFactory.php',
        'Box\\Spout\\Reader\\ODS\\Creator\\ManagerFactory' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/ODS/Creator/ManagerFactory.php',
        'Box\\Spout\\Reader\\ODS\\Helper\\CellValueFormatter' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/ODS/Helper/CellValueFormatter.php',
        'Box\\Spout\\Reader\\ODS\\Helper\\SettingsHelper' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/ODS/Helper/SettingsHelper.php',
        'Box\\Spout\\Reader\\ODS\\Manager\\OptionsManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/ODS/Manager/OptionsManager.php',
        'Box\\Spout\\Reader\\ODS\\Reader' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/ODS/Reader.php',
        'Box\\Spout\\Reader\\ODS\\RowIterator' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/ODS/RowIterator.php',
        'Box\\Spout\\Reader\\ODS\\Sheet' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/ODS/Sheet.php',
        'Box\\Spout\\Reader\\ODS\\SheetIterator' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/ODS/SheetIterator.php',
        'Box\\Spout\\Reader\\ReaderAbstract' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/ReaderAbstract.php',
        'Box\\Spout\\Reader\\ReaderInterface' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/ReaderInterface.php',
        'Box\\Spout\\Reader\\SheetInterface' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/SheetInterface.php',
        'Box\\Spout\\Reader\\Wrapper\\XMLInternalErrorsHelper' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/Wrapper/XMLInternalErrorsHelper.php',
        'Box\\Spout\\Reader\\Wrapper\\XMLReader' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/Wrapper/XMLReader.php',
        'Box\\Spout\\Reader\\XLSX\\Creator\\HelperFactory' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/Creator/HelperFactory.php',
        'Box\\Spout\\Reader\\XLSX\\Creator\\InternalEntityFactory' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/Creator/InternalEntityFactory.php',
        'Box\\Spout\\Reader\\XLSX\\Creator\\ManagerFactory' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/Creator/ManagerFactory.php',
        'Box\\Spout\\Reader\\XLSX\\Helper\\CellHelper' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/Helper/CellHelper.php',
        'Box\\Spout\\Reader\\XLSX\\Helper\\CellValueFormatter' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/Helper/CellValueFormatter.php',
        'Box\\Spout\\Reader\\XLSX\\Helper\\DateFormatHelper' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/Helper/DateFormatHelper.php',
        'Box\\Spout\\Reader\\XLSX\\Manager\\OptionsManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/Manager/OptionsManager.php',
        'Box\\Spout\\Reader\\XLSX\\Manager\\SharedStringsCaching\\CachingStrategyFactory' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/Manager/SharedStringsCaching/CachingStrategyFactory.php',
        'Box\\Spout\\Reader\\XLSX\\Manager\\SharedStringsCaching\\CachingStrategyInterface' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/Manager/SharedStringsCaching/CachingStrategyInterface.php',
        'Box\\Spout\\Reader\\XLSX\\Manager\\SharedStringsCaching\\FileBasedStrategy' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/Manager/SharedStringsCaching/FileBasedStrategy.php',
        'Box\\Spout\\Reader\\XLSX\\Manager\\SharedStringsCaching\\InMemoryStrategy' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/Manager/SharedStringsCaching/InMemoryStrategy.php',
        'Box\\Spout\\Reader\\XLSX\\Manager\\SharedStringsManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/Manager/SharedStringsManager.php',
        'Box\\Spout\\Reader\\XLSX\\Manager\\SheetManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/Manager/SheetManager.php',
        'Box\\Spout\\Reader\\XLSX\\Manager\\StyleManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/Manager/StyleManager.php',
        'Box\\Spout\\Reader\\XLSX\\Manager\\WorkbookRelationshipsManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/Manager/WorkbookRelationshipsManager.php',
        'Box\\Spout\\Reader\\XLSX\\Reader' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/Reader.php',
        'Box\\Spout\\Reader\\XLSX\\RowIterator' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/RowIterator.php',
        'Box\\Spout\\Reader\\XLSX\\Sheet' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/Sheet.php',
        'Box\\Spout\\Reader\\XLSX\\SheetIterator' => __DIR__ . '/..' . '/box/spout/src/Spout/Reader/XLSX/SheetIterator.php',
        'Box\\Spout\\Writer\\CSV\\Manager\\OptionsManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/CSV/Manager/OptionsManager.php',
        'Box\\Spout\\Writer\\CSV\\Writer' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/CSV/Writer.php',
        'Box\\Spout\\Writer\\Common\\Creator\\InternalEntityFactory' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Creator/InternalEntityFactory.php',
        'Box\\Spout\\Writer\\Common\\Creator\\ManagerFactoryInterface' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Creator/ManagerFactoryInterface.php',
        'Box\\Spout\\Writer\\Common\\Creator\\Style\\BorderBuilder' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Creator/Style/BorderBuilder.php',
        'Box\\Spout\\Writer\\Common\\Creator\\Style\\StyleBuilder' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Creator/Style/StyleBuilder.php',
        'Box\\Spout\\Writer\\Common\\Creator\\WriterEntityFactory' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Creator/WriterEntityFactory.php',
        'Box\\Spout\\Writer\\Common\\Creator\\WriterFactory' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Creator/WriterFactory.php',
        'Box\\Spout\\Writer\\Common\\Entity\\Options' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Entity/Options.php',
        'Box\\Spout\\Writer\\Common\\Entity\\Sheet' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Entity/Sheet.php',
        'Box\\Spout\\Writer\\Common\\Entity\\Workbook' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Entity/Workbook.php',
        'Box\\Spout\\Writer\\Common\\Entity\\Worksheet' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Entity/Worksheet.php',
        'Box\\Spout\\Writer\\Common\\Helper\\CellHelper' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Helper/CellHelper.php',
        'Box\\Spout\\Writer\\Common\\Helper\\FileSystemWithRootFolderHelperInterface' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Helper/FileSystemWithRootFolderHelperInterface.php',
        'Box\\Spout\\Writer\\Common\\Helper\\ZipHelper' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Helper/ZipHelper.php',
        'Box\\Spout\\Writer\\Common\\Manager\\CellManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Manager/CellManager.php',
        'Box\\Spout\\Writer\\Common\\Manager\\RegisteredStyle' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Manager/RegisteredStyle.php',
        'Box\\Spout\\Writer\\Common\\Manager\\RowManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Manager/RowManager.php',
        'Box\\Spout\\Writer\\Common\\Manager\\SheetManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Manager/SheetManager.php',
        'Box\\Spout\\Writer\\Common\\Manager\\Style\\PossiblyUpdatedStyle' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Manager/Style/PossiblyUpdatedStyle.php',
        'Box\\Spout\\Writer\\Common\\Manager\\Style\\StyleManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Manager/Style/StyleManager.php',
        'Box\\Spout\\Writer\\Common\\Manager\\Style\\StyleManagerInterface' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Manager/Style/StyleManagerInterface.php',
        'Box\\Spout\\Writer\\Common\\Manager\\Style\\StyleMerger' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Manager/Style/StyleMerger.php',
        'Box\\Spout\\Writer\\Common\\Manager\\Style\\StyleRegistry' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Manager/Style/StyleRegistry.php',
        'Box\\Spout\\Writer\\Common\\Manager\\WorkbookManagerAbstract' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Manager/WorkbookManagerAbstract.php',
        'Box\\Spout\\Writer\\Common\\Manager\\WorkbookManagerInterface' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Manager/WorkbookManagerInterface.php',
        'Box\\Spout\\Writer\\Common\\Manager\\WorksheetManagerInterface' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Common/Manager/WorksheetManagerInterface.php',
        'Box\\Spout\\Writer\\Exception\\Border\\InvalidNameException' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Exception/Border/InvalidNameException.php',
        'Box\\Spout\\Writer\\Exception\\Border\\InvalidStyleException' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Exception/Border/InvalidStyleException.php',
        'Box\\Spout\\Writer\\Exception\\Border\\InvalidWidthException' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Exception/Border/InvalidWidthException.php',
        'Box\\Spout\\Writer\\Exception\\InvalidSheetNameException' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Exception/InvalidSheetNameException.php',
        'Box\\Spout\\Writer\\Exception\\SheetNotFoundException' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Exception/SheetNotFoundException.php',
        'Box\\Spout\\Writer\\Exception\\WriterAlreadyOpenedException' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Exception/WriterAlreadyOpenedException.php',
        'Box\\Spout\\Writer\\Exception\\WriterException' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Exception/WriterException.php',
        'Box\\Spout\\Writer\\Exception\\WriterNotOpenedException' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/Exception/WriterNotOpenedException.php',
        'Box\\Spout\\Writer\\ODS\\Creator\\HelperFactory' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/ODS/Creator/HelperFactory.php',
        'Box\\Spout\\Writer\\ODS\\Creator\\ManagerFactory' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/ODS/Creator/ManagerFactory.php',
        'Box\\Spout\\Writer\\ODS\\Helper\\BorderHelper' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/ODS/Helper/BorderHelper.php',
        'Box\\Spout\\Writer\\ODS\\Helper\\FileSystemHelper' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/ODS/Helper/FileSystemHelper.php',
        'Box\\Spout\\Writer\\ODS\\Manager\\OptionsManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/ODS/Manager/OptionsManager.php',
        'Box\\Spout\\Writer\\ODS\\Manager\\Style\\StyleManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/ODS/Manager/Style/StyleManager.php',
        'Box\\Spout\\Writer\\ODS\\Manager\\Style\\StyleRegistry' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/ODS/Manager/Style/StyleRegistry.php',
        'Box\\Spout\\Writer\\ODS\\Manager\\WorkbookManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/ODS/Manager/WorkbookManager.php',
        'Box\\Spout\\Writer\\ODS\\Manager\\WorksheetManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/ODS/Manager/WorksheetManager.php',
        'Box\\Spout\\Writer\\ODS\\Writer' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/ODS/Writer.php',
        'Box\\Spout\\Writer\\WriterAbstract' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/WriterAbstract.php',
        'Box\\Spout\\Writer\\WriterInterface' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/WriterInterface.php',
        'Box\\Spout\\Writer\\WriterMultiSheetsAbstract' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/WriterMultiSheetsAbstract.php',
        'Box\\Spout\\Writer\\XLSX\\Creator\\HelperFactory' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/XLSX/Creator/HelperFactory.php',
        'Box\\Spout\\Writer\\XLSX\\Creator\\ManagerFactory' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/XLSX/Creator/ManagerFactory.php',
        'Box\\Spout\\Writer\\XLSX\\Helper\\BorderHelper' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/XLSX/Helper/BorderHelper.php',
        'Box\\Spout\\Writer\\XLSX\\Helper\\FileSystemHelper' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/XLSX/Helper/FileSystemHelper.php',
        'Box\\Spout\\Writer\\XLSX\\Manager\\OptionsManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/XLSX/Manager/OptionsManager.php',
        'Box\\Spout\\Writer\\XLSX\\Manager\\SharedStringsManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/XLSX/Manager/SharedStringsManager.php',
        'Box\\Spout\\Writer\\XLSX\\Manager\\Style\\StyleManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/XLSX/Manager/Style/StyleManager.php',
        'Box\\Spout\\Writer\\XLSX\\Manager\\Style\\StyleRegistry' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/XLSX/Manager/Style/StyleRegistry.php',
        'Box\\Spout\\Writer\\XLSX\\Manager\\WorkbookManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/XLSX/Manager/WorkbookManager.php',
        'Box\\Spout\\Writer\\XLSX\\Manager\\WorksheetManager' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/XLSX/Manager/WorksheetManager.php',
        'Box\\Spout\\Writer\\XLSX\\Writer' => __DIR__ . '/..' . '/box/spout/src/Spout/Writer/XLSX/Writer.php',
        'Valitron\\Validator' => __DIR__ . '/..' . '/vlucas/valitron/src/Valitron/Validator.php',
        'chillerlan\\QRCode\\Data\\AlphaNum' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Data/AlphaNum.php',
        'chillerlan\\QRCode\\Data\\Byte' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Data/Byte.php',
        'chillerlan\\QRCode\\Data\\Kanji' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Data/Kanji.php',
        'chillerlan\\QRCode\\Data\\MaskPatternTester' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Data/MaskPatternTester.php',
        'chillerlan\\QRCode\\Data\\Number' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Data/Number.php',
        'chillerlan\\QRCode\\Data\\QRCodeDataException' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Data/QRCodeDataException.php',
        'chillerlan\\QRCode\\Data\\QRDataAbstract' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Data/QRDataAbstract.php',
        'chillerlan\\QRCode\\Data\\QRDataInterface' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Data/QRDataInterface.php',
        'chillerlan\\QRCode\\Data\\QRMatrix' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Data/QRMatrix.php',
        'chillerlan\\QRCode\\Helpers\\BitBuffer' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Helpers/BitBuffer.php',
        'chillerlan\\QRCode\\Helpers\\Polynomial' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Helpers/Polynomial.php',
        'chillerlan\\QRCode\\Output\\QRCodeOutputException' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Output/QRCodeOutputException.php',
        'chillerlan\\QRCode\\Output\\QRFpdf' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Output/QRFpdf.php',
        'chillerlan\\QRCode\\Output\\QRImage' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Output/QRImage.php',
        'chillerlan\\QRCode\\Output\\QRImagick' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Output/QRImagick.php',
        'chillerlan\\QRCode\\Output\\QRMarkup' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Output/QRMarkup.php',
        'chillerlan\\QRCode\\Output\\QROutputAbstract' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Output/QROutputAbstract.php',
        'chillerlan\\QRCode\\Output\\QROutputInterface' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Output/QROutputInterface.php',
        'chillerlan\\QRCode\\Output\\QRString' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/Output/QRString.php',
        'chillerlan\\QRCode\\QRCode' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/QRCode.php',
        'chillerlan\\QRCode\\QRCodeException' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/QRCodeException.php',
        'chillerlan\\QRCode\\QROptions' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/QROptions.php',
        'chillerlan\\QRCode\\QROptionsTrait' => __DIR__ . '/..' . '/chillerlan/php-qrcode/src/QROptionsTrait.php',
        'chillerlan\\Settings\\SettingsContainerAbstract' => __DIR__ . '/..' . '/chillerlan/php-settings-container/src/SettingsContainerAbstract.php',
        'chillerlan\\Settings\\SettingsContainerInterface' => __DIR__ . '/..' . '/chillerlan/php-settings-container/src/SettingsContainerInterface.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite2cc8ae6a023a4631e7971c65cf5df16::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite2cc8ae6a023a4631e7971c65cf5df16::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite2cc8ae6a023a4631e7971c65cf5df16::$classMap;

        }, null, ClassLoader::class);
    }
}
