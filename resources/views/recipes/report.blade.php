
{{ csrf_field() }}
<h1 class="list-heading">Report</h1>
<label class="setting-list-label">In this form, you will report a post that you see it illegal or spam. The report will be send to admin to consider keep or delete the reported post.</label>
<hr>
<div class="form-group title-input">
    <label for="name">Title</label>
    @include('form.text', ['name' => 'name', 'autofocus' => true])
</div>

<div class="form-group description-input">
    <label for="description">Reason to report this recipe</label>
    @include('form.textarea', ['name' => 'description'])
</div>

<div class="form-group text-right">
    <a href="{{ $returnLocation }}" class="button outline">{{ trans('common.cancel') }}</a>
    <button type="submit" class="button">Submit</button>
</div>