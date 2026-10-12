// Global form double-submission prevention and loading state
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function (e) {
            if (this.checkValidity && !this.checkValidity()) {
                return;
            }

            const submitBtn = this.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                setTimeout(() => {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');

                    if (submitBtn.tagName.toLowerCase() === 'button') {
                        const spinner = document.createElement('span');
                        spinner.className = 'inline-block w-4 h-4 mr-2 border-2 border-current border-t-transparent rounded-full animate-spin shrink-0 align-middle';
                        submitBtn.prepend(spinner);
                    }
                }, 0);
            }
        });
    });
});
