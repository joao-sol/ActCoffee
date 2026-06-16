import './bootstrap';

document.addEventListener('change', (event) => {
    const input = event.target;

    if (!(input instanceof HTMLInputElement) || !input.matches('[data-swap-checkbox]')) {
        return;
    }

    if (!input.checked) {
        return;
    }

    document.querySelectorAll('[data-swap-checkbox]').forEach((checkbox) => {
        if (checkbox !== input) {
            checkbox.checked = false;
        }
    });
});
