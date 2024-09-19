<?php declare(strict_types=1);

namespace Leo\Model\Types;

use Leo\Model\Interface\ConfigType;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

final class JsonType extends BaseType implements ConfigType
{
    public function getType(): string
    {
        return 'json';
    }

    public function isType(): bool
    {
        try {
            return Json::encode($this->value) !== null;
        } catch (JsonException $e) {
            return false;
        }
    }
    public function toString(): string
    {
        try {
            return Json::encode($this->value, asciiSafe: true);
        } catch (JsonException $e) {
            return '';
        }
    }

    public function from(string $value, bool $forceArrays = false): array|\stdClass
    {
        try {
            return Json::decode($value, forceArrays: $forceArrays);
        } catch (JsonException $e) {
            return $forceArrays ? [] : new \stdClass();
        }
    }
}
