@extends('layouts.user-panel')

@section('title', 'Create Request')

@section('main')
<div class="header-section">
    <h1>Create Request</h1>
    <p>Add one or more items. Each item gets its own Request ID. Use only letters, spaces, dots (.), and commas (,).</p>
</div>

<div class="card" style="max-width: 720px;">
    <form action="{{ route('user.requests.store') }}" method="POST" id="create-request-form">
        @csrf
        <div id="items-container">
            <div class="item-row" data-index="0">
                <div class="item-row-header">
                    <span class="item-number">Item 1</span>
                </div>
                <div class="form-row form-row-inline">
                    <div class="form-group flex-1">
                        <label for="items_0_item_name">Item name <span style="color:#ef4444;">*</span></label>
                        <input type="text" id="items_0_item_name" name="items[0][item_name]" value="{{ old('items.0.item_name') }}" required placeholder="e.g. Laptop, Office Chair">
                        @error('items.0.item_name')<span style="color:#ef4444;font-size:13px;">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group" style="width: 100px;">
                        <label for="items_0_quantity">Qty</label>
                        <input type="number" id="items_0_quantity" name="items[0][quantity]" value="{{ old('items.0.quantity', 1) }}" min="1" max="9999" placeholder="1">
                        @error('items.0.quantity')<span style="color:#ef4444;font-size:13px;">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="items_0_description">Description (optional)</label>
                    <textarea id="items_0_description" name="items[0][description]" placeholder="Brief description or justification">{{ old('items.0.description') }}</textarea>
                    @error('items.0.description')<span style="color:#ef4444;font-size:13px;">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" id="add-item-btn" class="btn btn-outline"><span class="material-icons" style="font-size:18px;">add</span> Add item</button>
        </div>

        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="submit" class="btn btn-primary"><span class="material-icons" style="font-size:18px;">send</span> Submit all items</button>
            <a href="{{ auth()->check() ? route('user.dashboard') : route('user.guest') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<style>
.item-row { border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; margin-bottom: 16px; background: #fafafa; }
.item-row-header { margin-bottom: 12px; font-weight: 600; font-size: 14px; color: #374151; }
.form-row-inline { display: flex; gap: 16px; align-items: flex-start; flex-wrap: wrap; }
.form-row-inline .flex-1 { flex: 1; min-width: 200px; }
.form-actions { margin-top: 8px; margin-bottom: 8px; }
</style>

<script>
(function() {
    var container = document.getElementById('items-container');
    var addBtn = document.getElementById('add-item-btn');
    var itemCount = 1;

    function addItemRow() {
        itemCount++;
        var row = document.createElement('div');
        row.className = 'item-row';
        row.setAttribute('data-index', itemCount - 1);
        row.innerHTML =
            '<div class="item-row-header"><span class="item-number">Item ' + itemCount + '</span></div>' +
            '<div class="form-row form-row-inline">' +
            '<div class="form-group flex-1">' +
            '<label>Item name <span style="color:#ef4444;">*</span></label>' +
            '<input type="text" name="items[' + (itemCount - 1) + '][item_name]" required placeholder="e.g. Laptop, Office Chair">' +
            '</div>' +
            '<div class="form-group" style="width: 100px;">' +
            '<label>Qty</label>' +
            '<input type="number" name="items[' + (itemCount - 1) + '][quantity]" value="1" min="1" max="9999" placeholder="1">' +
            '</div>' +
            '</div>' +
            '<div class="form-group">' +
            '<label>Description (optional)</label>' +
            '<textarea name="items[' + (itemCount - 1) + '][description]" placeholder="Brief description or justification"></textarea>' +
            '</div>';
        container.appendChild(row);
    }

    if (addBtn) addBtn.addEventListener('click', addItemRow);
})();
</script>
@endsection
