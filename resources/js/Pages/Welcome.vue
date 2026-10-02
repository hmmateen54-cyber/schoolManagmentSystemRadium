<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';

defineProps({ canLogin: Boolean, canRegister: Boolean });
</script>

<template>
    <Head title="Welcome" />
    <main class="welcome-page">
        <header class="welcome-header">
            <Link href="/" class="welcome-brand">
                <span class="welcome-brand-mark"><Icon name="school" :size="23" /></span>
                <span><strong>Schoolhouse</strong><small>Management Suite</small></span>
            </Link>
            <nav v-if="canLogin" class="welcome-nav" aria-label="Account navigation">
                <Link :href="route('login')" class="welcome-sign-in">Sign in</Link>
                <Link v-if="canRegister" :href="route('register')" class="welcome-get-started">Get started <Icon name="arrow" :size="15" /></Link>
            </nav>
        </header>

        <section class="welcome-hero">
            <div class="welcome-copy">
                <p class="welcome-kicker"><span /> SCHOOL MANAGEMENT, MADE CLEAR</p>
                <h1>A better rhythm<br />for <em>school life.</em></h1>
                <p class="welcome-description">Bring your students, teachers, and classes into one calm, connected workspace—so your team can spend more time where it matters.</p>
                <div class="welcome-actions">
                    <Link v-if="canLogin" :href="route('login')" class="welcome-primary">Sign in to your workspace <Icon name="arrow" :size="17" /></Link>
                    <Link v-if="canRegister" :href="route('register')" class="welcome-secondary">Create an account</Link>
                </div>
                <div class="welcome-trust"><span class="welcome-trust-icon"><Icon name="shield" :size="15" /></span><span>Private by design</span><i /> <span>Built for school communities</span></div>
            </div>

            <div class="welcome-visual" aria-label="Illustration of the Schoolhouse dashboard">
                <div class="welcome-orbit welcome-orbit-one" /><div class="welcome-orbit welcome-orbit-two" />
                <div class="welcome-dashboard-card">
                    <div class="welcome-dashboard-top"><span class="welcome-mini-mark"><Icon name="school" :size="17" /></span><span><strong>School overview</strong><small>A thoughtful view of your day</small></span><span class="welcome-live"><i />Ready</span></div>
                    <div class="welcome-preview-metrics">
                        <div><span>Students</span><strong>Learning together</strong><small class="welcome-spark welcome-spark-one" /></div>
                        <div><span>Teachers</span><strong>Here for every class</strong><small class="welcome-spark welcome-spark-two" /></div>
                    </div>
                    <div class="welcome-preview-class">
                        <div class="welcome-preview-class-head"><span><strong>Classroom overview</strong><small>People and classes, in one place</small></span><span class="welcome-circle-arrow"><Icon name="arrow" :size="14" /></span></div>
                        <div class="welcome-roster-line"><span class="welcome-roster-avatar">AM</span><span><b>Student profiles</b><small>Organized and easy to find</small></span><span class="welcome-roster-status">Current</span></div>
                        <div class="welcome-roster-line"><span class="welcome-roster-avatar welcome-roster-avatar-alt">TC</span><span><b>Teacher workspace</b><small>Focused on each class</small></span><span class="welcome-roster-status">Connected</span></div>
                    </div>
                    <div class="welcome-chart-caption"><span>School day, in view</span><span><i /><i /><i /><i /><i /><i /><i /><i /></span></div>
                </div>
                <div class="welcome-float-card"><span class="welcome-float-icon"><Icon name="check" :size="16" /></span><span><strong>In good order</strong><small>One connected workspace</small></span></div>
            </div>
        </section>

        <section class="welcome-features" aria-label="Workspace highlights">
            <article><span class="welcome-feature-icon"><Icon name="students" /></span><div><strong>People, connected</strong><p>Student and teacher records that stay organized.</p></div></article>
            <article><span class="welcome-feature-icon"><Icon name="classes" /></span><div><strong>Classes, in context</strong><p>Keep class assignments clear and up to date.</p></div></article>
            <article><span class="welcome-feature-icon"><Icon name="shield" /></span><div><strong>Access with care</strong><p>Each role sees the information it needs.</p></div></article>
        </section>
        <footer class="welcome-footer"><span>© {{ new Date().getFullYear() }} Schoolhouse Management Suite</span><span>Made for better school days.</span></footer>
    </main>
</template>
