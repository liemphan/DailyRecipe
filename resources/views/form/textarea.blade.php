<textarea id="{{ $name }}" name="{{ $name }}" rows="5"

          @if(isset($required) && $required) required @endif
          @if($errors->has($name)) class="text-neg" @endif>@if(isset($model) || old($name)){{ old($name) ? old($name) : $model->$name}}@endif</textarea>
@if($errors->has($name))
    <div class="text-neg text-small">{{ $errors->first($name) }}</div>
@endif