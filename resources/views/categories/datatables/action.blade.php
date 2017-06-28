@if ($currentUser->hasAccess('categories.show'))
<a class="blue" href="{{ route('categories.show', $id) }}"><i class="ace-icon fa fa-search-plus bigger-130"></i></a>
@endif
@if ($currentUser->hasAccess('categories.edit'))
<a class="green" href="{{ route('categories.edit', $id) }}"><i class="ace-icon fa fa-pencil bigger-130"></i></a>
@endif
