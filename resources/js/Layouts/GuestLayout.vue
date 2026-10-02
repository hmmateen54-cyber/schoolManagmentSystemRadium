<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';

const page = usePage();
const pageMeta = computed(() => ({
    'Auth/Login': ['Welcome back', 'Sign in to continue to your school workspace.'],
    'Auth/Register': ['Create your account', 'Get started with a secure school account.'],
    'Auth/ForgotPassword': ['Reset your password', 'We’ll help you get back into your account.'],
    'Auth/ResetPassword': ['Choose a new password', 'Create a strong password for your account.'],
    'Auth/ConfirmPassword': ['Confirm it’s you', 'Enter your password to continue securely.'],
    'Auth/VerifyEmail': ['Verify your email', 'One quick step keeps your account protected.'],
}[page.component] ?? ['Your school workspace', 'Secure access to the information you need.']));
</script>

<template>
    <div class="auth-shell">
        <aside class="auth-story">
            <Link href="/" class="auth-brand">
                <span class="auth-brand-mark"><Icon name="school" :size="23" /></span>
                <span><strong>Schoolhouse</strong><small>Management Suite</small></span>
            </Link>

            <div class="auth-story-content">
                <div class="auth-story-kicker"><span /> MADE FOR YOUR SCHOOL</div>
                <h2>Every school day,<br /><em>more in sync.</em></h2>
                <p>A thoughtful home for your people, classes, and the work that keeps your school moving.</p>
                <div class="auth-preview-card">
                    <div class="auth-preview-heading"><span class="auth-preview-icon"><Icon name="chart" :size="18" /></span><span><strong>School overview</strong><small>Everything in its place</small></span><span class="auth-preview-dot" /></div>
                    <div class="auth-preview-rule" />
                    <div class="auth-preview-metrics"><div><b>Students</b><span>Well supported</span></div><div><b>Classes</b><span>In good rhythm</span></div></div>
                    <div class="auth-preview-bars"><span /><span /><span /><span /><span /><span /><span /><span /></div>
                </div>
            </div>

            <div class="auth-story-foot"><Icon name="shield" :size="16" /><span>Private by design. Built for school communities.</span></div>
        </aside>

        <main class="auth-main">
            <Link href="/" class="auth-brand auth-mobile-brand">
                <span class="auth-brand-mark"><Icon name="school" :size="22" /></span>
                <span><strong>Schoolhouse</strong><small>Management Suite</small></span>
            </Link>
            <section class="auth-card">
                <div class="auth-card-heading">
                    <p class="auth-eyebrow">SCHOOLHOUSE ACCOUNT</p>
                    <h1>{{ pageMeta[0] }}</h1>
                    <p>{{ pageMeta[1] }}</p>
                </div>
                <slot />
            </section>
            <p class="auth-footer">© {{ new Date().getFullYear() }} Schoolhouse <span>·</span> A calmer way to run your school.</p>
        </main>
    </div>
</template>
