{{--@php--}}
{{-- $isFavourite = $entity->isFavourite()--}}
{{--@endphp--}}
{{--<form action="{{ url('/favourites/' . ($isFavourite ? 'remove' : 'add')) }}" method="POST">--}}
{{--    {{ csrf_field() }}--}}
{{--    <input type="hidden" name="type" value="{{ get_class($entity) }}">--}}
{{--    <input type="hidden" name="id" value="{{ $entity->id }}">--}}
{{--    <button type="submit" class="icon-list-item text-primary">--}}
        <a href="{{ $page->getUrl('/report') }}" class="icon-list-item">
        <span>@icon('flag')</span>
        <span>{{ trans('entities.report') }}</span>
    </a>
{{--</form>--}}