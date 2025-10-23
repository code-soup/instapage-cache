/**
 * Run scripts on document ready
 * No jQuery here sorry
 */
document.addEventListener("DOMContentLoaded", () => {
	initCacheToggle();
});

/**
 * Initialize cache toggle buttons
 */
function initCacheToggle() {
	const buttons = document.querySelectorAll('.toggle-cache-btn');

	buttons.forEach(button => {
		button.addEventListener('click', function(e) {
			e.preventDefault();

			const postId = this.dataset.postId;
			const enable = this.dataset.enable;
			const nonce = this.dataset.nonce;
			const btn = this;
			const statusSpan = btn.parentElement.querySelector('span');

			btn.disabled = true;
			btn.textContent = 'Loading...';

			fetch(ajaxurl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams({
					action: 'toggle_post_cache',
					post_id: postId,
					enable: enable,
					_ajax_nonce: nonce
				})
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					btn.textContent = data.data.status;
					btn.classList.remove('button-primary', 'button-secondary');
					btn.classList.add(data.data.enabled ? 'button-primary' : 'button-secondary');
					btn.dataset.enable = data.data.enabled ? '0' : '1';
					statusSpan.textContent = data.data.enabled ? 'Active' : 'Inactive';
				} else {
					alert('Error: ' + (data.data?.message || 'Unknown error'));
					btn.disabled = false;
					btn.textContent = btn.dataset.enable === '1' ? 'Enable' : 'Disable';
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert('An error occurred');
				btn.disabled = false;
				btn.textContent = btn.dataset.enable === '1' ? 'Enable' : 'Disable';
			})
			.finally(() => {
				btn.disabled = false;
			});
		});
	});
}