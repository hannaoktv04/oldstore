<a href="{{ $edit_url }}" class="btn btn-sm btn-warning">Edit</a>
<form action="{{ $delete_url }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus item ini?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
</form>
