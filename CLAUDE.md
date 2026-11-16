# GBP Secure Local: project notes

## CSS changes
- Do NOT edit `public/seoai/css/site.css` (or any other file under `public/seoai/`) for small UI tweaks. Uploading changed static files to the server is a hassle.
- Put minor styling in a `<style>` tag instead:
  - page-specific tweaks: `@push('styles') <style>...</style> @endpush` in that page's Blade view (the layout already has `@stack('styles')`);
  - site-wide tweaks: a `<style>` block in the `<head>` of `resources/views/layouts/app.blade.php`.
- Only touch `public/seoai/` files when a change cannot be done inline (for example adding a new image or font), and say so.
