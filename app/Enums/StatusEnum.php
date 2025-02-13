<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class StatusEnum extends Enum
{
    public const PENDING = 'Pending';

    public const IN_PROGRESS = 'In Progress';

    public const PAUSED = 'Paused';

    public const ERROR = 'Error';

    public const SUCCESS = 'Success';

    public const VALIDATED = 'Validated';

    public const NOT_VALIDATED = 'Not Validated';

    public const ARCHIVED = 'Archived';

    public const DELETED = 'Deleted';

    public const TRANSFERRED = 'Transferred';

    public const FINALIZED = 'Finalized';

    public const CANCELLED = 'Cancelled';

    public const DRAFT = 'Draft';

    private static array $properties = [
        'Pending' => ['color' => 'bg-yellow-400', 'isActive' => true],
        'In Progress' => ['color' => 'bg-blue-400', 'isActive' => true],
        'Paused' => ['color' => 'bg-orange-400', 'isActive' => false],
        'Error' => ['color' => 'bg-red-400', 'isActive' => false],
        'Success' => ['color' => 'bg-green-400', 'isActive' => true],
        'Validated' => ['color' => 'bg-green-500', 'isActive' => true],
        'Not Validated' => ['color' => 'bg-red-500', 'isActive' => false],
        'Archived' => ['color' => 'bg-gray-400', 'isActive' => false],
        'Deleted' => ['color' => 'bg-gray-600', 'isActive' => false],
        'Transferred' => ['color' => 'bg-purple-400', 'isActive' => false],
        'Finalized' => ['color' => 'bg-teal-400', 'isActive' => true],
        'Cancelled' => ['color' => 'bg-red-700', 'isActive' => false],
        'Draft' => ['color' => 'bg-indigo-400', 'isActive' => true],
    ];

    /**
     * Get the properties associated with a status.
     */
    public static function getProperties(string $status): array
    {
        return self::$properties[$status] ?? ['color' => 'bg-gray-200', 'isActive' => false];
    }

    /**
     * Returns only the active statuses.
     */
    public static function getActiveStatuses(): array
    {
        return array_filter(self::$properties, function ($value) {
            return $value['isActive'];
        });
    }
}
