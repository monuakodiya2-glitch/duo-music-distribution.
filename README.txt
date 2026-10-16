DUO ARTIST PORTAL PLUGIN
========================

INSTALL
1. In WordPress Admin go to Plugins → Add New Plugin → Upload Plugin.
2. Upload duo-artist-portal.zip, click Install Now, then Activate.
3. Plugin creates Artist Register, Artist Dashboard, and Submit a Release pages if those slugs do not already exist.
4. If a page already exists, edit it and add the corresponding shortcode:
   Artist Register: [duo_artist_register]
   Artist Dashboard: [duo_artist_dashboard]
   Submit a Release: [duo_artist_release_upload]
5. The plugin adds a WordPress Admin menu called Artist Releases. Admins can review submissions and change status in the Distribution Review Status box.
6. If your theme already created a Submit a Release page, edit its content and replace the old shortcode with [duo_artist_release_upload].
7. For public registration, this plugin provides its own registration form. Keep WordPress core public registration disabled unless you have a reason to enable it.

WHAT IT DOES
- Artist account registration and login.
- Artist-only release dashboard.
- Audio upload (MP3, WAV, M4A, FLAC; 100 MB maximum) and JPG/PNG cover art (10 MB maximum).
- Release details saved as private WordPress records.
- Admin status options: Pending Review, Approved, Rejected, Needs Changes, Delivered.
- Email notifications depend on your hosting mail setup.

IMPORTANT SECURITY / BUSINESS NOTES
- WordPress and server upload limits may be lower than the plugin limits. Configure PHP upload_max_filesize and post_max_size accordingly.
- Files are stored in WordPress Media Library and may be accessible by direct URL depending on your hosting setup. For unreleased commercial music, configure private object storage and signed URLs/access controls before launching publicly at scale.
- Use HTTPS, backups, security updates, anti-spam/rate limiting, strong passwords and a reliable SMTP plugin.
- Add privacy policy, Terms of Service, rights/ownership confirmation, takedown procedure, refund and royalty terms before launch.
- Never accept content the artist doesn't own or have permission to distribute.
- This plugin does not distribute audio to Spotify, Apple Music, YouTube Music or any other platform. Delivery requires a real distribution partner/API and a separate operational process.
- Test uploads on a staging site first.
