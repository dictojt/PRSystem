@extends('layouts.user-panel')

@section('title', 'Create Request')

@section('main')
<div class="header-section">
    <h1>Create Request</h1>
    <p>Add one or more items. Each item gets its own Request ID. Use only letters, spaces, dots (.), and commas (,).</p>
</div>

<div class="card create-request-card">
    <form action="{{ route('user.requests.store') }}" method="POST" id="create-request-form">
        @csrf
        <div id="items-container">
            <div class="item-row" data-index="0">
                <div class="item-row-header">
                    <span class="item-number">Item 1</span>
                    <button type="button" class="item-row-delete" title="Remove item" aria-label="Remove this item" disabled><span class="material-icons">delete_outline</span></button>
                </div>
                <div class="form-row form-row-inline">
                    <div class="form-group flex-1">
                        <label for="items_0_item_name">Item name <span style="color:#ef4444;">*</span></label>
                        <input type="text" id="items_0_item_name" name="items[0][item_name]" value="{{ old('items.0.item_name') }}" required placeholder="e.g. Laptop, Office Chair">
                        @error('items.0.item_name')<span style="color:#ef4444;font-size:13px;">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group form-group-qty">
                        <label for="items_0_quantity">Qty</label>
                        <input type="number" id="items_0_quantity" name="items[0][quantity]" value="{{ old('items.0.quantity', 1) }}" min="1" max="1000" placeholder="1" title="Max 1000">
                        @error('items.0.quantity')<span style="color:#ef4444;font-size:13px;">{{ $message }}</span>@enderror
                        <span class="qty-hint">Max 1000</span>
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
.create-request-card { width: 100%; max-width: 100%; box-sizing: border-box; }
.item-row { border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; margin-bottom: 16px; background: #fafafa; }
.item-row-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; font-weight: 600; font-size: 14px; color: #374151; }
.item-row-delete { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; padding: 0; border: none; background: #fef2f2; color: #b91c1c; border-radius: 8px; cursor: pointer; transition: background 0.2s, color 0.2s; }
.item-row-delete:hover:not(:disabled) { background: #fee2e2; color: #991b1b; }
.item-row-delete:disabled { opacity: 0.5; cursor: not-allowed; background: #f3f4f6; color: #9ca3af; }
.item-row-delete .material-icons { font-size: 20px; }
.form-row-inline { display: flex; gap: 16px; align-items: flex-start; flex-wrap: wrap; }
.form-row-inline .flex-1 { flex: 1; min-width: 200px; }
.form-group-qty { width: 100px; }
.form-group-qty .qty-hint { display: block; font-size: 11px; color: #6b7280; margin-top: 2px; }
.form-actions { margin-top: 8px; margin-bottom: 8px; }
@media (max-width: 768px) {
    .create-request-card .form-row-inline .flex-1 { min-width: 100%; }
    .create-request-card .form-group-qty { width: 120px; }
}
</style>

<script>
(function() {
    var container = document.getElementById('items-container');
    var addBtn = document.getElementById('add-item-btn');
    var form = document.getElementById('create-request-form');
    var itemCount = 1;

    function updateRowIndices() {
        var rows = container.querySelectorAll('.item-row');
        rows.forEach(function(row, i) {
            row.setAttribute('data-index', i);
            var numEl = row.querySelector('.item-number');
            if (numEl) numEl.textContent = 'Item ' + (i + 1);
            var deleteBtn = row.querySelector('.item-row-delete');
            if (deleteBtn) deleteBtn.disabled = (rows.length <= 1);
            var nameInput = row.querySelector('input[name*="[item_name]"]');
            var qtyInput = row.querySelector('input[name*="[quantity]"]');
            var descInput = row.querySelector('textarea[name*="[description]"]');
            if (nameInput) nameInput.setAttribute('name', 'items[' + i + '][item_name]');
            if (qtyInput) qtyInput.setAttribute('name', 'items[' + i + '][quantity]');
            if (descInput) descInput.setAttribute('name', 'items[' + i + '][description]');
        });
    }

    function addItemRow() {
        itemCount++;
        var row = document.createElement('div');
        row.className = 'item-row';
        row.setAttribute('data-index', itemCount - 1);
        row.innerHTML =
            '<div class="item-row-header"><span class="item-number">Item ' + itemCount + '</span>' +
            '<button type="button" class="item-row-delete" title="Remove item" aria-label="Remove this item"><span class="material-icons">delete_outline</span></button></div>' +
            '<div class="form-row form-row-inline">' +
            '<div class="form-group flex-1">' +
            '<label>Item name <span style="color:#ef4444;">*</span></label>' +
            '<input type="text" name="items[' + (itemCount - 1) + '][item_name]" required placeholder="e.g. Laptop, Office Chair">' +
            '</div>' +
            '<div class="form-group form-group-qty">' +
            '<label>Qty</label>' +
            '<input type="number" name="items[' + (itemCount - 1) + '][quantity]" value="1" min="1" max="1000" placeholder="1" title="Max 1000">' +
            '<span class="qty-hint">Max 1000</span>' +
            '</div>' +
            '</div>' +
            '<div class="form-group">' +
            '<label>Description (optional)</label>' +
            '<textarea name="items[' + (itemCount - 1) + '][description]" placeholder="Brief description or justification"></textarea>' +
            '</div>';
        container.appendChild(row);
        row.querySelector('.item-row-delete').addEventListener('click', function() {
            var rows = container.querySelectorAll('.item-row');
            if (rows.length <= 1) return;
            row.remove();
            updateRowIndices();
        });
        updateRowIndices();
    }

    container.addEventListener('click', function(e) {
        if (e.target.closest('.item-row-delete')) {
            var btn = e.target.closest('.item-row-delete');
            if (btn.disabled) return;
            var row = btn.closest('.item-row');
            var rows = container.querySelectorAll('.item-row');
            if (rows.length <= 1) return;
            row.remove();
            updateRowIndices();
        }
    });

    if (form) {
        form.addEventListener('submit', function(e) {
            var qtyInputs = form.querySelectorAll('input[name*="[quantity]"]');
            for (var i = 0; i < qtyInputs.length; i++) {
                var val = parseInt(qtyInputs[i].value, 10);
                if (isNaN(val) || val < 1 || val > 1000) {
                    e.preventDefault();
                    alert('Quantity must be between 1 and 1000.');
                    qtyInputs[i].focus();
                    return false;
                }
            }
        });
    }

    if (addBtn) addBtn.addEventListener('click', addItemRow);
})();
</script>
@endsection
