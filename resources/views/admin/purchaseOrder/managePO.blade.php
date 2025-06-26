@extends('layouts.admin')

@section('title', 'Manage Purchase Order')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h4 class="card-title">{{ isset($po_code) ? 'Purchase Order Details - ' . $po_code : 'Create New Purchase Order' }}</h4>
    </div>
    <div class="card-body">
        <form id="po-form">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <label class="text-info">P.O. Code</label>
                        <input type="text" class="form-control form-control-sm rounded-0" value="PO-001" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="text-info">Supplier</label>
                        <select name="supplier_id" id="supplier_id" class="form-control form-control-sm select2">
                            <option value="" disabled selected>-- Choose Supplier --</option>
                            <option value="1">PT Sumber Rezeki</option>
                            <option value="2">CV Makmur Sentosa</option>
                        </select>
                    </div>
                </div>
                <hr>
                <fieldset>
                    <legend class="text-info">Item Form</legend>
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label>Item</label>
                            <select id="item_id" class="form-control form-control-sm">
                                <option disabled selected>-- Select Item --</option>
                                <option value="1" data-unit="pcs" data-cost="10000">Barang A</option>
                                <option value="2" data-unit="kg" data-cost="20000">Barang B</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Unit</label>
                            <input type="text" id="unit" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label>Qty</label>
                            <input type="number" id="qty" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary btn-sm mt-4" id="add_to_list">Add to List</button>
                        </div>
                    </div>
                </fieldset>
                <hr>
                <table class="table table-bordered" id="list">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>#</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Item</th>
                            <th>Cost</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dynamic item rows here -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end">Sub Total</td>
                            <td colspan="2" id="subtotal">0</td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end">Discount (%)</td>
                            <td colspan="2">
                                <input type="number" id="discount_perc" class="form-control form-control-sm d-inline-block" style="width:80px" value="0">
                                <span id="discount_amount">0</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end">Tax (%)</td>
                            <td colspan="2">
                                <input type="number" id="tax_perc" class="form-control form-control-sm d-inline-block" style="width:80px" value="0">
                                <span id="tax_amount">0</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end">Grand Total</td>
                            <td colspan="2" id="grandtotal">0</td>
                        </tr>
                    </tfoot>
                </table>

                <div class="form-group">
                    <label>Remarks</label>
                    <textarea class="form-control" rows="3"></textarea>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer text-center">
        <button type="submit" form="po-form" class="btn btn-flat btn-primary">Save</button>
        <a href="{{ route('admin.purchase_orders.index') }}" class="btn btn-flat btn-dark">Cancel</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const itemSelect = document.getElementById('item_id');
    const unitInput = document.getElementById('unit');
    const qtyInput = document.getElementById('qty');
    const listTable = document.querySelector('#list tbody');

    itemSelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        unitInput.value = selected.dataset.unit || '';
    });

    document.getElementById('add_to_list').addEventListener('click', function () {
        const itemId = itemSelect.value;
        const itemText = itemSelect.options[itemSelect.selectedIndex].text;
        const unit = unitInput.value;
        const qty = parseFloat(qtyInput.value);
        const cost = parseFloat(itemSelect.options[itemSelect.selectedIndex].dataset.cost || 0);
        const total = cost * qty;

        if (!itemId || !unit || !qty || qty <= 0) {
            alert('Please fill item, unit, and quantity.');
            return;
        }

        const row = document.createElement('tr');
        row.innerHTML = `
            <td></td>
            <td>${qty}</td>
            <td>${unit}</td>
            <td>${itemText}</td>
            <td class="text-end">${cost.toLocaleString()}</td>
            <td class="text-end total">${total.toLocaleString()}</td>
            <td><button class="btn btn-sm btn-danger remove-row">Remove</button></td>
        `;
        listTable.appendChild(row);
        updateTotals();
        row.querySelector('.remove-row').addEventListener('click', () => {
            row.remove();
            updateTotals();
        });

        itemSelect.value = '';
        unitInput.value = '';
        qtyInput.value = '';
    });

    ['discount_perc', 'tax_perc'].forEach(id => {
        document.getElementById(id).addEventListener('input', updateTotals);
    });

    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('#list tbody tr .total').forEach(cell => {
            subtotal += parseFloat(cell.textContent.replace(/,/g, '')) || 0;
        });

        document.getElementById('subtotal').textContent = subtotal.toLocaleString();
        const discountPerc = parseFloat(document.getElementById('discount_perc').value) || 0;
        const discount = subtotal * discountPerc / 100;
        document.getElementById('discount_amount').textContent = discount.toLocaleString();

        const afterDiscount = subtotal - discount;
        const taxPerc = parseFloat(document.getElementById('tax_perc').value) || 0;
        const tax = afterDiscount * taxPerc / 100;
        document.getElementById('tax_amount').textContent = tax.toLocaleString();

        const grandTotal = afterDiscount + tax;
        document.getElementById('grandtotal').textContent = grandTotal.toLocaleString();
    }
});
</script>
@endpush
