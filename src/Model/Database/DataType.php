<?php declare(strict_types=1);

namespace Leo\Model\Database;

use Nette;
use Nette\PhpGenerator\Helpers;
use Nette\Utils\Type;

class DataType
{
    const Array = 'array';
    const Boolean = 'bool';
    const Date = 'date';
    const DateTime = 'datetime';
    const Time = 'time';
    const Float = 'float';
    const Int = 'int';
    const String = 'string';
    const Text = 'text';

    private ?string $name;
    private ?string $type;
    private ?string $nativeType;
    private ?string $default;
    private ?int $size;
    private bool $nullable;
    private bool $allowNull;

    public function __construct(
        array $column,
        $typeChecker
    )
    {
        $this->name = $column['name'];
        $this->type = $typeChecker->checkType($column);
        $this->nativeType = $column['nativetype'];
        $this->default = $column['default'];
        $this->size = $column['size'];
        $this->nullable = $column['nullable'];
        $this->allowNull = $typeChecker->allowNull($column);
    }

    public function setAllowNull(bool $allowNull): static
    {
        $this->allowNull = $allowNull;
        return $this;
    }

    public function allowNull(): bool
    {
        return $this->allowNull;
    }

    public function hasDefault(): bool
    {
        return $this->default !== null;
    }

    public function setDefault(string $default): static
    {
        $this->default = $default;
        return $this;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setNativeType(?string $nativeType): static
    {
        $this->nativeType = $nativeType;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNativeType(): ?string
    {
        return $this->nativeType;
    }

    public function setSize(?int $size): static
    {
        $this->size = $size;
        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setType(?string $type): static
    {
        $this->type = Helpers::validateType($type, $this->nullable);
        return $this;
    }

    /**
     * @return ($asObject is true ? ?Type : ?string)
     */
    public function getType(bool $asObject = false): Type|string|null
    {
        return $asObject && $this->type
            ? Type::fromString($this->type)
            : $this->type;
    }

    public function setNullable(bool $state = true): static
    {
        $this->nullable = $state;
        return $this;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function setInitialized(bool $state = true): static
    {
        $this->initialized = $state;
        return $this;
    }

    public function isInitialized(): bool
    {
        return $this->initialized || $this->value !== null;
    }

    public function setReadOnly(bool $state = true): static
    {
        $this->readOnly = $state;
        return $this;
    }

    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    /** @throws Nette\InvalidStateException */
    public function validate(): void
    {
        if ($this->readOnly && !$this->type) {
            throw new Nette\InvalidStateException("DataType \$$this->name: Read-only properties are only supported on typed property.");
        }
    }
}
