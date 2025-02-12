<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Assistant()
 * @method static static Chat()
 * @method static static User()
 * @method static static File()
 * @method static static Url()
 * @method static static Audio()
 */
final class ChatType extends Enum
{
    public const Assistant = 'assistant';
    public const Chat = 'chat';
    public const User = 'user';
    public const File = 'file';
    public const Url = 'url';
    public const Audio = 'audio';
}
