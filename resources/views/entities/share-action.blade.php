{{--<form action="{{ url('/favourites/' . ($isFavourite ? 'remove' : 'add')) }}" method="POST">--}}
{{--    {{ csrf_field() }}--}}
{{--    <input type="hidden" name="type" value="{{ get_class($entity) }}">--}}
{{--    <input type="hidden" name="id" value="{{ $entity->id }}">--}}
{{--    <button type="submit" class="icon-list-item text-primary">--}}
<form action="{{url('https://www.facebook.com/sharer/sharer.php?u='). $recipe->getUrl() }}" method="POST">
    <button class="button">
        <span>@icon('auth/facebook')</span>
        <span>{{ trans('entities.share_facebook') }}</span>
    </button>
</form>