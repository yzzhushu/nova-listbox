<?php

namespace Jshxl\ListBox;

use Closure;
use App\Nova\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class ListBox extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'list-box';

    /**
     * The class name of the related resource.
     *
     * @var class-string<Resource>
     */
    public string $resourceClass;

    /**
     * Indicate the field type.
     *
     * @var boolean|string
     */
    public bool $belongsToMany = false;

    /**
     * Indicate the field options.
     *
     * @var array|string
     * */
    public string|array $options;

    /**
     * Add code before name.
     *
     * @var bool
     * */
    public bool $nameWithCode = false;

    /**
     * 是否需要将字段值转换为整数
     *
     * @var bool
     * */
    public bool $formatInt = false;

    /**
     * listbox提示文字
     *
     * @var array
     * */
    public array $messages = [
        'emptyMessage'       => '暂无可选数据',
        'filterPlaceholder'  => '输入关键词搜索',
        'emptyFilterMessage' => '暂无匹配数据',
    ];

    /**
     * Determine if the field should be displayed for the given request.
     *
     * @param  Request  $request
     * @return bool
     */
    public function authorize(Request $request): bool
    {
        if (!isset($this->resourceClass)) return parent::authorize($request);
        return call_user_func(
                [$this->resourceClass, 'authorizedToViewAny'], $request
            ) && parent::authorize($request);
    }

    /**
     * Indicate the related resource.
     * @param class-string<Resource> $resource
     * @param string|null $options
     *
     * @return self
     */
    public function resource(string $resource, string|null $options = null): self
    {
        $this->resourceClass = $resource;
        $this->options = $options ?: ('/nova-api/' . $resource::uriKey() . '/search');
        return $this;
    }

    /**
     * Indicate how to save this field.
     * @param bool $boolean
     *
     * @return self
     */
    public function belongsToMany(bool $boolean = true): self
    {
        $this->belongsToMany = isset($this->resourceClass) ? $boolean : false;
        return $this;
    }

    /**
     * 将字段值转换为整数
     * @param bool $boolean
     *
     * @return self
     */
    public function formatInt(bool $boolean = true): self
    {
        $this->formatInt = $boolean;
        return $this;
    }

    /**
     * Indicate the field options.
     * @param array|string $options
     *
     * @return self
     * */
    public function options(array|string $options = []): self
    {
        if (isset($this->resourceClass)) {
            return $this;
        }
        $this->options = $options;
        return $this;
    }

    /**
     * Add code before name.
     * @param bool $boolean
     *
     * @return self
     * */
    public function nameWithCode(bool $boolean = true): self
    {
        $this->nameWithCode = $boolean;
        return $this;
    }

    /**
     * 设置提示文字
     * @param string|array $key
     * @param string $value
     *
     * @return self
     * */
    public function setMessage(string|array $key, string $value = ''): self
    {
        if (is_array($key)) {
            $this->messages = array_merge($this->messages, $key);
        } else {
            $this->messages[$key] = $value;
        }
        return $this;
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     * @param NovaRequest $request
     * @param string $requestAttribute
     * @param Model $model
     * @param string $attribute
     *
     * @return Closure|void
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if (!$request->exists($requestAttribute)) return;

        $value = collect(json_decode($request->input($requestAttribute), true))
            ->map(function ($item) {
                return $this->formatInt ? intval($item) : $item;
            })
            ->all();
        if (!$this->belongsToMany) {
            $model->forceFill([Str::replace('.', '->', $attribute) => $value]);
            return;
        }

        return function () use ($model, $attribute, $value) {
            $model->{$attribute}()->sync($value);
        };
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return with(app(NovaRequest::class), function ($request) {
            return array_merge([
                'options'       => $this->options ?? [],
                'belongsToMany' => $this->belongsToMany,
                'resourceClass' => $this->resourceClass ?? '',
                'nameWithCode'  => $this->nameWithCode,
                'messages'      => $this->messages,
            ], parent::jsonSerialize());
        });
    }
}
