document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('addCategoryForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const name = document.getElementById('categori_name').value;
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch("/categories", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf
                },
                credentials: "same-origin",
                body: JSON.stringify({
                    categori_name: name
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Validation failed');
                }
                return response.json();
            })
            .then(data => {
                const select = document.getElementById('category_id');
                const option = document.createElement('option');
                option.value = data.id;
                option.text = data.categori_name;
                select.appendChild(option);
                select.value = data.id;

                const modal = bootstrap.Modal.getInstance(document.getElementById('addCategoryModal'));
                modal.hide();

                document.getElementById('addCategoryForm').reset();
            })
            .catch(error => {
                console.error(error);
                alert("Failed to save category. Please try again.");
            });
        });
    }
});
