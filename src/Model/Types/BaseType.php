<?php declare(strict_types=1);

namespace Leo\Model\Types;

class BaseType
{
    protected mixed $default = null;
    protected mixed $system;
    protected mixed $value;
    protected EditType $edit ;

    public function __construct(
        mixed $default,
        array $edit = []
    )
    {
        $this->value = $default;
        $this->value = $this->default = $this->from($this->toString());
        $this->edit = new EditType(...$edit);
    }

    public function &__get(string $name)
    {
        return $this->$name;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->$name = $value;
    }
}

final class EditType
{
    private ?array $section;
    private string $input;
    private array $options;

    public function __construct(...$args)
    {
        $this->section = $args['section'] ?? null;
        $this->input = $args['input'] ?? 'text';
        $this->options = $args['options'] ?? [];
    }

    public function &__get(string $name)
    {
        return $this->$name;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->$name = $value;
    }
}
