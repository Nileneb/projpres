// === Global 403 Policy Error Handling ===
// Intercept fetch
const origFetch = window.fetch;
window.fetch = async (...args) => {
	const response = await origFetch(...args);
	if (response.status === 403) {
		response.clone().json().then(data => {
			alert(data.message || 'Diese Aktion ist verboten!');
		}).catch(() => {
			alert('Diese Aktion ist verboten!');
		});
	}
	return response;
};

// Intercept classic form submissions (non-AJAX)
document.addEventListener('submit', function(e) {
	const form = e.target;
	if (form.tagName === 'FORM' && !form.hasAttribute('data-no-403-popup')) {
		e.preventDefault();
		const formData = new FormData(form);
		fetch(form.action, {
			method: form.method || 'POST',
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'Accept': 'application/json',
			},
			body: formData,
		}).then(async resp => {
			if (resp.status === 403) {
				let msg = 'Diese Aktion ist verboten!';
				try {
					const data = await resp.json();
					msg = data.message || msg;
				} catch {}
				alert(msg);
			} else {
				// Success: fallback to normal submit
				form.submit();
			}
		}).catch(() => {
			form.submit();
		});
	}
}, true);
