<?php

namespace Osm\Admin\Ui\Hints;

use Osm\Core\Exceptions\NotSupported;
use function Osm\__;
use function Osm\url_encode;

/**
 * @property string $type
 * @property ?string $param
 * @property ?string $value
 */
class UrlAction
{
    public const REMOVE_ALL_FILTERS = 'remove_all_filters';
    public const REMOVE_PARAMETER = 'remove_parameter';
    public const REMOVE_OPTION = 'remove_option';
    public const SET_PARAMETER = 'set_parameter';
    public const ADD_OPTION = 'add_option';

    /**
     * Removes all filters from the URL.
     *
     * String syntax: '-'
     *
     * @return \stdClass|static
     */
    public static function removeFilters(): \stdClass|static {
        return (object)[
            'type' => static::REMOVE_ALL_FILTERS,
        ];
    }

    /**
     * Removes specified parameter from the URL.
     *
     * String syntax: '-color'
     *
     * @param string $param
     * @return \stdClass|static
     */
    public static function removeParameter(string $param): \stdClass|static {
        return (object)[
            'type' => static::REMOVE_PARAMETER,
            'param' => $param,
        ];
    }

    /**
     * Removes specified multi-value filter option from the URL.
     *
     * String syntax: '-color=red'
     *
     * @param string $param
     * @param string $value
     * @return \stdClass|static
     */
    public static function removeOption(string $param, string $value)
        : \stdClass|static
    {
        return (object)[
            'type' => static::REMOVE_OPTION,
            'param' => $param,
            'value' => $value,
        ];
    }

    /**
     * Sets specified parameter.
     *
     * String syntax: 'color=red'
     *
     * @param string $param
     * @param string $value
     * @return \stdClass|static
     */
    public static function setParameter(string $param, string $value = null)
        : \stdClass|static
    {
        return (object)[
            'type' => static::SET_PARAMETER,
            'param' => $param,
            'value' => $value,
        ];
    }

    /**
     * Adds an option to specified multi-value filter.
     *
     * String syntax: '+color=red'
     *
     * @param string $param
     * @param string $value
     * @return \stdClass|static
     */
    public static function addOption(string $param, string $value)
        : \stdClass|static
    {
        return (object)[
            'type' => static::ADD_OPTION,
            'param' => $param,
            'value' => $value,
        ];
    }

    /**
     * @param UrlAction[] $actions
     * @return string
     */
    public static function toString(array $actions): string {
        $url = '';

        foreach ($actions as $action) {
            if ($url) {
                $url .= '&';
            }

            $url .= match ($action->type) {
                static::REMOVE_ALL_FILTERS => '-',
                static::REMOVE_PARAMETER => "-{$action->param}",
                static::REMOVE_OPTION => "-{$action->param}=" .
                    url_encode($action->value),
                static::SET_PARAMETER => $action->param .
                    $action->value !== null
                        ? '=' . url_encode($action->value)
                        : '',
                static::ADD_OPTION => "+{$action->param}=" .
                    url_encode($action->value),
                default => throw new NotSupported(__(
                    "URL action type ':type' not supported",
                    ['type' => $action->type])),
            };
        }

        return $url;
    }
}