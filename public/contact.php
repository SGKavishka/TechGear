<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/init.php';

$errors = [];
$form = [
    'name' => trim($_POST['name'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'phone' => trim($_POST['phone'] ?? ''),
    'subject' => trim($_POST['subject'] ?? ''),
    'message' => trim($_POST['message'] ?? ''),
];

function ensure_contact_phone_column(): bool
{
    static $ready = null;

    if ($ready !== null) {
        return $ready;
    }

    try {
        $stmt = db()->query("SHOW COLUMNS FROM contact_messages LIKE 'phone'");
        if ($stmt->fetch()) {
            return $ready = true;
        }

        db()->exec('ALTER TABLE contact_messages ADD phone VARCHAR(40) DEFAULT NULL AFTER email');
        return $ready = true;
    } catch (Throwable) {
        return $ready = false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    if (strlen($form['name']) < 3) {
        $errors['name'] = 'Enter your full name.';
    }

    if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }

    if ($form['phone'] !== '' && !preg_match('/^[0-9+\-\s()]{7,40}$/', $form['phone'])) {
        $errors['phone'] = 'Enter a valid phone number.';
    }

    if (strlen($form['subject']) < 4) {
        $errors['subject'] = 'Enter a useful subject.';
    }

    if (strlen($form['message']) < 10) {
        $errors['message'] = 'Message must be at least 10 characters.';
    }

    if (!$errors) {
        if (ensure_contact_phone_column()) {
            $stmt = db()->prepare('INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$form['name'], $form['email'], $form['phone'] ?: null, $form['subject'], $form['message']]);
        } else {
            $stmt = db()->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)');
            $stmt->execute([$form['name'], $form['email'], $form['subject'], $form['message']]);
        }

        flash('success', 'Message sent successfully. Our team will follow up soon.');
        redirect('contact.php');
    }
}

$pageTitle = 'Contact TechGear | Support, Orders & Technical Help';
$pageDescription = 'Contact TechGear for product support, order assistance, and technical help.';
$activePage = 'contact';
$extraCss = ['css/contact.css'];

?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<main class="contact-page">
    <section class="contact-hero">
        <div class="container contact-hero-grid">
            <div class="contact-hero-copy">
                <span class="eyebrow">Support Center</span>
                <h1>Contact TechGear</h1>
                <p>Get fast support for products, orders, availability, warranty questions, and technical help from the TechGear team.</p>
            </div>

            <div class="contact-status-panel" aria-label="Support status">
                <span class="status-light"></span>
                <div>
                    <strong>Support desk online</strong>
                    <p>Average reply within 2 business hours.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="container contact-layout" aria-label="Contact options">
        <div class="glass-panel contact-form-panel">
            <div class="contact-panel-heading">
                <span class="eyebrow">Message Us</span>
                <h2>Send a Message</h2>
                <p>Share the details and we will route your request to the right TechGear specialist.</p>
            </div>

            <form method="post" class="contact-form" novalidate>
                <?= csrf_field() ?>

                <div class="contact-form-grid">
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-input <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= h($form['name']) ?>" placeholder="Enter your full name" autocomplete="name">
                        <?php if (isset($errors['name'])): ?><span class="error-message visible"><?= h($errors['name']) ?></span><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-input <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= h($form['email']) ?>" placeholder="you@example.com" autocomplete="email">
                        <?php if (isset($errors['email'])): ?><span class="error-message visible"><?= h($errors['email']) ?></span><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" class="form-input <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" value="<?= h($form['phone']) ?>" placeholder="+94 77 123 4567" autocomplete="tel">
                        <?php if (isset($errors['phone'])): ?><span class="error-message visible"><?= h($errors['phone']) ?></span><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-input <?= isset($errors['subject']) ? 'is-invalid' : '' ?>" value="<?= h($form['subject']) ?>" placeholder="Product, order, or support topic">
                        <?php if (isset($errors['subject'])): ?><span class="error-message visible"><?= h($errors['subject']) ?></span><?php endif; ?>
                    </div>

                    <div class="form-group form-group-wide">
                        <label class="form-label" for="message">Message</label>
                        <textarea id="message" name="message" class="form-input <?= isset($errors['message']) ? 'is-invalid' : '' ?>" placeholder="Tell us what you need help with..."><?= h($form['message']) ?></textarea>
                        <?php if (isset($errors['message'])): ?><span class="error-message visible"><?= h($errors['message']) ?></span><?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary form-submit contact-submit">
                    <span>Send Message</span>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M22 2L11 13"></path>
                        <path d="M22 2l-7 20-4-9-9-4 20-7z"></path>
                    </svg>
                </button>
            </form>
        </div>

        <aside class="contact-side">
            <div class="contact-info-grid" aria-label="TechGear contact information">
                <article class="contact-info-card">
                    <div class="contact-card-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"></path>
                            <path d="M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3>Email</h3>
                        <p><a href="mailto:support@techgear.lk">support@techgear.lk</a><br><a href="mailto:sales@techgear.lk">sales@techgear.lk</a></p>
                    </div>
                </article>

                <article class="contact-info-card">
                    <div class="contact-card-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.95.68l1.5 4.5a1 1 0 01-.5 1.21l-2.26 1.13a11.04 11.04 0 005.52 5.52l1.13-2.26a1 1 0 011.21-.5l4.49 1.5a1 1 0 01.68.95V19a2 2 0 01-2 2h-1C9.72 21 3 14.28 3 6V5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3>Phone</h3>
                        <p><a href="tel:+94112345678">+94 11 234 5678</a><br><a href="tel:+94771234567">+94 77 123 4567</a></p>
                    </div>
                </article>

                <article class="contact-info-card">
                    <div class="contact-card-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 21s7-4.35 7-11a7 7 0 10-14 0c0 6.65 7 11 7 11z"></path>
                            <path d="M12 13a3 3 0 100-6 3 3 0 000 6z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3>Address</h3>
                        <p>45/2 Galle Road<br>Colombo 03, Sri Lanka</p>
                    </div>
                </article>

                <article class="contact-info-card">
                    <div class="contact-card-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 8v5l3 2"></path>
                            <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3>Working Hours</h3>
                        <p>Mon - Fri: 9:00 AM - 6:00 PM<br>Sat: 10:00 AM - 4:00 PM</p>
                    </div>
                </article>
            </div>

            <section class="quick-help" aria-labelledby="quick-help-title">
                <span class="eyebrow">Rapid Support</span>
                <h2 id="quick-help-title">Need quick help?</h2>
                <p>Choose the fastest channel for urgent order updates, product checks, or setup questions.</p>
                <div class="quick-help-actions">
                    <a class="quick-help-button" href="https://wa.me/94112345678" target="_blank" rel="noopener">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"></path>
                        </svg>
                        <span>WhatsApp</span>
                    </a>
                    <a class="quick-help-button" href="tel:+94112345678">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M22 16.92v3a2 2 0 01-2.18 2 19.8 19.8 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.8 19.8 0 012.12 4.2 2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.35 1.9.67 2.8a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.9.32 1.84.54 2.8.67A2 2 0 0122 16.92z"></path>
                        </svg>
                        <span>Call</span>
                    </a>
                    <a class="quick-help-button" href="mailto:support@techgear.lk">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 4h16v16H4z"></path>
                            <path d="M22 6l-10 7L2 6"></path>
                        </svg>
                        <span>Email</span>
                    </a>
                </div>
            </section>
        </aside>
    </section>
</main>

<?php require APP_PATH . '/views/partials/footer.php'; ?>
