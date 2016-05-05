if (typeof alertify == 'object' && typeof alertify.set == 'function') {
	alertify.set({
		labels: {
			ok: "Fortsetzen",
			cancel: "Stornieren"
		}
	});
}