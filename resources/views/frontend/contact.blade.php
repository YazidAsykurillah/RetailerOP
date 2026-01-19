@extends('frontend.layouts.app')

@section('title', 'Contact Us')
@section('meta_description', 'Get in touch with Siskha Store. We\'d love to hear from you. Find our contact information and send us a message.')

@section('styles')
<style>
    /* Contact Layout */
    .contact-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        min-height: calc(100vh - 80px);
        margin-top: 80px;
    }

    /* Contact Info Side */
    .contact-info {
        background: var(--color-primary-dark);
        color: var(--color-white);
        padding: var(--space-3xl);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .contact-info-eyebrow {
        font-size: 0.75rem;
        font-weight: 500;
        letter-spacing: 0.25em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.6);
        margin-bottom: var(--space-md);
    }

    .contact-info-title {
        font-family: var(--font-serif);
        font-size: clamp(2rem, 4vw, 2.5rem);
        font-weight: 300;
        margin-bottom: var(--space-lg);
        line-height: 1.2;
    }

    .contact-info-text {
        font-size: 1rem;
        font-weight: 300;
        line-height: 1.8;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: var(--space-2xl);
        max-width: 400px;
    }

    .contact-details {
        margin-bottom: var(--space-2xl);
    }

    .contact-item {
        display: flex;
        align-items: flex-start;
        gap: var(--space-md);
        margin-bottom: var(--space-lg);
    }

    .contact-icon {
        width: 24px;
        height: 24px;
        color: rgba(255, 255, 255, 0.6);
        flex-shrink: 0;
        margin-top: 2px;
    }

    .contact-label {
        font-size: 0.75rem;
        font-weight: 500;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.5);
        margin-bottom: var(--space-xs);
    }

    .contact-value {
        font-size: 1rem;
        font-weight: 300;
        color: var(--color-white);
        line-height: 1.6;
    }

    .contact-value a {
        color: var(--color-white);
        transition: opacity var(--transition-fast);
    }

    .contact-value a:hover {
        opacity: 0.8;
    }

    .contact-social {
        display: flex;
        gap: var(--space-md);
        margin-top: var(--space-xl);
        padding-top: var(--space-xl);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .contact-social a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: rgba(255, 255, 255, 0.6);
        transition: all var(--transition-fast);
    }

    .contact-social a:hover {
        border-color: var(--color-white);
        color: var(--color-white);
        background: rgba(255, 255, 255, 0.1);
    }

    .contact-social svg {
        width: 20px;
        height: 20px;
    }

    /* Contact Form Side */
    .contact-form-section {
        background: var(--color-white);
        padding: var(--space-3xl);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .form-header {
        margin-bottom: var(--space-xl);
    }

    .form-title {
        font-family: var(--font-serif);
        font-size: 1.75rem;
        font-weight: 400;
        color: var(--color-black);
        margin-bottom: var(--space-sm);
    }

    .form-subtitle {
        font-size: 1rem;
        font-weight: 300;
        color: var(--color-gray-600);
    }

    .contact-form {
        max-width: 500px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--space-md);
    }

    .form-group {
        margin-bottom: var(--space-lg);
    }

    .form-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 500;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--color-gray-800);
        margin-bottom: var(--space-xs);
    }

    .form-input,
    .form-textarea {
        width: 100%;
        padding: var(--space-sm) var(--space-md);
        font-family: var(--font-sans);
        font-size: 1rem;
        font-weight: 300;
        color: var(--color-black);
        border: 1px solid var(--color-gray-300);
        background: var(--color-white);
        transition: border-color var(--transition-fast);
    }

    .form-input:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--color-primary);
    }

    .form-input::placeholder,
    .form-textarea::placeholder {
        color: var(--color-gray-400);
    }

    .form-textarea {
        min-height: 150px;
        resize: vertical;
    }

    .form-select {
        width: 100%;
        padding: var(--space-sm) var(--space-md);
        font-family: var(--font-sans);
        font-size: 1rem;
        font-weight: 300;
        color: var(--color-black);
        border: 1px solid var(--color-gray-300);
        background: var(--color-white);
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236c757d'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right var(--space-sm) center;
        background-size: 20px;
    }

    .form-select:focus {
        outline: none;
        border-color: var(--color-primary);
    }

    .submit-btn {
        width: 100%;
        padding: var(--space-md) var(--space-xl);
        font-family: var(--font-sans);
        font-size: 0.875rem;
        font-weight: 500;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--color-white);
        background: var(--color-primary-dark);
        border: none;
        cursor: pointer;
        transition: all var(--transition-smooth);
    }

    .submit-btn:hover {
        background: var(--color-primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(26, 58, 92, 0.3);
    }

    /* Map Section */
    .map-section {
        background: var(--color-gray-100);
        padding: var(--space-3xl) 8%;
    }

    .map-header {
        text-align: center;
        margin-bottom: var(--space-xl);
    }

    .map-placeholder {
        width: 100%;
        height: 400px;
        background: var(--color-gray-200);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--color-gray-600);
        font-size: 1rem;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .contact-section {
            grid-template-columns: 1fr;
        }

        .contact-info {
            padding: var(--space-2xl);
        }

        .contact-form-section {
            padding: var(--space-2xl);
        }
    }

    @media (max-width: 640px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .map-placeholder {
            height: 300px;
        }
    }
