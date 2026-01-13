<div class="quantity-control flex items-center gap-2" data-product-id="{{ $productId }}">
    <button type="button" class="decrease px-3 py-1 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">-</button>

    <input type="number" name="quantity[{{ $productId }}]" value="{{ $value ?? 1 }}" min="1"
           class="quantity-input w-12 text-center border border-gray-300 rounded-md">

    <button type="button" class="increase px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600">+</button>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".quantity-control").forEach((control) => {
            const input = control.querySelector(".quantity-input");

            control.querySelector(".increase").addEventListener("click", function () {
                input.value = parseInt(input.value) + 1;
            });

            control.querySelector(".decrease").addEventListener("click", function () {
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                }
            });
        });
    });
</script>
