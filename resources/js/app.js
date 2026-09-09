const appMenuToggle = document.querySelector('[data-menu-toggle]');
const appSidebar = document.querySelector('[data-sidebar]');

const closeAppSidebar = () => {
    appSidebar?.classList.remove('open');
    document.body.classList.remove('app-sidebar-open');
    appMenuToggle?.setAttribute('aria-expanded', 'false');
};

appMenuToggle?.addEventListener('click', (event) => {
    event.stopPropagation();
    const isOpen = appSidebar?.classList.toggle('open') ?? false;
    document.body.classList.toggle('app-sidebar-open', isOpen);
    appMenuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
});

document.addEventListener('click', (event) => {
    if (! appSidebar?.classList.contains('open') || event.target.closest('[data-sidebar]')) {
        return;
    }

    closeAppSidebar();
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && appSidebar?.classList.contains('open')) {
        closeAppSidebar();
        appMenuToggle?.focus();
    }
});
const landingMenuToggle = document.querySelector('[data-landing-menu-toggle]');
const landingNav = document.querySelector('#landing-menu');
const landingShell = document.querySelector('.landing-shell');
const landingMenuClose = document.querySelector('[data-landing-menu-close]');
const landingMenuBackdrop = document.querySelector('[data-landing-menu-backdrop]');

document.querySelectorAll('[data-auto-dismiss]').forEach((notice) => {
    const duration = Number(notice.dataset.autoDismiss) || 2600;

    window.setTimeout(() => {
        notice.classList.add('is-leaving');
        window.setTimeout(() => notice.remove(), 220);
    }, duration);
});

landingMenuToggle?.addEventListener('click', () => {
    const isOpen = landingNav?.classList.toggle('is-open') ?? false;
    landingMenuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    landingShell?.classList.toggle('menu-open', isOpen);
    document.body.classList.toggle('landing-menu-open', isOpen);
});

const closeLandingMenu = () => {
    landingNav?.classList.remove('is-open');
    landingShell?.classList.remove('menu-open');
    document.body.classList.remove('landing-menu-open');
    landingMenuToggle?.setAttribute('aria-expanded', 'false');
};

landingMenuClose?.addEventListener('click', closeLandingMenu);
landingMenuBackdrop?.addEventListener('click', closeLandingMenu);

landingNav?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => {
    closeLandingMenu();
}));

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape' || ! landingNav?.classList.contains('is-open')) {
        return;
    }

    closeLandingMenu();
});

const directoryMenuToggle = document.querySelector('[data-directory-menu-toggle]');
const directoryNav = document.querySelector('#directory-menu');

const closeDirectoryMenu = () => {
    directoryNav?.classList.remove('is-open');
    document.body.classList.remove('directory-menu-open');
    directoryMenuToggle?.setAttribute('aria-expanded', 'false');
};

directoryMenuToggle?.addEventListener('click', (event) => {
    event.stopPropagation();
    const isOpen = directoryNav?.classList.toggle('is-open') ?? false;
    directoryMenuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    document.body.classList.toggle('directory-menu-open', isOpen);
});

directoryNav?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeDirectoryMenu));

document.addEventListener('click', (event) => {
    if (! directoryNav?.classList.contains('is-open') || event.target.closest('#directory-menu')) {
        return;
    }

    closeDirectoryMenu();
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && directoryNav?.classList.contains('is-open')) {
        closeDirectoryMenu();
        directoryMenuToggle?.focus();
    }
});

window.addEventListener('resize', () => {
    if (window.innerWidth > 1024 && directoryNav?.classList.contains('is-open')) {
        closeDirectoryMenu();
    }
});
document.querySelectorAll('[data-confirm]').forEach((form) => form.addEventListener('submit', (event) => {
    if (! window.confirm(form.dataset.confirm)) { event.preventDefault(); }
}));

const securityUser = document.querySelector('[data-security-user]');
const currentPasswordField = document.querySelector('[data-current-password-field]');
const currentPasswordInput = document.querySelector('#current_password');

const updateSecurityForm = () => {
    const isCurrentUser = securityUser?.selectedOptions[0]?.dataset.isCurrent === 'true';

    if (! currentPasswordField || ! currentPasswordInput) {
        return;
    }

    currentPasswordField.hidden = ! isCurrentUser;
    currentPasswordInput.required = isCurrentUser;
    if (! isCurrentUser) {
        currentPasswordInput.value = '';
    }
};

securityUser?.addEventListener('change', updateSecurityForm);
updateSecurityForm();

const logoutModal = document.querySelector('[data-logout-modal]');
const logoutDialog = document.querySelector('[data-logout-dialog]');
let logoutTrigger = null;

const closeLogoutModal = () => {
    if (! logoutModal) {
        return;
    }

    logoutModal.hidden = true;
    logoutModal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
    logoutTrigger?.focus();
};

document.querySelector('[data-logout-trigger]')?.addEventListener('click', (event) => {
    if (! logoutModal) {
        return;
    }

    logoutTrigger = event.currentTarget;
    logoutModal.hidden = false;
    logoutModal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');
    logoutDialog?.focus();
});

document.querySelectorAll('[data-logout-dismiss]').forEach((element) => element.addEventListener('click', closeLogoutModal));
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && ! logoutModal?.hidden) {
        closeLogoutModal();
    }
});

const pageLoader = document.querySelector('[data-page-loader]');
let loaderHideTimer = null;
let isNavigating = false;
let loaderFallbackTimer = null;

const hidePageLoader = () => {
    if (! pageLoader) {
        return;
    }

    isNavigating = false;
    window.clearTimeout(loaderFallbackTimer);
    pageLoader.classList.remove('is-visible');
    pageLoader.setAttribute('aria-hidden', 'true');
    loaderHideTimer = window.setTimeout(() => {
        pageLoader.hidden = true;
    }, 320);
};

const showPageLoader = () => {
    if (! pageLoader) {
        return;
    }

    window.clearTimeout(loaderHideTimer);
    window.clearTimeout(loaderFallbackTimer);
    pageLoader.hidden = false;
    pageLoader.setAttribute('aria-hidden', 'false');
    window.requestAnimationFrame(() => pageLoader.classList.add('is-visible'));
    loaderFallbackTimer = window.setTimeout(hidePageLoader, 5000);
};

const showEntryLoader = () => {
    showPageLoader();
    window.setTimeout(hidePageLoader, 900);
};

if (document.body.dataset.entryLoader === 'true') {
    showEntryLoader();
}

document.querySelector('[data-login-form]')?.addEventListener('submit', () => {
    isNavigating = true;
    showPageLoader();
});

document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href]');

    if (! link || link.target === '_blank' || link.hasAttribute('download') || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
        return;
    }

    const destination = new URL(link.href, window.location.href);

    if (destination.origin !== window.location.origin || destination.pathname === window.location.pathname && destination.search === window.location.search || destination.hash && destination.pathname === window.location.pathname) {
        return;
    }

    if (! pageLoader) {
        return;
    }

    if (document.body.classList.contains('app-body')) {
        return;
    }

    event.preventDefault();

    if (isNavigating) {
        return;
    }

    isNavigating = true;
    showPageLoader();
    window.location.assign(destination.href);
});

window.addEventListener('pageshow', () => {
    if (isNavigating) {
        hidePageLoader();
    }
});