</style>
@endsection

@section('content')
    <!-- Contact Section -->
    <section class="contact-section">
        <!-- Contact Info -->
        <div class="contact-info fade-in">
            <p class="contact-info-eyebrow">Get in Touch</p>
            <h1 class="contact-info-title">We'd Love to Hear From You</h1>
            <p class="contact-info-text">
                Have a question about our collections, need styling advice, or want to learn more 
                about Siskha? We're here to help.
            </p>

            <div class="contact-details">
                <div class="contact-item">
                    <svg class="contact-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                    <div>
                        <p class="contact-label">Address</p>
                        <p class="contact-value">
                            Jl. Fashion Boulevard No. 123<br>
                            Jakarta Selatan, 12345<br>
                            Indonesia
                        </p>
                    </div>
                </div>

                <div class="contact-item">
                    <svg class="contact-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    <div>
                        <p class="contact-label">Email</p>
                        <p class="contact-value">
                            <a href="mailto:hello@siskha.com">hello@siskha.com</a>
                        </p>
                    </div>
                </div>

                <div class="contact-item">
                    <svg class="contact-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                    <div>
                        <p class="contact-label">Phone</p>
                        <p class="contact-value">
                            <a href="tel:+6221123456">+62 21 123 456</a>
                        </p>
                    </div>
                </div>

                <div class="contact-item">
                    <svg class="contact-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="contact-label">Hours</p>
                        <p class="contact-value">
                            Monday - Saturday: 10:00 - 21:00<br>
                            Sunday: 11:00 - 20:00
                        </p>
                    </div>
                </div>
            </div>

            <div class="contact-social">
                <a href="#" aria-label="Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
                <a href="#" aria-label="Facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/>
                    </svg>
                </a>
                <a href="#" aria-label="WhatsApp">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form-section fade-in">
            <div class="form-header">
                <h2 class="form-title">Send Us a Message</h2>
                <p class="form-subtitle">Fill out the form below and we'll get back to you within 24 hours.</p>
            </div>

            <form class="contact-form" action="#" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="form-input" placeholder="John" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="form-input" placeholder="Doe" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="john@example.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="subject">Subject</label>
                    <select id="subject" name="subject" class="form-select" required>
                        <option value="">Select a subject</option>
                        <option value="general">General Inquiry</option>
                        <option value="collections">Collections Information</option>
                        <option value="styling">Styling Advice</option>
                        <option value="partnership">Partnership Inquiry</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="message">Message</label>
                    <textarea id="message" name="message" class="form-textarea" placeholder="How can we help you?" required></textarea>
                </div>

                <button type="submit" class="submit-btn">Send Message</button>
            </form>
        </div>
    </section>

    <!-- Map Section (Optional) -->
    <section class="map-section">
        <div class="map-header">
            <p class="section-eyebrow">Find Us</p>
            <h2 class="section-title">Visit Our Store</h2>
        </div>
        <div class="map-placeholder">
            <p>Map integration available upon request</p>
        </div>
    </section>
@endsection
