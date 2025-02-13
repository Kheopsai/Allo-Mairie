<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ExtensionEnum extends Enum
{
    public const DOC = 'doc';

    public const DOCX = 'docx';

    public const PDF = 'pdf';

    public const TXT = 'txt';

    public const XLS = 'xls';

    public const XLSX = 'xlsx';

    public const PPTX = 'pptx';

    public const PPT = 'ppt';

    public const HTML = 'html';

    public const PNG = 'png';

    public const JSON = 'json';

    public const UNKNOWN = 'unknown';
}
